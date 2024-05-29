<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="row border-bottom align-items-center">
    <div class="col-12 py-2 bg-secondary">
        <strong class="h5 text-light"><?php echo esc_html(isset($_GET['action']) && $_GET['action'] === 'edit' ?  __('Edit rule','disable-payment-method-for-woocommerce') : __('Add new payment method rule','disable-payment-method-for-woocommerce')); ?></strong>
    </div>
</div>
<form method="post" id="pisol-dpmw-new-method">
<div class="row py-4 border-bottom align-items-center">
    <div class="col-12 col-sm-5">
        <label for="pi_status" class="h6"><?php echo esc_html__('Status','disable-payment-method-for-woocommerce'); ?></label>
    </div>
    <div class="col-12 col-sm">
        <div class="custom-control custom-switch">
        <input type="checkbox" value="1" <?php echo esc_attr($data['pi_status']); ?> class="custom-control-input" name="pi_status" id="pi_status">
        <label class="custom-control-label" for="pi_status"></label>
        </div>
    </div>
</div>

<div class="row py-4 border-bottom align-items-center">
    <div class="col-12 col-sm-5">
        <label for="pi_status" class="h6"><?php echo esc_html__('Rule type','disable-payment-method-for-woocommerce'); ?></label>
    </div>
    <div class="col-12 col-sm">
        <select name="pi_rule_type" id="pi_rule_type" class="form-control">
            <option value="disable" <?php selected($data['pi_rule_type'], 'disable'); ?>><?php echo esc_html__('Disable payment method','disable-payment-method-for-woocommerce'); ?></option>
            <option value="fees"  <?php selected($data['pi_rule_type'], 'fees'); ?>><?php echo esc_html__('Payment method fees','disable-payment-method-for-woocommerce'); ?></option>
        </select>
    </div>
</div>

<div class="row py-4 border-bottom align-items-center">
    <div class="col-12 col-sm-5">
        <label for="pi_title" class="h6 pi-rule-type" data-type="disable"><?php esc_html_e('Name the disable payment method rule','disable-payment-method-for-woocommerce'); ?> <span class="text-primary">*</span></label>
        <label for="pi_title" class="h6 pi-rule-type" data-type="fees"><?php esc_html_e('Name the payment method fees','disable-payment-method-for-woocommerce'); ?> <span class="text-primary">*</span></label>
    </div>
    <div class="col-12 col-sm">
        <input type="text" required value="<?php echo esc_attr($data['pi_title']); ?>" class="form-control" name="pi_title" id="pi_title">
    </div>
</div>


<div class="row py-4 border-bottom align-items-center">
    <div class="col-12 col-sm-5">
        <label for="disable_payment_methods" class="h6  pi-rule-type"  data-type="disable"><?php echo esc_html__('Disable this payment methods','disable-payment-method-for-woocommerce'); ?> <span class="text-primary">*</span></label>
        <label for="pi_title" class="h6 pi-rule-type" data-type="fees"><?php esc_html_e('Apply fees on this payment methods','disable-payment-method-for-woocommerce'); ?> <span class="text-primary">*</span></label>
    </div>
    <div class="col-12 col-sm">
        <select required class="form-control" name="disable_payment_methods[]" id="disable_payment_methods" multiple="multiple">
            <?php
                foreach($data['all_payment_methods'] as $payment_method => $payment_method_name){
                    $selected = is_array($data['disable_payment_methods']) && in_array($payment_method, $data['disable_payment_methods']) ? ' selected="selected" ' : '';
                    echo '<option value="'.esc_attr($payment_method).'" '.$selected.'>'.esc_html($payment_method_name).'</option>';
                }
            ?>
        </select>
    </div>
</div>

<div class="row py-3 border-bottom align-items-center pi-rule-type" data-type="fees">
    <div class="col-12 col-sm-5">
        <label for="pi_cost" class="h6"><?php echo __('Extra Fees','disable-payment-method-for-woocommerce'); ?> <span class="text-primary">*</span></label>
    </div>
    <div class="col-4">
        <select class="form-control" name="pi_fees_type" id="pi_fees_type">
            <option value="fixed" <?php selected( $data['pi_fees_type'], "fixed" ); ?>><?php _e('Fixed fees','disable-payment-method-for-woocommerce'); ?></option>
            <option value="percentage" <?php selected( $data['pi_fees_type'], "percentage" ); ?>><?php _e('Cart Subtotal Percentage','disable-payment-method-for-woocommerce'); ?></option>
        </select>
    </div>
    <div class="col-3 col-sm">
        <input type="text" value="<?php echo esc_attr($data['pi_fees']); ?>" class="form-control" name="pi_fees" id="pi_fees">
    </div>
</div>

<div class="row py-3 border-bottom align-items-center pi-rule-type" data-type="fees">
    <div class="col-12 col-sm-5">
        <label for="pi_fees_taxable" class="h6 pi-rule-type" data-type="fees"><?php echo __('Fee Taxable and its tax class','disable-payment-method-for-woocommerce'); ?></label>
    </div>
    <div class="col-4">
        <select class="form-control" name="pi_fees_taxable" id="pi_fees_taxable">
            <option value="no" <?php selected( $data['pi_fees_taxable'], "no" ); ?>><?php _e('No','disable-payment-method-for-woocommerce'); ?></option>
            <option value="yes" <?php selected( $data['pi_fees_taxable'], "yes" ); ?>><?php _e('Yes','disable-payment-method-for-woocommerce'); ?></option>
        </select>
    </div>
    <div class="col-3 col-sm">
    <select class="form-control" name="pi_fees_tax_class" id="pi_fees_tax_class">
        
        <?php 
        echo '<option value="standard" '.selected( $data['pi_fees_tax_class'], 'standard', true ).' >Standard</option>';
        if(!empty($data['tax_classes']) && is_array($data['tax_classes'])){
            foreach($data['tax_classes'] as $tax_class){
                echo '<option value="'.esc_attr($tax_class->slug).'" '.selected( $data['pi_fees_tax_class'], $tax_class->slug, true ).' >'.esc_html($tax_class->name).'</option>';
            }
        }
        ?>
        </select>
    </div>
</div>

<div class="row py-4 border-bottom align-items-center">
    <div class="col-12 col-sm-5">
        <label for="pi_currency" class="h6"><?php echo __('Apply for currency (useful for multi currency website only)','disable-payment-method-for-woocommerce'); ?></label><?php pisol_help::tooltip('Select the currency for which to apply the rule, if left blank it will apply for all the currency'); ?>
    </div>
    <div class="col-12 col-sm">
        <select name="pi_currency[]" id="pi_currency" multiple="multiple">
                <?php self::get_currency($data['pi_currency']); ?>
        </select>
    </div>
</div>

<div class="border-bottom">
<?php
$selection_rule_obj = new Pi_dpmw_selection_rule_main(
    __('Below conditions determine when to disable payment methods','disable-payment-method-for-woocommerce'),
    $data['pi_metabox'], $data
);
wp_nonce_field( 'add_disable_payment_method_rule', 'pisol_dpmw_nonce');
?>
</div>

<input type="hidden" name="post_type" value="pi_dpmw_rules">
<input type="hidden" name="post_id" value="<?php echo esc_attr($data['post_id']); ?>">
<input type="hidden" name="action" value="pisol_dpmw_save_disable_rule">
<input type="submit" value="Save Rule" name="submit" class="m-2 mt-5 btn btn-primary btn-lg" id="pi-dpmw-new-rule">
</form>
