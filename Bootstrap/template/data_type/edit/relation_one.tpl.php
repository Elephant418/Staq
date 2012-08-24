<?php
$current_id = $this->content->get_id( );
?>
<select name="model[<?= $this->content->name ?>]">
	<option value="" <?= ( is_null( $current_id ) ) ? 'selected="selected"' : '' ?> >
		No selection
	</option>
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
