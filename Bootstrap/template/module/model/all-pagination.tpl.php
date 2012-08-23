<?php
	$pagination = $this->pagination;
	if ( $pagination->page_last > 0 ) {
?>
<div class="row">
	<div class="span6">
	<?php
		if ( $pagination_text == 'position' ) {
	?>
		<div class="dataTables_info" id="DataTables_Table_0_info">
			Showing <?= ( $pagination->start ) + 1 ?> 
			to <?= $pagination->end ?>
			of <?= $pagination->count ?> entries
		</div>
	<?php
		} else if ( $pagination_text == 'search' ) {
	?>
		<div class="dataTables_filter">
			<form method="get">
			<?php
				foreach ( $_GET as $parameter => $values ) {
					if ( is_array( $values ) ) {
						foreach ( $values as $key => $value ) {
						?>
							<input type="hidden" name="<?=$parameter?>[<?=$key?>]" value="<?= $value ?>">
						<?php
						}
					} else {
				?>
					<input type="hidden" name="<?=$parameter?>" value="<?= $values ?>">
				<?php
					}
				}
			?>
				<input type="hidden" name="offset" value="0">
				<label>Search: <input type="text" name="filter[]"></label>
			</form>
		</div>
	<?php
		}
	?>
	</div>
	<div class="span6">
		<div class="dataTables_paginate paging_bootstrap pagination">
			<ul>
				<li class="prev <?= ( $pagination->offset == 0 )?'disabled':'' ?>">
					<a href="<?= $this->base_get_parameter ?>&offset=<?= $pagination->offset - 1 ?>">← Previous</a>
				</li>
			<?php
			if ( $pagination->page_start > 0 ) {
			?>
				<li>
					<a href="<?= $this->base_get_parameter ?>&offset=0">1</a>
				</li>
			<?php
			}
			if ( $pagination->page_start > 1 ) {
			?>
				<li>
					<span>...</span>
				</li>
			<?php
			}
			?>
			<?php
			for ( $page=$pagination->page_start; $page <= $pagination->page_end; $page++ ) {
			?>
				<li class="<?= ( $pagination->offset == $page )?'active':'' ?>">
					<a href="<?= $this->base_get_parameter ?>&offset=<?= $page ?>"><?= $page + 1 ?></a>
				</li>
			<?php
			}
			?>
			<?php
			if ( $pagination->page_end < $pagination->page_last - 1 ) {
			?>
				<li>
					<span>...</span>
				</li>
			<?php
			}
			if ( $pagination->page_end < $pagination->page_last ) {
			?>
				<li>
					<a href="<?= $this->base_get_parameter ?>&offset=<?= $pagination->page_last ?>"><?= $pagination->page_last + 1 ?></a>
				</li>
			<?php
			}
			?>
				<li class="next <?= ( $pagination->offset == $pagination->page_last )?'disabled':'' ?>">
					<a href="<?= $this->base_get_parameter ?>&offset=<?= $pagination->offset + 1 ?>">Next → </a>
				</li>
			</ul>
		</div>
	</div>
</div>
<?php
	}
