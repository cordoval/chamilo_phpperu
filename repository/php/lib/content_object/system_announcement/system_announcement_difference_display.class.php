<?php
/**
 * $Id: system_announcement_difference_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.system_announcement
 */
/**
 * This class can be used to display the difference between system announcements
 */
class SystemAnnouncementDifferenceDisplay extends ContentObjectDifferenceDisplay
{

    function get_diff_as_html()
    {
        $diff = $this->get_difference();
        $icon_object = $diff->get_object()->get_icon_name();
        $icon_version = $diff->get_version()->get_icon_name();
        $html = parent :: get_diff_as_html();
        $html = str_replace('style="background-image: url(' . Theme :: get_common_image_path() . $icon_object . '.png);"', '', $html);
        $html = str_replace('class="titleleft"', 'class="titleleft" style="padding-left: 30px;margin-right: -30px;height: 25px;background-image: url(' . Theme :: get_common_image_path() . $icon_object . '.png);"', $html);
        $html = str_replace('class="titleright"', 'class="titleright" style="padding-left: 30px;height: 25px;background-image: url(' . Theme :: get_common_image_path() . $icon_version . '.png);"', $html);
        return $html;
    }
}
?>