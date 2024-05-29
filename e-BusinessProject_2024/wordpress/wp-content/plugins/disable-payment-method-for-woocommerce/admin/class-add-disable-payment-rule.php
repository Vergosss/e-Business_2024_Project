<?php

class Class_Pi_Dpmw_Add_Edit{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'pi_dpmw_add_disable_rule';

    private $tab_name = "Add Disable payment method rule";

    private $setting_key = 'pi_dpml_add_disable_rule';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

       
        $this->tab = sanitize_text_field(filter_input( INPUT_GET, 'tab'));
        $this->active_tab = $this->tab != "" ? $this->tab : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        //add_action($this->plugin_name.'_tab', array($this,'tab'),2);
        add_action('wp_ajax_pisol_dpmw_change_status', array(__CLASS__,'enableDisable'));
        add_action('wp_ajax_pisol_dpmw_save_disable_rule', array($this,'ajaxSave'));

    }

    
    function tab(){
        $page =  sanitize_text_field(filter_input( INPUT_GET, 'page'));
        ?>
        <a class=" px-3 text-light d-flex align-items-center  border-left border-right  <?php echo esc_attr(($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary')); ?>" href="<?php echo admin_url( 'admin.php?page='.$page.'&tab='.$this->this_tab ); ?>">
            <?php esc_html( $this->tab_name); ?> 
        </a>
        <?php
    }

    function tab_content(){
       $this->addEditShippingMethod();
    }

    function addEditShippingMethod(){
        $data = $this->formDate();

        if($data === false){
            echo '<div class="alert alert-danger mt-2">'.__('Rule you are trying to edit does not exist','disable-payment-method-for-woocommerce').'</div>';
            return;
        }
        
        include plugin_dir_path( __FILE__ ) . 'partials/add-disable-rule.php';
    }

    function ajaxSave(){
        $message = array();
        $error =  $this->validate();
        if(is_wp_error($error)){
            $error_msg = $this->showError($error);
            wp_send_json( array('error'=> $error_msg) );
        }else{
            /** Save form and redirect to list */
            $save_form_result = $this->saveForm();
            if($save_form_result === false){
                wp_send_json( array('error'=>array("There was some error in saving refresh the page and try again")));
            }else{
                if($save_form_result !== true){
                    $redirect_url =  $save_form_result;
                    wp_send_json( array('success'=>"Payment method rule saved", 'redirect' => $redirect_url));
                }
                wp_send_json( array('success'=>"Payment method rule saved"));
            }
        }
    }

    function formDate(){
        $action_value = sanitize_text_field(filter_input( INPUT_GET, 'action'));
        $id_value     = sanitize_text_field(filter_input( INPUT_GET, 'id'));
        $data = array();
       
        $data['all_payment_methods'] = self::allPaymentMethods();
        
        if ( isset( $action_value ) && 'edit' === $action_value ) {

            if(!self::methodExist($id_value)) return false;

            $data['post_id']                 = $id_value;
            $data['pi_status']               = get_post_meta( $data['post_id'], 'pi_status', true );
            $data['pi_title']               =  get_the_title( $data['post_id'] );
            $data['pi_metabox']              = get_post_meta( $data['post_id'], 'pi_metabox', true );
            $data['pi_condition_logic'] = empty(get_post_meta( $data['post_id'], 'pi_condition_logic', true )) ? 'and' : get_post_meta( $data['post_id'], 'pi_condition_logic', true ); 
            $data['disable_payment_methods'] = get_post_meta( $data['post_id'], 'disable_payment_methods', true );

            $data['pi_rule_type'] = get_post_meta( $data['post_id'], 'pi_rule_type', true );

            if(empty($data['pi_rule_type'])){
                $data['pi_rule_type'] = 'disable';
            }

            $data['pi_fees_type']                 = get_post_meta( $data['post_id'], 'pi_fees_type', true );

            $data['pi_fees']                 = get_post_meta( $data['post_id'], 'pi_fees', true );

            $data['pi_currency']    = get_post_meta($data['post_id'], 'pi_currency', true);

            $data['pi_fees_taxable'] = empty(get_post_meta( $data['post_id'], 'pi_fees_taxable', true )) ? 'no' : get_post_meta( $data['post_id'], 'pi_fees_taxable', true );

            $data['pi_fees_tax_class'] = get_post_meta( $data['post_id'], 'pi_fees_tax_class', true );

        } else {
            $data['post_id']                = '';
            $data['pi_status']               = '';
            $data['pi_title']                = '';
            $data['pi_condition_logic']           = 'and';
            $data['pi_metabox']              = array();
            $data['disable_payment_methods'] = array();
            $data['pi_rule_type'] = 'disable';

            $data['pi_fees_type']                 = '';
            $data['pi_fees']         = 0;

            $data['pi_currency'] = [];

            $data['pi_fees_taxable'] = 'no';
            $data['pi_fees_tax_class'] = '';
        }
        
        $data['pi_status']       = ( ( ! empty( $data['pi_status'] ) && 'on' === $data['pi_status'] ) || empty( $data['pi_status'] ) ) ? 'checked' : '';
        $data['pi_title']        = ! empty( $data['pi_title'] ) ? esc_attr( stripslashes( $data['pi_title'] ) ) : '';

        $data['tax_classes'] = class_exists('WC_Tax') ? WC_Tax::get_tax_rate_classes() : array();
       
        return $data;
    }

    static function allPaymentMethods(){
        
       $all_payment_methods = array();
       if(function_exists('WC') && is_object(WC()->payment_gateways)){
            $payment_methods = WC()->payment_gateways->payment_gateways();
            
            foreach( $payment_methods as $key => $gateway ){
                   $title = $method_title = $gateway->get_method_title() ? $gateway->get_method_title() : $gateway->get_title();
                   
                   $all_payment_methods[$key] = $title;
            }
            $all_payment_methods['cod'] = 'Cash on Delivery';
        }
       return apply_filters('pi_dpmw_available_payment_methods', $all_payment_methods);
    }

    static function ruleCount(){
        return (new WP_Query(['post_type' => 'pi_dpmw_rules', 'numberposts'      => -1]))->found_posts;
    }

    static function methodExist($id){

        if(!filter_var($id, FILTER_VALIDATE_INT)) return false;

        $post_exists = (new WP_Query(['post_type' => 'pi_dpmw_rules', 'p'=>$id]))->found_posts > 0;

        return $post_exists;
    }

    function validate(){
        $error = new WP_Error();

        if ( !current_user_can('editor') && !current_user_can('administrator') 
        ) {
            $error->add( 'access', 'You are not authorized to make this changes ' );
        } 

        if ( ! isset( $_POST['pisol_dpmw_nonce'] ) || ! wp_verify_nonce( $_POST['pisol_dpmw_nonce'], 'add_disable_payment_method_rule' ) 
        ) {
            $error->add( 'invalid-nonce', 'Form has expired Reload the form and try again ' );
        } 

        if ( empty( $_POST['pi_title'] ) ) {
            $error->add( 'empty', 'Rule name cant be empty' );
        }

        if( empty($_POST['disable_payment_methods']) || !is_array($_POST['disable_payment_methods'])){
            $error->add( 'empty', 'Select the payment method to disable' );
        }

       

        if ( empty( $_POST['pi_selection'] ) ) {
            $error->add( 'empty', 'You have not added any Selection Rules' );
        }


        if ( !empty( $error->get_error_codes() ) ) {
            return $error;
        }
    
        return true;
    }

    function showError($error){

        return $error->get_error_messages();
    }

    /**
     * return true (in case of editing of existing method), 
     * false, 
     * redirect url (in case of newly created shipping method)
     */
    function saveForm(){

        $redirect_url = "";

        if ( ! isset( $_POST['pisol_dpmw_nonce'] ) || ! wp_verify_nonce( $_POST['pisol_dpmw_nonce'], 'add_disable_payment_method_rule' ) 
        ) {

        return false;
        } 

        if ( empty( $_POST ) || !current_user_can( 'manage_options' )) {
			return false;
        }
        
        $post_type = sanitize_text_field(filter_input( INPUT_POST, 'post_type'));
		if ( isset( $post_type ) && 'pi_dpmw_rules' === $post_type ) {
            if ($_POST['post_id'] === '' || empty($_POST['post_id'])) {
				$shipping_method_post = array(
					'post_title'  => sanitize_text_field($_POST['pi_title']),
					'post_status' => 'publish',
					'post_type'   => 'pi_dpmw_rules',
				);
				$post_id  = wp_insert_post( $shipping_method_post );
                $redirect_url = admin_url( '/admin.php?page=pisol-dpmw-settings&tab=pi_dpmw_add_disable_rule&action=edit&id='.$post_id);
			} else {
				$shipping_method_post = array(
					'ID'          => (int)sanitize_text_field($_POST['post_id']),
					'post_title'  => sanitize_text_field($_POST['pi_title']),
					'post_status' => 'publish',
				);
				$post_id  = wp_update_post( $shipping_method_post );
            }
            
            if ( isset( $_POST['pi_status'] ) ) {
				update_post_meta( $post_id, 'pi_status', "on" );
			} else {
				update_post_meta( $post_id, 'pi_status', "off");
			}

            if ( isset( $_POST['pi_rule_type'] ) ) {
				update_post_meta( $post_id, 'pi_rule_type', sanitize_text_field( $_POST['pi_rule_type'] ));
			} else {
				update_post_meta( $post_id, 'pi_rule_type', 'disable');
			}
			
			if ( isset( $_POST['pi_fees_type'] ) ) {
				update_post_meta( $post_id, 'pi_fees_type', sanitize_text_field( $_POST['pi_fees_type'] ) );
			}

            if ( isset( $_POST['pi_fees'] ) ) {
                if(!empty($_POST['pi_fees'])){
                    update_post_meta( $post_id, 'pi_fees', sanitize_textarea_field( $_POST['pi_fees'] ) );
                }else{
                    update_post_meta( $post_id, 'pi_fees', 0 );
                }
			}

            if ( isset( $_POST['pi_condition_logic'] ) ) {
				update_post_meta( $post_id, 'pi_condition_logic', sanitize_text_field( $_POST['pi_condition_logic'] ) );
            }else{
                update_post_meta( $post_id, 'pi_condition_logic', 'and' );
            }


            $pi_selection  = array();
            $conditions = isset($_POST['pi_selection']) ? $_POST['pi_selection'] : array();
            if(is_array($conditions)){
            foreach($conditions as $key => $condition){
                $pi_selection[] = array(
                    'pi_condition'=> isset($condition['pi_dpmw_condition']) ?sanitize_text_field($condition['pi_dpmw_condition']) : '',
                    'pi_logic'=>isset($condition['pi_dpmw_logic']) ? sanitize_text_field($condition['pi_dpmw_logic']) : "",
                    'pi_value'=> isset($condition['pi_dpmw_condition_value']) ? self::sanitizeArray($condition['pi_dpmw_condition_value']) : ""
                );
            }
            }

            if(is_array($pi_selection)){
                update_post_meta( $post_id, 'pi_metabox', $pi_selection );
            }

            if(isset($_POST['disable_payment_methods']) && is_array($_POST['disable_payment_methods'])){
                update_post_meta( $post_id, 'disable_payment_methods', self::sanitizeArray($_POST['disable_payment_methods']) );
            }else{
                update_post_meta( $post_id, 'disable_payment_methods',array());
            }

            if(isset($_POST['pi_currency']) && is_array($_POST['pi_currency'])){
                update_post_meta( $post_id, 'pi_currency', self::sanitizeArray($_POST['pi_currency']) );
            }else{
                update_post_meta( $post_id, 'pi_currency', []);
            }

            if ( isset( $_POST['pi_fees_taxable'] ) ) {
				update_post_meta( $post_id, 'pi_fees_taxable', sanitize_text_field( $_POST['pi_fees_taxable'] ) );
			}else{
                update_post_meta( $post_id, 'pi_fees_taxable', 'no' );
            }

            if ( isset( $_POST['pi_fees_tax_class'] ) ) {
				update_post_meta( $post_id, 'pi_fees_tax_class', sanitize_textarea_field( $_POST['pi_fees_tax_class'] ) );
			}

            
            if(!empty($redirect_url)){
                return $redirect_url;
            }

            return true;

        }
    }

    static function sanitizeArray($values){
        if(is_array($values)){
           $values = array_map("sanitize_text_field", $values);
           return $values;
        }

        return sanitize_text_field($values);
    }

    static function enableDisable(){
        check_ajax_referer( 'dpmw-actions' );
        
        $post_id = sanitize_text_field(filter_input(INPUT_POST,'id'));
        $status = sanitize_text_field(filter_input(INPUT_POST,'status'));

        if(!current_user_can('administrator') || empty($post_id)) return;
        
        if ( !empty($status) ) {
            update_post_meta( $post_id, 'pi_status', "on" );
        } else {
            update_post_meta( $post_id, 'pi_status', "off");
        }
        
    }

    static function get_currency($saved_currency = array()){
        if(!is_array($saved_currency)) $saved_currency = array();

        $all_currencies = get_woocommerce_currencies();
        foreach($all_currencies as $currency => $name){
            $selected = in_array($currency, $saved_currency) ? 'selected' : '';
            echo '<option value="'.$currency.'" '.$selected.'>'.$name.'</option>';
        }
    }
    
}

new Class_Pi_Dpmw_Add_Edit($this->plugin_name);