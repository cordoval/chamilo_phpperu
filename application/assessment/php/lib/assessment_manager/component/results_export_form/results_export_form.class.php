<?php
/**
 * $Id: results_export_form.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component.results_export_form
 */

require_once dirname(__FILE__) . '/../../../trackers/assessment_assessment_attempts_tracker.class.php';

class AssessmentResultsExportForm extends FormValidator
{

    function AssessmentResultsExportForm($url)
    {
        parent :: __construct('assessment', 'post', $url);
        $this->initialize();
    }

    function initialize()
    {
        if (Request :: get('tid'))
        {
            $tid = Request :: get('tid');
            $track = new AssessmentAssessmentAttemptsTracker();
            $condition = new EqualityCondition(AssessmentAssessmentAttemptsTracker :: PROPERTY_ID, $tid);
            $uass = $track->retrieve_tracker_items($condition);
            $user_assessment = $uass[0];
            
            $publication = AssessmentDataManager :: get_instance()->retrieve_assessment_publication($user_assessment->get_assessment_id());
            $assessment = $publication->get_publication_object();
            $user = UserDataManager :: get_instance()->retrieve_user($user_assessment->get_user_id());
            
            //$this->addElement('html', '<h3>Assessment: '.$assessment->get_title().'</h3><br/>');
            $this->addElement('html', '<h3>Export results for user ' . $user->get_fullname() . '</h3><br />');
            
            $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/assessment.png);">';
            $html[] = '<div class="title">';
            $html[] = $assessment->get_title();
            $html[] = '</div>';
            $html[] = $assessment->get_description();
            $html[] = '</div><br />';
            
            $this->addElement('html', implode("\n", $html));
        }
        else 
            if (Request :: get(AssessmentTool :: PARAM_PUBLICATION_ID))
            {
                $aid = Request :: get(AssessmentTool :: PARAM_PUBLICATION_ID);
                $publication = AssessmentDataManager :: get_instance()->retrieve_assessment_publication($aid);
                
                $this->addElement('html', '<h3>Assessment: ' . $publication->get_content_object()->get_title() . '</h3><br/>');
                $this->addElement('html', '<h3>Export results for user ' . $user->get_fullname() . '</h3><br />');
            }
        
        $options = Export :: get_supported_filetypes(array('ical'));
        $this->addElement('select', 'filetype', 'Export to filetype:', $options);
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Export'), array('class' => 'positive export'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>