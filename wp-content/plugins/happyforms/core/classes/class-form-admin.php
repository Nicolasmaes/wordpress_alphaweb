<?php

class HappyForms_Form_Admin {

	/**
	 * The singleton instance.
	 *
	 * @since 1.0
	 *
	 * @var HappyForms_Form_Admin
	 */
	private static $instance;

	/**
	 * The singleton constructor.
	 *
	 * @since 1.0
	 *
	 * @return HappyForms_Form_Admin
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		self::$instance->hook();

		return self::$instance;
	}

	/**
	 * Register hooks.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function hook() {
		$post_type = happyforms_get_form_controller()->post_type;

		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_filter( 'admin_url', array( $this, 'admin_url' ), 10, 2 );
		add_filter( "views_edit-{$post_type}", array( $this, 'table_view_links' ) );
		add_filter( 'get_edit_post_link', array( $this, 'get_edit_post_link' ), 10, 3 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( "bulk_actions-edit-{$post_type}", array( $this, 'bulk_actions' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );
		add_action( 'load-edit.php', array( $this, 'define_screen_settings' ) );
		add_filter( 'pre_months_dropdown_query', array( $this, 'pre_months_dropdown_query' ), 10, 2 );
		add_filter( "manage_{$post_type}_posts_columns", array( $this, 'column_headers' ), PHP_INT_MAX );
		add_filter( "manage_edit-{$post_type}_sortable_columns", array( $this, 'sortable_columns' ) );
		add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'column_content' ), 10, 2 );
		add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 10, 2 );
		add_action( 'load-edit.php', array( $this, 'duplicate_form_redirect' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
		add_filter( 'admin_footer_text', 'happyforms_admin_footer' );
		add_action( 'load-edit.php', array( $this, 'change_bulk_draft_post_status' ) );
		add_action( 'transition_post_status', array( $this, 'change_draft_post_status' ), 10, 3 );
	}

	/**
	 * Action: output custom styles for the All Forms screen.
	 *
	 * @since 1.0
	 *
	 * @hooked action admin_head
	 *
	 * @return void
	 */
	public function admin_head() {
		global $pagenow;
		$post_type = happyforms_get_form_controller()->post_type;

		if ( 'edit.php' === $pagenow && $post_type === get_post_type() ) : ?>
		<style>
		.alignleft.actions { height: 32px; }
		fieldset.view-mode { display: none; }
		</style>
		<?php endif;
	}

	/**
	 * Filter: filter the Add New link url
	 *
	 * @since 1.5
	 *
	 * @hooked filter admin_url
	 *
	 * @param string $url  The current url
	 * @param string $path The current path
	 *
	 * @return array
	 */
	public function admin_url( $url, $path ) {
		$post_type = happyforms_get_form_controller()->post_type;
		$new_form_url = 'post-new.php?post_type=' . $post_type;

		if ( $new_form_url === $path ) {
			$url = happyforms_get_form_edit_link( 0 );
		}

		return $url;
	}

	/**
	 * Filter: filter the row actions links
	 * below entries in the All Forms admin table.
	 *
	 * @since 1.0
	 *
	 * @hooked filter views_edit-happyform
	 *
	 * @param array $views The original array of action links.
	 *
	 * @return array
	 */
	public function table_view_links( $views ) {
		unset( $views['publish'] );

		return $views;
	}

	/**
	 * Filter: return a form post object edit url.
	 *
	 * @since 1.0
	 *
	 * @hooked filter get_edit_post_link
	 *
	 * @param string     $link    The original url.
	 * @param int|string $post_id The ID of the form post object.
	 * @param string     $context The context this function is being called in.
	 *
	 * @return string
	 */
	public function get_edit_post_link( $link, $post_id, $context ) {
		return happyforms_get_form_edit_link( $post_id, '', $context );
	}

	/**
	 * Filter: tweak the text of the form post actions admin notices.
	 *
	 * @since 1.0
	 *
	 * @hooked filter post_updated_messages
	 *
	 * @param array $messages The messages configuration.
	 *
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		$post_type = happyforms_get_form_controller()->post_type;
		$permalink = get_permalink();
		$preview_url = get_preview_post_link();
		$view_form_link_html = sprintf(
			' <a href="%1$s">%2$s</a>',
			esc_url( $permalink ),
			__( 'View form' )
		);
		$preview_post_link_html = sprintf(
			' <a target="_blank" href="%1$s">%2$s</a>',
			esc_url( $preview_url ),
			__( 'Preview form' )
		);

		$messages[$post_type] = array(
			'',
			__( 'Form updated.' ) . $view_form_link_html,
			__( 'Custom field updated.' ),
			__( 'Custom field deleted.' ),
			__( 'Form updated.' ),
			isset($_GET['revision']) ? sprintf( __( 'Form restored to revision from %s.' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			__( 'Form published.' ) . $view_form_link_html,
			__( 'Form saved.' ),
			__( 'Form submitted.' ),
			__( 'Form scheduled.' ),
			__( 'Form draft updated.' ) . $preview_post_link_html,
		);

		return $messages;
	}

	/**
	 * Filter: tweak the text of the form post
	 * bulk actions admin notices.
	 *
	 * @since 1.0
	 *
	 * @hooked filter bulk_post_updated_messages
	 *
	 * @param array $messages The messages configuration.
	 * @param int   $count    The amount of posts for each bulk action.
	 *
	 * @return array
	 */
	public function bulk_post_updated_messages( $messages, $count ) {
		$post_type = happyforms_get_form_controller()->post_type;

		$messages[$post_type] = array(
			'updated'   => _n( '%s form updated.', '%s forms updated.', $count['updated'] ),
			'locked'    => _n( '%s form not updated, somebody is editing it.', '%s forms not updated, somebody is editing them.', $count['locked'] ),
			'deleted'   => _n( '%s form permanently deleted.', '%s forms permanently deleted.', $count['deleted'] ),
			'trashed'   => _n( '%s form moved to the Trash.', '%s forms moved to the Trash.', $count['trashed'] ),
			'untrashed' => _n( '%s form restored from the Trash.', '%s forms restored from the Trash.', $count['untrashed'] ),
		);

		return $messages;
	}

	public function bulk_actions( $actions ) {
		unset( $actions['edit'] );

		return $actions;
	}

	/**
	 * Action: ensure the current screen object is initialized.
	 *
	 * @since 1.0
	 *
	 * @hooked action load-edit.php
	 *
	 * @return void
	 */
	public function define_screen_settings() {
		$screen = get_current_screen();
	}

	/**
	 *
	 * Filters the months list in the "All dates" 
	 * filter dropdown.
	 *
	 */
	public function pre_months_dropdown_query( $months, $post_type ) {
		if ( $post_type !== happyforms_get_form_controller()->post_type ) {
			return $months;
		}

		global $wpdb, $wp_locale;

		$extra_checks = "AND post_status != 'auto-draft'";
		
		if ( ! isset( $_GET['post_status'] ) ) {
			$extra_checks .= " AND post_status != 'trash' AND post_status != 'archive'";
		} elseif ( $_GET['post_status'] !== 'all' ) {
			$extra_checks = $wpdb->prepare( ' AND post_status = %s', $_GET['post_status'] );
		}

		$query = $wpdb->prepare("
			SELECT DISTINCT YEAR( post_modified ) AS year, MONTH( post_modified ) AS month
			FROM $wpdb->posts
			WHERE post_type = %s
			$extra_checks
			ORDER BY post_modified DESC",
			$post_type
		);

		$months = $wpdb->get_results( $query );

		return $months;
	}

	/**
	 * Filter: filter the column headers for the
	 * All Forms admin screen table.
	 *
	 * @since 1.0
	 *
	 * @hooked filter manage_happyform_posts_columns
	 *
	 * @param array $columns  The original table headers.
	 *
	 * @return array          The filtered table headers.
	 */
	public function column_headers( $columns ) {
		$date_column = $columns['date'];
		$columns = array(
			'cb' => $columns['cb'],
			'title' => $columns['title'],
			'shortcode' => __( 'Shortcode', 'happyforms' ),
			'author' => __( 'Author', 'happyforms' ),
			'modified' => __( 'Date', 'happyforms' )
		);
		/**
		 * Filter the column headers of forms admin table.
		 *
		 * @since 1.4.5
		 *
		 * @param array  $columns Current column headers.
		 *
		 * @return array
		 */
		$columns = apply_filters( 'happyforms_manage_form_column_headers', $columns );

		return $columns;
	}

	/**
	 *
	 * Make modified date columnn sortable
	 *
	 */
	public function sortable_columns( $columns ) {
		$columns['modified'] = 'modified';

		return $columns;
	}

	/**
	 * Filter: output the columns content for the
	 * All Forms admin screen table.
	 *
	 * @since 1.0
	 *
	 * @hooked filter manage_happyform_posts_custom_column
	 *
	 * @param array      $column   The current column header.
	 * @param int|string $id       The current form post object ID.
	 *
	 * @return void
	 */
	public function column_content( $column, $id ) {
		switch ( $column ) {
			case 'shortcode':
				$shortcode = happyforms_get_shortcode( $id );
				?>
				<div class="happyforms-shortcode-col">
					<span><?php echo esc_html( $shortcode ); ?></span>
					<input type="text" value="<?php echo esc_attr( $shortcode ); ?>">
					<a href="#" class="happyforms-shortcode-clipboard"><?php _e( 'Copy to clipboard', 'happyforms' ); ?></a>
				</div>
				<?php
				break;
			case 'modified':
				$t_time = sprintf(
					__( '%1$s at %2$s' ),
					get_the_modified_time( __( 'Y/m/d' ), $id ),
					get_the_modified_time( __( 'g:i a' ), $id )
				);

				printf( '%1$s<br>%2$s', __( 'Last modified', 'happyforms' ), $t_time );
				break;
		}
	}

	/**
	 *
	 * Use modified date for date filtering
	 *
	 */
	public function pre_get_posts( $query ) {
		if ( ! is_admin() 
			|| ! $query->is_main_query() 
			|| $query->get( 'post_type' ) !== happyforms_get_form_controller()->post_type ) {

			return;
		}

		$m = $query->get( 'm' );

		if ( empty( $m ) ) {
			return;
		}

		$query->set( 'm', '' );

		$year = substr( $m, 0, 4 );
		$month = substr( $m, 4, 2 );

		$query->set( 'date_query', array(
			array(
				'column' => 'post_modified',
				'year' => $year,
				'month' => $month,
			),
		) );
	}

	/**
	 * Filter: filter the row actions contents for the
	 * All Form admin screen table.
	 *
	 * @since 1.0
	 *
	 * @hooked filter post_row_actions
	 *
	 * @param array   $actions The original array of action contents.
	 * @param WP_Post $post    The current post object.
	 *
	 * @return array           The filtered array of action contents.
	 */
	public function row_actions( $actions, $post ) {
		$post_type = happyforms_get_form_controller()->post_type;

		if ( $post->post_type === $post_type ) {
			if ( ! isset( $actions['inline hide-if-no-js'] ) ) {
				return $actions;
			}

			$actions = array();
			$link_template = '<a href="%s">%s</a>';
			$duplicate_url = add_query_arg(
				array(
					'happyforms_duplicate_nonce' => wp_create_nonce( 'duplicate' ),
					'post_type' => $post_type,
					'form_id' => $post->ID,
				),
				admin_url( 'edit.php' )
			);

			$links = array(
				'edit' => array(
					__( 'Edit', 'happyforms' ),
					get_edit_post_link( $post->ID, 'build' )
				),
				'duplicate' => array(
					__( 'Duplicate', 'happyforms' ),
					$duplicate_url,
				),
				'trash' => array(
					__( 'Trash', 'happyforms' ),
					get_delete_post_link( $post->ID, '' )
				),
			);

			foreach( $links as $key => $values ) {
				$actions[$key] = sprintf( $link_template, $values[1], $values[0] );
			}
		}

		return $actions;
	}

	/**
	 * Action: handle the redirect following a form
	 * duplicate action.
	 *
	 * @since 1.0
	 *
	 * @hooked action load-edit.php
	 *
	 * @return void
	 */
	public function duplicate_form_redirect() {
		if ( ! isset( $_GET['happyforms_duplicate_nonce'] )
			|| ! wp_verify_nonce( $_GET['happyforms_duplicate_nonce'], 'duplicate' )
			|| ! isset( $_GET['form_id'] ) ) {
			return;
		}

		$form = get_post( $_GET['form_id'] );

		if ( is_a( $form, 'WP_Post' ) ) {
			$controller = happyforms_get_form_controller();
			$new_form_id = $controller->duplicate( $form );

			if ( ! is_wp_error( $new_form_id ) ) {
				$redirect = add_query_arg(
					array( 'post_type' => $controller->post_type ),
					admin_url( 'edit.php' )
				);

				$notice = sprintf(
					'%s <a href="%s">%s</a>',
					__( '1 form duplicated.', 'happyforms' ),
					get_delete_post_link( $new_form_id, '', true ),
					__( 'Undo', 'happyforms' )
				);

				$admin_notices = happyforms_get_admin_notices();
				$admin_notices->register(
					'happyforms_form_duplicated',
					$notice,
					array(
						'type' => 'success',
						'screen' => array( 'edit-happyform' ),
						'one-time' => true,
					)
				);

				wp_safe_redirect( $redirect );
				exit();
			}
		}
	}

	/**
	 * Action: change bulk post from draft to publish.
	 *
	 * @since 1.0
	 *
	 * @hooked action load-edit.php
	 *
	 * @return void
	 */
	public function change_bulk_draft_post_status() {
		$post_type = happyforms_get_form_controller()->post_type;

		$args = array(
			'post_type' => $post_type,
			'post_status' => 'draft',
			'posts_per_page' => -1,
			'fields' => 'ids'
		);

		$draft_forms = get_posts( $args );

		foreach( $draft_forms as $form_id ) {
			$query = array(
				'ID' => $form_id,
				'post_status' => 'publish',
			);
			wp_update_post( $query, true );
		}
	}

	/**
	 * Action: change post from draft to publish.
	 *
	 * @since 1.24.1
	 *
	 * @hooked action transition_post_status
	 *
	 * @return void
	 */
	public function change_draft_post_status( $new, $old, $post ) {
		$post_type = happyforms_get_form_controller()->post_type;

		if ( $post_type !== $post->post_type ) {
			return;
		}

		if ( $new === $old ) {
			return;
		}

		if ( 'draft' !== $new ) {
			return;
		}

		wp_update_post( array( 'ID' => $post->ID, 'post_status' => 'publish' ) );
	}


	/**
	 * Action: output Javascript logic for copying
	 * shortcodes to clipboard in the All Forms admin screen.
	 *
	 * @since 1.0
	 *
	 * @hooked action admin_footer
	 *
	 * @return void
	 */
	public function admin_footer() {
		global $pagenow;
		$post_type = happyforms_get_form_controller()->post_type;

		if ( 'edit.php' === $pagenow && $post_type === get_post_type() ) : ?>
		<script type="text/javascript">
		// Shortcode copy-to-clipboard
		( function ( $ ) {
			$( document ).on( 'click', 'a.happyforms-shortcode-clipboard', function( e ) {
				e.preventDefault();

				var $shortcode = $( e.target ).prev();
				$shortcode.focus().select();

				try {
					document.execCommand( 'copy' );
				} catch( e ) {}
			} );
		} ) ( jQuery );
		</script>
		<?php endif;
	}

}

/**
 * Initialize the HappyForms_Form_Admin class immediately.
 */
HappyForms_Form_Admin::instance();
