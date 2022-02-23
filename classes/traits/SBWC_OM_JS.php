<?php

/**
 * Admin JS
 */
trait SBWC_OM_JS
{

    public static function sbwc_om_js()
    { ?>

        <script>
            jQuery(document).ready(function($) {

                // *****
                // TABS
                // *****
                $("#sbwc-om-cpt-tabs").tabs();

                // ********************
                // ADD/REMOVE STORES
                // ********************

                // html to insert
                var html = '<div class="sbwc-om-store-inputs">';
                html += '<input type="text" class="sbwc-om-store-name" name="sbwc-om-store-name[]" placeholder="<?php _e('store name', 'sbwc-om'); ?>"> ';
                html += '<input type="url" class="sbwc-om-store-url" name="sbwc-om-store-url[]" placeholder="<?php _e('store URL', 'sbwc-om'); ?>"> ';
                html += '<input type="text" class="sbwc-om-store-key" name="sbwc-om-store-key[]" placeholder="<?php _e('woocommerce API key', 'sbwc-om'); ?>"> ';
                html += '<input type="text" class="sbwc-om-store-secret" name="sbwc-om-store-secret[]" placeholder="<?php _e('woocommerce API secret', 'sbwc-om'); ?>"> ';
                html += '<button class="button button-primary button-small sbwc-om-add-store" title="<?php _e('add store', 'sbwc-om'); ?>">+</button> ';
                html += '<button class="button button-secondary button-small sbwc-om-rem-store" title="<?php _e('remove store', 'sbwc-om'); ?>">-</button>';
                html += '</div>';

                // add store input set
                $(document).on('click', '.sbwc-om-add-store', function(e) {
                    e.preventDefault();
                    $('#sbwc-om-store-details').append(html);
                });

                // remove store input set
                $(document).on('click', '.sbwc-om-rem-store', function(e) {
                    e.preventDefault();
                    $(this).parent().remove();
                });

                // ******************************************
                // RETRIEVE STORE PRODUCTS AND SAVE TO STORE
                // ******************************************
                $('#sbwc-om-retrieve-orders').on('click', function(e) {

                    e.preventDefault();

                    var data = {
                        '_ajax_nonce': $(this).data('nonce'),
                        'store_id': $(this).data('store-id'),
                        'action': 'sbwc_om_ajax_retrieve_orders',
                    }

                    $.post(ajaxurl, data, function(response) {
                        alert(response);
                        location.reload();
                    });

                });

                // *********************************************
                // DISPLAY ORDER DATA IN MODAL AND MODAL ITSELF
                // *********************************************
                $('.sbwc-om-view-order').on('click', function(e) {

                    e.preventDefault();

                    // zero out all values/text
                    $('#sbwc-om-ord-ship-tracking, #sbwc-om-ord-ship-co').val('');
                    $('#sbwc-om-order-lb > h3').text('').append('<a href="#" title="<?php _e('Dismiss', 'sbwc-om'); ?>">x</a>');
                    $('#sbwc-om-order-product-dataset').empty();

                    // scroll to top
                    window.scrollTo(0, 0);

                    // retrieve date
                    var date = $(this).data('order-date');

                    // retrieve order number
                    var order_no = $(this).data('order-no');

                    // decode order json
                    var order_data_b64 = $(this).data('order');
                    var order_json = JSON.parse(atob(order_data_b64));

                    // modal title
                    $('#sbwc-om-order-lb > h3').prepend('<?php _e('Details for Order No ', 'swbc-om') ?>' + order_no);

                    // retrieve order data from json
                    var currency = order_json.currency,
                        customer_id = order_json.customer_id,
                        order_id = order_json.id,
                        order_prods = order_json.line_items,
                        order_total = order_json.total,
                        order_key = order_json.order_key,
                        shipping_data = order_json.shipping,
                        line_items = order_json.line_items;

                    // append initial order data
                    $('#sbwc-om-ord-date').text(date);
                    $('#sbwc-om-ord-id').text(order_id);
                    $('#sbwc-om-ord-no').text(order_no);
                    $('#sbwc-om-ord-key').text(order_key);
                    $('#sbwc-om-ord-total').text(currency + ' ' + order_total);
                    $('#sbwc-om-ord-cust-id').text(customer_id);
                    $('#sbwc-om-ord-cust').text(shipping_data.first_name + ' ' + shipping_data.last_name);
                    $('#sbwc-om-ord-ship').html(shipping_data.address_1 + '<br>' + shipping_data.address_2 + '<br>' + shipping_data.city + '<br>' + shipping_data.country + '<br>' + shipping_data.postcode);

                    // append order product data
                    line_items.forEach(element => {

                        if (element.variation_id) {
                            var var_id = element.variation_id;
                        } else {
                            var var_id = 'N/A';
                        }

                        // console.log(element);
                        var to_insert = '<tr>';
                        to_insert += '<td>' + element.name + '</td>';
                        to_insert += '<td>' + element.product_id + '</td>';
                        to_insert += '<td>' + var_id + '</td>';
                        to_insert += '<td>' + element.quantity + '</td>';
                        to_insert += '<td>' + currency + ' ' + element.subtotal + '</td>';
                        to_insert += '</tr>';

                        $('#sbwc-om-order-product-dataset').append(to_insert);
                    });

                    // show modal and overlay
                    $('#sbwc-om-order-lb, #sbwc-om-order-lb-overlay').show();

                });

                // **********************
                // HIDE ORDER DATA MODAL
                // **********************
                $(document).on('click', '#sbwc-om-order-lb-overlay, #sbwc-om-order-lb > h3 > a',function(e) {
                    e.preventDefault();
                    $('#sbwc-om-order-lb, #sbwc-om-order-lb-overlay').hide();
                });

                // *************
                // UPDATE ORDER
                // *************
                $('#sbwc-om-update-order').on('click', function(e) {
                    e.preventDefault();

                    // show error if required data not present
                    if (!$('#sbwc-om-ord-ship-tracking').val() || !$('#sbwc-om-ord-ship-co').val()) {
                        $('#sbwc-om-update-error').show();
                        return;
                    }
                    
                    // hide error if all field data present
                    $('#sbwc-om-update-error').hide();

                    // send ajax request to update order
                    var data = {
                        '_ajax_nonce': $(this).data('nonce'),
                        'action': 'sbwc_om_update_single_order',
                        'order_number': $('#sbwc-om-ord-no').text(),
                        'order_id': $('#sbwc-om-ord-id').text(),
                        'store_id': $(this).data('store-id'),
                        'track_no': $('#sbwc-om-ord-ship-tracking').val(),
                        'ship_co_id': $('#sbwc-om-ord-ship-co').val()
                    }

                    $.post(ajaxurl, data, function(response) {
                        alert(response);
                        location.reload(); 
                    });

                });

                // *********************************
                // SCHEDULE SHIPMENT CSV PROCESSING
                // *********************************
                $('#sbwc-om-schedule-csv-process').on('click', function(e) {
                    e.preventDefault();

                    var nonce = $(this).data('nonce');
                    var store_id = $(this).data('store-id');
                    var csv_name = $(this).data('csv-name');

                    var data = {
                        '_ajax_nonce': nonce,
                        'action': 'sbwc_om_process_ship_csv',
                        'store_id': store_id,
                        'csv_name': csv_name
                    }

                    $.post(ajaxurl, data, function(response) {
                        alert(response);
                        location.reload();
                    });

                });

                // ****************************************
                // RETRIEVE STORE SHIPMENT COMPANY DETAILS
                // ****************************************
                $('#sbwc-om-retrieve-ship-cos').on('click', function(e) {
                    e.preventDefault();

                    var data = {
                        '_ajax_nonce': $(this).data('nonce'),
                        'store_id': $(this).data('store'),
                        'action': 'sbwc_om_retrieve_shipping_cos',
                    }

                    $.post(ajaxurl, data, function(response) {
                        alert(response);
                        location.reload();
                    });

                });

            });
        </script>

<?php }
}

?>