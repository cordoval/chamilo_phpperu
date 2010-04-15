<?php
/**
 * $Id: forum_post_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_display.forum.component
 */

class ForumDisplayForumPostCreatorComponent extends ForumDisplayComponent
{

    function run()
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
            
            $pub = new RepoViewer($this, 'forum_post', false, RepoViewer :: SELECT_MULTIPLE, array(), false);
            $pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumDisplay :: ACTION_CREATE_FORUM_POST);
            $pub->set_parameter('pid', $pid);
            $pub->set_parameter('cid', $cid);
            $pub->set_parameter('reply', $reply);
            
            $pub->parse_input_from_table();
            
            if ($reply_lo)
            {
                if (substr($reply_lo->get_title(), 0, 3) == 'RE:')
                    $reply = $reply_lo->get_title();
                else
                    $reply = 'RE: ' . $reply_lo->get_title();
                
                $pub->set_creation_defaults(array('title' => $reply));
            }
            
            if (!$pub->is_ready_to_be_published())
            {
                $html[] = $pub->as_html();
                $this->display_header(new BreadcrumbTrail());
                echo implode("\n", $html);
                $this->display_footer();
            }
            else
            {
                $object_ids = $pub->get_selected_objects();
                
            	if(!is_array($object_ids))
                {
                	$object_ids = array($object_ids);
                }
                	
                $item = $rdm->retrieve_complex_content_object_item($cid);
                	
            	foreach($object_ids as $object_id)
            	{
	            	$cloi = ComplexContentObjectItem :: factory('forum_post');
	                
	                $cloi->set_ref($object_id);
	                $cloi->set_user_id($this->get_user_id());
	                $cloi->set_parent($item->get_ref());
	                $cloi->set_display_order($rdm->select_next_display_order($item->get_ref()));
	                
	                if ($reply)
	                    $cloi->set_reply_on_post($reply);
	                
	                $cloi->create();
            	}
            	
            	$this->my_redirect($pid, $cid);
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