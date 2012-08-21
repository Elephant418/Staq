<h1>Deleted Models <?= $this->model->type ?></h1>

<?php
if ( count( $this->archives ) == 0 ) {
?>
	<p><em>There is no elements to display</em></p>
<?php
} else {
	
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
					<a href="<?= $page_url( 'see', $version->model_id, $version->model_attributes_version ) ?>" >
						Version : <?= $version->model_type_version ?>.<?= $version->model_attributes_version ?>
					</a><br/>
					Modification : <?= date_format( \DateTime::createFromFormat( 'Y-m-d G:i:s', $version->date_version ), 'd/m/Y - G:i' ); ?><br/>
					Changed by the IP : <?= $version->ip_version ?><br/><br/>
				<?php
				}
				$ignore[] = $archive->model_id;
				?>
					<a class="btn btn-danger" href="<?= $page_url( 'erase', $archive->model_id ) ?>"><i class="icon-remove"></i> Erase all</a><br/><br/>
				<?php
			}
		}
	}
}
?>
<a class="btn" href="<?= $page_url( 'all' ) ?>"><i class="icon-th-list"></i> List</a>
<?php
	if ( count( $this->archives ) != 0 ) {
		?>
		<a class="btn" href="<?= $page_url( 'create' ) ?>"><i class="icon-plus-sign"></i> Create</a>
		<?php 
	}
?>
