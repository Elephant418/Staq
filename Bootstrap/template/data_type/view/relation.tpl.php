</p><ul>
<?php
$relateds = $this->content->get( );
foreach ( $relateds as $related ) {
?>
	<li><?= $related->name( ) ?> (<?= $related->id ?>)</li>
<?php
}
?>
</ul><p>
