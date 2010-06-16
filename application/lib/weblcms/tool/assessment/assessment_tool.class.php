<?php
/**
 * $Id: assessment_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment
 */
require_once Path :: get_application_path() . 'lib/weblcms/tool/tool.class.php';
/**
 * This tool allows a user to publish assessments in his or her course.
 */
class AssessmentTool extends Tool
{
    const ACTION_TAKE_ASSESSMENT = 'take';
    const ACTION_VIEW_RESULTS = 'result';
    const ACTION_SAVE_DOCUMENTS = 'save_documents';
    const ACTION_EXPORT_RESULTS = 'export_results';
    const ACTION_DELETE_RESULTS = 'delete_results';
    const ACTION_EXPORT_QTI = 'export_qti';
    const ACTION_IMPORT_QTI = 'import_qti';
    
    
    const ACTION_DELETE_PUBLICATION = 'delete_pub';
    const ACTION_VIEW_ASSESSMENTS = 'view';
    const ACTION_VIEW_USER_ASSESSMENTS = 'view_user';
    const ACTION_PUBLISH_SURVEY = 'publish_survey';
    const ACTION_VIEW = 'view';
    
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
        //        $action = $this->get_action();
        //        $component = parent :: run();
        //
        //        if ($component)
        //            return;
        //
        //        switch ($action)
        //        {
        //            case self :: ACTION_PUBLISH :
        //                $component = AssessmentToolComponent :: factory('Publisher', $this);
        //                break;
        //            case self :: ACTION_VIEW_ASSESSMENTS :
        //                $component = AssessmentToolComponent :: factory('Viewer', $this);
        //                break;
        //            case self :: ACTION_TAKE_ASSESSMENT :
        //                $component = AssessmentToolComponent :: factory('Tester', $this);
        //                break;
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
        //            case self :: ACTION_PUBLISH_SURVEY :
        //                $component = AssessmentToolComponent :: factory('SurveyPublisher', $this);
        //                break;
        //            case self :: ACTION_DELETE_RESULTS :
        //                $component = AssessmentToolComponent :: Factory('ResultsDeleter', $this);
        //                break;
        //            case self :: ACTION_DELETE_PUBLICATION :
        //                $component = AssessmentToolComponent :: Factory('Deleter', $this);
        //                break;
        //            default :
        //                $component = AssessmentToolComponent :: factory('Viewer', $this);
        //                break;
        //        }
        //
        //        $component->run();
        

        $action = $this->get_action();
        
        switch ($action)
        {
            case self :: ACTION_VIEW :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_PUBLISH :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_UPDATE :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_DELETE :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_TOGGLE_VISIBILITY :
                $component = $this->create_component('ToggleVisibility');
                break;
            case self :: ACTION_MOVE_UP :
                $component = $this->create_component('MoveUp');
                break;
            case self :: ACTION_MOVE_DOWN :
                $component = $this->create_component('MoveDown');
                break;
            case self :: ACTION_PUBLISH_INTRODUCTION :
                $component = $this->create_component('IntroductionPublisher');
                break;
            case self :: ACTION_VIEW_REPORTING_TEMPLATE :
                $component = $this->create_component('ReportingViewer');
                break;
            case self :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT :
                $component = $this->create_component('Builder');
                break;
            case self :: ACTION_MOVE_TO_CATEGORY :
                $component = $this->create_component('CategoryMover');
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = $this->create_component('CategoryManager');
                break;
            case self :: ACTION_TAKE_ASSESSMENT :
                $component = $this->create_component('Taker');
                break;
            case self :: ACTION_VIEW_RESULTS :
                $component = $this->create_component('ResultsViewer');
                break;
            case self :: ACTION_SAVE_DOCUMENTS :
                $component = $this->create_component('DocumentSaver');
                break;
            case self :: ACTION_DELETE_RESULTS :
                $component = $this->create_component('ResultsDeleter');
                break;
            case self :: ACTION_EXPORT_RESULTS :
                $component = $this->create_component('ResultsExport');
                break;
            case self :: ACTION_EXPORT_QTI :
                $component = $this->create_component('QtiExporter');
                break;
            case self :: ACTION_IMPORT_QTI :
                $component = $this->create_component('QtiImporter');
                break;
            default :
                $component = $this->create_component('Browser');
        }
        
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Assessment :: get_type_name(), Hotpotatoes :: get_type_name());
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function is_category_management_enabled()
    {
        return true;
    }
}
?>