<?php
/*
Plugin Name: My WP Add-on WooCommerce
Plugin URI: https://mywpcustomize.com/add_ons/my-wp-add-on-woocommerce/
Description: My WP Add-on WooCommerce is customize for WooCommerce on My WP.
Version: 1.2.0
Author: My WP Customize
Author URI: https://mywpcustomize.com/
Text Domain: mywp-woocommerce
Domain Path: /languages
My WP Test working: 1.24
WooCommerce Test working: 9.4.3
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpWooCommerce' ) ) :

final class MywpWooCommerce {

  public static function init() {

    self::define_constants();
    self::include_core();

    add_action( 'mywp_start' , array( __CLASS__ , 'mywp_start' ) );

  }

  private static function define_constants() {

    define( 'MYWP_WOOCOMMERCE_NAME' , 'My WP Add-On WooCommerce' );
    define( 'MYWP_WOOCOMMERCE_VERSION' , '1.2.0' );
    define( 'MYWP_WOOCOMMERCE_PLUGIN_FILE' , __FILE__ );
    define( 'MYWP_WOOCOMMERCE_PLUGIN_BASENAME' , plugin_basename( MYWP_WOOCOMMERCE_PLUGIN_FILE ) );
    define( 'MYWP_WOOCOMMERCE_PLUGIN_DIRNAME' , dirname( MYWP_WOOCOMMERCE_PLUGIN_BASENAME ) );
    define( 'MYWP_WOOCOMMERCE_PLUGIN_PATH' , plugin_dir_path( MYWP_WOOCOMMERCE_PLUGIN_FILE ) );
    define( 'MYWP_WOOCOMMERCE_PLUGIN_URL' , plugin_dir_url( MYWP_WOOCOMMERCE_PLUGIN_FILE ) );

  }

  private static function include_core() {

    $dir = MYWP_WOOCOMMERCE_PLUGIN_PATH . 'core/';

    require_once( $dir . 'class.api.php' );

  }

  public static function mywp_start() {

    add_action( 'mywp_plugins_loaded', array( __CLASS__ , 'mywp_plugins_loaded' ) );

    add_action( 'init' , array( __CLASS__ , 'wp_init' ) );

  }

  public static function mywp_plugins_loaded() {

    add_filter( 'mywp_controller_plugins_loaded_include_modules' , array( __CLASS__ , 'mywp_controller_plugins_loaded_include_modules' ) );

    add_filter( 'mywp_setting_plugins_loaded_include_modules' , array( __CLASS__ , 'mywp_setting_plugins_loaded_include_modules' ) );

    add_filter( 'mywp_developer_plugins_loaded_include_modules' , array( __CLASS__ , 'mywp_developer_plugins_loaded_include_modules' ) );

    add_filter( 'mywp_debug_types' , array( __CLASS__ , 'mywp_debug_types' ) );

  }

  public static function wp_init() {

    load_plugin_textdomain( 'mywp-woocommerce' , false , MYWP_WOOCOMMERCE_PLUGIN_DIRNAME . '/languages' );

  }

  public static function mywp_controller_plugins_loaded_include_modules( $includes ) {

    $dir = MYWP_WOOCOMMERCE_PLUGIN_PATH . 'controller/modules/';

    $includes['woocommerce_main_general']              = $dir . 'mywp.controller.module.main.general.php';
    $includes['woocommerce_updater']                   = $dir . 'mywp.controller.module.updater.php';
    $includes['woocommerce_admin_posts']               = $dir . 'mywp.controller.module.admin.posts.php';
    $includes['woocommerce_admin_products']            = $dir . 'mywp.controller.module.admin.products.php';
    $includes['woocommerce_admin_regist_list_columns'] = $dir . 'mywp.controller.module.admin.cot-orders.php';
    $includes['woocommerce_admin_cot_orders']          = $dir . 'mywp.controller.module.admin.regist.list-columns.php';
    $includes['woocommerce_admin_shop_orders']         = $dir . 'mywp.controller.module.admin.shop-orders.php';
    $includes['woocommerce_admin_woocommerce']         = $dir . 'mywp.controller.module.admin.woocommerce.php';

    return $includes;

  }

  public static function mywp_setting_plugins_loaded_include_modules( $includes ) {

    $dir = MYWP_WOOCOMMERCE_PLUGIN_PATH . 'setting/modules/';

    $includes['woocommerce_admin_cot_orders'] = $dir . 'mywp.setting.admin.cot-orders.php';

    return $includes;

  }

  public static function mywp_developer_plugins_loaded_include_modules( $includes ) {

    $dir = MYWP_WOOCOMMERCE_PLUGIN_PATH . 'developer/modules/';

    $includes['woocommerce_developer'] = $dir . 'mywp.developer.module.woocommerce.php';

    return $includes;

  }

  public static function mywp_debug_types( $debug_types ) {

    $debug_types['woocommerce'] = 'WooCommerce';

    return $debug_types;

  }

}

MywpWooCommerce::init();

endif;
