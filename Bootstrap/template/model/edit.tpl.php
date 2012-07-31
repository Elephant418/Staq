<?php
foreach ( $this->content->get_attribute_fields( ) as $name ) {
	$attribute = $this->content->attribute( $name );
?>
	<label><b><?= $name ?></b> : <?= $this->display( $attribute, 'edit' ) ?></label>
<?php
}
?>