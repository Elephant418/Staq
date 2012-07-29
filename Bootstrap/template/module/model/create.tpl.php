<h1>Create</h1>

<form method="post">
	<?php
	foreach ( $this->model->get_attribute_fields( ) as $name ) {
		$attribute = $this->model->attribute( $name );
	?>
		<label><b><?= $name ?></b> : <?= $this->display( $attribute, 'edit' ) ?></label>
	<?php
	}
	?>

	<button class="btn" type="submit">Validate</button>
	<a class="btn" href="<?= $page_url( 'all' ) ?>">Cancel</a>
</form>
