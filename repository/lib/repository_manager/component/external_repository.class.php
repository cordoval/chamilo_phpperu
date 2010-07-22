<?php
class RepositoryManagerExternalRepositoryComponent extends RepositoryManager
{
	function run()
	{
	    $trail = BreadcrumbTrail::get_instance();
	    $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ExternalRepository')));
	    
		$external_repository_id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);
		$this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, $external_repository_id);
		$external_repository = $this->retrieve_external_repository($external_repository_id);

		if ($external_repository instanceof ExternalRepository && $external_repository->is_enabled())
		{
		    $external_repository_manager = ExternalRepositoryManager :: factory($external_repository, $this);
		    $external_repository_manager->run();
		}
		else
		{
		    $this->display_header();
		    $this->display_error_message('NoSuchExternalRepositoryManager');
		    $this->display_footer();
		}
	}
}
?>