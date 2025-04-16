<?php

use Delisend\WC\Lib\WC_Delisend_Definitions;
use Delisend\WC\Lib\WC_Delisend_Helper;

if (!defined('ABSPATH')) {
    exit;
}

$text_domain = WC_Delisend_Definitions::TEXT_DOMAIN;

$customer_rating = WC_Delisend_Helper::wc_delisend()->get_rating_handler()->getByOrderId($order->get_id());
$send_rating = WC_Delisend_Helper::wc_delisend()->get_rating_handler()->get_by_submit_order_to_delisend($order->get_id());
if ($customer_rating === null) {
    $is_automatic_check = WC_Delisend_Helper::wc_delisend()->is_automatic_check_enabled();
    if ($is_automatic_check === true) {
        $customer_rating = WC_Delisend_Helper::wc_delisend()->delisend_check_customer_rating_from_widget($order->get_id(), $order->get_customer_id());
    }
}
if (!empty($customer_rating)) {
    $rating_data = $customer_rating['rating_data']['results'];
} else {
    $rating_data = [];
}
?>

<div id="woocommerce_delisend_order_info_box">
    <div id="delisend_info">
        <?php if (empty($customer_rating) || empty($rating_data)) : ?>
            <div id="no_delisend_message" class="rank-wrapper3">
                <?php echo __('The risk of returning the goods with this order was not identified.', $text_domain); ?>
            </div>
        <?php else: ?>
            <div id="rating_delisend_message" class="rank-wrapper3">
                <table id="delisend_view_variant" class="r">
                    <tbody>
                    <tr>
                        <td class="td01" style="text-align: center; vertical-align: middle;">
                            <div id="delisend_view_variant_content">
                                <img src="<?php echo esc_url(WC_Delisend_Helper::wc_delisend()->plugins_url('/assets/images/' . $rating_data['variant'] . '-64.png')); ?>"
                                     style="height: 64px; width: 64px;" alt="<?php echo $rating_data['variant']; ?>"
                                     data-path="<?php echo esc_url(WC_Delisend_Helper::wc_delisend()->plugins_url('/assets/images/')); ?>"/>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table id="delisend_view_stat" class="r">
                    <tbody>
                    <tr>
                        <td class="td01"><strong><?php echo __('Views', $text_domain); ?>:</strong></td>
                        <td class="td02">
                            <div id="delisend_view_stat_content">
                                <?php echo $rating_data['count_views']; ?>×
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table id="delisend_review_stat" class="r">
                    <tbody>
                    <tr>
                        <td class="td01"><strong><?php echo __('Review', $text_domain); ?>:</strong></td>
                        <td class="td02">
                            <div id="delisend_review_stat_content">
                                <?php echo $rating_data['count_ratings']; ?>×
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table id="delisend_review_hazard_score" class="r">
                    <tbody>
                    <tr>
                        <td class="td01"><strong><?php echo __('Hazard Score', $text_domain); ?>:</strong></td>
                        <td class="td02">
                            <div id="delisend_hazard_score_content">
                                <?php echo $rating_data['hazard_score']; ?>%
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="delisend-rating-btn-group">
            <button type="button" id="delisend-check-customer" class="button button-primary" data-order_id="<?php echo $order->get_id(); ?>"
                    data-customer_id="<?php echo $order->get_customer_id(); ?>">
                <?php echo __('Verify customer', $text_domain); ?>
            </button>
            <button type="button" id="delisend-add-rating" class="button button-link-delete" data-order_id="<?php echo $order->get_id(); ?>"
                    data-customer_id="<?php echo $order->get_customer_id(); ?>">
                <?php if (empty($send_rating)): ?>
                    <?php echo __('Evaluate customer', $text_domain); ?>
                <?php else: ?>
                    <?php echo __('The rating was sent', $text_domain); ?>
                <?php endif; ?>
            </button>
        </div>
    </div>
</div>
