/* global wc_delisend_admin_js */

(function ($) {
    'use strict';

    let scripts = {
        initialize: function () {
            this.events();
        },

        // Custom js setting *
        events: function () {
            jQuery('body').on('click', '.check_customer_in_delisend', function (e) {
                e.preventDefault();
                let mwb_enhanced_tyo_remove = jQuery(this).data("id");

                jQuery("#mwb_enhanced_tyo_class" + mwb_enhanced_tyo_remove).remove();

                jQuery.ajax({
                    url: ajax_url,
                    type: "POST",
                    data: {
                        action: 'mwb_provider_remove_company_data_from_plugin',
                        mwb_company_name: mwb_enhanced_tyo_remove
                    }, success: function (response) {
                        console.log(response);
                    }
                });
            });

            let options = {
                series: [44, 55, 67, 83],
                chart: {
                    height: 350,
                    type: 'radialBar',
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: {
                                fontSize: '22px',
                            },
                            value: {
                                fontSize: '16px',
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function (w) {
                                    // By default this function returns the average of all series. The below is just an example to show the use of custom formatter function
                                    return 249
                                }
                            }
                        }
                    }
                },
                labels: ['Apples', 'Oranges', 'Bananas', 'Berries'],
            };

            let chart = new ApexCharts(document.querySelector("#chart-container"), options);
            chart.render();

        },

    };

    // Document ready
    $(document).ready(function () {
        scripts.initialize();
    });

}(jQuery));
