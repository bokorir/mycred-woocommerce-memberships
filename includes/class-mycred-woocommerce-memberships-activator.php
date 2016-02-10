<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    myCRED_WooCommerce_Memberships
 * @subpackage myCRED_WooCommerce_Memberships/includes
 * @author     Robert Bokori <robert@smarter.uk.com>
 */
class myCRED_WooCommerce_Memberships_Activator {

  public static function activate() {

    if ( ! class_exists( 'myCRED_Hook' ) ) {
      exit( "Plugin myCRED is required by myCRED: WooCommerce Memberships" );
    }

    if ( ! class_exists( 'WooCommerce' ) ) {
      exit( "Plugin WooCommerce is required by myCRED: WooCommerce Memberships" );
    }

    if ( ! class_exists( 'WC_Memberships' ) ) {
      exit( "Plugin WooCommerce Memberships is required by myCRED: WooCommerce Memberships" );
    }

  }

}
