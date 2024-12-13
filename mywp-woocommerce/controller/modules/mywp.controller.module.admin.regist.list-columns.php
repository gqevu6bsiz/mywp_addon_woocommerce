<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminWooCommerceRegistListColumns' ) ) :

final class MywpControllerModuleAdminWooCommerceRegistListColumns extends MywpControllerAbstractModule {

  static protected $id = 'admin_woocommerce_regist_list_columns';

  static protected $is_do_controller = true;

  static private $column_type = '';

  static private $sortable_type = '';

  protected static function after_init() {

    add_filter( 'mywp_controller_pre_get_model_' . self::$id , array( __CLASS__ , 'mywp_controller_pre_get_model' ) );

  }

  public static function mywp_controller_pre_get_model( $pre_model ) {

    $pre_model = true;

    return $pre_model;

  }

  public static function mywp_wp_loaded() {

    if( ! is_admin() ) {

      return false;

    }

    if( is_network_admin() ) {

      return false;

    }

    if( ! MywpApi::is_manager() ) {

      return false;

    }

    if( ! MywpWooCommerceApi::is_enable_woocommerce() ) {

      return false;

    }

    if( ! MywpWooCommerceApi::is_enabled_cot() ) {

      return false;

    }

    add_action( 'load-woocommerce_page_wc-orders' , array( __CLASS__ , 'load_screen' ) , 999 );

  }

  public static function load_screen() {

    if( ! empty( $_GET['action'] ) ) {

      return false;

    }

    self::$column_type = 'woocommerce_page_wc-orders';

    add_filter( 'manage_' . self::$column_type . '_columns' , array( __CLASS__ , 'registed_columns' ) , 10000 );

    add_filter( 'manage_' . self::$column_type . '_sortable_columns' , array( __CLASS__ , 'registed_sortable_columns' ) , 10000 );

  }

  public static function registed_columns( $columns ) {

    if( empty( self::$column_type ) ) {

      return $columns;

    }

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $columns;

    }

    $mywp_model = MywpControllerModuleAdminRegistListColumns::get_model();

    if( empty( $mywp_model ) ) {

      return $columns;

    }

    $option = $mywp_model->get_option();

    if( empty( $option ) ) {

      $option = array();

    }

    $column_id = self::$column_type;

    if( empty( $option['regist_columns'][ $column_id ] ) ) {

      $option['regist_columns'][ $column_id ] = array();

    }

    $option['regist_columns'][ $column_id ]['columns'] = $columns;

    $mywp_model->update_data( $option );

    self::after_do_function( __FUNCTION__ );

    return $columns;

  }

  public static function registed_sortable_columns( $columns ) {

    if( empty( self::$column_type ) ) {

      return $columns;

    }

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $columns;

    }

    $mywp_model = MywpControllerModuleAdminRegistListColumns::get_model();

    if( empty( $mywp_model ) ) {

      return $columns;

    }

    $option = $mywp_model->get_option();

    if( empty( $option ) ) {

      $option = array();

    }

    $column_id = self::$column_type;

    if( empty( $option['regist_columns'][ $column_id ] ) ) {

      $option['regist_columns'][ $column_id ] = array();

    }

    $option['regist_columns'][ $column_id ]['sortables'] = $columns;

    $mywp_model->update_data( $option );

    self::after_do_function( __FUNCTION__ );

    return $columns;

  }

}

MywpControllerModuleAdminWooCommerceRegistListColumns::init();

endif;
