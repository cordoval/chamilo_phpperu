<?php
class RepositoryManagerExternalRepositoryComponent extends RepositoryManager
{
	function run()
	{
		$type = Request :: get(ExternalRepositoryManager :: PARAM_TYPE);
		$this->set_parameter(ExternalRepositoryManager :: PARAM_TYPE, $type);
		$external_repository_manager = ExternalRepositoryManager :: factory($type, $this);
		$external_repository_manager->run();
	}
}
?>