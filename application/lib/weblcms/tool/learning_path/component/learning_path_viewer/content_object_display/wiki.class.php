<?php
/**
 * $Id: wiki.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component.learning_path_viewer.content_object_display
 */
require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class WikiDisplay extends LearningPathContentObjectDisplay
{

    function display_content_object($wiki)
    {
        $html[] = $this->add_tracking_javascript();
        $hp_cloi = WikiTool :: get_wiki_homepage($wiki->get_id());
        if (! empty($hp_cloi))
            $link = $this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_CLO, 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => $wiki->get_id(), 'selected_cloi' => $hp_cloi->get_id()));
        else
            $link = $this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_CLO, Tool :: PARAM_PUBLICATION_ID => $wiki->get_id()));
        $html[] = $this->display_link($link);
        
        return implode("\n", $html);
    }
}

?>