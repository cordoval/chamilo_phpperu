<?php
/**
 * $Id: mover.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class ComplexBuilderMoverComponent extends ComplexBuilderComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(ComplexBuilder :: PARAM_SELECTED_CLOI_ID);
        $root = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
        $parent_cloi = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
        $direction = Request :: get(ComplexBuilder :: PARAM_DIRECTION);
        $succes = true;
        
        if (isset($id))
        {
            $rdm = RepositoryDataManager :: get_instance();
            $cloi = $rdm->retrieve_complex_content_object_item($id);
            $parent = $cloi->get_parent();
            $max = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent));
            
            $display_order = $cloi->get_display_order();
            $new_place = ($display_order + ($direction == RepositoryManager :: PARAM_DIRECTION_UP ? - 1 : 1));
            
            if($new_place > 0 && $new_place <= $max)
            { 
	            $cloi->set_display_order($new_place);
	            
	            $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, $new_place, ComplexContentObjectItem :: get_table_name());
	            $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name());
	            $condition = new AndCondition($conditions);
	            $items = $rdm->retrieve_complex_content_object_items($condition);
	            $new_cloi = $items->next_result();
	            $new_cloi->set_display_order($display_order);
	            
	            if (! $cloi->update() || ! $new_cloi->update())
	            {
	                $succes = false;
            	}
            }
            
            if ($parent == $root)
                $parent = null;
            
            $this->redirect($succes ? Translation :: get('ComplexContentObjectItemsMoved') : Translation :: get('ComplexContentObjectItemsNotMoved'), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root, ComplexBuilder :: PARAM_CLOI_ID => $parent_cloi, 'publish' => Request :: get('publish')));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>