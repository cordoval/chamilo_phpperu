<?php
namespace home;
use common\libraries\Translation;
use common\libraries\Session;
use common\libraries\Authentication;
use common\libraries\PlatformSetting;
use common\libraries\Utilities;
/**
 * $Id: block_move.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
require_once dirname(__FILE__) . '/../../../common/global.inc.php';

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $column_id = $_POST['column'];
    $block_id = $_POST['block'];

    $hdm = HomeDataManager :: get_instance();

    $block = $hdm->retrieve_home_block($block_id);
    $block->set_column($column_id);
    if ($block->update())
    {
        $json_result['success'] = '1';
        $json_result['message'] = translate('BlockMovedToTab');
    }
    else
    {
        $json_result['success'] = '0';
        $json_result['message'] = translate('BlockNotMovedToTab');
    }
}
else
{
    $json_result['success'] = '0';
    $json_result['message'] = Translation :: get('NotAllowed', null, Utilities :: COMMON_LIBRARIES);
}

// Return a JSON object
echo json_encode($json_result);

function translate($variable)
{
    return Translation :: get($variable, null, __NAMESPACE__);
}
?>