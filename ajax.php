<?php
use common\libraries\Request;
use common\libraries\Utilities;
use common\libraries\Authentication;
use common\libraries\Session;
use common\libraries\JsonAjaxResult;
use common\libraries\AjaxManager;

use user\UserDataManager;

/**
 * This script will load the ajax request and return a JSON encoded result.
 * @author Hans De Bisschop
 */

try
{
    require_once dirname(__FILE__) . '/common/global.inc.php';

    /* Users have to be authenticated at all times */
    if (! Authentication :: is_valid())
    {
        JsonAjaxResult :: not_allowed();
    }

    $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());

    /* Determine the context and method to be invoked */
    $context = Request :: post('context');
    $method = Request :: post('method');

    /* If no context or method were set, return an error */
    if (! isset($context) || ! isset($method))
    {
        JsonAjaxResult :: bad_request();
    }

    /*
     * Launch the Ajax component, based on a context and method.
     * Context is used to determine the path, method to determine
     * the actual class that needs to be instantiated
     */
    try
    {
        AjaxManager :: launch($user, $context, $method);
    }
    catch (Exception $exception)
    {
        JsonAjaxResult :: general_error();
    }

}
catch (Exception $exception)
{
    JsonAjaxResult :: general_error();
}
?>