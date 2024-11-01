<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.presstigers.com/
 * @since      1.0.0
 *
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/includes
 */

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
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/includes
 * @author     PressTigers <support@presstigers.com>
 */
class Zipcode_BCP {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Zipcode_BCP_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
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
		if ( defined( 'ZIPCODE_BCP' ) ) {
			$this->version = ZIPCODE_BCP;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'zipcode-bcp';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Zipcode_BCP_Loader. Orchestrates the hooks of the plugin.
	 * - Zipcode_BCP_i18n. Defines internationalization functionality.
	 * - Zipcode_BCP_Admin. Defines all hooks for the admin area.
	 * - Zipcode_BCP_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-zipcode-bcp-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-zipcode-bcp-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/lists/class-zipcode-bcp-admin-zipcodes-list.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/lists/class-zipcode-bcp-admin-requested-zipcodes.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/lists/class-zipcode-bcp-admin-user-requested-zipcodes.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/settings/class-zipcode-bcp-admin-settings.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/metas/class-zipcode-bcp-admin-post-page-meta.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-zipcode-bcp-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-zipcode-bcp-public.php';

		$this->loader = new Zipcode_BCP_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Zipcode_BCP_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Zipcode_BCP_i18n();

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

		$plugin_admin = new Zipcode_BCP_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'zbcp_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'zbcp_enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_export_registered_users_in_zipcode', $plugin_admin, 'zbcp_export_registered_users_in_zipcode' );
		$this->loader->add_action( 'wp_ajax_preview_registered_users_in_zipcode', $plugin_admin, 'zbcp_preview_registered_users_in_zipcode' );
		$this->loader->add_action( 'wp_ajax_view_posts_registered_users_in_zipcode', $plugin_admin, 'zbcp_view_posts_registered_users_in_zipcode' );
		$this->loader->add_action( 'wp_ajax_insert_zipcode_into_database', $plugin_admin, 'zbcp_insert_zipcode_into_database' );
		$this->loader->add_action( 'wp_ajax_insert_multiple_zipcode_into_database', $plugin_admin, 'zbcp_insert_multiple_zipcode_into_database' );
		$this->loader->add_action( 'wp_ajax_get_zipcode_from_api', $plugin_admin, 'zbcp_get_zipcode_from_api' );

		$register_admin_settings = new Zipcode_BCP_Admin_Settings();
		$this->loader->add_action( 'admin_menu', $register_admin_settings, 'zbcp_add_plugin_menu', 100 );
		$this->loader->add_action( 'admin_init', $register_admin_settings, 'zbcp_initialize_plugin_options' );

		$register_admin_metas = new Zipcode_BCP_Admin_Post_Page_Meta();
		$this->loader->add_action( 'add_meta_boxes', $register_admin_metas, 'zbcp_post_and_page_meta_box' );
		$this->loader->add_action( 'save_post', $register_admin_metas, 'zbcp_save_post_and_page_meta_box' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Zipcode_BCP_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'zbcp_enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'zbcp_enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_check_zipcode_from_post_page_meta', $plugin_public, 'check_zipcode_from_post_page_meta' );
		$this->loader->add_action( 'wp_ajax_nopriv_check_zipcode_from_post_page_meta', $plugin_public, 'check_zipcode_from_post_page_meta' );
		$this->loader->add_action( 'wp_ajax_submit_email_against_zipcode', $plugin_public, 'submit_email_against_zipcode' );
		$this->loader->add_action( 'wp_ajax_nopriv_submit_email_against_zipcode', $plugin_public, 'submit_email_against_zipcode' );
		$this->loader->add_action( 'init', $plugin_public, 'zbcp_rewrite_rule', 11 );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'zbcp_register_query_var' );
		$this->loader->add_filter( 'template_redirect', $plugin_public, 'zbcp_load_templates' );
		$this->loader->add_filter( 'template_redirect', $plugin_public, 'zbcp_filter_page_and_post_content' );
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
	 * @return    Zipcode_BCP_Loader    Orchestrates the hooks of the plugin.
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
