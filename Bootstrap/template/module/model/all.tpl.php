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
				<a class="btn" href="<?= $page_url( 'view', $model->id ) ?>"><i class="icon-search"></i> View</a>
				<a class="btn" href="<?= $page_url( 'edit', $model->id ) ?>"><i class="icon-pencil"></i> Edit</a>
				<a class="btn btn-danger" href="<?= $page_url( 'delete', $model->id ) ?>"><i class="icon-remove"></i> Delete</a>
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
<a class="btn" href="<?= $page_url( 'create' ) ?>"><i class="icon-plus-sign"></i> Create</a>
