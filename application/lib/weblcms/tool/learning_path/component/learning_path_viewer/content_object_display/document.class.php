<?php
/**
 * $Id: document.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer.content_object_display
 */
require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class DocumentDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($document)
    {
        $html[] = $this->add_tracking_javascript();
        
        $name = $document->get_filename();
        
        //$html[] = '<h3>' . $document->get_title() . '</h3>' . $document->get_description() . '<br />';
        

        if ($this->is_showable($name))
        {
            $html[] = $this->display_link($this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_DOCUMENT, 'id' => $document->get_id())));
        }
        else
        {
            $info = sprintf(Translation :: get('LPDownloadDocument'), $document->get_filename(), $document->get_filesize());
            $info .= '<br /><a target="about:blank" href="' . RepositoryManager :: get_document_downloader_url($document->get_id()) . '">' . Translation :: get('Download') . '</a>';
            
            $html[] = '<h3>' . $document->get_title() . '</h3>';
            $html[] = $this->display_box($info);
        }
        
        return implode("\n", $html);
    }

    function is_showable($file)
    {
        $extensions = array('.html', '.htm', '.txt', '.jpg', '.bmp', '.jpeg', '.png');
        
        foreach ($extensions as $extension)
        {
            $len = strlen($extension) * - 1;
            if (substr(strtolower($file), $len) == $extension)
                return true;
        }
        
        return false;
    }
}

?>