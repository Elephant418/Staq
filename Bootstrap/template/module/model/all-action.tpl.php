<div class="action">
	<div class="btn-toolbar">
		<div class="btn-group">
			<?php
			if ( $this->from || $this->filter ) {
			?>
				<a class="btn" href="<?= $page_url( 'all' ) ?>"><i class="icon-th-list"></i> See all <?= $this->model_type ?></a>
			<?php
			} else {
				if ( count( $this->model_subtypes ) == 1 ) {
					$subtype = $this->model_subtypes[ 0 ];
				?>
					<a class="btn" href="<?= $page_url( 'create', $subtype ) ?>"><i class="icon-plus-sign"></i> Create <?= ucfirst( $subtype ) ?></a>
				<?php
				} else {
					?>
					<a class="btn dropdown-toggle" data-toggle="dropdown" href="javascript:;"><i class="icon-plus-sign"></i> Create <span class="caret"></span></a>
					<ul class="dropdown-menu">
					<?php
					foreach ( $this->model_subtypes as $subtype ) {
					?>
						<li><a href="<?= $page_url( 'create', $subtype ) ?>"><?= $subtype ?></a></li>
					<?php
					}
					?>
					</ul>
					<?php
				}
			}
			?>
			<a class="btn" href="javascript:window.print( );"><i class="icon-print"></i> Print</a>
		</div>
	</div>
</div>
