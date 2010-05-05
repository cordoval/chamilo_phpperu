<?php
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_data_provider.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu_item.class.php';

class GroupTreeMenuDataProvider extends TreeMenuDataProvider
{
	const PARAM_ID = 'group_id';
	
	function get_tree_menu_data()
	{
		$condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
        $group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_NAME))->next_result();
            
        $menu_item = new TreeMenuItem();
        $menu_item->set_title($group->get_name());
        $menu_item->set_id($group->get_id());
        //$menu_item['url'] = $this->get_url($group->get_id());
        $menu_item->set_url($this->get_url());
        	
        if ($group->has_children())
        {
       		$this->get_menu_items($menu_item, $group->get_id());
        }
            
        $menu_item->set_class('home');
        return $menu_item;
	}
	
	private function get_menu_items($parent_menu_item, $parent_id = 0)
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
    }
    
    public function get_id_param()
    {
    	return self :: PARAM_ID;
    }
}