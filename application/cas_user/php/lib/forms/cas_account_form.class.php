<?php
namespace application\cas_user;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Text;

class CasAccountForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    const PASSWORD_OPTION = 'password_option';
    const PASSWORD_GROUP = 'password_group';

    private $parent;

    /**
     * @var string
     */
    private $unencrypted_password;

    /**
     * @var CasAccount
     */
    private $cas_account;

    /**
     * @var User
     */
    private $user;

    function __construct($form_type, $cas_account, $action, $user)
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
        $this->addRule(CasAccount :: PROPERTY_FIRST_NAME, Translation :: get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required');

        $this->addElement('text', CasAccount :: PROPERTY_LAST_NAME, Translation :: get('LastName'), array("size" => "50"));
        $this->addRule(CasAccount :: PROPERTY_LAST_NAME, Translation :: get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required');

        $this->addElement('text', CasAccount :: PROPERTY_EMAIL, Translation :: get('Email'), array("size" => "50"));
        $this->addRule(CasAccount :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required');
        $this->addRule(CasAccount :: PROPERTY_EMAIL, Translation :: get('WrongEmail'), 'email');

        $group = array();
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $group[] = & $this->createElement('radio', self :: PASSWORD_OPTION, null, Translation :: get('KeepPassword') . '<br />', 2);
        }
        $group[] = & $this->createElement('radio', self :: PASSWORD_OPTION, null, Translation :: get('AutoGeneratePassword') . '<br />', 1);
        $group[] = & $this->createElement('radio', self :: PASSWORD_OPTION, null, null, 0);
        $group[] = & $this->createElement('password', CasAccount :: PROPERTY_PASSWORD, null, null, array('autocomplete' => 'off'));
        $this->addGroup($group, self :: PASSWORD_GROUP, Translation :: get('Password'), '');

        $affiliation_options = array();
        $affiliation_options['student'] = Translation :: get('Student');
        $affiliation_options['employee'] = Translation :: get('Employee');
        $affiliation_options['teacher'] = Translation :: get('Teacher');
        $affiliation_options['external'] = Translation :: get('External');

        $this->addElement('select', CasAccount :: PROPERTY_AFFILIATION, Translation :: get('Affiliation'), $affiliation_options);

        $this->addElement('text', CasAccount :: PROPERTY_GROUP, Translation :: get('Group'), array("size" => "50"));
        $this->addRule(CasAccount :: PROPERTY_GROUP, Translation :: get('ThisFieldIsRequired', null, Utilities::COMMON_LIBRARIES), 'required');

        $this->addElement('checkbox', CasAccount :: PROPERTY_STATUS, Translation :: get('Enabled'), '', 1);

    }

    function build_editing_form()
    {
        $group = $this->group;
        $parent = $this->parent;

        $this->build_basic_form();

        $this->addElement('hidden', CasAccount :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update', null, Utilities::COMMON_LIBRARIES), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities::COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities::COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities::COMMON_LIBRARIES), array('class' => 'normal empty'));

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

        if ($values[self :: PASSWORD_GROUP][self :: PASSWORD_OPTION] != 2)
        {
            $this->unencrypted_password = $values[self :: PASSWORD_GROUP][self :: PASSWORD_OPTION] == 1 ? $this->unencrypted_password : $values[self :: PASSWORD_GROUP][CasAccount :: PROPERTY_PASSWORD];
            $password = md5($this->unencrypted_password);
            $cas_account->set_password($password);
        }

        return $cas_account->update();
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

        $this->unencrypted_password = $values[self :: PASSWORD_GROUP][self :: PASSWORD_OPTION] == 1 ? Text :: generate_password() : $values[self :: PASSWORD_GROUP][CasAccount :: PROPERTY_PASSWORD];
        $cas_account->set_password(md5($this->unencrypted_password));

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

        if ($this->form_type == self :: TYPE_EDIT)
        {
            $defaults[self :: PASSWORD_GROUP][self :: PASSWORD_OPTION] = 2;
        }
        else
        {
            $defaults[self :: PASSWORD_GROUP][self :: PASSWORD_OPTION] = 1;
        }

        parent :: setDefaults($defaults);
    }

    function get_cas_account()
    {
        return $this->cas_account;
    }
}
?>