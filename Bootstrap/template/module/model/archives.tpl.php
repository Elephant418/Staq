<h1>Deleted Models</h1>

<?php
	
	$ignore = array( );
	$model_id = '';
	
	foreach ( $this->archives as $archive ) {
		
		$model_id = $archive->model_id;
		
		if ( ! in_array( $archive->model_id, $ignore ) ) {
			if ( ! $archive->current_version( $archive->model_id, $archive->model_type ) ) {
				$iterator = new \Model_Archive( );
				$versions = $iterator->get_model_history( $archive->model_id, $archive->model_type );
				?>
				<h3><?= $archive->model_type ?> number <?= $archive->model_id ?> : </h3>
				<?php
				foreach ( $versions as $version ) {
				?>
					<?php //TODO fix the following link (doesn't work with action_url) ?>
					<a href="<?= $page_url( $version->model_type, 'see', $version->model_id, $version->model_attributes_version ) ?>" >
					Version : <?= $version->model_type_version ?>.<?= $version->model_attributes_version ?></a><br/>
					Modification : <?= date_format( \DateTime::createFromFormat( 'Y-m-d G:i:s', $version->date_version ), 'd/m/Y - G:i' ); ?><br/>
					Changed by the IP : <?= $version->ip_version ?><br/><br/>
				<?php
				}
				$ignore[] = $archive->model_id; 
			} else {
			?>
				
			<?php
			}
		}
	}
?>
<a class="btn" href="<?= $page_url( 'all' ) ?>"><i class="icon-th-list"></i> List</a>
<a class="btn" href="<?= $page_url( 'create' ) ?>"><i class="icon-plus-sign"></i> Create</a>
<a class="btn btn-danger" href="<?= $page_url( 'erase', $model_id ) ?>"><i class="icon-remove"></i> Erase All Archives</a>
