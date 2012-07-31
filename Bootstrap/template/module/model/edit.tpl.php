<h1>Edit</h1>

<form method="post">

	<?= $this->display( $this->model, 'edit' ); ?>

	<button class="btn btn-primary" type="submit"><i class="icon-ok"></i> Validate</button>
	<a class="btn" href="<?= $page_url( 'view', $this->model->id ) ?>"><i class="icon-arrow-left"></i> Cancel</a>
</form>
