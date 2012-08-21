<h1>See <?= $this->model->type ?></h1>

<?php
	$archive = $this->archive;
	$attributes = unserialize( $archive->model_attributes[ 'attributes' ] );
	
	?><h2><?= $archive->model_type . ' ' . $archive->model_id . ' version ' . $archive->model_attributes_version; ?></h2>
	
	<h3>Model</h3><?php
	
	echo $this->display( $this->model );
	
	?><h3>Details</h3><?php
	
	if ( $archive == $archive->last_version( $archive->model_id, $archive->model_type ) ) {
		?><h4>Last model</h4><?php
		if ( ! $archive->current_version( $archive->model_id, $archive->model_type ) ) {
			?>Warning: has been deleted (no current version)<br/><?php
		}
	} else {
		?><br/><?php
	}
	?>
	Modification date : <?= date_format( \DateTime::createFromFormat( 'Y-m-d G:i:s', $archive->date_version ), 'd/m/Y' ); ?><br/>
	Modification hour : <?= date_format( \DateTime::createFromFormat( 'Y-m-d G:i:s', $archive->date_version ), 'G:i' ); ?><br/>
	Changed by the IP : <?= $archive->ip_version ?><br/>
	Version of the model : <?= $archive->model_type_version ?><br/>
	Version of the attributes : <?= $archive->model_attributes_version ?><br/>
	Values of the attributes : <br/>
	<ul><?php
	foreach ( $attributes as $key => $value ) {
		?><li><?= $key ?> = <?= $value ?></li><?php
	}
	?></ul>
<br/>
<a class="btn" href="<?= $page_url( 'archive', $archive->model_id ) ?>"><i class="icon-th-list"></i> List</a>
<a class="btn" href="<?= $page_url( 'restore', $archive->model_id, $archive->model_attributes_version ) ?>"><i class="icon-pencil"></i> Restore</a>
<a class="btn btn-danger" href="<?= $page_url( 'erase', $archive->model_id, $archive->model_attributes_version ) ?>"><i class="icon-remove"></i> Erase</a>
