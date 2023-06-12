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
            jQuery('body').on('click', '#delisend-check-customer', function (e) {
                e.preventDefault();

                let order_id = jQuery(this).data("order_id");

                jQuery.ajax({
                    type: 'POST',
                    url: window.ajaxurl,
                    data: {
                        action: 'delisend_check_customer_rating',
                        order_id: order_id,
                    }, success: function (response) {
                        console.log(response);
                    }
                });
            });

            jQuery('body').on('click', '#delisend-add-rating', function (e) {
                e.preventDefault();

                console.log('#delisend-add-rating');
                console.log('Post new rating to Delisend');

                let order_id = jQuery(this).data("order_id");

                jQuery.confirm({
                    theme: 'light',
                    columnClass: 'col-md-4 col-md-offset-4',
                    title: 'Add customer to Delisend blacklist',
                    content: '' +
                        '<form action="" class="formName">' +
                        '<div class="form-group">' +
                        '<div id="select-wrapper" class="rank-wrapper3">' +
                        '<div id="rank-wrapper1" class="rank"><label for="rank1">Veľmi spokojný<input type="radio" value="1" id="rank1" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                        '<div id="rank-wrapper2" class="rank"><label for="rank2">Mierne spokojný<input type="radio" value="2" id="rank2" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                        '<div id="rank-wrapper3" class="rank"><label for="rank3">Neutrálny spokojný<input type="radio" value="3" id="rank3" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                        '<div id="rank-wrapper4" class="rank"><label for="rank4">Mierne nespokojný<input type="radio" value="4" id="rank4" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                        '<div id="rank-wrapper5" class="rank"><label for="rank5">Veľmi nespokojný<input type="radio" value="5" id="rank5" name="rating" class="rank-input"> <span class="checkmark"></span></label></div>' +
                        '</div>' +
                        '<label>Pridajte komentár:</label>' +
                        '<textarea placeholder="Vaša obchodná skúsenosť so zákazníkom alebo adresou doručenia..." id="delisend-input-rating-comment" name="delisend-rating-comment" class="delisend-input-rating-comment form-control"></textarea>' +
                        '</div>' +
                        '</form>',
                    buttons: {
                        formSubmit: {
                            text: 'Submit',
                            btnClass: 'btn-blue',
                            action: function () {

                                let comment = this.$content.find("#delisend-input-rating-comment").val();
                                let rating = this.$content.find("input[type='radio'][name=rating]:checked").val();

                                jQuery.alert('Your comment is: ' + comment);
                                jQuery.ajax({
                                    type: 'POST',
                                    url: window.ajaxurl,
                                    data: {
                                        order_id: order_id,
                                        rating: rating,
                                        comment: comment,
                                        action: 'delisend_create_customer_rating',
                                    }, success: function (response) {
                                        console.log(response);
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
                            console.log('form submit');
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
