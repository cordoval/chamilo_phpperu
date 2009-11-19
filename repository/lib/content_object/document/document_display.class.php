<?php
/**
 * $Id: document_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.document
 */
/**
 * This class can be used to display documents
 */
class DocumentDisplay extends ContentObjectDisplay
{

    //Inherited
    function get_full_html()
    {
        $html = parent :: get_full_html();
        $object = $this->get_content_object();
        $name = $object->get_filename();
        
        $url = RepositoryManager :: get_document_downloader_url($object->get_id());
        
        $img_extensions = array('jpg', 'jpeg', 'bmp', 'png', 'gif');
        $extension = strtolower(substr($name, strrpos($name, '.') + 1));
        if(in_array($extension, $img_extensions))
        {
            $html = preg_replace('|</div>\s*$|s', '<br /><a href="' . htmlentities($url) . '"><img style="max-width: 100%" src="' . $url . '" /></a></div>', $html);
        }
        else
        {
            if (strtolower(substr($name, - 4)) == 'html' || strtolower(substr($name, - 3)) == 'htm' || strtolower(substr($name, - 3)) == 'txt')
            {
                $html = preg_replace('|</div>\s*$|s', '<br /><iframe border="0" style="border: 1px solid grey;" width="100%" height="500"  src="' . $url . '&display=1"></iframe>', $html);
            }
            else
            {
                $html = preg_replace('|</div>\s*$|s', '<br /><div class="document_link" style="margin-top: 1em;"><a href="' . htmlentities($url) . '">' . htmlentities($name) . '</a> (' . Filesystem :: format_file_size($object->get_filesize()) . ')</div></div>', $html);
            }
        }
        
        return $html;
    }

    //Inherited
    function get_short_html()
    {
        $object = $this->get_content_object();
        $url = RepositoryManager :: get_document_downloader_url($object->get_id());
        
        return '<span class="content_object"><a href="' . htmlentities($url) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }
}
?>