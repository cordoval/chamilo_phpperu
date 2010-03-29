<?php
require_once dirname(__FILE__).'/internship_planner_data_manager.class.php';

class InternshipPlannerUtilities
{



	function get_menu($browser)
	{
		$html = array();

		$html[] = '<div id="tool_bar" class="tool_bar tool_bar_right">';

		$html[] = '<div id="tool_bar_hide_container" class="hide">';
		$html[] = '<a id="tool_bar_hide" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_right_hide.png" /></a>';
		$html[] = '<a id="tool_bar_show" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_right_show.png" /></a>';
		$html[] = '</div>';

		$html[] = '<div class="tool_menu">';
		$html[] = '<ul>';

		// Browse Links
		$html[] = '<li class="tool_list_menu" style="font-weight: bold">' . Translation :: get('InternshipPlanner') . '</li><br />';
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_users.png)"><a style="top: -3px; position: relative;" href="'.$browser->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_CATEGORY)) .'">'.Translation :: get('CategoryBrowser').'</a></li>';
		$html[] = '<li class="tool_list_menu" style="list-style-position: inside; list-style-image: url(' . Theme :: get_common_image_path() . 'action_users.png)"><a style="top: -3px; position: relative;" href="'.$browser->get_url(array(InternshipPlannerManager :: PARAM_ACTION => InternshipPlannerManager :: ACTION_ORGANISATION)) .'">'.Translation :: get('OrganisationBrowser').'</a></li>';

		$html[] = '</ul>';
		$html[] = '</div>';

		$html[] = '</div>';
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' .'"></script>';
		$html[] = '<div class="clear"></div>';

		return implode("\n", $html);
	}

}
?>