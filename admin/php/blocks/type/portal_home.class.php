<?php
namespace admin;

use common\libraries\Translation;
use common\libraries\CoreApplication;
use common\libraries\PlatformSetting;

/**
 * @package admin.block
 * $Id: portal_home.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 */
require_once CoreApplication :: get_application_class_path('admin') . 'blocks/admin_block.class.php';

class AdminPortalHome extends AdminBlock
{

    function is_editable()
    {
        return true;
    }

    function is_hidable()
    {
        return false;
    }

    function is_deletable()
    {
        return false;
    }

    function display_content()
    {
        $html = PlatformSetting :: get('portal_home');
        $html = $html ? $html : Translation :: get('ConfigurePortalHomeFirst');
        return $html;
    }

}
?>