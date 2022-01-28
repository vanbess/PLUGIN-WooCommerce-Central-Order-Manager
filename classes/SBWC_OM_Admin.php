<?php

/**
 * Renders admin page for order management
 */

class SBWC_OM_Admin
{

    use SBWC_OM_CSS,
        SBWC_OM_JS,
        SBWC_OM_CPT,
        SBWC_OM_CPT_Metabox,
        SBWC_OM_AJAX;

    /**
     * Class init
     *
     * @return void
     */
    public static function init()
    {
        // register admin pages
        add_action('admin_menu', [__CLASS__, 'sbwc_om_register_admin']);

        // css and js
        add_action('admin_head', [__CLASS__, 'sbwc_om_scripts']);

        // register stores post type
        add_action('init', [__CLASS__, 'sbwc_om_register_store_cpt']);

        // register stores post type metabox
        add_action('add_meta_boxes', [__CLASS__, 'sbwc_om_register_store_metabox']);

        // order retrieval AJAX
        add_action('wp_ajax_sbwc_om_ajax_retrieve_orders', [__CLASS__, 'sbwc_om_ajax_retrieve_orders']);
        add_action('wp_ajax_nopriv_sbwc_om_ajax_retrieve_orders', [__CLASS__, 'sbwc_om_ajax_retrieve_orders']);

        // save shipping csv
        add_action('save_post', [__CLASS__, 'sbwc_om_save_shipp_csv_to_post'], 10, 2);

        // schedule shipping csv processing via AJAX
        add_action('wp_ajax_sbwc_om_process_ship_csv', [__CLASS__, 'sbwc_om_process_ship_csv']);
        add_action('wp_ajax_nopriv_sbwc_om_process_ship_csv', [__CLASS__, 'sbwc_om_process_ship_csv']);
        
        // retrieve store shipping companies via AJAX
        add_action('wp_ajax_sbwc_om_retrieve_shipping_cos', [__CLASS__, 'sbwc_om_retrieve_shipping_cos']);
        add_action('wp_ajax_nopriv_sbwc_om_retrieve_shipping_cos', [__CLASS__, 'sbwc_om_retrieve_shipping_cos']);

    }

    /**
     * CSS and JS
     *
     * @return void
     */
    public static function sbwc_om_scripts()
    {
        wp_enqueue_style('sbwc-om-admin', self::sbwc_om_css(), [], false);
        wp_enqueue_style('sbwc-jquery-ui', SBWC_OM_URL . 'assets/css/jquery.ui-min.css', [], false);
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('sbwc-om-admin', self::sbwc_om_js(), ['jquery', 'jquery-ui-tabs'], false, true);
    }

    /**
     * Register order manager main admin and sub pages
     *
     * @return void
     */
    public static function sbwc_om_register_admin()
    {
        // main admin page which houses store CPTs
        add_menu_page(__('Order Manager', 'sbwc-om'), __('Order Manager', 'sbwc-om'), 'manage_options', 'sbwc-order-manager', '', 'dashicons-database-view', 10);

        // store connections page
        add_submenu_page('sbwc-order-manager', __('Store Connection Settings', 'sbwc-om'), __('Connect Stores', 'sbwc-om'), 'manage_options', 'sbwc-om-stores', [__CLASS__, 'sbwc_om_render_stores'], null);

        // processing log
        add_submenu_page('sbwc-order-manager', __('Log', 'sbwc-om'), __('Log', 'sbwc-om'), 'manage_options', 'sbwc-om-shipping-log', [__CLASS__, 'sbwc_om_render_shipping_log'], null);

        // readme
        add_submenu_page('sbwc-order-manager', __('README', 'sbwc-om'), __('README', 'sbwc-om'), 'manage_options', 'sbwc-om-readme', [__CLASS__, 'sbwc_om_render_readme'], null);
    }

    /**
     * Render README page
     *
     * @return void
     */
    public static function sbwc_om_render_readme()
    {

        global $title; ?>

        <div id="sbwc-om-readme">

            <h2 class="sbwc-om-admin-title">
                <?php echo $title; ?>
            </h2>

            <div id="sbwc-om-readme-text-cont">
                <?php
                $parsedown = new Parsedown();
                $readme = file_get_contents(SBWC_OM_PATH . 'README.md',);
                $rm_arr = explode(PHP_EOL, $readme);

                foreach ($rm_arr as $index => $line) :
                    echo $parsedown->text($line);
                endforeach;
                ?>
            </div>

        </div>

    <?php }

    /**
     * Render main admin page which displays orders based on connected stores
     *
     * @return void
     */
    public static function sbwc_om_render_shipping_log()
    {

        global $title;
    ?>

        <div id="sbwc-om-shipping-log">

            <h2 class="sbwc-om-admin-title">
                <?php echo $title; ?>
            </h2>

            <!-- instructions -->
            <p>
                <i>
                    <b>
                        <?php _e('Order Manager historical shipping log is displayed below. Successfully scheduled and failed updates are noted for your reference.', 'sbwc-om'); ?>
                    </b>
                </i>
            </p>

            <div id="sbwc-om-log">

                <table id="sbwc-om-log-table" class="wp-list-table striped">

                    <thead>
                        <tr>
                            <th><?php _e('Item #', 'sbwc-om'); ?></th>
                            <th><?php _e('Log', 'sbwc-om'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        // retrieve log file and display
                        $log_file = str_replace('"', '', file_get_contents(SBWC_OM_PATH . 'log/schedule-history.txt'));
                        $log_data = explode(PHP_EOL, $log_file);
                        $log_data = array_filter($log_data);
                        $counter = 1;

                        foreach ($log_data as $line_data) : ?>
                            <tr>
                                <td><b><?php echo $counter; ?></b></td>
                                <td><?php echo $line_data; ?></td>
                            </tr>
                        <?php $counter++;
                        endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>

        <?php }

    /**
     * Render admin page which displays stores connected to Order Manager and allows adding additional stores
     *
     * @return void
     */
    public static function sbwc_om_render_stores()
    {

        global $title;

        if (isset($_POST['sbwc-om-save-stores'])) :

            // store names
            $store_names = $_POST['sbwc-om-store-name'];

            // store urls
            $store_urls = $_POST['sbwc-om-store-url'];

            // store consumer keys
            $consumer_keys = $_POST['sbwc-om-store-key'];

            // store consumer secrets
            $consumer_secrets = $_POST['sbwc-om-store-secret'];

            // array to hold inserted store ids
            $store_ids = [];

            foreach ($store_names  as $index => $name) :

                $store_ids[] = wp_insert_post([
                    'post_type'    => 'store',
                    'post_status'  => 'publish',
                    'post_title'   => $name,
                    'post_content' => '',
                    'meta_input'   => [
                        'store_url'       => $store_urls[$index],
                        'store_cs_key'    => $consumer_keys[$index],
                        'store_cs_secret' => $consumer_secrets[$index],
                        'orders_fetched'  => 'no'
                    ]
                ]);

            endforeach;

            if (!empty($store_names)) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php _e('Stores successfully saved.', 'sbwc-om'); ?></p>
                </div>
            <?php else : ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php _e('Failed to save stores. Please reload the page and try again.', 'sbwc-om'); ?></p>
                </div>
        <?php endif;

        endif;

        ?>

        <div id="sbwc-om-stores">

            <h2 class="sbwc-om-admin-title"><?php echo $title; ?></h2>
            <p>
                <i>
                    <b><?php _e('Below you can add stores which should be connected to the Order Manager and associated connection details. Once this data is saved, the retrieval of relevant order data will be scheduled.', 'sbwc-om'); ?>
                    </b>
                </i>
            </p>

            <p>
                <?php _e('<u><b>NOTE:</b></u> You can create credentials for a specific store by navigating to WooCommerce -> Settings -> Advanced -> REST API in the WordPress Dashboard of the store in question.', 'sbwc-om'); ?>
            </p>

            <form action="" method="post">

                <!-- store details cont -->
                <div id="sbwc-om-store-details">

                    <?php
                    // query and display existing stores
                    $stores = new WP_Query([
                        'post_type'      => 'store',
                        'post_status'    => 'publish',
                        'posts_per_page' => -1,
                        'order'          => 'ASC'
                    ]);

                    if ($stores->have_posts()) :

                        while ($stores->have_posts()) : $stores->the_post(); ?>
                            <div class="sbwc-om-store-inputs">

                                <!-- store name -->
                                <input type="text" class="sbwc-om-store-name" name="sbwc-om-store-name[]" placeholder="<?php _e('store name', 'sbwc-om'); ?>" value="<?php echo get_the_title() ?>">

                                <!-- url -->
                                <input type="url" class="sbwc-om-store-url" name="sbwc-om-store-url[]" placeholder="<?php _e('store URL', 'sbwc-om'); ?>" value="<?php echo get_post_meta(get_the_ID(), 'store_url', true); ?>">

                                <!-- store key -->
                                <input type="text" class="sbwc-om-store-key" name="sbwc-om-store-key[]" placeholder="<?php _e('woocommerce client key', 'sbwc-om'); ?>" value="<?php echo get_post_meta(get_the_ID(), 'store_cs_key', true); ?>">

                                <!-- store secret -->
                                <input type="text" class="sbwc-om-store-secret" name="sbwc-om-store-secret[]" placeholder="<?php _e('woocommerce client secret', 'sbwc-om'); ?>" value="<?php echo get_post_meta(get_the_ID(), 'store_cs_secret', true); ?>">

                                <!-- add store -->
                                <button class="button button-primary button-small sbwc-om-add-store" title="<?php _e('add store', 'sbwc-om'); ?>">+</button>

                                <!-- remove store -->
                                <button class="button button-secondary button-small sbwc-om-rem-store" title="<?php _e('remove store', 'sbwc-om'); ?>" data-store-id="<?php echo get_the_ID(); ?>">-</button>

                            </div>
                    <?php endwhile;

                    endif;

                    ?>

                    <div class="sbwc-om-store-inputs">

                        <!-- store name -->
                        <input type="text" class="sbwc-om-store-name" name="sbwc-om-store-name[]" placeholder="<?php _e('store name', 'sbwc-om'); ?>">

                        <!-- url -->
                        <input type="url" class="sbwc-om-store-url" name="sbwc-om-store-url[]" placeholder="<?php _e('store URL', 'sbwc-om'); ?>">

                        <!-- store key -->
                        <input type="text" class="sbwc-om-store-key" name="sbwc-om-store-key[]" placeholder="<?php _e('woocommerce client key', 'sbwc-om'); ?>">

                        <!-- store secret -->
                        <input type="text" class="sbwc-om-store-secret" name="sbwc-om-store-secret[]" placeholder="<?php _e('woocommerce client secret', 'sbwc-om'); ?>">

                        <!-- add store -->
                        <button class="button button-primary button-small sbwc-om-add-store" title="<?php _e('add store', 'sbwc-om'); ?>">+</button>

                        <!-- remove store -->
                        <button class="button button-secondary button-small sbwc-om-rem-store" title="<?php _e('remove store', 'sbwc-om'); ?>">-</button>

                    </div>

                </div>

                <!-- save stores -->
                <p>
                    <button id="sbwc-om-save-stores" class="button button-primary button-large" type="submit" name="sbwc-om-save-stores">
                        <?php _e('Save Stores', 'sbwc-om'); ?>
                    </button>
                </p>

            </form>

    <?php }
}

SBWC_OM_Admin::init();
