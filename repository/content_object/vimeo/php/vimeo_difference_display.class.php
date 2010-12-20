<?php
namespace repository\content_object\vimeo;

use repository\ContentObject;

use repository\ContentObjectDifferenceDisplay;
use common\libraries\Theme;

/**
 * $Id: vimeo_difference_display.class.php 2010-06-08
 * package repository.lib.content_object.vimeo
 * @author Shoira Mukhsinova
 */
class VimeoDifferenceDisplay extends ContentObjectDifferenceDisplay
{

    function get_diff_as_html()
    {
        $diff = $this->get_difference();

        $html = array();

        $html[] = '<div class="difference" style="background-image: url(' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($diff->get_object()->get_type())) . 'logo/' . $diff->get_object()->get_icon_name() . '.png);">';
        $html[] = '<div class="titleleft">';
        $html[] = $diff->get_object()->get_title();
        $html[] = date(" (d M Y, H:i:s O)", $diff->get_object()->get_creation_date());
        $html[] = '</div>';
        $html[] = '<div class="titleright">';
        $html[] = $diff->get_version()->get_title();
        $html[] = date(" (d M Y, H:i:s O)", $diff->get_version()->get_creation_date());
        $html[] = '</div>';

        $html[] = '<div class="left">';
        foreach ($diff->get_difference() as $d)
        {
            $html[] = print_r($d->parse('final'), true);
            $html[] = '<br style="clear:both;" />';
        }
        $html[] = '</div>';

        $html[] = '<div class="right">';
        foreach ($diff->get_difference() as $d)
        {
            $html[] = print_r($d->parse('orig'), true) . '';
            $html[] = '<br style="clear:both;" />';
        }
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
?>