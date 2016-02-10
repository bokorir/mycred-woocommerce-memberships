<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    myCRED_WooCommerce_Memberships
 * @subpackage myCRED_WooCommerce_Memberships/includes
 * @author     Robert Bokori <robert@smarter.uk.com>
 */
class myCRED_WooCommerce_Memberships {

  /**
   * The loader that's responsible for maintaining and registering all hooks that power
   * the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      myCRED_WooCommerce_Memberships_Loader $loader Maintains and registers all hooks for the plugin.
   */
  protected $loader;

  /**
   * The unique identifier of this plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $plugin_name The string used to uniquely identify this plugin.
   */
  protected $plugin_name;

  /**
   * The current version of the plugin.
   *
   * @since    1.0.0
   * @access   protected
   * @var      string $version The current version of the plugin.
   */
  protected $version;

  /**
   * Define the core functionality of the plugin.
   *
   * Set the plugin name and the plugin version that can be used throughout the plugin.
   * Load the dependencies, define the locale, and set the hooks for the admin area and
   * the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function __construct() {

    $this->plugin_name = 'mycred-woocommerce-memberships';

    $this->version = '1.0.0';

    $this->load_dependencies();

    $this->set_locale();

    $this->define_admin_hooks();

  }

  /**
   * Load the required dependencies for this plugin.
   *
   * Include the following files that make up the plugin:
   *
   * - myCRED_WooCommerce_Memberships_Loader. Orchestrates the hooks of the plugin.
   * - myCRED_WooCommerce_Memberships_i18n. Defines internationalization functionality.
   * - myCRED_WooCommerce_Memberships_Admin. Defines all hooks for the admin area.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function load_dependencies() {

    /**
     * The class responsible for orchestrating the actions and filters of the
     * core plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mycred-woocommerce-memberships-loader.php';

    /**
     * The class responsible for defining internationalization functionality
     * of the plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mycred-woocommerce-memberships-i18n.php';

    /**
     * The class hooks into the myCRED plugin.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mycred-woocommerce-memberships-hook.php';

    /**
     * The class responsible for defining all actions that occur in the admin area.
     */
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mycred-woocommerce-memberships-admin.php';

    $this->loader = new myCRED_WooCommerce_Memberships_Loader();

  }

  /**
   * Define the locale for this plugin for internationalization.
   *
   * Uses the myCRED_WooCommerce_Memberships_i18n class in order to set the domain and to register the hook
   * with WordPress.
   *
   * @since    1.0.0
   * @access   private
   */
  private function set_locale() {

    $plugin_i18n = new myCRED_WooCommerce_Memberships_i18n();

    $plugin_i18n->set_domain( $this->get_plugin_name() );

    $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

  }

  /**
   * Register all of the hooks related to the admin area functionality
   * of the plugin.
   *
   * @since    1.0.0
   * @access   private
   */
  private function define_admin_hooks() {

    $plugin_admin = new myCRED_WooCommerce_Memberships_Admin( $this->get_plugin_name(), $this->get_version() );

    /**
     * Register the myCRED Hook details
     */
    $this->loader->add_action( 'mycred_setup_hooks', $plugin_admin, 'mycred_wcm_register_hook', 10 );

    /**
     * Register myCRED references
     */
    $this->loader->add_action( 'mycred_all_references', $plugin_admin, 'mycred_wcm_register_references', 10 );

  }

  /**
   * Run the loader to execute all of the hooks with WordPress.
   *
   * @since    1.0.0
   */
  public function run() {
    $this->loader->run();
  }

  /**
   * The name of the plugin used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   *
   * @since     1.0.0
   * @return    string    The name of the plugin.
   */
  public function get_plugin_name() {
    return $this->plugin_name;
  }

  /**
   * The reference to the class that orchestrates the hooks with the plugin.
   *
   * @since     1.0.0
   * @return    myCRED_WooCommerce_Memberships_Loader    Orchestrates the hooks of the plugin.
   */
  public function get_loader() {
    return $this->loader;
  }

  /**
   * Retrieve the version number of the plugin.
   *
   * @since     1.0.0
   * @return    string    The version number of the plugin.
   */
  public function get_version() {
    return $this->version;
  }
}
