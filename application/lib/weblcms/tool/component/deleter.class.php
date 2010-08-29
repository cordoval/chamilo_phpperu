<?php
/**
 * $Id: delete.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
class ToolComponentDeleterComponent extends ToolComponent
{
    function run()
    {
        if ($this->is_allowed(WeblcmsRights :: DELETE_RIGHT))
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
	            if(WebApplication :: is_active('gradebook'))
       			{
       				require_once dirname(__FILE__) . '/../../../gradebook/gradebook_utilities.class.php';
			    	if(!GradebookUtilities :: move_internal_item_to_external_item(WeblcmsManager :: APPLICATION_NAME, $publication->get_id()))
			    		$message = 'failed to move internal evaluation to external evaluation';
       			}
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
            
            $this->redirect($message, '', array(Tool :: PARAM_PUBLICATION_ID => null, 'tool_action' => null));
        }
    }

}
?>