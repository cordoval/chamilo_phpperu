<?php
namespace admin;

use common\libraries\ActionBarRenderer;

class ThemeRegistrationDisplay extends RegistrationDisplay
{

    function get_action_bar()
    {
        return new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
    }

}
?>