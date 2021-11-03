<p>
	<label for="<%= instance.id %>_width"><?php _e( 'Width', 'happyforms' ); ?></label>
	<select id="<%= instance.id %>_width" name="width" data-bind="width" class="widefat">
		<option value="full"<%= (instance.width == 'full') ? ' selected' : '' %>><?php _e( 'Full', 'happyforms' ); ?></option>
		<option value="half"<%= (instance.width == 'half') ? ' selected' : '' %>><?php _e( 'Half', 'happyforms' ); ?></option>
		<option value="third"<%= (instance.width == 'third') ? ' selected' : '' %>><?php _e( 'Third', 'happyforms' ); ?></option>
		<option value="quarter"<%= (instance.width == 'quarter') ? ' selected' : '' %>><?php _e( 'Quarter', 'happyforms' ); ?></option>
		<option value="auto"<%= (instance.width == 'auto') ? ' selected' : '' %>><?php _e( 'Auto', 'happyforms' ); ?></option>
	</select>
</p>
<p class="width-options" style="display: none">
	<label>
		<input type="checkbox" class="checkbox apply-all-check" value="" data-apply-to="width" /> <?php _e( 'Apply to all fields', 'happyforms' ); ?>
	</label>
</p>