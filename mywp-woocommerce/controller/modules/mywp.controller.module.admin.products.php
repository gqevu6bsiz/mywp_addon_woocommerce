<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminWooCommerceProducts' ) ) :

final class MywpControllerModuleAdminWooCommerceProducts extends MywpControllerAbstractModule {

  static protected $id = 'admin_woocommerce_products';

  static private $post_type = 'product';

  public static function mywp_wp_loaded() {

    if( ! self::is_do_controller() ) {

      return false;

    }

    if( ! MywpWooCommerceApi::is_enable_woocommerce() ) {

      return false;

    }

    add_action( 'mywp_ajax' , array( __CLASS__ , 'mywp_ajax' ) , 1000 );

    add_action( 'load-edit.php' , array( __CLASS__ , 'load_edit' ) , 1000 );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'mywp_setting_admin_posts_get_available_list_columns_' . self::$post_type , array( __CLASS__ , 'mywp_setting_admin_posts_get_available_list_columns' ) );

    add_filter( 'mywp_controller_admin_posts_custom_search_filter_fields-' . self::$post_type , array( __CLASS__ , 'mywp_controller_admin_posts_custom_search_filter_fields' ) , 9 );

    add_action( 'mywp_controller_admin_posts_custom_search_filter-' . self::$post_type , array( __CLASS__ , 'mywp_controller_admin_posts_custom_search_filter' ) , 9 , 2 );

  }

  public static function mywp_ajax() {

    if( empty( $_POST['action'] ) or $_POST['action'] !== 'inline-save' ) {

      return false;

    }

    if( empty( $_POST['screen'] ) ) {

      return false;

    }

    if( empty( $_POST['post_type'] ) ) {

      return false;

    }

    $post_type = strip_tags( $_POST['post_type'] );

    if( $post_type !== self::$post_type ) {

      return false;

    }

    add_action( 'manage_' . self::$post_type . '_posts_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

  }

  public static function load_edit() {

    global $typenow;

    if( empty( $typenow ) ) {

      return false;

    }

    if( $typenow !== self::$post_type ) {

      return false;

    }

    add_action( "manage_{$typenow}_posts_custom_column" , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

  }

  public static function change_column_width() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = MywpControllerModuleAdminPosts::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return false;

    }

    $columns = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

      if( ! in_array( $column_id , array( 'product_cat' , 'product_tag' ) , true ) ) {

        continue;

      }

      if( empty( $column_setting['width'] ) ) {

        continue;

      }

      $columns[ $column_id ] = $column_setting['width'];

    }

    if( empty( $columns ) ) {

      return false;

    }

    echo '<style>';

    foreach( $columns as $column_id => $width ) {

      echo 'body.wp-admin .wp-list-table.widefat thead th.column-' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . ' !important; display: table-cell; }';

      echo 'body.wp-admin .wp-list-table.widefat thead th#' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . ' !important; display: table-cell; }';

    }

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function manage_column_body( $column_id , $post_id ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    if( strpos( $column_id , 'mywp_wc_column_' ) === false ) {

      return false;

    }

    $column_type = str_replace( 'mywp_wc_column_' , '' , $column_id );

    $WC_Product = wc_get_product( $post_id );

    if( $column_type === 'product_type' ) {

      printf( '[%s]<br />' , esc_html( $WC_Product->get_type() ) );

      $product_types = wc_get_product_types();

      if( ! empty( $product_types ) ) {

        foreach( $product_types as $product_type => $product_type_title ) {

          if( $product_type === $WC_Product->get_type() ) {

            echo esc_html( $product_type_title );

            break;

          }

        }

      }

    } elseif( $column_type === 'tax_status' ) {

      printf( '[%s]<br />' , esc_html( $WC_Product->get_tax_status( 'edit' ) ) );

      if( $WC_Product->get_tax_status() === 'taxable' ) {

        echo esc_html( __( 'Taxable' , 'woocommerce' ) );

      } elseif( $WC_Product->get_tax_status() === 'shipping' ) {

        echo esc_html( __( 'Shipping only' , 'woocommerce' ) );

      } elseif( $WC_Product->get_tax_status() === 'none' ) {

        echo esc_html( _x( 'None' , 'Tax status' , 'woocommerce' ) );

      }

    } elseif( $column_type === 'tax_class' ) {

      printf( '[%s]<br />' , esc_html( $WC_Product->get_tax_class( 'edit' ) ) );

      $wc_get_product_tax_class_options = wc_get_product_tax_class_options();

      if( ! empty( $wc_get_product_tax_class_options[ $WC_Product->get_tax_class() ] ) ) {

        echo esc_html( $wc_get_product_tax_class_options[ $WC_Product->get_tax_class() ] );

      }

    } elseif( $column_type === 'manage_stock' ) {

      echo esc_html( $WC_Product->get_manage_stock() );

    } elseif( $column_type === 'backorders' ) {

      printf( '[%s]<br />' , esc_html( $WC_Product->get_backorders( 'edit' ) ) );

      $product_backorder_options = wc_get_product_backorder_options();

      if( ! empty( $product_backorder_options[ $WC_Product->get_backorders( 'edit' ) ] ) ) {

        echo esc_html( $product_backorder_options[ $WC_Product->get_backorders( 'edit' ) ] );

      }

    } elseif( $column_type === 'sold_individually' ) {

      echo esc_html( $WC_Product->get_sold_individually( 'edit' ) );

    } elseif( $column_type === 'virtual' ) {

      echo esc_html( $WC_Product->get_virtual( 'edit' ) );

    } elseif( $column_type === 'downloadable' ) {

      echo esc_html( $WC_Product->get_downloadable( 'edit' ) );

    }

    self::after_do_function( __FUNCTION__ );

  }

  private static function get_product_list_default_columns() {

    $product_list_default_columns = array(
      'thumb' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '52px',
      ),
      'name' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '22%',
      ),
      'sku' => array(
        'sort' => true,
        'orderby' => 'sku',
        'width' => '10%',
      ),
      'is_in_stock' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '12ch',
      ),
      'price' => array(
        'sort' => true,
        'orderby' => 'price',
        'width' => '10ch',
      ),
      'product_cat' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '11%',
      ),
      'product_tag' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '11%',
      ),
      'featured' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '48px',
      ),
    );

    return $product_list_default_columns;

  }

  private static function get_product_list_extend_columns() {

    $product_list_extend_columns = array(
      'product_type' => array(
        'title' => __( 'Product Type' , 'woocommerce' ),
        'default_title' => __( 'Product Type' , 'woocommerce' ),
      ),
      'tax_status' => array(
        'title' => __( 'Tax status' , 'woocommerce' ),
        'default_title' => __( 'Tax status' , 'woocommerce' ),
      ),
      'tax_class' => array(
        'title' => __( 'Tax class' , 'woocommerce' ),
        'default_title' => __( 'Tax class' , 'woocommerce' ),
      ),
      'manage_stock' => array(
        'title' => __( 'Stock management' , 'woocommerce' ),
        'default_title' => __( 'Stock management' , 'woocommerce' ),
      ),
      'backorders' => array(
        'title' => __( 'Allow backorders?' , 'woocommerce' ),
        'default_title' => __( 'Allow backorders?' , 'woocommerce' ),
      ),
      'sold_individually' => array(
        'title' => __( 'Sold individually' , 'woocommerce' ),
        'default_title' => __( 'Sold individually' , 'woocommerce' ),
      ),
      'virtual' => array(
        'title' => __( 'Virtual' , 'woocommerce' ),
        'default_title' => __( 'Virtual' , 'woocommerce' ),
      ),
      'downloadable' => array(
        'title' => __( 'Downloadable' , 'woocommerce' ),
        'default_title' => __( 'Downloadable' , 'woocommerce' ),
      ),
    );

    return $product_list_extend_columns;

  }

  public static function mywp_setting_admin_posts_get_available_list_columns( $available_list_columns ) {

    if( empty( $available_list_columns ) ) {

      return $available_list_columns;

    }

    if( ! is_array( $available_list_columns ) ) {

      return $available_list_columns;

    }

    $new_available_list_columns = array();

    foreach( $available_list_columns as $available_list_column_key => $available_list_column ) {

      $new_available_list_columns[ $available_list_column_key ] = $available_list_column;

      if( $available_list_column_key === 'other' ) {

        $new_available_list_columns['woocommerce'] = array(
          'title' => 'WooCommerce',
          'columns' => array(),
        );

      }

    }

    $old_available_list_columns = $available_list_columns;

    $available_list_columns = $new_available_list_columns;

    $product_list_default_columns = self::get_product_list_default_columns();

    foreach( $product_list_default_columns as $column_id => $column_args ) {

      if( isset( $available_list_columns['other']['columns'][ $column_id ] ) ) {

        $available_list_columns['other']['columns'][ $column_id ] = wp_parse_args( $column_args , $available_list_columns['other']['columns'][ $column_id ] );

      }

    }

    $product_list_extend_columns = self::get_product_list_extend_columns();

    $columns = array();

    foreach( $product_list_extend_columns as $column_id => $column_args ) {

      if( isset( $available_list_columns['deprecated']['columns'][ $column_id ] ) ) {

        unset( $available_list_columns['deprecated']['columns'][ $column_id ] );

      }

      if( isset( $available_list_columns['deprecated']['columns'][ '_' . $column_id ] ) ) {

        unset( $available_list_columns['deprecated']['columns'][ '_' . $column_id ] );

      }

      $column = array(
        'id' => 'mywp_wc_column_' . $column_id,
        'type' => 'woocommerce',
        'sort' => false,
        'orderby' => '',
        'title' => '',
        'default_title' => '',
        'width' => '',
      );

      $columns[ 'mywp_wc_column_' . $column_id ] = wp_parse_args( $column_args , $column );

    }

    $available_list_columns['woocommerce']['columns'] = $columns;

    return $available_list_columns;

  }

  private static function get_product_custom_search_filter_fields() {

    $product_types = wc_get_product_types();

    $wc_get_product_tax_class_options = wc_get_product_tax_class_options();

    $product_tax_class_options = array();

    foreach( $wc_get_product_tax_class_options as $key => $val ) {

      if( empty( $key ) ) {

        $product_tax_class_options['_default_tax'] = $val;

        continue;

      }

      $product_tax_class_options[ rawurldecode( $key ) ] = $val;

    }

    $product_backorder_options = wc_get_product_backorder_options();

    $product_custom_search_filter_fields = array(
      'mywp_custom_search_wc_product_type' => array(
        'id' => 'mywp_custom_search_wc_product_type',
        'title' => __( 'Product Type' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => $product_types,
      ),
      'mywp_custom_search_wc_tax_status' => array(
        'id' => 'mywp_custom_search_wc_tax_status',
        'title' => __( 'Tax status' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => array(
          'taxable' => __( 'Taxable' , 'woocommerce' ),
          'shipping' => __( 'Shipping only' , 'woocommerce' ),
          'none' => _x( 'None' , 'Tax status' , 'woocommerce' ),
        ),
      ),
      'mywp_custom_search_wc_tax_class' => array(
        'id' => 'mywp_custom_search_wc_tax_class',
        'title' => __( 'Tax class' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => $product_tax_class_options,
      ),
      'mywp_custom_search_wc_manage_stock' => array(
        'id' => 'mywp_custom_search_wc_manage_stock',
        'title' => __( 'Stock management' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => array(
          'no' => __( 'No' , 'woocommerce' ),
          'yes' => __( 'Yes' , 'woocommerce' ),
        ),
      ),
      'mywp_custom_search_wc_backorders' => array(
        'id' => 'mywp_custom_search_wc_backorders',
        'title' => __( 'Allow backorders?' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => $product_backorder_options,
      ),
      'mywp_custom_search_wc_sold_individually' => array(
        'id' => 'mywp_custom_search_wc_sold_individually',
        'title' => __( 'Sold individually' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => array(
          'no' => __( 'No' , 'woocommerce' ),
          'yes' => __( 'Yes' , 'woocommerce' ),
        ),
      ),
      'mywp_custom_search_wc_virtual' => array(
        'id' => 'mywp_custom_search_wc_virtual',
        'title' => __( 'Virtual' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => array(
          'no' => __( 'No' , 'woocommerce' ),
          'yes' => __( 'Yes' , 'woocommerce' ),
        ),
      ),
      'mywp_custom_search_wc_downloadable' => array(
        'id' => 'mywp_custom_search_wc_downloadable',
        'title' => __( 'Downloadable' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => array(
          'no' => __( 'No' , 'woocommerce' ),
          'yes' => __( 'Yes' , 'woocommerce' ),
        ),
      ),
    );

    return $product_custom_search_filter_fields;

  }

  public static function mywp_controller_admin_posts_custom_search_filter_fields( $custom_search_filter_fields ) {

    $product_custom_search_filter_fields = self::get_product_custom_search_filter_fields();

    if( empty( $product_custom_search_filter_fields ) ) {

      return $custom_search_filter_fields;

    }

    if( isset( $custom_search_filter_fields['mywp_custom_search_taxonomy_product_type'] ) ) {

      unset( $custom_search_filter_fields['mywp_custom_search_taxonomy_product_type'] );

    }

    $custom_search_filter_fields = wp_parse_args( $product_custom_search_filter_fields , $custom_search_filter_fields );

    return $custom_search_filter_fields;

  }

  public static function mywp_controller_admin_posts_custom_search_filter( $query , $custom_search_filter_requests ) {

    $meta_query = array();

    $tax_query = array();

    foreach( $custom_search_filter_requests as $custom_search_filter_request_key => $custom_search_filter_request ) {

      if( empty( $custom_search_filter_request ) ) {

        continue;

      }

      if( strpos( $custom_search_filter_request_key , 'mywp_custom_search_wc_' ) === false ) {

        continue;

      }

      $custom_search_filter_type = str_replace( 'mywp_custom_search_wc_' , '' , $custom_search_filter_request_key );

      if( $custom_search_filter_type === 'product_type' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $tax_query[] = array(
            'taxonomy' => 'product_type',
            'field' => 'slug',
            'terms' => array( $value ),
            'operator' => 'IN',
          );

        }

      } elseif( $custom_search_filter_type === 'tax_status' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_tax_status',
            'value' => $value,
            'compare' => '=',
          );

        }

      } elseif( $custom_search_filter_type === 'tax_class' ) {

        $value = rawurlencode( MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) ) );

        if( ! empty( $value ) ) {

          if( $value === '_default_tax' ) {

            $meta_query[] = array(
              'key' => '_tax_class',
              'value' => '',
              'compare' => '=',
            );

          } else {

            $meta_query[] = array(
              'key' => '_tax_class',
              'value' => $value,
              'compare' => '=',
            );

          }

        }

      } elseif( $custom_search_filter_type === 'manage_stock' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_manage_stock',
            'value' => $value,
            'compare' => '=',
          );

        }

      } elseif( $custom_search_filter_type === 'backorders' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_backorders',
            'value' => $value,
            'compare' => '=',
          );

        }

      } elseif( $custom_search_filter_type === 'sold_individually' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_sold_individually',
            'value' => $value,
            'compare' => '=',
          );

        }

      } elseif( $custom_search_filter_type === 'virtual' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_virtual',
            'value' => $value,
            'compare' => '=',
          );

        }

      } elseif( $custom_search_filter_type === 'downloadable' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_downloadable',
            'value' => $value,
            'compare' => '=',
          );

        }

      }

    }

    if( ! empty( $tax_query ) ) {

      $query->set( 'tax_query' , wp_parse_args( $tax_query , array( 'relation' => 'AND' ) ) );

    }

    if( ! empty( $meta_query ) ) {

      $query->set( 'meta_query' , wp_parse_args( $meta_query , array( 'relation' => 'AND' ) ) );

    }

  }

}

MywpControllerModuleAdminWooCommerceProducts::init();

endif;
