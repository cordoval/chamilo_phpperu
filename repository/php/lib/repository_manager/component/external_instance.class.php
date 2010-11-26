<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\extensions\video_conferencing_manager\VideoConferencingManager;

class RepositoryManagerExternalInstanceComponent extends RepositoryManager
{
    private $external_instance;

	function run()
	{
	    $trail = BreadcrumbTrail::get_instance();
	    $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ExternalInstance', null, ExternalInstanceManager :: get_namespace())));

		$external_instance_id = Request :: get(VideoConferencingManager :: PARAM_VIDEO_CONFERENCING);
		$this->set_parameter(VideoConferencingManager :: PARAM_VIDEO_CONFERENCING, $external_instance_id);
		$this->external_instance = $this->retrieve_external_instance($external_instance_id);

		if ($this->external_instance instanceof ExternalInstance && $this->external_instance->is_enabled())
		{
		    VideoConferencingManager :: launch($this);
		}
		else
		{
		    $this->display_header();
		    $this->display_error_message('NoSuchExternalInstanceManager');
		    $this->display_footer();
		}
	}

	function get_video_conferencing()
	{
	    return $this->external_instance;
	}
}
?>