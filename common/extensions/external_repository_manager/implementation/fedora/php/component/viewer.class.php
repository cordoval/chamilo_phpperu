<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\extensions\external_repository_manager\ExternalRepositoryComponent;
use common\libraries\Request;

/**
 * If the current API provides a specialization for this component launch it instead.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerViewerComponent extends FedoraExternalRepositoryManager {

    /**
     * @param Application $application
     */
    function __construct($application) {
        parent :: __construct($application);
        $pid = Request :: get(self :: PARAM_EXTERNAL_REPOSITORY_ID);
        if ($pid) {
            $this->set_parameter(self :: PARAM_EXTERNAL_REPOSITORY_ID, $pid);
        }
    }

    function run() {
        if ($api = $this->create_api_component()) {
            return $api->run();
        }

        ExternalRepositoryComponent::launch($this);
    }

}

?>