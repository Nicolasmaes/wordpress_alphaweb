<div class="happyforms-modal__heading">
	<h1><?php _e( 'What about your data?', 'happyforms' ); ?></h1>
</div>
<div class="happyforms-modal__content">
	<form>
		<?php wp_nonce_field( happyforms_get_deactivation()->modal_action ); ?>
		<label for="happyforms-deactivate-keep-data">
			<input type="radio" name="keep-data" value="yes" id="happyforms-deactivate-keep-data" checked />
			<span><?php _e( 'Keep plugin data (recommended)', 'happyforms' ); ?></span>
		</label>
		<label for="happyforms-deactivate-remove-data">
			<input type="radio" name="keep-data" value="no" id="happyforms-deactivate-remove-data" />
			<span><?php _e( 'Permanently delete plugin data', 'happyforms' ); ?></span>
		</label>
		<button type="submit" class="button button-primary button-hero"><?php _e( 'Deactivate Plugin', 'happyforms' ); ?></button>
	</form>
</div>