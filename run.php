<?php
  
/**
 * $Id: run.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application
 *
 * This script will load the requested application and call its run() function.
 */
require_once dirname(__FILE__) . '/common/global.inc.php';

$application_name = Request :: get('application');
$this_section = $application_name;

// If application path doesn't exist, block the user
if (! WebApplication :: is_active($application_name))
{
    Display :: not_allowed();
}

require_once Path :: get_application_path() . 'lib/weblcms/tool/assessment/assessment_tool.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (! Authentication :: is_valid() && ! Request :: get(AssessmentTool :: PARAM_INVITATION_ID))
{
    Display :: not_allowed();
}

// Load the current user
$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());

// Load & run the application
try
{
    $application = WebApplication :: factory($application_name, $user);
    $application->set_parameter('application', $application_name);
    $application->run();
}
catch (Exception $exception)
{
    $application->display_header();
    Display :: error_message($exception->getMessage());
    $application->display_footer();
}
?>
