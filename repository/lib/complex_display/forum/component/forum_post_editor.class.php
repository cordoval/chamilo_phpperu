<?php
/**
 * $Id: forum_post_editor.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum.component
 */

class ForumDisplayForumPostEditorComponent extends ForumDisplayComponent
{

    function run()
    {
        if ($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
        {
            $cid = Request :: get('cid');
            $pid = Request :: get('pid');
            $post = Request :: get('post');
            
            if (! $pid || ! $cid || ! $post)
            {
                //trail here
                $this->display_error_message(Translation :: get('ObjectNotSelected'));
            }
            
            $url = $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_EDIT_FORUM_POST, 'cid' => $cid, 'pid' => $pid, 'post' => $post));
            
            $datamanager = RepositoryDataManager :: get_instance();
            $cloi = $datamanager->retrieve_complex_content_object_item($post);
            $content_object = $datamanager->retrieve_content_object($cloi->get_ref());
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $url);
            
            if ($form->validate() || Request :: get('validated'))
            {
                $form->update_content_object();
                if ($form->is_version())
                {
                    $cloi->set_ref($content_object->get_latest_version()->get_id());
                    $cloi->update();
                }
                
                if ($cloi->get_display_order() == 1)
                {
                    $parent = $datamanager->retrieve_content_object($cloi->get_parent());
                    $parent->set_title($content_object->get_title());
                    $parent->update();
                }
                
                $message = htmlentities(Translation :: get('ForumPostUpdated'));
                
                $params = array();
                $params['pid'] = $pid;
                $params['cid'] = $cid;
                $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_TOPIC;
                
                $this->redirect($message, '', $params);
            
            }
            else
            {
                //trail here
                $form->display();
            }
        
        }
    }

}
?>