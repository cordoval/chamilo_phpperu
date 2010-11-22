<?php
namespace admin;

use common\libraries\Utilities;

class RegistrationBrowser
{

    function factory($browser, $parameters, $condition)
    {
        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($browser->get_type()) . 'RegistrationBrowserTable';
        require_once dirname(__FILE__) . '/' . $browser->get_type() . '/' . $browser->get_type() . '_registration_browser_table.class.php';
        return new $class($browser, $parameters, $condition);
    }
}
?>