<?php
namespace repository\content_object\dailymotion;

use repository\ContentObjectDifferenceDisplay;

/**
 * $Id: dailymotion_difference_display.class.php 2010-06-08
 * package repository.lib.content_object.dailymotion
 * @author Shoira Mukhsinova
 */
class DailymotionDifferenceDisplay extends ContentObjectDifferenceDisplay
{

    function get_diff_as_html()
    {
        $diff = $this->get_difference();

        $html = array();

        $html[] = '<div class="difference" style="background-image: url(' . Theme :: get_common_image_path() . $diff->get_object()->get_icon_name() . '.png);">';
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