<?php

/**
 * Render store order list
 */
trait SBWC_OM_MB_Order_List
{

    public static function sbwc_om_render_store_order_list()
    {

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

            <table id="sbwc-om-orders" class="wp-list-table widefat fixed striped table-view-list">
                <thead>
                    <tr>
                        <th><?php _e('Status', 'sbwc-om'); ?></th>
                        <th><?php _e('Date', 'sbwc-om'); ?></th>
                        <th><?php _e('ID', 'sbwc-om'); ?></th>
                        <th><?php _e('Number', 'sbwc-om'); ?></th>
                        <th><?php _e('Customer', 'sbwc-om'); ?></th>
                        <th><?php _e('Total', 'sbwc-om'); ?></th>
                        <th><?php _e('Shipping Address', 'sbwc-om'); ?></th>
                        <th><?php _e('View', 'sbwc-om'); ?></th>
                    </tr>
                </thead>
                <tbody>

                    <?php foreach ($orders as $o_index => $order_data) : ?>
                        <tr>

                            <!-- status -->
                            <td>
                                <?php
                                in_array($order_data['id'], get_post_meta($post->ID, 'updated_orders', true)) ? _e('<span title="order has been finalized" style="cursor: help;"><b>F</b></span>', 'sbwc-om') : _e('<span title="to be finalized" style="cursor: help;"><b><u>TBF</u></b></span>', 'sbwc-om');
                                ?>
                            </td>

                            <!-- date -->
                            <td>
                                <?php echo date('j F, Y', strtotime($order_data['date_modified'])); ?>
                            </td>

                            <!-- order id -->
                            <td>
                                <?php echo $order_data['id']; ?>
                            </td>

                            <!-- order number -->
                            <td>
                                <?php
                                echo $order_data['number'];
                                ?>
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
            </table>
<?php endif;
    }
}

?>