<?php
$related = $this->content->get( );
if ( is_object( $related ) ) {
?>
	<?= $related->name( ) ?> (<?= $related->id ?>)
<?php
}
?>
