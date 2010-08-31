<?php
/**
 * This script will load the requested core application and launch it.
 */

try
{
    require_once dirname(__FILE__) . '/common/global.inc.php';

    $application_name = Request :: get('application');
    $this_section = $application_name;

    // If application path doesn't exist, block the user
    if (! CoreApplication :: is_active($application_name))
    {
        Display :: not_allowed();
    }

    Utilities :: set_application($this_section);

    if (! Authentication :: is_valid())
    {
        Display :: not_allowed();
    }

    // Load the current user for every application but the UserManager itself
    if (! (Request :: get('application') == 'user'))
    {
        $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
    }
    else
    {
        if (! Session :: get_user_id())
        {
            $user = null;
        }
        else
        {
            $user = Session :: get_user_id();
        }
    }

    // Load & run the application
    try
    {
        Application :: launch($application_name, $user);
    }
    catch (Exception $exception)
    {
        Display :: error_page($exception->getMessage());
    }
}
catch (Exception $exception)
{
    Display :: error_message($exception->getMessage());
}
?>