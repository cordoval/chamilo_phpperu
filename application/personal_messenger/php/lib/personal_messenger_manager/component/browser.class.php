<?php

namespace application\personal_messenger;

use common\libraries\WebApplication;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Display;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Application;
use common\libraries\ActionBarRenderer;

/**
 * $Id: browser.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.personal_messenger_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'personal_messenger_manager/component/pm_publication_browser/pm_publication_browser_table.class.php';

class PersonalMessengerManagerBrowserComponent extends PersonalMessengerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get(ucfirst($this->get_folder()))));

        if(!PersonalMessengerRights :: is_allowed_in_personal_messenger_subtree(PersonalMessengerRights :: RIGHT_BROWSE, PersonalMessengerRights :: get_personal_messenger_subtree_root()))
        {
            $this->display_header();
                Display :: error_message(Translation :: get("NotAllowed", null , Utilities :: COMMON_LIBRARIES));
                $this->display_footer();
                exit();
        }

        $this->display_header($trail);
        echo $this->get_action_bar_html() . '';
        echo $this->get_publications_html();
        $this->display_footer();
    }

    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);
        
        $table = new PmPublicationBrowserTable($this, null, $parameters, $this->get_condition());
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        $conditions = array();
        $folder = $this->get_folder();
        if (isset($folder))
        {
            $folder_condition = null;
            
            switch ($folder)
            {
                case PersonalMessengerManager :: FOLDER_INBOX :
                    $folder_condition = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_RECIPIENT, $this->get_user_id());
                    break;
                case PersonalMessengerManager :: FOLDER_OUTBOX :
                    $folder_condition = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_SENDER, $this->get_user_id());
                    break;
                default :
                    $folder_condition = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_RECIPIENT, $this->get_user_id());
            }
        }
        else
        {
            $folder_condition = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_RECIPIENT, $this->get_user_id());
        }
        
        $condition = $folder_condition;
        
        $user_condition = new EqualityCondition(PersonalMessengerPublication :: PROPERTY_USER, $this->get_user_id());
        return new AndCondition($condition, $user_condition);
    }

    function get_action_bar_html()
    {
        if($this->get_user()->is_platform_admin())
        {
            $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ManageRights', null , Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_rights.png', $this->get_url(array(Application :: PARAM_ACTION => PersonalMessengerManager :: ACTION_RIGHT_EDITS))));
        
            return $action_bar->as_html();
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('personal_messenger_browser');
    }
}
?>