<?php

namespace application\weblcms;

use common\libraries\Request;
use common\libraries\Translation;

/**
 * $Id: move.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
//require_once dirname(__PATH__).'../tool_component.class.php';


class ToolComponentMoverComponent extends ToolComponent
{

    function run()
    {
        $publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $publication_id))
        {

            $move = $this->get_parent()->get_move_direction();

            $datamanager = WeblcmsDataManager :: get_instance();
            $publication = $datamanager->retrieve_content_object_publication($publication_id);
            if ($publication->move($move))
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationMoved'));
            }

            $this->redirect($message, false, array(Tool :: PARAM_ACTION => null, Tool :: PARAM_BROWSER_TYPE => Request :: get(Tool :: PARAM_BROWSER_TYPE)));

            //$this->redirect($message, false, array(Tool :: PARAM_ACTION => null, Tool :: PARAM_BROWSER_TYPE => Request :: get(Tool :: PARAM_BROWSER_TYPE)));
        }
        else
        {
            $this->redirect(Translation :: get("NotAllowed"), '', array(Tool :: PARAM_PUBLICATION_ID => null, 'tool_action' => null));
        }
    }

}

?>