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

                console.log('#delisend-check-customer');
                console.log(delisend_admin_common);

                jQuery.ajax({
                    type: 'POST',
                    url: window.ajaxurl,
                    data: {
                        name: 'name',
                        action: 'delisend_check_customer_rating',
                        order_id: 6514
                    }, success: function (response) {
                        console.log(response);
                    }
                });
            });

            jQuery('body').on('click', '#delisend-add-rating', function (e) {
                e.preventDefault();
                let order_id = jQuery(this).data("order_id");
                console.log('#delisend-add-rating');
                $.confirm({
                    title: 'Prompt!',
                    content: '' +
                        '<form action="" class="formName">' +
                        '<div class="form-group">' +
                        '<label>Enter something here</label>' +
                        '<input type="text" placeholder="Your name" class="name form-control" required />' +
                        '</div>' +
                        '</form>',
                    buttons: {
                        formSubmit: {
                            text: 'Submit',
                            btnClass: 'btn-blue',
                            action: function () {
                                var name = this.$content.find('.name').val();
                                if(!name){
                                    $.alert('provide a valid name');
                                    return false;
                                }
                                $.alert('Your name is ' + name);
                            }
                        },
                        cancel: function () {
                            //close
                        },
                    },
                    onContentReady: function () {
                        // bind to events
                        var jc = this;
                        this.$content.find('form').on('submit', function (e) {
                            // if the user submits the form by pressing enter in the field.
                            e.preventDefault();
                            jc.$$formSubmit.trigger('click'); // reference the button and click it
                        });
                    }
                });
            });
        },

        select: function () {
            $('#wc_delisend_shipping_filter').select2({

            });
        }
    };

    // Document ready
    $(document).ready(function () {
        scripts.initialize();
    });

}(jQuery));
