<?php
/**
 * $Id: forum_topic_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_display.forum.component
 */

require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayForumTopicCreatorComponent extends ForumDisplay
{

    function run()
    {
        $pub = new RepoViewer($this, ForumTopic :: get_type_name(), RepoViewer :: SELECT_MULTIPLE, array(), false);
        $pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ForumDisplay :: ACTION_CREATE_TOPIC);
        $pub->set_parameter(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->get_complex_content_object_item_id());
        $pub->parse_input_from_table();
            
        if (!$pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
                
            $this->display_header($this->get_complex_content_object_breadcrumbs());
            echo implode("\n", $html);
            $this->display_footer();
        }
        else
        {
            $object_id = $pub->get_selected_objects();
                
            if (! is_array($object_id))
            {
                $object_id = array($object_id);
            }
                
            foreach ($object_id as $key => $value)
            {
                $cloi = ComplexContentObjectItem :: factory(ForumTopic :: get_type_name());
                    
            	if ($this->get_complex_content_object_item())
                {
                    $cloi->set_parent($this->get_complex_content_object_item()->get_ref());
                }
                else
                {
                    $cloi->set_parent($this->get_root_content_object_id());
                }
                        
                $cloi->set_ref($value);
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($cloi->get_parent()));
                      
                $cloi->create();
            }
                
            $this->my_redirect();
        }
    }

    private function my_redirect($pid, $forum, $is_subforum)
    {
        $message = htmlentities(Translation :: get('ForumTopicCreated'));
        
        $params = array();
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_FORUM;
		$params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        
        $this->redirect($message, false, $params);
    }

}
?>