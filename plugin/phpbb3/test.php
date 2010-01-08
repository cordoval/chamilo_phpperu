<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

function dump($var)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}

include 'phpbb3_template.php';

$user->theme = array();
$user->theme['template_path'] = 'aqua';
$user->theme['template_storedb'] = false;
$user->theme['template_inherits_id'] = false;
$user->theme['template_inherit_path'] = '';
$user->theme['template_id'] = '1';
$phpEx = 'php';

$phpbb_root_path = 'C:/wamp/www/chamilo/plugin/phpbb3/';

$template	= new template();
$template->set_template();

// Assign index specific vars
$template->assign_vars(array(
	'TEST'	=> 'Scara',
    'MESSAGE' => 'PHP 5 introduces the E_STRICT error reporting constant. The PHP manual states "Enable to have PHP suggest changes to your code which will ensure the best interoperability and forward compatibility of your code.". Thus it\'s recommendable to enable strict PHP errors.')
);

$template->set_filenames(array(
	'body' => 'test.html')
);

//echo '<pre>';
//print_r($template->_rootref);
//echo '</pre>';

$template->display('body');
?>