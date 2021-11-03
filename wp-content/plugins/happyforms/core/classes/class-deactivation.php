<?php

class HappyForms_Deactivation {
	
	private static $instance;

	public $modal_action = 'happyforms-deactivate-modal-submit';
	public $option = '_happyforms_cleanup_on_deactivate';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_action( 'admin_init', [ $this, 'register_modals' ] );
		add_filter( 'happyforms_dashboard_data', [ $this, 'get_dashboard_data' ] );
		add_action( 'wp_ajax_' . $this->modal_action, [ $this, 'deactivate_modal_submit' ] );
	}

	public function cleanup_on_deactivation() {
		$cleanup = get_option( $this->option, false );

		return $cleanup;
	}

	public function register_modals() {
		$modals = happyforms_get_dashboard_modals();

		$modals->register_modal( 'deactivate', [ $this, 'modal_deactivate_callback' ], [
			'classes' => 'happyforms-modal__frame--deactivate'
		] );
	}

	public function modal_deactivate_callback() {
		require( happyforms_get_core_folder() . '/templates/admin/modal-deactivate.php' );
	}

	public function get_dashboard_data( $data ) {
		$data['actionDeactivateModalSubmit'] = $this->modal_action;
		$data['textDeactivateAndKeep'] = __( 'Deactivate Plugin', 'happyforms' );
		$data['textDeactivateAndCleanup'] = __( 'Delete Data and Deactivate Plugin', 'happyforms' );

		return $data;
	}

	public function deactivate_modal_submit() {
		if ( ! check_ajax_referer( $this->modal_action ) ) {
			return;
		}

		$keep_data = isset( $_POST['keep_data'] ) ? $_POST['keep_data'] : 'yes';
		
		update_option( $this->option, 'no' === $keep_data );
	}

}

if ( ! function_exists( 'happyforms_get_deactivation' ) ) :

function happyforms_get_deactivation() {
	return HappyForms_Deactivation::instance();
}

endif;

happyforms_get_deactivation();