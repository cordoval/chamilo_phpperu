<?php
namespace group;
use common\libraries\Application;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\AdministrationComponent;
use common\libraries\BreadcrumbTrail;

require_once dirname(__FILE__) ."/../../group_rights.class.php";
/**
 * $Id: importer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class GroupManagerImporterComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
       if (!GroupRights::is_allowed_in_groups_subtree(GroupRights::RIGHT_CREATE, GroupRights::get_location_by_identifier_from_groups_subtree(Request::get(GroupManager::PARAM_GROUP_ID))))
        {
            $this->display_header();
            Display :: error_message(Translation :: get('NotAllowed', null , Utilities :: COMMON_LIBRARIES));
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
        $html[] = '<br />A: ' . Translation :: get('Add', null , Utilities :: COMMON_LIBRARIES);
        $html[] = '<br />U: ' . Translation :: get('Update', null , Utilities :: COMMON_LIBRARIES);
        $html[] = '<br />D: ' . Translation :: get('Delete', null , Utilities :: COMMON_LIBRARIES);
        $html[] = '</blockquote>';
        
        echo implode($html, "\n");
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('group general');
    }
}
?>