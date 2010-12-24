<?php
namespace repository\content_object\survey;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\FormValidator;

class SurveyTemplateForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $template;

    function __construct($form_type,  $action, $template)
    {
        parent :: __construct('template_settings', 'post', $action);
        
        $this->template = $template;
        
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
        
        $this->addElement('text', SurveyTemplate :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(SurveyTemplate :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(SurveyTemplate :: PROPERTY_DESCRIPTION, Translation :: get('Description'), true);
    
    }

    function build_editing_form()
    {
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

    function update_template()
    {
        $template = $this->template;
        $values = $this->exportValues();
        
        $template->set_name($values[SurveyTemplate :: PROPERTY_NAME]);
        $template->set_description($values[SurveyTemplate :: PROPERTY_DESCRIPTION]);
        return $template->update();
    }

    function create_template()
    {
        $template = $this->template;
        $values = $this->exportValues();
        $template->set_name($values[SurveyTemplate :: PROPERTY_NAME]);
        $template->set_description($values[SurveyTemplate :: PROPERTY_DESCRIPTION]);
        return $template->create();
    }
   

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $template = $this->template;
        $defaults[SurveyTemplate :: PROPERTY_NAME] = $template->get_name();
        $defaults[SurveyTemplate :: PROPERTY_DESCRIPTION] = $template->get_description();
        parent :: setDefaults($defaults);
    }
}
?>