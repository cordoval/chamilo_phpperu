<?php
namespace application\survey;

use common\libraries\InCondition;

use tracking\Tracker;

use common\libraries\Translation;
use common\libraries\Request;

class SurveyManagerParticipantDeleterComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $ids = Request :: get(self :: PARAM_PARTICIPANT_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $tracker_condition = new InCondition(SurveyParticipantTracker :: PROPERTY_ID, $ids);
            $succes = Tracker :: remove_data(SurveyParticipantTracker :: CLASS_NAME, self :: APPLICATION_NAME, $tracker_condition);
            
            if ($succes)
            {
                $tracker_answer_condition = new InCondition(SurveyQuestionAnswerTracker :: PROPERTY_SURVEY_PARTICIPANT_ID, $ids);
                $succes = Tracker :: remove_data(SurveyQuestionAnswerTracker :: CLASS_NAME, self :: APPLICATION_NAME, $tracker_answer_condition);
            
            }
            
            if (! $succes)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedParticipantDeleted';
                }
                else
                {
                    $message = 'SelectedParticipantDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedParticipantsDeleted';
                }
                else
                {
                    $message = 'SelectedParticipantsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(
                    self :: PARAM_ACTION => self :: ACTION_BROWSE));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoParticipantsSelected')));
        }
    }
}
?>