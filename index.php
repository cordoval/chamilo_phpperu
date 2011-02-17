<?php
use common\libraries\Utilities;
use common\libraries\Session;
use common\libraries\Display;
use common\libraries\Authentication;
use common\libraries\Request;

use user\UserDataManager;
use home\HomeRenderer;

try
{
    $this_section = 'home';

    include_once ('common/global.inc.php');

    Utilities :: set_application($this_section);

    if (Authentication :: is_valid())
    {
        $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
    }
    else
    {
        $user = null;
    }


    $view = Request::get(HomeRenderer::PARAM_VIEW_TYPE, HomeRenderer :: TYPE_DEFAULT);

    echo HomeRenderer :: as_html($view, $user);
}
catch (Exception $exception)
{
    Display :: error_message($exception->getMessage());
}
?>