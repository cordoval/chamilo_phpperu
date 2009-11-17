<?php
/**
 * $Id: hotpotatoes_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.hotpotatoes
 */
/**
 * This class can be used to display open questions
 */
class HotpotatoesDisplay extends ContentObjectDisplay
{

    function get_full_html()
    {
        $object = $this->get_content_object();
        $path = $object->get_full_url();
        $html = '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';
        
        return $html;
    }

    //Inherited
    function get_short_html()
    {
        $object = $this->get_content_object();
        return '<span class="content_object"><a href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }
}
?>