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

    add_action( 'load-woocommerce_page_wc-orders' , array( __CLASS__ , 'admin_load_wc_orders' ) );

  }

  public static function mywp_request_frontend() {

    add_action( 'wp' , array( __CLASS__ , 'wp' ) );

  }

  public static function wp() {

    if( is_product() ) {

      add_action( 'mywp_debug_render_' . self::$id . '_product' , array( __CLASS__ , 'mywp_debug_render_product' ) );

    } elseif( is_account_page() && is_wc_endpoint_url( 'view-order' ) ) {

      add_action( 'mywp_debug_render_' . self::$id . '_order' , array( __CLASS__ , 'mywp_debug_render_order' ) );

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

      add_action( 'mywp_debug_render_' . self::$id . '_order' , array( __CLASS__ , 'mywp_debug_render_order' ) );

    }

  }

  public static function admin_load_wc_orders() {

    add_action( 'mywp_debug_render_' . self::$id . '_order' , array( __CLASS__ , 'mywp_debug_render_admin_wc_orders' ) );

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

    $WC_Product = wc_get_product( $product_id );

    if( empty( $WC_Product ) or ! is_object( $WC_Product ) ) {

      return false;

    }

    printf( '<p>%s</p>' , '$WC_Product = wc_get_product( ' . $product_id . ' );' );

    echo '<table class="debug-table">';

    $method_lists = array(
      'get_type',
      'add_to_cart_url',
      'add_to_cart_text',
      'add_to_cart_description',
      'add_to_cart_success_message',

      'is_purchasable',

      'single_add_to_cart_text',

      'add_to_cart_aria_describedby',
      'get_variation_prices',
      'get_variation_regular_price',
      'get_variation_sale_price',
      'get_variation_price',
      'get_price_html',
      'get_price_suffix',
      'get_visible_children',
      'get_variation_attributes',
      'get_available_variations',
      'child_is_in_stock',
      'child_is_on_backorder',
      'has_options',

      'exists',
      'is_downloadable',
      'is_virtual',
      'is_featured',
      'is_sold_individually',
      'is_visible',
      'has_dimensions',
      'has_weight',
      'is_in_stock',
      'needs_shipping',
      'is_taxable',
      'is_shipping_taxable',
      'managing_stock',
      'backorders_allowed',
      'backorders_require_notification',
      'has_attributes',
      'has_child',
      'child_has_dimensions',
      'child_has_weight',
      'has_options',
      'get_title',
      'get_permalink',
      'get_stock_managed_by_id',
      'get_formatted_name',
      'get_min_purchase_quantity',
      'get_max_purchase_quantity',
      'get_image',
      'get_shipping_class',
      'get_rating_count',
      'get_availability',

      'get_data_store',
      'get_id',
      'get_data_keys',
      'get_extra_data_keys',
      'get_meta_data',
    );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( $WC_Product , $method_list ) ) {

        continue;

      }

      echo '<tr>';

      printf( '<th>$WC_Product->%s()</th>' , esc_html( $method_list ) );

      echo '<td>';

      $value = $WC_Product->$method_list();

      if( is_array( $value ) or is_object( $value ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

      } else {

        echo esc_html( $value );

      }

      echo '</td>';

      echo '</tr>';

    }

    $method_lists = array(
      'is_on_sale',
      'get_children',
      'get_product_url',

      'get_button_text',

      'get_downloadable',

      'get_name',
      'get_slug',
      'get_date_created',
      'get_date_modified',
      'get_status',
      'get_featured',
      'get_catalog_visibility',
      'get_description',
      'get_short_description',
      'get_sku',
      'get_global_unique_id',
      'get_price',
      'get_regular_price',
      'get_sale_price',
      'get_date_on_sale_from',
      'get_date_on_sale_to',
      'get_total_sales',
      'get_tax_status',
      'get_tax_class',
      'get_manage_stock',
      'get_stock_quantity',
      'get_stock_status',
      'get_backorders',
      'get_low_stock_amount',
      'get_sold_individually',
      'get_weight',
      'get_length',
      'get_width',
      'get_height',
      'get_upsell_ids',
      'get_cross_sell_ids',
      'get_parent_id',
      'get_reviews_allowed',
      'get_purchase_note',
      'get_attributes',
      'get_default_attributes',
      'get_menu_order',
      'get_post_password',
      'get_category_ids',
      'get_tag_ids',
      'get_virtual',
      'get_gallery_image_ids',
      'get_shipping_class_id',
      'get_downloads',
      'get_download_expiry',
      'get_downloadable',
      'get_download_limit',
      'get_image_id',
      'get_rating_counts',
      'get_average_rating',
      'get_review_count',
    );

    $contexts = array( '' , 'edit' );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( $WC_Product , $method_list ) ) {

        continue;

      }

      foreach( $contexts as $context ) {

        echo '<tr>';

        printf( '<th>$WC_Product->%s( %s )</th>' , esc_html( $method_list ) , esc_html( $context ) );

        echo '<td>';

        $value = $WC_Product->$method_list( $context );

        if( is_array( $value ) or is_object( $value ) ) {

          printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

        } else {

          echo esc_html( $value );

        }

        echo '</td>';

        echo '</tr>';

      }

    }

    echo '</table>';

  }

  public static function mywp_debug_render_order() {

    global $wp;
    global $post;

    $wc_order_id = false;

    if( is_admin() ) {

      if( ! is_object( $post ) or empty( $post->ID ) ) {

        return false;

      }

      $wc_order_id = $post->ID;

    } else {

      if( ! empty( $wp->query_vars['view-order'] ) ) {

        $wc_order_id = $wp->query_vars['view-order'];

      }

    }

    self::print_debug_render_order( $wc_order_id );

  }

  public static function mywp_debug_render_admin_wc_orders() {

    if( empty( $_GET['action'] ) ) {

      return false;

    }

    if( $_GET['action'] !== 'edit' ) {

      return false;

    }

    if( empty( $_GET['id'] ) ) {

      return false;

    }

    $wc_order_id = (int) $_GET['id'];

    self::print_debug_render_order( $wc_order_id );

  }

  private static function print_debug_render_order( $wc_order_id ) {

    if( empty( $wc_order_id ) ) {

      return false;

    }

    $wc_order_id = intval( $wc_order_id );

    $WC_Order = wc_get_order( $wc_order_id );

    if( empty( $WC_Order ) or ! is_object( $WC_Order ) ) {

      return false;

    }

    printf( '<p>%s</p>' , '$WC_Order = wc_get_order( ' . $wc_order_id . ' );' );

    echo '<table class="debug-table">';

    $method_lists = array(
      'get_formatted_order_total',
      'get_base_data',
      'get_data',
      'get_changes',
      'get_order_number',
      'get_user',
      'get_address',
      'get_shipping_address_map_url',
      'get_formatted_billing_full_name',
      'get_formatted_shipping_full_name',
      'get_formatted_billing_address',
      'get_formatted_shipping_address',
      'has_billing_address',
      'has_shipping_address',
      'is_editable',
      'is_paid',
      'is_download_permitted',
      'needs_shipping_address',
      'has_downloadable_item',
      'get_downloadable_items',
      'needs_payment',
      'get_checkout_payment_url',
      'get_checkout_order_received_url',
      'get_cancel_order_url_raw',
      'get_cancel_order_url',
      'get_cancel_endpoint',
      'get_view_order_url',
      'get_edit_order_url',
      'get_customer_order_notes',
      'get_refunds',
      'get_total_refunded',
      'get_total_tax_refunded',
      'get_total_shipping_refunded',
      'get_item_count_refunded',
      'get_total_qty_refunded',
      'get_remaining_refund_amount',
      'get_remaining_refund_items',
      'get_order_item_totals',

      'get_type',
      'get_total_discount',
      'get_subtotal',
      'get_tax_totals',
      'get_items',
      'get_coupons',
      'get_fees',
      'get_taxes',
      'get_shipping_methods',
      'get_shipping_method',
      'get_coupon_codes',
      'get_item_count',
      'get_payment_tokens',
      'get_items_tax_classes',
      'get_taxable_location',
      'get_total_fees',
      'get_subtotal_to_display',
      'get_shipping_to_display',
      'get_discount_to_display',
      'has_free_item',
      'get_title',

      'get_data_store',
      'get_id',
      'get_data_keys',
      'get_extra_data_keys',
      'get_meta_data',
    );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( $WC_Order , $method_list ) ) {

        continue;

      }

      echo '<tr>';

      printf( '<th>$WC_Order->%s()</th>' , esc_html( $method_list ) );

      echo '<td>';

      $value = $WC_Order->$method_list();

      if( is_array( $value ) or is_object( $value ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

      } else {

        echo esc_html( $value );

      }

      echo '</td>';

      echo '</tr>';

    }

    $method_lists = array(
      'get_order_key',
      'get_customer_id',
      'get_user_id',
      'get_billing_first_name',
      'get_billing_last_name',
      'get_billing_company',
      'get_billing_address_1',
      'get_billing_address_2',
      'get_billing_city',
      'get_billing_state',
      'get_billing_postcode',
      'get_billing_country',
      'get_billing_email',
      'get_billing_phone',
      'get_shipping_first_name',
      'get_shipping_last_name',
      'get_shipping_company',
      'get_shipping_address_1',
      'get_shipping_address_2',
      'get_shipping_city',
      'get_shipping_state',
      'get_shipping_postcode',
      'get_shipping_country',
      'get_shipping_phone',
      'get_payment_method',
      'get_payment_method_title',
      'get_transaction_id',
      'get_customer_ip_address',
      'get_customer_user_agent',
      'get_created_via',
      'get_customer_note',
      'get_date_completed',
      'get_date_paid',
      'get_cart_hash',
      'get_order_stock_reduced',
      'get_download_permissions_granted',
      'get_new_order_email_sent',
      'get_recorded_sales',

      'get_parent_id',
      'get_currency',
      'get_version',
      'get_prices_include_tax',
      'get_date_created',
      'get_date_modified',
      'get_status',
      'get_discount_total',
      'get_discount_tax',
      'get_shipping_total',
      'get_shipping_tax',
      'get_cart_tax',
      'get_total',
      'get_total_tax',
      'get_recorded_coupon_usage_counts',
    );

    $contexts = array( '' , 'edit' );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( $WC_Order , $method_list ) ) {

        continue;

      }

      foreach( $contexts as $context ) {

        echo '<tr>';

        printf( '<th>$WC_Order->%s( %s )</th>' , esc_html( $method_list ) , esc_html( $context ) );

        echo '<td>';

        $value = $WC_Order->$method_list( $context );

        if( is_array( $value ) or is_object( $value ) ) {

          printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

        } else {

          echo esc_html( $value );

        }

        echo '</td>';

        echo '</tr>';

      }

    }

    echo '</table>';

    echo '<p>Order Items</p>';

    echo '<p>&nbsp;</p>';

    if( method_exists( $WC_Order , 'get_items' ) ) {

      $wc_order_items = $WC_Order->get_items();

      foreach( $wc_order_items as $wc_order_item_id => $WC_Order_Item ) {

        printf( '<p>%s</p>' , 'WC Order Item ID: ' . $wc_order_item_id . '' );

        echo '<table class="debug-table">';

        $method_lists = array(
          'get_type',
          'get_product',
          'get_item_downloads',
          'get_tax_status',

          'get_order',
          'get_all_formatted_meta_data',
          'get_formatted_meta_data',
          'get_data_store',
          'get_id',
          'get_data_keys',
          'get_extra_data_keys',
          'get_meta_data',
          'get_changes',
        );

        foreach( $method_lists as $method_list ) {

          if( ! method_exists( $WC_Order_Item , $method_list ) ) {

            continue;

          }

          echo '<tr>';

          printf( '<th>$WC_Order_Item->%s()</th>' , esc_html( $method_list ) );

          echo '<td>';

          $value = $WC_Order_Item->$method_list();

          if( is_array( $value ) or is_object( $value ) ) {

            printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

          } else {

            echo esc_html( $value );

          }

          echo '</td>';

          echo '</tr>';

        }

        $method_lists = array(
          'get_product_id',
          'get_variation_id',
          'get_quantity',
          'get_tax_class',
          'get_subtotal',
          'get_subtotal_tax',
          'get_total',
          'get_total_tax',
          'get_taxes',

          'get_order_id',
          'get_name',
        );

        $contexts = array( '' , 'edit' );

        foreach( $method_lists as $method_list ) {

          if( ! method_exists( $WC_Order_Item , $method_list ) ) {

            continue;

          }

          foreach( $contexts as $context ) {

            echo '<tr>';

            printf( '<th>$WC_Order_Item->%s( %s )</th>' , esc_html( $method_list ) , esc_html( $context ) );

            echo '<td>';

            $value = $WC_Order_Item->$method_list( $context );

            if( is_array( $value ) or is_object( $value ) ) {

              printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

            } else {

              echo esc_html( $value );

            }

            echo '</td>';

            echo '</tr>';

          }

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

    printf( '<p>%s</p>' , 'WC()->customer' );

    echo '<table class="debug-table">';

    $method_lists = array(
      'is_customer_outside_base',
      'get_avatar_url',
      'get_taxable_address',
      'get_downloadable_products',
      'is_vat_exempt',
      'has_calculated_shipping',
      'has_shipping_address',
      'get_is_vat_exempt',
      'get_password',
      'get_calculated_shipping',
      'get_last_order',
      'get_order_count',
      'get_total_spent',

      'get_data_store',
      'get_id',
      'get_data',
      'get_data_keys',
      'get_extra_data_keys',
      'get_meta_data',
      'get_changes',
    );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( WC()->customer , $method_list ) ) {

        continue;

      }

      echo '<tr>';

      printf( '<th>WC()->customer->%s()</th>' , esc_html( $method_list ) );

      echo '<td>';

      $value = WC()->customer->$method_list();

      if( is_array( $value ) or is_object( $value ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

      } else {

        echo $value;

      }

      echo '</td>';

      echo '</tr>';

    }

    $method_lists = array(
      'get_username',
      'get_email',
      'get_first_name',
      'get_last_name',
      'get_display_name',
      'get_role',
      'get_date_created',
      'get_date_modified',
      'get_billing',
      'get_billing_first_name',
      'get_billing_last_name',
      'get_billing_company',
      'get_billing_address',
      'get_billing_address_1',
      'get_billing_address_2',
      'get_billing_city',
      'get_billing_state',
      'get_billing_postcode',
      'get_billing_country',
      'get_billing_email',
      'get_billing_phone',
      'get_shipping',
      'get_shipping_first_name',
      'get_shipping_last_name',
      'get_shipping_company',
      'get_shipping_address',
      'get_shipping_address_1',
      'get_shipping_address_2',
      'get_shipping_city',
      'get_shipping_state',
      'get_shipping_postcode',
      'get_shipping_country',
      'get_shipping_phone',
      'get_is_paying_customer',
    );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( WC()->customer , $method_list ) ) {

        continue;

      }

      $contexts = array( '' , 'edit' );

      foreach( $contexts as $context ) {

        echo '<tr>';

        printf( '<th>WC()->customer->%s( %s )</th>' , esc_html( $method_list ) , esc_html( $context ) );

        echo '<td>';

        $value = WC()->customer->$method_list( $context );

        if( is_array( $value ) or is_object( $value ) ) {

          printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

        } else {

          echo $value;

        }

        echo '</td>';

        echo '</tr>';

      }

    }

    echo '</table>';

  }

  public static function mywp_debug_render_cart() {

    if( ! is_object( WC()->cart ) ) {

      return false;

    }

    printf( '<p>%s</p>' , '$wc_cart = WC()->cart' );

    echo '<table class="debug-table">';

    $method_lists = array(
      'get_cart_contents',
      'get_removed_cart_contents',
      'get_applied_coupons',
      'get_coupon_discount_totals',
      'get_coupon_discount_tax_totals',
      'get_totals',
      'get_subtotal',
      'get_subtotal_tax',
      'get_discount_total',
      'get_discount_tax',
      'get_shipping_total',
      'get_shipping_tax',
      'get_cart_contents_total',
      'get_cart_contents_tax',
      'get_total_tax',
      'get_fee_total',
      'get_fee_tax',
      'get_shipping_taxes',
      'get_cart_contents_taxes',
      'get_fee_taxes',
      'display_prices_including_tax',
      'get_taxes',
      'get_cart',
      'is_empty',
      'get_cart_contents_count',
      'get_cart_contents_weight',
      'get_cart_item_quantities',
      'get_cross_sells',
      'get_tax_totals',
      'get_cart_item_tax_classes',
      'get_cart_item_tax_classes_for_shipping',
      'get_displayed_subtotal',
      'get_customer',
      'needs_payment',
      'get_shipping_packages',
      'needs_shipping',
      'needs_shipping_address',
      'show_shipping',
      'get_cart_shipping_total',
      'get_coupons',
      'get_fees',
      'get_total_ex_tax',
      'get_cart_total',
      'get_cart_subtotal',
      'get_cart_tax',
      'get_taxes_total',
      'get_total_discount',
      'get_tax_price_display_mode',
      'get_cart_hash',

      'get_cart_from_session',
      'get_cart_for_session',
      'get_cart_discount_total',
      'get_cart_discount_tax_total',
    );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( WC()->cart , $method_list ) ) {

        continue;

      }

      echo '<tr>';

      printf( '<th>WC()->cart->%s()</th>' , esc_html( $method_list ) );

      echo '<td>';

      $value = WC()->cart->$method_list();

      if( is_array( $value ) or is_object( $value ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

      } else {

        echo $value;

      }

      echo '</td>';

      echo '</tr>';

    }

    $method_lists = array(
      'get_total',
    );

    $contexts = array( '' , 'edit' );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( WC()->cart , $method_list ) ) {

        continue;

      }

      foreach( $contexts as $context ) {

        echo '<tr>';

        printf( '<th>WC()->cart->%s( %s )</th>' , esc_html( $method_list ) , esc_html( $context ) );

        echo '<td>';

        $value = WC()->cart->$method_list( $context );

        if( is_array( $value ) or is_object( $value ) ) {

          printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

        } else {

          echo $value;

        }

        echo '</td>';

        echo '</tr>';

      }

    }

    echo '</table>';

  }

  public static function mywp_debug_render_checkout() {

    if( ! is_object( WC()->checkout ) ) {

      return false;

    }

    printf( '<p>%s</p>' , 'WC()->checkout' );

    echo '<table class="debug-table">';

    $method_lists = array(
      'is_registration_required',
      'is_registration_enabled',
      'get_checkout_fields',
    );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( WC()->checkout , $method_list ) ) {

        continue;

      }

      echo '<tr>';

      printf( '<th>WC()->checkout->%s()</th>' , esc_html( $method_list ) );

      echo '<td>';

      $value = WC()->checkout->$method_list();

      if( is_array( $value ) or is_object( $value ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

      } else {

        echo $value;

      }

      echo '</td>';

      echo '</tr>';

    }

    echo '</table>';

  }

  public static function mywp_debug_render_session() {

    if( ! is_object( WC()->session ) ) {

      return false;

    }

    printf( '<p>%s</p>' , 'WC()->session' );

    echo '<table class="debug-table">';

    $method_lists = array(
      'get_customer_id',
      'has_session',
      'get_customer_unique_id',
      'get_session_cookie',
      'get_session_data',
    );

    foreach( $method_lists as $method_list ) {

      if( ! method_exists( WC()->session , $method_list ) ) {

        continue;

      }

      echo '<tr>';

      printf( '<th>WC()->session->%s</th>' , esc_html( $method_list ) );

      echo '<td>';

      $value = WC()->session->$method_list();

      if( is_array( $value ) or is_object( $value ) ) {

        printf( '<textarea readonly="readonly">%s</textarea>' , print_r( map_deep( $value , 'esc_html' ) , true ) );

      } else {

        echo $value;

      }

      echo '</td>';

      echo '</tr>';

    }

    echo '</table>';

    $wc_session_data = WC()->session->get_session_data();

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

      echo '</tr>';

    }

    echo '</table>';

  }

}

MywpDeveloperModuleDevWooCommerce::init();

endif;
