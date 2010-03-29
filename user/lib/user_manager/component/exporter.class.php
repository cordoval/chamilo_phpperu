<?php
/**
 * $Id: exporter.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerExporterComponent extends UserManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => UserManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Users')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserCreateExport')));
        $trail->add_help('user general');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header($trail);
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
            	
                Events :: trigger_event('export', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
                $data[] = $user_array;
            }
            $this->export_users($file_type, $data);
        }
        else
        {
            $this->display_header($trail);
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
        if ($act_date != 0)
            $act_date = Utilities :: to_db_date($act_date);
                
        $user_array[$activation_date_title] = $act_date;
               
        $exp_date = $user->get_expiration_date();
        if ($exp_date != 0)
            $exp_date = Utilities :: to_db_date($exp_date);
              
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
        if ($act_date != 0)
            $act_date = Utilities :: to_db_date($act_date);
                
        $user_array[User :: PROPERTY_ACTIVATION_DATE] = $act_date;
               
        $exp_date = $user->get_expiration_date();
        if ($exp_date != 0)
            $exp_date = Utilities :: to_db_date($exp_date);
              
        $user_array[User :: PROPERTY_EXPIRATION_DATE] = $exp_date;
                
        $user_array[User :: PROPERTY_AUTH_SOURCE] = $user->get_auth_source();
        
        return $user_array;
    }

    function export_users($file_type, $data)
    {
        $filename = 'export_users_' . date('Y-m-d_H-i-s');
        $export = Export :: factory($file_type, $filename);
        if ($file_type == 'pdf')
            $data = array(array('key' => 'users', 'data' => $data));
        $export->write_to_file($data);
        return;
    }
}
?>