<?php
namespace home;
use common\libraries\Translation;
use common\libraries\Session;
use common\libraries\Request;
use common\libraries\Authentication;
use common\libraries\PlatformSetting;
/**
 * $Id: tab_edit.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
require_once dirname(__FILE__) . '/../../../common/global.inc.php';

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $tab = Request :: post('tab'); //$_POST['tab'];
    $title = Request :: post('title'); //$_POST['title'];

    $hdm = HomeDataManager :: get_instance();

    $tab = $hdm->retrieve_home_tab($tab);

    if ($tab->get_user() == $user_id)
    {
        $tab->set_title($title);
        if ($tab->update())
        {
            $json_result['success'] = '1';
            $json_result['message'] = Translation :: get('TabUpdated');
            $json_result['title'] = $title;
        }
        else
        {
            $json_result['success'] = '0';
            $json_result['message'] = Translation :: get('TabNotUpdated');
        }
    }
    else
    {
        $json_result['success'] = '0';
        $json_result['message'] = Translation :: get('TabNotUpdated');
    }
}
else
{
    $json_result['success'] = '0';
    $json_result['message'] = Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES);
}

// Return a JSON object
echo json_encode($json_result);
?>