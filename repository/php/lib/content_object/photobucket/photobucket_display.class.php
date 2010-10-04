<?php
/**
 * $Id: photobucket_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.youtube
 */
class PhotobucketDisplay extends ContentObjectDisplay
{

    function get_video_element($width = 425, $height = 344)
    {
    	return '<iframe src="' . $this->get_content_object()->get_video_url() . '" style="border:0px #FFFFFF none;" name="Photobucket" scrolling="no" frameborder="1" marginheight="0px" marginwidth="0px" width="'. $width . '" height="'. $height .'"></iframe>';
    }

    function get_description()
    {
        $html = parent :: get_description();
        $object = $this->get_content_object();
                       
        return str_replace(self :: DESCRIPTION_MARKER, '<div class="link_url" style="margin-top: 1em;">' . $this->get_preview() . '<br/></div>' . self :: DESCRIPTION_MARKER, $html);
   }

    function get_short_html()
    {
        $object = $this->get_content_object();
        return '<span class="content_object"><a target="about:blank" href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }

    function get_thumbnail()
    {
        return '<img class="thumbnail" src="' . $this->get_content_object()->get_thumbnail() . '" />';    
    }
    
    function get_preview($is_thumbnail = false)
    {
        if ($is_thumbnail)
        {
            return $this->get_thumbnail();
        }
        else
        {
            return $this->get_video_element();
        }
    }
}
?>