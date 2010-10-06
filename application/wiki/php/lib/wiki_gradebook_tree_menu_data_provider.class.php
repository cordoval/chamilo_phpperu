<?php
require_once Path :: get_common_libraries_path() . '/html/menu/tree_menu/tree_menu_data_provider.class.php';
require_once Path :: get_common_libraries_path() . '/html/menu/tree_menu/tree_menu.class.php';
require_once Path :: get_common_libraries_path() . '/html/menu/tree_menu/tree_menu_item.class.php';
require_once WebApplication :: get_application_class_lib_path('wiki') . 'wiki_data_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('wiki') . 'wiki_publication.class.php';

class WikiGradebookTreeMenuDataProvider extends GradebookTreeMenuDataProvider
{
	function get_tree_menu_data()
	{
		$condition = new EqualityCondition(WikiPublication :: PROPERTY_PARENT_ID, 0);
        $wikis = WikiDataManager :: get_instance()->retrieve_wiki_publications($condition);
        $menu_item = new TreeMenuItem();
        $menu_item->set_title(Translation :: get('WikiCategories'));
        $menu_item->set_id('C0');
        $menu_item->set_url($this->get_url($this->get_url()));
        $menu_item->set_class('home');
        
//        while($wiki = $wikis->next_result())
//        {
//        	$application_manager = WebApplication :: factory('wiki');
//			$attributes = $application_manager->get_content_object_publication_attribute($wiki->get_id());
//			$rdm = RepositoryDataManager :: get_instance();
//			$content_object = $rdm->retrieve_content_object($attributes->get_publication_object_id());
////			$condition = new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $content_object->get_parent_id());
////			$category = $rdm->retrieve_categories($condition)->next_result();
//			
////			if(!$content_object->get_parent_id() == 0)
////			{
//				$wiki_item = new TreeMenuItem();
//		        $wiki_item->set_title($content_object->get_title());
//		        $wiki_item->set_id($content_object->get_id());
//		        $wiki_item->set_url($this->format_url('C' . $content_object->get_id()));
//		        $wiki_item->set_class('wiki');
//		        
////		        $condition = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $category->get_id());
////				$category_child = $rdm->retrieve_categories($condition)->next_result();
////				if ($category_child)
////		        {
////		       		$this->get_menu_items($wiki_item, $category->get_id());
////		        }
//		        
//		        if(!in_array($wiki_item, $menu_item->get_children()))
//		        {
//		       		$menu_item->add_child($wiki_item);
//		        }
////			}
//        }
        return $menu_item;
	}
	
//	private function get_menu_items($parent_menu_item, $parent_id = 0)
//    {   
//    	$conditions = array();
//        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_PARENT_ID, $parent_id);
//        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'wiki');
//        $condition = new AndCondition($conditions);
//        
//        $wikis = RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);
//        
//        while ($wiki = $wikis->next_result())
//        {
//        	$application_manager = WebApplication :: factory('wiki');
//			$attributes = $application_manager->get_content_object_publication_attribute($wiki->get_id());
//			$rdm = RepositoryDataManager :: get_instance();
//			$content_object = $rdm->retrieve_content_object($attributes->get_publication_object_id());
//			$condition = new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $content_object->get_parent_id());
//			$category = $rdm->retrieve_categories($condition)->next_result();
//			
//			if($content_object->get_parent_id() == $parent_id)
//			{
//	            $wiki_item = new TreeMenuItem();
//		        $wiki_item->set_title($category->get_name());
//		        $wiki_item->set_id($category->get_id());
//		        $wiki_item->set_url($this->format_url('C' . $category->get_id()));
//		        $wiki_item->set_class('wiki');
//	            
//				$condition = new EqualityCondition(RepositoryCategory :: PROPERTY_PARENT, $category->get_id());
//				$category_child = $rdm->retrieve_categories($condition)->next_result();
//				if ($category_child)
//		        {
//		       		$this->get_menu_items($wiki_item, $category->get_id());
//		        }
//	            
//				if(!in_array($wiki_item, $menu_item->get_children()))
//		        {
//		       		$parent_menu_item->add_child($wiki_item);
//		        }
//			}
//        }
//    }
}
?>