<?php
require_once dirname(__FILE__) . '/../../../common/global.inc.php';
require_once dirname(__FILE__) . '/streaming_media_browser.class.php';

$application_name = 'repository';
$this_section = $application_name;

Utilities :: set_application($this_section);

if (! Authentication :: is_valid())
{
    Display :: not_allowed();
}

// Load the current user
$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
$browser = new StreamingMediaBrowser($user);
$browser->run();

?>