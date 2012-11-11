<?php
$name = $this->content->name;
if ( isset( $_POST[ 'model' ][ $name ] ) ) {
	$value = $_POST[ 'model' ][ $name ];
} else {
	$value = $this->content->get( );
}
?>
<input type="text" name="model[<?= $name ?>]" value="<?= $value ?>" />
