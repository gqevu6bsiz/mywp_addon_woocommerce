<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAdminWooCommerceCotOrders' ) ) :

final class MywpSettingScreenAdminWooCommerceCotOrders extends MywpAbstractSettingColumnsModule {

  static protected $id = 'admin_woocommerce_cot_orders';

  static protected $priority = 51;

  static private $menu = 'admin';

  static private $post_type = '';

  static protected $list_column_id = false;

  public static function mywp_setting_screens( $setting_screens ) {

    if( ! MywpWooCommerceApi::is_enable_woocommerce() ) {

      return $setting_screens;

    }

    if( ! MywpWooCommerceApi::is_enabled_cot() ) {

      return $setting_screens;

    }

    $setting_screens[ self::$id ] = array(
      'title' => sprintf( 'WC %s (HPOS)' , __( 'Orders' , 'woocommerce' ) ),
      'menu' => self::$menu,
      'controller' => 'admin_woocommerce_cot_orders',
      'use_advance' => true,
    );

    return $setting_screens;

  }

  public static function ajax_add_column() {

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'add_column' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( empty( $_POST['add_column_id'] ) ) {

      return false;

    }

    $add_column_id = strip_tags( $_POST['add_column_id'] );

    self::set_list_column_id();

    $available_list_columns = self::get_available_list_columns();

    if( empty( $available_list_columns ) ) {

      return false;

    }

    $found_column = false;

    foreach( $available_list_columns as $group => $list_columns ) {

      if( empty( $list_columns['columns'] ) ) {

        continue;

      }

      foreach( $list_columns['columns'] as $column_id => $list_column ) {

        if( $column_id === $add_column_id ) {

          $found_column = $list_column;

          break;

        }

      }

    }

    if( empty( $found_column ) ) {

      return false;

    }

    $result_html = '';

    ob_start();

    self::print_item( $found_column , $found_column['id'] );

    $result_html .= ob_get_contents();

    ob_end_clean();

    wp_send_json_success( array( 'result_html' => $result_html ) );

  }

  public static function mywp_current_admin_print_footer_scripts() {

    $post_data_js_custom_values = array();

    $post_data_js_custom_values = apply_filters( 'mywp_setting_admin_woocommerce_cot_orders_post_data_js_custom_values' , $post_data_js_custom_values );

    if( ! is_array( $post_data_js_custom_values ) ) {

      $post_data_js_custom_values = array();

    }

    ?>
    <script>
    jQuery(function( $ ) {

      const post_data_js_custom_values = JSON.parse( '<?php echo json_encode( $post_data_js_custom_values ); ?>' );

      $('#setting-screen-setting-list-columns #setting-list-columns-available-add-column').on('click', function() {

        let $available_column = $(this).parent();

        let $spinner = $available_column.find('.spinner');

        let select_column_key = $available_column.find('#setting-list-columns-available-select-column').val();

        if( select_column_key === null || select_column_key === '' ) {

          return false;

        }

        let $available_columnm_key = $available_column.find('.available-column.column-key-' + select_column_key);

        if( ! $available_columnm_key.length ) {

          return false;

        }

        let add_column_id = $available_columnm_key.find('.id').val();

        if( add_column_id === null || add_column_id === '' ) {

          return false;

        }

        let $already_column = false;

        $(document).find('#setting-screen-setting-list-columns #setting-list-columns-setting-columns-items .list-columns-item').each( function( index , el ) {

          let list_column_item_id = $(el).find('.list-column-item-id').val();

          if( list_column_item_id === add_column_id ) {

            $already_column = $(el);

            $(el).addClass('already');

            setTimeout( function() {

              $(el).removeClass('already');

            }, 2000);

            return false;

          }

        });

        if( $already_column !== false ) {

          alert( mywp_admin_setting.column_already_added );

          let scroll_position = $already_column.offset().top;

          scroll_position = ( scroll_position - 80 );

          $( 'html,body' ).animate({
            scrollTop: scroll_position
          });

          return false;

        }

        PostData = {
          action: '<?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'add_column' ) ); ?>',
          <?php echo esc_js( MywpSetting::get_ajax_action_name( self::$id , 'add_column' ) ); ?>: '<?php echo esc_js( wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'add_column' ) ) ); ?>',
          add_column_id: add_column_id
        };

        $.each( post_data_js_custom_values, function( key , value ) {

          PostData[ key ] = value;

        });

        $spinner.css('visibility', 'visible');

        $.ajax({
          type: 'post',
          url: ajaxurl,
          data: PostData
        }).done( function( xhr ) {

          if( typeof xhr !== 'object' || xhr.success === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          if( xhr.data.result_html === undefined ) {

            alert( mywp_admin_setting.unknown_error_reload_page );

            return false;

          }

          $html = $(xhr.data.result_html);

          $html.addClass('active');

          $(document).find('#setting-list-columns-setting-columns-items').append( $html );

          $(document).find('.list-columns-sortable-items').sortable({
            connectWith: '.list-columns-sortable-items'
          });

          let scroll_position = $(document).find('#setting-list-columns-setting-columns-items .list-columns-item').last().offset().top;

          scroll_position = ( scroll_position - 40 );

          $( 'html,body' ).animate({
            scrollTop: scroll_position
          });

        }).fail( function( xhr ) {

          alert( mywp_admin_setting.unknown_error_reload_page );

          return false;

        }).always( function( xhr ) {

          $spinner.css('visibility', 'hidden');

        });

      });

    });
    </script>
    <?php

  }

  public static function mywp_current_setting_screen_advance_content() {

    $setting_data = self::get_setting_data();

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Other' , 'my-wp' ); ?></h3>
    <table class="form-table">
      <tbody>
        <tr>
          <th><?php _e( 'Number of items per page:' ); ?></th>
          <td>
            <label>
              <input type="number" name="mywp[data][per_page_num]" class="per_page_num small-text" value="<?php echo esc_attr( $setting_data['per_page_num'] ); ?>" placeholder="20" />
            </label>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Add order' , 'woocommerce' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_add_new]" class="hide_add_new" value="1" <?php checked( $setting_data['hide_add_new'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Search orders' , 'woocommerce' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_search_box]" class="hide_search_box" value="1" <?php checked( $setting_data['hide_search_box'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th><?php echo _e( 'Bulk actions' ); ?></th>
          <td>
            <label>
              <input type="checkbox" name="mywp[data][hide_bulk_actions]" class="hide_bulk_actions" value="1" <?php checked( $setting_data['hide_bulk_actions'] , true ); ?> />
              <?php _e( 'Hide' ); ?>
            </label>
          </td>
        </tr>
        <tr>
          <th>
            <?php _e( 'Custom search filter' , 'my-wp' ); ?>
          </th>
          <td>
            <select name="mywp[data][custom_search_filter]" class="custom_search_filter">
              <option value="" <?php selected( false , $setting_data['custom_search_filter'] ); ?>><?php echo esc_attr( __( 'Hide' , 'my-wp' ) ); ?></option>
              <option value="1" <?php selected( true , $setting_data['custom_search_filter'] ); ?>><?php echo esc_attr( __( 'Show' , 'my-wp' ) ); ?></option>
            </select>
          </td>
        </tr>
      </tbody>
    </table>
    <p>&nbsp;</p>
    <?php

  }

  protected static function set_list_column_id() {

    self::$list_column_id = 'woocommerce_page_wc-orders';

  }

  protected static function get_list_link() {

    $list_link = add_query_arg( array( 'page' => 'wc-orders' ) , admin_url( 'admin.php' ) );

    return $list_link;

  }

  protected static function get_core_list_columns() {

    $core_list_columns = array(
      'cb' => array(
        'id' => 'cb',
        'type' => 'core',
        'title' => '<input type="checkbox" />',
        'width' => '2.2em',
      ),
      'order_number' => array(
        'id' => 'order_number',
        'type' => 'core',
        'sort' => true,
        'orderby' => 'ID',
        'title' => __( 'Order' , 'woocommerce' ),
        'width' => '20ch',
      ),
      'order_date' => array(
        'id' => 'order_date',
        'type' => 'core',
        'orderby' => 'date',
        'title' => __( 'Date' , 'woocommerce' ),
        'width' => '10ch',
      ),
      'order_status' => array(
        'id' => 'order_status',
        'type' => 'core',
        'title' => __( 'Status' , 'woocommerce' ),
        'width' => '14ch',
      ),
      'billing_address' => array(
        'id' => 'billing_address',
        'type' => 'core',
        'title' => __( 'Billing' , 'woocommerce' ),
        'width' => '20ch',
      ),
      'shipping_address' => array(
        'id' => 'shipping_address',
        'type' => 'core',
        'title' => __( 'Ship to' , 'woocommerce' ),
        'width' => '20ch',
      ),
      'order_total' => array(
        'id' => 'order_total',
        'type' => 'core',
        'sort' => true,
        'orderby' => 'order_total',
        'title' => __( 'Total' , 'woocommerce' ),
        'width' => '8ch',
      ),
      'wc_actions' => array(
        'id' => 'wc_actions',
        'type' => 'core',
        'title' => __( 'Actions' , 'woocommerce' ),
      ),
      'origin' => array(
        'id' => 'origin',
        'type' => 'core',
        'title' => __( 'Origin' , 'woocommerce' ),
      ),
    );

    return $core_list_columns;

  }

  public static function current_available_list_columns( $available_list_columns ) {

    $called_text = sprintf( '%s::%s( %s )' , __CLASS__ , __FUNCTION__ , '$available_list_columns' );

    if( empty( $available_list_columns['core']['columns'] ) ) {

      MywpHelper::error_require_message( '$available_list_columns["core"]["columns"]' , $called_text );

      return false;

    }

    $available_list_columns['woocommerce'] = array(
      'title' => __( 'WooCommerce' , 'mywp-woocommerce' ),
      'columns' => array(
        'mywp_wc_column_order_key' => array(
          'id' => 'mywp_wc_column_order_key',
          'type' => 'woocommerce',
          'title' => __( 'Order key' , 'woocommerce' ),
        ),
        'mywp_wc_column_customer_user' => array(
          'id' => 'mywp_wc_column_customer_user',
          'type' => 'woocommerce',
          'title' => __( 'Customer User' , 'woocommerce' ),
        ),
        'mywp_wc_column_payment_method' => array(
          'id' => 'mywp_wc_column_payment_method',
          'type' => 'woocommerce',
          'title' => __( 'Payment method' , 'woocommerce' ),
        ),
        'mywp_wc_column_billing_email' => array(
          'id' => 'mywp_wc_column_billing_email',
          'type' => 'woocommerce',
          'title' => __( 'Billing email' , 'woocommerce' ),
        ),
        'mywp_wc_column_billing_phone' => array(
          'id' => 'mywp_wc_column_billing_phone',
          'type' => 'woocommerce',
          'title' => __( 'Billing Phone Number' , 'woocommerce' ),
        ),
        'mywp_wc_column_shipping_phone' => array(
          'id' => 'mywp_wc_column_shipping_phone',
          'type' => 'woocommerce',
          'title' => __( 'Shipping Phone Number' , 'woocommerce' ),
        ),
      ),
    );

    $available_list_columns['other'] = array(
      'title' => __( 'Other' , 'my-wp' ),
      'columns' => array(
        'mywp_column_id' => array(
          'id' => 'mywp_column_id',
          'type' => 'other',
          'orderby' => 'ID',
          'title' => __( 'ID' , 'my-wp' ),
        ),
      ),
    );

    $default_list_columns = self::get_default_list_columns();

    $core_list_columns = self::get_core_list_columns();

    foreach( $default_list_columns['columns'] as $column_id => $column_title ) {

      if( isset( $core_list_columns[ $column_id ] ) ) {

        continue;

      }

      $available_list_column = array(
        'id' => $column_id,
        'type' => 'other',
        'title' => $column_title,
      );

      if( isset( $default_list_columns['sortables'][ $column_id ] ) ) {

        $available_list_column['sort'] = true;

      }

      $available_list_columns['other']['columns'][ $column_id ] = $available_list_column;

    }

    foreach( $available_list_columns as $group => $list_columns ) {

      foreach( $list_columns['columns'] as $column_id => $list_column ) {

        if( empty( $list_column['width'] ) ) {

          $available_list_columns[ $group ]['columns'][ $column_id ]['width'] = '12ch';

        }

      }

    }

    return $available_list_columns;

  }

  public static function mywp_current_setting_post_data_format_update( $formatted_data ) {

    $mywp_model = self::get_model();

    if( empty( $mywp_model ) ) {

      return $formatted_data;

    }

    $new_formatted_data = $mywp_model->get_initial_data();

    $new_formatted_data['advance'] = $formatted_data['advance'];

    $list_column_default = MywpControllerModuleAdminWooCommerceCotOrders::get_list_column_default();

    if( ! empty( $formatted_data['list_columns'] ) ) {

      $wp_kses_allowed_html = wp_kses_allowed_html( 'post' );

      $wp_kses_allowed_html['input'] = array(
        'type' => 1,
        'class' => 1,
        'id' => 1,
      );

      foreach( $formatted_data['list_columns'] as $list_column_id => $list_column_setting ) {

        $list_column_id = strip_tags( $list_column_id );

        $new_list_column_setting = wp_parse_args( array( 'id' => $list_column_id ) , $list_column_default );

        if( ! empty( $list_column_setting['sort'] ) ) {

          $new_list_column_setting['sort'] = true;

        }

        if( ! empty( $list_column_setting['title'] ) ) {

          $new_list_column_setting['title'] = wp_unslash( wp_kses( $list_column_setting['title'] , $wp_kses_allowed_html ) );

        }

        if( ! empty( $list_column_setting['orderby'] ) ) {

          $new_list_column_setting['orderby'] = wp_unslash( $list_column_setting['orderby'] );

        }

        if( ! empty( $list_column_setting['width'] ) ) {

          $new_list_column_setting['width'] = strip_tags( $list_column_setting['width'] );

        }

        $new_formatted_data['list_columns'][ $list_column_id ] = $new_list_column_setting;

      }

    }

    if( ! empty( $formatted_data['per_page_num'] ) ) {

      $new_formatted_data['per_page_num'] = (int) $formatted_data['per_page_num'];

    }

    if( ! empty( $formatted_data['hide_add_new'] ) ) {

      $new_formatted_data['hide_add_new'] = true;

    }

    if( ! empty( $formatted_data['hide_search_box'] ) ) {

      $new_formatted_data['hide_search_box'] = true;

    }

    if( ! empty( $formatted_data['hide_bulk_actions'] ) ) {

      $new_formatted_data['hide_bulk_actions'] = true;

    }

    if( ! empty( $formatted_data['custom_search_filter'] ) ) {

      $new_formatted_data['custom_search_filter'] = true;

    }

    return $new_formatted_data;

  }

  public static function mywp_current_setting_before_post_data_action_remove( $validated_data ) {

    self::set_list_column_id();

    self::delete_current_list_columns();

  }

}

MywpSettingScreenAdminWooCommerceCotOrders::init();

endif;
