<?php

use Delisend\WC\Lib\WC_Delisend_Definitions;

if (!defined('ABSPATH')) { exit; }


$text_domain = WC_Delisend_Definitions::TEXT_DOMAIN;
$order_data = wc_get_order( $order_id );

if (!empty($order_vat_data) && !empty($order_vat_data['taxes'])) {
	if ($order_vat_data['invoice_currency'] == $order_vat_data['vat_currency']) {
		$invoice_currency_column_css = 'hidden';
	}
	$order_vat_totals = $order_vat_data['totals'];
}
?>
<div id="woocommerce_delisend_order_info_box">
	<div id="delisend_info">

        <div class="delisend_rating">
            <h4 class="title"><?php echo __('Totals by VAT rate', $text_domain); ?></h4>

            <div id="chart-container"></div>

            <button type="button" id="" class="button button-primary">Overiť zákazíka</button>
            <button type="button" id="" class="button button-link-delete">Nahlásiť zakazníka</button>
            <div><?php

                ?></div>
        </div>

		<?php if(empty($order_vat_totals) || (get_value('total', $order_vat_totals, 0) == 0)) : ?>
			<div id="no_delisend_message">
                <?php echo __('The risk of returning the goods with this order was not identified.', $text_domain); ?>
            </div>
		<?php else: ?>

		<?php endif; ?>
	</div>
</div>
