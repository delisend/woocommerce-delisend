/* global wc_delisend_admin_js */

(function ($) {
    'use strict';

    let scripts = {

        initialize: function () {
            this.events();
            this.select();
        },

        // Custom js setting *
        events: function () {
            jQuery('body')
                .on('click', '#delisend-check-customer', function (e) {
                    e.preventDefault();

                    let order_id = jQuery(this).data("order_id");
                    let customer_id = jQuery(this).data("customer_id");

                    jQuery.ajax({
                        type: 'POST',
                        url: window.ajaxurl,
                        dataType: "json",
                        data: {
                            action: 'delisend_check_customer_rating',
                            order_id: order_id,
                            customer_id: customer_id,
                        }, success: function (response) {
                            if (response.results !== undefined) {
                                let img_path = jQuery("#delisend_view_variant_content img").data("path");
                                jQuery('#delisend_view_variant_content img').attr('src', img_path + response.results.variant + '-64.png');
                                jQuery("#delisend_view_stat_content").html(response.results.count_views + "x");
                                jQuery("#delisend_review_stat_content").html(response.results.count_ratings + "x");
                                jQuery("#delisend_hazard_score_content").html(response.results.hazard_score + "%");
                            }
                        }
                    });
                })
                .on('click', '#delisend-add-rating', function (e) {

                    e.preventDefault();

                    let order_id = jQuery(this).data("order_id");
                    let customer_id = jQuery(this).data("customer_id");

                    jQuery.confirm({
                        theme: 'light',
                        columnClass: 'col-md-4 col-md-offset-4',
                        title: 'Add customer to Delisend Blacklist',
                        content: '' +
                            '<form action="" class="formName delisend-rating-form">' +
                            '<div class="form-group">' +
                            '<label class="form-label"><strong>Ohodnoť prístup zákazníka:</strong></label>' +
                            '<div id="select-wrapper" class="rank-wrapper">' +
                            '<div id="rank-wrapper1" class="rank"><label for="rank1">Veľmi spokojný<input type="radio" value="1" id="rank1" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                            '<div id="rank-wrapper2" class="rank"><label for="rank2">Mierne spokojný<input type="radio" value="2" id="rank2" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                            '<div id="rank-wrapper3" class="rank"><label for="rank3">Neutrálny spokojný<input type="radio" value="3" id="rank3" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                            '<div id="rank-wrapper4" class="rank"><label for="rank4">Mierne nespokojný<input type="radio" value="4" id="rank4" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                            '<div id="rank-wrapper5" class="rank"><label for="rank5">Veľmi nespokojný<input type="radio" value="5" id="rank5" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                            '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                            '<label class="form-label"><strong>Pridaj komentár:</strong></label>' +
                            '<div id="delisend-textarea-wrapper" class="delisend-comment-wrapper">' +
                            '<textarea placeholder="Vaša obchodná skúsenosť so zákazníkom alebo adresou doručenia..." id="delisend-input-rating-comment" name="delisend-rating-comment" class="delisend-input-rating-comment form-control"></textarea>' +
                            '</div>' +
                            '</div>' +
                            '</form>',
                        buttons: {
                            formSubmit: {
                                text: 'Submit',
                                btnClass: 'btn-blue',
                                action: function () {

                                    let rating = this.$content.find("input[type='radio'][name=rating]:checked").val();
                                    let comment = this.$content.find("#delisend-input-rating-comment").val();

                                    if (!rating) {
                                        $.alert('Choose one of the rating options.');
                                        return false;
                                    }

                                    let messageComment = '';
                                    if (comment) {
                                        messageComment = ': <strong>' + comment + '</strong>';
                                    }

                                    let sendRating = jQuery.alert('Your review' + messageComment + ' is being sent, it may take a little while.' );

                                    jQuery.ajax({
                                        type: 'POST',
                                        url: window.ajaxurl,
                                        dataType: "json",
                                        data: {
                                            order_id: order_id,
                                            customer_id: customer_id,
                                            rating: rating,
                                            comment: comment,
                                            action: 'delisend_create_customer_rating',
                                        }, success: function (response) {
                                            console.log(response);
                                            toastr.options = {
                                                "closeButton": false,
                                                "debug": false,
                                                "newestOnTop": false,
                                                "progressBar": false,
                                                "positionClass": "toast-top-center",
                                                "preventDuplicates": false,
                                                "onclick": null,
                                                "showDuration": "300",
                                                "hideDuration": "1000",
                                                "timeOut": "5000",
                                                "extendedTimeOut": "1000",
                                                "showEasing": "swing",
                                                "hideEasing": "linear",
                                                "showMethod": "fadeIn",
                                                "hideMethod": "fadeOut"
                                            }
                                            toastr["success"](response.message);
                                        }
                                    });
                                }
                            },
                            cancel: function () {
                                //close
                            },
                        },
                        onContentReady: function () {
                            // bind to events
                            let jc = this;
                            this.$content.find('form').on('submit', function (e) {
                                // if the user submits the form by pressing enter in the field.
                                e.preventDefault();
                                jc.$formSubmit.trigger('click'); // reference the button and click it
                            });
                        }
                    });
                });
        },

        select: function () {
            $('#wc_delisend_shipping_filter').select2({});
        }
    };

    // Document ready
    $(document).ready(function () {
        scripts.initialize();
    });

}(jQuery));
