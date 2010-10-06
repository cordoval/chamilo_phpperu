<?php
try
{
    $this_section = 'home';

    include_once ('common/global.inc.php');

    Utilities :: set_application($this_section);

    if (Session :: get_user_id())
    {
        $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
    }
    else
    {
        $user = null;
    }

    echo HomeRenderer :: as_html(HomeRenderer :: TYPE_DEFAULT, $user);
}
catch (Exception $exception)
{
    Display :: error_message($exception->getMessage());
}
?>