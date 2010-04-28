<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';


class SurveyManagerVisibilityChangerComponent extends SurveyManager
{
    
    const MESSAGE_VISIBILITY_CHANGED = 'VisibilityChanged';
    const MESSAGE_VISIBILITY_NOT_CHANGED = 'VisibilityNotChanged';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get(SurveyManager :: PARAM_SURVEY_PUBLICATION);
        
        if ($pid)
        {
            $publication = $this->retrieve_survey_publication($pid);
            
            if (! $publication->is_visible_for_target_user($this->get_user()))
            {
                $this->redirect(null, false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
            }
            
            $publication->toggle_visibility();
            $succes = $publication->update();
            
            $message = $succes ? self :: MESSAGE_VISIBILITY_CHANGED : self :: MESSAGE_VISIBILITY_NOT_CHANGED;
            
            $this->redirect(Translation :: get($message), ! $succes, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
        else
        {
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
    }
}
?>