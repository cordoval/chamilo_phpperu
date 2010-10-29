<?php
namespace repository\content_object\learning_path;

use repository\RepositoryManager;
use common\libraries\Translation;
use repository\content_object\document\Document;

/**
 * @package repository.content_object.learning_path
 */

class LearningPathDocumentContentObjectDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($document)
    {
        $html[] = $this->add_tracking_javascript();
        $name = $document->get_filename();
        
        if ($this->is_showable($name))
        {
            $html[] = $this->display_link($this->get_parent()->get_parent()->get_learning_path_document_preview_url($document->get_id()));
        }
        else
        {
            $info = array();
            $info[] = sprintf(Translation :: get('LPDownloadDocument'), $document->get_filename(), $document->get_filesize());
            $info[] = '<br />';
            $info[] = '<a target="about:blank" href="' . RepositoryManager :: get_document_downloader_url($document->get_id()) . '">' . Translation :: get('Download') . '</a>';
            
            $html[] = '<h3>' . $document->get_title() . '</h3>';
            $html[] = $this->display_box(implode("\n", $info));
        }
        
        return implode("\n", $html);
    }

    function is_showable($file)
    {
        $parts = explode('.', $file);
        $extension = $parts[count($parts) - 1];
        
        return in_array($extension, Document :: get_showable_types()) || in_array($extension, Document :: get_image_types());
    }
}

?>