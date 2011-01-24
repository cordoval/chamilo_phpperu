<?php
namespace rights;

use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Application;

/**
 * $Id: creator.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.type_template_manager.component
 */

class TypeTemplateManagerEditorComponent extends TypeTemplateManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
            exit();
        }

        $type_template_id = $this->get_parameter(self :: PARAM_TYPE_TEMPLATE_ID);
        $type_template = RightsDataManager :: get_instance()->retrieve_type_template($type_template_id);

        $form = new TypeTemplateForm(TypeTemplateForm :: TYPE_EDIT, $type_template, $this->get_url());

        if ($form->validate())
        {
            $success = $form->update_type_template();
            $this->redirect(Translation :: get($success ? 'TypeTemplateEdited' : 'TypeTemplateNotEdited'), ($success ? false : true),
                    array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_TYPE_TEMPLATES, TypeTemplateManager :: PARAM_TYPE_TEMPLATE_ACTION => TypeTemplateManager :: ACTION_BROWSE_TYPE_TEMPLATES));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_TYPE_TEMPLATES,
    											TypeTemplateManager :: PARAM_TYPE_TEMPLATE_ACTION => TypeTemplateManager :: ACTION_BROWSE_TYPE_TEMPLATES)),
    										 Translation :: get('TypeTemplateManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('rights_type_templates_editor');
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_TYPE_TEMPLATE_ID);
    }
}
?>