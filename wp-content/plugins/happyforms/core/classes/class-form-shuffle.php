<?php
class HappyForms_Form_Shuffle_Parts {
	private static $instance;

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function hook() {
		add_filter( 'happyforms_meta_fields', array( $this, 'add_fields' ), 10, 1 );
		add_filter( 'happyforms_setup_controls', array( $this, 'add_setup_controls' ), 10, 1 );

		add_filter( 'happyforms_part_customize_fields_select', array( $this, 'add_shuffle_part_options_field' ), 10, 1 );
		add_filter( 'happyforms_part_customize_fields_checkbox', array( $this, 'add_shuffle_part_options_field' ), 10, 1 );
		add_filter( 'happyforms_part_customize_fields_radio', array( $this, 'add_shuffle_part_options_field' ), 10, 1 );
		add_filter( 'happyforms_part_customize_fields_table', array( $this, 'add_shuffle_part_options_field' ), 10, 1 );
		add_filter( 'happyforms_part_customize_fields_poll', array( $this, 'add_shuffle_part_options_field' ), 10, 1 );
		add_filter( 'happyforms_part_customize_fields_rank_order', array( $this, 'add_shuffle_part_options_field' ), 10, 1 );

		add_filter( 'happyforms_get_form_parts', array( $this, 'get_form_parts' ), 10, 2 );
		add_filter( 'happyforms_part_options', array( $this, 'shuffle_part_options' ), 10, 3 );
	}

	public function add_fields( $fields ) {
		$fields['shuffle_parts'] = array(
			'default'  => 0,
			'sanitize' => 'happyforms_sanitize_checkbox'
		);

		return $fields;
	}

	public function add_setup_controls( $controls ) {
		$setup_controls = array(
			1450 => array(
				'field' => 'shuffle_parts',
				'label' => __( 'Randomize fields to prevent bias', 'happyforms' ),
				'type' => 'checkbox'
			),
		);

		$controls = happyforms_safe_array_merge( $controls, $setup_controls );

		return $controls;
	}

	public function get_form_parts( $parts, $form ) {
		if ( is_customize_preview() ) {
			return $parts;
		}

		if ( ! happyforms_get_form_property( $form, 'shuffle_parts' ) ) {
			return $parts;
		}

		if ( happyforms_get_stepper()->is_multistep( $form ) ) {
			return $parts;
		}

		$parts = $this->shuffle_form_parts( $parts );

		return $parts;
	}

	public function shuffle_form_parts( $parts ) {
		$shuffled = $parts;
		$index = 0;

		shuffle( $shuffled );

		foreach ( $shuffled as $key => $part ) {
			$shuffled[$key]['width'] = $parts[$index]['width'];
			$index++;
		}

		$parts = $shuffled;

		return $parts;
	}

	public function add_shuffle_part_options_field( $fields ) {
		$fields['shuffle_options'] = array(
			'default' => 0,
			'sanitize' => 'happyforms_sanitize_checkbox'
		);

		return $fields;
	}

	public function shuffle_part_options( $options, $part, $form ) {
		if ( ! is_customize_preview() && isset( $part['shuffle_options'] ) && 1 === intval( $part['shuffle_options'] ) ) {
			// only shuffle rows in Table part
			if ( 'table' === $part['type'] && $options[0]['type'] === 'column' ) {
				return $options;
			}

			$options_keys = array_keys( $options );
			shuffle( $options_keys );
			$shuffled = array();

			foreach ( $options_keys as $key ) {
				$shuffled[$key] = $options[$key];
			}

			$options = $shuffled;
		}

		return $options;
	}

}

if ( ! function_exists( 'happyforms_upgrade_get_shuffle_parts' ) ) :

function happyforms_upgrade_get_shuffle_parts() {
	return HappyForms_Form_Shuffle_Parts::instance();
}

endif;

happyforms_upgrade_get_shuffle_parts();
