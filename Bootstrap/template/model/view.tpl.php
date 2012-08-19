<?php
foreach ( $this->content->get_attribute_fields( 'get' ) as $name ) {
	$attribute = $this->content->attribute( $name );
?>
	<p><b><?= $name ?></b> : <?= $this->display( $attribute ) ?></p>
<?php
}
?>
