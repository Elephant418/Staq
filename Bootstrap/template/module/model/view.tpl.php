<h1>View <?= $this->model->type ?></h1>

<?= $this->display( $this->model ); ?>

<a class="btn" href="<?= $page_url( 'all' ) ?>"><i class="icon-th-list"></i> List</a>
<a class="btn" href="<?= $page_url( 'edit', $this->model->id ) ?>"><i class="icon-pencil"></i> Edit</a>
<?php if ( $this->model->is_versioned ) { ?>
	<a class="btn" href="<?= $page_url( 'archive', $this->model->id ) ?>"><i class="icon-th-list"></i> Archives</a>
<?php } ?>
