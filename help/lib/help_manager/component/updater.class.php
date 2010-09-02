<?php
/**
 * $Id: updater.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.help_manager.component
 */

require_once dirname(__FILE__) . '/../../help_rights.class.php';

class HelpManagerUpdaterComponent extends HelpManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => HelpManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Help')));
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HelpManager :: ACTION_BROWSE_HELP_ITEMS)), Translation :: get('HelpItemList')));
        $trail->add_help('help general');
        
        $id = Request :: Get(HelpManager :: PARAM_HELP_ITEM);
        if ($id)
        {
            $help_item = $this->retrieve_help_item($id);
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('HelpItemUpdate')));
            
            if (! HelpRights :: is_allowed_in_help_subtree(HelpRights :: EDIT_RIGHT, HelpRights :: get_location_by_identifier_from_help_subtree(Request :: Get(HelpManager :: PARAM_HELP_ITEM))))
            {
                $this->display_header();
                Display :: error_message(Translation :: get("NotAllowed"));
                $this->display_footer();
                exit();
            }
            
            $form = new HelpItemForm($help_item, $this->get_url(array(HelpManager :: PARAM_HELP_ITEM => $id)));
            
            if ($form->validate())
            {
                $success = $form->update_help_item();
                $help_item = $form->get_help_item();
                $this->redirect(Translation :: get($success ? 'HelpItemUpdated' : 'HelpItemNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => HelpManager :: ACTION_BROWSE_HELP_ITEMS));
            }
            else
            {
                $this->display_header();
                echo '<h4>' . Translation :: get('UpdateItem') . ': ' . $help_item->get_name() . '</h4>';
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoHelpItemSelected')));
        }
    }
}
?>