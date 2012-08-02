<select name="model[<?= $this->content->name ?>][]" multiple="multiple">
<?php
	$current_ids = $this->content->get_ids( );
	foreach ( $this->content->get_related_model( ) as $related ) {
	?>
		<option 
			value="<?= $related->id ?>" 
			<?= ( in_array( $related->id, $current_ids ) ) ? 'selected="selected"' : '' ?>
		>
		<?= $related->name( ) ?>
		</option>
	<?php
	}
?>
</select>
