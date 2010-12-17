<?php
namespace application\package;

use common\libraries\WebApplication;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\ToolbarItem;
use common\libraries\ConditionProperty;
use common\libraries\Application;
use common\libraries\Utilities;
/**
 * @package application.package.package.component
 */
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/package_browser/package_browser_table.class.php';

/**
 * package component which allows the user to browse his package_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class AuthorManagerBrowserComponent extends PackageInstanceManager
{
    private $action_bar;

    function run()
    {
        echo('test');
    }
}
?>