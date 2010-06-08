<?php
/**
 * $Id: forum_post_quoter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum.component
 */
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayForumPostQuoterComponent extends ForumDisplay
{

    function run()
    {
        $rdm = RepositoryDataManager :: get_instance();
            
        $quote_item = $this->get_selected_complex_content_object_item();
        $quote_lo = $rdm->retrieve_content_object($quote_item->get_ref());
            
        $content_object = new AbstractContentObject(ForumPost :: get_type_name(), $this->get_user_id());
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE, $content_object, 'create', 'post', 
            	$this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_QUOTE_FORUM_POST, 
            	ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id(),
            	ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $quote_item->get_id())));
            
        if (substr($quote_lo->get_title(), 0, 3) == 'RE:')
        {
            $reply = $quote_lo->get_title();
        }
        else
        {
            $reply = 'RE: ' . $quote_lo->get_title();
        }
            
        $defaults['title'] = $reply;
        $defaults['description'] = '[quote="' . UserDataManager :: get_instance()->retrieve_user($quote_lo->get_owner_id())->get_fullname() . '"]' . $quote_lo->get_description() . '[/quote]';
            
        $form->setParentDefaults($defaults);
            
        if ($form->validate())
        {
            $object = $form->create_content_object();
            $complex_content_object_item = ComplexContentObjectItem :: factory(ForumPost :: get_type_name());
                
            $complex_content_object_item->set_ref($object->get_id());
            $complex_content_object_item->set_user_id($this->get_user_id());
            $complex_content_object_item->set_parent($this->get_complex_content_object_item()->get_ref());
            $complex_content_object_item->set_display_order($rdm->select_next_display_order($complex_content_object_item->get_parent()));
                
            if ($quote_item)
            {
                $complex_content_object_item->set_reply_on_post($quote_item->get_id());
            }
                
            $complex_content_object_item->create();
            $this->my_redirect();
        }
        else
        {
            $this->display_header($this->get_complex_content_object_breadcrumbs());
            $form->display();
            $this->display_footer();
        }
    }

    private function my_redirect($pid, $cid)
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));
        
        $params = array();
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_VIEW_TOPIC;
        $params[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        
        $this->redirect($message, false, $params);
    }

}
?>