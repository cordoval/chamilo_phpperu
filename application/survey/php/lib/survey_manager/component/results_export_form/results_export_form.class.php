<?php
/**
 * $Id: results_export_form.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.results_export_form
 */

require_once dirname(__FILE__) . '/../../../trackers/survey_survey_attempts_tracker.class.php';

class SurveyResultsExportForm extends FormValidator
{

    function SurveyResultsExportForm($url)
    {
        parent :: __construct('survey', 'post', $url);
        $this->initialize();
    }

    function initialize()
    {
        if (Request :: get('tid'))
        {
            $tid = Request :: get('tid');
            $track = new SurveySurveyAttemptsTracker();
            $condition = new EqualityCondition(SurveySurveyAttemptsTracker :: PROPERTY_ID, $tid);
            $uass = $track->retrieve_tracker_items($condition);
            $user_survey = $uass[0];
            
            $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($user_survey->get_survey_id());
            $survey = $publication->get_publication_object();
            $user = UserDataManager :: get_instance()->retrieve_user($user_survey->get_user_id());
            
            //$this->addElement('html', '<h3>Survey: '.$survey->get_title().'</h3><br/>');
            $this->addElement('html', '<h3>Export results for user ' . $user->get_fullname() . '</h3><br />');
            
            $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/survey.png);">';
            $html[] = '<div class="title">';
            $html[] = $survey->get_title();
            $html[] = '</div>';
            $html[] = $survey->get_description();
            $html[] = '</div><br />';
            
            $this->addElement('html', implode("\n", $html));
        }
        else 
            if (Request :: get(SurveyTool :: PARAM_PUBLICATION_ID))
            {
                $aid = Request :: get(SurveyTool :: PARAM_PUBLICATION_ID);
                $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($aid);
                
                $this->addElement('html', '<h3>Survey: ' . $publication->get_content_object()->get_title() . '</h3><br/>');
                $this->addElement('html', '<h3>Export results for user ' . $user->get_fullname() . '</h3><br />');
            }
        
        $options = Export :: get_supported_filetypes(array('ical'));
        $this->addElement('select', 'filetype', 'Export to filetype:', $options);
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Export'), array('class' => 'positive export'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>