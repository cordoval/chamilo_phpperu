<?php
/**
 * $Id: rss_feed_difference_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.rss_feed
 */
/**
 * This class can be used to display the difference between links
 */
class RssFeedDifferenceDisplay extends ContentObjectDifferenceDisplay
{

    function get_diff_as_html()
    {
        $diff = $this->get_difference();
        
        $html = array();
        
        $html[] = '<div class="difference" style="background-image: url(' . $this->get_path(WEB_IMG_PATH) . $diff->get_object()->get_icon_name() . '.png);">';
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
            $html[] = print_r($d->parse('final'), true) . '';
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