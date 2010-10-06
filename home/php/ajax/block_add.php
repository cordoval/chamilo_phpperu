<?php
/**
 * $Id: block_add.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
$this_section = 'home';

require_once dirname(__FILE__) . '/../../../common/global.inc.php';

Utilities :: set_application($this_section);

function unserialize_jquery($jquery)
{
    $block_data = explode('&', $jquery);
    $blocks = array();

    foreach ($block_data as $block)
    {
        $block_split = explode('=', $block);
        $blocks[] = $block_split[1];
    }

    return $blocks;
}

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $component = explode('.', $_POST['component']);
    $column_data = explode('_', $_POST['column']);
    $blocks = unserialize_jquery($_POST['order']);

    /*
	 * TODO: Make this accept input from the jQuery script, should automatically add the correct block to the homepage
	 */

    $block = new HomeBlock();
    $block->set_column($column_data[1]);
    $block->set_title(Utilities :: underscores_to_camelcase($component[1]));
    $block->set_application($component[0]);
    $block->set_component($component[1]);
    $block->set_visibility('1');
    $block->set_user($user_id);

    $block->create();
    $user = UserDataManager :: get_instance()->retrieve_user($user_id);

    $renderer = HomeRenderer :: factory(HomeRenderer :: TYPE_DEFAULT, $user);
    echo Block :: factory($renderer, $block)->as_html();
}
?>