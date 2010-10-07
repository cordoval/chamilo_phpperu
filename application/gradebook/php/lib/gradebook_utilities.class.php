<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'gradebook_manager/gradebook_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation_manager/evaluation_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'connector/gradebook_connector.class.php';

class GradebookUtilities
{
	/*static function check_tracker_for_user($application, $publication_id, $tool = null)
	{
		$connector = GradeBookConnector :: factory($application, $tool);
		if($tracker_user = $connector->get_tracker_user($publication_id))
			return $tracker_user;
		else
			return false;
	}*/
	static function move_internal_item_to_external_item($application, $publication_id)
	{
		if(EvaluationManager :: retrieve_evaluation_ids_by_publication($application, $publication_id))
        {	
        	$application_manager = WebApplication :: factory($application);
        	$content_object_publication = $application_manager->get_content_object_publication_attribute($publication_id);
			$gdm = GradebookDataManager :: get_instance();
			$internal_item = $gdm->retrieve_internal_item_by_publication($application, $publication_id);
			$category = $internal_item->get_category();
			$category = split('_', $category);
			if($internal_item->get_calculated() == 1)
			{
				$connector = GradeBookConnector :: factory($application);
				if(!$connector->get_tracker_user($publication_id))
				{
					$del_internal_item = $gdm->delete_internal_item($internal_item);
					return false;
				}
				
				if($application != 'weblcms')
				{
					$category = null;
				}
				$external_item = $gdm->create_external_item_by_content_object($content_object_publication->get_publication_object_id(), $category[0]);
				foreach($connector->get_tracker_user($publication_id) as $connector_user)
				{
					$evaluation = $gdm->create_evaluation_object_from_data($content_object_publication, $connector_user, $connector->get_tracker_date($publication_id));
					if(!$evaluation)
						return false;
					
					$grade_evaluation = new GradeEvaluation();
					$grade_evaluation->set_score($connector->get_tracker_score($publication_id));
					$grade_evaluation->set_comment('automatic generated result');
					$grade_evaluation->set_id($evaluation->get_id());
					$grade_evaluation = $gdm->create_grade_evaluation($grade_evaluation);
					
					if(!($grade_evaluation || $evaluation))
						return false;
					$ext_item_inst = $gdm->create_external_item_instance_by_moving($external_item, $evaluation->get_id());
				}
				$del_internal_item = $gdm->delete_internal_item($internal_item);
			}
			else
			{
				$evaluations_id = $gdm->retrieve_evaluation_ids_by_internal_item_id($internal_item->get_id())->as_array();
				if(!$evaluations_id)
					return false;
				if($application != 'weblcms')
				{
					$category = null;
				}
				$external_item = $gdm->create_external_item_by_content_object($content_object_publication->get_publication_object_id(), $category[0]);
				$ext_item_inst = $gdm->create_external_item_instance_by_moving($external_item, $evaluations_id);
				$del_internal_item = $gdm->delete_internal_item($internal_item);
			}
			if(!($internal_item || $external_item || $ext_item_inst || $del_internal_item))
				return false;
			return true;
        }
	}
	
	/*static function get_categories($application, $category_array)
	{
		$connector = CategoriesConnector :: factory($application);
		$connector->get_categories($category_array);
	}*/

  	
//	function get_gradebook_admin_menu($browser)
//	{
//		$html = array();
//		
//		$html[] = '<div id="tool_bar" class="tool_bar tool_bar_right">';
//		
//		$html[] = '<div id="tool_bar_hide_container" class="hide">';
//		$html[] = '<a id="tool_bar_hide" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_right_hide.png" /></a>';
//		$html[] = '<a id="tool_bar_show" href="#"><img src="'. Theme :: get_common_image_path() .'action_action_bar_right_show.png" /></a>';
//		$html[] = '</div>';
//		
//		$html[] = '<div class="tool_menu">';
//		$html[] = '<ul>';
//		
//		// Browse Links
//		$html[] = '<li class="tool_list_menu title" style="font-weight: bold">' . Translation :: get('GradeBook') . '</li><br />';
//		$html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a style="top: -3px; position: relative;" href="'.$browser->get_url(array(Application :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK)) .'">'.Translation :: get('BrowseGradeBook').'</a></li>';
//
////		$html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
////		
////		// Tool Links
////		$html[] = '<li class="tool_list_menu" style="font-weight: bold">' . Translation :: get('GradebookTools') . '</li><br />';
//		
//		$html[] = '</ul>';
//		$html[] = '</div>';
//		
//		$html[] = '</div>';
//		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' .'"></script>';
//		$html[] = '<div class="clear"></div>';
//		
//		return implode("\n", $html);
//	}
//	
//	function get_gradebook_table_as_html($parameters, $user_id){
//		$condition = new EqualityCondition(GradebookRelUser :: PROPERTY_USER_ID, $user_id);
//		$table = new GradebookScoreBrowserTable($browser, $parameters, $condition);
//		
//		return $table->as_html();
//		
//	}
}
?>