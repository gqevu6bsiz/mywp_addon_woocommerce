<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractControllerListModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminWooCommerceShopOrders' ) ) :

final class MywpControllerModuleAdminWooCommerceShopOrders extends MywpAbstractControllerListModule {

  static protected $id = 'admin_woocommerce_shop_orders';

  static private $post_type = 'shop_order';

  public static function mywp_wp_loaded() {

    if( ! self::is_do_controller() ) {

      return false;

    }

    if( ! MywpWooCommerceApi::is_enable_woocommerce() ) {

      return false;

    }

    if( MywpWooCommerceApi::is_enabled_cot() ) {

      return false;

    }

    add_action( 'mywp_ajax' , array( __CLASS__ , 'mywp_ajax' ) , 1000 );

    add_action( 'load-edit.php' , array( __CLASS__ , 'load_edit' ) , 1000 );

    add_filter( 'mywp_controller_admin_posts_get_post_statuses-' . self::$post_type , array( __CLASS__ , 'mywp_controller_admin_posts_get_post_statuses' ) , 9 );

    add_filter( 'mywp_setting_admin_posts_get_available_list_columns_' . self::$post_type , array( __CLASS__ , 'mywp_setting_admin_posts_get_available_list_columns' ) );

    add_filter( 'mywp_controller_admin_posts_custom_search_filter_fields-' . self::$post_type , array( __CLASS__ , 'mywp_controller_admin_posts_custom_search_filter_fields' ) , 9 );

    add_action( 'mywp_controller_admin_posts_custom_search_filter-' . self::$post_type , array( __CLASS__ , 'mywp_controller_admin_posts_custom_search_filter' ) , 9 , 2 );

    add_action( 'mywp_controller_admin_posts_custom_search_filter_form_field_content' , array( __CLASS__ , 'mywp_controller_admin_posts_custom_search_filter_form_field_content' ) );

    add_filter( 'mywp_controller_admin_posts_custom_search_filter_fields_after-' . self::$post_type , array( __CLASS__ , 'mywp_controller_admin_posts_custom_search_filter_fields_after' ) , 10 , 2 );

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

    add_action( 'admin_print_styles' , array( __CLASS__ , 'show_display_num' ) );

    add_action( "manage_{$typenow}_posts_custom_column" , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

  }

  public static function show_display_num() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    ?>
    <style>
    body.post-type-shop_order .tablenav .one-page .displaying-num,
    body.post-type-shop_order .tablenav .one-page .pagination-links,
    body.woocommerce_page_wc-orders .tablenav .one-page .displaying-num,
    body.woocommerce_page_wc-orders .tablenav .one-page .pagination-links {
      display: inline-block;
    }
    </style>
    <?php

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

    $WC_Order = wc_get_order( $post_id );

    if( $column_type === 'order_key' ) {

      echo esc_html( $WC_Order->get_order_key() );

    } elseif( $column_type === 'customer_user' ) {

      $user_id = $WC_Order->get_customer_id();

      if( ! empty( $user_id ) ) {

        printf( '[%s] ' , esc_html( $user_id ) );

        $user_data = get_userdata( $user_id );

        if( ! empty( $user_data ) ) {

          echo esc_html( $user_data->display_name );

          echo '<br />';

          printf( '(%s %s)' , esc_html( $user_data->first_name ) , esc_html( $user_data->last_name ) );

        }

      }

    } elseif( $column_type === 'payment_method' ) {

      printf( '[%s]<br />%s' , esc_html( $WC_Order->get_payment_method() ) , esc_html( $WC_Order->get_payment_method_title() ) );

    } elseif( $column_type === 'billing_email' ) {

      echo esc_html( $WC_Order->get_billing_email() );

    } elseif( $column_type === 'billing_phone' ) {

      echo esc_html( $WC_Order->get_billing_phone() );

    } elseif( $column_type === 'shipping_phone' ) {

      echo esc_html( $WC_Order->get_shipping_phone() );

    }

    self::after_do_function( __FUNCTION__ );

  }

  public static function mywp_controller_admin_posts_get_post_statuses( $post_statuses ) {

    $order_statuses = wc_get_order_statuses();

    if( empty( $order_statuses ) ) {

      return $post_statuses;

    }

    $post_statuses = $order_statuses;

    return $post_statuses;

  }

  private static function get_product_list_default_columns() {

    $product_list_default_columns = array(
      'order_number' => array(
        'sort' => true,
        'orderby' => 'ID',
        'width' => '20ch',
      ),
      'order_date' => array(
        'sort' => true,
        'orderby' => 'date',
        'width' => '10ch',
      ),
      'order_status' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '14ch',
      ),
      'billing_address' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '20ch',
      ),
      'shipping_address' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '20ch',
      ),
      'order_total' => array(
        'sort' => true,
        'orderby' => 'order_total',
        'width' => '8ch',
      ),
      'wc_actions' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '12ch',
      ),
      'origin' => array(
        'sort' => false,
        'orderby' => '',
        'width' => '',
      ),
    );

    return $product_list_default_columns;

  }

  private static function get_product_list_extend_columns() {

    $product_list_extend_columns = array(
      'order_key' => array(
        'title' => __( 'Order key' , 'woocommerce' ),
        'default_title' => __( 'Order key' , 'woocommerce' ),
      ),
      'customer_user' => array(
        'title' => __( 'Customer User' , 'woocommerce' ),
        'default_title' => __( 'Customer User' , 'woocommerce' ),
      ),
      'payment_method' => array(
        'title' => __( 'Payment method' , 'woocommerce' ),
        'default_title' => __( 'Payment method' , 'woocommerce' ),
      ),
      'billing_email' => array(
        'title' => __( 'Billing email' , 'woocommerce' ),
        'default_title' => __( 'Billing email' , 'woocommerce' ),
      ),
      'billing_phone' => array(
        'title' => __( 'Billing Phone Number' , 'woocommerce' ),
        'default_title' => __( 'Billing Phone Number' , 'woocommerce' ),
      ),
      'shipping_phone' => array(
        'title' => __( 'Shipping Phone Number' , 'woocommerce' ),
        'default_title' => __( 'Shipping Phone Number' , 'woocommerce' ),
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

    if( isset( $available_list_columns['deprecated']['columns'][ '_payment_method_title' ] ) ) {

      unset( $available_list_columns['deprecated']['columns'][ '_payment_method_title' ] );

    }

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

    $payment_gateways = WC()->payment_gateways->payment_gateways();

    $selectable_payment_methods = array();

    if( ! empty( $payment_gateways ) ) {

      foreach( $payment_gateways as $payment_method_id => $payment_gateway ) {

        $selectable_payment_methods[ $payment_method_id ] = $payment_gateway->title;

      }

    }

    $product_custom_search_filter_fields = array(
      'mywp_custom_search_wc_order_key' => array(
        'id' => 'mywp_custom_search_wc_order_key',
        'title' => __( 'Order key' , 'mywp-woocommerce' ),
        'type' => 'text',
      ),
      'mywp_custom_search_wc_customer_user' => array(
        'id' => 'mywp_custom_search_wc_customer_user',
        'title' => __( 'Customer User ID' , 'mywp-woocommerce' ),
        'type' => 'number',
      ),
      'mywp_custom_search_wc_payment_method' => array(
        'id' => 'mywp_custom_search_wc_payment_method',
        'title' => __( 'Payment method' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => $selectable_payment_methods,
      ),
      'mywp_custom_search_wc_billing_email' => array(
        'id' => 'mywp_custom_search_wc_billing_email',
        'title' => __( 'Billing email' , 'woocommerce' ),
        'type' => 'text',
        'placeholder' => 'example@example.com',
      ),
      'mywp_custom_search_wc_billing_phone' => array(
        'id' => 'mywp_custom_search_wc_billing_phone',
        'title' => __( 'Billing Phone Number' , 'woocommerce' ),
        'type' => 'text',
      ),
      'mywp_custom_search_wc_billing_name' => array(
        'id' => 'mywp_custom_search_wc_billing_name',
        'title' => __( 'Billing name' , 'mywp-woocommerce' ),
        'type' => 'custom',
        'woocommerce' => 'billing_name',
        'placeholder' => array(
          'first_name' => __( 'First name' , 'woocommerce' ),
          'last_name' => __( 'Last name' , 'woocommerce' ),
        ),
      ),
      'mywp_custom_search_wc_shipping_phone' => array(
        'id' => 'mywp_custom_search_wc_shipping_phone',
        'title' => __( 'Shipping Phone Number' , 'woocommerce' ),
        'type' => 'text',
      ),
      'mywp_custom_search_wc_shipping_name' => array(
        'id' => 'mywp_custom_search_wc_shipping_name',
        'title' => __( 'Shipping name' , 'woocommerce' ),
        'type' => 'custom',
        'woocommerce' => 'shipping_name',
        'placeholder' => array(
          'first_name' => __( 'First name' , 'woocommerce' ),
          'last_name' => __( 'Last name' , 'woocommerce' ),
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

    $custom_search_filter_fields = wp_parse_args( $product_custom_search_filter_fields , $custom_search_filter_fields );

    if( isset( $custom_search_filter_fields['mywp_custom_search_post_status'] ) ) {

      $custom_search_filter_fields['mywp_custom_search_post_status']['title'] = __( 'Order Status' , 'woocommerce' );

    }

    if( isset( $custom_search_filter_fields['mywp_custom_search_title'] ) ) {

      unset( $custom_search_filter_fields['mywp_custom_search_title'] );

    }

    if( isset( $custom_search_filter_fields['mywp_custom_search_post_parent'] ) ) {

      unset( $custom_search_filter_fields['mywp_custom_search_post_parent'] );

    }

    return $custom_search_filter_fields;

  }

  public static function mywp_controller_admin_posts_custom_search_filter( $query , $custom_search_filter_requests ) {

    $meta_query = array();

    foreach( $custom_search_filter_requests as $custom_search_filter_request_key => $custom_search_filter_request ) {

      if( empty( $custom_search_filter_request ) ) {

        continue;

      }

      if( strpos( $custom_search_filter_request_key , 'mywp_custom_search_wc_' ) === false ) {

        continue;

      }

      $custom_search_filter_type = str_replace( 'mywp_custom_search_wc_' , '' , $custom_search_filter_request_key );

      if( $custom_search_filter_type === 'order_key' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_order_key',
            'value' => $value,
            'compare' => 'LIKE',
          );

        }

      } elseif( $custom_search_filter_type === 'customer_user' ) {

        $value = MywpHelper::sanitize_number( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_customer_user',
            'value' => $value,
            'compare' => '=',
          );

        }

      } elseif( $custom_search_filter_type === 'payment_method' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_payment_method',
            'value' => $value,
            'compare' => '=',
          );

        }

      } elseif( $custom_search_filter_type === 'billing_email' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_billing_email',
            'value' => $value,
            'compare' => 'LIKE',
          );

        }

      } elseif( $custom_search_filter_type === 'billing_phone' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_billing_phone',
            'value' => $value,
            'compare' => 'LIKE',
          );

        }

      } elseif( $custom_search_filter_type === 'billing_name' ) {

        if( ! empty( $custom_search_filter_request['first_name'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['first_name'] ) );

          if( ! empty( $value ) ) {

            $meta_query[] = array(
              'key' => '_billing_first_name',
              'value' => $value,
              'compare' => 'LIKE',
            );

          }

        }

        if( ! empty( $custom_search_filter_request['last_name'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['last_name'] ) );

          if( ! empty( $value ) ) {

            $meta_query[] = array(
              'key' => '_billing_last_name',
              'value' => $value,
              'compare' => 'LIKE',
            );

          }

        }

      } elseif( $custom_search_filter_type === 'shipping_phone' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $meta_query[] = array(
            'key' => '_shipping_phone',
            'value' => $value,
            'compare' => 'LIKE',
          );

        }

      } elseif( $custom_search_filter_type === 'shipping_name' ) {

        if( ! empty( $custom_search_filter_request['first_name'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['first_name'] ) );

          if( ! empty( $value ) ) {

            $meta_query[] = array(
              'key' => '_shipping_first_name',
              'value' => $value,
              'compare' => 'LIKE',
            );

          }

        }

        if( ! empty( $custom_search_filter_request['last_name'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['last_name'] ) );

          if( ! empty( $value ) ) {

            $meta_query[] = array(
              'key' => '_shipping_last_name',
              'value' => $value,
              'compare' => 'LIKE',
            );

          }

        }

      }

    }

    if( ! empty( $meta_query ) ) {

      $query->set( 'meta_query' , wp_parse_args( $meta_query , array( 'relation' => 'AND' ) ) );

    }

  }

  public static function mywp_controller_admin_posts_custom_search_filter_form_field_content( $custom_search_filter_field ) {

    if( empty( $custom_search_filter_field['woocommerce'] ) ) {

      return false;

    }

    $wc_field = $custom_search_filter_field['woocommerce'];

    if( in_array( $wc_field , array( 'billing_name' , 'shipping_name' ) , true ) ) {

      ?>

      <label class="first_name">
        <?php _e( 'First name' , 'woocommerce' ); ?>
        <input type="text" name="<?php echo esc_attr( $custom_search_filter_field['input_name'] ); ?>[first_name]" value="<?php echo esc_attr( $custom_search_filter_field['input_value']['first_name'] ); ?>" placeholder="<?php echo esc_attr( $custom_search_filter_field['placeholder']['first_name'] ); ?>" class="first_name" />
      </label>

      <label class="last_name">
        <?php _e( 'Last name' , 'woocommerce' ); ?>
        <input type="text" name="<?php echo esc_attr( $custom_search_filter_field['input_name'] ); ?>[last_name]" value="<?php echo esc_attr( $custom_search_filter_field['input_value']['last_name'] ); ?>" placeholder="<?php echo esc_attr( $custom_search_filter_field['placeholder']['last_name'] ); ?>" class="last_name" />
      </label>

      <?php

    }

  }

  public static function mywp_controller_admin_posts_custom_search_filter_fields_after( $custom_search_filter_fields , $custom_search_filter_requests ) {

    foreach( $custom_search_filter_fields as $custom_search_filter_field_id => $custom_search_filter_field ) {

      if( empty( $custom_search_filter_field['woocommerce'] ) ) {

        continue;

      }

      $wc_field = $custom_search_filter_field['woocommerce'];

      if( in_array( $wc_field , array( 'billing_name' , 'shipping_name' ) ) ) {

        $filteterd = false;

        $first_name = '';

        if( ! empty( $custom_search_filter_requests[ $custom_search_filter_field_id ]['first_name'] ) ) {

          $first_name = MywpHelper::sanitize_text( $custom_search_filter_requests[ $custom_search_filter_field_id ]['first_name'] );

          $filteterd = true;

        }

        $last_name = '';

        if( ! empty( $custom_search_filter_requests[ $custom_search_filter_field_id ]['last_name'] ) ) {

          $last_name = MywpHelper::sanitize_text( $custom_search_filter_requests[ $custom_search_filter_field_id ]['last_name'] );

          $filteterd = true;

        }

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['input_value'] = array(
          'first_name' => $first_name,
          'last_name' => $last_name,
        );

        $custom_search_filter_fields[ $custom_search_filter_field_id ]['filtered'] = $filteterd;

      }

    }

    return $custom_search_filter_fields;

  }

}

MywpControllerModuleAdminWooCommerceShopOrders::init();

endif;
