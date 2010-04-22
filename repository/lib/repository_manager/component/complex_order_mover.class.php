<?php
/**
 * $Id: complex_order_mover.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerComplexOrderMoverComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(RepositoryManager :: PARAM_CLOI_ID);
        $root = Request :: get(RepositoryManager :: PARAM_CLOI_ROOT_ID);
        $direction = Request :: get(RepositoryManager :: PARAM_MOVE_DIRECTION);
        $succes = true;
        
        if (isset($id))
        {
            $cloi = $this->retrieve_complex_content_object_item($id);
            $parent = $cloi->get_parent();
            $display_order = $cloi->get_display_order();
            $new_place = ($display_order + ($direction == RepositoryManager :: PARAM_DIRECTION_UP ? - 1 : 1));
            $cloi->set_display_order($new_place);
            
            $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, $new_place);
            $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name());
            $condition = new AndCondition($conditions);
            $items = $this->retrieve_complex_content_object_items($condition);
            $new_cloi = $items->next_result();
            $new_cloi->set_display_order($display_order);
            
            if (! $cloi->update() || ! $new_cloi->update())
            {
                $succes = false;
            }
            
            $this->redirect($succes ? Translation :: get('ComplexContentObjectItemsMoved') : Translation :: get('ComplexContentObjectItemsNotMoved'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $parent, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root, 'publish' => Request :: get('publish'), 'clo_action' => 'organise'));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>