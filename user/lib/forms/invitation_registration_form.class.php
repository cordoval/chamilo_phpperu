<?php
/**
 * $Id: register_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */

class InvitationRegistrationForm extends FormValidator
{
    const PASSWORD = 'password';
    const PASSWORD_CONFIRMATION = 'password_confirmation';

    /**
     * @var Invitation
     */
    private $invitation;

    /**
     * Creates a new RegisterForm
     * Used for a guest to register him/herself
     */
    function InvitationRegistrationForm($action, $invitation)
    {
        parent :: __construct('user_settings', 'post', $action);

        $this->build_basic_form();
        $this->invitation = $invitation;
        $this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    function build_basic_form()
    {
        $this->add_information_message('introduction', null, Translation :: get('InvitedUserRegistrationIntroduction'), true);

        $this->addElement('category', Translation :: get('Profile'));

        $this->addElement('text', User :: PROPERTY_USERNAME, Translation :: get('Username'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_USERNAME, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->add_warning_message('password_requirements', null, Translation :: get('GeneralPasswordRequirements'));

        $this->addElement('password', self :: PASSWORD, Translation :: get('Password'), array('size' => 40, 'autocomplete' => 'off', 'id' => 'password'));
        $this->addElement('password', self :: PASSWORD_CONFIRMATION, Translation :: get('PasswordConfirmation'), array('size' => 40, 'autocomplete' => 'off'));
        $this->addRule(array(self :: PASSWORD, self :: PASSWORD_CONFIRMATION), Translation :: get('PassTwo'), 'compare');
        $this->addRule(self :: PASSWORD, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addRule(self :: PASSWORD_CONFIRMATION, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('category');

        $this->addElement('category', Translation :: get('BasicProfile'));

        $this->addElement('text', User :: PROPERTY_FIRSTNAME, Translation :: get('FirstName'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_FIRSTNAME, Translation :: get('ThisFieldIsRequired'), 'required');

        $this->addElement('text', User :: PROPERTY_LASTNAME, Translation :: get('LastName'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_LASTNAME, Translation :: get('ThisFieldIsRequired'), 'required');

        // Email
        $this->addElement('text', User :: PROPERTY_EMAIL, Translation :: get('Email'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addRule(User :: PROPERTY_EMAIL, Translation :: get('WrongEmail'), 'email');
        $this->freeze(User :: PROPERTY_EMAIL);

        $this->addElement('category');

        if (PlatformSetting :: get('enable_terms_and_conditions', 'user'))
        {
            $this->addElement('category', Translation :: get('Information'));
            $this->addElement('textarea', 'conditions', Translation :: get('TermsAndConditions'), array('cols' => 80, 'rows' => 20, 'disabled' => 'disabled', 'style' => 'background-color: white;'));
            $this->addElement('checkbox', 'conditions_accept', '', Translation :: get('IAccept'));
            $this->addRule('conditions_accept', Translation :: get('ThisFieldIsRequired'), 'required');
            $this->addElement('category');
        }

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('CreateAccount'), array('class' => 'positive register'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Creates the user
     */
    function create_user()
    {
        $invitation = $this->invitation;
        $values = $this->exportValues();

        $user = new User();
        $user->set_username($values[User :: PROPERTY_USERNAME]);
        $user->set_password(Hashing :: hash($values[self :: PASSWORD]));
        $user->set_firstname($values[User :: PROPERTY_FIRSTNAME]);
        $user->set_lastname($values[User :: PROPERTY_LASTNAME]);
        $user->set_email($values[User :: PROPERTY_EMAIL]);
        $user->set_active(1);
        $user->set_registration_date(time());
        $user->set_activation_date($invitation->get_date());
        $user->set_expiration_date($invitation->get_expiration_date());

        if ($user->create())
        {
            $invitation->set_user_created(1);
            $invitation->update();

            $this->send_mail($user);

            Session :: register('_uid', intval($user->get_id()));
            Event :: trigger('register', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $user->get_id()));
            Event :: trigger('login', 'user', array('server' => $_SERVER, 'user' => $user));

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $invitation = $this->invitation;
        $defaults[User :: PROPERTY_EMAIL] = $invitation->get_email();
        $defaults['conditions'] = implode("\n", file(Path :: get(SYS_PATH) . 'documentation/license.txt'));
        parent :: setDefaults($defaults);
    }

    /**
     * Sends an email to the registered/created user
     */
    function send_email($user)
    {
        $options = array();
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $this->exportValue(self :: PASSWORD);
        $options['site_name'] = PlatformSetting :: get('site_name');
        $options['site_url'] = Path :: get(WEB_PATH);
        $options['admin_firstname'] = PlatformSetting :: get('administrator_firstname');
        $options['admin_surname'] = PlatformSetting :: get('administrator_surname');
        $options['admin_telephone'] = PlatformSetting :: get('administrator_telephone');
        $options['admin_email'] = PlatformSetting :: get('administrator_email');

        $subject = Translation :: get('YourRegistrationOn') . $options['site_name'];

        $body = PlatformSetting :: get('email_template', 'user');
        foreach ($options as $option => $value)
        {
            $body = str_replace('[' . $option . ']', $value, $body);
        }

        $mail = Mail :: factory($subject, $body, $user->get_email());
        $mail->send();
    }
}
?>