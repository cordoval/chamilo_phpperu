<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';

class SurveyManagerDeleterComponent extends SurveyManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $ids = Request :: get(self :: PARAM_PUBLICATION_ID);
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                
                $survey_publication = $this->retrieve_survey_publication($id);
                
                if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_DELETE, $id, SurveyRights :: TYPE_PUBLICATION, $this->get_user_id()))
                {
                    $failures ++;
                }
                else
                {
                    if (! $survey_publication->delete())
                    {
                        $failures ++;
                    }
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedSurveyPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedSurveyPublicationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedSurveyPublicationsDeleted';
                }
                else
                {
                    $message = 'SelectedSurveyPublicationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(self :: PARAM_ACTION => self :: ACTION_BROWSE_SURVEY_PUBLICATIONS));
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoSurveyPublicationsSelected')));
        }
    }
}
?>