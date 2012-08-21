<thead>
	<tr>
		<th>Name</th>
	</tr>
</thead>
<tbody>
<?php
foreach ( $this->content as $model ) {
	$name = $model->name( );
	if ( strlen( str_replace( ' ', '', $name ) ) == 0 ) {
		$name = '<i>No display name</i>';
	}
?>
	<tr>
		<td>
			<a href="<?= $module_page_url( 'Model\\' . $model->type, 'view', $model->id ) ?>">
				<?= $name ?>
			</a>
		</td>
	</tr>
<?
}
?>
</tbody>
