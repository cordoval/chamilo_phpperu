<?php
$this_section = 'home';

include_once ('../../common/global.inc.php');

Utilities :: set_application($this_section);

if (Session :: get_user_id())
{
    $usermgr = new UserManager($_SESSION['_uid']);
    $user = $usermgr->get_user();
}
else
{
    $user = null;
}

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once Path :: get_library_path(). 'html/layout/chamilo_template.class.php';

$template = ChamiloTemplate :: get_instance();

// Assign index specific vars
$template->assign_vars(array(
	'TEST'	=> 'Scara',
    'MESSAGE' => 'PHP 5 introduces the E_STRICT error reporting constant. The PHP manual states "Enable to have PHP suggest changes to your code which will ensure the best interoperability and forward compatibility of your code.". Thus it\'s recommendable to enable strict PHP errors.')
);

$template->set_filenames(array(
	'body' => 'test.tpl')
);

$template->display('body');

//dump($template);
?>