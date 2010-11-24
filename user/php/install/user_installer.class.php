<?php
namespace user;

use common\libraries\Translation;
use common\libraries\Hashing;
use common\libraries\Installer;
use common\libraries\LocalSetting;

/**
 * $Id: user_installer.class.php 187 2009-11-13 10:31:25Z vanpouckesven $
 * @package user.install
 */
/**
 * This installer can be used to create the storage structure for the
 * users application.
 */
class UserInstaller extends Installer
{

    /**
     * Constructor
     */
    function __construct($values)
    {
        parent :: __construct($values, UserDataManager :: get_instance());
    }

    /**
     * Runs the install-script.
     */
    function install_extra()
    {
    	if (!UserRights :: create_user_subtree_root_location())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('UsersSubtreeCreated'));
        }

    	if (! $this->create_anonymous_user())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('AnonymousAccountCreated'));
        }

        if (! $this->create_admin_account())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('AdminAccountCreated'));
        }

        if (! $this->create_test_user_account())
        {
            return false;
        }
        else
        {
            $this->add_message(self :: TYPE_NORMAL, Translation :: get('TestUserAccountCreated'));
        }

        return true;
    }

    function create_admin_account()
    {
        $values = $this->get_form_values();

        $user = new User();
        $user->set_lastname($values['admin_surname']);
        $user->set_firstname($values['admin_firstname']);
        $user->set_username($values['admin_username']);
        $user->set_password(Hashing :: hash($values['admin_password']));
        $user->set_auth_source('platform');
        $user->set_email($values['admin_email']);
        $user->set_status('1');
        $user->set_platformadmin('1');
        $user->set_official_code('ADMIN');
        $user->set_phone($values['admin_phone']);
        $user->set_disk_quota('209715200');
        $user->set_database_quota('300');
        $user->set_version_quota('20');
        $user->set_expiration_date(0);

        if (! $user->create())
        {
            return false;
        }
        else
        {
            return true;
        }

    }

    function create_anonymous_user()
    {
        $values = $this->get_form_values();

        $user = new User();
        $user->set_lastname(Translation :: get('Anonymous'));
        $user->set_firstname(Translation :: get('Mr'));
        $user->set_username('anonymous');
        $user->set_password(Hashing :: hash($values['admin_password']));
        $user->set_auth_source('platform');
        $user->set_email($values['admin_email']);
        $user->set_status('1');
        $user->set_platformadmin('0');
        $user->set_official_code('ANONYMOUS');
        $user->set_phone($values['admin_phone']);
        $user->set_disk_quota('0');
        $user->set_database_quota('0');
        $user->set_version_quota('0');
        $user->set_expiration_date(0);

        if (! $user->create())
        {
            return false;
        }
        else
        {
            $user->set_id('0');
            if (! $user->update())
            {
                return false;
            }
            else
            {
                return true;
            }
        }
    }

    function create_test_user_account()
    {
        $values = $this->get_form_values();

        $user = new User();
        $user->set_lastname('Doe');
        $user->set_firstname('John');
        $user->set_username('JohnDoe');
        $user->set_password(Hashing :: hash('JohnDoe'));
        $user->set_auth_source('platform');
        $user->set_email('john.doe@nowhere.org');
        $user->set_status('1');
        $user->set_platformadmin('0');
        $user->set_official_code('TEST_USER');
        $user->set_disk_quota('209715200');
        $user->set_database_quota('300');
        $user->set_version_quota('20');
        $user->set_expiration_date(0);

        if (! $user->create())
        {
            return false;
        }
        else
        {
        	LocalSetting :: create_local_setting('platform_language', 'nl', 'admin', $user->get_id());
            return true;
        }

    }

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>