<div class="action">
	<a class="btn" href="<?= $page_url( 'create' ) ?>"><i class="icon-plus-sign"></i> Create</a>
	<?php
	foreach ( $this->model_subtypes as $subtype ) {
	?>
		<a class="btn" href="<?= $page_url( 'create', $subtype ) ?>"><i class="icon-plus-sign"></i> Create <?= ucfirst( $subtype ) ?></a>
	<?php
	}
	?>
</div>
