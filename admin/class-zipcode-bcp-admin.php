<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.presstigers.com/
 * @since      1.0.0
 *
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/admin
 * @author     PressTigers <support@presstigers.com>
 */
class Zipcode_BCP_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
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
		wp_enqueue_style( 'select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/zipcode-bcp-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
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
		wp_enqueue_script( 'select2', plugin_dir_url( __FILE__ ) . 'js/select2.full.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/zipcode-bcp-admin.js', array( 'jquery', 'select2' ), $this->version, true );
		wp_localize_script(
			$this->plugin_name,
			'frontend_ajax_object',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
			)
		);
	}

	/**
	 * Export the users for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function zbcp_export_registered_users_in_zipcode() {
		if ( ! empty( $_REQUEST['zip_code'] ) ) {

			global $wpdb;
			$table   = $wpdb->get_blog_prefix() . 'zipcode_requested_users';
			$zipcode = sanitize_text_field( $_REQUEST['zip_code'] );

			$pt_query = "SELECT * FROM $table WHERE zipcode = '$zipcode'";
			$users    = $wpdb->get_results( $wpdb->prepare( $pt_query, '' ), ARRAY_A );

			echo "email\n";
			foreach ( $users as $user ) :
				echo esc_attr($user['user_email']) . "\n";
			endforeach;
			exit();
		}
	}

	function zbcp_preview_registered_users_in_zipcode() {
		if ( ! empty( $_REQUEST['zip_code'] ) ) {
			global $wpdb;
			$table   = $wpdb->get_blog_prefix() . 'zipcode_requested_users';
			$zipcode = sanitize_text_field( $_REQUEST['zip_code'] );

			$pt_query = "SELECT * FROM $table WHERE zipcode = '$zipcode'";
			$users    = $wpdb->get_results( $wpdb->prepare( $pt_query, '' ), ARRAY_A );
			$html     = '';
			foreach ( $users as $user ) :
				$html .= '<p>' . $user['user_email'] . "</p>\n";
			endforeach;
			echo json_encode(
				array(
					'status' => true,
					'result' => $html,
				)
			);
			die();
		}
	}

	function zbcp_view_posts_registered_users_in_zipcode() {
		if ( ! empty( $_REQUEST['zip_code'] ) ) {
			global $wpdb;
			$table   = $wpdb->get_blog_prefix() . 'zipcode_requested_users';
			$zipcode = sanitize_text_field( $_REQUEST['zip_code'] );

			$pt_query = "SELECT * FROM $table WHERE zipcode = '$zipcode'";
			$users    = $wpdb->get_results( $wpdb->prepare( $pt_query, '' ), ARRAY_A );
			$html     = '<table style="border: 1px solid #333;width: 100%;">';
			$html    .= '<thead style="background: #eee;">'
					. '<tr>'
					. '<td>ID</td>'
					. '<td>Type</td>'
					. '<td>Title</td>'
					. '</tr>'
					. '</thead>';
			foreach ( $users as $user ) :
				$html .= '<tr>'
					. '<td>' . $user['post_id'] . '</td>'
					. '<td>' . $user['post_type'] . '</td>'
					. '<td> <a href="' . esc_url( get_the_permalink( $user['post_id'] ) ) . '" target="_blank">' . esc_attr( get_the_title( $user['post_id'] ) ) . '</a></td>'
					. '</tr>';
			endforeach;
			$html .= '</table>';
			echo json_encode(
				array(
					'status' => true,
					'result' => $html,
				)
			);
			die();
		}
	}

	/**
	 * Insert the users for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function is_valid_zip_code( $zip_codes ) {
		return ( preg_match( '/^[a-zA-Z0-9]{4,8}(-[0-9]{4})?$/', $zip_codes ) ) ? true : false;
	}
	public function zbcp_insert_zipcode_into_database() {
		global $wpdb;
		// Place all user submitted values in an array (or empty
		// strings if no value was sent)
		$zipcode_data = array();
		if ( isset( $_POST['zipcode'] ) ) {
			$is_valid_format = $this->is_valid_zip_code( sanitize_text_field( $_POST['zipcode'] ) );
			if ( $is_valid_format ) {
				$zipcode = sanitize_text_field( $_POST['zipcode'] );
				$table   = $wpdb->get_blog_prefix() . 'zipcode_serving';
				$count   = $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE zipcode = '$zipcode'" );

				if ( $count == 0 ) {
					$zipcode_data['zipcode'] = sanitize_text_field( $_POST['zipcode'] );
					$res                     = $wpdb->insert( $wpdb->get_blog_prefix() . 'zipcode_serving', $zipcode_data );
					if ( $res ) {
						echo json_encode(
							array(
								'status' => true,
								'result' => __(
									'Zipcode has been Inserted',
									'zipcode-bcp'
								),
							)
						);
						die();
					} else {
						echo json_encode(
							array(
								'status' => false,
								'result' => __(
									'There is an error please try again.',
									'zipcode-bcp'
								),
							)
						);
						die();
					}
				} else {
					echo json_encode(
						array(
							'status' => false,
							'result' => __(
								'Dublicate entry, please try a different one.',
								'zipcode-bcp'
							),
						)
					);
					die();
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
				die();
			} // Endif for  zbcp_insert_zipcode_into_database Function
		} else {
			echo json_encode(
				array(
					'status' => false,
					'result' => __(
						'Plese enter zipcode',
						'zipcode-bcp'
					),
				)
			);
			die();
		}

		exit;
	}

	/**
	 * Insert the users for the admin area.
	 *
	 * @since    1.0.0
	 */

	public function zbcp_insert_multiple_zipcode_into_database() {
		global $wpdb;
		// Place all user submitted values in an array (or empty
		// strings if no value was sent)

		$zipcode_data = array();
		$count        = 0;
		if ( isset( $_POST['zipCodesArray'] ) && is_array( $_POST['zipCodesArray'] ) ) {

			$ziparrays = array_map( 'sanitize_text_field', $_POST['zipCodesArray'] );
			foreach ( $ziparrays as $key => $zipcode ) {
				$szipcode = explode( ':', $zipcode );
				$szipcode = sanitize_text_field( $szipcode[0] );
				
				$table  = $wpdb->get_blog_prefix() . 'zipcode_serving';
				$count += $wpdb->get_var( "SELECT COUNT(*) FROM $table WHERE zipcode = '$szipcode'" );
			}
			if ( $count == 0 ) {
				foreach ( $ziparrays as $key => $zipcode ) {
					$szipcode                 = explode( ':', $zipcode );
					$zipcode_data['zipcode']  = sanitize_text_field( $szipcode[0] );
					$zipcode_data['services'] = sanitize_text_field( $szipcode[2] ) . '-' . sanitize_text_field( $szipcode[1] );

					$res = $wpdb->insert( $wpdb->get_blog_prefix() . 'zipcode_serving', $zipcode_data );
				}
				if ( $res ) {
					echo json_encode(
						array(
							'status' => true,
							'result' => __(
								'Zipcode has been Inserted',
								'zipcode-bcp'
							),
						)
					);
					die();
				} else {
					echo json_encode(
						array(
							'status' => false,
							'result' => __(
								'There is an error please try again.',
								'zipcode-bcp'
							),
						)
					);
					die();
				}
			} else {
				echo json_encode(
					array(
						'status' => false,
						'result' => __(
							'Dublicate entry, please try a different one.',
							'zipcode-bcp'
						),
					)
				);
				die();
			}
		} else {
			echo json_encode(
				array(
					'status' => false,
					'result' => __(
						'Plese enter a valid zipcode',
						'zipcode-bcp'
					),
				)
			);
			die();
		}

		exit;
	}

	/**
	 * Insert the users for the admin area.
	 *
	 * @since    1.0.0
	 */
	function zbcp_get_zipcode_from_api() {
		if ( isset( $_POST['usa_state'] ) ) {
			$usa_state = sanitize_text_field( $_POST['usa_state'] );
		}

		if ( isset( $usa_state ) ) {

			$string   = file_get_contents( plugin_dir_url( __FILE__ ) . 'zipcodes/USCities.json' );
			$json_a   = json_decode( $string, true );
			$zipcodes = array();

			foreach ( $json_a as $key => $obj ) {
				if ( $obj['state'] == $usa_state ) {
					array_push( $zipcodes, $obj );
				}
			}
			echo json_encode(
				array(
					'status' => true,
					'result' => __(
						$zipcodes,
						'zipcode-bcp'
					),
				)
			);
			die();
		} else {
			echo json_encode(
				array(
					'status' => false,
					'result' => __(
						'Plese enter a select a state',
						'zipcode-bcp'
					),
				)
			);
			die();
		}
	}

}
