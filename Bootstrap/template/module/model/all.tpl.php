<h1>
	List <?= $this->model_type ?> 
<?php
	if ( $this->from ) {
		echo 'from ' . $this->from->type . ' "' . $this->from->name( ) . '"';
	} else if ( $this->filter ) {
		echo 'filtered "' . $this->filter . '"';
	}
?>
	(<?= $this->pagination->count ?>)
</h1>
<?php

include( __DIR__ . '/all-action.tpl.php' );

if ( count( $this->models ) == 0 ) {
?>
	<p><em>There is no elements to display</em></p>
<?php
} else {
	$pagination_text = 'search';
	include( __DIR__ . '/all-pagination.tpl.php' );
?>
<table class="table table-bordered table-striped table-data">

	<?= $this->display( $this->models, 'table' ); ?>

</table>
<?php
	$pagination_text = 'position';
	include( __DIR__ . '/all-pagination.tpl.php' );
}

if ( count( $this->models ) > 10 ) {
	include( __DIR__ . '/all-action.tpl.php' );
}
?>
