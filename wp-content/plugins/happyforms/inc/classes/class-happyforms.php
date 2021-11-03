<?php
class HappyForms extends HappyForms_Core {

	public $default_notice;
	
	public $action_archive = 'archive';

	public $onboarding_list_url = 'https://emailoctopus.com/lists/a58bf658-425e-11ea-be00-06b4694bee2a/members/embedded/1.3/add';

	public $action_onboarding = 'happyforms-submit-onboarding';

	public $option_show_powered_by = 'happyforms_show_powered_by';

	public function initialize_plugin() {
		parent::initialize_plugin();

		add_action( 'happyforms_do_setup_control', array( $this, 'do_control' ), 10, 3 );
		add_action( 'happyforms_do_email_control', array( $this, 'do_control' ), 10, 3 );
		add_action( 'happyforms_do_style_control', array( $this, 'do_control' ), 10, 3 );
		add_filter( 'happyforms_setup_controls', array( $this, 'add_dummy_setup_controls' ) );
		add_filter( 'happyforms_email_controls', array( $this, 'add_dummy_email_controls' ) );
		add_action( 'parse_request', array( $this, 'parse_archive_request' ) );
		add_action( 'admin_init', [ $this, 'register_modals' ] );
		add_action( 'admin_init', array( $this, 'redirect_to_forms_page' ) );
		add_action( 'happyforms_modal_dismissed', [ $this, 'modal_dismissed' ] );
		add_action( "wp_ajax_{$this->action_onboarding}", [ $this, 'ajax_action_onboarding' ] );
		add_action( 'happyforms_form_after', [ $this, 'output_powered_by_form' ] );
		add_action( 'happyforms_email_owner_after', [ $this, 'output_powered_by_email' ] );
		add_action( 'happyforms_email_user_after', [ $this, 'output_powered_by_email' ] );

		$this->register_dummy_parts();
		$this->add_setup_logic_upgrade_links();
	}

	public function register_dummy_parts() {
		$part_library = happyforms_get_part_library();

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-website-url-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_WebsiteUrl_Dummy', 3 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-attachment-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Attachment_Dummy', 6 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-table-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Table_Dummy', 7 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-poll-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Poll_Dummy', 10 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-phone-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Phone_Dummy', 11 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-date-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Date_Dummy', 12 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-address-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Address_Dummy', 13 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-scale-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Scale_Dummy', 14 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-rank-order-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_RankOrder_Dummy', 15 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-likert-scale-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_LikertScale_Dummy', 16 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-rich-text-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_RichText_Dummy', 17 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-legal-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Legal_Dummy', 18 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-signature-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Signature_Dummy', 19 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-rating-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Rating_Dummy', 20 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-narrative-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Narrative_Dummy', 21 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-optin-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_OptIn_Dummy', 22 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-payments-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Payments_Dummy', 23 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-layout-drawer-group.php' );
		$part_library->register_part( 'HappyForms_Part_LayoutDrawerGroup', 24 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-layout-title-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_LayoutTitle_Dummy', 25 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-placeholder-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Placeholder_Dummy', 26 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-media-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Media_Dummy', 27 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-divider-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_Divider_Dummy', 28 );

		require_once( happyforms_get_include_folder() . '/classes/parts/class-part-page-break-dummy.php' );
		$part_library->register_part( 'HappyForms_Part_PageBreak_Dummy', 29 );
	}

	public function add_dummy_setup_controls( $controls ) {
		$controls[11] = array(
			'type' => 'upsell',
			'label' => __( 'Upgrade', 'happyforms' ),
			'field' => '',
			'id' => 'happyforms-redirect-upsell',
		);

		$controls[1450] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'shuffle_parts',
			'label' => __( 'Randomize fields to prevent bias', 'happyforms' ),
		);

		$controls[1500] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'captcha',
			'label' => __( 'Use reCAPTCHA', 'happyforms' ),
		);

		$controls[1650] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'save_abandoned_responses',
			'label' => __( 'Save incomplete and abandoned submissions', 'happyforms' ),
		);

		$controls[1655] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'allow_abandoned_resume',
			'label' => __( 'Let respondents save a draft submission and come back to it later', 'happyforms' ),
		);

		$controls[1800] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'preview_before_submit',
			'label' => __( 'Require respondents to review a submission before submitting', 'happyforms' ),
		);

		$controls[1900] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'disable_submit_until_valid',
			'label' => __( 'Disable buttons until required fields are answered', 'happyforms' ),
		);

		$controls[2300] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'restrict_entries',
			'label' => __( 'Limit submissions', 'happyforms' ),
		);

		$controls[3000] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'schedule_visibility',
			'label' => __( 'Schedule visibility', 'happyforms' ),
		);

		$controls[3190] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'save_submissions',
			'label' => __( 'Store activity in database', 'happyforms' ),
		);

		$controls[3191] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'save_submissions',
			'label' => __( 'Capture user metadata (IP, language, platform, time and timezone)', 'happyforms' ),
		);

		$controls[3200] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'block_emails',
			'label' => __( 'Block these email addresses, email domains and email partials', 'happyforms' ),
		);

		$controls[3201] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'block_language',
			'label' => __( 'Block these words, phrases and letters', 'happyforms' ),
		);

		return $controls;
	}

	public function add_dummy_email_controls( $controls ) {
		$controls[500] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'email_mark_and_reply',
			'label' => __( 'Include reply link', 'happyforms' ),
		);

		$controls[531] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'alert_email_include_referral_url',
			'label' => __( 'Include referral web address', 'happyforms' ),
		);

		$controls[541] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'owner_attach_pdf',
			'label' => __( 'Attach .pdf', 'happyforms' ),
		);

		$controls[645] = array(
			'type' => 'email-parts-list_dummy',
			'dummy_id' => 'confirmation_email_respondent_address',
			'label' => __( 'To email address', 'happyforms' ),
		);

		$controls[871] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'attach_pdf',
			'label' => __( 'Attach .pdf', 'happyforms' ),
		);

		$controls[1660] = array(
			'type' => 'checkbox_dummy',
			'dummy_id' => 'abandoned_resume_send_alert_email',
			'label' => __( 'Send abandonment email', 'happyforms' ),
		);

		return $controls;
	}

	public function do_control( $control, $field, $index ) {
		$type = $control['type'];

		if ( 'checkbox_dummy' === $type ) {
			require( happyforms_get_include_folder() . '/templates/customize-controls/checkbox_dummy.php' );
		}

		if ( 'email-parts-list_dummy' === $type ) {
			require( happyforms_get_include_folder() . '/templates/customize-controls/email-parts-list-dummy.php' );
		}
	}

	public function admin_enqueue_scripts() {
		parent::admin_enqueue_scripts();

		wp_enqueue_style(
			'happyforms-free-admin',
			happyforms_get_plugin_url() . 'inc/assets/css/admin.css',
			array( 'thickbox' ), HAPPYFORMS_VERSION
		);

		wp_enqueue_script(
			'happyforms-free-admin',
			happyforms_get_plugin_url() . 'inc/assets/js/admin/dashboard.js',
			array( 'happyforms-admin' ), HAPPYFORMS_VERSION, true
		);

		$this->enqueue_onboarding_modal();
	}

	public function parse_archive_request() {
		global $pagenow;

		if ( 'edit.php' !== $pagenow ) {
			return;
		}

		$form_post_type = happyforms_get_form_controller()->post_type;

		if ( ! isset( $_GET['post_type'] ) || $form_post_type !== $_GET['post_type'] ) {
			return;
		}

		if ( ! isset( $_GET[$this->action_archive] ) ) {
			return;
		}

		$form_id = $_GET[$this->action_archive];
		$form_controller = happyforms_get_form_controller();
		$message_controller = happyforms_get_message_controller();
		$form = $form_controller->get( $form_id );

		if ( ! $form ) {
			return;
		}

		$message_controller->export_archive( $form );
	}

	public function is_new_user( $forms ) {
		if ( 1 !== count( $forms ) ) {
			return false;
		}

		$form = $forms[0];

		if ( 'Sample Form' === $form['post_title'] ) {
			return true;
		}

		return false;
	}

	public function add_setup_logic_upgrade_links() {
		$control_slugs = array(
			'email_recipient',
			'email_bccs',
			'alert_email_subject',
			'redirect_url'
		);

		foreach ( $control_slugs as $slug ) {
			add_action( "happyforms_setup_control_{$slug}_after", array( $this, 'set_logic_link_template' ) );
		}
	}

	public function set_logic_link_template() {
		$html = '';

		ob_start();
			require( happyforms_get_core_folder() . '/templates/customize-form-setup-logic.php' );
		$html = ob_get_clean();

		echo $html;
	}

	public function register_modals() {
		$modals = happyforms_get_dashboard_modals();

		$modals->register_modal( 'upgrade', [ $this, 'modal_upgrade_callback' ], [
			'classes' => 'happyforms-modal__frame--upgrade'
		] );

		$modals->register_modal( 'onboarding', [ $this, 'modal_onboarding_callback' ], [
			'classes' => 'happyforms-modal__frame--onboarding',
			'dismissible' => true,
		] );
	}

	public function enqueue_onboarding_modal() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		if ( 'edit-happyform' !== $screen->id ) {
			return;
		}

		$modals = happyforms_get_dashboard_modals();

		if ( $modals->is_dismissed( 'onboarding' ) ) {
			return;
		}

		wp_enqueue_script(
			'happyforms-onboarding',
			happyforms_get_plugin_url() . 'inc/assets/js/admin/onboarding.js',
			array( 'happyforms-free-admin' ), HAPPYFORMS_VERSION, true
		);

		wp_localize_script( 'happyforms-onboarding', '_happyFormsOnboardingSettings', array(
			'action' => $this->action_onboarding,
		) );
	}

	public function modal_upgrade_callback() {
		require( happyforms_get_include_folder() . '/templates/admin/modal-upgrade.php' );
	}

	public function modal_onboarding_callback() {
		require( happyforms_get_include_folder() . '/templates/admin/modal-onboarding.php' );
	}

	public function redirect_to_forms_page() {
		$screen = get_current_screen();

		if ( ! $screen ) {
			return;
		}

		if ( 'edit-happyform' === $screen->id ) {
			return;
		}

		if ( happyforms_get_dashboard_modals()->is_dismissed( 'onboarding' ) ) {
			return;
		}

		$tracking = happyforms_get_tracking();
		$status = $tracking->get_status();

		if ( 1 < intval( $status['status'] ) ) {
			return;
		}

		$url = admin_url( 'edit.php?post_type=happyform' );
		wp_safe_redirect( $url );

		exit;
	}

	public function modal_dismissed( $id ) {
		if ( 'onboarding' === $id ) {
			happyforms_get_tracking()->update_status( 2 );
		}
	}

	public function ajax_action_onboarding() {
		$email = isset( $_POST['email'] ) ? $_POST['email'] : '';
		$email = trim( $email );
		$powered_by = isset( $_POST['powered_by'] ) ? $_POST['powered_by'] : '';
		$powered_by = intval( $powered_by );

		// Store powered by option
		update_option( $this->option_show_powered_by, $powered_by );

		// Submit to EmailOctopus
		if ( ! empty( $email ) ) {
			wp_remote_post( $this->onboarding_list_url, array(
				'body' => array(
					'field_0' => $email,
				), 
			) );
		}
	}

	public function output_powered_by_form() {
		if ( apply_filters( 'happyforms_force_hide_powered_by', false ) === true ) {
			return;
		}

		if ( ! get_option( $this->option_show_powered_by, false ) ) {
			return;
		}

		?>
		<p class="happyforms-powered-by"><?php printf( 
			'<a href="%1$s" style="font-size: 12px; color: black; text-decoration: underline;">%2$s</a>', 
			'https://happyforms.io/?utm_source=footer&utm_medium=form&utm_campaign=public_form_footer',
			__( 'Build your own WordPress form with Happyforms', 'happyforms' )
		); ?></p>
		<?php
	}

	public function output_powered_by_email() {
		if ( apply_filters( 'happyforms_force_hide_powered_by', false ) === true ) {
			return;
		}

		if ( ! get_option( $this->option_show_powered_by, false ) ) {
			return;
		}

		?>
		<p><?php printf( 
			'<a href="%1$s">%2$s</a>', 
			'https://happyforms.io/?utm_source=footer&utm_medium=email&utm_campaign=public_email_footer',
			__( 'Build your own WordPress form with Happyforms', 'happyforms' )
		); ?></p>
		<?php
	}
}
