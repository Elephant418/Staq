<select name="model[<?= $this->content->name ?>]">
	<option value="empty">Votre choix</option>
<?php
	foreach ( $this->content->options as $value => $name ) {
	?>
		<option 
			value="<?= $value ?>" 
			<?= ( $this->content->value() === $value ) ? 'selected="selected"' : '' ?>
		> <?= $name ?>
		</option>
	<?php
	}
?>
</select>
