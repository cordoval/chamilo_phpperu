<?php
namespace common\extensions\external_repository_manager\implementation\fedora;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;

/**
 * If the current API provides a specialization for this component launch it instead.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerViewerComponent extends FedoraExternalRepositoryManager
{

	function run(){
		if($api = $this->create_api_component()){
			return $api->run();
		}

		ExternalRepositoryComponent::launch($this);
	}

}

?>