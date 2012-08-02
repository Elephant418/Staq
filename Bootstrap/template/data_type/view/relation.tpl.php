</p><ul>
<?php
$relateds = $this->content->get( );
foreach ( $relateds as $related ) {
?>
	<li><?= $related->name( ) ?></li>
<?php
}
?>
</ul><p>
