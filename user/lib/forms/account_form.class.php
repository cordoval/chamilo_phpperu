<?php
/**
 * $Id: account_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */

class AccountForm extends FormValidator
{

    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'UserUpdated';
    const RESULT_ERROR = 'UserUpdateFailed';
    const NEW_PASSWORD = 'new_password';
    const NEW_PASSWORD_CONFIRMATION = 'new_password_confirmation';

    private $parent;
    private $user;
    private $unencryptedpass;
    private $adm;

    /**
     * Creates a new AccountForm
     */
    function AccountForm($form_type, $user, $action)
    {
        parent :: __construct('user_account', 'post', $action);

        $this->user = $user;
        $this->adm = AdminDataManager :: get_instance();

        $this->form_type = $form_type;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }

        $this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    function build_basic_form()
    {
        // Show user picture
        $this->addElement('html', '<img src="' . $this->user->get_full_picture_url() . '" alt="' . $this->user->get_fullname() . '" style="position:absolute; right: 40px; z-index:1; border:1px solid black; max-width: 150px; margin-top: 10px"/>');

        $this->addElement('category', Translation :: get('PersonalDetails'));
        // Name
        $this->addElement('text', User :: PROPERTY_LASTNAME, Translation :: get('LastName'), array("size" => "50"));
        $this->addElement('text', User :: PROPERTY_FIRSTNAME, Translation :: get('FirstName'), array("size" => "50"));

        if (PlatformSetting :: get('allow_change_firstname', UserManager :: APPLICATION_NAME) == 0)
        {
            $this->freeze(array(User :: PROPERTY_FIRSTNAME));
        }
        if (PlatformSetting :: get('allow_change_lastname', UserManager :: APPLICATION_NAME) == 0)
        {
            $this->freeze(array(User :: PROPERTY_LASTNAME));
        }

        $this->applyFilter(array(User :: PROPERTY_LASTNAME, User :: PROPERTY_FIRSTNAME), 'stripslashes');
        $this->applyFilter(array(User :: PROPERTY_LASTNAME, User :: PROPERTY_FIRSTNAME), 'trim');

        if (PlatformSetting :: get('allow_change_lastname', UserManager :: APPLICATION_NAME) == 1)
        {
            $this->addRule(User :: PROPERTY_LASTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        }
        if (PlatformSetting :: get('allow_change_firstname', UserManager :: APPLICATION_NAME) == 1)
        {
            $this->addRule(User :: PROPERTY_FIRSTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        }
        // Official Code
        $this->addElement('text', User :: PROPERTY_OFFICIAL_CODE, Translation :: get('OfficialCode'), array("size" => "50"));

        if (PlatformSetting :: get('allow_change_official_code', UserManager :: APPLICATION_NAME) == 0)
        {
            $this->freeze(User :: PROPERTY_OFFICIAL_CODE);
        }

        $this->applyFilter(User :: PROPERTY_OFFICIAL_CODE, 'stripslashes');
        $this->applyFilter(User :: PROPERTY_OFFICIAL_CODE, 'trim');

        if (PlatformSetting :: get('require_official_code', UserManager :: APPLICATION_NAME) && PlatformSetting :: get('allow_change_official_code', UserManager :: APPLICATION_NAME) == 1)
        {
            $this->addRule(User :: PROPERTY_OFFICIAL_CODE, Translation :: get('ThisFieldIsRequired'), 'required');
        }

        // Email
        $this->addElement('text', User :: PROPERTY_EMAIL, Translation :: get('Email'), array("size" => "50"));

        if (PlatformSetting :: get('allow_change_email', UserManager :: APPLICATION_NAME) == 0)
        {
            $this->freeze(User :: PROPERTY_EMAIL);
        }

        $this->applyFilter(User :: PROPERTY_EMAIL, 'stripslashes');
        $this->applyFilter(User :: PROPERTY_EMAIL, 'trim');

        if (PlatformSetting :: get('require_email', UserManager :: APPLICATION_NAME))
        {
            $this->addRule(User :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
        }

        $this->addRule(User :: PROPERTY_EMAIL, Translation :: get('EmailWrong'), 'email');
        // Username
        $this->addElement('text', User :: PROPERTY_USERNAME, Translation :: get('Username'), array("size" => "50"));

        if (PlatformSetting :: get('allow_change_username', UserManager :: APPLICATION_NAME) == 0 || !Authentication :: factory($this->user->get_auth_source()) instanceof ChangeableUsername)
        {
            $this->freeze(User :: PROPERTY_USERNAME);
        }

        $this->applyFilter(User :: PROPERTY_USERNAME, 'stripslashes');
        $this->applyFilter(User :: PROPERTY_USERNAME, 'trim');
        $this->addRule(User :: PROPERTY_USERNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addRule(User :: PROPERTY_USERNAME, Translation :: get('UsernameWrong'), 'username');
        //Todo: The rule to check unique username should be updated to the LCMS code api
        //$this->addRule(User :: PROPERTY_USERNAME, Translation :: get('UserTaken'), 'username_available', $user_data['username']);
        $this->addElement('category');

        // Password
        if (PlatformSetting :: get('allow_change_password', UserManager :: APPLICATION_NAME) == 1 && Authentication :: factory($this->user->get_auth_source()) instanceof ChangeablePassword)
        {
            $this->addElement('category', Translation :: get('ChangePassword'));

            $password_requirements = Authentication :: factory($this->user->get_auth_source())->get_password_requirements();
            if (!is_null($password_requirements))
            {
                $this->add_warning_message('password_requirements', null, $password_requirements);
            }

            $this->addElement('static', null, null, '<em>' . Translation :: get('EnterCurrentPassword') . '</em>');
            $this->addElement('password', User :: PROPERTY_PASSWORD, Translation :: get('CurrentPassword'), array('size' => 40, 'autocomplete' => 'off'));
            $this->addElement('static', null, null, '<em>' . Translation :: get('EnterNewPasswordTwice') . '</em>');
            $this->addElement('password', self :: NEW_PASSWORD, Translation :: get('NewPassword'), array('size' => 40, 'autocomplete' => 'off', 'id' => 'new_password'));
            $this->addElement('password', self :: NEW_PASSWORD_CONFIRMATION, Translation :: get('PasswordConfirmation'), array('size' => 40, 'autocomplete' => 'off'));
            $this->addRule(array(self :: NEW_PASSWORD, self :: NEW_PASSWORD_CONFIRMATION), Translation :: get('PassTwo'), 'compare');

            $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'plugin/jquery/jquery.jpassword.js'));
            $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/password.js'));
            $this->addElement('category');
        }

        // Picture
        if (PlatformSetting :: get('allow_change_user_picture', UserManager :: APPLICATION_NAME) == 1)
        {
            $this->addElement('category', Translation :: get('PlatformOptions'));
            $this->addElement('file', User :: PROPERTY_PICTURE_URI, ($this->user->has_picture() ? Translation :: get('UpdateImage') : Translation :: get('AddImage')));
            if ($this->form_type == self :: TYPE_EDIT && $this->user->has_picture())
            {
                $this->addElement('checkbox', 'remove_picture', null, Translation :: get('DelImage'));
            }
            $this->addRule(User :: PROPERTY_PICTURE_URI, Translation :: get('OnlyImagesAllowed'), 'mimetype', array('image/gif', 'image/jpeg', 'image/png', 'image/x-png'));
            $this->addElement('category');
        }
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', User :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds an update form
     */
    function update_account()
    {
        $user = $this->user;
        $values = $this->exportValues();
        if (PlatformSetting :: get('allow_change_firstname', UserManager :: APPLICATION_NAME))
        {
            $user->set_firstname($values[User :: PROPERTY_FIRSTNAME]);
        }

        if (PlatformSetting :: get('allow_change_lastname', UserManager :: APPLICATION_NAME))
        {
            $user->set_lastname($values[User :: PROPERTY_LASTNAME]);
        }

        if (PlatformSetting :: get('allow_change_official_code', UserManager :: APPLICATION_NAME))
        {
            $user->set_official_code($values[User :: PROPERTY_OFFICIAL_CODE]);
        }

        if (PlatformSetting :: get('allow_change_email', UserManager :: APPLICATION_NAME))
        {
            $user->set_email($values[User :: PROPERTY_EMAIL]);
        }

        if (PlatformSetting :: get('allow_change_username', UserManager :: APPLICATION_NAME) && Authentication :: factory($this->user->get_auth_source()) instanceof ChangeableUsername)
        {
            $user->set_username($values[User :: PROPERTY_USERNAME]);
        }

        if (PlatformSetting :: get('allow_change_password', UserManager :: APPLICATION_NAME) && strlen($values[User :: PROPERTY_PASSWORD]) && Authentication :: factory($this->user->get_auth_source()) instanceof ChangeablePassword)
        {
            $result = Authentication :: factory($this->user->get_auth_source())->change_password($user, $values[User :: PROPERTY_PASSWORD], $values[self :: NEW_PASSWORD]);
            if (!$result)
            {
                return false;
            }
        }

        if (PlatformSetting :: get('allow_change_user_picture', UserManager :: APPLICATION_NAME))
        {
            if (isset($_FILES['picture_uri']) && strlen($_FILES['picture_uri']['name']) > 0)
            {
                $user->set_picture_file($_FILES['picture_uri']);
            }
            if (isset($values['remove_picture']))
            {
                $user->delete_picture();
            }
        }

        $value = $user->update();

        if ($value)
        {
            Event :: trigger('update', UserManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $user->get_id(), ChangesTracker :: PROPERTY_USER_ID => $user->get_id()));
        }

        return $value;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $user = $this->user;
        $defaults[User :: PROPERTY_ID] = $user->get_id();
        $defaults[User :: PROPERTY_LASTNAME] = $user->get_lastname();
        $defaults[User :: PROPERTY_FIRSTNAME] = $user->get_firstname();
        $defaults[User :: PROPERTY_EMAIL] = $user->get_email();
        $defaults[User :: PROPERTY_USERNAME] = $user->get_username();
        $defaults[User :: PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        parent :: setDefaults($defaults);
    }
}
?>