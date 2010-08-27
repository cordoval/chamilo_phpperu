<?php
class RepositoryManagerExternalRepositoryComponent extends RepositoryManager
{
    private $external_repository;

	function run()
	{
	    $trail = BreadcrumbTrail::get_instance();
	    $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ExternalRepository')));

		$external_repository_id = Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);
		$this->set_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY, $external_repository_id);
		$this->external_repository = $this->retrieve_external_repository($external_repository_id);

		if ($this->external_repository instanceof ExternalRepository && $this->external_repository->is_enabled())
		{
		    ExternalRepositoryManager :: launch($this);
		}
		else
		{
		    $this->display_header();
		    $this->display_error_message('NoSuchExternalRepositoryManager');
		    $this->display_footer();
		}
	}

	function get_external_repository()
	{
	    return $this->external_repository;
	}
}
?>