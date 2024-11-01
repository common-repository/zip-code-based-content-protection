<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.presstigers.com/
 * @since      1.0.0
 *
 * @package    Zipcode_BCP
 * @subpackage Zipcode_BCP/public/partials
 */
get_header();

if ( isset( $_GET['id'] ) && $_GET['id'] ) {
	$post_id    = sanitize_text_field( $_GET['id'] );
	$post_type  = get_post_type( $post_id );
	$post_title = get_the_title( $post_id );
	if ( 'publish' == get_post_status( $post_id ) ) {
		$cdzc_settings_frontend_description = get_option( 'cdzc_settings_frontend_description' );
		$content                            = '<div class="zbcp_form_container">'
			. '<div id="zbcp_spacing_div" class="zbcp_spacing zbcp_input_zipcode">'
			. '<p>' . esc_html__( $cdzc_settings_frontend_description, 'zipcode-bcp' ) . '</p>'
			. '<p id="zbcp_messages"></p>'
			. '<form action="#" id="zbcp_search_form_wrapper" style="display:none" class="zbcp_search_form_wrapper">'
			. '<input type="email" name="zbcp_email_against_zipcode" id="zbcp_email_against_zipcode" class="zbcp_email_against_zipcode" value="" placeholder="Email Address">'
			. '<input type="hidden" name="zbcp_post_id_against_zipcode" id="zbcp_post_id_against_zipcode" class="zbcp_post_id_against_zipcode" value="' . esc_attr( $post_id ) . '" />'
			. '<input type="hidden" name="zbcp_post_type_against_zipcode" id="zbcp_post_type_against_zipcode" class="zbcp_post_type_against_zipcode" value="' . esc_attr( $post_type ) . '" />'
			. '<input type="hidden" name="zbcp_post_title_against_zipcode" id="zbcp_post_title_against_zipcode" class="zbcp_post_title_against_zipcode" value="' . esc_attr( $post_title ) . '" />'
			. '<input type="submit" id="zbcp_submit_email_zipcode" class="zbcp_submit_email_zipcode" value="Submit">'
			. '</form>'
			. '<form action="#" id="zbcp_form_wrapper" class="zbcp_form_wrapper">'
			. '<input type="hidden" name="zbcp_serving_id" id="zbcp_serving_id" value="' . esc_attr( $post_id ) . '">'
			. '<input type="text" name="zbcp_serving_zipcode" id="zbcp_serving_zipcode" class="zbcp_serving_zipcode" value="" placeholder="Enter zip code">'
			. '<input type="submit" id="zbcp_check_serving_zipcode" class="zbcp_check_serving_zipcode" value="Check">'
			. '</form>'
			. '</div>'
			. '</div>';
		echo $content;
	} else {
		echo esc_html__( 'Something Wrong Please try again later', 'zipcode-bcp' );
	}
} else {
	echo esc_html__( 'Something Wrong Please try again later', 'zipcode-bcp' );
}
get_footer();
