<?php
/**
 * $Id: updater.class.php 226 2009-11-13 14:44:03Z chellee $
 * @package help.lib.help_manager.component
 */

require_once dirname(__FILE__) . '/../../help_rights.class.php';

class HelpManagerUpdaterComponent extends HelpManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: Get(HelpManager :: PARAM_HELP_ITEM);
        if ($id)
        {
            $help_item = $this->retrieve_help_item($id);
            
            if (! HelpRights :: is_allowed_in_help_subtree(HelpRights :: RIGHT_EDIT, Request :: Get(HelpManager :: PARAM_HELP_ITEM)))
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
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HelpManager :: ACTION_BROWSE_HELP_ITEMS)), Translation :: get('HelpManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('help_updater');
    }
    
    function get_additional_parameters()
    {
    	return array(HelpManager :: PARAM_HELP_ITEM);
    }
}
?>