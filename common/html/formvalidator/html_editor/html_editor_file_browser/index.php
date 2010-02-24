<?php
require_once dirname(__FILE__) . '/../../../../global.inc.php';
require_once dirname(__FILE__) . '/html_editor_file_browser.class.php';

$application_name = 'repository';
$this_section = $application_name;

Utilities :: set_application($this_section);

if (! Authentication :: is_valid() && ! Request :: get(AssessmentTool :: PARAM_INVITATION_ID))
{
    Display :: not_allowed();
}

// Load the current user
$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
$browser = HtmlEditorFileBrowser :: factory(LocalSetting :: get('html_editor'), $user);

Display :: small_header();
$browser->run();
Display :: small_footer();
?>