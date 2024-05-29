<?php

if ( !defined( 'ABSPATH' ) ) {
    exit; 
}

class Pi_dpmw_partial_payment_admin {

    protected static $instance = null;

    public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

    protected function __construct() {
        /**
         * separate table for deposit order
         */
        add_action( 'manage_pi_pending_amt_posts_custom_column', array( $this, 'pi_pending_amt_column' ), 10, 2 );
        add_action( 'manage_woocommerce_page_wc-orders--pi_pending_amt_custom_column', array( $this, 'pi_pending_amt_column' ), 10, 2 ); //hpos

        add_action( 'pre_get_posts', array( $this, 'show_all_orders' ), 10, 1 );
        add_action( 'pre_get_posts', array( $this, 'order_by_columns' ), 10, 1 );
        add_filter( 'views_edit-pi_pending_amt', array( $this, 'remove_status' ) );

        add_filter( 'manage_pi_pending_amt_posts_columns', array( $this, 'pi_pending_amt_columns' ) );
        add_action( 'manage_woocommerce_page_wc-orders--pi_pending_amt_columns', array($this,'pi_pending_amt_columns')); //hpos

        add_filter( 'manage_edit-pi_pending_amt_sortable_columns', array( $this, 'sortable_columns' ) );

        add_filter( 'post_row_actions', array( $this, 'remove_row_actions' ), 10, 1 );
        add_filter( 'admin_body_class', array( $this, 'deposit_body_class' ) );


         /**
         * showing deposit order in backend
         */
        add_action( 'add_meta_boxes', [$this, 'deposit_metabox'] );

        /**
         * Registers the partial paid order status 
         */
        add_action( 'woocommerce_register_shop_order_post_statuses', [$this, 'shop_order_status'], 10, 1 );

        /**
         * add the partial paid order status in the list of available order status
         */
        add_filter( 'wc_order_statuses', [$this, 'shows_order_status'] );
    }
    

    public function order_by_columns( $query ) {
        if ( !is_admin() ) {
            return;
        }

        $orderby = $query->get( 'orderby' );

        if ( 'deposit' == $orderby ) {
            $query->set( 'meta_key', '_deposit_id' );
            $query->set( 'orderby', 'meta_value_num' );
        } elseif ( 'parent_order' == $orderby ) {
            $query->set( 'orderby', 'parent' );
        } elseif ( 'deposit_date' == $orderby ) {
            $query->set( 'orderby', 'date' );
        }
    }
    

    public function sortable_columns( $columns ) {
        $columns['deposit']      = 'deposit';
        $columns['parent_order'] = 'parent_order';
        $columns['deposit_date'] = 'deposit_date';

        return $columns;
    }
   

    public function deposit_body_class( $classes ) {
        if ( get_post_type() === 'pi_pending_amt' ) {
            $classes .= ' post-type-shop_order';
        }
        return $classes;
    }

    

    public function remove_row_actions( $actions ) {
        if ( get_post_type() === 'pi_pending_amt' ) {
            unset( $actions['edit'] );
            unset( $actions['trash'] );
            unset( $actions['view'] );
            unset( $actions['inline hide-if-no-js'] );
        }
        return $actions;
    }

    

    public function show_all_orders( $query ) {

        if ( is_admin() && $query->is_main_query() && $query->get( 'post_type' ) == "pi_pending_amt" ) {
            if ( !isset( $_GET['post_status'] ) ) {
                $query->set( 'post_status', 'any' );
            }
        }
    }

   

    public function remove_status( $views ) {
        unset( $views['draft'] );
        unset( $views['publish'] );
        return $views;
    }

    

    public function pi_pending_amt_columns( $columns ) {
        unset( $columns['title'] );
        unset( $columns['order_number'] );
        unset( $columns['order_date'] );
        unset( $columns['date'] );
        unset( $columns['billing_address'] );
        unset( $columns['shipping_address'] );
        unset( $columns['wc_actions'] );
        unset( $columns['order_status'] );
        unset( $columns['order_total'] );

        $columns['type']        = __( 'Order type', 'disable-payment-method-for-woocommerce' );
        $columns['deposit']        = __( 'Deposit ID', 'disable-payment-method-for-woocommerce' );
        $columns['deposit_date'] = __( 'Date', 'disable-payment-method-for-woocommerce' );
        $columns['deposit_status'] = __( 'Status', 'disable-payment-method-for-woocommerce' );
        $columns['deposit_total']          = __( 'Total', 'disable-payment-method-for-woocommerce' );
        $columns['parent_order']   = __( 'Parent order', 'disable-payment-method-for-woocommerce' );

        return $columns;
    }
    
    
    public function pi_pending_amt_column( $column, $post ) {
        //require_once DISABLE_PAYMENT_METHOD_RULE_WOOCOMMERCE_PLUGIN_DIR_PATH . 'public/class-shop-deposit-order.php';
        if(is_a($post, 'WP_Post')){
            $depositOrder = new PIShopDeposit( $post->ID );
        }elseif(is_object($post) && !is_a($post, 'WP_Post')){
            $depositOrder = $post;
        }elseif(is_numeric($post)){
            $depositOrder = new PIShopDeposit( $post );
        }

        switch ( $column ) {

        case 'deposit':

            echo '<a href="' . esc_url( $depositOrder->get_edit_order_url() ) . '" class="order-view"><strong>#' . $depositOrder->get_meta( '_deposit_id', true ) . '</strong></a>';

            break;

        case 'deposit_total':
            echo wc_price( $depositOrder->get_total() );

            break;

        case 'deposit_status':

            $depositStatus = $depositOrder->get_status(); // order status
            echo sprintf( '<mark class="22 order-status %s tips"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $depositStatus ) ), wc_get_order_status_name( $depositStatus ) );

            break;

        case 'type':

                if(Pi_dpmw_partial_payment::is_deposit_payment_order($depositOrder->get_id())){
                    echo sprintf( '<mark class="order-status %s tips"><span>%s</span></mark>', 'status-completed' , 'Deposit payment' );
                }else{
                    echo sprintf( '<mark class="order-status %s tips"><span>%s</span></mark>', 'status-pending' , 'Pending payment' );
                }
                
    
        break;

        case 'parent_order':

            $parentId = $depositOrder->get_parent_id(); // order parent

            echo '<a href="' . esc_url( admin_url( 'post.php?post=' . $parentId ) . '&action=edit' ) . '" class="order-view">#' . $parentId . '</a>';

            break;

        case 'deposit_date':

            $order_date = $depositOrder->get_date_created();
            $formatted_order_date = $order_date->format('Y-m-d H:i:s');

            echo $formatted_order_date;
           
            break;

        }
    }

    public function deposit_metabox() {
        if ( empty( get_post_meta( get_the_id(), '_deposit_value', true ) ) ) {
            //return; // return if not deposit
        }

        add_meta_box( 'pending-payment-orders', __( 'Pending Payments','disable-payment-method-for-woocommerce'), [$this, 'pendingPaymentOrder'], ['shop_order', 'woocommerce_page_wc-orders'] );

    }

    public function pendingPaymentOrder($post) {
        //require_once DISABLE_PAYMENT_METHOD_RULE_WOOCOMMERCE_PLUGIN_DIR_PATH . 'public/class-shop-deposit-order.php';

        $args = array(
            'type'   => 'pi_pending_amt',
            'parent' => is_a($post, 'WC_Order') ? $post->get_id() : $post->ID,
        );

        $depositList = wc_get_orders( $args );?>

        <table class="wp-list-table widefat fixed striped table-view-excerpt ">
        <thead>

            <tr>
            <th><?php esc_html_e( 'Order Number', 'disable-payment-method-for-woocommerce' );?></th>
            
            <th><?php esc_html_e( 'Date', 'disable-payment-method-for-woocommerce' );?></th>
            <th><?php esc_html_e( 'Payment', 'disable-payment-method-for-woocommerce' );?></th>
            <th><?php esc_html_e( 'Status', 'disable-payment-method-for-woocommerce' );?></th>
            <th><?php esc_html_e( 'Total', 'disable-payment-method-for-woocommerce' );?></th>
            </tr>
        </thead>
        <tbody>
        <?php 
        if(!empty($depositList)){
        foreach ( $depositList as $key => $depositOrder ) {
            if ( $depositOrder->get_status() == 'completed' ) {
                $paymentStatus = __( 'Payment done','disable-payment-method-for-woocommerce' );
            } else {
                $paymentStatus = __( 'Due Payment','disable-payment-method-for-woocommerce' );
            }?>
                    <tr>
                    <td><?php echo '<a href="' . esc_url( $depositOrder->get_edit_order_url() ) . '" class="order-view"><strong>#' . $depositOrder->get_id() . '</strong></a>'; ?></td>

                    <td><?php $depositDate = human_time_diff( get_the_date( 'U' ), current_time( 'U' ) );
            if ( get_the_date( 'U' ) > current_time( 'U' ) - 86400 ) {
                echo $depositDate;
            } else {
                echo get_the_date( 'F j Y' );
            }?></td>

                    <td><?php echo esc_html( $paymentStatus ); ?></td>

                    <td>
                    <?php $depositStatus = $depositOrder->get_status(); // order status ?>
                    <?php echo sprintf( '<mark class="order-status %s tips"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $depositStatus ) ), wc_get_order_status_name( $depositStatus ) ); ?>
                    </td>

                    <td><?php echo wc_price( $depositOrder->get_total() ) ?></td>

                    </tr>

        <?php 
        }
        }else{
            echo '<tr><td colspan="5">'.__('Not a partially paid order','disable-payment-method-for-woocommerce').'</td></tr>';
        }
        
        ?>

        </tbody>
        </table>
    <?php }

    public function shop_order_status( $statuses ) {
        $statuses['wc-partial-paid'] = [
            'label'                     => _x( 'Partial Paid', 'Order status', 'disable-payment-method-for-woocommerce' ),
            'public'                    => false,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            /* translators: %s: number of orders */
            'label_count'               => _n_noop( 'Partial Paid <span class="count">(%s)</span>', 'Partial Paid <span class="count">(%s)</span>', 'disable-payment-method-for-woocommerce' ),
        ];

        return $statuses;
    }

    public function shows_order_status( $order_statuses ) {
        $order_statuses['wc-partial-paid'] = _x( 'Partial Paid', 'Order status', 'disable-payment-method-for-woocommerce' );
        return $order_statuses;
    }
}

Pi_dpmw_partial_payment_admin::get_instance();