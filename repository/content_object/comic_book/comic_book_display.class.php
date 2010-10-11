<?php
/**
 * This class can be used to display comic books
 *
 * @package repository.lib.content_object.comic_book
 * @author Hans De Bisschop
 */

class ComicBookDisplay extends ContentObjectDisplay
{
    function get_preview($is_thumbnail = false)
    {
        $object = $this->get_content_object();
        if ($object->has_covers())
        {
            $cover = $object->get_first_cover();
            $cover_display = ContentObjectDisplay::factory($cover);
            return $cover_display->get_preview($is_thumbnail);
        }
        else
        {
            return parent :: get_preview($is_thumbnail);
        }
    }
    
    /**
     * Returns a full HTML view of the learning object.
     * @return string The HTML.
     */
    function get_full_html($buttons = null)
    {
        $object = $this->get_content_object();
        $html = array();
        
        //Display extract as header
        $extract = $object->get_extract();
        if ($extract)
        {
    		$html[] = '<div class="story_image" style="background-image: url('. $extract->get_url() .');">';
    		$html[] = '<div class="story_text">';
    		$html[] = '<span class="title">' . $object->get_issue() . ' - ' . $object->get_title() . '</span>';
    		$html[] = '</div>';
    		$html[] = '</div>';
        }
        else
        {
            $html[] = '<h3>'. $object->get_issue() . ' - ' . $object->get_title() .'</h3>';
        }
        
        $html[] = '<div class="content_object comic_book" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $object->get_icon_name() . ($object->is_latest_version() ? '' : '_na') . '.png);">';
        $html[] = '<div class="title">' . Translation :: get('Synopsis') . '</div>';
        $html[] = '<div class="description" style="overflow: auto;">';
        $html[] = $object->get_synopsis();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="title">' . Translation :: get('FactsFiction') . '</div>';
        $html[] = '<div class="description" style="overflow: auto;">';
        
        //if ($object->has_covers())
        //{
        //    $html[] = $this->get_preview(true);
        //}
        
        $html[] = $object->get_facts();
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';

        return implode("\n", $html);
    }
}
?>