<?php
foreach ( $this->content->get_attribute_fields( 'set' ) as $name ) {
	$attribute = $this->content->attribute( $name );
?>
	<label><b><?= $name ?></b> : <?= $this->display( $attribute, 'edit' ) ?></label>
<?php
}
?>
