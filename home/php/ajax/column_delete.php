<?php
namespace home;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Session;
use common\libraries\Authentication;
use common\libraries\PlatformSetting;
/**
 * $Id: column_delete.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
require_once dirname(__FILE__) . '/../../../common/global.inc.php';

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $column_id = $_POST['column'];

    $hdm = HomeDataManager :: get_instance();

    $column = $hdm->retrieve_home_column($column_id);

    if ($column->get_user() == $user_id && $column->is_empty())
    {
        if ($column->delete())
        {
            $json_result['success'] = '1';
            $json_result['message'] = translate('ColumnDeleted');
        }
        else
        {
            $json_result['success'] = '0';
            $json_result['message'] = translate('ColumnNotDeleted');
        }
    }
    else
    {
        $json_result['success'] = '0';
        $json_result['message'] = translate('ColumnNotDeleted');
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