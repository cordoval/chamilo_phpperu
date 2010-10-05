<?php
class CasUserRequestForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $parent;

    /**
     * @var CasUserRequest
     */
    private $cas_user_request;

    /**
     * @var User
     */
    private $user;

    function CasUserRequestForm($form_type, $cas_user_request, $action, $user)
    {
        parent :: __construct('cas_user_request', 'post', $action);

        $this->cas_user_request = $cas_user_request;
        $this->user = $user;
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
        $this->addElement('text', CasUserRequest :: PROPERTY_FIRST_NAME, Translation :: get('FirstName'), array("size" => "50"));
        $this->addRule(CasUserRequest :: PROPERTY_FIRST_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('text', CasUserRequest :: PROPERTY_LAST_NAME, Translation :: get('LastName'), array("size" => "50"));
        $this->addRule(CasUserRequest :: PROPERTY_LAST_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('text', CasUserRequest :: PROPERTY_EMAIL, Translation :: get('Email'), array("size" => "50"));
        $this->addRule(CasUserRequest :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addRule(CasUserRequest :: PROPERTY_EMAIL, Translation :: get('WrongEmail'), 'email');

        $affiliation_options = array();
        $affiliation_options['student'] = Translation :: get('Student');
        $affiliation_options['employee'] = Translation :: get('Employee');
        $affiliation_options['teacher'] = Translation :: get('Teacher');
        $affiliation_options['external'] = Translation :: get('External');

        $this->addElement('select', CasUserRequest :: PROPERTY_AFFILIATION, Translation :: get('Affiliation'), $affiliation_options);

        $this->add_html_editor(CasUserRequest :: PROPERTY_MOTIVATION, Translation :: get('Motivation'), true);

    }

    function build_editing_form()
    {
        $group = $this->group;
        $parent = $this->parent;

        $this->build_basic_form();

        $this->addElement('hidden', CasUserRequest :: PROPERTY_ID);

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

    function update_cas_user_request()
    {
        $cas_user_request = $this->cas_user_request;
        $values = $this->exportValues();

        $cas_user_request->set_first_name($values[CasUserRequest :: PROPERTY_FIRST_NAME]);
        $cas_user_request->set_last_name($values[CasUserRequest :: PROPERTY_LAST_NAME]);
        $cas_user_request->set_email($values[CasUserRequest :: PROPERTY_EMAIL]);
        $cas_user_request->set_affiliation($values[CasUserRequest :: PROPERTY_AFFILIATION]);
        $cas_user_request->set_motivation($values[CasUserRequest :: PROPERTY_MOTIVATION]);

        return $cas_user_request->create();
    }

    function create_cas_user_request()
    {
        $cas_user_request = $this->cas_user_request;
        $values = $this->exportValues();

        $cas_user_request->set_first_name($values[CasUserRequest :: PROPERTY_FIRST_NAME]);
        $cas_user_request->set_last_name($values[CasUserRequest :: PROPERTY_LAST_NAME]);
        $cas_user_request->set_email($values[CasUserRequest :: PROPERTY_EMAIL]);
        $cas_user_request->set_affiliation($values[CasUserRequest :: PROPERTY_AFFILIATION]);
        $cas_user_request->set_motivation($values[CasUserRequest :: PROPERTY_MOTIVATION]);
        $cas_user_request->set_requester_id($this->user->get_id());
        $cas_user_request->set_request_date(time());

        return $cas_user_request->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $cas_user_request = $this->cas_user_request;
        $defaults[CasUserRequest :: PROPERTY_ID] = $cas_user_request->get_id();
        $defaults[CasUserRequest :: PROPERTY_FIRST_NAME] = $cas_user_request->get_first_name();
        $defaults[CasUserRequest :: PROPERTY_LAST_NAME] = $cas_user_request->get_last_name();
        $defaults[CasUserRequest :: PROPERTY_EMAIL] = $cas_user_request->get_email();
        $defaults[CasUserRequest :: PROPERTY_MOTIVATION] = $cas_user_request->get_motivation();
        $defaults[CasUserRequest :: PROPERTY_AFFILIATION] = $cas_user_request->get_affiliation();
        parent :: setDefaults($defaults);
    }

    function get_cas_user_request()
    {
        return $this->cas_user_request;
    }
}
?>