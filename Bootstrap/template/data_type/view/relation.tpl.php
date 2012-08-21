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
