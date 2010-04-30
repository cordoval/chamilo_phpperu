<?php
require_once dirname(__FILE__) . '/../global.inc.php';

$application_name = Request :: get('application');
$this_section = $application_name;

Utilities :: set_application($this_section);

if (! Authentication :: is_valid())
{
    Display :: not_allowed();
}

// Load the current user
$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());

$application = LauncherApplication :: factory($application_name, $user);
$application->set_parameter('application', $application_name);

try
{
    $application->run();
}
catch (Exception $exception)
{
    Display :: header(new BreadcrumbTrail());
    Display :: error_message($exception->getMessage());
    Display :: footer();
}
?>