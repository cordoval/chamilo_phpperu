<?php
namespace application\weblcms\tool\video_conferencing;

use common\extensions\video_conferencing_manager\VideoConferencingManager;

use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Translation;
use application\weblcms\ToolComponent;
use application\weblcms\Tool;
use application\weblcms\WeblcmsRights;
use application\weblcms\WeblcmsDataManager;

class VideoConferencingToolEnderComponent extends VideoConferencingTool
{
	private $external_instance;
	
    function run()
    {
        $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID) ? Request :: get(Tool :: PARAM_PUBLICATION_ID) : $_POST[Tool :: PARAM_PUBLICATION_ID];
        
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT, $pid))
        {
            $datamanager = WeblcmsDataManager :: get_instance();
            $publication = $datamanager->retrieve_content_object_publication($pid);
            $content_object = $publication->get_content_object();
            
            $external_sync = $content_object->get_synchronization_data();
            $this->external_instance = $external_sync->get_external();
            
            VideoConferencingManager :: launch($this);
            
        }
        else
        {
            $this->redirect(Translation :: get("NotAllowed"), '', array(Tool :: PARAM_PUBLICATION_ID => null, 'tool_action' => null));
        }
    
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