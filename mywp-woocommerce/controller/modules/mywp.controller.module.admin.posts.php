<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminWooCommercePosts' ) ) :

final class MywpControllerModuleAdminWooCommercePosts extends MywpControllerAbstractModule {

  static protected $id = 'admin_woocommerce_posts';

  static private $post_type = '';

  public static function mywp_wp_loaded() {

    if( ! self::is_do_controller() ) {

      return false;

    }

    if( ! MywpWooCommerceApi::is_enable_woocommerce() ) {

      return false;

    }

    add_filter( 'mywp_controller_admin_posts_get_post_statuses' , array( __CLASS__ , 'mywp_controller_admin_posts_get_post_statuses' ) , 9 , 2 );

  }

  public static function mywp_controller_admin_posts_get_post_statuses( $post_statuses , $post_type ) {

    $order_statuses = wc_get_order_statuses();

    if( empty( $order_statuses ) ) {

      return $post_statuses;

    }

    foreach( $order_statuses as $order_status => $order_status_label ) {

      if( isset( $post_statuses[ $order_status ] ) ) {

        unset( $post_statuses[ $order_status ] );

      }

    }

    return $post_statuses;

  }

}

MywpControllerModuleAdminWooCommercePosts::init();

endif;
