<?php

use Delisend\WC\Lib\WC_Delisend_Definitions;
use Delisend\WC\Lib\WC_Delisend_Helper;

if (!defined('ABSPATH')) { exit; }

$text_domain = WC_Delisend_Definitions::TEXT_DOMAIN;
$order = wc_get_order( $order_id );

//dump($order->get_shipping_methods());
//dump($order);

$data = [
    "customer_ip" =>  "customer_ip_address",
    "customer_user_agent" => "customer_user_agent",
];

//dump($data);
//dump(WC_Delisend_Helper::wc_delisend()->is_automatic_check_enabled());

?>
<div id="woocommerce_delisend_order_info_box">
	<div id="delisend_info">

        <?php if (empty($customer_rating) ) : ?>

            <div id="no_delisend_message" class="rank-wrapper3">
                <?php echo __('The risk of returning the goods with this order was not identified.', $text_domain); ?>
            </div>

            <?php //print_r($order->get_address()) ?>
            <div class="delisend-rating-btn-group">
                <button type="button" id="delisend-check-customer" class="button button-primary" data-order_id="<?php echo $order->get_id(); ?>">Overiť zákazníka</button>
                <button type="button" id="delisend-add-rating" class="button button-link-delete" data-order_id="<?php echo $order->get_id(); ?>">Hodnotiť zákazníka</button>
            </div>

		<?php endif; ?>
	</div>
</div>
