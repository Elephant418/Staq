<select name="model[<?= $this->content->name ?>]">
<?php
	$current_id = $this->content->get_id( );
	foreach ( $this->content->get_related_model( ) as $related ) {
	?>
		<option 
			value="<?= $related->id ?>" 
			<?= ( $current_id == $related->id ) ? 'selected="selected"' : '' ?>
		> <?= $related->name( ) ?>
		</option>
	<?php
	}
?>
</select>
