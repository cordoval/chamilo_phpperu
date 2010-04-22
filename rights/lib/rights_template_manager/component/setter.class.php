<?php
/**
 * $Id: setter.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component
 */

class RightsTemplateManagerSetterComponent extends RightsTemplateManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $rights_template = Request :: get('rights_template_id');
        $right = Request :: get('right_id');
        $location_id = Request :: get(RightsTemplateManager :: PARAM_LOCATION);
        $location = $this->retrieve_location($location_id);
        
        if (isset($rights_template) && isset($right) && isset($location))
        {
            $success = RightsUtilities :: invert_rights_template_right_location($right, $rights_template, $location->get_id());
            
            if ($location->get_parent() == 0)
            {
                $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_SOURCE => $location->get_application(), RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID => $rights_template));
            }
            else
            {
                $this->redirect(Translation :: get($success == true ? 'RightUpdated' : 'RightUpdateFailed'), ($success == true ? false : true), array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ACTION => RightsTemplateManager :: ACTION_CONFIGURE_RIGHTS_TEMPLATES, RightsTemplateManager :: PARAM_SOURCE => $location->get_application(), RightsTemplateManager :: PARAM_LOCATION => $location->get_id(), RightsTemplateManager :: PARAM_RIGHTS_TEMPLATE_ID => $rights_template));
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }
}
?>