<h1>View</h1>

<?php
foreach ( $this->model->get_attribute_fields( ) as $name ) {
	$attribute = $this->model->attribute( $name );
?>
	<p><b><?= $name ?></b> : <?= $this->display( $attribute ) ?></p>
<?php
}
?>

<a class="btn" href="<?= $page_url( 'all' ) ?>">List</a>
<a class="btn" href="<?= $page_url( 'create' ) ?>">Create</a>
<a class="btn" href="<?= $page_url( 'edit', $this->model->id ) ?>">Edit</a>
