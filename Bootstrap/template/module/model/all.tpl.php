<h1>List <?= $this->model_type ?> (<?= count( $this->models ) ?>)</h1>
<?php

include( __DIR__ . '/all-action.tpl.php' );

if ( count( $this->models ) == 0 ) {
?>
	<p><em>There is no elements to display</em></p>
<?php
} else {
?>
<table class="table table-bordered table-striped table-data">

	<?= $this->display( $this->models, 'table' ); ?>

</table>
<?php
}

if ( count( $this->models ) > 10 ) {
	include( __DIR__ . '/all-action.tpl.php' );
}
?>
