<h1>Archives</h1>

<?php

$model_id = '';

foreach ( $this->archives as $archive ) {
	
	if ( $model_id == '' ) {
		$model_id = $archive->model_id;
		?><h2><?= $archive->model_type . ' ' . $model_id ?></h2><?php
	}
	
	if ( $archive == $archive->last_version( $archive->model_id, $archive->model_type ) ) {
		?><h3>Last model</h3><?php
		if ( $archive->current_version( $archive->model_id, $archive->model_type ) ) {
		?>
			<a href="<?= $page_url( 'view', $archive->model_id ) ?>" >
				Version : <?= $archive->model_type_version ?>.<?= $archive->model_attributes_version ?>
			</a> (Current) 
			<a class="btn" href="<?= $page_url( 'edit', $archive->model_id ) ?>"><i class="icon-pencil"></i> Edit</a>
		<?php
		} else {
		?>
			<a href="<?= $page_url( 'see', $archive->model_id, $archive->model_attributes_version ) ?>" >
			Version : <?= $archive->model_type_version ?>.<?= $archive->model_attributes_version ?></a><br/>
			Warning: has been deleted (no current version)<?php
		}
		?>
		<br/>
	<?php
	} else {
		if ( $archive != $archive->last_version( $archive->model_id, $archive->model_type ) ) {
		?>
			<a href="<?= $page_url( 'see', $archive->model_id, $archive->model_attributes_version ) ?>" >Version : <?= $archive->model_type_version ?>.<?= $archive->model_attributes_version ?></a><br>
		<?php
		}
	}
	?>
	Modification : <?= date_format( \DateTime::createFromFormat( 'Y-m-d G:i:s', $archive->date_version ), 'd/m/Y - G:i' ); ?><br/>
	Changed by the IP : <?= $archive->ip_version ?><br/><br/><?php
}
?>
<a class="btn" href="<?= $page_url( 'all' ) ?>"><i class="icon-th-list"></i> List</a>
<a class="btn" href="<?= $page_url( 'create' ) ?>"><i class="icon-plus-sign"></i> Create</a>
<a class="btn btn-danger" href="<?= $page_url( 'erase', $model_id ) ?>"><i class="icon-remove"></i> Erase All Archives</a>
