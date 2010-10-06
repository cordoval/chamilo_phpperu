<?php
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
            $json_result['message'] = Translation :: get('ColumnDeleted');
        }
        else
        {
            $json_result['success'] = '0';
            $json_result['message'] = Translation :: get('ColumnNotDeleted');
        }
    }
    else
    {
        $json_result['success'] = '0';
        $json_result['message'] = Translation :: get('ColumnNotDeleted');
    }
}
else
{
    $json_result['success'] = '0';
    $json_result['message'] = Translation :: get('NotAuthorized');
}

// Return a JSON object
echo json_encode($json_result);
?>