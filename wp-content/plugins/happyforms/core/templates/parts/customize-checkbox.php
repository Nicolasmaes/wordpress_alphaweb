<script type="text/template" id="customize-happyforms-checkbox-template">
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
	<p>
		<label for="<%= instance.id %>_description"><?php _e( 'Hint', 'happyforms' ); ?></label>
		<textarea id="<%= instance.id %>_description" data-bind="description"><%= instance.description %></textarea>
	</p>

	<?php do_action( 'happyforms_part_customize_checkbox_before_options' ); ?>

	<div class="options">
		<ul class="option-list"></ul>
		<h3><?php _e( 'Choices', 'happyforms' ); ?></h3>
		<p class="no-options description"><?php _e( 'No choices added yet.', 'happyforms' ); ?></p>
	</div>
	<div class="options-import">
		<h3><?php _e( 'Choices', 'happyforms' ); ?></h3>
		<textarea class="option-import-area" cols="30" rows="10" placeholder="<?php _e( 'Type or paste your choices here, adding each on a new line.' ); ?>"></textarea>
	</div>
	<p class="links mode-manual">
		<a href="#" class="button add-option"><?php _e( 'Add choice', 'happyforms' ); ?></a>
		<span class="centered">
			<a href="#" class="import-options"><?php _e( 'Or, bulk add choices', 'happyforms' ); ?></a>
		</span>
	</p>
	<p class="links mode-import">
		<a href="#" class="button import-option"><?php _e( 'Add choices', 'happyforms' ); ?></a>
		<span class="centered">
			<a href="#" class="add-options"><?php _e( 'Cancel', 'happyforms' ); ?></a>
		</span>
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.required ) { %>checked="checked"<% } %> data-bind="required" /> <?php _e( 'Require an answer', 'happyforms' ); ?>
		</label>
	</p>

	<?php do_action( 'happyforms_part_customize_checkbox_after_options' ); ?>

	<?php do_action( 'happyforms_part_customize_checkbox_before_advanced_options' ); ?>

	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.display_as_letters ) { %>checked="checked"<% } %> data-bind="display_as_letters" /> <?php _e( 'Show as alphabetical choice', 'happyforms' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.show_select_all ) { %>checked="checked"<% } %> data-bind="show_select_all" /> <?php _e( 'Add \'select all\' choice', 'happyforms' ); ?>
		</label>
	</p>
	<div class="happyforms-nested-settings" data-trigger="show_select_all" style="display: <%= ( instance.show_select_all ) ? 'block' : 'none' %>">
		<p>
			<label for="<%= instance.id %>_select_all_label"><?php _e( '\'Select all\' label', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_select_all_label" maxlength="30" class="widefat title" value="<%= instance.select_all_label %>" data-bind="select_all_label" />
		</p>
	</div>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.other_option ) { %>checked="checked"<% } %> data-bind="other_option" /> <?php _e( 'Add \'other\' choice', 'happyforms' ); ?>
		</label>
	</p>
	<div class="happyforms-nested-settings" data-trigger="other_option" style="display: <%= ( instance.other_option ) ? 'block' : 'none' %>">
		<p>
			<label for="<%= instance.id %>_other_option_label"><?php _e( '\'Other\' label', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_other_option_label" maxlength="30" class="widefat title" value="<%= instance.other_option_label %>" data-bind="other_option_label" />
		</p>
		<p>
			<label for="<%= instance.id %>_other_option_placeholder"><?php _e( '\'Other\' placeholder', 'happyforms' ); ?></label>
			<input type="text" id="<%= instance.id %>_other_option_placeholder" maxlength="50" class="widefat title" value="<%= instance.other_option_placeholder %>" data-bind="other_option_placeholder" />
		</p>
	</div>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.shuffle_options ) { %>checked="checked"<% } %> data-bind="shuffle_options" /> <?php _e( 'Randomize choices to prevent bias', 'happyforms' ); ?>
		</label>
	</p>
	<p>
		<label>
			<input type="checkbox" class="checkbox" value="1" <% if ( instance.limit_choices ) { %>checked="checked"<% } %> data-bind="limit_choices" /> <?php _e( 'Limit choices', 'happyforms' ); ?>
		</label>
	</p>
	<div class="happyforms-nested-settings" data-trigger="limit_choices" style="display: <%= ( instance.limit_choices ) ? 'block' : 'none' %>">
		<p>
			<label for="<%= instance.id %>_limit_choices_min"><?php _e( 'Min choices', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_limit_choices_min" class="widefat title happyforms-checkbox-limit-min" min="1" value="<%= instance.limit_choices_min %>" data-trigger="limit_choices_min" data-bind="limit_choices_min" />
		</p>
		<p>
			<label for="<%= instance.id %>_limit_choices_max"><?php _e( 'Max choices', 'happyforms' ); ?></label>
			<input type="number" id="<%= instance.id %>_limit_choices_max" class="widefat title happyforms-checkbox-limit-max" min="1" value="<%= instance.limit_choices_max %>" data-trigger="limit_choices_max" data-bind="limit_choices_max" />
		</p>
	</div>
	<p>
		<label for="<%= instance.id %>_display_type"><?php _e( 'Choices display', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_display_type" name="display_type" data-bind="display_type" class="widefat">
			<option value="inline"<%= (instance.display_type == 'inline') ? ' selected' : '' %>><?php _e( 'Horizontal', 'happyforms' ); ?></option>
			<option value="block"<%= (instance.display_type == 'block') ? ' selected' : '' %>><?php _e( 'Vertical', 'happyforms' ); ?></option>
		</select>
	</p>
	<p class="part-options-width-setting">
		<label for="<%= instance.id %>_options_width"><?php _e( 'Choices width', 'happyforms' ); ?></label>
		<select id="<%= instance.id %>_options_width" name="options_width" data-bind="options_width" class="widefat">
			<option class="display-type--inline display-type--block" value="full"<%= (instance.options_width == 'full') ? ' selected' : '' %>><?php _e( 'Full', 'happyforms' ); ?></option>
			<option class="display-type--inline" value="half" style="display: <%= ( instance.display_type == 'inline' ) ? 'block' : 'none' %>" <%= (instance.options_width == 'half') ? ' selected' : '' %>><?php _e( 'Half', 'happyforms' ); ?></option>
			<option class="display-type--inline" value="third" style="display: <%= ( instance.display_type == 'inline' ) ? 'block' : 'none' %>" <%= (instance.options_width == 'third') ? ' selected' : '' %>><?php _e( 'Third', 'happyforms' ); ?></option>
			<option class="display-type--inline" value="quarter" style="display: <%= ( instance.display_type == 'inline' ) ? 'block' : 'none' %>" <%= (instance.options_width == 'quarter') ? ' selected' : '' %>><?php _e( 'Quarter', 'happyforms' ); ?></option>
			<option class="display-type--inline display-type--block" value="auto"<%= (instance.options_width == 'auto') ? ' selected' : '' %>><?php _e( 'Auto', 'happyforms' ); ?></option>
		</select>
	</p>

	<?php happyforms_customize_part_width_control(); ?>

	<?php do_action( 'happyforms_part_customize_checkbox_after_advanced_options' ); ?>

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
<script type="text/template" id="customize-happyforms-checkbox-item-template">
	<li data-option-id="<%= id %>">
		<div class="happyforms-part-item-body">
			<div class="happyforms-part-item-handle"></div>
			<label>
				<?php _e( 'Label', 'happyforms' ); ?>:
				<input type="text" class="widefat" name="label" value="<%= label %>">
			</label>
			<div class="happyforms-part-item-advanced">
				<label>
					<input type="checkbox" name="is_default" value="1" <% if (is_default == 1) { %> checked="checked"<% } %>> <?php _e( 'Make this choice default', 'happyforms' ); ?>
				</label><br>
				<label>
					<input type="checkbox" name="limit_submissions" value="1" class="default-option-switch"<% if ( typeof limit_submissions !== 'undefined' && limit_submissions == 1) { %> checked="checked"<% } %>> <?php _e( 'Limit submissions', 'happyforms' ); ?>
				</label>
				<div class="happyforms-nested-settings happyforms-part-item-limit-submission-settings" <% if ( typeof limit_submissions === 'undefined' || limit_submissions != 1 ) { %>style="display: none;"<% } %>>
					<label>
						<?php _e( 'Max submissions', 'happyforms' ); ?>:
						<input type="number" class="widefat" name="limit_submissions_amount" min="1" value="<%= typeof limit_submissions_amount !== 'undefined' ? limit_submissions_amount : '1' %>">
					</label>
					<label>
						<input type="checkbox" name="show_submissions_amount" class="" <% if ( typeof show_submissions_amount !== 'undefined' && show_submissions_amount == 1) { %> checked="checked"<% } %>> <?php _e( 'Show remaining submissions', 'happyforms' ); ?>
					</label><br>
				</div>
			</div>
			<div class="option-actions">
				<a href="#" class="delete-option"><?php _e( 'Delete', 'happyforms' ); ?></a> |
				<a href="#" class="advanced-option"><?php _e( 'More', 'happyforms' ); ?></a>
			</div>
		</div>
	</li>
</script>
