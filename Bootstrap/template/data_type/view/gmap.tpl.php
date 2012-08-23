<?php
if ( $this->content->get( ) ) {
?>
<br />
<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?t=h&amp;ie=UTF8&amp;ll=<?= $this->content->get( ) ?>&amp;z=15&amp;output=embed"></iframe><br />
<small><a href="https://maps.google.com/maps?source=embed&amp;t=h&amp;ie=UTF8&amp;ll=<?= $this->content->get( ) ?>&amp;z=15" target="_blank">Agrandir le plan</a></small>
<?php
}
