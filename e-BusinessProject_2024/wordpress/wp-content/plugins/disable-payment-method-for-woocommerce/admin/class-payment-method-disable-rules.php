<?php

class Class_Pisol_Dpmw_Disable_Rules_list{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'default';

    private $tab_name = "Payment method rules";

    private $setting_key = 'pi_dpmw_list_shipping';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;

       
        $this->tab = sanitize_text_field(filter_input( INPUT_GET, 'tab'));
        $this->active_tab = $this->tab != "" ? $this->tab : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),1);

        $action = sanitize_text_field(filter_input(INPUT_GET, 'action'));
        if($action == 'dpmw_disable_rule_delete'){
            $this->post_id = sanitize_text_field(filter_input(INPUT_GET, 'id'));
            add_action('init',array($this,'deletePost' ));
        }

    }

    
    function tab(){
        ?>
        <a class=" px-3 text-light d-flex align-items-center  border-left border-right  <?php echo esc_attr(($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary')); ?>" href="<?php echo admin_url( 'admin.php?page='.sanitize_text_field($_GET['page']).'&tab='.$this->this_tab ); ?>">
            <?php echo esc_html( $this->tab_name); ?> 
        </a>
        <?php
    }

    function tab_content(){
       $this->listShippingMethod();
    }

    function listShippingMethod(){
        
        include plugin_dir_path( __FILE__ ) . 'partials/list-disable-payment-rules.php';
    }

    function deletePost(){
        $submitted_value = isset($_REQUEST['_wpnonce']) ? sanitize_text_field($_REQUEST['_wpnonce']) : '';
        if(!wp_verify_nonce($submitted_value, 'dpmw-delete')){
            wp_die( 'Your page has expired, refresh and try again' );
        }

        if(!current_user_can( 'manage_options' )) {
            wp_safe_redirect(  admin_url( '/admin.php?page=pisol-dpmw-settings' )  );
            exit();
        }
        wp_delete_post($this->post_id);
        wp_safe_redirect(  admin_url( '/admin.php?page=pisol-dpmw-settings' )  );
        exit();
    }
    
}

new Class_Pisol_Dpmw_Disable_Rules_list($this->plugin_name);