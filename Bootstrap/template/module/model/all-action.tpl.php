<div class="action">
	<?php
	if ( $this->from || $this->filter ) {
	?>
		<a class="btn" href="<?= $page_url( 'all' ) ?>"><i class="icon-th-list"></i> See all <?= $this->model_type ?></a>
	<?php
	} else {
		foreach ( $this->model_subtypes as $subtype ) {
		?>
			<a class="btn" href="<?= $page_url( 'create', $subtype ) ?>"><i class="icon-plus-sign"></i> Create <?= ucfirst( $subtype ) ?></a>
		<?php
		}
	}
	if ( $this->from ) {
	?>
		<a class="btn" href="<?= \Supersoniq\module_model_url( $this->from ) ?>"><i class="icon-search"></i> Back to <?= $this->from->name( ) ?></a>
	<?php
	}
	?>
</div>
