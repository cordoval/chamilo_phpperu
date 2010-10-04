<?php
/**
 * $Id: buddy_list_viewer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerBuddyListViewerComponent extends UserManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		Header :: set_section('my_account');

		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyAccount')));
		$trail->add_help('user general');

		$this->display_header();
		echo '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
		$actions = array('account', 'buddy_view');
		foreach ($actions as $action)
		{
			echo '<li><a';
			if ($action == 'buddy_view')
			{
				echo ' class="current"';
			}
			echo' href="'.$this->get_url(array(UserManager :: PARAM_ACTION => $action)).'">'.htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($action).'Title')).'</a></li>';
		}
		echo '</ul><div class="tabbed-pane-content"><br />';

		echo '<div style="width: 20%; float: left; overflow: auto;">';
		$buddylist = new BuddyList($this->get_user(), $this);
		echo $buddylist->to_html();
		echo '</div>';
		
		echo '<div style="width: 78%; float: right; overflow: auto;">';
		$chatmanager = new ChatManager($this->get_user(), $this->get_user(), $this);
		echo $chatmanager->to_html();
		echo '</div>';
		
		echo '<div class="clear"></div>';
		echo '</div></div>';

		$this->display_footer();
	}
}
?>