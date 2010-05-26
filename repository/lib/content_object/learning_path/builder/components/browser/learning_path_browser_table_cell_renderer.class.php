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
                    $title_short = '<a href="' . $this->browser->get_url(array(ComplexBuilder :: PARAM_ROOT_CONTENT_OBJECT => $this->browser->get_root(), ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(), 'publish' => Request :: get('publish'))) . '">' . $title_short . '</a>';
                }

                return $title_short;
        }

        return parent :: render_cell($column, $complex_content_object_item, $content_object);
    }

    protected function get_modification_links($complex_content_object_item, $content_object)
    {
        $additional_items = array();
        $parent = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object_item->get_parent());

        if ($content_object->get_type() == LearningPathItem :: get_type_name())
        {
            if ($parent->get_version() == 'chamilo' && $this->count > 1)
            {
                $prerequisites = $complex_content_object_item->get_prerequisites();
                if (!empty($prerequisites))
                {
                    $additional_items[] = array('href' => $this->browser->get_prerequisites_url($complex_content_object_item->get_id()), 'label' => Translation :: get('EditPrerequisites'), 'img' => Theme :: get_common_image_path() . 'action_edit_prerequisites.png');
                }
                else
                {
                    $additional_items[] = array('href' => $this->browser->get_prerequisites_url($complex_content_object_item->get_id()), 'label' => Translation :: get('BuildPrerequisites'), 'img' => Theme :: get_common_image_path() . 'action_build_prerequisites.png');
                }
            }

            if ($this->lpi_ref_object->get_type() == Assessment :: get_type_name())
            {
                $additional_items[] = array('href' => $this->browser->get_mastery_score_url($complex_content_object_item->get_id()), 'label' => Translation :: get('SetMasteryScore'), 'img' => Theme :: get_common_image_path() . 'action_quota.png');
            }
        }

        $toolbar_data = array();

        $edit_url = $this->browser->get_complex_content_object_item_edit_url($complex_content_object_item, $this->browser->get_root());
        if ($complex_content_object_item->is_extended() || get_parent_class($this->browser) == 'ComplexBuilder')
        {
            $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('EditNA'), 'img' => Theme :: get_common_image_path() . 'action_edit_na.png');
        }

        if ($parent->get_version() == 'chamilo')
        {

            $delete_url = $this->browser->get_complex_content_object_item_delete_url($complex_content_object_item, $this->browser->get_root());
            $moveup_url = $this->browser->get_complex_content_object_item_move_url($complex_content_object_item, $this->browser->get_root(), RepositoryManager :: PARAM_DIRECTION_UP);
            $movedown_url = $this->browser->get_complex_content_object_item_move_url($complex_content_object_item, $this->browser->get_root(), RepositoryManager :: PARAM_DIRECTION_DOWN);
			$change_parent_url = $this->browser->get_complex_content_object_parent_changer_url($complex_content_object_item, $this->browser->get_root());
			
            $toolbar_data[] = array('href' => $delete_url, 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
		 	$toolbar_data[] = array('href' => $change_parent_url, 'label' => Translation :: get('ChangeParent'), 'img' => Theme :: get_common_image_path() . 'action_move.png');
		 	
            $allowed = $this->check_move_allowed($complex_content_object_item);

            if ($allowed["moveup"])
            {
                $toolbar_data[] = array('href' => $moveup_url, 'label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up.png');
            }
            else
            {
                $toolbar_data[] = array('label' => Translation :: get('MoveUpNA'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');

            }

            if ($allowed["movedown"])
            {
                $toolbar_data[] = array('href' => $movedown_url, 'label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down.png');
            }
            else
            {
                $toolbar_data[] = array('label' => Translation :: get('MoveDownNA'), 'img' => Theme :: get_common_image_path() . 'action_down_na.png');
            }
        }

        $toolbar_data = array_merge($toolbar_data, $additional_items);

        return Utilities :: build_toolbar($toolbar_data);

    //return parent :: get_modification_links($complex_content_object_item, $additional_items);
    }
}
?>