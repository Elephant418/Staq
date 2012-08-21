<h1>List <?= $this->model_type ?> (<?= count( $this->models ) ?>)</h1>

<div class="action">
	<a class="btn" href="<?= $page_url( 'create' ) ?>"><i class="icon-plus-sign"></i> Create</a>
</div>

<?php

if ( count( $this->models ) == 0 ) {
?>
	<p><em>There is no elements to display</em></p>
<?php
} else {
?>
<table class="table table-bordered table-striped">

	<?= $this->display( $this->models, 'table' ); ?>

</table>
<?php
}

if ( count( $this->models ) > 10 ) {
?>
<div class="action">
	<a class="btn" href="<?= $page_url( 'create' ) ?>"><i class="icon-plus-sign"></i> Create</a>
</div>
<?php
}
?>
