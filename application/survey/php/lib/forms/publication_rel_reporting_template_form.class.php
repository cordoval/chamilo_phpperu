<?php 
namespace survey;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\FormValidator;

use reporting\ReportingManager;

class SurveyPublicationRelReportingTemplateRegistrationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const RESULT_SUCCESS = 'SurveyPublicationRelReportingTemplateRegistrationUpdated';
    const RESULT_ERROR = 'SurveyPublicationRelReportingTemplateRegistrationUpdateFailed';
    
    private $publication_rel_reporting_template_registration;
    private $user;
    private $parent;

    function SurveyPublicationRelReportingTemplateRegistrationForm($parent, $form_type, $publication_rel_reporting_template_registration, $action, $user)
    {
        parent :: __construct('create_publication_rel_reporting_template_registration', 'post', $action);
        
        $this->publication_rel_reporting_template_registration = $publication_rel_reporting_template_registration;
        $this->user = $user;
        $this->parent = $parent;
        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        $this->setDefaults();
    }

    function build_basic_form()
    {
        
        $this->addElement('text', SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('textarea', SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_DESCRIPTION, Translation :: get('Description'), array("rows" => "5", "cols" => "17"));
        
        $levels = $this->get_levels();
        if (count($levels))
        {
            $this->addElement('select', SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_LEVEL, Translation :: get('Level'), $levels);
            $this->addRule(SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_LEVEL, Translation :: get('ThisFieldIsRequired'), 'required');
        }
    
    }

    function build_editing_form()
    {
        $publication_rel_reporting_template_registration = $this->publication_rel_reporting_template_registration;
        
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update()
    {
        $publication_rel_reporting_template_registration = $this->publication_rel_reporting_template_registration;
        $values = $this->exportValues();
        
        $level = $values[SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_LEVEL];
        if ($level)
        {
            $publication_rel_reporting_template_registration->set_level($level);
        }
        else
        {
            $publication_rel_reporting_template_registration->set_level(0);
        }
        $publication_rel_reporting_template_registration->set_name($values[SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_NAME]);
        $publication_rel_reporting_template_registration->set_description($values[SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_DESCRIPTION]);
        
        return $publication_rel_reporting_template_registration->update();
    }

    function create()
    {
        $publication_rel_reporting_template_registration = $this->publication_rel_reporting_template_registration;
        $values = $this->exportValues();
        
        $publication_rel_reporting_template_registration->set_name($values[SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_NAME]);
        $publication_rel_reporting_template_registration->set_description($values[SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_DESCRIPTION]);
        
        $level = $values[SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_LEVEL];
        if ($level)
        {
            $publication_rel_reporting_template_registration->set_level($level);
        }
        else
        {
            $publication_rel_reporting_template_registration->set_level(0);
        }
        
        return $publication_rel_reporting_template_registration->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $publication_rel_reporting_template_registration = $this->publication_rel_reporting_template_registration;
        $defaults[SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_LEVEL] = $publication_rel_reporting_template_registration->get_level();
        $defaults[SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_NAME] = $publication_rel_reporting_template_registration->get_name();
        $defaults[SurveyPublicationRelReportingTemplateRegistration :: PROPERTY_DESCRIPTION] = $publication_rel_reporting_template_registration->get_description();
        parent :: setDefaults($defaults);
    }

    function get_publication_rel_reporting_template_registration()
    {
        return $this->publication_rel_reporting_template_registration;
    }

    function get_levels()
    {
        
        $registration = ReportingDataManager :: get_instance()->retrieve_reporting_template_registration($this->publication_rel_reporting_template_registration->get_reporting_template_registration_id());
        $reporting_template = ReportingTemplate :: factory($registration, $this->parent);
        if ($reporting_template instanceof SurveyLevelReportingTemplateInterface)
        {
            $publication = SurveyDataManager :: get_instance()->retrieve_survey_publication($this->publication_rel_reporting_template_registration->get_publication_id());
            $survey = $publication->get_publication_object();
            $level_count = $survey->count_levels();
            $levels = array();
            $level = 1;
            while ($level_count > 0)
            {
                $context_template = $survey->get_context_template($level_count);
                $name = $context_template->get_context_type_name();
                $levels[$context_template->get_id()] = $name;
                $level_count --;
            }
            return $levels;
        }
        else
        {
            return array();
        }
    
    }

}
?>