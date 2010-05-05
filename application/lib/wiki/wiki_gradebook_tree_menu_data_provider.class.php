<?php
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_data_provider.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_item.class.php';
require_once dirname(__FILE__).'/wiki_data_manager.class.php';
require_once dirname(__FILE__).'/wiki_publication.class.php';

class WikiGradebookTreeMenuDataProvider extends TreeMenuDataProvider
{
	const PARAM_ID = 'category_id';
	
	function get_tree_menu_data()
	{
		$condition = new EqualityCondition(WikiPublication :: PROPERTY_PARENT_ID, 0);
        $wikis = WikiDataManager :: get_instance()->retrieve_wiki_publications($condition);
        $menu_item = new TreeMenuItem();
        $menu_item->set_title(Translation :: get('WikiCategories'));
        $menu_item->set_id(0);
        $menu_item->set_url($this->get_url($this->get_url()));
        	
//        if ($wiki->has_children())
//        {
//       		$this->get_menu_items($menu_item, $wiki->get_id());
//        }
            
        $menu_item->set_class('home');
        
        while($wiki = $wikis->next_result())
        {
        	$application_manager = WebApplication :: factory('wiki');
			$attributes = $application_manager->get_content_object_publication_attribute($wiki->get_id());
			$rdm = RepositoryDataManager :: get_instance();
			$content_object = $rdm->retrieve_content_object($attributes->get_publication_object_id());
			$condition = new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $content_object->get_parent_id());
			$category = $rdm->retrieve_categories($condition)->next_result();
			
			if(!$content_object->get_parent_id() == 0)
			{
				$wiki_item = new TreeMenuItem();
		        $wiki_item->set_title($category->get_name());
		        $wiki_item->set_id($category->get_id());
		        $wiki_item->set_url($this->format_url($category->get_id()));
		        $wiki_item->set_class('wiki');
		        
		        $menu_item->add_child($wiki_item);
			}
        }
        
        return $menu_item;
	}
	
	/*private function get_menu_items($parent_menu_item, $parent_id = 0)
    {   
    
        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, $parent_id);
        $groups = GroupDataManager :: get_instance()->retrieve_groups($condition, null, null, new ObjectTableOrder(Group :: PROPERTY_NAME));
        
        while ($group = $groups->next_result())
        {
            $group_id = $group->get_id();
            
			$menu_item = new TreeMenuItem();
            $menu_item->set_title($group->get_name());
            $menu_item->set_id($group->get_id());
            $menu_item->set_url($this->format_url($group->get_id()));
            
            if ($group->has_children())
            {
              	$this->get_menu_items($menu_item, $group->get_id());
            }
                
			$parent_menu_item->add_child($menu_item);
        }
    }*/
    
    public function get_id_param()
    {
    	return self :: PARAM_ID;
    }
}
?>