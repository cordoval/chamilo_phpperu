<?php
/**
 * $Id: forum_subforum_editor.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum.component
 */
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayForumSubforumEditorComponent extends ForumDisplay
{

    function run()
    {
        if ($this->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $selected_complex_content_object_item = $this->get_selected_complex_content_object_item();
            
            $url = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_EDIT_SUBFORUM, 
            							ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
            							ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $selected_complex_content_object_item->get_id()));
            
            $datamanager = RepositoryDataManager :: get_instance();
            $forum_object = $datamanager->retrieve_content_object($selected_complex_content_object_item->get_ref());
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $forum_object, 'edit', 'post', $url);
            
            if ($form->validate())
            {
                $form->update_content_object();
                if ($form->is_version())
                {
                    $old_id = $selected_complex_content_object_item->get_ref();
                    $new_id = $forum_object->get_latest_version()->get_id();
                    $selected_complex_content_object_item->set_ref($new_id);
                    $selected_complex_content_object_item->update();
                    
                    $children = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $old_id, ComplexContentObjectItem :: get_table_name()));
                    while ($child = $children->next_result())
                    {
                        $child->set_parent($new_id);
                        $child->update();
                    }
                }
                
                $this->my_redirect();
            }
            else
            {
                $this->display_header($this->get_complex_content_object_breadcrumbs());
                $form->display();
                $this->display_footer();
            }
        }
    }

    private function my_redirect($pid, $is_subforum, $forum)
    {
        $message = htmlentities(Translation :: get('SubforumEdited'));
        
        $params = array();
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
        $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        
        $this->redirect($message, '', $params);
    }

}
?>