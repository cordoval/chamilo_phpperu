<?php
namespace user;

use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\BreadcrumbTrail;

use common\libraries\AdministrationComponent;

/**
 * $Id: exporter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerExporterComponent extends UserManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (!UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, 0))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        $form = new UserExportForm(UserExportForm :: TYPE_EXPORT, $this->get_url());

        if ($form->validate())
        {
            $export = $form->exportValues();
            $file_type = $export['file_type'];
            $result = parent :: retrieve_users();
            while ($user = $result->next_result())
            {
            	if($file_type == 'pdf')
            	{
            		$user_array = $this->prepare_for_pdf_export($user);
            	}
            	else
            	{
            		$user_array = $this->prepare_for_other_export($user);

            	}

                Event :: trigger('export', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
                $data[] = $user_array;
            }
            $this->export_users($file_type, $data);
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

	function prepare_for_pdf_export($user)
    {
        $lastname_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_LASTNAME));
        $firstname_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_FIRSTNAME));
        $username_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_USERNAME));
        $email_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_EMAIL));
        $language_title = Translation :: get(Utilities :: underscores_to_camelcase('language'));
        $status_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_STATUS));
        $active_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_ACTIVE));
        $official_code_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_OFFICIAL_CODE));
        $phone_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_PHONE));
        $activation_date_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_ACTIVATION_DATE));
        $expiration_date_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_EXPIRATION_DATE));
        $auth_source_title = Translation :: get(Utilities :: underscores_to_camelcase(User :: PROPERTY_AUTH_SOURCE));

    	$user_array[$lastname_title] = $user->get_lastname();
        $user_array[$firstname_title] = $user->get_firstname();
        $user_array[$username_title] = $user->get_username();
        $user_array[$email_title] = $user->get_email();
        $user_array[$language_title] = LocalSetting :: get('platform_language');
        $user_array[$status_title] = $user->get_status();
        $user_array[$active_title] = $user->get_active();
        $user_array[$official_code_title] = $user->get_official_code();
        $user_array[$phone_title] = $user->get_phone();

        $act_date = $user->get_activation_date();

        $user_array[$activation_date_title] = $act_date;

        $exp_date = $user->get_expiration_date();

        $user_array[$expiration_date_title] = $exp_date;

        $user_array[$auth_source_title] = $user->get_auth_source();

        return $user_array;
    }

    function prepare_for_other_export($user)
    {
    	//$user_array[User::PROPERTY_USER_ID] = $user->get_id();
        $user_array[User :: PROPERTY_LASTNAME] = $user->get_lastname();
        $user_array[User :: PROPERTY_FIRSTNAME] = $user->get_firstname();
        $user_array[User :: PROPERTY_USERNAME] = $user->get_username();
        $user_array[User :: PROPERTY_EMAIL] = $user->get_email();
        $user_array['language'] = LocalSetting :: get('platform_language');
        $user_array[User :: PROPERTY_STATUS] = $user->get_status();
        $user_array[User :: PROPERTY_ACTIVE] = $user->get_active();
        $user_array[User :: PROPERTY_OFFICIAL_CODE] = $user->get_official_code();
        $user_array[User :: PROPERTY_PHONE] = $user->get_phone();

        $act_date = $user->get_activation_date();

        $user_array[User :: PROPERTY_ACTIVATION_DATE] = $act_date;

        $exp_date = $user->get_expiration_date();

        $user_array[User :: PROPERTY_EXPIRATION_DATE] = $exp_date;

        $user_array[User :: PROPERTY_AUTH_SOURCE] = $user->get_auth_source();

        return $user_array;
    }

    function export_users($file_type, $data)
    {
        $filename = 'export_users_' . date('Y-m-d_H-i-s');
    	if ($file_type == 'pdf')
        {
            $data = array(array('key' => 'users', 'data' => $data));
        }
        $export = Export :: factory($file_type, $data);
        $export->set_filename($filename);
        $export->send_to_browser();
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('user_exporter');
    }

}
?>