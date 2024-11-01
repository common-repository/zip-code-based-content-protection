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
class Zipcode_BCP_Admin_Settings {


	/**
	 * Function for adding setting menu in WordPress dashboard
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return void
	 */
	public function zbcp_add_plugin_menu() {

		add_submenu_page(
			'zbcp',
			__( 'Settings', 'zipcode-bcp' ),
			__( 'Settings', 'zipcode-bcp' ),
			'administrator',
			'zbcp-settings',
			array( $this, 'zbcp_menu_page_display' ),
			30
		);
	}

	/**
	 * Function for adding the settings page HTML
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return void
	 */
	public function zbcp_menu_page_display() {
		?>
		<div class="wrap">

			<div id="icon-themes" class="icon32"></div>
			<h2><?php esc_html_e( 'Settings', 'zipcode-bcp' ); ?></h2>
			<?php settings_errors(); ?>
			<?php
			$default    = 'zipcode-bcp';
			$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : sanitize_text_field( $default );
			?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=zbcp_settings&tab=ncm_general" class="nav-tab <?php echo $active_tab == 'zipcode-bcp' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'General', 'zipcode-bcp' ); ?></a>
			</h2>
			<form method="post" action="options.php" class="zbcp_admin_settings_form" enctype="multipart/form-data">
				<?php
				settings_fields( 'zbcp_plugin_general_display_options' );
				do_settings_sections( 'zbcp_plugin_general_display_options' );
				?>
				<?php submit_button(); ?>
			</form>

		</div><!-- /.wrap -->
		<?php
	}

	/**
	 * Function for initializing plugin settings
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return void
	 */
	public function zbcp_initialize_plugin_options() {

		if ( false == get_option( 'zbcp_plugin_general_display_options' ) ) {
			add_option( 'zbcp_plugin_general_display_options' );
		}

		// Gerenal settings
		register_setting( 'zbcp_plugin_general_display_options', 'cdzc_settings_requested_zipcode_highlight' );
		register_setting( 'zbcp_plugin_general_display_options', 'cdzc_settings_no_zipcode_added' );
		register_setting( 'zbcp_plugin_general_display_options', 'cdzc_settings_no_valid_email' );
		register_setting( 'zbcp_plugin_general_display_options', 'cdzc_settings_not_providing_services' );
		register_setting( 'zbcp_plugin_general_display_options', 'cdzc_settings_user_already_requested' );
		register_setting( 'zbcp_plugin_general_display_options', 'cdzc_settings_thankyou_for_email' );
		register_setting( 'zbcp_plugin_general_display_options', 'cdzc_settings_frontend_description' );
		register_setting( 'zbcp_plugin_general_display_options', 'cdzc_settings_for_checkbox' );

		add_settings_section( 'zbcp_general_settings_section', 'General Settings', array( $this, 'zbcp_general_settings_options_callback' ), 'zbcp_plugin_general_display_options' );

		add_settings_field( 'cdzc_settings_requested_zipcode_highlight', 'Highlight Number of Requests Based on this Value', array( $this, 'cdzc_settings_requested_zipcode_highlight_callback' ), 'zbcp_plugin_general_display_options', 'zbcp_general_settings_section', array( '' ) );
		add_settings_field( 'cdzc_settings_no_zipcode_added', 'No ZIP Code Added Message', array( $this, 'zbcp_gerenal_settings_no_zipcode_added_callback' ), 'zbcp_plugin_general_display_options', 'zbcp_general_settings_section', array( '' ) );
		add_settings_field( 'cdzc_settings_no_valid_email', 'No Valid Email Message', array( $this, 'cdzc_settings_no_valid_email_callback' ), 'zbcp_plugin_general_display_options', 'zbcp_general_settings_section', array( '' ) );
		add_settings_field( 'cdzc_settings_not_providing_services', 'Not Providing Services Message', array( $this, 'cdzc_settings_not_providing_services_callback' ), 'zbcp_plugin_general_display_options', 'zbcp_general_settings_section', array( '' ) );
		add_settings_field( 'cdzc_settings_user_already_requested', 'User Already Requested', array( $this, 'cdzc_settings_user_already_requested_callback' ), 'zbcp_plugin_general_display_options', 'zbcp_general_settings_section', array( '' ) );
		add_settings_field( 'cdzc_settings_thankyou_for_email', 'Subscribing to newsletter', array( $this, 'cdzc_settings_thankyou_for_email_callback' ), 'zbcp_plugin_general_display_options', 'zbcp_general_settings_section', array( '' ) );
		add_settings_field( 'cdzc_settings_frontend_description', 'Frontend Description', array( $this, 'cdzc_settings_frontend_description_callback' ), 'zbcp_plugin_general_display_options', 'zbcp_general_settings_section', array( '' ) );
		add_settings_field( 'cdzc_settings_for_checkbox', 'Select Post Types for ZBCP Feature Activation?', array( $this, 'cdzc_settings_for_checkbox_callback' ), 'zbcp_plugin_general_display_options', 'zbcp_general_settings_section', array( '' ) );
	}

	/**
	 * Function for showing the text above the general settings section
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return html
	 */
	public function zbcp_general_settings_options_callback() {
	 	echo '<p>'.esc_html__( 'ZIP Code Based Content Protection Plugin Settings.', 'zipcode-bcp' ).'</p>';
	}

	/**
	 * Function for showing the gerenal settings no zipcode highlighted
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return html
	 */
	public function cdzc_settings_requested_zipcode_highlight_callback() {
		?>
		<?php $cdzc_settings_requested_zipcode_highlight = get_option( 'cdzc_settings_requested_zipcode_highlight' ); ?>
		<input type="number" id="cdzc_settings_requested_zipcode_highlight" min="0" style="width: 400px" name="cdzc_settings_requested_zipcode_highlight" value="<?php echo esc_attr( $cdzc_settings_requested_zipcode_highlight ); ?>" />
		<?php
	}

	/**
	 * Function for showing the gerenal settings no zipcode added field
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return html
	 */
	public function zbcp_gerenal_settings_no_zipcode_added_callback() {
		?>
		<?php $cdzc_settings_no_zipcode_added = get_option( 'cdzc_settings_no_zipcode_added' ); ?>
		<textarea id="cdzc_settings_no_zipcode_added" style="width: 400px" name="cdzc_settings_no_zipcode_added"><?php echo esc_textarea( $cdzc_settings_no_zipcode_added ); ?></textarea>
		<?php
	}

	/**
	 * Function for showing the gerenal settings no valid email
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return html
	 */
	public function cdzc_settings_no_valid_email_callback() {
		?>
		<?php $cdzc_settings_no_valid_email = get_option( 'cdzc_settings_no_valid_email' ); ?>
		<textarea id="cdzc_settings_no_valid_email" style="width: 400px" name="cdzc_settings_no_valid_email"><?php echo esc_textarea( $cdzc_settings_no_valid_email ); ?></textarea>
		<?php
	}

	/**
	 * Function for showing the gerenal settings no providing services field
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return html
	 */
	public function cdzc_settings_not_providing_services_callback() {
		?>
		<?php $cdzc_settings_not_providing_services = get_option( 'cdzc_settings_not_providing_services' ); ?>
		<textarea id="cdzc_settings_not_providing_services" style="width: 400px" name="cdzc_settings_not_providing_services"><?php echo esc_textarea( $cdzc_settings_not_providing_services ); ?></textarea>
		<?php
	}

	/**
	 * Function for showing the gerenal settings user already requested field
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return html
	 */
	public function cdzc_settings_user_already_requested_callback() {
		?>
		<?php $cdzc_settings_user_already_requested = get_option( 'cdzc_settings_user_already_requested' ); ?>
		<textarea id="cdzc_settings_user_already_requested" style="width: 400px" name="cdzc_settings_user_already_requested"><?php echo esc_textarea( $cdzc_settings_user_already_requested ); ?></textarea>
		<?php
	}

	/**
	 * Function for showing the gerenal settings user already requested field
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return html
	 */
	public function cdzc_settings_thankyou_for_email_callback() {
		?>
		<?php $cdzc_settings_thankyou_for_email = get_option( 'cdzc_settings_thankyou_for_email' ); ?>
		<textarea id="cdzc_settings_thankyou_for_email" style="width: 400px" name="cdzc_settings_thankyou_for_email"><?php echo esc_textarea( $cdzc_settings_thankyou_for_email ); ?></textarea>
		<?php
	}

	/**
	 * Function for showing the frontend user description
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return html
	 */
	public function cdzc_settings_frontend_description_callback() {
		?>
		<?php $cdzc_settings_frontend_description = get_option( 'cdzc_settings_frontend_description' ); ?>
		<textarea id="cdzc_settings_frontend_description" style="width: 400px" name="cdzc_settings_frontend_description"><?php echo esc_textarea( $cdzc_settings_frontend_description ); ?></textarea>
		<?php
	}

	/**
	 * Function for showing the checkbox field
	 *
	 * @since  1.0.0
	 * @param  void
	 * @return html
	 */
	public function cdzc_settings_for_checkbox_callback() {
		// getting default post type names
		$get_default_post_types = array(
			'public'   => true,
			'_builtin' => true,
		);

		// getting custom pot type names
		$get_custom_post_types = array(
			'public'   => true,
			'_builtin' => false,
		);

		$post_types = array();

		/**
		 * merging both arrays of post type names into one.
		 */
		$default_post_types = get_post_types( $get_default_post_types, 'object' );
		foreach ( $default_post_types as $post_key => $post_val ) {
			$post_types[ $post_val->name ] = $post_val->label;
		}

		$default_post_types = get_post_types( $get_custom_post_types, 'object' );
		foreach ( $default_post_types as $post_key => $post_val ) {
			$post_types[ $post_val->name ] = $post_val->label;
		}

		$check_boxes_array = get_option( 'cdzc_settings_for_checkbox' );
		// checking if returned array is empty or not.
		if ( empty( $check_boxes_array ) ) {
			$check_boxes_array = array();
		}
		?>
		<?php
		/**
		 * displaying checkboxes
		 */
		$ptcount = 1;
		$id_chkb = 'cdzc_settings_for_checkbox';
		foreach ( $post_types as $post_key => $post_val ) {

			// if post type is present in post_titles array i.e., if user has marked this post type in plugin settings page
			if ( in_array( $post_key, $check_boxes_array ) ) {

				// displaying checked checkboxes
				?>
				<input type="checkbox" id="<?php echo esc_attr( $id_chkb . $ptcount ); ?>" name="cdzc_settings_for_checkbox[]" value="<?php echo esc_attr( $post_key ); ?>"checked>
				<label for="<?php echo esc_attr( $id_chkb . $ptcount ); ?>"><?php esc_html_e( $post_val ); ?></label>
				<br>
				<?php
			}

			// displaying unchecked checkboxes
			else {
				?>
				<input type="checkbox" id="<?php echo esc_attr( $id_chkb . $ptcount ); ?>" name="cdzc_settings_for_checkbox[]" value="<?php echo esc_attr( $post_key ); ?>">
				<label for="<?php echo esc_attr( $id_chkb . $ptcount ); ?>"><?php esc_html_e( $post_val ); ?></label>
				<br>
				<?php
			}
			$ptcount++;
		}
	}

}
