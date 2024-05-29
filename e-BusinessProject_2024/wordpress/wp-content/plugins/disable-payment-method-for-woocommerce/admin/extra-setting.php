<?php

class pisol_dpmw_extra_settings{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'extra_options';

    private $tab_name = "Extra settings";

    private $setting_key = 'dpmw_extra_setting';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;


        $this->settings = array(
           
            array('field'=>'pisol_dpmw_show_system_name', 'label'=>__('Show System name of shipping method on checkout page'), 'desc'=>__('After enabling go to the checkout page and you will see the system name of the shipping method below the shipping method name, Only admin can see this shipping method name your customer will not see it'), 'type'=>'switch', 'default'=>"0"),
        );
        
        $this->tab = sanitize_text_field(filter_input( INPUT_GET, 'tab'));
        $this->active_tab = $this->tab != "" ? $this->tab : 'default';

        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),20);

       
        $this->register_settings();

        add_action('woocommerce_after_shipping_rate', array($this,'getMethodName'),9999,2);
    }

    
    function delete_settings(){
        foreach($this->settings as $setting){
            delete_option( $setting['field'] );
        }
    }

    function register_settings(){   

        foreach($this->settings as $setting){
            register_setting( $this->setting_key, $setting['field']);
        }
    
    }

    function tab(){
        $page = sanitize_text_field(filter_input( INPUT_GET, 'page'));
        ?>
        <a class=" px-3 py-2 text-light d-flex align-items-center  border-left border-right  <?php echo ($this->active_tab == $this->this_tab ? 'bg-primary' : 'bg-secondary'); ?>" href="<?php echo admin_url( 'admin.php?page='.$page.'&tab='.$this->this_tab ); ?>">
            <?php _e( $this->tab_name); ?> 
        </a>
        <?php
    }

    function tab_content(){
        
       ?>
        <form method="post" action="options.php"  class="pisol-setting-form">
        <?php settings_fields( $this->setting_key ); ?>
        <?php
            foreach($this->settings as $setting){
                new pisol_class_form_dpmw($setting, $this->setting_key);
            }
        ?>
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" />
        </form>
       <?php
    }

    function getMethodName($method, $index){
        $view_name = get_option('pisol_dpmw_show_system_name', 0);
        $require_capability = Pi_dpmw_Menu::getCapability();
        if(current_user_can( $require_capability ) && !empty($view_name)){
            echo '<small>System name: <strong>'.$method->get_id().'</strong></small>';
        }
    }
}

new pisol_dpmw_extra_settings($this->plugin_name);