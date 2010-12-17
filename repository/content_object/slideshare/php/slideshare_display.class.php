<?php
namespace repository\content_object\slideshare;

use common\libraries\Text;

use repository\ContentObjectDisplay;

/**
 * $Id: slideshare_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.slideshare
 */
class SlideshareDisplay extends ContentObjectDisplay
{

    function get_description()
    {
        $html = parent :: get_description();
        $object = $this->get_content_object();                    
        return $html . $object->get_embed();
    }  	
}
?>