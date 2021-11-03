( function( $, settings ) {

	var dashboardInit = happyForms.dashboard.init;

	happyForms.dashboard = $.extend( {}, happyForms.dashboard, {
		init: function() {
			dashboardInit.apply( this, arguments );

			$( document ).on( 'click', '#adminmenu #toplevel_page_happyforms a[href="#responses"]', this.openUpgradeModal );
			$( document ).on( 'click', '#adminmenu #toplevel_page_happyforms a[href="#settings"]', this.openUpgradeModal );
			$( document ).on( 'click', '#adminmenu #toplevel_page_happyforms a[href="#integrations"]', this.openUpgradeModal );

			// Onboarding modal logic
			$( document ).on( 'click', '.happyforms-modal__navigation-item', this.onModalNavigationItemClick );
		},

		openUpgradeModal: function( e ) {
			e.preventDefault();

			happyForms.dashboard.openModal( 'upgrade' );
		},

		onModalNavigationItemClick: function( e ) {
			var $item = $( e.currentTarget );
			
			$( '.happyforms-modal__pages-page' ).removeClass( 'happyforms-modal__pages-page--active' );
			$( '.happyforms-modal__navigation-item' ).removeClass( 'happyforms-modal__navigation-item--active' );

			$item.addClass( 'happyforms-modal__navigation-item--active' );
			$( '.happyforms-modal__pages-page:nth-child(' + ( $item.index() + 1 ) + ')' )
				.addClass( 'happyforms-modal__pages-page--active' );
		},
	} );

} )( jQuery, _happyFormsAdmin );
