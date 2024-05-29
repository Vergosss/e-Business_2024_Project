<div class="row">
    <div class="col-12 py-3 text-right">
        <?php if(Class_Pi_Dpmw_Add_Edit::ruleCount() < 3){ ?>
        <a class="btn btn-primary btn-sm mr-3" href="<?php echo admin_url( 'admin.php?page=pisol-dpmw-settings&tab=pi_dpmw_add_disable_rule' ); ?>"><span class="dashicons dashicons-plus"></span> <?php echo esc_html__('Add disable payment method rule', 'disable-payment-method-for-woocommerce'); ?></a>
        <?php }else{ ?>
            <div class="alert alert-warning text-center">
            You can only create 3 payment method rules in FREE version
            </div>
        <?php } ?>
    </div>
</div>
<?php

$disable_rules = get_posts(array(
    'post_type'=>'pi_dpmw_rules',
    'numberposts'      => -1
));

?>
<div id="pisol-dpmw-disable-rules-list-view">
<table class="table text-center table-striped" >
				<thead>
				<tr class="afrsm-head">
					<th><?php echo esc_html__( 'Payment method rule', 'disable-payment-method-for-woocommerce'); ?></th>
                    <th><?php echo esc_html__( 'Type', 'disable-payment-method-for-woocommerce'); ?></th>
					<th><?php echo esc_html__( 'Rule active', 'disable-payment-method-for-woocommerce'); ?></th>
					<th><?php echo esc_html__( 'Actions', 'disable-payment-method-for-woocommerce'); ?></th>
				</tr>
				</thead>
                <tbody >
                

<?php
if(count($disable_rules) > 0){
foreach($disable_rules as $disable_rule){
    $disable_rule_title  = get_the_title( $disable_rule->ID ) ? get_the_title( $disable_rule->ID ) : __('Disable payment method','disable-payment-method-for-woocommerce');
    $disable_rule_state = get_post_meta( $disable_rule->ID, 'pi_status', true );

    $type = get_post_meta( $disable_rule->ID, 'pi_rule_type', true );
    $type_html = '';
    if($type == 'disable' || empty($type)){
        $type_html = '<span class="badge badge-primary">Disable payment method</span>';
    }elseif($type== 'fees'){
        $type_html = '<span class="badge badge-danger">Payment method fees</span>';
    }
    echo '<tr id="pisol_tr_container_'.esc_attr($disable_rule->ID).'">';
    echo '<td class="pisol-scod-td-name"><a href="'.admin_url( '/admin.php?page=pisol-dpmw-settings&tab=pi_dpmw_add_disable_rule&action=edit&id='.$disable_rule->ID ).'">'.esc_html($disable_rule_title).'</a></td>';
    echo '<td>'.$type_html.'</td>';
    echo '<td>';
    echo '<div class="custom-control custom-switch">
    <input type="checkbox" value="1" '.checked($disable_rule_state,'on', false).' class="custom-control-input pi-dpmw-status-change" name="pi_status" id="pi_status_'.esc_attr($disable_rule->ID).'" data-id="'.esc_attr($disable_rule->ID).'">
    <label class="custom-control-label" for="pi_status_'.esc_attr($disable_rule->ID).'"></label>
    </div>';
    echo '</td>';
    echo '<td>';
    echo '<a href="'.wp_nonce_url(admin_url( '/admin.php?page=pisol-dpmw-settings&tab=pi_dpmw_add_disable_rule&action=edit&id='.$disable_rule->ID ), 'dpmw-edit').'" class="btn btn-primary btn-sm m-2" title="'.esc_attr__('Edit disabling rule','disable-payment-method-for-woocommerce').'"><span class="dashicons dashicons-admin-customizer"></span></a>';
    echo '<a href="'.wp_nonce_url(admin_url( '/admin.php?page=pisol-dpmw-settings&action=dpmw_disable_rule_delete&id='.$disable_rule->ID ), 'dpmw-delete').'" class="btn btn-warning btn-sm m-2"  title="'.esc_attr__('Delete disabling rule','disable-payment-method-for-woocommerce').'"><span class="dashicons dashicons-trash "></span></a>';
    echo '</td>';
    echo '</tr>';
}
}else{
    echo '<tr>';
    echo '<td colspan="4" class="text-center">';
    echo esc_html__('There are no payment method rules created yet','disable-payment-method-for-woocommerce' );
    echo '</td>';
    echo '</tr>';
}
?>
</tbody>
</table>
</div>