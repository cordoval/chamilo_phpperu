<?php
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_data_provider.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_item.class.php';
require_once dirname(__FILE__).'/assessment_data_manager.class.php';
require_once dirname(__FILE__).'/assessment_publication.class.php';

class AssessmentGradebookTreeMenuDataProvider extends GradebookTreeMenuDataProvider
{
	function get_tree_menu_data()
	{
		$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_CATEGORY, 0);
        $assessment = AssessmentDataManager :: get_instance()->retrieve_assessment_publications($condition);
        $menu_item = new TreeMenuItem();
        $menu_item->set_title(Translation :: get('AssessmentCategories'));
        $menu_item->set_id('C0');
        $menu_item->set_url($this->get_url($this->get_url()));
        $menu_item->set_class('home');
//		$condition = new EqualityCondition(AssessmentPublication :: PROPERTY_PARENT, $assessment->category_id());
//		$category_child = $rdm->retrieve_categories($condition)->next_result();
//		if ($category_child)
//        {
//       		$this->get_menu_items($assessment_item, $category->get_id());
//        }
//        
        return $menu_item;
	}
//	
//	function get_menu_items($parent_menu_item, $parent_id = 0)
//	{
//		
//	}
}
?>