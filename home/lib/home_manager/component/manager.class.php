<?php
/**
 * $Id: manager.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component
 */

class HomeManagerManagerComponent extends HomeManager
{
    private $user_id;
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('admin');
        
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => HomeManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Home')));
        //$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeManager')));
        $trail->add_help('home general');
        
        $this->action_bar = $this->get_action_bar();
        
        $user = $this->get_user();
        $user_home_allowed = $this->get_platform_setting('allow_user_home');
        
        // Get user id
        if ($user_home_allowed && Authentication :: is_valid())
        {
            $this->user_id = $user->get_id();
        }
        else
        {
            if (! $user->is_platform_admin())
            {
                $this->display_header();
                Display :: error_message(Translation :: get('NotAllowed'));
                $this->display_footer();
                exit();
            }
            
            $this->user_id = '0';
        }
        
        $this->display_header();
        echo $this->action_bar->as_html();
        echo $this->get_preview_html();
        
        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddTab'), Theme :: get_image_path() . 'action_add_tab.png', $this->get_home_tab_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddRow'), Theme :: get_image_path() . 'action_add_row.png', $this->get_home_row_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddColumn'), Theme :: get_image_path() . 'action_add_column.png', $this->get_home_column_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddBlock'), Theme :: get_image_path() . 'action_add_block.png', $this->get_home_block_creation_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }

    function get_preview_html()
    {
        $user_id = $this->user_id;
        
        $tabs_condition = new EqualityCondition(HomeTab :: PROPERTY_USER, $user_id);
        $tabs = $this->retrieve_home_tabs($tabs_condition);
        
        $html = array();
        
        while ($tab = $tabs->next_result())
        {
            $html[] = '<div style="text-align: center; border: 1px solid grey; margin-bottom: 30px; padding: 15px; width: 720px;">';
            $html[] = '<h1 style="margin: 0px; padding: 0px;">';
            $html[] = Translation :: get('Tab') . ':&nbsp;' . $tab->get_title();
            $html[] = '</h1>';
            $html[] = $this->get_tab_modification_links($tab, $tabs->position());
            
            $rows_condition = new EqualityCondition(HomeRow :: PROPERTY_TAB, $tab->get_id());
            $rows = $this->retrieve_home_rows($rows_condition);
            
            while ($row = $rows->next_result())
            {
                $html[] = '<div class="row" style="' . ($rows->position() != 'last' && $rows->position() != 'single' ? 'margin-bottom: 15px;' : '') . 'padding: 10px; text-align: center; line-height: 20px; font-size: 20pt; background-color: #9a9a9a; color: #FFFFFF;">';
                $html[] = '<h2 style="margin: 0px; padding: 0px;">' . Translation :: get('Row') . ':&nbsp;' . $row->get_title() . '</h2>';
                $html[] = $this->get_row_modification_links($row, $rows->position());
                
                $conditions = array();
                $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row->get_id());
                $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
                $condition = new AndCondition($conditions);
                
                $columns = $this->retrieve_home_columns($condition);
                
                while ($column = $columns->next_result())
                {
                    $column_width = floor((700 - ($columns->size() - 1) * 10) / $columns->size()) - 20;
                    $html[] = '<div class="column" style="' . ($columns->position() != 'last' && $columns->position() != 'single' ? 'margin-right: 1%;' : '') . ' text-align: center; width: ' . $column->get_width() . '%; font-size: 10pt;background-color: #E8E8E8; color: #000000;">';
                    $html[] = '<h3 style="margin: 0px; padding: 0px;">' . Translation :: get('Column') . ':&nbsp;' . $column->get_title() . ' (' . $column->get_width() . '%)</h3>';
                    $html[] = $this->get_column_modification_links($column, $columns->position());
                    
                    $conditions = array();
                    $conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $column->get_id());
                    $conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_USER, $user_id);
                    $condition = new AndCondition($conditions);
                    
                    $blocks = $this->retrieve_home_blocks($condition);
                    
                    while ($block = $blocks->next_result())
                    {
                        $html[] = '<div style="margin: 0px 10px 10px 10px; padding: 10px; text-align: center; height: 40px; line-height: 20px; font-size: 8pt;background-color: #B8B8B8; color: #2F2F2F;">';
                        $html[] = Translation :: get('Block') . ':&nbsp;' . $block->get_title();
                        $html[] = $this->get_block_modification_links($block, $blocks->position());
                        $html[] = '</div>';
                        $html[] = '<div style="clear: both;"></div>';
                    }
                    $html[] = '</div>';
                }
                $html[] = '<div style="clear: both;"></div>';
                $html[] = '</div>';
            }
            
            $html[] = '<div style="clear: both;"></div>';
            $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }

    private function get_tab_modification_links($home_tab, $index)
    {
    	$toolbar = new Toolbar();
        
        $edit_url = $this->get_home_tab_editing_url($home_tab);
        $toolbar->add_item( new ToolbarItem( Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $edit_url, ToolbarItem :: DISPLAY_ICON, false));
        
        $edit_url = $this->get_home_tab_deleting_url($home_tab);
        $toolbar->add_item( new ToolbarItem( Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $edit_url, ToolbarItem :: DISPLAY_ICON, true));    
        
        if ($index == 'first' || $index == 'single')
        {
        	$toolbar->add_item( new ToolbarItem( Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up_na.png', NULL , ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $move_url = $this->get_home_tab_moving_url($home_tab, 'up');
            $toolbar->add_item( new ToolbarItem( Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up.png', $move_url, ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($index == 'last' || $index == 'single')
        {
        	$toolbar->add_item( new ToolbarItem( Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down_na.png', NULL, ToolbarItem :: DISPLAY_ICON));
        }
        else
        {
            $move_url = $this->get_home_tab_moving_url($home_tab, 'down');
            $toolbar->add_item( new ToolbarItem( Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down_na.png', $move_url, ToolbarItem :: DISPLAY_ICON));
        }
        
        return $toolbar->as_html();
    }

    private function get_row_modification_links($home_row, $index)
    {
    	
    	$toolbar = new Toolbar(); 
        
    	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('Edit'),
    			Theme :: get_common_image_path() . 'action_edit.png',
    			$this->get_home_row_editing_url($home_row),
    			ToolbarItem :: DISPLAY_ICON
    	));
    	
    	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('Delete'),
    			Theme :: get_common_image_path() . 'action_delete.png',
    			$this->get_home_row_deleting_url($home_row),
    			ToolbarItem :: DISPLAY_ICON,
    			true
    	));
        
        if ($index == 'first' || $index == 'single')
        {
			$toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveUpNA'),
    				Theme :: get_common_image_path() . 'action_up_na.png',
    				null,
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveUp'),
    				Theme :: get_common_image_path() . 'action_up.png',
    				$this->get_home_row_moving_url($home_row, 'up'),
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        
        if ($index == 'last' || $index == 'single')
        {
            $toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveDownNA'),
    				Theme :: get_common_image_path() . 'action_down_na.png',
    				null,
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveDown'),
    				Theme :: get_common_image_path() . 'action_down.png',
    				$this->get_home_row_moving_url($home_row, 'down'),
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        
        return '<div class="manage">' . $toolbar->as_html() . '<div class="clear"></div></div>';
    }

    private function get_column_modification_links($home_column, $index)
    {
        $toolbar = new Toolbar(); 
        
    	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('Edit'),
    			Theme :: get_common_image_path() . 'action_edit.png',
    			$this->get_home_column_editing_url($home_column),
    			ToolbarItem :: DISPLAY_ICON
    	));
    	
    	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('Delete'),
    			Theme :: get_common_image_path() . 'action_delete.png',
    			$this->get_home_column_deleting_url($home_column),
    			ToolbarItem :: DISPLAY_ICON,
    			true
    	));
        
        if ($index == 'first' || $index == 'single')
        {
            $toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveLeftNA'),
    				Theme :: get_common_image_path() . 'action_left_na.png',
    				null,
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveLeft'),
    				Theme :: get_common_image_path() . 'action_left.png',
    				$this->get_home_column_moving_url($home_column, 'up'),
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        
        if ($index == 'last' || $index == 'single')
        {
        	$toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveRightNA'),
    				Theme :: get_common_image_path() . 'action_right_na.png',
    				null,
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        else
        {
        	$toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveRight'),
    				Theme :: get_common_image_path() . 'action_right.png',
    				$this->get_home_column_moving_url($home_column, 'down'),
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        
        return '<div class="manage">' . $toolbar->as_html() . '<div class="clear"></div></div>';
    }

    private function get_block_modification_links($home_block, $index)
    {
        $toolbar = new Toolbar(); 
        
    	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('Edit'),
    			Theme :: get_common_image_path() . 'action_edit.png',
    			$this->get_home_block_editing_url($home_block),
    			ToolbarItem :: DISPLAY_ICON
    	));
        
    	$toolbar->add_item(new ToolbarItem(
    			Translation :: get('Configure'),
    			Theme :: get_common_image_path() . 'action_config.png',
    			$this->get_home_block_configuring_url($home_block),
    			ToolbarItem :: DISPLAY_ICON
    	));
    	
    	 $toolbar->add_item(new ToolbarItem(
    			Translation :: get('Delete'),
    			Theme :: get_common_image_path() . 'action_delete.png',
    			$this->get_home_block_deleting_url($home_block),
    			ToolbarItem :: DISPLAY_ICON,
    			true
    	));
    	
        if ($index == 'first' || $index == 'single')
        {
            $toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveUpNA'),
    				Theme :: get_common_image_path() . 'action_up_na.png',
    				null,
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveUp'),
    				Theme :: get_common_image_path() . 'action_up.png',
    				$this->get_home_block_moving_url($home_block, 'up'),
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        
        if ($index == 'last' || $index == 'single')
        {
            $toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveDownNA'),
    				Theme :: get_common_image_path() . 'action_down_na.png',
    				null,
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
    				Translation :: get('MoveDown'),
    				Theme :: get_common_image_path() . 'action_down.png',
    				$this->get_home_block_moving_url($home_block, 'down'),
    				ToolbarItem :: DISPLAY_ICON
    		));
        }
        
        return '<div class="manage">' . $toolbar->as_html() . '<div class="clear"></div></div>';
    }

    function get_manager_modification_links()
    {
        $toolbar = new Toolbar();
        
        $toolbar->add_item(new ToolbarItem(
    				Translation :: get('AddRow'),
    				Theme :: get_common_image_path() . 'action_add.png',
    				$this->get_home_row_creation_url(),
    				ToolbarItem :: DISPLAY_ICON_AND_LABEL
    	));
    	
    	$toolbar->add_item(new ToolbarItem(
    				Translation :: get('AddColumn'),
    				Theme :: get_common_image_path() . 'action_add.png',
    				$this->get_home_column_creation_url(),
    				ToolbarItem :: DISPLAY_ICON_AND_LABEL
    	));
    	
    	$toolbar->add_item(new ToolbarItem(
    				Translation :: get('AddBlock'),
    				Theme :: get_common_image_path() . 'action_add.png',
    				$this->get_home_block_creation_url(),
    				ToolbarItem :: DISPLAY_ICON_AND_LABEL
    	));
        
        return $toolbar->as_html();
    }
}
?>