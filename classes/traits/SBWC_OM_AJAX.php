<?php

/**
 * AJAX function to retrieve store orders and some other related stuff
 */

trait SBWC_OM_AJAX
{

  /**
   * AJAX to retrieve orders for a specific store
   *
   * @return void
   */
  public static function sbwc_om_ajax_retrieve_orders()
  {

    check_ajax_referer('sbwc om retrieve store orders');

    // *********************
    // ORDER LIST RETRIEVAL
    // *********************

    // retrieve store id
    $store_id = $_POST['store_id'];

    // retrieve store connection data
    $store_url       = get_post_meta($store_id, 'store_url', true);
    $store_cs_key    = get_post_meta($store_id, 'store_cs_key', true);
    $store_cs_secret = get_post_meta($store_id, 'store_cs_secret', true);

    // setup request url
    $request_url = "$store_url/wp-json/wc/v3/orders?consumer_key=$store_cs_key&consumer_secret=$store_cs_secret&status=processing&per_page=100&_fields=id,currency,date_modified,total,customer_id,order_key,shipping,number,line_items";

    // remote get
    $response = wp_remote_get($request_url, [
      'headers' => ['Content-Type' => 'application/json'],
      'timeout' => 30,
    ]);

    // retrieve order data
    $orders = json_decode($response['body'], true);

    // timestamp
    $timestamp = strtotime('now');

    // if $orders not empty, save to store and return success, else delete any previously saved store orders
    if (!empty($orders)) :

      // save orders
      $orders_saved = update_post_meta($store_id, 'store_orders', maybe_serialize($orders));

      // save timestamp
      $ts_saved = update_post_meta($store_id, 'retrieval_timestamp', $timestamp);

      // if no errors, send success message
      if (false !== $orders_saved || false !== $ts_saved) :
        wp_send_json(__('Latest orders successfully retrieved and saved to store.', 'sbwc-om'));
      endif;

    else :

      // delete previously saved orders, if any
      delete_post_meta($store_id, 'store_orders');

      // save timestamp
      $ts_saved = update_post_meta($store_id, 'retrieval_timestamp', $timestamp);

      if (false !== $ts_saved) :
        wp_send_json(__('No orders with a status of processing returned for this store, or order retrieval error occurred. If you\'re sure that there are orders available, check your store connection settings under Order Manager -> Connect Stores, or reload the page and try again.', 'sbwc-om'));
      endif;

    endif;

    wp_die();
  }

  /**
   * Process shipment tracking CSV
   *
   * @return void
   */
  public static function sbwc_om_process_ship_csv()
  {

    check_ajax_referer('sbwc om process CSV');

    // vars
    $store_id       = $_POST['store_id'];
    $csv_name       = $_POST['csv_name'];
    $last_processed = get_post_meta($store_id, 'last_csv_processed', true);

    // if file already processed, bail early
    if ($last_processed === $csv_name) :
      wp_send_json(__('File already scheduled for processing. Please upload a different file, or reupload the same file, and try again.', 'sbwc-om'));
      wp_die();
    endif;

    // update option which is listened to for scheduling retrieval function
    $store_scheduled = update_option('sbwc_om_store_scheduled', $store_id);

    // update last CSV file processed meta so that we avoid duplicate processing
    $csv_queued      = update_post_meta($store_id, 'last_csv_processed', $csv_name);

    // send appropriate responses
    if ($store_scheduled || $csv_queued) :
      wp_send_json(__('Store CSV successfully scheduled for processing', 'sbwc-om'));
    else :
      wp_send_json(__('Store CSV already scheduled for processing', 'sbwc-om'));
    endif;

    wp_die();
  }
}
