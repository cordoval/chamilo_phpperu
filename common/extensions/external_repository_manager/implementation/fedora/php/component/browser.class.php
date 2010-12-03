<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

use common\libraries\Request;
use common\extensions\external_repository_manager\ExternalRepositoryComponent;

/**
 * Browse Fedora's objects.
 *
 * If the current API provides a specialization for this component launch it instead.
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraExternalRepositoryManagerBrowserComponent extends FedoraExternalRepositoryManager {

    function run() {
        if ($api = $this->create_api_component()) {
            return $api->run();
        }

        ExternalRepositoryComponent :: launch($this);
    }

    function get_parameters() {
        $result = parent::get_parameters();
        $result[self::PARAM_FOLDER] = Request::get(self::PARAM_FOLDER);
        return $result;
    }

}

?>