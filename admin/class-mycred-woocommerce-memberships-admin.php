<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    myCRED_WooCommerce_Memberships
 * @subpackage myCRED_WooCommerce_Memberships/admin
 * @author     Robert Bokori <robert@smarter.uk.com>
 */
class myCRED_WooCommerce_Memberships_Admin {

  private $plugin_name;
  private $version;

  public function __construct( $plugin_name, $version ) {

    $this->plugin_name = $plugin_name;

    $this->version = $version;
  }

  /**
   * Register custom hook
   *
   * @param array $installed array with installed hooks
   *
   * @return array $installed array with our new hook added
   */
  public function mycred_wcm_register_hook( $installed ) {

    $installed['mycred_woocommerce_memberships'] = array(
      'title'       => __( 'WooCommerce Memberships', $this->plugin_name ),
      'description' => __( 'Award points for users who bought a membership plan on your website.', $this->plugin_name ),
      'callback'    => array( 'myCRED_WooCommerce_Memberships_Hook' )
    );

    return $installed;
  }

  /**
   * Add references
   *
   * @param array $references array of all myCRED references
   *
   * @return array $references array with our new references
   */
  public function mycred_wcm_register_references( $references ) {

    $references['mycred_woocommerce_memberships'] = __( 'Membership', $this->plugin_name );

    $membership_plans = wc_memberships_get_membership_plans();

    if ( ! empty( $membership_plans ) ) {
      foreach ( $membership_plans as $membership_plan ) {
        $references[ 'mycred_woocommerce_memberships_plan_' . str_replace( '-', '_', $membership_plan->get_slug() ) ] = sprintf( __( 'Membership: %s', $this->plugin_name ), $membership_plan->get_name() );
      }
    }

    return $references;
  }
}
