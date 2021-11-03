<?php $status = happyforms_get_tracking()->get_status(); ?>
<?php if ( 2 > $status['status'] ) : ?>
<div class="happyforms-modal__heading">
	<h1><?php _e( 'Welcome to Happyforms', 'happyforms' ); ?></h1>
	<p><?php _e( 'Tell us about yourself and we\'ll get you set up in no time*' ); ?></p>
</div>
<div class="happyforms-modal__content">
	<form>
		<label for="happyforms-onboarding-email"><?php _e( 'Email address', 'happyforms' ); ?></label>
		<input type="email" id="happyforms-onboarding-email" />
		<label for="happyforms-onboarding-byline-optin">
			<input type="checkbox" id="happyforms-onboarding-byline-optin" />
			<span><?php _e( 'Yes, I want to help support the free plugin by adding a powered by link in the footer of my forms and emails.', 'happyforms' ); ?></span>
		</label>
		<button type="submit" class="button button-primary button-hero"><?php _e( 'Complete Set Up', 'happyforms' ); ?></button>
		<p><?php _e( '*By submitting you\'re okay for us to send you occasional marketing emails.', 'happyforms' ); ?></p>
	</form>
</div>
<script type="text/javascript">
jQuery( function( $ ) { $( '#happyforms-onboarding-email' ).trigger( 'focus' ); } );
</script>
<?php else : ?>
<div class="happyforms-modal__heading">
	<h1><?php _e( 'Powered by Happyforms', 'happyforms' ); ?></h1>
	<p><?php _e( 'All it takes is one little "yes" from you to help us out.', 'happyforms' ); ?></p>
</div>
<div class="happyforms-modal__content">
	<form>
		<label for="happyforms-onboarding-byline-optin">
			<input type="checkbox" id="happyforms-onboarding-byline-optin" />
			<span><?php _e( 'Yes, I want to help support the free plugin by adding a powered by link in the footer of my forms and emails.', 'happyforms' ); ?></span>
		</label>
		<button type="submit" class="button button-primary button-hero"><?php _e( 'Complete', 'happyforms' ); ?></button>
	</form>
</div>
<?php endif; ?>
