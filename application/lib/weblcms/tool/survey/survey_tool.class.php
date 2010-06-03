<?php
/**
 * $Id: survey_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment
 */
require_once dirname(__FILE__) . '/survey_tool_component.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/tool/tool.class.php';
/**
 * This tool allows a user to publish assessments in his or her course.
 */
class SurveyTool extends Tool
{
    const ACTION_DELETE_PUBLICATION = 'delete_pub';
    const ACTION_VIEW_USER_ASSESSMENTS = 'view_user';
    const ACTION_TAKE_SURVEY = 'take';
    const ACTION_VIEW_RESULTS = 'result';
    const ACTION_EXPORT_QTI = 'exportqti';
    const ACTION_IMPORT_QTI = 'importqti';
    const ACTION_SAVE_DOCUMENTS = 'save_documents';
    const ACTION_EXPORT_RESULTS = 'export_results';
    const ACTION_VIEW = 'view';
    const ACTION_DELETE_RESULTS = 'delete_results';
    
    const PARAM_USER_ASSESSMENT = 'uaid';
    const PARAM_QUESTION_ATTEMPT = 'qaid';
    const PARAM_ASSESSMENT = 'aid';
    const PARAM_ANONYMOUS = 'anonymous';
    const PARAM_INVITATION_ID = 'invitation_id';
    const PARAM_PUBLICATION_ACTION = 'publication_action';

    /*
	 * Inherited.
	 */
    function run()
    {
        $action = $this->get_action();
        $component = parent :: run();
        
        if ($component)
            return;
        
        switch ($action)
        {
            case self :: ACTION_PUBLISH :
                $component = SurveyToolComponent :: factory('Publisher', $this);
                break;
            case self :: ACTION_VIEW :
                $component = SurveyToolComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_TAKE_SURVEY :
                $component = SurveyToolComponent :: factory('Taker', $this);
                break;
//            case self :: ACTION_VIEW_RESULTS :
//                $component = AssessmentToolComponent :: factory('ResultsViewer', $this);
//                break;
//            case self :: ACTION_EXPORT_QTI :
//                $component = AssessmentToolComponent :: factory('QtiExport', $this);
//                //$component->set_redirect_params(array(Tool :: PARAM_ACTION => AssessmentTool :: ACTION_VIEW_ASSESSMENTS));
//                break;
//            case self :: ACTION_IMPORT_QTI :
//                $component = AssessmentToolComponent :: factory('QtiImport', $this);
//                break;
//            case self :: ACTION_SAVE_DOCUMENTS :
//                $component = AssessmentToolComponent :: factory('DocumentSaver', $this);
//                break;
//            case self :: ACTION_EXPORT_RESULTS :
//                $component = AssessmentToolComponent :: factory('ResultsExport', $this);
//                break;
//            case self :: ACTION_DELETE_RESULTS :
//                $component = AssessmentToolComponent :: Factory('ResultsDeleter', $this);
//                break;
//            case self :: ACTION_DELETE_PUBLICATION :
//                $component = AssessmentToolComponent :: Factory('Deleter', $this);
//                break;
            default :
                $component = SurveyToolComponent :: factory('Viewer', $this);
                break;
        }
        
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Survey :: get_type_name());
    }
}
?>