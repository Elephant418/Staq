<h1>Edit Password</h1>

<form method="post">

	<label><b>Password</b> : <?= $this->display( $this->model->attribute( 'password' ), 'edit' ) ?></label>

	<button class="btn btn-primary" type="submit"><i class="icon-ok"></i> Validate</button>
	<a class="btn" href="<?= $page_url( 'view', $this->model->id ) ?>"><i class="icon-arrow-left"></i> Cancel</a>
</form>
