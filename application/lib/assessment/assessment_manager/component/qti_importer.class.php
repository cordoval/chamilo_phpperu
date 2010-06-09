<?php
/**
 * $Id: qti_importer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../assessment_manager.class.php';

/**
 * Component to create a new assessment_publication object
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerQtiImporterComponent extends AssessmentManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $form = $this->build_importing_form();
        if ($form->validate())
        {
            $aid = $this->import_qti($form);
            $this->redirect(Translation :: get('QtiImported'), false, array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_CREATE_ASSESSMENT_PUBLICATION, 'object' => $aid));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
            $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_IMPORT_QTI)), Translation :: get('ImportQTI')));
            
            $this->display_header($trail, true);
            
            echo $form->toHtml();
            $this->display_footer();
        }
    
    }

    function build_importing_form()
    {
        $url = $this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_IMPORT_QTI));
        $form = new FormValidator('qti_import', 'post', $url);
        $form->addElement('html', '<b>Import assessment from QTI</b><br/><br/>');
        $form->addElement('html', '<em>' . Translation :: get('FileMustContainAllAssessmentXML') . '</em>');
        $form->addElement('file', 'file', Translation :: get('FileName'));
        
        $allowed_upload_types = array('zip');
        $form->addRule('file', Translation :: get('OnlyZipAllowed'), 'filetype', $allowed_upload_types);
        
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive import'));
        
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    function import_qti($form)
    {
        $values = $form->exportValues();
        $file = $_FILES['file'];
        $user = $this->get_user();
        //TODO: change categories
        $category = 0;
        
        $importer = ContentObjectImport :: factory('qti', $file, $user, $category);
        $result = $importer->import_content_object();
        return $result->get_id();
    }

    function import_groups()
    {
        $values = $this->exportValues();
        $this->parse_file($_FILES['file']['tmp_name'], $_FILES['file']['type']);
        return true;
    }
}
?>