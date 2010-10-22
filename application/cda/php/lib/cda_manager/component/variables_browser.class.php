<?php

namespace application\cda;

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
 * @package application.cda.cda.component
 */

require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/variable_browser/variable_browser_table.class.php';

/**
 * cda component which allows the user to browse his variables
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerVariablesBrowserComponent extends CdaManager
{

	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseVariables')));

		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');
		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');
		$can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');

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
		$table = new VariableBrowserTable($this, array(Application :: PARAM_APPLICATION => 'cda', Application :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLES), null);
		return $table->as_html();
	}

    function get_action_bar_html()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $cda_language_id = Request :: get(CdaManager :: PARAM_CDA_LANGUAGE);
        $language_pack = $this->retrieve_cda_language(Request :: get(CdaManager :: PARAM_LANGUAGE_PACK));

    	if($this->can_language_pack_be_locked($language_pack, $cda_language_id))
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Lock'), Theme :: get_common_image_path() . 'action_lock.png',
				$this->get_lock_language_pack_url($language_pack, $cda_language_id)));
        }
        else
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('LockNa'), Theme :: get_common_image_path() . 'action_lock_na.png'));
        }

        if($this->can_language_pack_be_unlocked($language_pack, $cda_language_id))
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Unlock'), Theme :: get_common_image_path() . 'action_unlock.png',
				$this->get_unlock_language_pack_url($language_pack, $cda_language_id)));
        }
        else
        {
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('UnlockNa'), Theme :: get_common_image_path() . 'action_unlock_na.png'));
        }

        return $action_bar->as_html();
    }
    
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_browse_cda_languages_url(), Translation :: get('CdaManagerCdaLanguagesBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_LANGUAGE_PACKS, CdaManager :: PARAM_CDA_LANGUAGE => Request :: get(self :: PARAM_CDA_LANGUAGE))), Translation :: get('CdaManagerLanguagePacksBrowserComponent')));
    	$breadcrumbtrail->add_help('cda_variables_browser');
    }
    
    function get_additional_parameters()
    {
    	return array(CdaManager :: PARAM_LANGUAGE_PACK);
    }
}
?>