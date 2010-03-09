<?php
require_once dirname(__FILE__).'/data_manager/database.class.php';
require_once dirname(__FILE__).'/gradebook_manager/component/gradebook_score_browser/gradebook_score_browser_table.class.php';
require_once dirname(__FILE__).'/gradebook_manager/gradebook_manager.class.php';


class GradebookUtilities
{
  	
	function get_gradebook_admin_menu($browser)
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
		$html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . Translation :: get('GradeBook') . '</li><br />';
		$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a style="top: -3px; position: relative;" href="'.$browser->get_url(array(Application :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK)) .'">'.Translation :: get('BrowseGradeBook').'</a></li>';

//		$html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
//		
//		// Tool Links
//		$html[] = '<li class="tool_list_menu" style="font-weight: bold">' . Translation :: get('GradebookTools') . '</li><br />';
		
		$html[] = '</ul>';
		$html[] = '</div>';
		
		$html[] = '</div>';
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' .'"></script>';
		$html[] = '<div class="clear"></div>';
		
		return implode("\n", $html);
	}
	
	function get_gradebook_table_as_html($parameters, $user_id){
		$condition = new EqualityCondition(GradebookRelUser :: PROPERTY_USER_ID, $user_id);
		$table = new GradebookScoreBrowserTable($browser, $parameters, $condition);
		
		return $table->as_html();
		
	}
}
?>