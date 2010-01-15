<?php
/**
 * $Id: user_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */

class UserForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'UserUpdated';
    const RESULT_ERROR = 'UserUpdateFailed';
    const PARAM_FOREVER = 'forever';

    private $parent;
    private $user;
    private $form_user;
    private $unencryptedpass;
    private $adminDM;

    /**
     * Creates a new UserForm
     * Used by the admin to create/update a user
     */
    function UserForm($form_type, $user, $form_user, $action)
    {
        parent :: __construct('user_settings', 'post', $action);

        $this->adminDM = AdminDataManager :: get_instance();
        $this->user = $user;
        $this->form_user = $form_user;

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

    /**
     * Creates a basic form
     */
    function build_basic_form()
    {
        $this->addElement('html', '<img src="' . $this->user->get_full_picture_url() . '" alt="' . $this->user->get_fullname() . '" style="position:absolute; right: 10px; z-index:1; border:1px solid black; max-width: 150px;"/>');
        // Lastname
        $this->addElement('text', User :: PROPERTY_LASTNAME, Translation :: get('LastName'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_LASTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        // Firstname
        $this->addElement('text', User :: PROPERTY_FIRSTNAME, Translation :: get('FirstName'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_FIRSTNAME, Translation :: get('ThisFieldIsRequired'), 'required');
        // Email
        $this->addElement('text', User :: PROPERTY_EMAIL, Translation :: get('Email'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addRule(User :: PROPERTY_EMAIL, Translation :: get('WrongEmail'), 'email');
        // Username
        $this->addElement('text', User :: PROPERTY_USERNAME, Translation :: get('Username'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_USERNAME, Translation :: get('ThisFieldIsRequired'), 'required');

        $group = array();
        $group[] = & $this->createElement('radio', User :: PROPERTY_ACTIVE, null, Translation :: get('Yes'), 1);
        $group[] = & $this->createElement('radio', User :: PROPERTY_ACTIVE, null, Translation :: get('No'), 0);
        $this->addGroup($group, 'active', Translation :: get('Active'), '&nbsp;');

        //pw
        $group = array();
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $group[] = & $this->createElement('radio', 'pass', null, Translation :: get('KeepPassword') . '<br />', 2);
        }
        $group[] = & $this->createElement('radio', 'pass', null, Translation :: get('AutoGeneratePassword') . '<br />', 1);
        $group[] = & $this->createElement('radio', 'pass', null, null, 0);
        $group[] = & $this->createElement('password', User :: PROPERTY_PASSWORD, null, null, array('autocomplete' => 'off'));
        $this->addGroup($group, 'pw', Translation :: get('Password'), '');

        //$this->add_forever_or_expiration_date_window(User :: PROPERTY_EXPIRATION_DATE, 'ExpirationDate');
        $this->add_forever_or_timewindow(User :: PROPERTY_EXPIRATION_DATE, 'ExpirationDate');

        // Official Code
        $this->addElement('text', User :: PROPERTY_OFFICIAL_CODE, Translation :: get('OfficialCode'), array("size" => "50"));
        // Picture URI
        $this->addElement('file', User :: PROPERTY_PICTURE_URI, Translation :: get('AddPicture'));
        		$allowed_picture_types = array ('jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF');
        		$this->addRule(User :: PROPERTY_PICTURE_URI, Translation :: get('OnlyImagesAllowed'), 'filetype', $allowed_picture_types);
        //$this->addRule(User :: PROPERTY_PICTURE_URI, Translation :: get('OnlyImagesAllowed'), 'mimetype', array('image/gif', 'image/jpeg', 'image/png', 'image/x-png'));
        // Phone Number
        $this->addElement('text', User :: PROPERTY_PHONE, Translation :: get('PhoneNumber'), array("size" => "50"));
        
        // Disk Quota
        $this->addElement('text', User :: PROPERTY_DISK_QUOTA, Translation :: get('DiskQuota'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_DISK_QUOTA, Translation :: get('FieldMustBeNumeric'), 'numeric', null, 'server');
        // Database Quota
        $this->addElement('text', User :: PROPERTY_DATABASE_QUOTA, Translation :: get('DatabaseQuota'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_DATABASE_QUOTA, Translation :: get('FieldMustBeNumeric'), 'numeric', null, 'server');
        // Version quota
        $this->addElement('text', User :: PROPERTY_VERSION_QUOTA, Translation :: get('VersionQuota'), array("size" => "50"));
        $this->addRule(User :: PROPERTY_VERSION_QUOTA, Translation :: get('FieldMustBeNumeric'), 'numeric', null, 'server');

        // Status
        $status = array();
        $status[5] = Translation :: get('Student');
        $status[1] = Translation :: get('CourseAdmin');
        $this->addElement('select', User :: PROPERTY_STATUS, Translation :: get('Status'), $status);
        // Platform admin
        if ($this->user->is_platform_admin() && $this->user->get_id() == $this->form_user->get_id() && $this->form_type == self :: TYPE_EDIT)
        {
            $this->add_warning_message('admin_lockout_message', null, Translation :: get('LockOutWarningMessage'));
        }
        $group = array();
        $group[] = & $this->createElement('radio', User :: PROPERTY_PLATFORMADMIN, null, Translation :: get('Yes'), 1);
        $group[] = & $this->createElement('radio', User :: PROPERTY_PLATFORMADMIN, null, Translation :: get('No'), 0);
        $this->addGroup($group, 'admin', Translation :: get('PlatformAdmin'), '&nbsp;');

        //  Send email
        $group = array();
        $group[] = & $this->createElement('radio', 'send_mail', null, Translation :: get('Yes'), 1);
        $group[] = & $this->createElement('radio', 'send_mail', null, Translation :: get('No'), 0);
        $this->addGroup($group, 'mail', Translation :: get('SendMailToNewUser'), '&nbsp;');

        // RightsTemplates element finder
        $user = $this->user;

        if ($this->form_type == self :: TYPE_EDIT)
        {
            $linked_rights_templates = $user->get_rights_templates();
            $user_rights_templates = RightsUtilities :: rights_templates_for_element_finder($linked_rights_templates);
        }
        else
        {
            $user_rights_templates = array();
        }

        $rights_templates = RightsDataManager :: get_instance()->retrieve_rights_templates();
        while ($rights_template = $rights_templates->next_result())
        {
            $defaults[$rights_template->get_id()] = array('title' => $rights_template->get_name(), 'description', $rights_template->get_description(), 'class' => 'rights_template');
        }

        $url = Path :: get(WEB_PATH) . 'rights/xml_feeds/xml_rights_template_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('AddRightsTemplates');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $hidden = true;

        $elem = $this->addElement('element_finder', 'rights_templates', null, $url, $locale, $user_rights_templates);
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(count($user_rights_templates) == 0);
    }

    /**
     * Creates an editing form
     */
    function build_editing_form()
    {
        $this->build_basic_form();

        $this->addElement('hidden', User :: PROPERTY_ID);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Creates a creating form
     */
    function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Updates the user with the new data
     */
    function update_user()
    {
        $user = $this->user;
        $values = $this->exportValues();

        if ($values['pw']['pass'] != '2')
        {
            $this->unencryptedpass = $values['pw']['pass'] == '1' ? $this->unencryptedpass : $values['pw'][User :: PROPERTY_PASSWORD];
            $password = Hashing :: hash($this->unencryptedpass);
            $user->set_password($password);
        }

        if ($_FILES[User :: PROPERTY_PICTURE_URI] && file_exists($_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name']))
        {
            $user->set_picture_file($_FILES[User :: PROPERTY_PICTURE_URI]);
        }

        $user->set_lastname($values[User :: PROPERTY_LASTNAME]);
        $user->set_firstname($values[User :: PROPERTY_FIRSTNAME]);
        $user->set_email($values[User :: PROPERTY_EMAIL]);
        $user->set_username($values[User :: PROPERTY_USERNAME]);

        if ($values['ExpirationDateforever'] != 0)
        {
            $user->set_expiration_date(0);
            $user->set_activation_date(0);
        }
        else
        {
            $act_date = Utilities :: time_from_datepicker($values['ExpirationDatefrom_date']);
            $exp_date = Utilities :: time_from_datepicker($values['ExpirationDateto_date']);
            $user->set_activation_date($act_date);
            $user->set_expiration_date($exp_date);
        }

        $user->set_official_code($values[User :: PROPERTY_OFFICIAL_CODE]);
        $user->set_phone($values[User :: PROPERTY_PHONE]);
        $user->set_status(intval($values[User :: PROPERTY_STATUS]));
        $user->set_version_quota(intval($values[User :: PROPERTY_VERSION_QUOTA]));
        $user->set_database_quota(intval($values[User :: PROPERTY_DATABASE_QUOTA]));
        $user->set_disk_quota(intval($values[User :: PROPERTY_DISK_QUOTA]));
        
        $user->set_active(intval($values['active'][User :: PROPERTY_ACTIVE]));
        $user->set_platformadmin(intval($values['admin'][User :: PROPERTY_PLATFORMADMIN]));
        $send_mail = intval($values['mail']['send_mail']);
        if ($send_mail)
        {
            $this->send_email($user);
        }

        $value = $user->update();

        if (! $user->update_rights_template_links($values['rights_templates']))
        {
            return false;
        }

        if ($value)
        {
            Events :: trigger_event('update', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->form_user->get_id()));
        }

        return $value;
    }

    /**
     * Creates the user, and stores it in the database
     */
    function create_user()
    {
        $user = $this->user;
        $values = $this->exportValues();

        $password = $values['pw']['pass'] == '1' ? Text :: generate_password() : $values['pw'][User :: PROPERTY_PASSWORD];

        if ($_FILES[User :: PROPERTY_PICTURE_URI] && file_exists($_FILES[User :: PROPERTY_PICTURE_URI]['tmp_name']))
        {
            $user->set_picture_file($_FILES[User :: PROPERTY_PICTURE_URI]);
        }
        $udm = UserDataManager :: get_instance();
        if ($udm->is_username_available($values[User :: PROPERTY_USERNAME], $values[User :: PROPERTY_ID]))
        {
            $user->set_id($values[User :: PROPERTY_ID]);
            $user->set_lastname($values[User :: PROPERTY_LASTNAME]);
            $user->set_firstname($values[User :: PROPERTY_FIRSTNAME]);
            $user->set_email($values[User :: PROPERTY_EMAIL]);
            $user->set_username($values[User :: PROPERTY_USERNAME]);
            $user->set_password(Hashing :: hash($password));
            $this->unencryptedpass = $password;

            if ($values['ExpirationDateforever'] != 0)
            {
                $user->set_expiration_date(0);
                $user->set_activation_date(0);
            }
            else
            {
                $act_date = Utilities :: time_from_datepicker($values['ExpirationDatefrom_date']);
                $exp_date = Utilities :: time_from_datepicker($values['ExpirationDateto_date']);
                $user->set_activation_date($act_date);
                $user->set_expiration_date($exp_date);
            }

            $user->set_official_code($values[User :: PROPERTY_OFFICIAL_CODE]);
            $user->set_phone($values[User :: PROPERTY_PHONE]);
            $user->set_status(intval($values[User :: PROPERTY_STATUS]));
            if ($values[User :: PROPERTY_VERSION_QUOTA] != '')
                $user->set_version_quota(intval($values[User :: PROPERTY_VERSION_QUOTA]));
            if ($values[User :: PROPERTY_DATABASE_QUOTA] != '')
                $user->set_database_quota(intval($values[User :: PROPERTY_DATABASE_QUOTA]));
            if ($values[User :: PROPERTY_DISK_QUOTA] != '')
                $user->set_disk_quota(intval($values[User :: PROPERTY_DISK_QUOTA]));

            $user->set_platformadmin(intval($values['admin'][User :: PROPERTY_PLATFORMADMIN]));
            $send_mail = intval($values['mail']['send_mail']);
            if ($send_mail)
            {
                $this->send_email($user);
            }

            $user->set_active(intval($values['active'][User :: PROPERTY_ACTIVE]));
            $user->set_registration_date(time());

            $value = $user->create();

            foreach ($values['rights_templates'] as $rights_template_id)
            {
                $user->add_rights_template_link($rights_template_id);
            }

            if ($value)
            {
                Events :: trigger_event('create', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->form_user->get_id()));
            }

            return $value;
        }
        else
        {
            return - 1; // Username not available
        }

    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $user = $this->user;
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $expiration_date = $user->get_expiration_date();
            if ($expiration_date != 0)
            {
                $defaults['ExpirationDate' . self :: PARAM_FOREVER] = 0;
                $defaults['ExpirationDatefrom_date'] = $user->get_activation_date();
                $defaults['ExpirationDateto_date'] = $user->get_expiration_date();
            }
            else
            {
                $defaults['ExpirationDate' . self :: PARAM_FOREVER] = 1;
            }

            $defaults['pw']['pass'] = 2;
            $defaults[User :: PROPERTY_DATABASE_QUOTA] = $user->get_database_quota();
            $defaults[User :: PROPERTY_DISK_QUOTA] = $user->get_disk_quota();
            $defaults[User :: PROPERTY_VERSION_QUOTA] = $user->get_version_quota();
        }
        else
        {
            $defaults['ExpirationDate' . self :: PARAM_FOREVER] = 1;

            //$defaults['from_date'] = time();
            //echo Utilities :: to_db_date(strtotime('+ ' . intval(PlatformSetting :: get('days_valid', 'user')) . 'Days', time()));
            $defaults['ExpirationDate' . 'to_date'] = Utilities :: to_db_date(strtotime('+ ' . intval(PlatformSetting :: get('days_valid', 'user')) . 'Days', time()));
            $defaults['pw']['pass'] = $user->get_password();

            $defaults[User :: PROPERTY_DATABASE_QUOTA] = '300';
            $defaults[User :: PROPERTY_DISK_QUOTA] = '209715200';
            $defaults[User :: PROPERTY_VERSION_QUOTA] = '20';
        }

        $defaults['admin'][User :: PROPERTY_PLATFORMADMIN] = $user->get_platformadmin();
        $defaults['mail']['send_mail'] = 1;
        $defaults[User :: PROPERTY_ID] = $user->get_id();
        $defaults[User :: PROPERTY_LASTNAME] = $user->get_lastname();
        $defaults[User :: PROPERTY_FIRSTNAME] = $user->get_firstname();
        $defaults[User :: PROPERTY_EMAIL] = $user->get_email();
        $defaults[User :: PROPERTY_USERNAME] = $user->get_username();
        $defaults[User :: PROPERTY_EXPIRATION_DATE] = $user->get_expiration_date();
        $defaults[User :: PROPERTY_ACTIVATION_DATE] = $user->get_activation_date();
        $defaults[User :: PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $defaults[User :: PROPERTY_PICTURE_URI] = $user->get_picture_uri();
        $defaults[User :: PROPERTY_PHONE] = $user->get_phone();
        $defaults[User :: PROPERTY_STATUS] = $user->get_status();
        $defaults['active'][User :: PROPERTY_ACTIVE] = ! is_null($user->get_active()) ? $user->get_active() : 1;

        parent :: setDefaults($defaults);
    }

    /**
     * Sends an email to the updated/new user
     */
    function send_email($user)
    {
        $options = array();
        $options['firstname'] = $user->get_firstname();
        $options['lastname'] = $user->get_lastname();
        $options['username'] = $user->get_username();
        $options['password'] = $this->unencryptedpass;
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