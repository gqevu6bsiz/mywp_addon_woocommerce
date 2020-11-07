<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpWooCommerceApi' ) ) :

final class MywpWooCommerceApi {

  private static $instance;

  private function __construct() {}

  public static function get_instance() {

    if ( !isset( self::$instance ) ) {

      self::$instance = new self();

    }

    return self::$instance;

  }

  private function __clone() {}

  private function __wakeup() {}

  public static function plugin_info() {

    $plugin_info = array(
      'document_url' => 'https://mywpcustomize.com/add_ons/my-wp-add-on-woocommerce/',
      'website_url' => 'https://mywpcustomize.com/',
      'github' => 'https://github.com/gqevu6bsiz/mywp_addon_woocommerce',
      'github_tags' => 'https://api.github.com/repos/gqevu6bsiz/mywp_addon_woocommerce/tags',
    );

    $plugin_info = apply_filters( 'mywp_woocommerce_plugin_info' , $plugin_info );

    return $plugin_info;

  }

}

endif;
