<?php

class pisol_dpmw_options{

    public $plugin_name;

    private $settings = array();

    private $active_tab;

    private $this_tab = 'cod_deposit';

    private $tab_name = "Partial payment for order";

    private $setting_key = 'dpmw_cod_deposit_setting';
    
    

    function __construct($plugin_name){
        $this->plugin_name = $plugin_name;


        $this->settings = array(
           
            
            array('field'=>'pi_dpmw_enable_partial_payment', 'label'=>__('Enable partial payment for order'), 'desc'=>__('When enabled you can collect some amount from the customer as partial payment for order'), 'type'=>'switch', 'default'=>"0"),

            array('field'=>'pi_dpmw_partial_amt_calculation', 'label'=>__('Partial payment based on'), 'desc'=>__('How the partial payment amount will be calculated'), 'type'=>'select', 'default'=>"fixed", 'value'=>['fixed' => 'Fixed amount', 'percentage' => 'Percentage of Total']),

            array('field'=>'pi_dpmw_partial_amt', 'label'=>__('Partial payment'), 'desc'=>__('partial payment amt will be flat amount or percent of the total'), 'type'=>'text', 'default'=>"0"),

            array('field'=>'pi_dpmw_remove_payment_methods','label'=>__('Remove payment method when partial payment enabled'), 'desc'=>__('Remove Payment methods for when partial payment option is enabled'),'type'=>'multiselect','default'=>array('cod'), 'value'=>$this->paymentMethods()),

            array('field'=>'pi_dpmw_remove_payment_methods_selected','label'=>__('Remove payment method when partial payment selected'), 'desc'=>__('Remove Payment methods for when partial payment option is selected by the customer during checkout'),'type'=>'multiselect','default'=>array('cod'), 'value'=>$this->paymentMethods()),

            array('field'=>'pi_dpmw_excluded_products','label'=>__('Exclude product from partial payment'), 'desc'=>__('User will have to pay this product total amount even when they select for partial payment, It is just like you are excluding this product to be part of partial payment option E.g: if you set 10 as partial payment amount and there is no excluded product then user will pay 10 and checkout, but if there is some excluded product and non excluded product in the cart excluded product is of 20, so now user will pay 10+20 = 30 and then checkout, so he will be paying in full for the excluded product'),'type'=>'multiselect','default'=>array(), 'value'=>[], 'pro'=>true),

            array('field'=>'title', 'class'=> 'bg-primary text-light', 'class_title'=>'text-light font-weight-light h4', 'label'=>__("Labels"), 'type'=>"setting_category"),

            array('field'=>'pi_dpmw_partial_payment_title_checkout', 'label'=>__('Partial payment for the order'),'type'=>'text', 'default'=>'Partial payment for the order',  'desc'=>__('This label is shown on the checkout page next to the partial payment checkbox')),

            array('field'=>'pi_dpmw_txt_to_pay', 'label'=>__('To Pay'),'type'=>'text', 'default'=>'To Pay',  'desc'=>__('This label is shown on the checkout page next to the amount that has to be paid now')),

            array('field'=>'pi_dpmw_balance_to_pay', 'label'=>__('Due Payment'),'type'=>'text', 'default'=>'Due Payment',  'desc'=>__('This label is shown on the checkout page next to the amount that has to be paid afterwords')),

            array('field'=>'pi_dpmw_paid_amt', 'label'=>__('Paid amount'),'type'=>'text', 'default'=>'Paid amount',  'desc'=>__('This label is shown on the thank your page and order email next to the amount paid')),

            array('field'=>'pi_dpmw_balance_amt', 'label'=>__('Balance amount'),'type'=>'text', 'default'=>'Balance amount',  'desc'=>__('This label is shown on the thank your page and order email next to the amount remaining to be paid')),
        );
        
        $this->tab = sanitize_text_field(filter_input( INPUT_GET, 'tab'));
        $this->active_tab = $this->tab != "" ? $this->tab : 'default';
        
        if($this->this_tab == $this->active_tab){
            add_action($this->plugin_name.'_tab_content', array($this,'tab_content'));
        }


        add_action($this->plugin_name.'_tab', array($this,'tab'),10);

       
        $this->register_settings();

        add_filter( 'pre_update_option_pi_dpmw_partial_amt', [$this, 'onlyNumeric']);

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
        <input type="submit" class="mt-3 btn btn-primary btn-sm" value="Save Option" id="pi-dpmw-new-rule"/>
        </form>
       <?php
    }

    function paymentMethods(){
        if(!(isset($_GET['page']) && $_GET['page'] == 'pisol-dpmw-settings')) return array();
        $gateways = WC()->payment_gateways->payment_gateways();
        $enabled_gateways = [];

        if( !empty($gateways) ) {
            foreach( $gateways as $gateway ) {

                if( $gateway->enabled == 'yes' ) {

                    $enabled_gateways[$gateway->id] = $gateway->title;

                }
            }
        }
        return apply_filters('pisol_dpmw_payment_method_list',$enabled_gateways);
    }

    function onlyNumeric( $input ) {
        $output = '';
    
        // Check if input is numeric
        if ( is_numeric( $input ) ) {
            // If numeric, sanitize input and return
            $output = sanitize_text_field( $input );
        } else {
            // If non-numeric, add error message
            add_settings_error(
                'pi_dpmw_partial_amt',
                'pi_dpmw_partial_amt',
                'Please enter a numeric value for the Partial payment.'
            );
        }
    
        return $output;
    }
}

add_action('wp_loaded', function(){
    new pisol_dpmw_options($this->plugin_name);
});