<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.presstigers.com/
 * @since      1.0.0
 *
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/includes
 * @author     PressTigers <support@presstigers.com>
 */
class Zipcode_BCP_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$prefix = $wpdb->get_blog_prefix();
		// `zipcode` int(10) NOT NULL DEFAULT 0, Change ZIpcode Field from Int To String
		$creation_query = 'CREATE TABLE IF NOT EXISTS ' . $prefix . 'zipcode_serving (
                `id` int(20) NOT NULL AUTO_INCREMENT,
                `zipcode` text,
                `services` text,
                PRIMARY KEY (`id`)
                );';
		$tble_creation  = $wpdb->query( $creation_query );

		$creation_query = 'CREATE TABLE IF NOT EXISTS ' . $prefix . 'zipcode_requested (
                `id` int(20) NOT NULL AUTO_INCREMENT,
                `zipcode` text,
                `request_count` int(10) NOT NULL DEFAULT 0,
                PRIMARY KEY (`id`)
                );';
		$tble_creation  = $wpdb->query( $creation_query );

		$creation_query = 'CREATE TABLE IF NOT EXISTS ' . $prefix . 'zipcode_requested_users (
                `id` int(20) NOT NULL AUTO_INCREMENT,
                `zipcode` text,
                `user_email` text,
                `post_id` int(10) NOT NULL DEFAULT 0,
                `post_type` text,
                `post_title` text,
                PRIMARY KEY (`id`)
                );';
		$tble_creation  = $wpdb->query( $creation_query );

		sanitize_option( 'cdzc_settings_no_zipcode_added', update_option( 'cdzc_settings_no_zipcode_added', 'Please add a valid ZIP Code to proceed.' ) );
		sanitize_option( 'cdzc_settings_no_valid_email', update_option( 'cdzc_settings_no_valid_email', 'Please add a valid email address to subscribe.' ) );
		sanitize_option( 'cdzc_settings_not_providing_services', update_option( 'cdzc_settings_not_providing_services', 'Sorry, we are not operating in this area at the moment.' ) );
		sanitize_option( 'cdzc_settings_user_already_requested', update_option( 'cdzc_settings_user_already_requested', 'Oops! You have already request for this ZIP Code. We will notify you once we start operating in your area.' ) );
		sanitize_option( 'cdzc_settings_thankyou_for_email', update_option( 'cdzc_settings_thankyou_for_email', 'Thank you for subscribing for our newsletter. We will notify you once we start operating in your area.' ) );
		sanitize_option( 'cdzc_settings_frontend_description', update_option( 'cdzc_settings_frontend_description', 'There is a ZIP Code restriction on the content please add ZIP Code of your area to get the content access.' ) );
		sanitize_option( 'cdzc_settings_requested_zipcode_highlight', update_option( 'cdzc_settings_requested_zipcode_highlight', '50' ) );

		$get_default_post_types = array(
			'public'   => true,
			'_builtin' => true,
		);

		$get_custom_post_types = array(
			'public'   => true,
			'_builtin' => false,
		);

		$post_types = array();

		$default_post_types = get_post_types( $get_default_post_types, 'object' );
		foreach ( $default_post_types as $post_key => $post_val ) {
			$post_types[ $post_val->name ] = $post_val->label;
		}

		$default_post_types = get_post_types( $get_custom_post_types, 'object' );
		foreach ( $default_post_types as $post_key => $post_val ) {
			$post_types[ $post_val->name ] = $post_val->label;
		}

		$check_boxes_array = array();
		$i                 = 0;
		foreach ( $post_types as $post_key => $post_val ) {
			$check_boxes_array[ $i ] = $post_key;
			$i++;
		}

		// Setting default values for when plugin is avtivated user can have some view without going into settings.
		if ( ! sanitize_option( 'cdzc_settings_for_checkbox', update_option( 'cdzc_settings_for_checkbox', $check_boxes_array ) ) ) {
			sanitize_option( 'cdzc_settings_for_checkbox', update_option( 'cdzc_settings_for_checkbox', $check_boxes_array ) );
		}
	}

}
