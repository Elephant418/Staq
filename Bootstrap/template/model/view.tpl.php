<?php
foreach ( $this->content->get_attribute_fields( ) as $name ) {
	$attribute = $this->content->attribute( $name );
	if ( ! \Supersoniq\object_is_a( $attribute, 'Password' ) ) {
		$class = str_replace( '\\', '-', strtolower( \Supersoniq\class_subtype( $attribute ) ) );
	?>
		<p class="attribute attribute-<?= $class ?>">
			<b><?= $name ?></b> : <?= $this->display( $attribute ) ?>
		</p>
	<?php
	}
}
?>
