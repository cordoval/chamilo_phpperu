<?php
/**
 * $Id: learning_path_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component.browser
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LearningPathBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{
    private $count;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LearningPathBrowserTableCellRenderer($browser, $condition)
    {
        if($condition)
        {
    		$count_conditions[] = $condition;
        }
        
        $subselect_condition = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_TYPE, LearningPath :: get_type_name()));
        $count_conditions[] = new SubselectCondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subselect_condition);
        $count_condition = new AndCondition($count_conditions);
        
    	$this->count = RepositoryDataManager :: get_instance()->count_complex_content_object_items($count_condition);
        parent :: __construct($browser, $condition);
    }

    private $lpi_ref_object;

    // Inherited
    function render_cell($column, $complex_content_object_item)
    {
        $content_object = $this->retrieve_content_object($complex_content_object_item->get_ref());
        $ref_lo = $content_object;
        if ($content_object->get_type() == LearningPathItem :: get_type_name())
        {
            if (! $this->lpi_ref_object || $this->lpi_ref_object->get_id() != $content_object->get_reference())
            {
                $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object->get_reference());
                $this->lpi_ref_object = $content_object;
            }
            else
            {
                $content_object = $this->lpi_ref_object;
            }
        }

        if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($complex_content_object_item, $ref_lo);
        }

        switch ($column->get_name())
        {
            case Translation :: get(Utilities :: underscores_to_camelcase(ContentObject :: PROPERTY_TITLE)) :
                $title = htmlspecialchars($content_object->get_title());
                $title_short = $title;

                $title_short = Utilities :: truncate_string($title_short, 53, false);

                if ($content_object->get_type() == LearningPath :: get_type_name())
                {
                    $title_short = '<a href="' . $this->browser->get_url(array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id())) . '">' . $title_short . '</a>';
                }
                else
                {
	        		if ($content_object->is_complex_content_object())
	                {
	                    $url = Path :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=complex_builder&' . RepositoryManager :: PARAM_CONTENT_OBJECT_ID . '=' . $content_object->get_id();
	                	$title_short = '<a href="#" onclick="javascript:openPopup(\'' . $url . '\'); return false">' . $title_short . '</a>';
	                }
	                else
	                {
	                	$title_short = '<a href="' . $this->browser->get_complex_content_object_item_view_url($complex_content_object_item->get_id()) . '">' . $title_short . '</a>';
	                }
                }

                return $title_short;
        }

        return parent :: render_cell($column, $complex_content_object_item, $content_object);
    }

    protected function get_modification_links($complex_content_object_item, $content_object)
    {
        $toolbar = new Toolbar();
        $parent = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object_item->get_parent());

        if ($content_object->get_type() == LearningPathItem :: get_type_name())
        {
            if ($parent->get_version() == 'chamilo' && $this->count > 1)
            {
                $prerequisites = $complex_content_object_item->get_prerequisites();
                if (!empty($prerequisites))
                {
                    $toolbar->add_item(new ToolbarItem(
        				Translation :: get('EditPrerequisites'), 
        				Theme :: get_common_image_path().'action_edit_prerequisites.png', 
						$this->browser->get_prerequisites_url($complex_content_object_item->get_id()), 
						ToolbarItem :: DISPLAY_ICON
					));
                }
                else
                {
                	$toolbar->add_item(new ToolbarItem(
        				Translation :: get('BuildPrerequisites'), 
        				Theme :: get_common_image_path().'action_build_prerequisites.png', 
						$this->browser->get_prerequisites_url($complex_content_object_item->get_id()), 
						ToolbarItem :: DISPLAY_ICON
					));
                }
            }

            if ($this->lpi_ref_object->get_type() == Assessment :: get_type_name())
            {
                	$toolbar->add_item(new ToolbarItem(
        				Translation :: get('SetMasteryScore'), 
        				Theme :: get_common_image_path().'action_quota.png', 
						$this->browser->get_mastery_score_url($complex_content_object_item->get_id()), 
						ToolbarItem :: DISPLAY_ICON
					));
            }
        }

        $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'), 
        			Theme :: get_common_image_path().'action_edit.png', 
					$this->browser->get_complex_content_object_item_edit_url($complex_content_object_item->get_id()), 
					ToolbarItem :: DISPLAY_ICON
		));

        if ($parent->get_version() == 'chamilo')
        {

            $delete_url = $this->browser->get_complex_content_object_item_delete_url($complex_content_object_item->get_id());
            $moveup_url = $this->browser->get_complex_content_object_item_move_url($complex_content_object_item->get_id(), RepositoryManager :: PARAM_DIRECTION_UP);
            $movedown_url = $this->browser->get_complex_content_object_item_move_url($complex_content_object_item->get_id(), RepositoryManager :: PARAM_DIRECTION_DOWN);
			$change_parent_url = $this->browser->get_complex_content_object_parent_changer_url($complex_content_object_item->get_id());
			
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Delete'), 
        			Theme :: get_common_image_path().'action_delete.png', 
					$delete_url, 
					ToolbarItem :: DISPLAY_ICON,
					true
			));
			$toolbar->add_item(new ToolbarItem(
        			Translation :: get('ChangeParent'), 
        			Theme :: get_common_image_path().'action_move.png', 
					$change_parent_url, 
					ToolbarItem :: DISPLAY_ICON
			));
		 	
            $allowed = $this->check_move_allowed($complex_content_object_item);

            if ($allowed["moveup"])
            {
               	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('MoveUp'), 
        			Theme :: get_common_image_path().'action_up.png', 
					$moveup_url, 
					ToolbarItem :: DISPLAY_ICON
				));
            }
            else
            {
            	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('MoveUpNA'), 
        			Theme :: get_common_image_path().'action_up_na.png', 
					null, 
					ToolbarItem :: DISPLAY_ICON
				));

            }

            if ($allowed["movedown"])
            {
               $toolbar->add_item(new ToolbarItem(
        			Translation :: get('MoveDown'), 
        			Theme :: get_common_image_path().'action_down.png', 
					$movedown_url, 
					ToolbarItem :: DISPLAY_ICON
				));
            }
            else
            {
    		   	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('MoveDownNA'), 
        			Theme :: get_common_image_path().'action_down_na.png', 
					null, 
					ToolbarItem :: DISPLAY_ICON
				));
                
            }
        }

		return $toolbar->as_html();

    //return parent :: get_modification_links($complex_content_object_item, $additional_items);
    }
}
?>