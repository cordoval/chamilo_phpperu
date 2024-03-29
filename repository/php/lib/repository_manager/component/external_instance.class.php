<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\extensions\video_conferencing_manager\VideoConferencingManager;
use common\extensions\video_conferencing_manager\VideoConferencingRights;

class RepositoryManagerExternalInstanceComponent extends RepositoryManager
{
    private $external_instance;

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ExternalInstance', null, ExternalInstanceManager :: get_namespace())));
        
        $external_instance_id = Request :: get(self :: PARAM_EXTERNAL_INSTANCE);
        $this->set_parameter(self :: PARAM_EXTERNAL_INSTANCE, $external_instance_id);
        $this->external_instance = $this->retrieve_external_instance($external_instance_id);
        
        if ($this->external_instance instanceof ExternalInstance && $this->external_instance->is_enabled())
        {
            $manager_class = ExternalInstanceManager :: get_manager_class($this->external_instance->get_instance_type());
            $manager_class :: launch($this);
        }
        else
        {
            $this->display_header();
            $this->display_error_message('NoSuchExternalInstanceManager');
            $this->display_footer();
        }
    }

    function get_external_instance()
    {
        return $this->external_instance;
    }

    function get_video_conferencing_rights()
    {
        $rights = VideoConferencingRights :: factory($this->get_external_instance()->get_type());
        
        $rights->set_moderator(true);
        
        return $rights;
    }

}
?>