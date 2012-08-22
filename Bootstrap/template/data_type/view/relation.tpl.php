<?php
if ( \Supersoniq\object_is_a( $this->content, 'Alias\\Relation' ) ) {
	$href = \Supersoniq\module_model_url( $this->content->definition->related_model_type, 'all' )
		. '?from[type]=' . $this->_parent->content->type
		. '&from[id]=' . $this->_parent->content->id
		. '&from[attribute]=' . $this->content->name;
?>
	<a href="<?= $href ?>">
		See associated
	</a>
<?php
} else {
?>
	</p><ul>
<?php
	$relateds = $this->content->get( );
	foreach ( $relateds as $related ) {
	?>
		<li>
			<a href="<?= \Supersoniq\module_model_url( $related ) ?>">
				<?= $related->name( ) ?>
			</a>
		</li>
	<?php
	}
?>
	</ul><p>
<?php
}
?>
