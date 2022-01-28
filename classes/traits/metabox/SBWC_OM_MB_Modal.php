<?php

/**
 * Renders order edit metabox
 */

trait SBWC_OM_MB_Modal
{

    public static function sbwc_om_mb_render_order_modal()
    { ?>

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

                </div>

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

                </div>

                <!-- update order -->
                <div id="sbwc-om-update-order-cont">

                    <!-- update order data -->
                    <button id="sbwc-om-update-order" class="button button-primary button-large button-block" data-order-id="">
                        <?php _e('Update Order', 'sbwc-om'); ?>
                    </button>
                </div>

            </div>
        </div>

<?php }
}

?>