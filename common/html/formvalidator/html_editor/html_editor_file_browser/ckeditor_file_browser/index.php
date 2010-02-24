<?php
require_once dirname(__FILE__) . '/../../../../global.inc.php';
require_once dirname(__FILE__) . '/ckeditor_file_browser.class.php';

$application_name = 'repository';
$this_section = $application_name;

Utilities :: set_application($this_section);

if (! Authentication :: is_valid() && ! Request :: get(AssessmentTool :: PARAM_INVITATION_ID))
{
    Display :: not_allowed();
}

Display :: small_header();
$browser = new CkeditorFileBrowser();
$browser->run();
Display :: small_footer();
?>