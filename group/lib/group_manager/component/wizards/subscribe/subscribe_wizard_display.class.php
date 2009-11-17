<?php
/**
 * $Id: subscribe_wizard_display.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package groups.lib.group_manager.component.wizards.subscribe
 */
/**
 * This class provides the needed functionality to show a page in a maintenance
 * wizard.
 */

class SubscribeWizardDisplay extends HTML_QuickForm_Action_Display
{
    /**
     * The repository tool in which the wizard runs
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs
     */
    public function SubscribeWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->parent->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_BROWSE_GROUPS)), Translation :: get('GroupList')));
        
        $group_id = Request :: get(GroupManager :: PARAM_GROUP_ID);
        
        if (isset($group_id))
        {
            $group = $this->parent->retrieve_group($group_id);
            $trail->add(new Breadcrumb($this->parent->get_url(array(Application :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $group_id)), $group->get_name()));
        }
        
        $trail->add(new Breadcrumb($this->parent->get_url(), Translation :: get('SubscribeUsersToGroup')));
        
        $this->parent->display_header($trail, false, 'group subscribe users');
        if (isset($_SESSION['subscribe_message']))
        {
            Display :: normal_message($_SESSION['subscribe_message']);
            unset($_SESSION['subscribe_message']);
        }
        if (isset($_SESSION['subscribe_error_message']))
        {
            Display :: error_message($_SESSION['subscribe_error_message']);
            unset($_SESSION['subscribe_error_message']);
        }
        parent :: _renderForm($current_page);
        $this->parent->display_footer();
    }
}
?>