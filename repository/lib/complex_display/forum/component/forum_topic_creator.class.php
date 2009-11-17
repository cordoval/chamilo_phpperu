<?php
/**
 * $Id: forum_topic_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_display.forum.component
 */

class ForumDisplayForumTopicCreatorComponent extends ForumDisplayComponent
{

    function run()
    {
        //if($this->get_parent()->get_parent()->is_allowed(ADD_RIGHT))
        {
            $pid = Request :: get('pid');
            $forum = Request :: get('forum');
            $is_subforum = Request :: get('is_subforum');
            
            if (! $pid || ! $forum)
            {
                //trail here
                $this->display_error_message(Translation :: get('NoParentSelected'));
            }
            
            $pub = new RepoViewer($this, 'forum_topic', true, RepoViewer :: SELECT_MULTIPLE, array(), false);
            $pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumDisplay :: ACTION_CREATE_TOPIC);
            $pub->set_parameter('pid', $pid);
            $pub->set_parameter('forum', $forum);
            $pub->set_parameter('is_subforum', $is_subforum);
            $pub->set_redirect(false);
            $pub->parse_input_from_table();
            
            $object_id = Request :: get('object');
            
            if (! isset($object_id))
            {
                $html[] = '<p><a href="' . $this->get_url(array('forum' => $forum, 'pid' => $pid)) . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
                $html[] = $pub->as_html();
                //trail here
                echo implode("\n", $html);
            }
            else
            {
                if (! is_array($object_id))
                {
                    $cloi = ComplexContentObjectItem :: factory('forum_topic');
                    
                    if ($is_subforum)
                    {
                        $subforum = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($forum)->get_ref();
                        $cloi->set_parent($subforum);
                    }
                    else
                    {
                        $cloi->set_parent($forum);
                    }
                    
                    $cloi->set_ref($object_id);
                    $cloi->set_user_id($this->get_user_id());
                    $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($forum));
                    
                    $cloi->create();
                }
                else
                {
                    foreach ($object_id as $key => $value)
                    {
                        $cloi = ComplexContentObjectItem :: factory('forum_topic');
                        
                        if ($is_subforum)
                        {
                            $subforum = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($forum)->get_ref();
                            $cloi->set_parent($subforum);
                        }
                        else
                        {
                            $cloi->set_parent($forum);
                        }
                        
                        $cloi->set_ref($value);
                        $cloi->set_user_id($this->get_user_id());
                        $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($forum));
                        
                        $cloi->create();
                    }
                }
                $this->my_redirect($pid, $forum, $is_subforum);
            }
        
        }
    }

    private function my_redirect($pid, $forum, $is_subforum)
    {
        $message = htmlentities(Translation :: get('ForumTopicCreated'));
        
        $params = array();
        $params['pid'] = $pid;
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
        
        if ($is_subforum)
            $params['forum'] = $forum;
        
        $this->redirect($message, false, $params);
    }

}
?>