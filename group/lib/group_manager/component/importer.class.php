<?php
require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: importer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerImporterComponent extends GroupManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => GroupManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Group')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('GroupCreateCsv')));
        $trail->add_help('group importer');
        
       if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::CREATE_RIGHT, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $form = new GroupImportForm($this->get_url());
        
        if ($form->validate())
        {
            $success = $form->import_groups();
            $this->redirect(Translation :: get($success ? 'GroupXMLProcessed' : 'GroupXMLNotProcessed') . '<br />' . $form->get_failed_elements(), ($success ? false : true), array(Application :: PARAM_ACTION => GroupManager :: ACTION_IMPORT));
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
        $html[] = '<p>' . Translation :: get('XMLMustLookLike') . ' (' . Translation :: get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;';
        $html[] = '&lt;groups&gt;';
        $html[] = '    &lt;item&gt;';
        $html[] = '        <b>&lt;action&gt;A/U/D&lt;/action&gt;</b>';
        $html[] = '        <b>&lt;name&gt;xxx&lt;/name&gt;</b>';
        $html[] = '        <b>&lt;code&gt;xxx&lt;/code&gt;</b>';
        $html[] = '        &lt;description&gt;xxx&lt;/description&gt;';
        $html[] = '        &lt;children&gt;';
        $html[] = '            &lt;item&gt;';
        $html[] = '                <b>&lt;action&gt;A/U/D&lt;/action&gt;</b>';
        $html[] = '                <b>&lt;name&gt;xxx&lt;/name&gt;</b>';
        $html[] = '                <b>&lt;code&gt;xxx&lt;/code&gt;</b>';
        $html[] = '                &lt;description&gt;xxx&lt;/description&gt;';
        $html[] = '                &lt;children&gt;xxx&lt;/children&gt;';
        $html[] = '            &lt;/item&gt;';
        $html[] = '        &lt;/children&gt;';
        $html[] = '    &lt;/item&gt;';
        $html[] = '&lt;/groups&gt;';
        $html[] = '</pre>';
        $html[] = '</blockquote>';
        $html[] = '<p>' . Translation :: get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation :: get('Action') . '</u></b>';
        $html[] = '<br />A: ' . Translation :: get('Add');
        $html[] = '<br />U: ' . Translation :: get('Update');
        $html[] = '<br />D: ' . Translation :: get('Delete');
        $html[] = '</blockquote>';
        
        echo implode($html, "\n");
    }
}
?>