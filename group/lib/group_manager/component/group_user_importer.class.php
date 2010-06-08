<?php
/**
 * $Id: $
 * @author vanpouckesven
 * @package group.lib.group_manager.component
 */
 
class GroupManagerGroupUserImporterComponent extends GroupManager
{
	/**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupUserCreateCsv')));
        $trail->add_help('group user importer');
        
        if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $form = new GroupUserImportForm($this->get_url());
        
        if ($form->validate())
        {
            $success = $form->import_group_users();
            $this->redirect(Translation :: get($success ? 'GroupUserCSVProcessed' : 'GroupUserCSVNotProcessed') . '<br />' . $form->get_failed_elements(), ($success ? false : true), array(Application :: PARAM_ACTION => GroupManager :: ACTION_IMPORT_GROUP_USERS));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_extra_information();
            $this->display_footer();
        }
    }

    function display_extra_information()
    {
        $html = array();
        $html[] = '<p>' . Translation :: get('CSVMustLookLike') . ' (' . Translation :: get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '<b>action</b>;<b>group</b>;<b>username</b>';
        $html[] = 'A;Chamilo;admin';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        $html[] = '<p>' . Translation :: get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation :: get('Action') . '</u></b>';
        $html[] = '<br />A: ' . Translation :: get('Add');
        $html[] = '<br />D: ' . Translation :: get('Delete');
        $html[] = '</blockquote>';
        
        echo implode($html, "\n");
    }
}
?>