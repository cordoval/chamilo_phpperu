<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */
require_once dirname(__FILE__) . '/../../forum.class.php';

class ForumBuilderMoverComponent extends ForumBuilder
{
    function run()
    {
        $id = Request :: get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $direction = Request :: get(ComplexBuilder :: PARAM_DIRECTION);
        $succes = true;

        if (isset($id))
        {
            $rdm = RepositoryDataManager :: get_instance();
            $complex_content_object_item = $rdm->retrieve_complex_content_object_item($id);
            $parent = $complex_content_object_item->get_parent();
            
            $conditions = array();
            $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent);
            $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Forum :: get_type_name());
	        $conditions[] = new SubSelectcondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subcondition);
	        $condition = new AndCondition($conditions);
            $max = $rdm->count_complex_content_object_items($condition);

            $display_order = $complex_content_object_item->get_display_order();
            $new_place = ($display_order + ($direction == RepositoryManager :: PARAM_DIRECTION_UP ? - 1 : 1));
            if ($new_place > 0 && $new_place <= $max)
            {
                $complex_content_object_item->set_display_order($new_place);
                $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_DISPLAY_ORDER, $new_place, ComplexContentObjectItem :: get_table_name());
                $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name());
                $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Forum :: get_type_name());
	            $conditions[] = new SubSelectcondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subcondition);
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