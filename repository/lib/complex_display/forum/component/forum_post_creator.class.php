<?php
/**
 * $Id: forum_post_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_display.forum.component
 */

class ForumDisplayForumPostCreatorComponent extends ForumDisplayComponent
{

    function run()
    {
        if ($this->get_parent()->get_parent()->is_allowed(ADD_RIGHT))
        {
            $pid = Request :: get('pid');
            $cid = Request :: get('cid');
            $reply = Request :: get('reply');
            
            if (! $pid || ! $cid)
            {
                $this->display_error_message(Translation :: get('NoParentSelected'));
            }
            
            $rdm = RepositoryDataManager :: get_instance();
            
            if ($reply)
            {
                $reply_item = $rdm->retrieve_complex_content_object_item($reply);
                $reply_lo = $rdm->retrieve_content_object($reply_item->get_ref(), 'forum_post');
            }
            
            $pub = new RepoViewer($this, 'forum_post', true);
            $pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumDisplay :: ACTION_CREATE_FORUM_POST);
            $pub->set_parameter('pid', $pid);
            $pub->set_parameter('cid', $cid);
            $pub->set_parameter('type', $type);
            $pub->set_parameter('reply', $reply);
            if ($reply_lo)
            {
                if (substr($reply_lo->get_title(), 0, 3) == 'RE:')
                    $reply = $reply_lo->get_title();
                else
                    $reply = 'RE: ' . $reply_lo->get_title();
                
                $pub->set_creation_defaults(array('title' => $reply));
            }
            
            $object_id = Request :: get('object');
            
            if (! isset($object_id))
            {
                $html[] = '<p><a href="' . $this->get_url(array('type' => $type, 'pid' => $pid)) . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
                $html[] = $pub->as_html();
                echo implode("\n", $html);
            }
            else
            {
                $cloi = ComplexContentObjectItem :: factory('forum_post');
                
                $item = $rdm->retrieve_complex_content_object_item($cid);
                
                $cloi->set_ref($object_id);
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_parent($item->get_ref());
                $cloi->set_display_order($rdm->select_next_display_order($item->get_ref()));
                
                if ($reply)
                    $cloi->set_reply_on_post($reply);
                
                $cloi->create();
                $this->my_redirect($pid, $cid);
            }
        
        }
    }

    private function my_redirect($pid, $cid)
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));
        
        $params = array();
        $params['pid'] = $pid;
        $params['cid'] = $cid;
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_TOPIC;
        
        $this->redirect($message, '', $params);
    }

}
?>