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
        $id = Request :: get(ComplexBuilder :: PARAM_SELECTED_CONTENT_OBJECT_ITEM_ID);
        $root = Request :: get(ComplexBuilder :: PARAM_ROOT_CONTENT_OBJECT);
        $parent_complex_content_object_item = Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $direction = Request :: get(ComplexBuilder :: PARAM_DIRECTION);
        $succes = true;

        if (isset($id))
        {
            $rdm = RepositoryDataManager :: get_instance();
            $complex_content_object_item = $rdm->retrieve_complex_content_object_item($id);
            $parent = $complex_content_object_item->get_parent();
            $max = $rdm->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent));

            $display_order = $complex_content_object_item->get_display_order();
            $new_place = ($display_order + ($direction == RepositoryManager :: PARAM_DIRECTION_UP ? - 1 : 1));

            if ($new_place > 0 && $new_place <= $max)
            {
                $complex_content_object_item->set_display_order($new_place);

                $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, $new_place, ComplexContentObjectItem :: get_table_name());
                $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name());
                $condition = new AndCondition($conditions);
                $items = $rdm->retrieve_complex_content_object_items($condition);
                $new_complex_content_object_item = $items->next_result();
                $new_complex_content_object_item->set_display_order($display_order);

                if (! $complex_content_object_item->update() || ! $new_complex_content_object_item->update())
                {
                    $succes = false;
                }
            }

            if ($parent == $root)
            {
                $parent = null;
            }

            $this->redirect($succes ? Translation :: get('ComplexContentObjectItemsMoved') : Translation :: get('ComplexContentObjectItemsNotMoved'), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>