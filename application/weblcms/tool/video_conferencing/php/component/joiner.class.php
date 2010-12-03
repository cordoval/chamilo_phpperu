<?php
namespace application\weblcms\tool\video_conferencing;

use common\extensions\video_conferencing_manager\VideoConferencingRights;
use common\extensions\video_conferencing_manager\VideoConferencingManager;

use application\weblcms\Tool;
use application\weblcms\ToolComponent;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\WeblcmsRights;

use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Translation;
use common\libraries\Request;

class VideoConferencingToolJoinerComponent extends VideoConferencingTool
{
    private $external_instance;
    private $publication;

    function run()
    {
        $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID) ? Request :: get(Tool :: PARAM_PUBLICATION_ID) : $_POST[Tool :: PARAM_PUBLICATION_ID];
        
        //check if the content object has indeed been published for the user
        
        $datamanager = WeblcmsDataManager :: get_instance();
        $this->publication = $datamanager->retrieve_content_object_publication($pid);
        $content_object = $this->publication->get_content_object();
        
        $external_sync = $content_object->get_synchronization_data();
        $this->external_instance = $external_sync->get_external();
        
        VideoConferencingManager :: launch($this);
    	
    }

    function get_video_conferencing_rights()
    {
    	$rights = VideoConferencingRights::factory($this->get_external_instance()->get_type());
    	if($this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $this->publication->get_id()))
    	{
    		$rights->set_moderator(true);
    	} 
    	else
    	{
    		$rights->set_moderator(false);
    	}
    	return $rights;
    }
    
    function get_external_instance()
    {
        return $this->external_instance;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('VideoConferencingToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(Tool :: PARAM_PUBLICATION_ID);
    }

}

?>