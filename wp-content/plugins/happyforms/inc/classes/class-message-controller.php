<?php

class HappyForms_Message_Controller {

	/**
	 * The singleton instance.
	 *
	 * @since 1.0
	 *
	 * @var HappyForms_Message_Controller
	 */
	private static $instance;

	/**
	 * The message post type slug.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $post_type = 'happyforms-message';

	/**
	 * The parameter name used to identify a
	 * submission form
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $form_parameter = 'happyforms_form_id';

	/**
	 * The parameter name used to identify a
	 * submission form
	 *
	 * @var string
	 */
	public $form_step_parameter = 'happyforms_step';

	/**
	 * The action name used to identify a
	 * message submission request.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $submit_action = 'happyforms_message';

	/**
	 * The nonce prefix used in forms.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $nonce_prefix = 'happyforms_message_nonce_';

	/**
	 * The nonce name used in forms.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $nonce_name = 'happyforms_message_nonce';

	/**
	 * The singleton constructor.
	 *
	 * @since 1.0
	 *
	 * @return HappyForms_Message_Controller
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	public function get_post_fields() {
		$fields = array(
			'post_title' => '',
			'post_type' => $this->post_type,
			'post_status' => 'publish',
		);

		return $fields;
	}

	/**
	 * Create a new message post object.
	 *
	 * @since 1.0
	 *
	 * @param array $form       The message form data.
	 * @param array $submission The message form data.
	 *
	 * @return int|boolean
	 */
	public function create( $form, $submission ) {
		$post_data = $this->get_insert_post_data( $form, $submission );
		$message_id = wp_insert_post( wp_slash( $post_data ), true );

		wp_update_post( array(
			'ID' => $message_id,
			'post_title' => happyforms_get_message_title( $message_id ),
		) );

		do_action( 'happyforms_response_created', $message_id, $form );

		return $message_id;
	}

	public function get( $post_ids, $force = false ) {
		$args = md5( serialize( func_get_args() ) );
		$key = "_happyforms_cache_responses_get_{$args}";
		$found = false;
		$result = happyforms_cache_get( $key, $found );

		if ( false === $found || $force ) {
			$result = $this->do_get( $post_ids );
			happyforms_cache_set( $key, $result );
		}

		return $result;
	}

	public function do_get( $post_ids = '' ) {
		$query_params = array(
			'post_type' => $this->post_type,
			'post_status' => array( 'publish', 'draft', 'trash' ),
			'posts_per_page' => -1,
		);

		if ( ! empty( $post_ids ) ) {
			if ( is_numeric( $post_ids ) ) {
				$query_params['p'] = $post_ids;
			} else if ( is_array( $post_ids ) )  {
				$query_params['post__in'] = $post_ids;
			}
		}

		$messages = get_posts( $query_params );
		$message_entries = array_map( array( $this, 'to_array'), $messages );

		if ( is_numeric( $post_ids ) ) {
			if ( count( $message_entries ) > 0 ) {
				return $message_entries[0];
			} else {
				return false;
			}
		}

		return $message_entries;
	}

	public function get_insert_post_data( $form, $submission ) {
		$defaults = $this->get_post_fields();
		$defaults_meta = $this->get_meta_fields();
		$raw_request = $this->get_raw_request( $form, $submission );
		$message_meta = wp_parse_args( array(
			'form_id' => $form['ID'],
			'request' => $raw_request,
		), $defaults_meta );
		$message_meta = array_merge( $message_meta, $submission );
		$message_meta = happyforms_prefix_meta( $message_meta );
		$post_data = array_merge( $defaults, array(
			'meta_input' => $message_meta
		) );

		return $post_data;
	}

	public function get_raw_request( $form, $submission ) {
		$request = array();

		foreach( $form['parts'] as $part_id => $part ) {
			$part_name = happyforms_get_part_name( $part, $form );

			if ( ! isset( $_REQUEST[$part_name] ) ) {
				continue;
			}

			$part_class = happyforms_get_part_library()->get_part( $part['type'] );
			$value = $part_class->sanitize_value( $part, $form, $_REQUEST );
			$request[$part_name] = $value;
		}

		return $request;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function hook() {
		add_action( 'parse_request', array( $this, 'admin_post' ) );
		add_action( 'admin_init', array( $this, 'admin_post' ) );
		add_filter( 'happyforms_email_part_visible', array( $this, 'email_part_visible' ), 10, 4 );

		// Core multi-step hooks
		add_action( 'happyforms_step', array( $this, 'default_submission_step' ) );
		// Submission preview and review
		add_action( 'happyforms_step', array( $this, 'preview_submission_step' ) );
		add_action( 'happyforms_step', array( $this, 'review_submission_step' ) );
	}

	public function get_meta_fields() {
		$fields = array(
			'form_id' => 0,
			'read' => false,
			'tracking_id' => '',
		);
		return $fields;
	}

	public function to_array( $message ) {
		$message_array = $message->to_array();
		$message_meta = happyforms_unprefix_meta( get_post_meta( $message->ID ) );
		$form_id = $message_meta['form_id'];
		$form = happyforms_get_form_controller()->get( $form_id );
		$meta_defaults = $this->get_meta_fields();
		$message_array = array_merge( $message_array, wp_parse_args( $message_meta, $meta_defaults ) );
		$message_array['parts'] = array();

		if ( $form ) {
			foreach ( $form['parts'] as $part_data ) {
				$part = happyforms_get_part_library()->get_part( $part_data['type'] );

				if ( $part ) {
					$part_id = $part_data['id'];
					$part_value = $part->get_default_value( $part_data );

					if ( isset( $message_meta[$part_id] ) ) {
						$part_value = $message_meta[$part_id];
					}

					$message_array['parts'][$part_id] = $part_value;
					unset( $message_array[$part_id] );
				}
			}
		}

		return $message_array;
	}

	/**
	 * Action: handle a form submission.
	 *
	 * @hooked action parse_request
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function admin_post() {
		// Exit early if we're not submitting any form
		if ( ! isset ( $_REQUEST['action'] ) || $this->submit_action != $_REQUEST['action'] ) {
			return;
		}

		// Check form_id parameter
		if ( ! isset ( $_REQUEST[$this->form_parameter] ) ) {
			wp_send_json_error();
		}

		$form_id = intval( $_REQUEST[$this->form_parameter] );

		// Validate nonce
		if ( apply_filters( 'happyforms_verify_nonce', true ) ) {
			if ( ! isset( $_REQUEST[$this->nonce_name] )
				|| ! $this->verify_nonce( $_REQUEST[$this->nonce_name], $form_id ) ) {

				wp_send_json_error();
			}
		}

		$form_controller = happyforms_get_form_controller();
		$form = $form_controller->get( $form_id );

		// Check if form found
		if ( ! $form || is_wp_error( $form ) ) {
			wp_send_json_error();
		}

		// Set form step
		$step = isset( $_REQUEST[$this->form_step_parameter] ) ?
			$_REQUEST[$this->form_step_parameter] : '';

		happyforms_get_session()->set_step( $step );

		// Validate honeypot
		if ( happyforms_get_form_controller()->has_spam_protection( $form ) ) {
			if ( ! $this->validate_honeypot( $form ) ) {
				wp_send_json_error();
			}
		}

		define( 'HAPPYFORMS_STEPPING', true );
		do_action( 'happyforms_step', $form );
	}

	public function default_submission_step( $form ) {
		if ( 'submit' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$session = happyforms_get_session();
		$submission = $this->validate_submission( $form, $_REQUEST );
		$response = array();

		if ( false === $submission ) {
			// Add a general error notice at the top
			$session->add_error( $form_id, html_entity_decode( $form['error_message'] ) );

			// Reset to start step
			$session->reset_step();

			// Render the form
			$response['html'] = $form_controller->render( $form );

			/**
			 * This action fires upon an invalid submission.
			 *
			 * @since 1.4
			 *
			 * @param WP_Error $submission Error data.
			 * @param array    $form   Current form data.
			 *
			 * @return void
			 */
			do_action( 'happyforms_submission_error', $submission, $form );

			// Send error response
			wp_send_json_error( $response );
		} else {
			// Add a general success notice at the top
			$session->add_notice( $form_id, html_entity_decode( $form['confirmation_message'] ) );

			// Reset to start step
			$session->reset_step();

			// Empty submitted values
			$session->clear_values();

			if ( 1 === intval( $form['receive_email_alerts'] ) ) {
				$this->email_owner_confirmation( $form, $submission );
			}

			if ( 1 === intval( $form['send_confirmation_email'] ) ) {
				$this->email_user_confirmation( $form, $submission );
			}

			// Create message post
			$message_id = $this->create( $form, $submission );
			$message = $this->get( $message_id );

			/**
			 * This action fires once a message is succesfully submitted.
			 *
			 * @since 1.4
			 *
			 * @param array $submission Submission data.
			 * @param array $form   Current form data.
			 *
			 * @return void
			 */
			do_action( 'happyforms_submission_success', $submission, $form, $message );

			// Render the form
			$response['html'] = $form_controller->render( $form );

			// Send success response
			$this->send_json_success( $response, $submission, $form );
		}
	}

	public function preview_submission_step( $form ) {
		if ( 'preview' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$session = happyforms_get_session();
		$submission = $this->validate_submission( $form, $_REQUEST );
		$response = array();

		if ( false === $submission ) {
			// Add a general error notice at the top
			$session->add_error( $form_id, html_entity_decode( $form['error_message'] ) );

			// Reset to start step
			$session->reset_step();

			// Render the form
			$response['html'] = $form_controller->render( $form );

			// Send error response
			wp_send_json_error( $response );
		} else {
			// Advance step
			$session->next_step();

			// Render the form
			$response['html'] = $form_controller->render( $form );

			// Send success response
			$this->send_json_success( $response, $submission, $form );
		}
	}

	public function review_submission_step( $form ) {
		if ( 'review' !== happyforms_get_current_step( $form ) ) {
			return;
		}

		$form_id = $form['ID'];
		$form_controller = happyforms_get_form_controller();
		$session = happyforms_get_session();
		$submission = $this->validate_submission( $form, $_REQUEST );
		$response = array();

		if ( false === $submission ) {
			// Add a general error notice at the top
			$session->add_error( $form_id, html_entity_decode( $form['error_message'] ) );
		}

		// Reset to start step
		$session->reset_step();

		// Render the form
		$response['html'] = $form_controller->render( $form );

		if ( false === $submission ) {
			// Send error response
			wp_send_json_error( $response );
		}

		// Send success response
		$this->send_json_success( $response, $submission, $form );
	}

	public function send_json_success( $response = array(), $submission = array(), $form = array() ) {
		$response = apply_filters( 'happyforms_json_response', $response, $submission, $form );

		wp_send_json_success( $response );
	}

	/**
	 * Verify a message nonce.
	 *
	 * @since 1.0
	 *
	 * @param string $nonce   The submitted value.
	 * @param string $form_id The ID of the form being submitted.
	 *
	 * @return boolean
	 */
	public function verify_nonce( $nonce, $form_id ) {
		return wp_verify_nonce( $nonce, $this->nonce_prefix . $form_id );
	}

	/**
	 * Verify honeypot data.
	 *
	 * @since 1.3
	 *
	 * @param array $form Current form data.
	 *
	 * @return boolean
	 */
	private function validate_honeypot( $form ) {
		$honeypot_name = $form['ID'] . 'single_line_text_-1';
		$names = array( 'username', 'email', 'password' );
		$validated = true;

		foreach( $names as $key ) {
			$name = $form['ID'] . '-' . $key;

			if ( isset( $_REQUEST[$name] ) ) {
				$validated = $validated && ( empty( $_REQUEST[$name] ) );
			}
		}

		return $validated;
	}

	public function validate_part( $form, $part, $request ) {
		$part_class = happyforms_get_part_library()->get_part( $part['type'] );

		if ( false !== $part_class ) {
			$part_id = $part['id'];
			$part_name = happyforms_get_part_name( $part, $form );
			$sanitized_value = $part_class->sanitize_value( $part, $form, $request );
			$validated_value = $part_class->validate_value( $sanitized_value, $part, $form );

			$session = happyforms_get_session();
			$session->add_value( $part_name, $sanitized_value );

			if ( ! is_wp_error( $validated_value ) ) {
				return $validated_value;
			} else {
				do_action( 'happyforms_validation_error', $form, $part );

				$part_field = $part_name;
				$error_data = $validated_value->get_error_data();

				if ( ! empty( $error_data ) && isset( $error_data['components'] ) ) {
					foreach ( $error_data['components'] as $component ) {
						$session->add_error( $part_field, $validated_value->get_error_message(), $component );
					}
				} else {
					$session->add_error( $part_field, $validated_value->get_error_message() );
				}
			}
		}

		return false;
	}

	public function validate_submission( $form, $request = array() ) {
		$submission = array();
		$is_valid = true;

		foreach( $form['parts'] as $part ) {
			$part_id = $part['id'];
			$validated_value = $this->validate_part( $form, $part, $request );

			if ( false !== $validated_value ) {
				$string_value = happyforms_stringify_part_value( $validated_value, $part, $form );
				$submission[$part_id] = $string_value;
			} else {
				$is_valid = false;
			}
		}

		$is_valid = apply_filters( 'happyforms_validate_submission', $is_valid, $request, $form );

		return $is_valid ? $submission : false;
	}

	public function email_part_visible( $visible, $part, $form, $response ) {
		$required = happyforms_is_truthy( $part['required'] );
		$value = happyforms_get_email_part_value( $response, $part, $form );

		if ( false === $required && empty( $value ) ) {
			$visible = false;
		}

		if ( isset( $part['use_as_subject'] ) && $part['use_as_subject'] ) {
			$visible = false;
		}

		return $visible;
	}

	public function get_email_owner_confirmation_subject( $form, $message ) {
		$subject = $form['alert_email_subject'];

		$subject_parts = array_filter( $form['parts'], function( $part ) {
			$use_as_subject = (
				isset( $part['use_as_subject'] )
				&& intval( $part['use_as_subject'] )
			);

			return $use_as_subject;
		} );
		$subject_parts = array_values( $subject_parts );

		if ( count( $subject_parts ) > 0 ) {
			$part = $subject_parts[count( $subject_parts ) - 1];
			$message = array( 'parts' => $message );
			$subject = happyforms_get_email_part_value( $message, $part, $form );
		}

		return $subject;
	}

	/**
	 * Send a confirmation email to the site owner.
	 *
	 * @since 1.0
	 *
	 * @param array  $form    The message form data.
	 * @param string $message The message contents.
	 *
	 * @return void
	 */
	private function email_owner_confirmation( $form, $message ) {
		$subject = $this->get_email_owner_confirmation_subject( $form, $message );

		if ( ! empty( $form['email_recipient'] ) && ! empty( $subject ) ) {
			// Compose an email message
			$email_message = new HappyForms_Email_Message( $message );
			$name = $form['alert_email_from_name'];
			$to = explode( ',', $form['email_recipient'] );

			$email_message->set_from_name( $name );
			$email_message->set_to( $to[0] );

			if ( count( $to ) > 1 ) {
				$email_message->set_ccs( array_slice( $to, 1 ) );
			}

			$bccs = explode( ',', $form['email_bccs'] );

			if ( count( $bccs ) > 0 ) {
				$email_message->set_bccs( $bccs );
			}

			$email_message->set_subject( $subject );

			$email_part = happyforms_get_form_controller()->get_first_part_by_type( $form, 'email' );

			if ( false !== $email_part ) {
				$email_part_id = $email_part['id'];
				$reply_to = happyforms_get_message_part_value( $message[$email_part_id], $email_part );
				$email_message->set_reply_to( $reply_to );
			}

			ob_start();
			$response = $message;
			require_once( happyforms_owner_email_template_path() );
			$content = ob_get_clean();

			$email_message->set_content( $content );
			$email_message = apply_filters( 'happyforms_email_alert', $email_message );
			$email_message->send();
		}
	}

	/**
	 * Send a confirmation email to the user submitting the form.
	 *
	 * @since 1.0
	 *
	 * @param array  $form    The message form data.
	 * @param string $message The message contents.
	 *
	 * @return void
	 */
	private function email_user_confirmation( $form, $message ) {
		$email_part = happyforms_get_form_controller()->get_first_part_by_type( $form, 'email' );

		if ( false !== $email_part
			&& ! empty( $form['confirmation_email_subject'] )
			&& ! empty( $form['confirmation_email_content'] )
			&& ! empty( $form['confirmation_email_sender_address'] ) ) {

			// Compose an email message
			$email_message = new HappyForms_Email_Message( $message );
			$senders = happyforms_get_form_property( $form, 'confirmation_email_sender_address' );
			$senders = explode( ',', $senders );
			$name = happyforms_get_form_property( $form, 'confirmation_email_from_name' );
			$from = $senders[0];
			$reply_to = happyforms_get_form_property( $form, 'confirmation_email_reply_to' );
			$reply_to = empty( $reply_to ) ? $from : $reply_to;

			$email_message->set_from( $from );
			$email_message->set_from_name( $name );
			$email_message->set_reply_to( $reply_to );
			$email_message->set_subject( $form['confirmation_email_subject'] );
			$part_id = $email_part['id'];
			$to = happyforms_get_message_part_value( $message[$part_id], $email_part );
			$email_message->set_to( $to );

			ob_start();
			$response = $message;
			require_once( happyforms_user_email_template_path() );
			$content = ob_get_clean();

			$email_message->set_content( $content );
			$email_message = apply_filters( 'happyforms_email_confirmation', $email_message );
			$email_message->send();
		}
	}

	public function get_archivable_forms() {
		global $wpdb;

		$query = "
			SELECT p.ID, p.post_title FROM $wpdb->posts p
			JOIN $wpdb->postmeta m
			ON p.ID = m.meta_value AND m.meta_key = '_happyforms_form_id'
			GROUP BY p.ID;
		";

		$forms = $wpdb->get_results( $query );

		return $forms;
	}

	public function export_archive( $form ) {
		$form_id = $form['ID'];
		$parts = wp_list_pluck( $form['parts'], 'id' );
		$parts = array_combine( $parts, $form['parts'] );

		$messages = get_posts( array(
			'post_type'   => $this->post_type,
			'post_status' => array( 'publish', 'draft', 'trash' ),
			'posts_per_page' => -1,
			'meta_query' => array( array(
				'field' => '_happyforms_form_id',
				'value' => $form_id,
			) )
		) );

		$messages = array_map( array( $this, 'to_array'), $messages );
		$headers = array();
		$rows = array();

		foreach ( $parts as $part_id => $part ) {
			$headers[$part_id] = happyforms_get_csv_header( $part );
		}

		$headers = apply_filters( 'happyforms_csv_headers', $headers, $form );

		foreach( $messages as $message ) {
			$row = array();
			foreach( $headers as $part_id => $header ) {
				$value = $message['parts'][$part_id];
				$part = $parts[$part_id];
				$row[] = happyforms_get_csv_value( $value, $message, $part, $form );
			}
			$rows[] = $row;
		}

		$filename = 'messages.csv';
		$output = fopen( 'php://output', 'w' );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-Type: text/csv; charset=' . get_option( 'blog_charset' ), true );
		fputcsv( $output, array_values( $headers ) );

		foreach( $rows as $row ) {
			fputcsv( $output, array_values( $row ) );
		}

		exit();
	}

}

if ( ! function_exists( 'happyforms_get_message_controller' ) ):
/**
 * Get the HappyForms_Message_Controller class instance.
 *
 * @since 1.0
 *
 * @return HappyForms_Message_Controller
 */
function happyforms_get_message_controller() {
	return HappyForms_Message_Controller::instance();
}

endif;

/**
 * Initialize the HappyForms_Message_Controller class immediately.
 */
happyforms_get_message_controller();
