<?php
/**
 *
 * Core activation/deactivation/remove hooks
 *
 */
if ( ! function_exists( 'happyforms_first_run' ) ):

function happyforms_activate() {
	do_action( 'happyforms_activate' );
}

endif;

if ( ! function_exists( 'happyforms_deactivate' ) ):

function happyforms_deactivate() {
	do_action( 'happyforms_deactivate' );
}

endif;

if ( ! function_exists( 'happyforms_uninstall' ) ) :

function happyforms_uninstall() {
	do_action( 'happyforms_uninstall' );
}

endif;

register_activation_hook( happyforms_plugin_file(), 'happyforms_activate' );
register_deactivation_hook( happyforms_plugin_file(), 'happyforms_deactivate' );
register_uninstall_hook( happyforms_plugin_file(), 'happyforms_uninstall' );

/**
 *
 * Hooked activation/deactivation/remove routines
 *
 */
if ( ! function_exists( 'happyforms_create_samples' ) ):

function happyforms_create_samples() {
	require_once( happyforms_get_core_folder() . '/classes/class-cache.php' );
	require_once( happyforms_get_core_folder() . '/classes/class-tracking.php' );
	require_once( happyforms_get_core_folder() . '/helpers/helper-misc.php' );

	$tracking = happyforms_get_tracking();
	$status = $tracking->get_status();

	if ( 0 < intval( $status['status'] ) ) {
		return;
	}

	require_once( happyforms_get_core_folder() . '/classes/class-form-controller.php' );
	require_once( happyforms_get_core_folder() . '/classes/class-form-part-library.php' );
	require_once( happyforms_get_core_folder() . '/classes/class-form-styles.php' );
	require_once( happyforms_get_core_folder() . '/classes/class-session.php' );
	require_once( happyforms_get_core_folder() . '/helpers/helper-form-templates.php' );
	require_once( happyforms_get_core_folder() . '/helpers/helper-validation.php' );

	$part_library = happyforms_get_part_library();
	$form_controller = happyforms_get_form_controller();

	// Create a new form
	$form = $form_controller->create();

	// Get the new form default data
	$form_data = $form_controller->get( $form->ID );

	$form_data['post_title'] = __( 'Sample Form', 'happyforms' );

	// Prepare age dropdown options
	$age_options = array(
		array(
			'label' => '20-24'
		),
		array(
			'label' => '25-29'
		),
		array(
			'label' => '30-34'
		),
		array(
			'label' => '35-39'
		),
		array(
			'label' => '40-44'
		),
		array(
			'label' => '45-49'
		),
		array(
			'label' => '50+'
		),
	);

	$form_parts = array(
		array(
			'type' => 'single_line_text',
			'label' => __( 'First name', 'happyforms' ),
			'width' => 'half',
		),
		array(
			'type' => 'single_line_text',
			'label' => __( 'Last name', 'happyforms' ),
			'width' => 'half',
		),
		array(
			'type' => 'checkbox',
			'label' => __( 'What\'s your reason for contacting us?', 'happyforms' ),
			'options' => array(
				array(
					'label' => __( 'Need technical help', 'happyforms' ),
				),
				array(
					'label' => __( 'Want to suggest a feature', 'happyforms' ),
				),
				array(
					'label' => __( 'Asking about my account', 'happyforms' ),
				),
			),
		),
		array(
			'type' => 'select',
			'label' => __( 'What\'s your age?', 'happyforms' ),
			'options' => $age_options
		),
		array(
			'type' => 'multi_line_text',
			'label' => __( 'Your message', 'happyforms' ),
		),
	);

	foreach( $form_parts as $part_id => $part_data ) {
		$part_type = $part_data['type'];
		$part_complete_id = "{$part_type}_$part_id";
		$part_data['id'] = $part_complete_id;
		$part = $part_library->get_part( $part_type );
		$part_defaults = $part->get_customize_defaults();
		$part_data = wp_parse_args( $part_data, $part_defaults );

		if ( isset( $part_data['options'] ) ) {
			foreach( $part_data['options'] as $option_id => $option_data ) {
				$option_data['id'] = "{$part_complete_id}_{$option_id}";
				$part_data['options'][$option_id] = $option_data;
			}
		}

		$form_data['parts'][] = $part_data;
	}

	// Update the new form with default parts
	$form_data = $form_controller->update( $form_data );

	// Store an option to avoid creating new forms on reactivation
	$tracking->update_status( 1 );

	// Force a permalinks refresh
	flush_rewrite_rules();
}

endif;

add_action( 'happyforms_activate', 'happyforms_create_samples' );

if ( ! function_exists( 'happyforms_cleanup_on_deactivation' ) ) :

function happyforms_cleanup_on_deactivation() {
	$cleanup_on_deactivation = happyforms_get_deactivation()->cleanup_on_deactivation();

	return apply_filters( 'happyforms_cleanup_on_deactivation', $cleanup_on_deactivation );
}

endif;

if ( ! function_exists( 'happyforms_cleanup' ) ) :

function happyforms_cleanup() {
	if ( ! happyforms_cleanup_on_deactivation() ) {
		return;
	}

	return;

	// Forms
	$forms = get_posts( array(
		'post_type' => 'happyform',
		'post_status' => array_values( get_post_stati() ),
		'numberposts' => -1,
	) );

	foreach( $forms as $form ) {
		wp_delete_post( $form->ID, true );
	}

	// General options
	delete_option( 'widget_happyforms_widget' );
	delete_option( 'happyforms-tracking' );
	delete_option( 'ttf_updates_key_happyforms' );

	// User meta
	$users = get_users();

	foreach( $users as $user ) {
		delete_user_meta( $user->ID, 'happyforms-dismissed-notices' );
		delete_transient( 'happyforms_admin_notices_' . md5( $user->user_login ) );
		delete_user_meta( $user->ID, 'happyforms-settings-sections-states' );
	}

	// Migrations
	delete_option( 'happyforms-data-version' );

	// Deactivation
	delete_option( '_happyforms_cleanup_on_deactivate' );
}

endif;

add_action( 'happyforms_deactivate', 'happyforms_cleanup' );