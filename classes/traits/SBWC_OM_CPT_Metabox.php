<?php

/**
 * Renders store CPT metabox and associated data
 */
trait SBWC_OM_CPT_Metabox
{

    /**
     * Register metabox
     */
    public static function sbwc_om_register_store_metabox()
    {
        add_meta_box('sbwc-om-post-meta', __('Store Order Data', 'sbwc-om'), [__TRAIT__, 'sbwc_render_store_metabox'], 'store', 'advanced', 'high');
    }

    /**
     * Render metabox/data
     *
     * @return void
     */
    public static function sbwc_render_store_metabox()
    { ?>

        <!-- cpt tabs cont -->
        <div id="sbwc-om-cpt-tabs">

            <ul>
                <li><a href="#sbwc-om-orders-list"><b><?php _e('Shop Orders', 'sbwc-om'); ?></b></a></li>
                <li><a href="#sbwc-om-upload-csv"><b><?php _e('Upload Shipping CSV', 'sbwc-om'); ?></b></a></li>
            </ul>

            <!-- *********** -->
            <!-- orders list -->
            <!-- *********** -->
            <div id="sbwc-om-orders-list">
                <?php
                global $post;

                // retrieve shop orders
                $orders = maybe_unserialize(get_post_meta($post->ID, 'store_orders', true));

                // delete_post_meta($post->ID, 'store_orders');

                // retrieve store connection data
                $store_url       = get_post_meta($post->ID, 'store_url', true);
                $store_cs_key    = get_post_meta($post->ID, 'store_cs_key', true);
                $store_cs_secret = get_post_meta($post->ID, 'store_cs_secret', true);

                // if no orders
                if (!$orders) : ?>

                    <p id="sbwc-om-orders-not-retrieved"><i><b><?php _e('Orders have not been retrieved or received for this store yet.', 'sbwc-om'); ?></b></i></p>

                    <p>
                        <!-- retrieve orders via REST using Action Scheduler (done via admin AJAX) -->
                        <button id="sbwc-om-retrieve-orders" class="button button-large button-primary" data-store-id="<?php echo $post->ID; ?>" data-nonce="<?php echo wp_create_nonce('sbwc om retrieve store orders'); ?>" title="<?php _e('click to retrieve latest orders', 'sbwc-om'); ?>">
                            <?php _e('Retrieve Orders', 'sbwc-om'); ?>
                        </button>

                        <a href="#" id="sbwc-om-show-import-info" title="<?php _e('This will only retrieve the latest 100 orders. If you would like to send all orders with a status of processing to the Order Manager, you can use the order manager settings plugin in your store\'s backend to do so.', 'sbwc-om'); ?>">i</a>
                    </p>

                    <!-- current connection settings -->
                    <p>
                        <u><b><i><?php _e('Current connection settings:', 'sbwc-om'); ?></i></b></u>
                    </p>

                    <table id="sbwc-om-store-settings-curr">
                        <tr>
                            <th><?php _e('Store URL:', 'sbwc-om'); ?></th>
                            <td><?php echo $store_url; ?></td>
                        </tr>
                        <tr>
                            <th><?php _e('Consumer key: ', 'sbwc-om'); ?></th>
                            <td><?php echo $store_cs_key; ?></td>
                        </tr>
                        <tr>
                            <th><?php _e('Consumer secret: ', 'sbwc-om'); ?></th>
                            <td><?php echo $store_cs_secret; ?></td>
                        </tr>
                    </table>

                <?php

                // if orders, display table
                else : ?>

                    <div id="sbwc-om-refresh-orders-cont">

                        <span><b><i><?php _e('Retrieve latest 100 orders by clicking the Refresh button: ', 'sbwc-om'); ?></i></b></span>

                        <!-- retrieve orders via REST using Action Scheduler (done via admin AJAX) -->
                        <button id="sbwc-om-retrieve-orders" class="button button-large button-primary sbwc-om-refresh-orders" data-store-id="<?php echo $post->ID; ?>" data-nonce="<?php echo wp_create_nonce('sbwc om retrieve store orders'); ?>">
                            <?php _e('Refresh', 'sbwc-om'); ?>
                        </button>

                        <?php if (get_post_meta($post->ID, 'retrieval_timestamp', true)) :

                            // last request timestamp
                            $request_ts = get_post_meta($post->ID, 'retrieval_timestamp', true);
                            $ts_msg = date('j F Y @ h:i:s', $request_ts) . __(' (server time)');

                        ?>
                            <span id="sbwc-om-retrieval-timestamp">
                                <?php _e("<b><i><u>Last retrieval request:</u> $ts_msg</b></i>", 'sbwc-om'); ?>
                            </span>
                        <?php endif; ?>

                    </div>

                    <table id="sbwc-om-orders" class="wp-list-table widefat fixed striped table-view-list">
                        <thead>
                            <tr>
                                <th><b><?php _e('Number', 'sbwc-om'); ?></b></th>
                                <th><b><?php _e('Status', 'sbwc-om'); ?></b></th>
                                <th><b><?php _e('ID', 'sbwc-om'); ?></b></th>
                                <th><b><?php _e('Date', 'sbwc-om'); ?></b></th>
                                <th><b><?php _e('Customer', 'sbwc-om'); ?></b></th>
                                <th><b><?php _e('Total', 'sbwc-om'); ?></b></th>
                                <th><b><?php _e('Shipping Address', 'sbwc-om'); ?></b></th>
                                <th><b><?php _e('View', 'sbwc-om'); ?></b></th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php foreach ($orders as $o_index => $order_data) : ?>
                                <tr>

                                    <!-- order number -->
                                    <td>
                                        <b>
                                            <?php
                                            echo $order_data['number'];
                                            ?>
                                        </b>
                                    </td>

                                    <!-- status -->
                                    <td>
                                        <?php
                                        in_array($order_data['id'], get_post_meta($post->ID, 'updated_orders', true)) ? _e('<span title="order has been finalized" style="cursor: help;"><b>F</b></span>', 'sbwc-om') : _e('<span title="to be finalized" style="cursor: help;"><b><u>TBF</u></b></span>', 'sbwc-om');
                                        ?>
                                    </td>

                                    <!-- order id -->
                                    <td>
                                        <?php echo $order_data['id']; ?>
                                    </td>

                                    <!-- date -->
                                    <td>
                                        <?php echo date('j F, Y', strtotime($order_data['date_modified'])); ?>
                                    </td>

                                    <!-- customer -->
                                    <td>
                                        <?php
                                        print $order_data['shipping']['first_name'] . ' ' . $order_data['shipping']['last_name'] . '<br>';
                                        ?>
                                    </td>

                                    <!-- order total -->
                                    <td>
                                        <?php echo $order_data['currency'] . ' ' . $order_data['total']; ?>
                                    </td>

                                    <!-- shipping address -->
                                    <td>
                                        <?php
                                        print $order_data['shipping']['address_1'] ? $order_data['shipping']['address_1'] . '<br>' : 'N/A' . '<br>';
                                        print $order_data['shipping']['address_2'] ? $order_data['shipping']['address_2'] . '<br>' : 'N/A' . '<br>';
                                        print $order_data['shipping']['city'] ? $order_data['shipping']['city'] . '<br>' : 'N/A' . '<br>';
                                        print $order_data['shipping']['postcode'] ? $order_data['shipping']['postcode'] . '<br>' : 'N/A' . '<br>';
                                        print $order_data['shipping']['country'] ? $order_data['shipping']['country'] : 'N/A';
                                        ?>
                                    </td>

                                    <!-- view/edit -->
                                    <td>
                                        <button class="button button-primary sbwc-om-view-order" data-order-date="<?php echo date('j F, Y', strtotime($order_data['date_modified'])); ?>" data-order-no="<?php echo $order_data['number']; ?>" data-order="<?php echo base64_encode(json_encode($order_data)) ?>">
                                            <?php _e('View/Edit', 'woocommerce'); ?>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table><!-- orders table -->

                    <!-- order data lightbox overlay -->
                    <div id="sbwc-om-order-lb-overlay" style="display: none;"></div>

                    <!-- order data lightbox -->
                    <div id="sbwc-om-order-lb" style="display: none;">

                        <!-- modal header -->
                        <h3><a href="#" title="<?php _e('Dismiss', 'sbwc-om'); ?>">x</a></h3>

                        <!-- order data body -->
                        <div id="sbwc-om-order-data-body">

                            <!-- order details -->
                            <div id="sbwc-om-order-details">
                                <h4><?php _e('ORDER DETAILS', 'sbwc-om'); ?></h4>
                                <hr>
                                <br>

                                <!-- order details table -->
                                <table id="sbwc-om-order-details-table">

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

                                <!-- input label -->
                                <p>
                                    <label for="sbwc-om-ord-ship-tracking">
                                        <b><i><?php _e('If this order has been shipped, enter the shipping tracking number below and hit the update button.', 'sbwc-om'); ?></i></b>
                                    </label>
                                </p>

                                <!-- shipping/tracking input -->
                                <p>
                                    <input type="text" name="sbwc-om-ord-ship-tracking" style="min-width: 350px;" id="sbwc-om-ord-ship-tracking" placeholder="<?php _e('order tracking number', 'sbwc-om'); ?>">
                                </p>

                                <!-- error -->
                                <p id="sbwc-om-update-error" style="display: none;">
                                    <?php _e('Order tracking number required before attempting to update.', 'sbwc-om'); ?>
                                </p>

                            </div><!-- order details -->

                            <!-- order products -->
                            <div id="sbwc-om-order-products">

                                <h4><?php _e('PRODUCTS', 'sbwc-om'); ?> &nbsp; &nbsp;<span style="color: #888"><?php _e('<u>[ NOTE:</u> Line Subtotals are based on pre-discount item cost, so may not match order total. ]', 'sbwc-om'); ?></span></h4>
                                <hr>
                                <br>

                                <table id="sbwc-om-order-product-data">

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
                                    <tbody id="sbwc-om-order-product-dataset">

                                    </tbody>
                                </table>

                            </div><!-- order products table container -->

                            <!-- update order -->
                            <div id="sbwc-om-update-order-cont">
                                <!-- update order data -->
                                <button id="sbwc-om-update-order" class="button button-primary button-large button-block" data-order-id="">
                                    <?php _e('Update Order', 'sbwc-om'); ?>
                                </button>
                            </div>

                        </div><!-- order data body -->
                    </div><!-- order lightbox -->

                <?php endif; ?>

            </div><!-- order list tab -->

            <!-- *********************** -->
            <!-- upload shipping csv tab -->
            <!-- *********************** -->
            <div id="sbwc-om-upload-csv">

                <!-- file upload inputs -->
                <div id="sbwc-om-csv-inputs">

                    <script>
                        jQuery(document).ready(function($) {
                            $('#post').attr('enctype', 'multipart/form-data')
                        });
                    </script>

                    <!-- input label -->
                    <p>
                        <label for="sbwc-om-upload-shipping-csv">
                            <?php _e('<b><i>Upload shipping CSV below with which to schedule bulk shipping update for this store</i></b><br><u>NOTE:</u> file gets renamed to store_name + timestamp, signifying timestamp on which last file was uploaded. This is done to avoid any potential processing errors.', 'sbwc-om'); ?>
                        </label>
                    </p>

                    <!-- last file uploaded -->
                    <p>
                        <u><i><b><?php _e('Last file uploaded:', 'sbwc-om'); ?></b></i></u>
                        <?php get_post_meta($post->ID, 'last_uploaded_csv', true) ? print get_post_meta($post->ID, 'last_uploaded_csv', true) : _e('NONE', 'sbwc-om'); ?>
                    </p>

                    <!-- last file processed -->
                    <p>
                        <u><i><b><?php _e('Last file processed:', 'sbwc-om'); ?></b></i></u>
                        <?php get_post_meta($post->ID, 'last_file_processed', true) ? print get_post_meta($post->ID, 'last_file_processed', true) : _e('NONE', 'sbwc-om'); ?>
                    </p>

                    <!-- file input -->
                    <p>
                        <input type="file" id="sbwc-om-upload-shipping-csv" name="sbwc-om-upload-shipping-csv">
                    </p>

                    <!-- file upload submit (submits standard form #post) -->
                    <p>
                        <input class="button button-primary button-large" name="sbwc-om-sub-ship-csv" type="submit" value="<?php _e('Upload CSV', 'sbwc-om'); ?>">
                    </p>

                </div><!-- file upload inputs ends -->

                <?php
                $csv_name = get_post_meta($post->ID, 'last_uploaded_csv', true);
                if ($csv_name) :
                ?>

                    <!-- csv file data -->
                    <div id="sbwc-om-csv-file-data">

                        <h4>
                            <?php _e('Review shipping data for last uploaded CSV:', 'sbwc-om'); ?>

                            <?php if (get_post_meta($post->ID, 'last_file_processed', true) !== $csv_name) : ?>

                                <!-- submit CSV for processing -->
                                <a href="#" id="sbwc-om-schedule-csv-process" class="button button-primary button-large" data-nonce="<?php echo wp_create_nonce('sbwc om process CSV'); ?>" data-store-id="<?php echo $post->ID; ?>" data-csv-name="<?php echo $csv_name; ?>">
                                    <?php _e('Submit CSV for processing', 'sbwc-om'); ?>
                                </a>

                            <?php else : ?>

                                <!-- CSV already processed -->
                                <a href="#" id="sbwc-om-schedule-csv-process" class="button button-primary button-large disabled">
                                    <?php _e('Current CSV already processed', 'sbwc-om'); ?>
                                </a>

                            <?php endif; ?>
                        </h4>

                        <table id="sbwc-om-csv-data-table" class="wp-list-table widefat fixed striped table-view-list">

                            <?php

                            $csv_file = SBWC_OM_PATH . 'uploads/' . $csv_name;
                            $csv = array_map('str_getcsv', file($csv_file));

                            foreach ($csv as $index => $line_data) :

                                if ($index === 0) : ?>
                                    <tr>
                                        <?php foreach ($line_data as $key => $hdata) : ?>
                                            <th>
                                                <b><?php echo $hdata; ?></b>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php else : ?>
                                    <tr>
                                        <?php foreach ($line_data as $key => $bdata) : ?>
                                            <?php if (gettype($bdata) === 'string' && !empty($bdata)) : ?>
                                                <td>
                                                    <?php echo $bdata; ?>
                                                </td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                            <?php endif;
                            endforeach;
                            ?>

                        </table><!-- #sbwc-om-csv-data-table ends -->
                    </div><!-- #sbwc-om-csv-file-data ends -->
                <?php endif; ?>
            </div><!-- #sbwc-om-upload-csv ends -->
        </div><!-- #sbwc-om-cpt-tabs ends -->

<?php }

    /**
     * Upload shipping CSV and insert associated processing flags to post meta
     *
     * @param  int $post_id - post id being saved to
     * @param  object $post - attached post object
     * @return void
     */
    public static function sbwc_om_save_shipp_csv_to_post($post_id, $post)
    {

        // check correct post type
        if ($post->post_type !== 'store') :
            return;
        endif;

        // move uploaded CSV
        $target_dir = SBWC_OM_PATH . "uploads/";
        $time_stamp = date('j_F_Y_h:i:s', strtotime('now'));
        $post_title = strtolower($post->post_title);
        $post_title = str_replace(' ', '_', $post_title);
        $target_file = $target_dir . $post_title . '_' . $time_stamp . '.csv';

        // if file successfully moved/uploaded
        if (move_uploaded_file($_FILES["sbwc-om-upload-shipping-csv"]["tmp_name"], $target_file)) :

            // update required post meta for processing via AS
            update_post_meta($post_id, 'last_uploaded_csv', $post_title . '_' . $time_stamp . '.csv');
            update_post_meta($post_id, 'last_file_processed', 'N/A');

        endif;
    }
}
