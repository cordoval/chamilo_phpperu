<?php
/**
 * $Id: assessment_qti_import.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */
class AssessmentToolQtiImportComponent extends AssessmentToolComponent
{

    function run()
    {
        if (! $this->is_allowed(EDIT_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $form = $this->build_importing_form();
        if ($form->validate())
        {
            //import
            $aid = $this->import_qti($form);
            $this->redirect(null, false, array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_PUBLISH, 'object' => $aid));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(AssessmentTool :: PARAM_ACTION => AssessmentTool :: ACTION_IMPORT_QTI)), Translation :: get('ImportQTI')));
            $trail->add_help('courses assessment tool');
            $this->display_header();
            
            $this->action_bar = $this->get_toolbar();
            echo $this->action_bar->as_html();
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