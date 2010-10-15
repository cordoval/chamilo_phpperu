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

    function as_html()
    {
        $html[] = $this->display_header();

        /*$object_id = PlatformSetting :: get('portal_home');

        if (! isset($object_id) || $object_id == 0)
        {
            $html[] = Translation :: get('ConfigureBlockFirst');
        }
        else
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
            $html[] = $content_object->get_description();
        }*/

        $portal_home = PlatformSetting :: get('portal_home');
        if ($portal_home == '')
        {
            $html[] = Translation :: get('ConfigurePortalHomeFirst');
        }
        else
        {
            $html[] = $portal_home;
        }

        $html[] = $this->display_footer();
        return implode("\n", $html);
    }

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

}
?>