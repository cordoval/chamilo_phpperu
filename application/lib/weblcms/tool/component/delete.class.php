<?php
/**
 * $Id: delete.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
class ToolDeleteComponent extends ToolComponent
{

    function run()
    {
        if ($this->is_allowed(DELETE_RIGHT) /*&& !WikiTool :: is_wiki_locked(Request :: get(Tool :: PARAM_PUBLICATION_ID))*/)
		{
            if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
                $publication_ids = Request :: get(Tool :: PARAM_PUBLICATION_ID);
            else
                $publication_ids = $_POST[Tool :: PARAM_PUBLICATION_ID];
            
            if (! is_array($publication_ids))
            {
                $publication_ids = array($publication_ids);
            }
            
            $datamanager = WeblcmsDataManager :: get_instance();
            
            foreach ($publication_ids as $index => $pid)
            {
                $publication = $datamanager->retrieve_content_object_publication($pid);
                $publication->delete();
            }
            if (count($publication_ids) > 1)
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationsDeleted'));
            }
            else
            {
                $message = htmlentities(Translation :: get('ContentObjectPublicationDeleted'));
            }
            
            $this->redirect($message, '', array('pid' => null, 'tool_action' => null));
        }
    }

}
?>