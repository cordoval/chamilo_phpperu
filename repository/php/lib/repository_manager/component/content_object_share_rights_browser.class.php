<?php
namespace repository;

use common\libraries;

use user;

use common\libraries\Request;
use common\libraries\ActionBarRenderer;
use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\Utilities;
use common\libraries\DynamicVisualTabsRenderer;
use common\libraries\DynamicVisualTab;
use common\libraries\ToolbarItem;


use user\UserManager;
use group\GroupManager;

require_once dirname(__FILE__) . "/content_object_user_share_rights_browser/content_object_user_share_rights_browser_table.class.php";
require_once dirname(__FILE__) . "/content_object_group_share_rights_browser/content_object_group_share_rights_browser_table.class.php";

/**
 * Browser for content object share rights
 *
 * @author Pieterjan Broekaert
 */
class RepositoryManagerContentObjectShareRightsBrowserComponent extends RepositoryManager
{

    private $type;
    private $content_object_ids;

    const TAB_DETAILS = 0;
    const TAB_SUBGROUPS = 1;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $this->content_object_ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);

        //set rights for users or groups?
        $this->type = Request :: get(ContentObjectShare :: PARAM_TYPE);
        if (is_null($this->type))
        {
            $this->type = ContentObjectUserShare :: TYPE_USER_SHARE;
        }

        $this->action_bar = $this->get_action_bar();

        //display the component
        $this->display_header();
        echo $this->action_bar->as_html();
        $this->display_body();
        $this->display_footer();
    }

    /**
     * Displays the body
     */
    private function display_body()
    {
        switch ($this->type)
        {
            case ContentObjectUserShare :: TYPE_USER_SHARE :
                $table = $this->get_users_browser_html();
                break;
            case ContentObjectGroupShare :: TYPE_GROUP_SHARE :
                $table = $this->get_groups_browser_html();
                break;
            default :
                $table = '';
                break;
        }

        $renderer_name = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $tabs = new DynamicVisualTabsRenderer($renderer_name, $table);

        $label = htmlentities(Translation :: get('Users', null, UserManager :: APPLICATION_NAME));
        $link = $this->get_url(array(ContentObjectShare :: PARAM_TYPE => ContentObjectUserShare :: TYPE_USER_SHARE));
        $tabs->add_tab(new DynamicVisualTab('users', $label, Theme :: get_image_path(UserManager :: APPLICATION_NAME) . 'logo/22.png', $link, ($this->type == ContentObjectUserShare :: TYPE_USER_SHARE)));

        $label = htmlentities(Translation :: get('Groups', null, GroupManager :: APPLICATION_NAME));
        $link = $this->get_url(array(ContentObjectShare :: PARAM_TYPE => ContentObjectGroupShare :: TYPE_GROUP_SHARE));
        $tabs->add_tab(new DynamicVisualTab('users', $label, Theme :: get_image_path(GroupManager :: APPLICATION_NAME) . 'logo/22.png', $link, ($this->type == ContentObjectGroupShare :: TYPE_GROUP_SHARE)));

        echo $tabs->render();
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

        $html[] = '<a href="' . $this->get_url(array(ContentObjectShare :: PARAM_TYPE => ContentObjectUserShare :: TYPE_USER_SHARE)) . '">';
        $html[] = '<div class="application" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_user.png);">' . Translation :: get('Users', null, UserManager :: APPLICATION_NAME) . '</div>';
        $html[] = '</a>';

        $html[] = '<a href="' . $this->get_url(array(ContentObjectShare :: PARAM_TYPE => ContentObjectGroupShare :: TYPE_GROUP_SHARE)) . '">';
        $html[] = '<div class="application" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_group.png);">' . Translation :: get('Groups', null, GroupManager :: APPLICATION_NAME) . '</div>';
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
        //        $dm = RepositoryDataManager :: get_instance();
        //        $user_shares_result_set = $dm->retrieve_content_object_user_shares();
        //
        //        $user_share = $user_shares_result_set->next_result();
        //        while ($user_share != null)
        //        {
        //            $conditions[] = new EqualityCondition(User :: PROPERTY_ID, $user_share->get_user_id());
        //            $user_to_right[$user_share->get_user_id()] = $user_share->get_right_id();
        //            $user_share = $user_shares_result_set->next_result();
        //        }


        $condition = new EqualityCondition(ContentObjectShare :: PROPERTY_CONTENT_OBJECT_ID, $this->content_object_ids);
        $browser_table = new ContentObjectUserShareRightsBrowserTable($this, $this->get_parameters(), $condition);
        return $browser_table->as_html();
    }

    /**
     * Displays the groups you shared with and the rights
     */
    private function get_groups_browser_html()
    {
        //        $html = array();
        //
        //        $dm = RepositoryDataManager :: get_instance();
        //        $groups_result_set = $dm->retrieve_content_object_group_shares();
        //
        //        $group = $groups_result_set->next_result();
        //        while ($group != null)
        //        {
        //            $conditions[] = new EqualityCondition(Group :: PROPERTY_ID, $group->get_group_id());
        //            $group = $groups_result_set->next_result();
        //        }
        //        $condition = new OrCondition($conditions);
        $condition = new EqualityCondition(ContentObjectShare :: PROPERTY_CONTENT_OBJECT_ID, $this->content_object_ids);

        $browser_table = new ContentObjectGroupShareRightsBrowserTable($this, $this->get_parameters(), $condition);

        return $browser_table->as_html();
    }

    /**
     * create an action bar
     */
    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShareWithOtherUsersGroups'), Theme :: get_common_image_path() . 'action_rights.png', $this->get_content_object_share_create_url($this->content_object_ids)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
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

    function get_additional_parameters()
    {
        $parameters[] = ContentObjectUserShare :: PROPERTY_USER_ID;
        $parameters[] = ContentObjectGroupShare :: PROPERTY_GROUP_ID;
        $parameters[] = RepositoryManager :: PARAM_CONTENT_OBJECT_ID;
        $parameters[] = RepositoryManager :: PARAM_CATEGORY_ID;
        $parameters[] = ContentObjectShare :: PARAM_TYPE;

        return $parameters;
    }

}

?>
