<h1>View</h1>

<?php
foreach ( $this->model->get_attribute_fields( ) as $name ) {
	$attribute = $this->model->attribute( $name );
?>
	<p><b><?= $name ?></b> : <?= $this->display( $attribute ) ?></p>
<?php
}
?>

<a class="btn" href="<?= $page_url( 'all' ) ?>"><i class="icon-th-list"></i> List</a>
<a class="btn" href="<?= $page_url( 'create' ) ?>"><i class="icon-plus-sign"></i> Create</a>
<a class="btn" href="<?= $page_url( 'edit', $this->model->id ) ?>"><i class="icon-pencil"></i> Edit</a>
<?php if ( $this->model->is_versioned ) { ?>
	<a class="btn" href="<?= $page_url( 'archive', $this->model->id ) ?>"><i class="icon-th-list"></i> Archives</a>
<?php } ?>
