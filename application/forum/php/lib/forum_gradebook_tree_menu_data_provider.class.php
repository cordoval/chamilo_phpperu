<?php
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_data_provider.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_item.class.php';
require_once dirname(__FILE__).'/forum_data_manager.class.php';
require_once dirname(__FILE__).'/forum_publication.class.php';

class ForumGradebookTreeMenuDataProvider extends GradebookTreeMenuDataProvider
{
	function get_tree_menu_data()
	{
		$condition = new EqualityCondition(ForumPublication :: PROPERTY_CATEGORY_ID, 0);
        $forum = ForumDataManager :: get_instance()->retrieve_forum_publications($condition)->next_result();
        $menu_item = new TreeMenuItem();
        $menu_item->set_title(Translation :: get('ForumCategories'));
        $menu_item->set_id('C0');
        $menu_item->set_url($this->get_url($this->get_url()));
        $menu_item->set_class('home');
        
//		$condition = new EqualityCondition(ForumPublication :: PROPERTY_PARENT, $forum->category_id());
//		$category_child = $rdm->retrieve_categories($condition)->next_result();
//		if ($category_child)
//        {
//       		$this->get_menu_items($wiki_item, $category->get_id());
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