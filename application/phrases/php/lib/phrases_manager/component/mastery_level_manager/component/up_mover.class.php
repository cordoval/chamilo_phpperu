<?php
/**
 * @package application.lib.assessment.assessment_manager.component
 * @author Hans De Bisschop
 * @author
 */

require_once WebApplication :: get_application_class_lib_path('phrases') . 'phrases_manager/component/mastery_level_manager/component/mover.class.php';

class PhrasesMasteryLevelManagerUpMoverComponent extends PhrasesMasteryLevelManagerMoverComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Request :: set_get(self :: PARAM_MOVE, - 1);
        parent :: run();
    }
}
?>