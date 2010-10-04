<?php
/**
 * $Id: results_export_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_results_export_form.results_exporters
 */
require_once dirname(__FILE__) . '/../../../../trackers/weblcms_assessment_attempts_tracker.class.php';

class AssessmentResultsExportForm extends FormValidator
{

    function AssessmentResultsExportForm($url)
    {
        parent :: __construct('assessment', 'post', $url);
        $this->initialize();
    }

    function initialize()
    {
        $wdm = WeblcmsDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();
        
        if (Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT))
        {
            $uaid = Request :: get(AssessmentTool :: PARAM_USER_ASSESSMENT);
            $track = new WeblcmsAssessmentAttemptsTracker();
            $condition = new EqualityCondition(WeblcmsAssessmentAttemptsTracker :: PROPERTY_ID, $uaid);
            $uass = $track->retrieve_tracker_items($condition);
            $user_assessment = $uass[0];
            $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($user_assessment->get_assessment_id());
            $assessment = $publication->get_content_object();
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
                $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($aid);
                
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