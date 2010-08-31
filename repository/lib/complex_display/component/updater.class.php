<?php
/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

class ComplexDisplayComponentUpdaterComponent extends ComplexDisplayComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $selected_complex_content_object_item = $this->get_selected_complex_content_object_item();
            $rdm = RepositoryDataManager :: get_instance();
            
            $content_object = $rdm->retrieve_content_object($selected_complex_content_object_item->get_ref());
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_selected_complex_content_object_item_id(), ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id())));
            
            if ($form->validate())
            {
                $form->update_content_object();
                if ($form->is_version())
                {
                    $old_id = $selected_complex_content_object_item->get_ref();
                    $new_id = $content_object->get_latest_version()->get_id();
                    $selected_complex_content_object_item->set_ref($new_id);
                    $selected_complex_content_object_item->update();
                    
                    $children = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $old_id, ComplexContentObjectItem :: get_table_name()));
                    while ($child = $children->next_result())
                    {
                        $child->set_parent($new_id);
                        $child->update();
                    }
                }
                
                $message = htmlentities(Translation :: get('ContentObjectUpdated'));
                
                $params = array();
                $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
                $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ComplexDisplay :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;
                
                $this->redirect($message, '', $params);
            
            }
            else
            {
                $trail = BreadcrumbTrail :: get_instance();
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_selected_complex_content_object_item_id(), ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id())), Translation :: get('EditWikiPage')));
                
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
    }
}
?>