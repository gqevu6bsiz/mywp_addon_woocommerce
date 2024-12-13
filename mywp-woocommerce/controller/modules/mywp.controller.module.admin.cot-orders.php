<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractControllerListModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAdminWooCommerceCotOrders' ) ) :

final class MywpControllerModuleAdminWooCommerceCotOrders extends MywpAbstractControllerListModule {

  static protected $id = 'admin_woocommerce_cot_orders';

  static private $column_type = '';

  public static function mywp_controller_initial_data( $initial_data ) {

    $initial_data['list_columns'] = array();

    $initial_data['per_page_num'] = '';
    $initial_data['hide_add_new'] = '';
    $initial_data['hide_search_box'] = '';
    $initial_data['hide_bulk_actions'] = '';
    $initial_data['custom_search_filter'] = '';

    return $initial_data;

  }

  public static function mywp_controller_default_data( $default_data ) {

    $default_data['list_columns'] = array();

    $default_data['per_page_num'] = '';
    $default_data['hide_add_new'] = false;
    $default_data['hide_search_box'] = false;
    $default_data['hide_bulk_actions'] = false;
    $default_data['custom_search_filter'] = false;

    return $default_data;

  }

  public static function mywp_wp_loaded() {

    if( ! self::is_do_controller() ) {

      return false;

    }

    if( ! MywpWooCommerceApi::is_enable_woocommerce() ) {

      return false;

    }

    if( ! MywpWooCommerceApi::is_enabled_cot() ) {

      return false;

    }

    add_action( 'mywp_setting_screen_content_admin_posts' , array( __CLASS__ , 'mywp_setting_screen_content_admin_posts' ) , 9 );

    add_action( 'load-woocommerce_page_wc-orders' , array( __CLASS__ , 'load_orders' ) , 1000 );

    add_action( 'mywp_controller_' . self::$id . '_custom_search_filter_form_field_content' , array( __CLASS__ , 'custom_search_filter_form_field_content' ) );

  }

  public static function mywp_setting_screen_content_admin_posts() {

    $current_setting_post_type_id = MywpSettingPostType::get_current_post_type_id();

    if( $current_setting_post_type_id !== 'shop_order' ) {

      return false;

    }

    $customize_link = add_query_arg( array( 'page' => 'mywp_admin' , 'setting_screen' => 'admin_woocommerce_cot_orders' ) , admin_url( 'admin.php' ) );

    ?>
    <p class="" style="text-align: center; color: #cc0000;">
      <?php printf( __( '%1$s is %2$s (HPOS), so %3$s(shop_order) of %4$s are not available.' , 'mywp-woocommerce' ) , __( 'Order data storage' , 'woocommerce' ) , __( 'High-performance order storage (recommended)' , 'woocommerce' ) , __( 'Order' , 'woocommerce' ) , __( 'Posts' ) ); ?><br />
    </p>
    <p class="" style="text-align: center; margin: 0 auto 22px auto;">
      <a href="<?php echo esc_url( $customize_link ); ?>"><?php printf( __( 'WC %s (HPOS) can be customized from here.' , 'mywp-woocommerce' ) , __( 'Orders' , 'woocommerce' ) ); ?></a>
    </p>

    <style>
    #setting-screen-setting-list-columns,
    #setting-screen-advance {
      opacity: 0.4;
    }
    </style>
    <?php

  }

  public static function load_orders() {

    if( ! empty( $_GET['action'] ) ) {

      return false;

    }

    self::$column_type = 'woocommerce_page_wc-orders';

    add_action( 'admin_print_styles' , array( __CLASS__ , 'show_display_num' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_add_new' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_search_box' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'hide_bulk_actions' ) );

    add_action( 'admin_print_styles' , array( __CLASS__ , 'change_column_width' ) );

    add_filter( 'edit_' . 'shop_order' . '_per_page' , array( __CLASS__ , 'edit_per_page' ) );

    add_filter( 'manage_' . self::$column_type . '_columns' , array( __CLASS__ , 'manage_columns' ) , 10001 );

    add_action( 'manage_' . self::$column_type . '_custom_column' , array( __CLASS__ , 'manage_column_body' ) , 10 , 2 );

    self::custom_search_filter();

  }

  public static function show_display_num() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    ?>
    <style>
    body.woocommerce_page_wc-orders .tablenav .one-page .displaying-num,
    body.woocommerce_page_wc-orders .tablenav .one-page .pagination-links {
      display: inline-block;
    }
    </style>
    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_add_new() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_add_new'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin .wrap .page-title-action { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_search_box() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_search_box'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin #wc-orders-filter .search-box { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function hide_bulk_actions() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['hide_bulk_actions'] ) ) {

      return false;

    }

    ?>

    <style>
    body.wp-admin #wc-orders-filter .tablenav .bulkactions { display: none; }
    </style>

    <?php

    self::after_do_function( __FUNCTION__ );

  }

  public static function change_column_width() {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return false;

    }

    $columns = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

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

      echo 'body.wp-admin .wp-list-table.widefat thead th.column-' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead th.column-' . esc_attr( $column_id ) . '.hidden { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead td.column-' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead td.column-' . esc_attr( $column_id ) . '.hidden { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead th#' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead th#' . esc_attr( $column_id ) . '.hidden { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead td#' . esc_attr( $column_id ) . ' { width: ' . esc_attr( $width ) . '; display: table-cell; }';
      echo 'body.wp-admin .wp-list-table.widefat thead td#' . esc_attr( $column_id ) . '.hidden { display: none; }';

    }

    echo '@media screen and (max-width: 782px) {';

    foreach( $columns as $column_id => $width ) {

      if( in_array( $column_id , array( 'cb' , 'title' ) , true ) ) {

        continue;

      }

      echo 'body.wp-admin .wp-list-table.widefat thead th.column-' . esc_attr( $column_id ) . ' { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead td.column-' . esc_attr( $column_id ) . ' { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead th#' . esc_attr( $column_id ) . ' { display: none; }';

      echo 'body.wp-admin .wp-list-table.widefat thead td#' . esc_attr( $column_id ) . ' { display: none; }';

    }

    echo '}';

    echo '</style>';

    self::after_do_function( __FUNCTION__ );

  }

  public static function edit_per_page( $per_page ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $per_page;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['per_page_num'] ) ) {

      return $per_page;

    }

    $per_page = $setting_data['per_page_num'];

    self::after_do_function( __FUNCTION__ );

    return $per_page;

  }

  public static function manage_columns( $columns ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return $columns;

    }

    $setting_data = self::get_setting_data();

    if( empty( $setting_data['list_columns'] ) ) {

      return $columns;

    }

    $wp_kses_allowed_html = wp_kses_allowed_html( 'post' );

    $wp_kses_allowed_html['input'] = array(
      'type' => 1,
      'class' => 1,
      'id' => 1,
    );

    $columns = array();

    foreach( $setting_data['list_columns'] as $column_id => $column_setting ) {

      $columns[ $column_id ] = wp_kses( do_shortcode( $column_setting['title'] ) , $wp_kses_allowed_html );

    }

    self::after_do_function( __FUNCTION__ );

    return $columns;

  }

  public static function manage_column_body( $column_id , $WC_Order ) {

    if( ! self::is_do_function( __FUNCTION__ ) ) {

      return false;

    }

    if( empty( $WC_Order ) ) {

      return false;

    }

    if( strpos( $column_id , 'mywp_wc_column_' ) === false ) {

      return false;

    }

    $column_type = str_replace( 'mywp_wc_column_' , '' , $column_id );

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

  protected static function custom_search_filter_do() {

    add_action( 'woocommerce_order_list_table_prepare_items_query_args' , array( __CLASS__ , 'custom_search_filter_query' ) );

    add_action( 'mywp_controller_' . self::$id . '_custom_search_filter_form' , array( __CLASS__ , 'form_in_page' ) );

  }

  public static function custom_search_filter_query( $order_query_args ) {

    $nonce_key = 'mywp_controller_' . self::$id . '_custom_search_filter';

    if( empty( $_REQUEST[ $nonce_key ] ) ) {

      return false;

    }

    check_admin_referer( $nonce_key , $nonce_key );

    if( empty( $_REQUEST['mywp_controller_' . self::$id ]['custom_search_filter_request'] ) ) {

      return false;

    }

    $custom_search_filter_requests = $_REQUEST['mywp_controller_' . self::$id ]['custom_search_filter_request'];

    return self::custom_search_filter_query_do( $order_query_args , $custom_search_filter_requests );

  }

  protected static function custom_search_filter_query_do( $order_query_args , $custom_search_filter_requests ) {

    if( empty( $custom_search_filter_requests ) ) {

      return $order_query_args;

    }

    foreach( $custom_search_filter_requests as $custom_search_filter_request_key => $custom_search_filter_request ) {

      if( empty( $custom_search_filter_request ) ) {

        continue;

      }

      if( strpos( $custom_search_filter_request_key , 'mywp_custom_search_wc_' ) === false ) {

        continue;

      }

      $custom_search_filter_type = str_replace( 'mywp_custom_search_wc_' , '' , $custom_search_filter_request_key );

      if( $custom_search_filter_type === 'order_id' ) {

        $value = MywpHelper::sanitize_number( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $order_query_args['id'] = array( $value );

        }

      } elseif( $custom_search_filter_type === 'order_status' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        $wc_order_statuses = wc_get_order_statuses();

        if( ! empty( $wc_order_statuses[ $value ] ) ) {

          $order_query_args['status'] = array( $value );

        }

      } elseif( $custom_search_filter_type === 'order_date' ) {

        if( ! empty( $custom_search_filter_request['from'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['from'] ) );

          if( ! empty( $value ) ) {

            $order_query_args['date_after'] = $value;

          }

        }

        if( ! empty( $custom_search_filter_request['to'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['to'] ) );

          if( ! empty( $value ) ) {

            $order_query_args['date_before'] = $value;

          }

        }

      } elseif( $custom_search_filter_type === 'order_key' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $order_query_args['order_key'] = $value;

        }

      } elseif( $custom_search_filter_type === 'customer_user' ) {

        $value = MywpHelper::sanitize_number( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $order_query_args['customer_id'] = array( $value );

        }

      } elseif( $custom_search_filter_type === 'payment_method' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        $payment_gateways = WC()->payment_gateways->payment_gateways();

        if( ! empty( $payment_gateways[ $value ] ) ) {

          $order_query_args['payment_method'] = $value;

        }

      } elseif( $custom_search_filter_type === 'billing_email' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $order_query_args['billing_email'] = $value;

        }

      } elseif( $custom_search_filter_type === 'billing_phone' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $order_query_args['billing_phone'] = $value;

        }

      } elseif( $custom_search_filter_type === 'billing_name' ) {

        if( ! empty( $custom_search_filter_request['first_name'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['first_name'] ) );

          if( ! empty( $value ) ) {

            $order_query_args['billing_first_name'] = $value;

          }

        }

        if( ! empty( $custom_search_filter_request['last_name'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['last_name'] ) );

          if( ! empty( $value ) ) {

            $order_query_args['billing_last_name'] = $value;

          }

        }

      } elseif( $custom_search_filter_type === 'shipping_phone' ) {

        $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request ) );

        if( ! empty( $value ) ) {

          $order_query_args['shipping_phone'] = $value;

        }

      } elseif( $custom_search_filter_type === 'shipping_name' ) {

        if( ! empty( $custom_search_filter_request['first_name'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['first_name'] ) );

          if( ! empty( $value ) ) {

            $order_query_args['shipping_first_name'] = $value;

          }

        }

        if( ! empty( $custom_search_filter_request['last_name'] ) ) {

          $value = MywpHelper::sanitize_text( wp_unslash( $custom_search_filter_request['last_name'] ) );

          if( ! empty( $value ) ) {

            $order_query_args['shipping_last_name'] = $value;

          }

        }

      }

    }

    return $order_query_args;

  }

  protected static function get_custom_search_filter_fields() {

    $wc_order_statuses = wc_get_order_statuses();

    $payment_gateways = WC()->payment_gateways->payment_gateways();

    $selectable_payment_methods = array();

    if( ! empty( $payment_gateways ) ) {

      foreach( $payment_gateways as $payment_method_id => $payment_gateway ) {

        $selectable_payment_methods[ $payment_method_id ] = $payment_gateway->title;

      }

    }

    $custom_search_filter_fields = array(
      'mywp_custom_search_wc_order_id' => array(
        'id' => 'mywp_custom_search_wc_order_id',
        'title' => __( 'Order Number' , 'woocommerce' ),
        'type' => 'number',
      ),
      'mywp_custom_search_wc_order_status' => array(
        'id' => 'mywp_custom_search_wc_order_status',
        'title' => __( 'Order Status' , 'woocommerce' ),
        'type' => 'select',
        'multiple' => false,
        'choices' => $wc_order_statuses,
      ),
      'mywp_custom_search_wc_order_date' => array(
        'id' => 'mywp_custom_search_wc_order_date',
        'title' => __( 'Date' ),
        'type' => 'date',
      ),
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

    $custom_search_filter_fields = apply_filters( 'mywp_controller_' . self::$id . '_custom_search_filter_fields' , $custom_search_filter_fields );

    if( empty( $custom_search_filter_fields ) ) {

      return false;

    }

    return $custom_search_filter_fields;

  }

  public static function custom_search_filter_form_field_content( $custom_search_filter_field ) {

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

  protected static function get_custom_search_filter_fields_after( $custom_search_filter_fields , $custom_search_filter_requests ) {

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

  public static function form_in_page() {

    ?>

    <input type="hidden" name="page" value="<?php echo esc_attr( 'wc-orders' ); ?>">

    <?php

  }

}

MywpControllerModuleAdminWooCommerceCotOrders::init();

endif;
