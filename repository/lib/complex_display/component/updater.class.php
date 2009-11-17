<?php
/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */

class ComplexDisplayUpdaterComponent extends ComplexDisplayComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $cid = Request :: get('selected_cloi') ? Request :: get('selected_cloi') : $_POST['selected_cloi'];
            $pid = Request :: get('pid') ? Request :: get('pid') : $_POST['pid'];
            $selected_cloi = Request :: get('selected_cloi') ? Request :: get('selected_cloi') : $_POST['selected_cloi'];
            
            $datamanager = RepositoryDataManager :: get_instance();
            $cloi = $datamanager->retrieve_complex_content_object_item($selected_cloi);
            
            $cloi->set_default_property('user_id', $this->get_user_id());
            $content_object = $datamanager->retrieve_content_object($cloi->get_ref());
            $content_object->set_default_property('owner', $this->get_user_id());
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE, 'selected_cloi' => $selected_cloi, 'selected_cloi' => $cid, 'pid' => $pid)));
            
            if ($form->validate() || Request :: get('validated'))
            {
                $form->update_content_object();
                if ($form->is_version())
                {
                    $old_id = $cloi->get_ref();
                    $new_id = $content_object->get_latest_version()->get_id();
                    $cloi->set_ref($new_id);
                    $cloi->update();
                    
                    $children = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $old_id, ComplexContentObjectItem :: get_table_name()));
                    while ($child = $children->next_result())
                    {
                        $child->set_parent($new_id);
                        $child->update();
                    }
                }
                
                $message = htmlentities(Translation :: get('ContentObjectUpdated'));
                
                $params = array();
                $params['pid'] = Request :: get('pid');
                $params['selected_cloi'] = $cid;
                $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ComplexDisplay :: ACTION_VIEW_CLO;
                $params['display_action'] = 'view_item';
                
                $this->redirect($message, '', $params);
            
            }
            else
            {
                $form->display();
            }
        }
    }
}
?>
