<?php
foreach( \Notification::pull(  ) as $notification ) {
?>
	<div class="alert alert-<?= $notification->level ?>">
		<?= $notification ?>
	</div>
<?php
}
?>
<?= $this->display( $this->content ) ?>