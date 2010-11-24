<?php
namespace group;

use common\libraries\Path;
use common\libraries\TreeMenuDataProvider;
use common\libraries\ObjectTableOrder;
use common\libraries\TreeMenuItem;

require_once Path :: get_common_libraries_class_path() . 'html/menu/tree_menu/tree_menu_data_provider.class.php';
require_once Path :: get_common_libraries_class_path() . 'html/menu/tree_menu/tree_menu.class.php';
require_once Path :: get_common_libraries_class_path() . 'html/menu/tree_menu/tree_menu_item.class.php';

class GroupUsageTreeMenuDataProvider extends TreeMenuDataProvider
{
    const PARAM_ID = 'group_id';

    /**
     * @var array
     */
    private $group_ids;

    /**
     * @var array
     */
    private $groups;

    /**
     * @var User
     */
    private $user;

    function __construct(User $user, $url, $selected_tree_menu_item)
    {
        parent :: __construct($url, $selected_tree_menu_item);
        $this->user = $user;
    }

    function get_tree_menu_data()
    {
        if (! $this->user->is_platform_admin())
        {
            $this->group_ids = $this->user->get_allowed_groups();
            $group_condition = new InCondition(Group :: PROPERTY_ID, $this->group_ids);
        }

        $group_result_set = GroupDataManager :: get_instance()->retrieve_groups($group_condition, null, null, array(new ObjectTableOrder(Group :: PROPERTY_NAME)));

        while ($group = $group_result_set->next_result())
        {
            $group_parent_id = $group->get_parent();

            if (! is_array($this->groups[$group_parent_id]))
            {
                $this->groups[$group_parent_id] = array();
            }

            if (! isset($this->groups[$group_parent_id][$group->get_id()]))
            {
                $this->groups[$group_parent_id][$group->get_id()] = $group;
            }

            if ($group_parent_id != 0)
            {
                $tree_parents = $group->get_parents(false);

                while ($tree_parent = $tree_parents->next_result())
                {
                    $tree_parent_parent_id = $tree_parent->get_parent();

                    if (! is_array($this->groups[$tree_parent_parent_id]))
                    {
                        $this->groups[$tree_parent_parent_id] = array();
                    }

                    if (! isset($this->groups[$tree_parent_parent_id][$tree_parent->get_id()]))
                    {
                        $this->groups[$tree_parent_parent_id][$tree_parent->get_id()] = $tree_parent;
                    }
                }
            }
        }

        $root = $this->groups[0][1];

        $menu_item = new TreeMenuItem();
        $menu_item->set_title($root->get_name());
        $menu_item->set_id($root->get_id());

        if (! in_array($root->get_id(), $this->group_ids) && ! $this->user->is_platform_admin())
        {
            $menu_item->set_class('home disabled');
        }
        else
        {
            $menu_item->set_url($this->get_url());
            $menu_item->set_class('home');
        }

        $this->get_menu_items($menu_item);

        return $menu_item;
    }

    function get_menu_items($parent_item)
    {
        if (isset($this->groups[$parent_item->get_id()]))
        {
            foreach ($this->groups[$parent_item->get_id()] as $child)
            {
                $menu_item = new TreeMenuItem();
                $menu_item->set_title($child->get_name());
                $menu_item->set_id($child->get_id());

                if (! in_array($menu_item->get_id(), $this->group_ids) && ! $this->user->is_platform_admin())
                {
                    $menu_item->set_class('type_group disabled');
                }
                else
                {
                    $menu_item->set_url($this->format_url($child->get_id()));
                    $menu_item->set_class('type_group');
                }

                $this->get_menu_items($menu_item);

                $parent_item->add_child($menu_item);
            }
        }
    }

    public function get_id_param()
    {
        return self :: PARAM_ID;
    }
}
?>