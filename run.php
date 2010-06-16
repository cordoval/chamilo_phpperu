<?php
/**
 * $Id: run.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application
 *
 * This script will load the requested application and call its run() function.
 */

try
{
	require_once dirname(__FILE__) . '/common/global.inc.php';

	$application_name = Request :: get('application');
	$this_section = $application_name;

	// If application path doesn't exist, block the user
	if (! WebApplication :: is_active($application_name))
	{
	    Display :: not_allowed();
	}

//	require_once Path :: get_application_path() . 'lib/weblcms/tool/assessment/assessment_tool.class.php';

	Utilities :: set_application($this_section);

//	if (! Authentication :: is_valid() && ! Request :: get(AssessmentTool :: PARAM_INVITATION_ID))
	if (! Authentication :: is_valid())
	{
	    Display :: not_allowed();
	}

	// Load the current user
	$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());

	// Load & run the application
    $application = WebApplication :: factory($application_name, $user);
    $application->set_parameter('application', $application_name);

	try
	{
	    $application->run();
	}
	catch (Exception $exception)
	{
	    Display :: header(BreadcrumbTrail :: get_instance());
	    Display :: error_message($exception->getMessage());
	    Display :: footer();
	}
}
catch(Exception $exception)
{
	Display :: error_message($exception->getMessage());
}
?>