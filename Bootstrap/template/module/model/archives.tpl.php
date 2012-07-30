<h1>Archives</h1>

<?php
foreach ( $this->archives as $archive ) {
	$attributes = $archive->model_attributes;
	if ( $archive == $archive->last_version( $archive->model_id, $archive->model_type ) ) {
		?><h4>Last model</h4><?php
		if ( $archive->current_version( $archive->model_id, $archive->model_type ) ) {
		?>
			<a href="<?= $action_url( 'view', $archive->model_id ) ?>" >
				Version : <?= $archive->model_type_version ?>.<?= $archive->model_attributes_version ?>
			</a> (Current) 
			<button onclick="location.href='<?= $action_url( 'edit', $archive->model_id ) ?>'">Edit</button>
		<?php
		} else {
		?>
			<a href="<?= $action_url( 'see', $archive->model_id, $archive->model_attributes_version ) ?>" >
			Version : <?= $archive->model_type_version ?>.<?= $archive->model_attributes_version ?></a><br/>
			Warning: has been deleted (no current version)<?php
		}
		?>
		<br/>
	<?php
	} else {
		if ( $archive != $archive->last_version( $archive->model_id, $archive->model_type ) ) {
		?>
			<a href="<?= $action_url( 'see', $archive->model_id, $archive->model_attributes_version ) ?>" >Version : <?= $archive->model_type_version ?>.<?= $archive->model_attributes_version ?></a><br>
		<?php
		}
	}
	?>
	Modification : <?= date_format( \DateTime::createFromFormat( 'Y-m-d G:i:s', $archive->date_version ), 'd/m/Y - G:i' ); ?><br/>
	Changed by the IP : <?= $archive->ip_version ?><br/><br/><?php
}
?>
<button onclick="location.href='<?= $page_url( 'all' ) ?>'"><i class="icon-th-list"></i> List</button>
<button onclick="location.href='<?= $page_url( 'create' ) ?>'"><i class="icon-plus-sign"></i> Create</button>
<a class="btn btn-danger" href="<?= $page_url( 'erase', $archive->model_id ) ?>"><i class="icon-remove"></i> Erase All Archives</button>
