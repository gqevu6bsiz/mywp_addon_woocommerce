<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpWooCommerceApi' ) ) :

final class MywpWooCommerceApi {

  public static function plugin_info() {

    $plugin_info = array(
      'document_url' => 'https://mywpcustomize.com/add_ons/my-wp-add-on-woocommerce/',
      'website_url' => 'https://mywpcustomize.com/',
      'github' => 'https://github.com/gqevu6bsiz/mywp_addon_woocommerce',
      'github_releases' => 'https://github.com/gqevu6bsiz/mywp_addon_woocommerce/releases',
      'github_release_latest' => 'https://api.github.com/repos/gqevu6bsiz/mywp_addon_woocommerce/releases/latest',
    );

    $plugin_info = apply_filters( 'mywp_woocommerce_plugin_info' , $plugin_info );

    return $plugin_info;

  }

  public static function is_enable_woocommerce() {

    if( class_exists( 'WooCommerce' ) ) {

      return true;

    }

    return false;

  }

  public static function is_support_cot() {

    if( ! function_exists( 'wc_get_container' ) ) {

      return false;

    }

    if( ! method_exists( wc_get_container() , 'get' ) ) {

      return false;

    }

    if( ! class_exists( 'Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) ) {

      return false;

    }

    if( ! class_exists( 'MywpWooCommerceWC' ) ) {

      require_once( __DIR__ . '/' . 'class.woocommerce.php' );

    }

    return true;

  }

  public static function is_enabled_cot() {

    if( ! self::is_support_cot() ) {

      return false;

    }

    return MywpWooCommerceWC::is_enabled_cot();

  }

}

endif;
