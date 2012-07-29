<h1>List</h1>
<?php 
if ( count( $this->models ) == 0 ) {
?>
	<p><em>There is no elements to display</em></p>
<?php
} else {
?>
<table class="table table-bordered table-striped">
	<thead>
		<tr>
			<th>Name</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach ( $this->models as $model ) {
	?>
		<tr>
			<td><?= $model->name( ) ?></td>
			<td>
				<a class="btn" href="<?= $page_url( 'view', $model->id ) ?>">View</a>
				<a class="btn" href="<?= $page_url( 'edit', $model->id ) ?>">Edit</a>
			</td>
		</tr>
	<?
	}
	?>
	</tbody>
</table>
<?php
}
?>
<a class="btn" href="<?= $page_url( 'create' ) ?>">Create</a>
