<div class="<?php happyforms_the_part_class( $part, $form ); ?>" id="<?php happyforms_the_part_id( $part, $form ); ?>-part" <?php happyforms_the_part_data_attributes( $part, $form ); ?>>
	<div class="happyforms-part-wrap">
		<?php happyforms_the_part_label( $part, $form ); ?>

		<?php if ( 'tooltip' !== $part['description_mode'] || 'hidden' === $part['label_placement'] ) : ?>
			<?php happyforms_print_part_description( $part ); ?>
		<?php endif; ?>

		<?php do_action( 'happyforms_part_input_before', $part, $form ); ?>

		<div class="happyforms-part__el">
			<?php
			$options = happyforms_get_part_options( $part['options'], $part, $form );
			$value = happyforms_get_part_value( $part, $form );
			$checkmark_content = '<span class="happyforms-radio-circle"></span>';

			foreach( $options as $o => $option ) : ?>
			<?php
			$checked = false;

			if ( is_string( $value ) ) {
				$checked = checked( $value, $o, false );
			}

			if ( false === $checked ) {
				$checked = checked( 1, $option['is_default'], false );
			}

			if ( 1 == $part['display_as_letters'] ) {
				$letter = happyforms_get_part_choice_letter( $option, $part );
				$checkmark_content = $letter;
			}
			?>
			<div class="happyforms-part__option happyforms-part-option" id="<?php echo esc_attr( $option['id'] ); ?>">
				<label class="option-label">
					<input type="radio" class="happyforms-visuallyhidden" name="<?php happyforms_the_part_name( $part, $form ); ?>" value="<?php echo $o; ?>" <?php echo $checked; ?> <?php happyforms_the_part_attributes( $part, $form ); ?>>
					<span class="checkmark"><?php echo $checkmark_content; ?></span>
					<span class="label"><?php echo esc_attr( $option['label'] ); ?></span><?php echo $option['submissions_left_label']; ?>
				</label>
				<span class="happyforms-part-option__description"><?php echo esc_attr( $option['description'] ); ?></span>
			</div>
			<?php endforeach; ?>

			<?php do_action( 'happyforms_part_input_after', $part, $form ); ?>

			<?php happyforms_part_error_message( happyforms_get_part_name( $part, $form ) ); ?>
		</div>
	</div>
</div>
