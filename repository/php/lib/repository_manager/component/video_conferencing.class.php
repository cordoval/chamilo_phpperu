<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\extensions\video_conferencing_manager\VideoConferencingManager;

class RepositoryManagerVideoConferencingExternalRepositoryComponent extends RepositoryManager
{
    private $video_conferencing;

	function run()
	{
	    $trail = BreadcrumbTrail::get_instance();
	    $trail->add(new Breadcrumb($this->get_url(), Translation :: get('VideoConferencing', null, VideoConferencingManager :: get_namespace())));

		$external_repository_id = Request :: get(VideoConferencingManager :: PARAM_EXTERNAL_REPOSITORY);
		$this->set_parameter(VideoConferencingManager :: PARAM_EXTERNAL_REPOSITORY, $video_conferencing_id);
		$this->video_conferencing = $this->retrieve_video_conferencing($video_conferencing_id);

		if ($this->video_conferencing instanceof VideoConferencing && $this->video_conferencing->is_enabled())
		{
		    VideoConferencingManager :: launch($this);
		}
		else
		{
		    $this->display_header();
		    $this->display_error_message('NoSuchVideoConferencingManager');
		    $this->display_footer();
		}
	}

	function get_video_conferencing()
	{
	    return $this->video_conferencing;
	}
}
?>