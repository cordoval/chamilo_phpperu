<?php
/**
 * $Id: move.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
//require_once dirname(__PATH__).'../tool_component.class.php';


class ToolMoveComponent extends ToolComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
                   	
        	$move = $this->get_parent()->get_move_direction();
            
            $datamanager = WeblcmsDataManager :: get_instance();
            $publication = $datamanager->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
            if ($publication->move($move))
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationMoved'));
            }
            $this->redirect($message, false, array(Tool :: PARAM_ACTION => null));
        }
    }
}

?>