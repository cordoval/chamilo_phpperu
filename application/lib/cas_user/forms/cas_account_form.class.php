<?php
class CasAccountForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $parent;

    /**
     * @var CasAccount
     */
    private $cas_account;

    /**
     * @var User
     */
    private $user;

    function CasAccountForm($form_type, $cas_account, $action, $user)
    {
        parent :: __construct('cas_account', 'post', $action);

        $this->cas_account = $cas_account;
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
        $this->addElement('text', CasAccount :: PROPERTY_FIRST_NAME, Translation :: get('FirstName'), array("size" => "50"));
        $this->addRule(CasAccount :: PROPERTY_FIRST_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('text', CasAccount :: PROPERTY_LAST_NAME, Translation :: get('LastName'), array("size" => "50"));
        $this->addRule(CasAccount :: PROPERTY_LAST_NAME, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('text', CasAccount :: PROPERTY_EMAIL, Translation :: get('Email'), array("size" => "50"));
        $this->addRule(CasAccount :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addRule(CasAccount :: PROPERTY_EMAIL, Translation :: get('WrongEmail'), 'email');

        $affiliation_options = array();
        $affiliation_options['student'] = Translation :: get('Student');
        $affiliation_options['employee'] = Translation :: get('Employee');
        $affiliation_options['teacher'] = Translation :: get('Teacher');
        $affiliation_options['external'] = Translation :: get('External');

        $this->addElement('select', CasAccount :: PROPERTY_AFFILIATION, Translation :: get('Affiliation'), $affiliation_options);

        $this->addElement('text', CasAccount :: PROPERTY_GROUP, Translation :: get('Group'), array("size" => "50"));
        $this->addRule(CasAccount :: PROPERTY_GROUP, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('checkbox', CasAccount :: PROPERTY_STATUS, Translation :: get('Enabled'), '', 1);

    }

    function build_editing_form()
    {
        $group = $this->group;
        $parent = $this->parent;

        $this->build_basic_form();

        $this->addElement('hidden', CasAccount :: PROPERTY_ID);

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

    function update_cas_account()
    {
        $cas_account = $this->cas_account;
        $values = $this->exportValues();

        $cas_account->set_first_name($values[CasAccount :: PROPERTY_FIRST_NAME]);
        $cas_account->set_last_name($values[CasAccount :: PROPERTY_LAST_NAME]);
        $cas_account->set_email($values[CasAccount :: PROPERTY_EMAIL]);
        $cas_account->set_affiliation($values[CasAccount :: PROPERTY_AFFILIATION]);
        $cas_account->set_group($values[CasAccount :: PROPERTY_GROUP]);
        $cas_account->set_status($values[CasAccount :: PROPERTY_STATUS]);

        return $cas_account->create();
    }

    function create_cas_account()
    {
        $cas_account = $this->cas_account;
        $values = $this->exportValues();

        $cas_account->set_first_name($values[CasAccount :: PROPERTY_FIRST_NAME]);
        $cas_account->set_last_name($values[CasAccount :: PROPERTY_LAST_NAME]);
        $cas_account->set_email($values[CasAccount :: PROPERTY_EMAIL]);
        $cas_account->set_affiliation($values[CasAccount :: PROPERTY_AFFILIATION]);
        $cas_account->set_group($values[CasAccount :: PROPERTY_GROUP]);
        $cas_account->set_status($values[CasAccount :: PROPERTY_STATUS]);

        return $cas_account->create();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $cas_account = $this->cas_account;
        $defaults[CasAccount :: PROPERTY_ID] = $cas_account->get_id();
        $defaults[CasAccount :: PROPERTY_FIRST_NAME] = $cas_account->get_first_name();
        $defaults[CasAccount :: PROPERTY_LAST_NAME] = $cas_account->get_last_name();
        $defaults[CasAccount :: PROPERTY_EMAIL] = $cas_account->get_email();
        $defaults[CasAccount :: PROPERTY_AFFILIATION] = $cas_account->get_affiliation();
        $defaults[CasAccount :: PROPERTY_GROUP] = $cas_account->get_group();
        $defaults[CasAccount :: PROPERTY_STATUS] = $cas_account->get_status();
        parent :: setDefaults($defaults);
    }

    function get_cas_account()
    {
        return $this->cas_account;
    }
}
?>