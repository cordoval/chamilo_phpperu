<?php
namespace rights;

use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Display;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Application;

/**
 * $Id: editor.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */
require_once Path :: get_rights_path() . 'lib/rights_template_manager/component/rights_template_browser_table/rights_template_browser_table.class.php';
/**
 * Weblcms component which allows the user to manage his or her user subscriptions
 */
class RightsTemplateManagerEditorComponent extends RightsTemplateManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID);

        if ($id)
        {
            $rights_template = $this->retrieve_rights_template($id);

            if (! $this->get_user()->is_platform_admin())
            {
                $this->display_header();
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit();
            }

            $form = new RightsTemplateForm(RightsTemplateForm :: TYPE_EDIT, $rights_template, $this->get_url(array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID => $id)));

            if ($form->validate())
            {
                $success = $form->update_rights_template();
                $this->redirect(Translation :: get($success ? 'RightsTemplateUpdated' : 'RightsTemplateNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoRightsTemplateSelected')));
        }
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES,
    															  RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_BROWSE_RIGHTS_TEMPLATES)),
    										 Translation :: get('RightsTemplateManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('rights_templates_editor');
    }

	function get_additional_parameters()
    {
    	return array(RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID);
    }
}
?>