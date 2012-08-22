<div class="action">
	<?php
	if ( $this->from ) {
	?>
		<a class="btn" href="<?= $page_url( 'all' ) ?>"><i class="icon-th-list"></i> See all</a>
	<?php
	} else {
		foreach ( $this->model_subtypes as $subtype ) {
		?>
			<a class="btn" href="<?= $page_url( 'create', $subtype ) ?>"><i class="icon-plus-sign"></i> Create <?= ucfirst( $subtype ) ?></a>
		<?php
		}
	}
	?>
</div>
