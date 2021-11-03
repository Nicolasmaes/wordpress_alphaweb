<?php

class HappyForms_Dashboard_Modals {

	private static $instance;

	private $modals = array();

	public $fetch_action = 'happyforms-modal-fetch';
	public $dismiss_action = 'happyforms-modal-dismiss';

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_action( "wp_ajax_{$this->fetch_action}", [ $this, 'fetch_modal' ] );
		add_action( "wp_ajax_{$this->dismiss_action}", [ $this, 'dismiss_modal' ] );
	}

	public function get_modal_defaults() {
		$defaults = array(
			'classes' => '',
			'dismissible' => false,
		);

		return $defaults;
	}

	public function register_modal( $id, $callback, $settings = array() ) {
		if ( $this->is_dismissed( $id ) ) {
			return;
		}

		$settings = wp_parse_args( $settings, $this->get_modal_defaults() );

		$this->modals[$id] = [ $callback, $settings ];
	}

	public function fetch_modal() {
		if ( ! isset( $_GET['id'] ) ) {
			die( '' );
		}

		$id = $_GET['id'];

		if ( ! isset( $this->modals[$id] ) ) {
			die( '' );
		}

		list( $callback, $settings ) = $this->modals[$id];

		require( happyforms_get_core_folder() . '/templates/partials/admin-modal-header.php' );
		call_user_func( $callback );
		require( happyforms_get_core_folder() . '/templates/partials/admin-modal-footer.php' );

		die();
	}

	public function dismiss_modal() {
		if ( ! isset( $_POST['id'] ) ) {
			die( '' );
		}

		$id = $_POST['id'];

		if ( ! isset( $this->modals[$id] ) ) {
			die( '' );
		}

		if ( ! $this->is_dismissible( $id ) ) {
			die( '' );
		}

		update_option( "happyforms_modal_dismissed_{$id}", true );

		do_action( 'happyforms_modal_dismissed', $id );

		die( '' );
	}

	public function is_dismissible( $id ) {
		list( $callback, $settings ) = $this->modals[$id];
		$dismissible = isset( $settings['dismissible'] ) ? $settings['dismissible'] : false;

		return $dismissible;
	}

	public function is_dismissed( $id ) {
		return get_option( "happyforms_modal_dismissed_{$id}", false );
	}

}

if ( ! function_exists( 'happyforms_get_dashboard_modals' ) ):

function happyforms_get_dashboard_modals() {
	return HappyForms_Dashboard_Modals::instance();
}

endif;

happyforms_get_dashboard_modals();
