<?php
/**
 * $Id: browser.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.distribute_manager.component
 */
require_once dirname(__FILE__) . '/../distribute_manager.class.php';
require_once dirname(__FILE__) . '/announcement_distribution_browser/announcement_distribution_browser_table.class.php';

/**
 * Distribute component which allows the user to browse the distribute application
 * @author Hans De Bisschop
 */
class DistributeManagerBrowserComponent extends DistributeManager
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseDistribute')));
        
        $this->display_header($trail);
        echo '<a name="top"></a>';
        echo $this->get_action_bar_html() . '';
        echo '<div id="action_bar_browser">';
        echo $this->get_browser_html();
        echo '</div>';
        $this->display_footer();
    }

    function get_action_bar_html()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Send'), Theme :: get_common_image_path() . 'action_mail.png', $this->get_url(array(Application :: PARAM_ACTION => DistributeManager :: ACTION_DISTRIBUTE_ANNOUNCEMENT)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar->as_html();
    }

    function get_browser_html()
    {
        $parameters = $this->get_parameters(true);
        
        $table = new AnnouncementDistributionBrowserTable($this, null, $parameters, $this->get_condition());
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        return null;
        
    //		$conditions = array();
    //		$folder = $this->get_folder();
    //		if (isset($folder))
    //		{
    //			$folder_condition = null;
    //
    //			switch ($folder)
    //			{
    //				case PersonalMessengerManager :: ACTION_FOLDER_INBOX :
    //					$folder_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
    //					break;
    //				case PersonalMessengerManager :: ACTION_FOLDER_OUTBOX :
    //					$folder_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_SENDER, $this->get_user_id());
    //					break;
    //				default :
    //					$folder_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
    //			}
    //		}
    //		else
    //		{
    //			$folder_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
    //		}
    //
    //		$condition = $folder_condition;
    //
    //		$user_condition = new EqualityCondition(PersonalMessagePublication :: PROPERTY_USER, $this->get_user_id());
    //		return new AndCondition($condition, $user_condition);
    }
}
?>