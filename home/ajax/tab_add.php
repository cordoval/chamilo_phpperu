<?php
/**
 * $Id: tab_add.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
$this_section = 'home';

require_once dirname(__FILE__) . '/../../common/global.inc.php';
Utilities :: set_application($this_section);

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();

    $tab = new HomeTab();
    $tab->set_title(Translation :: get('NewTab'));
    $tab->set_user($user_id);
    if (! $tab->create())
    {
        $json_result['success'] = '0';
        $json_result['message'] = Translation :: get('TabNotAdded');
    }

    $row = new HomeRow();
    $row->set_title(Translation :: get('NewRow'));
    $row->set_tab($tab->get_id());
    $row->set_user($user_id);
    if (! $row->create())
    {
        $json_result['success'] = '0';
        $json_result['message'] = Translation :: get('TabRowNotAdded');
    }

    $column = new HomeColumn();
    $column->set_row($row->get_id());
    $column->set_title(Translation :: get('NewColumn'));
    $column->set_sort('1');
    $column->set_width('100');
    $column->set_user($user_id);
    if (! $column->create())
    {
        $json_result['success'] = '0';
        $json_result['message'] = Translation :: get('TabColumnNotAdded');
    }

    $block = new HomeBlock();
    $block->set_column($column->get_id());
    $block->set_title(Translation :: get('DummyBlock'));
    $block->set_application('repository');
    $block->set_component('dummy');
    $block->set_visibility('1');
    $block->set_user($user_id);
    if (! $block->create())
    {
        $json_result['success'] = '0';
        $json_result['message'] = Translation :: get('TabBlockNotAdded');
    }

    	$user = UserDataManager :: get_instance()->retrieve_user($user_id);
//    $usermgr = new UserManager($user_id);
//    $user = $usermgr->get_user();
    

    $application = $block->get_application();
    $application_class = Application :: application_to_class($application);

    if (! WebApplication :: is_application($application))
    {
        $path = Path :: get(SYS_PATH) . $application . '/lib/' . $application . '_manager' . '/' . $application . '_manager.class.php';
        require_once $path;
        $application_class .= 'Manager';
        $app = new $application_class($user);
    }
    else
    {
        $path = Path :: get_application_path() . 'lib' . '/' . $application . '/' . $application . '_manager' . '/' . $application . '_manager.class.php';
        require_once $path;
        $app = Application :: factory($application, $user);
    }

    $html[] = '<div class="tab" id="tab_' . $tab->get_id() . '" style="display: none;">';
    $html[] = '<div class="row" id="row_' . $row->get_id() . '">';
    $html[] = '<div class="column" id="column_' . $column->get_id() . '" style="width: ' . $column->get_width() . '%;">';
    $html[] = $app->render_block($block);
    $html[] = '</div>';
    $html[] = '</div>';
    $html[] = '</div>';

    $title = array();
    $title[] = '<li class="normal" id="tab_select_' . $tab->get_id() . '">';
    $title[] = '<a class="tabTitle" href="#">' . $tab->get_title() . '</a>';
    $title[] = '<a class="deleteTab"><img src="' . Theme :: get_image_path() . 'action_delete_tab.png" /></a>';
    $title[] = '</li>';

    $json_result['html'] = implode("\n", $html);
    $json_result['title'] = implode("\n", $title);
    $json_result['success'] = '1';
    $json_result['message'] = Translation :: get('TabAdded');
}
else
{
    $json_result['success'] = '0';
    $json_result['message'] = Translation :: get('NotAuthorized');
}

echo json_encode($json_result);
?>