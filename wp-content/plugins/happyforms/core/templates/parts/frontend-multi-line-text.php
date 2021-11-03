<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php if ( 'as_placeholder' !== $part['label_placement'] ) : ?>
			<?php happyforms_the_part_label( $part, $form ); ?>
		<?php endif; ?>

		<?php if ( 'tooltip' !== $part['description_mode'] || 'hidden' === $part['label_placement'] ) : ?>
			<?php happyforms_print_part_description( $part ); ?>
		<?php endif; ?>

		<div class="happyforms-part__el">
			<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

			<?php if ( 1 == $part['rich_text'] ) : ?>
				<div class="happyforms-visual-editor">
			<?php endif; ?>

			<textarea id="<?php happyforms_the_part_id( $part, $form ); ?>" name="<?php happyforms_the_part_name( $part, $form ); ?>" rows="<?php echo $part['rows']; ?>" placeholder="<?php echo esc_attr( $part['placeholder'] ); ?>" <?php happyforms_the_part_attributes( $part, $form ); ?>><?php happyforms_the_part_value( $part, $form ); ?></textarea>
			<?php if ( 'as_placeholder' === $part['label_placement'] ) : ?>
				<?php happyforms_the_part_label( $part, $form ); ?>
			<?php endif; ?>

			<?php if ( 1 == $part['rich_text'] ) : ?>
				</div>
			<?php endif; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
