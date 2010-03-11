<?php
/**
 * $Id: visibility_changer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component
 */
require_once dirname(__FILE__) . '/../survey_manager.class.php';
require_once dirname(__FILE__) . '/../survey_manager_component.class.php';

/**
 * Component to create a new survey_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class SurveyManagerVisibilityChangerComponent extends SurveyManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get('survey_publication');
        
        if ($pid)
        {
            $publication = $this->retrieve_survey_publication($pid);
            
            if (! $publication->is_visible_for_target_user($this->get_user()))
            {
                $this->redirect(null, false, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
            }
            
            $publication->toggle_visibility();
            $succes = $publication->update();
            
            $message = $succes ? 'VisibilityChanged' : 'VisibilityNotChanged';
            
            $this->redirect(Translation :: get($message), ! $succes, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
        else
        {
            $this->redirect(Translation :: get('NoPublicationSelected'), true, array(SurveyManager :: PARAM_ACTION => SurveyManager :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        }
    }
}
?>