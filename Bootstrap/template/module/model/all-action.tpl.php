<div class="action">
	<div class="btn-toolbar">
		<div class="btn-group">
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
			?>
			<a class="btn" href="javascript:window.print( );"><i class="icon-print"></i> Print</a>
		</div>
	</div>
</div>
