<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\ActionBarRenderer;
use common\libraries\Request;
use common\libraries\ToolbarItem;
use common\libraries\Theme;


/**
 * @package application.package.package.component
 */

require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/variable_browser/variable_browser_table.class.php';

/**
 * package component which allows the user to browse his variables
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerVariablesBrowserComponent extends PackageManager
{

	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		//$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseVariables')));

		$can_edit = PackageRights :: is_allowed(PackageRights :: EDIT_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');
		$can_delete = PackageRights :: is_allowed(PackageRights :: DELETE_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');
		$can_add = PackageRights :: is_allowed(PackageRights :: ADD_RIGHT, PackageRights :: LOCATION_VARIABLES, 'manager');

		if (!$can_edit && !$can_delete && !$can_add)
		{
		    Display :: not_allowed();
		}

		$this->display_header($trail);

        echo '<a name="top"></a>';
        echo $this->get_action_bar_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->get_table();
        echo '</div>';
		$this->display_footer();
	}

	function get_table()
	{
		$table = new VariableBrowserTable($this, array(Application :: PARAM_APPLICATION => 'package', Application :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_VARIABLES), null);
		return $table->as_html();
	}

    function get_action_bar_html()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $package_language_id = Request :: get(PackageManager :: PARAM_PACKAGE_LANGUAGE);
        $language_pack = $this->retrieve_package_language(Request :: get(PackageManager :: PARAM_LANGUAGE_PACK));

    	if($this->can_language_pack_be_locked($language_pack, $package_language_id))
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Lock'), Theme :: get_common_image_path() . 'action_lock.png',
				$this->get_lock_language_pack_url($language_pack, $package_language_id)));
        }
        else
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('LockNa'), Theme :: get_common_image_path() . 'action_lock_na.png'));
        }

        if($this->can_language_pack_be_unlocked($language_pack, $package_language_id))
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Unlock'), Theme :: get_common_image_path() . 'action_unlock.png',
				$this->get_unlock_language_pack_url($language_pack, $package_language_id)));
        }
        else
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('UnlockNa'), Theme :: get_common_image_path() . 'action_unlock_na.png'));
        }

        return $action_bar->as_html();
    }
    
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_package_languages_url(), Translation :: get('PackageManagerPackageLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_LANGUAGE_PACKS, PackageManager :: PARAM_PACKAGE_LANGUAGE => Request :: get(self :: PARAM_PACKAGE_LANGUAGE))), Translation :: get('PackageManagerLanguagePacksBrowserComponent')));
    	$breadcrumbtrail->add_help('package_variables_browser');
    }
    
    function get_additional_parameters()
    {
    	return array(PackageManager :: PARAM_LANGUAGE_PACK);
    }
}
?>