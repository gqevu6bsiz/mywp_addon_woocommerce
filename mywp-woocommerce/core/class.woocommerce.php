<?php

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpWooCommerceWC' ) ) :

final class MywpWooCommerceWC {

  static function is_enabled_cot() {

    $CustomOrdersTableController = wc_get_container()->get( CustomOrdersTableController::class );

    if( ! is_object( $CustomOrdersTableController ) ) {

      return false;

    }

    if( ! method_exists( $CustomOrdersTableController , 'custom_orders_table_usage_is_enabled' ) ) {

      return false;

    }

    return wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled();

  }

}

endif;
