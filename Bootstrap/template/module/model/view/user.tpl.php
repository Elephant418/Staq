<h1><?= $this->model->name( ) ?></h1>

<?= $this->display( $this->model ); ?>

<a class="btn" href="<?= $page_url( 'all' ) ?>"><i class="icon-th-list"></i> List</a>
<a class="btn" href="<?= $page_url( 'edit', $this->model->id ) ?>"><i class="icon-pencil"></i> Edit</a>
<a class="btn" href="<?= $page_url( 'edit_password', $this->model->id ) ?>"><i class="icon-pencil"></i> Edit Password</a>
<a class="btn btn-danger" href="<?= $page_url( 'delete', $this->model->id ) ?>" onclick="return confirm( 'Do you want to delete \'<?= $this->model->name( ) ?>\' ?')"><i class="icon-remove"></i> Delete</a>
<?php if ( $this->model->is_versioned ) { ?>
	<a class="btn" href="<?= $page_url( 'archive', $this->model->id ) ?>"><i class="icon-th-list"></i> Archives</a>
<?php } ?>
