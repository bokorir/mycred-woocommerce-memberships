<?php
/**
 * Plugin Name: myCRED: WooCommerce Memberships
 * Plugin URI: http://smarter.uk.com
 * Description: Add different amount of points for WooCommerce Memberships purchases.
 * Tags: points, mycred, woocommerce, memberships
 * Version: 1.0.0
 * Author: Robert Bokori
 * Author URI: http://smarter.uk.com
 * Requires at least: WP 3.8
 * Tested up to: WP 4.4.2
 * Text Domain: mycred-woocommerce-memberships
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
  die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mycred-woocommerce-memberships-activator.php
 */
function activate_mycred_woocommerce_memberships() {
  require_once plugin_dir_path( __FILE__ ) . 'includes/class-mycred-woocommerce-memberships-activator.php';
  myCRED_WooCommerce_Memberships_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_mycred_woocommerce_memberships' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mycred-woocommerce-memberships.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function mycred_woocommerce_memberships_init() {

  $plugin = new myCRED_WooCommerce_Memberships();

  if ( mycred_woocommerce_memberships_missing_dependencies() ) {
    add_action( 'admin_notices', 'mycred_woocommerce_memberships_show_fail_message' );

    return;
  }

  $plugin->run();
}

add_action( 'plugins_loaded', 'mycred_woocommerce_memberships_init', 99 );

/**
 * Check dependencies
 */
function mycred_woocommerce_memberships_missing_dependencies() {

  if ( ! class_exists( 'myCRED_Hook' ) ) {
    return true;
  }

  if ( ! class_exists( 'WooCommerce' ) ) {
    return true;
  }

  if ( ! class_exists( 'WC_Memberships' ) ) {
    return true;
  }

  return false;
}

/**
 * Shows an admin_notices message explaining why it couldn't be activated.
 */
function mycred_woocommerce_memberships_show_fail_message() {

  if ( ! current_user_can( 'activate_plugins' ) ) {
    return;
  }

  $missing_plugins = array();

  if ( ! class_exists( 'myCRED_Hook' ) ) {

    $title = __( 'myCRED', 'mycred-woocommerce-memberships' );

    $url = add_query_arg( array(
      'tab'       => 'plugin-information',
      'plugin'    => 'mycred',
      'TB_iframe' => 'true',
    ), admin_url( 'plugin-install.php' ) );

    array_push( $missing_plugins, array(
      'title' => $title,
      'url'   => $url
    ) );
  }

  if ( ! class_exists( 'WooCommerce' ) ) {

    $title = __( 'WooCommerce', 'mycred-woocommerce-memberships' );

    $url = add_query_arg( array(
      'tab'       => 'plugin-information',
      'plugin'    => 'woocommerce',
      'TB_iframe' => 'true',
    ), admin_url( 'plugin-install.php' ) );

    array_push( $missing_plugins, array(
      'title' => $title,
      'url'   => $url
    ) );
  }

  if ( ! class_exists( 'WC_Memberships' ) ) {

    $title = __( 'WooCommerce Memberships', 'mycred-woocommerce-memberships' );

    $url = add_query_arg( array(
      'tab'       => 'plugin-information',
      'plugin'    => 'woocommerce-memberships',
      'TB_iframe' => 'true',
    ), admin_url( 'plugin-install.php' ) );

    array_push( $missing_plugins, array(
      'title' => $title,
      'url'   => $url
    ) );
  }

  echo '<div class="error"><p>';

  printf( __( 'To begin using myCRED: WooCommerce Memberships, please install and activate the latest version of: ', 'mycred-woocommerce-memberships' ), esc_url( $url ), $title, $title );

  echo '<ul>';

  foreach ( $missing_plugins as $plugin ) {
    echo '<li>';

    printf( '<a href="%s" class="thickbox" title="%s">%s</a>', esc_url( $plugin['url'] ), $plugin['title'], $plugin['title'] );

    echo '<li>';
  }

  echo '</ul></p></div>';
}
