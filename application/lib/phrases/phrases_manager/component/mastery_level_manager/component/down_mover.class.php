<?php
/**
 * @package application.lib.assessment.assessment_manager.component
 * @author Hans De Bisschop
 * @author
 */

require_once dirname(__FILE__) . '/mover.class.php';

class PhrasesMasteryLevelManagerDownMoverComponent extends PhrasesMasteryLevelManagerMoverComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Request :: set_get(self :: PARAM_MOVE, 1);
        parent :: run();
    }
}
?>