<?php

/**
 * List all store orders
 * 
 * @author WC Bessinger <dev@silverbackdev.co.za>
 */
trait SBWC_OM_All_Orders
{

    /**
     * Query all stores
     * 
     * @return boolean - returns false if no orders found
     */
    public static function query_all_stores()
    {

        // query stores
        $stores = get_posts([
            'post_type'      => 'store',
            'posts_per_page' => -1
        ]);

        // store ids
        $store_ids = [];

        // if stores, retrieve ids, push to $store_ids and return, else return false
        if ($stores) :
            foreach ($stores as $store) :
                $store_ids[] = $store->ID;
            endforeach;

            return $store_ids;
        else :
            return false;
        endif;
    }

    /**
     * Render store filter dropdown
     */
    public static function display_store_filter($store_ids)
    {

        if (is_iterable($store_ids) && count($store_ids) > 1) : ?>

            <span id="sbwc-om-store-select-cont" style="display: block; padding-bottom: 15px;">

                <form action="" id="sbwc-om-filter-stores-form" method="post">
                    <label for="sbwc-om-filter-stores"><u><b><?php _e('Filter Stores', 'sbwc-om'); ?></b></u></label>
                    <select name="sbwc-om-filter-stores" id="sbwc-om-filter-stores" style="font-size: small; margin-left: 5px;" data-subbed="<? isset($_POST['sbwc-om-filter-stores'])?print $_POST['sbwc-om-filter-stores']:''; ?>">
                        <option value=""><?php _e('select store...', 'sbwc-om'); ?></option>
                        <?php foreach ($store_ids as $sid) : ?>
                            <option value="<?php echo $sid; ?>"><?php echo get_the_title($sid); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button id="sbwc-om-all-orders-filter" class="button button-primary" type="submit"><?php _e('Filter', 'sbwc-om'); ?></button>
                </form>

            </span>

            <script>
                jQuery(function($) {
                    var selected = $('#sbwc-om-filter-stores').data('subbed');
                    $('#sbwc-om-filter-stores').val(selected).change();
                });
            </script>

        <?php endif;
    }

    /**
     * Display queried orders if present
     */
    public static function orders_list()
    {

        // retrieve store ids
        $store_ids = self::query_all_stores();

        // if false returned for store ideas, show message
        if ($store_ids === false) : ?>

            <p><b><i><?php _e('No stores present. Please set up at least one store and import orders for it.', 'sbwc-om'); ?></i></b></p>

        <?php elseif (is_iterable($store_ids) && !isset($_POST['sbwc-om-filter-stores'])) :
            // display store filter if more than 1 store present
            self::display_store_filter($store_ids);
        ?>

            <table id="sbwc-om-all-orders-table" class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <th><b><?php _e('Store Name', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Number', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Status', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('ID', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Date', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Customer', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Total', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Shipping Address', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('View', 'sbwc-om'); ?></b></th>
                </thead>
                <tbody>
                    <?php
                    foreach ($store_ids as $store_id) :

                        // retrieve orders
                        $orders = maybe_unserialize(get_post_meta($store_id, 'store_orders', true));

                        // retrieve shipping companies
                        $ship_cos = get_post_meta($store_id, 'ship_cos', true) ? htmlspecialchars(json_encode(maybe_unserialize(get_post_meta($store_id, 'ship_cos', true))), ENT_QUOTES, 'UTF-8') : false;

                        if (is_iterable($orders)) :
                            $ajx_store_id = $store_id;
                            foreach ($orders as $order_data) :
                    ?>

                                <tr>

                                    <!-- store name -->
                                    <td>
                                        <b>
                                            <?php echo get_the_title($ajx_store_id); ?>
                                        </b>
                                    </td>

                                    <!-- order number -->
                                    <td>
                                        <?php
                                        echo $order_data['number'];
                                        ?>
                                    </td>

                                    <!-- order status -->
                                    <td>
                                        <?php
                                        $updated_orders = maybe_unserialize(get_post_meta($ajx_store_id, 'updated_orders', true));

                                        if (is_array($updated_orders) && in_array($order_data['id'], $updated_orders)) :
                                            _e('<span title="order has been finalized" style="cursor: help;"><b><u>Finalized</u></b></span>', 'sbwc-om');
                                        else :
                                            _e('<span title="to be finalized" style="cursor: help;"><b><u>TBF</u></b></span>', 'sbwc-om');
                                        endif;
                                        ?>
                                    </td>

                                    <!-- order ID -->
                                    <td>
                                        <?php
                                        echo $order_data['id'];
                                        ?>
                                    </td>

                                    <!-- order date -->
                                    <td>
                                        <?php
                                        echo date('j F Y', strtotime($order_data['date_modified']));
                                        ?>
                                    </td>

                                    <!-- order customer -->
                                    <td>
                                        <?php
                                        echo $order_data['shipping']['first_name'] . ' ' . $order_data['shipping']['last_name'];
                                        ?>
                                    </td>

                                    <!-- order total -->
                                    <td>
                                        <?php
                                        echo $order_data['currency'] . ' ' . $order_data['total'];
                                        ?>
                                    </td>

                                    <!-- order shipping address -->
                                    <td>
                                        <?php
                                        $order_data['shipping']['address_1'] ? print $order_data['shipping']['address_1'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                        $order_data['shipping']['address_2'] ? print $order_data['shipping']['address_2'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                        $order_data['shipping']['city'] ? print $order_data['shipping']['city'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                        $order_data['shipping']['state'] ? print $order_data['shipping']['state'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                        $order_data['shipping']['postcode'] ? print $order_data['shipping']['postcode'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                        $order_data['shipping']['country'] ? print $order_data['shipping']['country'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                        ?>
                                    </td>

                                    <!-- view order -->
                                    <td data-ship-cos="<?php echo $ship_cos ?>">
                                        <button id="sbwc-view-order-data" class="button button-primary sbwc-view-order-data" data-order-date="<?php echo date('j F Y', strtotime($order_data['date_modified'])); ?>" data-order-data="<?php echo htmlspecialchars(json_encode($order_data), ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php _e('View', 'sbwc-om'); ?>
                                        </button>
                                    </td>
                                </tr>
                    <?php
                            endforeach;
                        endif;
                    endforeach;
                    ?>
                </tbody>
            </table>
        <?php
            // order details modal
            self::order_data_modal($ajx_store_id);

        // ----------------------------------
        // if store filter request submitted
        // ----------------------------------
        elseif (isset($_POST['sbwc-om-filter-stores'])) :

            // query all store ids
            $all_store_ids = self::query_all_stores();

            // retrieve store id
            $store_id = $_POST['sbwc-om-filter-stores'];

            // retrieve orders
            $orders = maybe_unserialize(get_post_meta($store_id, 'store_orders', true));

            // retrieve shipping companies
            $ship_cos = get_post_meta($store_id, 'ship_cos', true) ? htmlspecialchars(json_encode(maybe_unserialize(get_post_meta($store_id, 'ship_cos', true))), ENT_QUOTES, 'UTF-8') : false;

            // display store filter (only displays of store count > 1)
            self::display_store_filter($all_store_ids);

            // if no orders present for stores, display error message
            if (!$orders) :
                _e('<p><i><b><u>No orders found for this store. Please select another store from the list, or import orders via Stores & Orders page.</u></b></i></p>', 'sbwc-om');
                return;
            endif;

        ?>

            <table id="sbwc-om-all-orders-table" class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <th><b><?php _e('Store Name', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Number', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Status', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('ID', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Date', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Customer', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Total', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('Shipping Address', 'sbwc-om'); ?></b></th>
                    <th><b><?php _e('View', 'sbwc-om'); ?></b></th>
                </thead>
                <tbody>

                    <?php
                    if (is_iterable($orders)) :
                        foreach ($orders as $order_data) :
                    ?>

                            <tr>

                                <!-- store name -->
                                <td>
                                    <b>
                                        <?php echo get_the_title($store_id); ?>
                                    </b>
                                </td>

                                <!-- order number -->
                                <td>
                                    <?php
                                    echo $order_data['number'];
                                    ?>
                                </td>

                                <!-- order status -->
                                <td>
                                    <?php
                                    $updated_orders = maybe_unserialize(get_post_meta($store_id, 'updated_orders', true));

                                    if (is_array($updated_orders) && in_array($order_data['id'], $updated_orders)) :
                                        _e('<span title="order has been finalized" style="cursor: help;"><b><u>Finalized</u></b></span>', 'sbwc-om');
                                    else :
                                        _e('<span title="to be finalized" style="cursor: help;"><b><u>TBF</u></b></span>', 'sbwc-om');
                                    endif;
                                    ?>
                                </td>

                                <!-- order ID -->
                                <td>
                                    <?php
                                    echo $order_data['id'];
                                    ?>
                                </td>

                                <!-- order date -->
                                <td>
                                    <?php
                                    echo date('j F Y', strtotime($order_data['date_modified']));
                                    ?>
                                </td>

                                <!-- order customer -->
                                <td>
                                    <?php
                                    echo $order_data['shipping']['first_name'] . ' ' . $order_data['shipping']['last_name'];
                                    ?>
                                </td>

                                <!-- order total -->
                                <td>
                                    <?php
                                    echo $order_data['currency'] . ' ' . $order_data['total'];
                                    ?>
                                </td>

                                <!-- order shipping address -->
                                <td>
                                    <?php
                                    $order_data['shipping']['address_1'] ? print $order_data['shipping']['address_1'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                    $order_data['shipping']['address_2'] ? print $order_data['shipping']['address_2'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                    $order_data['shipping']['city'] ? print $order_data['shipping']['city'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                    $order_data['shipping']['state'] ? print $order_data['shipping']['state'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                    $order_data['shipping']['postcode'] ? print $order_data['shipping']['postcode'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                    $order_data['shipping']['country'] ? print $order_data['shipping']['country'] . '<br>' : _e('N/A<br>', 'sbwc-om');
                                    ?>
                                </td>

                                <!-- view order -->
                                <td data-ship-cos="<?php echo $ship_cos ?>">
                                    <button id="sbwc-view-order-data" class="button button-primary sbwc-view-order-data" data-order-date="<?php echo date('j F Y', strtotime($order_data['date_modified'])); ?>" data-order-data="<?php echo htmlspecialchars(json_encode($order_data), ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php _e('View', 'sbwc-om'); ?>
                                    </button>
                                </td>
                            </tr>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </tbody>
            </table>
        <?php

            // order details modal
            self::order_data_modal($store_id);

        endif;
    }

    /**
     * Display order data modal
     */
    public static function order_data_modal($store_id)
    {
        ?>

        <div id="sbwc-all-order-modal-overlay" style="display: none;"></div>

        <div id="sbwc-all-order-modal-proper" style="display: none;">

            <!-- modal header -->
            <h3><a href="#" title="<?php _e('Dismiss', 'sbwc-om'); ?>">x</a></h3>

            <!-- order data body -->
            <div id="sbwc-om-all-order-data-body">

                <!-- order details -->
                <div id="sbwc-om-all-order-details">
                    <h4><?php _e('ORDER DETAILS', 'sbwc-om'); ?></h4>
                    <hr>
                    <br>

                    <!-- order details table -->
                    <table id="sbwc-om-all-order-details-table">

                        <!-- date -->
                        <tr>
                            <th><?php _e('Placed On', 'sbwc-om'); ?></th>
                            <td id="sbwc-om-ord-date"></td>
                        </tr>

                        <!-- id -->
                        <tr>
                            <th><?php _e('Order ID', 'sbwc-om'); ?></th>
                            <td id="sbwc-om-ord-id"></td>
                        </tr>

                        <!-- number -->
                        <tr>
                            <th><?php _e('Order No', 'sbwc-om'); ?></th>
                            <td id="sbwc-om-ord-no"></td>
                        </tr>

                        <!-- order key -->
                        <tr>
                            <th><?php _e('Order Key', 'sbwc-om'); ?></th>
                            <td id="sbwc-om-ord-key"></td>
                        </tr>

                        <!-- order total-->
                        <tr>
                            <th><?php _e('Order Total', 'sbwc-om'); ?></th>
                            <td id="sbwc-om-ord-total"></td>
                        </tr>

                        <!-- customer id -->
                        <tr>
                            <th><?php _e('Customer ID', 'sbwc-om'); ?></th>
                            <td id="sbwc-om-ord-cust-id"></td>
                        </tr>

                        <!-- customer name -->
                        <tr>
                            <th><?php _e('Customer Name', 'sbwc-om'); ?></th>
                            <td id="sbwc-om-ord-cust"></td>
                        </tr>

                        <!-- customer shipping -->
                        <tr>
                            <th><?php _e('Shipping Address', 'sbwc-om'); ?></th>
                            <td id="sbwc-om-ord-ship"></td>
                        </tr>

                    </table>

                    <br>

                    <!-- update shipping if ship cos present -->
                    <div id="sbwc-om-all-orders-ship-update-cont" style="display: none;">

                        <h4>
                            <?php _e("UPDATE ORDER SHIPMENT INFO BELOW", 'sbwc-om'); ?>
                        </h4>
                        <hr>

                        <!-- tracking input label -->
                        <p>
                            <label for="sbwc-om-ord-ship-tracking">
                                <b><i><?php _e('Specify order tracking number:', 'sbwc-om'); ?></i></b>
                            </label>
                        </p>

                        <!-- shipping/tracking input -->
                        <p>
                            <input type="text" name="sbwc-om-ord-ship-tracking" style="min-width: 350px;" id="sbwc-om-ord-ship-tracking" placeholder="<?php _e('order tracking number', 'sbwc-om'); ?>">
                        </p>

                        <!-- shipping co select label -->
                        <p>
                            <label for="sbwc-om-ord-ship-co">
                                <b><i><?php _e('Select shipping company:', 'sbwc-om'); ?></i></b>
                            </label>
                        </p>

                        <!-- shipping company select -->
                        <p>
                            <select name="sbwc-om-ord-ship-co" id="sbwc-om-ord-ship-co">
                                <option value=""><?php _e('select shipping company', 'sbwc-om'); ?></option>
                            </select>
                        </p>

                        <!-- error -->
                        <p id="sbwc-om-update-error" style="display: none;">
                            <?php _e('Order tracking number and/or shipping company required before attempting to update.', 'sbwc-om'); ?>
                        </p>

                        <!-- update order -->
                        <div id="sbwc-om-update-order-cont">
                            <!-- update order data -->
                            <button id="sbwc-om-update-order-all" class="button button-primary button-large button-block" data-store-id="<?php echo $store_id; ?>" data-nonce="<?php echo wp_create_nonce('sbwc update single order all'); ?>">
                                <?php _e('Update Order', 'sbwc-om'); ?>
                            </button>
                        </div>

                    </div>

                </div>

                <!-- order products -->
                <div id="sbwc-om-all-order-products">

                    <h4><?php _e('PRODUCTS', 'sbwc-om'); ?> &nbsp; &nbsp;<span style="color: #888"><?php _e('<u>[ NOTE:</u> Line Subtotals are based on pre-discount item cost, so may not match order total. ]', 'sbwc-om'); ?></span></h4>
                    <hr>
                    <br>

                    <table id="sbwc-om-all-order-product-data">

                        <!-- header -->
                        <thead>
                            <!-- product name -->
                            <th>
                                <?php _e('Name', 'sbwc-om'); ?>
                            </th>

                            <!-- product ID -->
                            <th>
                                <?php _e('Product ID', 'sbwc-om'); ?>
                            </th>

                            <!-- variation ID -->
                            <th>
                                <?php _e('Variation ID', 'sbwc-om'); ?>
                            </th>

                            <!-- QTY -->
                            <th>
                                <?php _e('QTY', 'sbwc-om'); ?>
                            </th>

                            <!-- Subtotal -->
                            <th>
                                <?php _e('Subtotal', 'sbwc-om'); ?>
                            </th>
                        </thead>

                        <!-- body -->
                        <tbody id="sbwc-om-all-order-product-dataset">

                        </tbody>
                    </table>

                </div><!-- order products table container -->

            </div>

        <?php
    }

    /**
     * JS to display order data modal and append associated data
     */
    public static function order_data_modal_js()
    {
        ?>

            <script>
                jQuery(document).ready(function($) {

                    // ------------------------------------
                    // show modal and append required data
                    // ------------------------------------
                    $('.sbwc-view-order-data').on('click', function(e) {

                        e.preventDefault();

                        // empty order data table
                        $('#sbwc-om-all-order-product-dataset').empty();

                        // empty tracking no input
                        $('#sbwc-om-ord-ship-tracking').val('');

                        // show modal and overlay
                        $('#sbwc-all-order-modal-overlay, #sbwc-all-order-modal-proper').show();

                        // retrieve order data
                        var order_data = $(this).data('order-data');

                        // retrieve ship cos
                        var ship_cos = $(this).parent().data('ship-cos');

                        // scroll window to top
                        window.scrollTo(0, 0);

                        // order date
                        var date = $(this).data('order-date');

                        // order number
                        var order_no = order_data.number;

                        // modal title
                        $('#sbwc-all-order-modal-proper > h3').empty().prepend('<?php _e('Details for Order No ', 'sbwc-om') ?>' + order_no).append('<a href="#" title="<?php _e('Dismiss', 'sbwc-om'); ?>">x</a>');

                        // retrieve order data from json
                        var currency = order_data.currency,
                            customer_id = order_data.customer_id,
                            order_id = order_data.id,
                            order_total = order_data.total,
                            order_key = order_data.order_key,
                            shipping_data = order_data.shipping,
                            line_items = order_data.line_items;

                        // append initial order data
                        $('#sbwc-om-ord-date').text('').text(date);
                        $('#sbwc-om-ord-id').text('').text(order_id), $('#sbwc-om-update-order-all').attr('data-order-id', order_id);
                        $('#sbwc-om-ord-no').text('').text(order_no), $('#sbwc-om-update-order-all').attr('data-order-no', order_no);
                        $('#sbwc-om-ord-key').text('').text(order_key);
                        $('#sbwc-om-ord-total').text('').text(currency + ' ' + order_total);
                        $('#sbwc-om-ord-cust-id').text('').text(customer_id);
                        $('#sbwc-om-ord-cust').text('').text(shipping_data.first_name + ' ' + shipping_data.last_name);
                        $('#sbwc-om-ord-ship').text('').html(shipping_data.address_1 + '<br>' + shipping_data.address_2 + '<br>' + shipping_data.city + '<br>' + shipping_data.country + '<br>' + shipping_data.postcode);

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
                            $('#sbwc-om-all-order-product-dataset').append(to_insert);
                        });

                        // show shipping update inputs and insert ship co dropdown options if ship cos present
                        if (ship_cos.length !== 0) {

                            $('#sbwc-om-ord-ship-co').empty()

                            $('#sbwc-om-all-orders-ship-update-cont').show();

                            for (const prop in ship_cos) {
                                var ship_co_id = prop;
                                var ship_co_name = ship_cos[prop].name;
                                $('#sbwc-om-ord-ship-co').append('<option value="' + ship_co_id + '">' + ship_co_name + '</option>')
                            }
                        }
                    });

                    // ------------
                    // close modal
                    // ------------
                    $(document).on('click', '#sbwc-all-order-modal-proper > h3 > a, #sbwc-all-order-modal-overlay', function(e) {
                        e.preventDefault();
                        $('#sbwc-all-order-modal-overlay, #sbwc-all-order-modal-proper').hide();
                    });

                    // -----------------------------
                    // update single order shipping
                    // -----------------------------
                    $('#sbwc-om-update-order-all').on('click', function(e) {
                        e.preventDefault();

                        var store_id = $(this).data('store-id'),
                            nonce = $(this).data('nonce'),
                            tracking_no = $('#sbwc-om-ord-ship-tracking').val(),
                            ship_co_id = $('#sbwc-om-ord-ship-co').val(),
                            order_id = $(this).data('order-id'),
                            order_no = $(this).data('order-no');

                        if (!tracking_no || !ship_co_id) {
                            $('#sbwc-om-update-error').show();
                            return;
                        }

                        $('#sbwc-om-update-error').hide();

                        var data = {
                            '_ajax_nonce': nonce,
                            'action': 'sbwc_om_update_single_order_all',
                            'store_id': store_id,
                            'track_no': tracking_no,
                            'ship_co_id': ship_co_id,
                            'order_id': order_id,
                            'order_no': order_no
                        }

                        $.post(ajaxurl, data, function(response) {
                            alert(response);
                            location.reload();
                        });

                    });
                });
            </script>


    <?php
    }
}
