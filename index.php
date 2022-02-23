<?php

/**
 * Plugin Name: SBWC Order Manager
 * Description: Central Order Management System for WooCommerce
 * Author: WC Bessinger
 * Version: 1.0.0
 * Text Domain: sbwc-om
 */

if (!defined('ABSPATH')) :
    exit();
endif;

define('SBWC_OM_PATH', plugin_dir_path(__FILE__));
define('SBWC_OM_URL', plugin_dir_url(__FILE__));

// main class & traits
include SBWC_OM_PATH . 'classes/traits/SBWC_OM_CSS.php';
include SBWC_OM_PATH . 'classes/traits/SBWC_OM_JS.php';
include SBWC_OM_PATH . 'classes/traits/SBWC_OM_CPT.php';
include SBWC_OM_PATH . 'classes/traits/SBWC_OM_CPT_Metabox.php';
include SBWC_OM_PATH . 'classes/traits/SBWC_OM_AJAX.php';
include SBWC_OM_PATH . 'classes/traits/SBWC_OM_All_Orders.php';
include SBWC_OM_PATH . 'classes/SBWC_OM_Admin.php';

// action scheduler function to remote update orders
include SBWC_OM_PATH.'functions/sbwc-om-as.php';

// parsedown
include SBWC_OM_PATH.'composer/vendor/autoload.php';