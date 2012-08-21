<?php
$related = $this->content->get( );
if ( is_object( $related ) ) {
?>
	<a href="<?= \Supersoniq\module_model_url( $related ) ?>">
		<?= $related->name( ) ?>
	</a>
<?php
}
?>
