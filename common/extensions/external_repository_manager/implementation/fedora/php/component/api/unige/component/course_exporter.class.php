<?php

namespace common\extensions\external_repository_manager\implementation\fedora;

require_once dirname(__FILE__) . '/../forms/fedora_unige_metadata_form.class.php';
require_once dirname(__FILE__) . '/../forms/fedora_unige_confirm_form.class.php';

/**
 *
 * @copyright (c) 2010 University of Geneva
 * @license GNU General Public License
 * @author laurent.opprecht@unige.ch
 *
 */
class FedoraUnigeCourseExporterComponent extends FedoraExternalRepositoryManagerCourseExporterComponent {

    /**
     * Returns the form to be displayed for a step.
     *
     * @param string $action action for the step
     * @param any $p1 form constructor parameter
     */
    protected function create_form($action=false, $p1=null) {
        $action = $action ? $action : $this->get_wizard_action();
        $p1 = $p1 ? $p1 : $this->get_data();
        $parameters = $this->get_wizard_parameters($action);
        switch ($action) {

            case self::ACTION_METADATA:
                $result = new FedoraUnigeMetadataForm($this, $parameters, $p1);
                return $result;

            case self::ACTION_CONFIRM:
                $result = new FedoraUnigeConfirmForm($this, $parameters, $p1);
                return $result;

            default:
                $result = parent::create_form($action, $p1);
                return $result;
        }
    }

}

?>