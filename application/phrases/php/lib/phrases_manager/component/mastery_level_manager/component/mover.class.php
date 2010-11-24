<?php
namespace application\phrases;

use common\libraries\Translation;
use common\libraries\Request;
/**
 * $Id: deleter.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */

/**
 * Component to delete assessment_mastery_levels objects
 * @author Hans De Bisschop
 * @author
 */
class PhrasesMasteryLevelManagerMoverComponent extends PhrasesMasteryLevelManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $mastery_level_id = Request :: get(PhrasesMasteryLevelManager :: PARAM_PHRASES_MASTERY_LEVEL_ID);

        if ($mastery_level_id)
        {
            $move = Request :: get(PhrasesMasteryLevelManager :: PARAM_MOVE);
            if (!isset($move))
            {
                $move = 0;
            }

            $mastery_level = $this->retrieve_phrases_mastery_level($mastery_level_id);

            $success = $mastery_level->move($move);
            if ($mastery_level->move($move))
            {
                $this->redirect(Translation :: get(($success ? 'PhrasesMasteryLevelMoved' : 'PhrasesMasteryLevelNotMoved')), ($success ? false : true), array(PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_MASTERY_LEVELS, PhrasesMasteryLevelManager :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => PhrasesMasteryLevelManager :: ACTION_BROWSE));
            }
        }
        else

        {
            $this->display_error_page(htmlentities(Translation :: get('NoPhrasesMasteryLevelSelected')));
        }

        $ids = Request :: get(PhrasesMasteryLevelManager :: PARAM_PHRASES_MASTERY_LEVEL_ID);
        $failures = 0;

    //        if (! empty($ids))
    //        {
    //            if (! is_array($ids))
    //            {
    //                $ids = array($ids);
    //            }
    //
    //            foreach ($ids as $id)
    //            {
    //                $phrases_mastery_level = $this->retrieve_phrases_mastery_level($id);
    //                if (! $phrases_mastery_level->delete())
    //                {
    //                    $failures ++;
    //                }
    //            }
    //
    //            if ($failures)
    //            {
    //                if (count($ids) == 1)
    //                {
    //                    $message = 'SelectedPhrasesMasteryLevelDeleted';
    //                }
    //                else
    //                {
    //                    $message = 'SelectedPhrasesMasteryLevelDeleted';
    //                }
    //            }
    //            else
    //            {
    //                if (count($ids) == 1)
    //                {
    //                    $message = 'SelectedPhrasesMasteryLevelsDeleted';
    //                }
    //                else
    //                {
    //                    $message = 'SelectedPhrasesMasteryLevelsDeleted';
    //                }
    //            }
    //
    //            $this->redirect(Translation :: get($message), ($failures ? true : false), array(PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_MASTERY_LEVELS, PhrasesMasteryLevelManager :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => PhrasesMasteryLevelManager :: ACTION_BROWSE));
    //        }
    //        else
    //        {
    //            $this->display_error_page(htmlentities(Translation :: get('NoPhrasesMasteryLevelsSelected')));
    //        }
    }
}
?>