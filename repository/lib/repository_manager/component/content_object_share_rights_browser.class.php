<?php

require_once dirname(__FILE__) . "/content_object_user_share_rights_browser/content_object_user_share_rights_browser_table.class.php";

/**
 * Browser for content object share rights
 *
 * @author Pieterjan Broekaert
 */
class RepositoryManagerContentObjectShareRightsBrowserComponent extends RepositoryManager
{

    private $type;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        //set the necessary parameters
        //$this->set_parameter(self :: PARAM_TYPE, Request :: get(self :: PARAM_TYPE));
        $this->set_parameter(ContentObjectUserShare :: PROPERTY_USER_ID, Request :: get(ContentObjectUserShare :: PROPERTY_USER_ID));
        $this->set_parameter(ContentObjectGroupShare :: PROPERTY_GROUP_ID, Request :: get(ContentObjectGroupShare :: PROPERTY_GROUP_ID));
        
        $content_object_ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        $this->set_parameter(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, $content_object_ids);
        

        //set rights for users or groups?
        $this->type = Request :: get(ContentObjectShare :: PARAM_TYPE);
        if(is_null($this->type))
        {
            $this->type = ContentObjectUserShare :: TYPE_USER_SHARE;
        }

        $this->action_bar = $this->get_action_bar();

        //display the component
        $this->display_header();
        $this->display_body();
        $this->display_footer();
    }

    /**
     * Displays the body
     */
    private function display_body()
    {
        $html = array();

        $html[] = $this->get_type_selector_html();

        if ($this->type == ContentObjectUserShare :: TYPE_USER_SHARE)
        {
            $html[] = $this->get_users_browser_html();
        }
        else
        {
            $html[] = $this->get_groups_browser_html();
        }

        echo implode("\n", $html);
    }

    /**
     * Displays a type selecter: user or group
     * @return the type selector html
     */
    private function get_type_selector_html()
    {
        $html = array();

        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/application.js');
        $html[] = '<div class="application_selecter">';

        $html[] = '<a href="' . $this->get_url(array(ContentObjectShare :: PARAM_TYPE => ContentObjectUserShare::TYPE_USER_SHARE)) . '">';
        $html[] = '<div class="application" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_user.png);">' . Translation :: get('Users') . '</div>';
        $html[] = '</a>';

        $html[] = '<a href="' . $this->get_url(array(ContentObjectShare :: PARAM_TYPE => ContentObjectGroupShare::TYPE_GROUP_SHARE)) . '">';
        $html[] = '<div class="application" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_group.png);">' . Translation :: get('Groups') . '</div>';
        $html[] = '</a>';

        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';

        return implode("\n", $html);
    }

    /**
     * Displays the users you shared with and the rights
     */
    private function get_users_browser_html()
    {
        $condition = new EqualityCondition(User :: PROPERTY_ID, 1); //temp: get shared users
        $browser_table = new ContentObjectUserShareRightsBrowserTable($this, $this->get_parameters(), $condition);
        return $browser_table->as_html();
    }

    /**
     * Displays the groups you shared with and the rights
     */
    private function get_groups_browser_html()
    {
        
    }

    /**
     * create an action bar
     */
    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        return $action_bar;
    }

    /**
     * add additional breadcrumbs before the auto generated share_rights_browser breadcrumb
     * @param BreadcrumbTrail $breadcrumbtrail
     */
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help("repository_content_object_share_rights_browser");
    }

}

?>
