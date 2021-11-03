<script type="text/template" id="customize-happyforms-multi-line-text-template">
	<?php include( happyforms_get_core_folder() . '/templates/customize-form-part-header.php' ); ?>
	<p class="label-field-group">
		<label for="<%= instance.id %>_title"><?php _e( 'Label', 'happyforms' ); ?></label>
		<div class="label-group">
			<input type="text" id="<%= instance.id %>_title" class="widefat title" value="<%= instance.label %>" data-bind="label" />
			<select id="<%= instance.id %>_label_placement" name="label_placement" data-bind="label_placement" class="widefat">
				<option value="show"<%= (instance.label_placement == 'show') ? ' selected' : '' %>><?php _e( 'Show', 'happyforms' ); ?></option>
				<% if ( 'left' == instance.label_placement ) { %>
					<option value="left" selected><?php _e( 'Left', 'happyforms' ); ?></option>
				<% } %>
				<% if ( 'below' == instance.label_placement ) { %>
					<option value="below" selected><?php _e( 'Below', 'happyforms' ); ?></option>
				<% } %>
				<option value="hidden"<%= (instance.label_placement == 'hidden') ? ' selected' : '' %>><?php _e( 'Hide', 'happyforms' ); ?></option>
			</select>
		</div>
	</p>
	<p class="happyforms-placeholder-option" style="display: <%= ( 'as_placeholder' !== instance.label_placement ) ? 'block' : 'none' %>">
		<label for="<%= instance.id %>_placeholder"><?php _e( 'Placeholder', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_placeholder" class="widefat title" value="<%= instance.placeholder %>" data-bind="placeholder" />
	</p>
	<p class="happyforms-default-value-option">
	  <label for="<%= instance.id %>_default_value"><?php _e( 'Prefill', 'happyforms' ); ?></label>
	  <input type="text" id="<%= instance.id %>_default_value" class="widefat title default_value" value="<%= instance.default_value %>" data-bind="default_value" />
	</p>
	<p>
		<label for="<%= instance.id %>_description"><?php _e( 'Hint', 'happyforms' ); ?></label>
		<textarea id="<%= instance.id %>_description" data-bind="description"><%= instance.description %></textarea>
	</p>

	<?php do_action( 'happyforms_part_customize_multi_line_text_before_options' ); ?>

	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'Require an answer', 'happyforms' ); ?>
		</label>
	</p>

	<?php do_action( 'happyforms_part_customize_multi_line_text_after_options' ); ?>

	<?php do_action( 'happyforms_part_customize_multi_line_text_before_advanced_options' ); ?>

	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.limit_input ) { %>checked="checked"<% } %> data-bind="limit_input" /> <?php _e( 'Limit words/characters', 'happyforms' ); ?>
		</label>
	</p>

	<div class="happyforms-nested-settings character-limit-settings" <% if ( ! instance.limit_input ) { %>style="display: none;"<% } %>>
		<p>
			<label for="<%= instance.id %>_character_limit"><?php _e( 'Limit', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_character_limit" class="widefat title" step="1" min="1" value="<%= instance.character_limit %>" data-bind="character_limit" />
		</p>
		<p>
			<label for="<%= instance.id %>_character_limit_mode"><?php _e( 'Count', 'happyforms' ); ?></label>
			<select id="<%= instance.id %>_character_limit_mode" data-bind="character_limit_mode">
				<option value="word_max"<%= (instance.character_limit_mode == 'word_max') ? ' selected' : '' %>><?php _e( 'Max words', 'happyforms' ); ?></option>
				<option value="word_min"<%= (instance.character_limit_mode == 'word_min') ? ' selected' : '' %>><?php _e( 'Min words', 'happyforms' ); ?></option>
				<option value="character_max"<%= (instance.character_limit_mode == 'character_max') ? ' selected' : '' %>><?php _e( 'Max characters', 'happyforms' ); ?></option>
				<option value="character_min"<%= (instance.character_limit_mode == 'character_min') ? ' selected' : '' %>><?php _e( 'Min characters', 'happyforms' ); ?></option>
			</select>
		</p>
	</div>

	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.rich_text ) { %>checked="checked"<% } %> data-bind="rich_text" /> <?php _e( 'Add rich-text editor toolbar', 'happyforms' ); ?>
		</label>
	</p>

	<p>
		<label for="<%= instance.id %>_rows"><?php _e( 'Rows', 'happyforms' ); ?></label>
		<input type="number" id="<%= instance.id %>_rows" min="2" max="20" class="widefat title" value="<%= instance.rows %>" data-bind="rows" />
	</p>

	<?php happyforms_customize_part_width_control(); ?>

	<?php do_action( 'happyforms_part_customize_multi_line_text_after_advanced_options' ); ?>

	<p>
		<label for="<%= instance.id %>_css_class"><?php _e( 'CSS classes', 'happyforms' ); ?></label>
		<input type="text" id="<%= instance.id %>_css_class" class="widefat title" value="<%= instance.css_class %>" data-bind="css_class" />
	</p>

	<div class="happyforms-part-logic-wrap">
		<div class="happyforms-logic-view">
			<?php happyforms_customize_part_logic(); ?>
		</div>
	</div>

	<?php happyforms_customize_part_footer(); ?>
</script>
