<?php
class HappyForms_Form_Option_Limiter {

	private static $instance;

	private $part_types;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function __construct() {
		$this->part_types = array(
			'radio',
			'checkbox',
			'select',
			'table',
		);
	}

	public function hook() {
		add_filter( 'happyforms_part_options', array( $this, 'get_part_options' ), 10, 3 );
		add_filter( 'happyforms_get_form_parts', array( $this, 'get_form_parts' ), 10, 2 );
		add_action( 'happyforms_submission_success', array( $this, 'submission_success' ), 10, 3 );
	}

	public function get_option_fields() {
		$defaults = array(
			'limit_submissions' => 0,
			'limit_submissions_amount' => 1,
			'show_submissions_amount' => 0,
			'submissions_left' => 0,
			'submissions_left_label' => '',
		);

		return $defaults;
	}

	public function get_part_options( $options, $part, $form ) {
		$option_defaults = $this->get_option_fields();

		// Calculate remaining choices
		$options = array_map( function( $option ) use( $option_defaults, $form ) {
			$option = wp_parse_args( $option, $option_defaults );

			if ( intval( $option['limit_submissions'] ) ) {
				$limit = intval( $option['limit_submissions_amount'] );
				$count = happyforms_get_form_controller()->count_by_option( $form['ID'], $option['id'], $limit );
				$option['submissions_left'] = $limit - $count;
			}

			return $option;
		}, $options );

		// Remove exhausted choices
		$options = array_filter( $options, function( $option ) {
			if ( intval( $option['limit_submissions'] ) ) {
				return $option['submissions_left'] > 0;
			}

			return $option;
		} );

		// Add remaining choices labels
		$options = array_map( function( $option ) use( $form ) {
			if ( 1 == $option['show_submissions_amount'] ) {
				$submissions_left_label = $form['submissions_left_label'];

				if ( happyforms_is_preview() ) {
					$submissions_left_label = sprintf(
						'<span class="happyforms-submissions-left">%s</span>',
						$submissions_left_label
					);
				}

				$submissions_left = sprintf(
					' <span class="happyforms-remaining-choice">(%1$s %2$s)</span>',
					$option['submissions_left'], $submissions_left_label
				);

				$option['submissions_left_label'] = $submissions_left;
			}

			return $option;
		}, $options );

		return $options;
	}

	public function submission_success( $submission, $form, $message ) {
		$parts = array();

		foreach( $form['parts'] as $part ) {
			if ( ! in_array( $part['type'], $this->part_types ) ) {
				continue;
			}

			$options_key = 'options';

			if ( 'table' === $part['type'] ) {
				$options_key = 'columns';
			}

			$options = $part[$options_key];

			foreach( $part[$options_key] as $o => $option ) {
				$option = wp_parse_args( $option, $this->get_option_fields() );

				if ( ! $option['limit_submissions'] ) {
					continue;
				}

				$part_name = happyforms_get_part_name( $part, $form );

				if ( ! isset( $_REQUEST[$part_name] ) ) {
					continue;
				}

				$request_value = $_REQUEST[$part_name];

				if ( ! is_array( $request_value ) ) {
					$request_value = array( $request_value );
				}

				foreach( $request_value as $submitted_value ) {
					if ( ! is_array( $submitted_value ) ) {
						$submitted_value = array( $submitted_value );
					}

					// Filter out "other" options
					$submitted_value = array_filter( $submitted_value, 'is_numeric' );
					$submitted_value = array_map( 'intval', $submitted_value );

					if ( ! in_array( $o, $submitted_value ) ) {
						continue;
					}

					$meta_key = happyforms_get_option_counter_meta_key( $form['ID'], $option['id'] );
					update_post_meta( $message['ID'], $meta_key, 1 );
				}
			}
		}
	}

	public function get_form_parts( $parts, $form ) {
		foreach( $parts as $p => $part ) {
			if ( ! in_array( $part['type'], $this->part_types ) ) {
				continue;
			}

			$options_key = 'options';

			if ( 'table' === $part['type'] ) {
				$options_key = 'columns';
			}

			$options = $part[$options_key];

			foreach( $options as $o => $option ) {
				$option = wp_parse_args( $option, $this->get_option_fields() );

				if ( ! $option['limit_submissions'] ) {
					continue;
				}

				$limit = intval( $option['limit_submissions_amount'] );
				$form_id = $form['ID'];
				$option_id = $option['id'];
				$count = happyforms_get_form_controller()->count_by_option( $form_id, $option_id, $limit );

				if ( $limit <= $count ) {
					unset( $part[$options_key][$o] );
				}

				if ( 0 === count( $part[$options_key] ) ) {
					unset( $parts[$p] );
				}
			}
		}

		return $parts;
	}

}

if ( ! function_exists( 'happyforms_upgrade_get_option_limiter' ) ) :

function happyforms_upgrade_get_option_limiter() {
	return HappyForms_Form_Option_Limiter::instance();
}

endif;

happyforms_upgrade_get_option_limiter();
