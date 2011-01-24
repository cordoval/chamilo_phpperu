<?php
namespace application\handbook;

use common\libraries\DelegateComponent;
use common\libraries\Request;
use common\extensions\rights_editor_manager\RightsEditorManager;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
require_once dirname(__FILE__) . '/../../handbook_rights.class.php';

/**
 * Handbook manager component to set the rights for a handbook publication
 */
class HandbookManagerRightsEditorComponent extends HandbookManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $publication_id = Request :: get(HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID);
        $hdm = HandbookDataManager::get_instance();
        $publication = $hdm->retrieve_handbook_publication($publication_id);
                    
            if($publication != null)
            {

                if ($this->get_user()->is_platform_admin() || $publication->get_publisher_id() == $this->get_user_id())
        	{
                    //TODO: also when user has editrights on publication
        		$locations[] = HandbookRights::get_location_by_identifier_from_handbooks_subtree($publication_id);

        	}
            }
        
      
        $manager = new RightsEditorManager($this, $locations);
	$manager->exclude_users(array($this->get_user_id()));
    	$manager->run();
    }
    
	
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_HANDBOOK_PUBLICATION_ID);
    }
    
    function get_available_rights()
    {
    	$publications = Request :: get(HandbookManager :: PARAM_HANDBOOK_PUBLICATION_ID);
    	if(count($publications) > 0)
    	{
    		return HandbookRights :: get_available_rights_for_publications();
    	}
        else
        {
            return null;
        }
    	
    	
    }

}
?>