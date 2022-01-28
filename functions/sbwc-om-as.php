<?php

// schedule action which will remote update orders based on supplied shipping csv
function sbwc_om_schedule_shipping_process()
{
    if (false === as_has_scheduled_action('sbwc_om_remote_update_orders') && get_option('sbwc_om_store_scheduled')) {
        as_schedule_single_action(strtotime('now'), 'sbwc_om_remote_update_orders');
    }
}
add_action('init', 'sbwc_om_schedule_shipping_process');

// callback for remote update action scheduled above
function sbwc_process_remote_order_update()
{

    // retrieve store id
    $store_id = get_option('sbwc_om_store_scheduled');

    // retrieve store connection data
    $store_url       = get_post_meta($store_id, 'store_url', true);
    $store_cs_key    = get_post_meta($store_id, 'store_cs_key', true);
    $store_cs_secret = get_post_meta($store_id, 'store_cs_secret', true);

    // retrieve CSV name
    $csv_name = get_post_meta($store_id, 'last_uploaded_csv', true);

    // define CSV file location
    $csv_file = SBWC_OM_PATH . 'uploads/' . $csv_name;

    // mapped defined file to array
    $store_csv = array_map('str_getcsv', file($csv_file));

    // if $store_csv is not empty, loop and send update requests for each order
    if (!empty($store_csv)) :

        // setup logging vars
        $log_time_date = date('j F Y h:i:s', strtotime('now'));
        $store = get_the_title(($store_id));

        // data
        $data = [
            'shipping_data' => $store_csv
        ];

        // request link
        $request_link = "$store_url/wp-json/wc/v3/schedule_shipping?consumer_key=$store_cs_key&consumer_secret=$store_cs_secret";

        // send request
        $result = wp_remote_post($request_link, [
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 30,
            'body'    => json_encode($data)
        ]);

        // setup log message
        $log_msg = $log_time_date . ' - ' . $store . ': ' . $result['body'];

        // append request result to log
        file_put_contents(SBWC_OM_PATH . 'log/schedule-history.txt', $log_msg . PHP_EOL, FILE_APPEND);

    endif;

    // delete scheduled store id to avoid repeated processing
    delete_option('sbwc_om_store_scheduled');
}
add_action('sbwc_om_remote_update_orders', 'sbwc_process_remote_order_update');
