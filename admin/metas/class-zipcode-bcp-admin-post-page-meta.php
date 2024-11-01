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
class Zipcode_BCP_Admin_Post_Page_Meta {


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
	}
	/**
	 * Initialize and add the metabox on the selected screens
	 *
	 * @since    1.0.0
	 */
	public function zbcp_post_and_page_meta_box() {
		$cdzc_settings_for_checkbox = get_option( 'cdzc_settings_for_checkbox' );
		$screens                    = $cdzc_settings_for_checkbox;

		foreach ( $screens as $screen ) {
			add_meta_box(
				'global-post-page-zipcode-selector',
				__( 'ZIP Code Based Content Protection', 'zipcode-bcp' ),
				array( $this, 'global_post_page_zipcode_meta_box_callback' ),
				$screen,
				'advanced',
				'high'
			);
		}
	}
	/**
	 * Metabox HTML
	 *
	 * @since    1.0.0
	 */
	public function global_post_page_zipcode_meta_box_callback( $post ) {
		echo '<input type="hidden" name="zipcode_nonce" id="zipcode_nonce" value="' . esc_attr( wp_create_nonce( plugin_basename( __FILE__ ) ) ) . '" />';
		$post_id                   = $post->ID;
		$activate_zipcode_checkbox = get_post_meta( $post_id, 'activate_zipcode_checkbox', true );
		$post_page_zipcode_select  = get_post_meta( $post_id, 'post_page_zipcode_select', true );
		?>

		<label for="zipcode_checkbox"><?php esc_html_e( 'Activate ZBCP?', 'zipcode-bcp' ); ?></label>
		<input type="checkbox" name="activate_zipcode_checkbox" <?php checked( $activate_zipcode_checkbox, 1 ); ?> value="<?php echo esc_attr( 1 ); ?>" id="activate_zipcode_checkbox" />
		<div 
		<?php
		if ( $activate_zipcode_checkbox == 0 ) {
			?>
  style="display:none" <?php } ?> class="activated_zipcode_checkbox">
			<p><?php esc_html_e( 'Select ZIP Code(s) for which this post content will be visible:', 'zipcode-bcp' ); ?></p>
				<select multiple="multiple" name="post_page_zipcode_select[]" id="post_page_zipcode_select" class="widefat zpcode_all" style="width: 300px">
				<option value="all"><?php esc_html_e( 'All', 'zipcode-bcp' ); ?></option>
					<?php
					global $wpdb;
					$sql     = "SELECT * FROM {$wpdb->prefix}zipcode_serving";
					$results = $wpdb->get_results( $sql, 'ARRAY_A' );
					foreach ( $results as $post ) {
						if ( in_array( $post['zipcode'], $post_page_zipcode_select ) ) {
							echo '<option selected value="' . esc_attr( $post['zipcode'] ) . '">' . esc_html( $post['zipcode'] ) . '</option>';
						} else {
							echo '<option value="' . esc_attr( $post['zipcode'] ) . '">' . esc_html( $post['zipcode'] ) . '</option>';
						}
					}
					?>
				</select>
		</div>
		<?php
	}
	/**
	 * Save post type Metabox
	 *
	 * @since    1.0.0
	 */

	public function zbcp_save_post_and_page_meta_box( $post_id ) {
		global $post;
		if ( ! wp_verify_nonce( $_POST['zipcode_nonce'], plugin_basename( __FILE__ ) ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$activate_zipcode_checkbox = sanitize_text_field( $_POST['activate_zipcode_checkbox'] );
		$post_page_zipcode_select  = array_map( 'sanitize_text_field', $_POST['post_page_zipcode_select'] );

		if ( isset( $_POST['activate_zipcode_checkbox'] ) ) {
			update_post_meta( $post_id, 'activate_zipcode_checkbox', 1 );
		} else {
			update_post_meta( $post_id, 'activate_zipcode_checkbox', 0 );
		}
		update_post_meta( $post_id, 'post_page_zipcode_select', $post_page_zipcode_select );
	}

}
