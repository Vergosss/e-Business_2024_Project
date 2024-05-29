<?php
/**
 * Order details Summary
 *
 * This template displays a summary of Desposit payments for email template
 *
 * @version 1.5
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( !$order ) {
    return;
}
$args = array(
    'type'   => 'pi_pending_amt',
    'parent' => $order->get_id(),
);

$depositList = wc_get_orders( $args );

if(empty($depositList)) return;

?> <h3> <?php _e( 'Pending payments summary', 'disable-payment-method-for-woocommerce' )?> </h3>

<table border="0" cellpadding="20" cellspacing="0" style="width:100%; margin-bottom:15px">

    <thead>
    <tr>

        <th class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px;"><?php esc_html_e( 'Payment ID', 'disable-payment-method-for-woocommerce' );?> </th>
        <th class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px;"><?php esc_html_e( 'Status', 'disable-payment-method-for-woocommerce' );?> </th>
        <th class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px;"><?php esc_html_e( 'Amount', 'disable-payment-method-for-woocommerce' );?> </th>
        <th class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px;"><?php esc_html_e( 'Actions', 'disable-payment-method-for-woocommerce' );?> </th>

    </tr>

    </thead>

    <tbody>
    <?php

foreach ( $depositList as $key => $depositOrder ) {
?>
        <tr class="order_item">

            <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px;">
              <?php echo '<strong>#' . $depositOrder->get_meta( '_deposit_id', true ) . '</strong>'; ?>

            </td>
            <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px;">
            <?php $depositStatus = $depositOrder->get_status(); // order status ?>
            <?php echo wc_get_order_status_name( $depositStatus ); ?>
            </td>
            <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px;">
            <?php echo wc_price( $depositOrder->get_total() ); ?>
            </td>


            <td class="td" style="color: #636363; border: 1px solid #e5e5e5; vertical-align: middle; padding: 12px;">
            <?php
if ( $depositStatus == 'completed' ) {
    echo 'Completed';
    } elseif($depositStatus == 'pending') {
        /* translators: %s: Customer first name */
        printf( '<a href="%s" class="woocommerce-button button deposit-pay-button">%s</a>', esc_url( $depositOrder->get_checkout_payment_url() ),__( 'Make Payment ', 'disable-payment-method-for-woocommerce' ));
    }else{
        echo wc_get_order_status_name( $depositStatus );
    }
    ?>
            </td>
        </tr>
        <?php
}
?>

    </tbody>

    <tfoot>


    </tfoot>
</table>
