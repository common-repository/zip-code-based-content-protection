<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.presstigers.com/
 * @since      1.0.0
 *
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/public
 * @author     PressTigers <support@presstigers.com>
 */
class Zipcode_BCP_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function zbcp_enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zipcode_BCP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zipcode_BCP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zipcode-bcp-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function zbcp_enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Zipcode_BCP_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Zipcode_BCP_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zipcode-bcp-public.js', array( 'jquery' ), $this->version, false );
		$options                      = get_option( 'cdzc_settings_no_zipcode_added' );
		$cdzc_settings_no_valid_email = get_option( 'cdzc_settings_no_valid_email' );
		wp_localize_script(
			$this->plugin_name,
			'frontend_ajax_object',
			array(
				'ajaxurl'                        => admin_url( 'admin-ajax.php' ),
				'cdzc_settings_no_zipcode_added' => __( $options, 'zipcode-bcp' ),
				'cdzc_settings_no_valid_email'   => __( $cdzc_settings_no_valid_email, 'zipcode-bcp' ),
			)
		);
	}

	/**
	 * Filtering the content
	 *
	 * @since    1.0.0
	 */
	public function zbcp_filter_page_and_post_content() {

		if ( is_singular() ) {
			global $post;
			$post_id                   = $post->ID;
			$activate_zipcode_checkbox = get_post_meta( $post_id, 'activate_zipcode_checkbox', true );
			$screens                   = get_option( 'cdzc_settings_for_checkbox' );
			$post_type                 = get_post_type( $post_id );
			if ( in_array( $post_type, $screens ) && $activate_zipcode_checkbox == 1 && ! isset( $_COOKIE[ 'zipcode-view-content-' . $post_id ] ) ) {
				$url = site_url() . '/zipcode-bcp/redirect/zipcode/?id=' . $post_id . '';
				wp_safe_redirect( $url, '302' );
				exit();
			}
		}
	}

	/**
	 * Function for setting the rule.
	 *
	 * @since    1.0.0
	 */
	public function zbcp_rewrite_rule() {
		add_rewrite_rule( '^zipcode-bcp/redirect/zipcode/?$', 'index.php?pg=zipcode-bcp', 'top' );
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	/**
	 * Function for adding the query vars for the plugin.
	 *
	 * @since    1.0.0
	 */
	public function zbcp_register_query_var( $vars ) {
		$vars[] = 'pg';
		$vars[] = 'id';
		return $vars;
	}

	/**
	 * Function for loading the template.
	 *
	 * @since    1.0.0
	 */
	public function zbcp_load_templates() {

		if ( get_query_var( 'pg' ) ) {
			if ( get_query_var( 'pg' ) == 'zipcode-bcp' ) {
				add_filter(
					'template_include',
					function() {
						return plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/zipcode-bcp-public-display.php';
					}
				);
				return;
			}
		}
	}

	/**
	 * function for handling the ajax request of zipcode search form
	 *
	 * @since    1.0.0
	 */
	public function is_valid_zip_code( $zip_codes ) {
			return ( preg_match( '/^[a-zA-Z0-9]{4,8}(-[0-9]{4})?$/', $zip_codes ) ) ? true : false;
	}
	public function check_zipcode_from_post_page_meta() {

		$post_id                              = sanitize_text_field( $_POST['post_id'] );
		$zipcode                              = sanitize_text_field( $_POST['zipcode'] );
		$post_page_zipcode_select             = get_post_meta( $post_id, 'post_page_zipcode_select', true );
		$cdzc_settings_not_providing_services = get_option( 'cdzc_settings_not_providing_services' );
		$cdzc_settings_no_zipcode_added       = get_option( 'cdzc_settings_no_zipcode_added' );
		$is_valid_format                        = $this->is_valid_zip_code( sanitize_text_field( $_POST['zipcode'] ) );
		if ( $is_valid_format ) {
			if ( isset( $_POST['zipcode'] ) ) {
				if ( in_array( $zipcode, $post_page_zipcode_select ) ) {
					$cookie_name  = 'zipcode-view-content-' . $post_id;
					$cookie_value = $zipcode . '-' . $post_id;
					setcookie( $cookie_name, $cookie_value, time() + ( 86400 * 30 ), '/' ); // 86400 = 1 day
					$url = get_the_permalink( $post_id );
					echo json_encode(
						array(
							'status'     => true,
							'showSearch' => false,
							'result'     => $url,
						)
					);
				} else {
					global $wpdb;
					$prefix = $wpdb->get_blog_prefix();
					// Check someone already requested for this ZIP Code
					$table = $prefix . 'zipcode_requested';
					$count = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE zipcode = '$zipcode'" );

					// Already requested by someone else
					if ( $this->zbcp_is_cookies_exist( 'is_requested', $zipcode ) != 2 ) {
						// User not requested for this Zip code
						$this->zbcp_set_ip_cookies( 'is_requested', $zipcode );
						if ( $count ) {

							$pt_query  = "SELECT * FROM $table WHERE zipcode = '$zipcode'";
							$zip_codes = $wpdb->get_results( $wpdb->prepare( $pt_query, '' ), ARRAY_A );

							$zip_codes     = $zip_codes[0];
							$request_count = $zip_codes['request_count'];
							$request_count++;
							$zip_codes['request_count'] = $request_count;
							$wpdb->update( $wpdb->get_blog_prefix() . 'zipcode_requested', $zip_codes, array( 'id' => $zip_codes['id'] ) );
						} else {

							$zipcode_data                  = array();
							$zipcode_data['zipcode']       = $zipcode;
							$zipcode_data['request_count'] = 1;
							$wpdb->insert( $wpdb->get_blog_prefix() . 'zipcode_requested', $zipcode_data );
						}
					}
					echo json_encode(
						array(
							'status'     => false,
							'showSearch' => true,
							'result'     => __(
								$cdzc_settings_not_providing_services,
								'zipcode-bcp'
							),
						)
					);
				}
			} else {
				echo json_encode(
					array(
						'status'     => false,
						'showSearch' => false,
						'result'     => __(
							$cdzc_settings_no_zipcode_added,
							'zipcode-bcp'
						),
					)
				);
			}
		} else {
			echo json_encode(
				array(
					'status' => false,
					'result' => __(
						'Invalid Format',
						'zipcode-bcp'
					),
				)
			);
		}
		die();
	}

	// set cookies with IP
	public function zbcp_set_ip_cookies( $ip, $zip ) {
		$cookie_expire_on = time() + 60 * 60 * 24 * 30;
		$cookies_exist    = $this->zbcp_is_cookies_exist( $ip, $zip );

		if ( $cookies_exist == 0 ) {
			setcookie( $ip, $zip, $cookie_expire_on, '/' );
		} elseif ( $cookies_exist == 1 ) {
			$zips = sanitize_text_field( $_COOKIE[ $ip ] );
			$zips = $zips . ',' . $zip;
			setcookie( $ip, $zips, $cookie_expire_on, '/' );
		}
	}

	/**
	 * Check cookies already exist or not
	 * 0 - Not exit
	 * 1 - Exist but not added Ip
	 * 2 - Exist with IP
	 *
	 * @since    1.0.0
	 */
	public function zbcp_is_cookies_exist( $ip, $zip ) {

		if ( ! isset( $_COOKIE[ $ip ] ) ) {
			return 0;
		} else {
			$ips      = array_map( 'sanitize_text_field', $_COOKIE[ $ip ] );
			$zipcodes = explode( ',', $ips );
			foreach ( $zipcodes as $zipcode ) {
				if ( $zipcode == $zip ) {
					return 2;
				}
			}
			return 1;
		}
	}

	/**
	 * Function for validation of zipcode
	 *
	 * @since    1.0.0
	 */


	/**
	 * function for handling the ajax request of submit email form
	 *
	 * @since    1.0.0
	 */
	public function submit_email_against_zipcode() {

		$email      = sanitize_email( $_POST['email'] );
		$zipcode    = sanitize_text_field( $_POST['zipcode'] );
		$post_id    = sanitize_text_field( $_POST['post_id'] );
		$post_type  = sanitize_text_field( $_POST['post_type'] );
		$post_title = sanitize_title( $_POST['post_title'] );

		$cdzc_settings_not_providing_services = get_option( 'cdzc_settings_not_providing_services' );
		$cdzc_settings_no_zipcode_added       = get_option( 'cdzc_settings_no_zipcode_added' );

		$cdzc_settings_user_already_requested = get_option( 'cdzc_settings_user_already_requested' );
		$cdzc_settings_thankyou_for_email     = get_option( 'cdzc_settings_thankyou_for_email' );

		global $wpdb;
		$prefix = $wpdb->get_blog_prefix();
		$table  = $prefix . 'zipcode_requested_users';

		$count = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE user_email = '$email' AND zipcode = $zipcode" );

		if ( $count ) {
			echo json_encode(
				array(
					'status' => false,
					'result' => __(
						$cdzc_settings_user_already_requested,
						'zipcode-bcp'
					),
				)
			);
			die();
		} else {
			$zipcode_data               = array();
			$zipcode_data['zipcode']    = $zipcode;
			$zipcode_data['user_email'] = $email;
			$zipcode_data['post_id']    = $post_id;
			$zipcode_data['post_type']  = $post_type;
			$zipcode_data['post_title'] = $post_title;

			$result = $wpdb->insert( $wpdb->get_blog_prefix() . 'zipcode_requested_users', $zipcode_data );
			echo json_encode(
				array(
					'status' => true,
					'result' => __(
						$cdzc_settings_thankyou_for_email,
						'zipcode-bcp'
					),
				)
			);
			die();
		}
		die();
	}

}
