<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleWooCommerce' ) ) :

final class MywpControllerModuleWooCommerce extends MywpControllerAbstractModule {

  static protected $id = 'woocommerce';

  static private $post_type = '';

  protected static function after_init() {

    add_action( 'registered_post_type' , array( __CLASS__ , 'registered_post_type' ) );

    add_filter( 'mywp_shortcode' , array( __CLASS__ , 'mywp_shortcode' ) );

  }

  public static function mywp_wp_loaded() {

    if( ! self::is_do_controller() ) {

      return false;

    }

    if( ! class_exists( 'WooCommerce' ) ) {

      return false;

    }

    add_filter( 'mywp_setting_get_latest_post_args_shop_order' , array( __CLASS__ , 'mywp_setting_get_latest_post_args_shop_order' ) );

    add_filter( 'mywp_setting_admin_sidebar_get_default_sidebar_items' , array( __CLASS__ , 'mywp_setting_admin_sidebar_get_default_sidebar_items' ) , 10 , 2 );

    add_filter( 'mywp_controller_admin_sidebar_get_sidebar_item_added_classes_found_current_item_ids' , array( __CLASS__ , 'mywp_controller_admin_sidebar_get_sidebar_item_added_classes_found_current_item_ids' ) , 10 , 5 );

  }

  public static function registered_post_type() {

    global $wp_post_types;

    if( ! MywpDeveloper::is_debug() ) {

      return false;

    }

    if( empty( $wp_post_types['product_variation'] ) ) {

      return false;

    }

    $wp_post_types['product_variation']->show_ui = 1;

  }

  public static function mywp_shortcode( $shortcodes ) {

    $shortcodes['mywp_woocommerce_order_count'] = array( __CLASS__ , 'do_shortcode_order_count' );

    return $shortcodes;

  }

  public static function do_shortcode_order_count( $atts = array() , $content = false , $tag = false ) {

    if( ! current_user_can( 'manage_woocommerce' ) ) {

      return false;

    }

    $status = 'processing';

    if( ! empty( $atts['status'] ) ) {

      $status = strip_tags( $atts['status'] );

    }

    $count = wc_orders_count( $status );

    if( empty( $count ) ) {

      return $content;

    }

    if( ! empty( $atts['tag'] ) ) {

      $content = sprintf(
        '<span class="update-plugins count-%d"><span class="%s-count">%d</span></span>',
        $count,
        $status,
        number_format_i18n( $count )
      );

    } else {

      $content = $count;

    }

    return $content;

  }

  public static function mywp_setting_get_latest_post_args_shop_order( $args ) {

    $args['post_status'] = array( 'wc-processing', 'wc-completed' );

    return $args;

  }

  public static function mywp_setting_admin_sidebar_get_default_sidebar_items( $default_sidebar ) {

    if( empty( $default_sidebar ) ) {

      return $default_sidebar;

    }

    if( ! empty( $default_sidebar['submenu']['woocommerce'] ) ) {

      foreach( $default_sidebar['submenu']['woocommerce'] as $key => $submenu ) {

        if( $submenu[2] === 'edit.php?post_type=shop_order' ) {

          $default_sidebar['submenu']['woocommerce'][ $key ][0] = sprintf( '%s %s' , _x( 'Orders', 'Admin menu name', 'woocommerce' ) , '[mywp_woocommerce_order_count status="processing" tag="1"]' );

          break;

        }

      }

    }

    return $default_sidebar;

  }

  public static function mywp_controller_admin_sidebar_get_sidebar_item_added_classes_found_current_item_ids( $found_current_item_ids , $sidebar_items , $current_url , $current_url_parse , $current_url_query ) {

    if( ! empty( $found_current_item_ids ) ) {

      return $found_current_item_ids;

    }

    if( empty( $current_url_query['post_type'] ) or empty( $current_url_query['taxonomy'] ) or $current_url_query['post_type'] !== 'product' ) {

      return $found_current_item_ids;

    }

    if(
      strpos( $current_url_parse['path'] , 'edit-tags.php' ) === false &&
      strpos( $current_url_parse['path'] , 'term.php' ) === false
    ) {

      return $found_current_item_ids;

    }

    foreach( $sidebar_items as $key => $sidebar_item ) {

      if( ! is_object( $sidebar_item ) ) {

        continue;

      }

      if( empty( $sidebar_item->item_link_url_parse['host'] ) or empty( $sidebar_item->item_link_url_parse['path'] ) ) {

        continue;

      }

      if(
        $current_url_parse['scheme'] !== $sidebar_item->item_link_url_parse['scheme'] or
        $current_url_parse['host'] !== $sidebar_item->item_link_url_parse['host']
      ) {

        continue;

      }

      if( empty( $sidebar_item->item_link_url_parse_query['post_type'] ) or $sidebar_item->item_link_url_parse_query['post_type'] !== 'product' ) {

        continue;

      }

      if( empty( $sidebar_item->item_link_url_parse_query['page'] ) or $sidebar_item->item_link_url_parse_query['page'] !== 'product_attributes' ) {

        continue;

      }

      $found_current_item_ids[] = $sidebar_item->ID;

    }


    return $found_current_item_ids;

  }

}

MywpControllerModuleWooCommerce::init();

endif;
