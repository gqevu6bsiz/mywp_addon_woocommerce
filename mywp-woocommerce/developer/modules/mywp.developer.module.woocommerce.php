<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpDeveloperAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpDeveloperModuleDevWooCommerce' ) ) :

final class MywpDeveloperModuleDevWooCommerce extends MywpDeveloperAbstractModule {

  static protected $id = 'dev_woocommerce';

  protected static function after_init() {

    add_action( 'mywp_wp_loaded' , array( __CLASS__ , 'mywp_wp_loaded' ) );

  }

  public static function mywp_debug_renders( $debug_renders ) {

    $debug_renders[ self::$id . '_product' ] = array(
      'debug_type' => 'woocommerce',
      'title' => 'Product',
    );

    $debug_renders[ self::$id . '_order' ] = array(
      'debug_type' => 'woocommerce',
      'title' => 'Order',
    );

    $debug_renders[ self::$id . '_customer' ] = array(
      'debug_type' => 'woocommerce',
      'title' => 'Customer',
    );

    $debug_renders[ self::$id . '_cart' ] = array(
      'debug_type' => 'woocommerce',
      'title' => 'Cart',
    );

    $debug_renders[ self::$id . '_checkout' ] = array(
      'debug_type' => 'woocommerce',
      'title' => 'Checkout',
    );

    $debug_renders[ self::$id . '_session' ] = array(
      'debug_type' => 'woocommerce',
      'title' => 'Session',
    );

    return $debug_renders;

  }

  public static function mywp_wp_loaded() {

    if( ! function_exists( 'WC' ) ) {

      return false;

    }

    add_action( 'mywp_request_admin' , array( __CLASS__ , 'mywp_request_admin' ) );

    add_action( 'mywp_request_frontend' , array( __CLASS__ , 'mywp_request_frontend' ) );

  }

  public static function mywp_request_admin() {

    add_action( 'load-post.php' , array( __CLASS__ , 'admin_load_post' ) );

    add_action( 'load-post-new.php' , array( __CLASS__ , 'admin_load_post' ) );

  }

  public static function mywp_request_frontend() {

    add_action( 'wp' , array( __CLASS__ , 'wp' ) );

  }

  public static function wp() {

    if( is_product() ) {

      add_action( 'mywp_debug_render_' . self::$id . '_product' , array( __CLASS__ , 'mywp_debug_render_product' ) );

    } elseif( is_account_page() && is_wc_endpoint_url( 'view-order' ) ) {

      add_action( 'mywp_debug_render_' . self::$id . '_order' , array( __CLASS__ , 'mywp_debug_render_shop_order' ) );

    }

    add_action( 'mywp_debug_render_' . self::$id . '_customer' , array( __CLASS__ , 'mywp_debug_render_customer' ) );

    add_action( 'mywp_debug_render_' . self::$id . '_cart' , array( __CLASS__ , 'mywp_debug_render_cart' ) );

    add_action( 'mywp_debug_render_' . self::$id . '_checkout' , array( __CLASS__ , 'mywp_debug_render_checkout' ) );

    add_action( 'mywp_debug_render_' . self::$id . '_session' , array( __CLASS__ , 'mywp_debug_render_session' ) );

  }

  public static function admin_load_post() {

    global $typenow;

    if( $typenow === 'product' ) {

      add_action( 'mywp_debug_render_' . self::$id . '_product' , array( __CLASS__ , 'mywp_debug_render_product' ) );

    } elseif( $typenow === 'shop_order' ) {

      add_action( 'mywp_debug_render_' . self::$id . '_order' , array( __CLASS__ , 'mywp_debug_render_shop_order' ) );

    }

  }

  public static function mywp_debug_render_product() {

    global $post;

    if( ! is_object( $post ) or empty( $post->ID ) ) {

      return false;

    }

    $product_id = $post->ID;

    self::print_debug_render_product( $product_id );

  }

  private static function print_debug_render_product( $product_id ) {

    if( empty( $product_id ) ) {

      return false;

    }

    $product_id = intval( $product_id );

    $wc_product = wc_get_product( $product_id );

    if( empty( $wc_product ) or ! is_object( $wc_product ) ) {

      return false;

    }

    $debug_lists = array();

    if( method_exists( $wc_product , 'get_id' ) ) {

        $debug_lists['$wc_product->get_id()'] = $wc_product->get_id();

    }

    if( method_exists( $wc_product , 'get_title' ) ) {

        $debug_lists['$wc_product->get_title()'] = $wc_product->get_title();

    }

    if( method_exists( $wc_product , 'get_name' ) ) {

        $debug_lists['$wc_product->get_name()'] = $wc_product->get_name();

    }

    if( method_exists( $wc_product , 'get_formatted_name' ) ) {

        $debug_lists['$wc_product->get_formatted_name()'] = $wc_product->get_formatted_name();

    }

    if( method_exists( $wc_product , 'get_type' ) ) {

        $debug_lists['$wc_product->get_type()'] = $wc_product->get_type();

    }

    if( method_exists( $wc_product , 'get_description' ) ) {

        $debug_lists['$wc_product->get_description()'] = $wc_product->get_description();

    }

    if( method_exists( $wc_product , 'get_short_description' ) ) {

        $debug_lists['$wc_product->get_short_description()'] = $wc_product->get_short_description();

    }

    if( method_exists( $wc_product , 'get_slug' ) ) {

        $debug_lists['$wc_product->get_slug()'] = $wc_product->get_slug();

    }

    if( method_exists( $wc_product , 'get_regular_price' ) ) {

        $debug_lists['$wc_product->get_regular_price()'] = $wc_product->get_regular_price();

    }

    if( method_exists( $wc_product , 'get_sale_price' ) ) {

        $debug_lists['$wc_product->get_sale_price()'] = $wc_product->get_sale_price();

    }

    if( method_exists( $wc_product , 'get_price_suffix' ) ) {

        $debug_lists['$wc_product->get_price_suffix()'] = $wc_product->get_price_suffix();

    }

    if( method_exists( $wc_product , 'get_price_html' ) ) {

        $debug_lists['$wc_product->get_price_html()'] = $wc_product->get_price_html();

    }

    if( method_exists( $wc_product , 'get_total_sales' ) ) {

        $debug_lists['$wc_product->get_total_sales()'] = $wc_product->get_total_sales();

    }

    if( method_exists( $wc_product , 'get_status' ) ) {

        $debug_lists['$wc_product->get_status()'] = $wc_product->get_status();

    }

    if( method_exists( $wc_product , 'is_visible' ) ) {

        $debug_lists['$wc_product->is_visible()'] = $wc_product->is_visible();

    }

    if( method_exists( $wc_product , 'is_on_sale' ) ) {

        $debug_lists['$wc_product->is_on_sale()'] = $wc_product->is_on_sale();

    }

    if( method_exists( $wc_product , 'is_purchasable' ) ) {

        $debug_lists['$wc_product->is_purchasable()'] = $wc_product->is_purchasable();

    }

    if( method_exists( $wc_product , 'get_catalog_visibility' ) ) {

        $debug_lists['$wc_product->get_catalog_visibility()'] = $wc_product->get_catalog_visibility();

    }

    if( method_exists( $wc_product , 'get_tax_status' ) ) {

        $debug_lists['$wc_product->get_tax_status()'] = $wc_product->get_tax_status();

    }

    if( method_exists( $wc_product , 'get_tax_class' ) ) {

        $debug_lists['$wc_product->get_tax_class()'] = $wc_product->get_tax_class();

    }

    if( method_exists( $wc_product , 'is_taxable' ) ) {

        $debug_lists['$wc_product->is_taxable()'] = $wc_product->is_taxable();

    }

    if( method_exists( $wc_product , 'get_shipping_class' ) ) {

        $debug_lists['$wc_product->get_shipping_class()'] = $wc_product->get_shipping_class();

    }

    if( method_exists( $wc_product , 'get_shipping_class_id' ) ) {

        $debug_lists['$wc_product->get_shipping_class_id()'] = $wc_product->get_shipping_class_id();

    }

    if( method_exists( $wc_product , 'is_shipping_taxable' ) ) {

        $debug_lists['$wc_product->is_shipping_taxable()'] = $wc_product->is_shipping_taxable();

    }

    if( method_exists( $wc_product , 'is_in_stock' ) ) {

        $debug_lists['$wc_product->is_in_stock()'] = $wc_product->is_in_stock();

    }

    if( method_exists( $wc_product , 'get_manage_stock' ) ) {

        $debug_lists['$wc_product->get_manage_stock()'] = $wc_product->get_manage_stock();

    }

    if( method_exists( $wc_product , 'managing_stock' ) ) {

        $debug_lists['$wc_product->managing_stock()'] = $wc_product->managing_stock();

    }

    if( method_exists( $wc_product , 'get_stock_quantity' ) ) {

        $debug_lists['$wc_product->get_stock_quantity()'] = $wc_product->get_stock_quantity();

    }

    if( method_exists( $wc_product , 'get_stock_status' ) ) {

        $debug_lists['$wc_product->get_stock_status()'] = $wc_product->get_stock_status();

    }

    if( method_exists( $wc_product , 'get_low_stock_amount' ) ) {

        $debug_lists['$wc_product->get_low_stock_amount()'] = $wc_product->get_low_stock_amount();

    }

    if( method_exists( $wc_product , 'get_menu_order' ) ) {

        $debug_lists['$wc_product->get_menu_order()'] = $wc_product->get_menu_order();

    }

    if( method_exists( $wc_product , 'get_image' ) ) {

        $debug_lists['$wc_product->get_image()'] = $wc_product->get_image();

    }

    if( method_exists( $wc_product , 'get_image_id' ) ) {

        $debug_lists['$wc_product->get_image_id()'] = $wc_product->get_image_id();

    }

    if( method_exists( $wc_product , 'get_gallery_image_ids' ) ) {

        $debug_lists['$wc_product->get_gallery_image_ids()'] = $wc_product->get_gallery_image_ids();

    }

    if( method_exists( $wc_product , 'get_permalink' ) ) {

        $debug_lists['$wc_product->get_permalink()'] = $wc_product->get_permalink();

    }

    if( method_exists( $wc_product , 'get_category_ids' ) ) {

        $debug_lists['$wc_product->get_category_ids()'] = $wc_product->get_category_ids();

    }

    if( method_exists( $wc_product , 'get_tag_ids' ) ) {

        $debug_lists['$wc_product->get_tag_ids()'] = $wc_product->get_tag_ids();

    }

    if( method_exists( $wc_product , 'get_attributes' ) ) {

        $debug_lists['$wc_product->get_attributes()'] = $wc_product->get_attributes();

    }

    if( method_exists( $wc_product , 'get_parent_id' ) ) {

        $debug_lists['$wc_product->get_parent_id()'] = $wc_product->get_parent_id();

    }

    if( method_exists( $wc_product , 'get_children' ) ) {

        $debug_lists['$wc_product->get_children()'] = $wc_product->get_children();

    }

    if( method_exists( $wc_product , 'get_min_purchase_quantity' ) ) {

        $debug_lists['$wc_product->get_min_purchase_quantity()'] = $wc_product->get_min_purchase_quantity();

    }

    if( method_exists( $wc_product , 'get_max_purchase_quantity' ) ) {

        $debug_lists['$wc_product->get_max_purchase_quantity()'] = $wc_product->get_max_purchase_quantity();

    }

    if( method_exists( $wc_product , 'get_reviews_allowed' ) ) {

        $debug_lists['$wc_product->get_reviews_allowed()'] = $wc_product->get_reviews_allowed();

    }

    if( method_exists( $wc_product , 'get_rating_counts' ) ) {

        $debug_lists['$wc_product->get_rating_counts()'] = $wc_product->get_rating_counts();

    }

    if( method_exists( $wc_product , 'get_average_rating' ) ) {

        $debug_lists['$wc_product->get_average_rating()'] = $wc_product->get_average_rating();

    }

    if( method_exists( $wc_product , 'get_review_count' ) ) {

        $debug_lists['$wc_product->get_review_count()'] = $wc_product->get_review_count();

    }

    printf( '<p>%s</p>' , '$wc_product = wc_get_product( ' . $product_id . ' );' );

    echo '<table class="debug-table">';

    foreach( $debug_lists as $key => $val ) {

      echo '<tr>';

      printf( '<th>%s</th>' , $key );

      echo '<td>';

      if( is_array( $val ) or is_object( $val ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $val , 'esc_html' ) , true ) );

      } else {

        echo esc_html( $val );

      }

      echo '</td>';

    }

    echo '</table>';

  }

  public static function mywp_debug_render_shop_order() {

    global $wp;
    global $post;

    $shop_order_id = false;

    if( is_admin() ) {

      if( ! is_object( $post ) or empty( $post->ID ) ) {

        return false;

      }

      $shop_order_id = $post->ID;

    } else {

      if( ! empty( $wp->query_vars['view-order'] ) ) {

        $shop_order_id = $wp->query_vars['view-order'];

      }

    }

    self::print_debug_render_shop_order( $shop_order_id );

  }

  private static function print_debug_render_shop_order( $shop_order_id ) {

    if( empty( $shop_order_id ) ) {

      return false;

    }

    $shop_order_id = intval( $shop_order_id );

    $wc_order = wc_get_order( $shop_order_id );

    if( empty( $wc_order ) or ! is_object( $wc_order ) ) {

      return false;

    }

    $debug_lists = array();

    if( method_exists( $wc_order , 'get_id' ) ) {

        $debug_lists['$wc_order->get_id()'] = $wc_order->get_id();

    }

    if( method_exists( $wc_order , 'get_order_number' ) ) {

        $debug_lists['$wc_order->get_order_number()'] = $wc_order->get_order_number();

    }

    if( method_exists( $wc_order , 'get_order_key' ) ) {

        $debug_lists['$wc_order->get_order_key()'] = $wc_order->get_order_key();

    }

    if( method_exists( $wc_order , 'get_customer_id' ) ) {

        $debug_lists['$wc_order->get_customer_id()'] = $wc_order->get_customer_id();

    }

    if( method_exists( $wc_order , 'get_type' ) ) {

        $debug_lists['$wc_order->get_type()'] = $wc_order->get_type();

    }

    if( method_exists( $wc_order , 'get_total' ) ) {

        $debug_lists['$wc_order->get_total()'] = $wc_order->get_total();

    }

    if( method_exists( $wc_order , 'get_formatted_order_total' ) ) {

        $debug_lists['$wc_order->get_formatted_order_total()'] = $wc_order->get_formatted_order_total();

    }

    if( method_exists( $wc_order , 'get_subtotal' ) ) {

        $debug_lists['$wc_order->get_subtotal()'] = $wc_order->get_subtotal();

    }

    if( method_exists( $wc_order , 'get_subtotal_to_display' ) ) {

        $debug_lists['$wc_order->get_subtotal_to_display()'] = $wc_order->get_subtotal_to_display();

    }

    if( method_exists( $wc_order , 'get_status' ) ) {

        $debug_lists['$wc_order->get_status()'] = $wc_order->get_status();

    }

    if( method_exists( $wc_order , 'get_currency' ) ) {

        $debug_lists['$wc_order->get_currency()'] = $wc_order->get_currency();

    }

    if( method_exists( $wc_order , 'get_version' ) ) {

        $debug_lists['$wc_order->get_version()'] = $wc_order->get_version();

    }

    if( method_exists( $wc_order , 'get_parent_id' ) ) {

        $debug_lists['$wc_order->get_parent_id()'] = $wc_order->get_parent_id();

    }

    if( method_exists( $wc_order , 'get_item_count' ) ) {

        $debug_lists['$wc_order->get_item_count()'] = $wc_order->get_item_count();

    }

    if( method_exists( $wc_order , 'get_items' ) ) {

        $debug_lists['$wc_order->get_items()'] = $wc_order->get_items();

    }

    if( method_exists( $wc_order , 'get_taxes' ) ) {

        $debug_lists['$wc_order->get_taxes()'] = $wc_order->get_taxes();

    }

    if( method_exists( $wc_order , 'get_items_tax_classes' ) ) {

        $debug_lists['$wc_order->get_items_tax_classes()'] = $wc_order->get_items_tax_classes();

    }

    if( method_exists( $wc_order , 'get_prices_include_tax' ) ) {

        $debug_lists['$wc_order->get_prices_include_tax()'] = $wc_order->get_prices_include_tax();

    }

    if( method_exists( $wc_order , 'get_discount_tax' ) ) {

        $debug_lists['$wc_order->get_discount_tax()'] = $wc_order->get_discount_tax();

    }

    if( method_exists( $wc_order , 'get_cart_tax' ) ) {

        $debug_lists['$wc_order->get_cart_tax()'] = $wc_order->get_cart_tax();

    }

    if( method_exists( $wc_order , 'get_total_tax' ) ) {

        $debug_lists['$wc_order->get_total_tax()'] = $wc_order->get_total_tax();

    }

    if( method_exists( $wc_order , 'get_tax_totals' ) ) {

        $debug_lists['$wc_order->get_tax_totals()'] = $wc_order->get_tax_totals();

    }

    if( method_exists( $wc_order , 'get_coupons' ) ) {

        $debug_lists['$wc_order->get_coupons()'] = $wc_order->get_coupons();

    }

    if( method_exists( $wc_order , 'get_coupon_codes' ) ) {

        $debug_lists['$wc_order->get_coupon_codes()'] = $wc_order->get_coupon_codes();

    }

    if( method_exists( $wc_order , 'get_shipping_method' ) ) {

        $debug_lists['$wc_order->get_shipping_method()'] = $wc_order->get_shipping_method();

    }

    if( method_exists( $wc_order , 'get_shipping_methods' ) ) {

        $debug_lists['$wc_order->get_shipping_methods()'] = $wc_order->get_shipping_methods();

    }

    if( method_exists( $wc_order , 'get_shipping_total' ) ) {

        $debug_lists['$wc_order->get_shipping_total()'] = $wc_order->get_shipping_total();

    }

    if( method_exists( $wc_order , 'get_shipping_tax' ) ) {

        $debug_lists['$wc_order->get_shipping_tax()'] = $wc_order->get_shipping_tax();

    }

    if( method_exists( $wc_order , 'get_shipping_to_display' ) ) {

        $debug_lists['$wc_order->get_shipping_to_display()'] = $wc_order->get_shipping_to_display();

    }

    if( method_exists( $wc_order , 'get_total_fees' ) ) {

        $debug_lists['$wc_order->get_total_fees()'] = $wc_order->get_total_fees();

    }

    if( method_exists( $wc_order , 'get_fees' ) ) {

        $debug_lists['$wc_order->get_fees()'] = $wc_order->get_fees();

    }

    if( method_exists( $wc_order , 'get_discount_total' ) ) {

        $debug_lists['$wc_order->get_discount_total()'] = $wc_order->get_discount_total();

    }

    if( method_exists( $wc_order , 'get_discount_to_display' ) ) {

        $debug_lists['$wc_order->get_discount_to_display()'] = $wc_order->get_discount_to_display();

    }

    if( method_exists( $wc_order , 'get_total_discount' ) ) {

        $debug_lists['$wc_order->get_total_discount()'] = $wc_order->get_total_discount();

    }

    if( method_exists( $wc_order , 'has_free_item' ) ) {

        $debug_lists['$wc_order->has_free_item()'] = $wc_order->has_free_item();

    }

    if( method_exists( $wc_order , 'get_meta_data' ) ) {

        $debug_lists['$wc_order->get_meta_data()'] = $wc_order->get_meta_data();

    }

    if( method_exists( $wc_order , 'get_base_data' ) ) {

        $debug_lists['$wc_order->get_base_data()'] = $wc_order->get_base_data();

    }

    if( method_exists( $wc_order , 'get_data' ) ) {

        $debug_lists['$wc_order->get_data()'] = $wc_order->get_data();

    }

    if( method_exists( $wc_order , 'get_view_order_url' ) ) {

        $debug_lists['$wc_order->get_view_order_url()'] = $wc_order->get_view_order_url();

    }

    if( method_exists( $wc_order , 'get_refunds' ) ) {

        $debug_lists['$wc_order->get_refunds()'] = $wc_order->get_refunds();

    }

    if( method_exists( $wc_order , 'get_total_refunded' ) ) {

        $debug_lists['$wc_order->get_total_refunded()'] = $wc_order->get_total_refunded();

    }

    if( method_exists( $wc_order , 'get_total_tax_refunded' ) ) {

        $debug_lists['$wc_order->get_total_tax_refunded()'] = $wc_order->get_total_tax_refunded();

    }

    if( method_exists( $wc_order , 'get_total_shipping_refunded' ) ) {

        $debug_lists['$wc_order->get_total_shipping_refunded()'] = $wc_order->get_total_shipping_refunded();

    }

    if( method_exists( $wc_order , 'get_item_count_refunded' ) ) {

        $debug_lists['$wc_order->get_item_count_refunded()'] = $wc_order->get_item_count_refunded();

    }

    if( method_exists( $wc_order , 'get_total_qty_refunded' ) ) {

        $debug_lists['$wc_order->get_total_qty_refunded()'] = $wc_order->get_total_qty_refunded();

    }

    printf( '<p>%s</p>' , '$wc_order = wc_get_order( ' . $shop_order_id . ' );' );

    echo '<table class="debug-table">';

    foreach( $debug_lists as $key => $val ) {

      echo '<tr>';

      printf( '<th>%s</th>' , $key );

      echo '<td>';

      if( is_array( $val ) or is_object( $val ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $val , 'esc_html' ) , true ) );

      } else {

        echo esc_html( $val );

      }

      echo '</td>';

    }

    echo '</table>';

    echo '<p>Order Items</p>';

    echo '<p>&nbsp;</p>';

    if( method_exists( $wc_order , 'get_items' ) ) {

      $order_items = $wc_order->get_items();

      foreach( $order_items as $order_item_id => $order_item ) {

        printf( '<p>%s</p>' , 'Order Item ID: ' . $order_item_id . '' );

        $debug_lists = array();

        if( method_exists( $order_item , 'get_order_id' ) ) {

            $debug_lists['$order_item->get_order_id()'] = $order_item->get_order_id();

        }

        if( method_exists( $order_item , 'get_name' ) ) {

            $debug_lists['$order_item->get_name()'] = $order_item->get_name();

        }

        if( method_exists( $order_item , 'get_type' ) ) {

            $debug_lists['$order_item->get_type()'] = $order_item->get_type();

        }

        if( method_exists( $order_item , 'get_product_id' ) ) {

            $debug_lists['$order_item->get_product_id()'] = $order_item->get_product_id();

        }

        if( method_exists( $order_item , 'get_variation_id' ) ) {

            $debug_lists['$order_item->get_variation_id()'] = $order_item->get_variation_id();

        }

        if( method_exists( $order_item , 'get_quantity' ) ) {

            $debug_lists['$order_item->get_quantity()'] = $order_item->get_quantity();

        }

        if( method_exists( $order_item , 'get_tax_class' ) ) {

            $debug_lists['$order_item->get_tax_class()'] = $order_item->get_tax_class();

        }

        if( method_exists( $order_item , 'get_subtotal' ) ) {

            $debug_lists['$order_item->get_subtotal()'] = $order_item->get_subtotal();

        }

        if( method_exists( $order_item , 'get_subtotal_tax' ) ) {

            $debug_lists['$order_item->get_subtotal_tax()'] = $order_item->get_subtotal_tax();

        }

        if( method_exists( $order_item , 'get_total' ) ) {

            $debug_lists['$order_item->get_total()'] = $order_item->get_total();

        }

        if( method_exists( $order_item , 'get_total_tax' ) ) {

            $debug_lists['$order_item->get_total_tax()'] = $order_item->get_total_tax();

        }

        if( method_exists( $order_item , 'get_taxes' ) ) {

            $debug_lists['$order_item->get_taxes()'] = $order_item->get_taxes();

        }

        if( method_exists( $order_item , 'get_product' ) ) {

            $debug_lists['$order_item->get_product()'] = $order_item->get_product();

        }

        if( method_exists( $order_item , 'get_tax_status' ) ) {

            $debug_lists['$order_item->get_tax_status()'] = $order_item->get_tax_status();

        }

        if( method_exists( $order_item , 'get_formatted_meta_data' ) ) {

            $debug_lists['$order_item->get_formatted_meta_data()'] = $order_item->get_formatted_meta_data();

        }

        if( method_exists( $order_item , 'get_meta_data' ) ) {

            $debug_lists['$order_item->get_meta_data()'] = $order_item->get_meta_data();

        }

        echo '<table class="debug-table">';

        foreach( $debug_lists as $key => $val ) {

          echo '<tr>';

          printf( '<th>%s</th>' , $key );

          echo '<td>';

          if( is_array( $val ) or is_object( $val ) ) {

            printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $val , 'esc_html' ) , true ) );

          } else {

            echo esc_html( $val );

          }

          echo '</td>';

        }

        echo '</table>';

      }

    }

  }

  public static function mywp_debug_render_customer() {

    if( ! is_object( WC()->customer ) ) {

      return false;

    }

    $wc_customer = WC()->customer;

    $debug_lists = array();

    if( method_exists( $wc_customer , 'get_id' ) ) {

        $debug_lists['$wc_customer->get_id()'] = $wc_customer->get_id();

    }

    if( method_exists( $wc_customer , 'get_avatar_url' ) ) {

        $debug_lists['$wc_customer->get_avatar_url()'] = $wc_customer->get_avatar_url();

    }

    if( method_exists( $wc_customer , 'get_taxable_address' ) ) {

        $debug_lists['$wc_customer->get_taxable_address()'] = $wc_customer->get_taxable_address();

    }

    if( method_exists( $wc_customer , 'get_downloadable_products' ) ) {

        $debug_lists['$wc_customer->get_downloadable_products()'] = $wc_customer->get_downloadable_products();

    }

    if( method_exists( $wc_customer , 'get_is_vat_exempt' ) ) {

        $debug_lists['$wc_customer->get_is_vat_exempt()'] = $wc_customer->get_is_vat_exempt();

    }

    if( method_exists( $wc_customer , 'get_calculated_shipping' ) ) {

        $debug_lists['$wc_customer->get_calculated_shipping()'] = $wc_customer->get_calculated_shipping();

    }

    if( method_exists( $wc_customer , 'get_last_order' ) ) {

        $debug_lists['$wc_customer->get_last_order()'] = $wc_customer->get_last_order();

    }

    if( method_exists( $wc_customer , 'get_order_count' ) ) {

        $debug_lists['$wc_customer->get_order_count()'] = $wc_customer->get_order_count();

    }

    if( method_exists( $wc_customer , 'get_total_spent' ) ) {

        $debug_lists['$wc_customer->get_total_spent()'] = $wc_customer->get_total_spent();

    }

    if( method_exists( $wc_customer , 'get_username' ) ) {

        $debug_lists['$wc_customer->get_username()'] = $wc_customer->get_username();

    }

    if( method_exists( $wc_customer , 'get_email' ) ) {

        $debug_lists['$wc_customer->get_email()'] = $wc_customer->get_email();

    }

    if( method_exists( $wc_customer , 'get_first_name' ) ) {

        $debug_lists['$wc_customer->get_first_name()'] = $wc_customer->get_first_name();

    }

    if( method_exists( $wc_customer , 'get_last_name' ) ) {

        $debug_lists['$wc_customer->get_last_name()'] = $wc_customer->get_last_name();

    }

    if( method_exists( $wc_customer , 'get_display_name' ) ) {

        $debug_lists['$wc_customer->get_display_name()'] = $wc_customer->get_display_name();

    }

    if( method_exists( $wc_customer , 'get_role' ) ) {

        $debug_lists['$wc_customer->get_role()'] = $wc_customer->get_role();

    }

    if( method_exists( $wc_customer , 'get_date_created' ) ) {

        $debug_lists['$wc_customer->get_date_created()'] = $wc_customer->get_date_created();

    }

    if( method_exists( $wc_customer , 'get_date_modified' ) ) {

        $debug_lists['$wc_customer->get_date_modified()'] = $wc_customer->get_date_modified();

    }

    if( method_exists( $wc_customer , 'get_billing' ) ) {

        $debug_lists['$wc_customer->get_billing()'] = $wc_customer->get_billing();

    }

    if( method_exists( $wc_customer , 'get_shipping' ) ) {

        $debug_lists['$wc_customer->get_shipping()'] = $wc_customer->get_shipping();

    }

    if( method_exists( $wc_customer , 'get_is_paying_customer' ) ) {

        $debug_lists['$wc_customer->get_is_paying_customer()'] = $wc_customer->get_is_paying_customer();

    }

    if( method_exists( $wc_customer , 'get_data_store' ) ) {

        $debug_lists['$wc_customer->get_data_store()'] = $wc_customer->get_data_store();

    }

    if( method_exists( $wc_customer , 'get_data' ) ) {

        $debug_lists['$wc_customer->get_data()'] = $wc_customer->get_data();

    }

    if( method_exists( $wc_customer , 'get_data_keys' ) ) {

        $debug_lists['$wc_customer->get_data_keys()'] = $wc_customer->get_data_keys();

    }

    if( method_exists( $wc_customer , 'get_extra_data_keys' ) ) {

        $debug_lists['$wc_customer->get_extra_data_keys()'] = $wc_customer->get_extra_data_keys();

    }

    if( method_exists( $wc_customer , 'get_meta_data' ) ) {

        $debug_lists['$wc_customer->get_meta_data()'] = $wc_customer->get_meta_data();

    }

    if( method_exists( $wc_customer , 'get_object_read' ) ) {

        $debug_lists['$wc_customer->get_object_read()'] = $wc_customer->get_object_read();

    }

    if( method_exists( $wc_customer , 'get_changes' ) ) {

        $debug_lists['$wc_customer->get_changes()'] = $wc_customer->get_changes();

    }

    printf( '<p>%s</p>' , '$wc_customer = WC()->customer' );

    echo '<table class="debug-table">';

    foreach( $debug_lists as $key => $val ) {

      echo '<tr>';

      printf( '<th>%s</th>' , $key );

      echo '<td>';

      if( is_array( $val ) or is_object( $val ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $val , 'esc_html' ) , true ) );

      } else {

        echo $val;

      }

      echo '</td>';

    }

    echo '</table>';

  }

  public static function mywp_debug_render_cart() {

    if( ! is_object( WC()->cart ) ) {

      return false;

    }

    $wc_cart = WC()->cart;

    $debug_lists = array();

    if( method_exists( $wc_cart , 'get_totals' ) ) {

        $debug_lists['$wc_cart->get_totals()'] = $wc_cart->get_totals();

    }

    if( method_exists( $wc_cart , 'get_total' ) ) {

        $debug_lists['$wc_cart->get_total()'] = $wc_cart->get_total();

    }

    if( method_exists( $wc_cart , 'get_total_tax' ) ) {

        $debug_lists['$wc_cart->get_total_tax()'] = $wc_cart->get_total_tax();

    }

    if( method_exists( $wc_cart , 'get_subtotal' ) ) {

        $debug_lists['$wc_cart->get_subtotal()'] = $wc_cart->get_subtotal();

    }

    if( method_exists( $wc_cart , 'get_subtotal_tax' ) ) {

        $debug_lists['$wc_cart->get_subtotal_tax()'] = $wc_cart->get_subtotal_tax();

    }

    if( method_exists( $wc_cart , 'get_cart_contents_total' ) ) {

        $debug_lists['$wc_cart->get_cart_contents_total()'] = $wc_cart->get_cart_contents_total();

    }

    if( method_exists( $wc_cart , 'get_total_ex_tax' ) ) {

        $debug_lists['$wc_cart->get_total_ex_tax()'] = $wc_cart->get_total_ex_tax();

    }

    if( method_exists( $wc_cart , 'get_cart_total' ) ) {

        $debug_lists['$wc_cart->get_cart_total()'] = $wc_cart->get_cart_total();

    }

    if( method_exists( $wc_cart , 'get_cart_subtotal' ) ) {

        $debug_lists['$wc_cart->get_cart_subtotal()'] = $wc_cart->get_cart_subtotal();

    }

    if( method_exists( $wc_cart , 'get_discount_total' ) ) {

        $debug_lists['$wc_cart->get_discount_total()'] = $wc_cart->get_discount_total();

    }

    if( method_exists( $wc_cart , 'get_discount_tax' ) ) {

        $debug_lists['$wc_cart->get_discount_tax()'] = $wc_cart->get_discount_tax();

    }

    if( method_exists( $wc_cart , 'get_total_discount' ) ) {

        $debug_lists['$wc_cart->get_total_discount()'] = $wc_cart->get_total_discount();

    }

    if( method_exists( $wc_cart , 'get_cart_tax' ) ) {

        $debug_lists['$wc_cart->get_cart_tax()'] = $wc_cart->get_cart_tax();

    }

    if( method_exists( $wc_cart , 'get_cart_contents_tax' ) ) {

        $debug_lists['$wc_cart->get_cart_contents_tax()'] = $wc_cart->get_cart_contents_tax();

    }

    if( method_exists( $wc_cart , 'get_cart_contents_taxes' ) ) {

        $debug_lists['$wc_cart->get_cart_contents_taxes()'] = $wc_cart->get_cart_contents_taxes();

    }

    if( method_exists( $wc_cart , 'get_taxes' ) ) {

        $debug_lists['$wc_cart->get_taxes()'] = $wc_cart->get_taxes();

    }

    if( method_exists( $wc_cart , 'get_tax_totals' ) ) {

        $debug_lists['$wc_cart->get_tax_totals()'] = $wc_cart->get_tax_totals();

    }

    if( method_exists( $wc_cart , 'get_cart_item_tax_classes' ) ) {

        $debug_lists['$wc_cart->get_cart_item_tax_classes()'] = $wc_cart->get_cart_item_tax_classes();

    }

    if( method_exists( $wc_cart , 'get_shipping_total' ) ) {

        $debug_lists['$wc_cart->get_shipping_total()'] = $wc_cart->get_shipping_total();

    }

    if( method_exists( $wc_cart , 'get_shipping_tax' ) ) {

        $debug_lists['$wc_cart->get_shipping_tax()'] = $wc_cart->get_shipping_tax();

    }

    if( method_exists( $wc_cart , 'get_shipping_taxes' ) ) {

        $debug_lists['$wc_cart->get_shipping_taxes()'] = $wc_cart->get_shipping_taxes();

    }

    if( method_exists( $wc_cart , 'get_cart_item_tax_classes_for_shipping' ) ) {

        $debug_lists['$wc_cart->get_cart_item_tax_classes_for_shipping()'] = $wc_cart->get_cart_item_tax_classes_for_shipping();

    }

    if( method_exists( $wc_cart , 'get_shipping_packages' ) ) {

        $debug_lists['$wc_cart->get_shipping_packages()'] = $wc_cart->get_shipping_packages();

    }

    if( method_exists( $wc_cart , 'needs_shipping' ) ) {

        $debug_lists['$wc_cart->needs_shipping()'] = $wc_cart->needs_shipping();

    }

    if( method_exists( $wc_cart , 'needs_shipping_address' ) ) {

        $debug_lists['$wc_cart->needs_shipping_address()'] = $wc_cart->needs_shipping_address();

    }

    if( method_exists( $wc_cart , 'show_shipping' ) ) {

        $debug_lists['$wc_cart->show_shipping()'] = $wc_cart->show_shipping();

    }

    if( method_exists( $wc_cart , 'get_cart_shipping_total' ) ) {

        $debug_lists['$wc_cart->get_cart_shipping_total()'] = $wc_cart->get_cart_shipping_total();

    }

    if( method_exists( $wc_cart , 'get_fee_total' ) ) {

        $debug_lists['$wc_cart->get_fee_total()'] = $wc_cart->get_fee_total();

    }

    if( method_exists( $wc_cart , 'get_fee_tax' ) ) {

        $debug_lists['$wc_cart->get_fee_tax()'] = $wc_cart->get_fee_tax();

    }

    if( method_exists( $wc_cart , 'get_fee_taxes' ) ) {

        $debug_lists['$wc_cart->get_fee_taxes()'] = $wc_cart->get_fee_taxes();

    }

    if( method_exists( $wc_cart , 'get_fees' ) ) {

        $debug_lists['$wc_cart->get_fees()'] = $wc_cart->get_fees();

    }

    if( method_exists( $wc_cart , 'display_prices_including_tax' ) ) {

        $debug_lists['$wc_cart->display_prices_including_tax()'] = $wc_cart->display_prices_including_tax();

    }

    if( method_exists( $wc_cart , 'get_cart_contents_count' ) ) {

        $debug_lists['$wc_cart->get_cart_contents_count()'] = $wc_cart->get_cart_contents_count();

    }

    if( method_exists( $wc_cart , 'get_cart_item_quantities' ) ) {

        $debug_lists['$wc_cart->get_cart_item_quantities()'] = $wc_cart->get_cart_item_quantities();

    }

    if( method_exists( $wc_cart , 'check_cart_items' ) ) {

        $debug_lists['$wc_cart->check_cart_items()'] = $wc_cart->check_cart_items();

    }

    if( method_exists( $wc_cart , 'check_cart_item_validity' ) ) {

        $debug_lists['$wc_cart->check_cart_item_validity()'] = $wc_cart->check_cart_item_validity();

    }

    if( method_exists( $wc_cart , 'check_cart_item_stock' ) ) {

        $debug_lists['$wc_cart->check_cart_item_stock()'] = $wc_cart->check_cart_item_stock();

    }

    if( method_exists( $wc_cart , 'get_displayed_subtotal' ) ) {

        $debug_lists['$wc_cart->get_displayed_subtotal()'] = $wc_cart->get_displayed_subtotal();

    }

    if( method_exists( $wc_cart , 'get_customer' ) ) {

        $debug_lists['$wc_cart->get_customer()'] = $wc_cart->get_customer();

    }

    if( method_exists( $wc_cart , 'needs_payment' ) ) {

        $debug_lists['$wc_cart->needs_payment()'] = $wc_cart->needs_payment();

    }

    if( method_exists( $wc_cart , 'get_cart_hash' ) ) {

        $debug_lists['$wc_cart->get_cart_hash()'] = $wc_cart->get_cart_hash();

    }

    if( method_exists( $wc_cart , 'get_cart_contents' ) ) {

        $debug_lists['$wc_cart->get_cart_contents()'] = $wc_cart->get_cart_contents();

    }

    printf( '<p>%s</p>' , '$wc_cart = WC()->cart' );

    echo '<table class="debug-table">';

    foreach( $debug_lists as $key => $val ) {

      echo '<tr>';

      printf( '<th>%s</th>' , $key );

      echo '<td>';

      if( is_array( $val ) or is_object( $val ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $val , 'esc_html' ) , true ) );

      } else {

        echo $val;

      }

      echo '</td>';

    }

    echo '</table>';

  }

  public static function mywp_debug_render_checkout() {

    if( ! is_object( WC()->checkout ) ) {

      return false;

    }

    $wc_checkout = WC()->checkout;

    $debug_lists = array();

    if( method_exists( $wc_checkout , 'get_checkout_fields' ) ) {

        $debug_lists['$wc_checkout->get_checkout_fields()'] = $wc_checkout->get_checkout_fields();

    }

    printf( '<p>%s</p>' , '$wc_checkout = WC()->wc_checkout' );

    echo '<table class="debug-table">';

    foreach( $debug_lists as $key => $val ) {

      echo '<tr>';

      printf( '<th>%s</th>' , $key );

      echo '<td>';

      if( is_array( $val ) or is_object( $val ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $val , 'esc_html' ) , true ) );

      } else {

        echo $val;

      }

      echo '</td>';

    }

    echo '</table>';

  }

  public static function mywp_debug_render_session() {

    if( ! is_object( WC()->session ) ) {

      return false;

    }

    $wc_session_handler = WC()->session;

    $debug_lists = array();

    if( method_exists( $wc_session_handler , 'get_customer_id' ) ) {

        $debug_lists['$wc_session_handler->get_customer_id()'] = $wc_session_handler->get_customer_id();

    }

    if( method_exists( $wc_session_handler , 'get_customer_unique_id' ) ) {

        $debug_lists['$wc_session_handler->get_customer_unique_id()'] = $wc_session_handler->get_customer_unique_id();

    }

    if( method_exists( $wc_session_handler , 'get_session_cookie' ) ) {

        $debug_lists['$wc_session_handler->get_session_cookie()'] = $wc_session_handler->get_session_cookie();

    }

    if( method_exists( $wc_session_handler , 'get_session_data' ) ) {

        $debug_lists['$wc_session_handler->get_session_data()'] = $wc_session_handler->get_session_data();

    }

    printf( '<p>%s</p>' , '$wc_session_handler = WC()->session' );

    echo '<table class="debug-table">';

    foreach( $debug_lists as $key => $val ) {

      echo '<tr>';

      printf( '<th>%s</th>' , $key );

      echo '<td>';

      if( is_array( $val ) or is_object( $val ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $val , 'esc_html' ) , true ) );

      } else {

        echo $val;

      }

      echo '</td>';

    }

    echo '</table>';

    $wc_session_data = $wc_session_handler->get_session_data();

    $debug_lists = array();

    if( ! empty( $wc_session_data ) ) {

      foreach( $wc_session_data as $session_data_key => $session_data_val ) {

        $debug_lists['WC()->session->get( "' . $session_data_key . '" )'] = WC()->session->get( $session_data_key );

      }

    }

    echo '<table class="debug-table">';

    foreach( $debug_lists as $key => $val ) {

      echo '<tr>';

      printf( '<th>%s</th>' , $key );

      echo '<td>';

      if( is_array( $val ) or is_object( $val ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $val , 'esc_html' ) , true ) );

      } else {

        echo $val;

      }

      echo '</td>';

    }

    echo '</table>';

  }

}

MywpDeveloperModuleDevWooCommerce::init();

endif;
