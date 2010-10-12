<?php

/**
 * A personal blog layout with the user picture on the side
 */
class PersonalBlogLayout extends BlogLayout
{
	function display_blog_item(ComplexBlogItem $complex_blog_item)
    {
		$blog_item = $complex_blog_item->get_ref_object();
    	$owner = UserDataManager :: get_instance()->retrieve_user($blog_item->get_owner_id());
		
		if($owner)
		{
			$name = $owner->get_fullname();
			$picture = $owner->get_full_picture_url();			
		}
		else
		{
			$name = Translation :: get('AuthorUnknown');
			$picture = Theme :: get_common_image_path() . 'unknown.jpg';
		}
		
		$html = array();
		
    	$html[] = '<div class="blog_item">';
        $html[] = '<div class="information_box">';
        $html[] = '<img class="user_image" src="' . $picture . '" /><br /><br />';
        $html[] = $name . '<br />';
        $html[] = DatetimeUtilities :: format_locale_date(null, $complex_blog_item->get_add_date());
        $html[] = '</div>';
        $html[] = '<div class="message_box">';
        $html[] = '<div class="title">' . $blog_item->get_title() . '</div>';
        $html[] = '<div class="description">';
        $html[] = $blog_item->get_description();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp</div>';
        $html[] = $this->get_attached_content_objects_as_html($blog_item);
        $html[] = '<div class="actions_box">';
        $html[] = '<div class="actions">' . $this->get_blog_item_actions($complex_blog_item) . '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp</div>';
        $html[] = '</div><br />';
        
        return implode("\n", $html);
    }
    
    /**
     * Gets the layout of the attachments list
     * @param BlogItem $blog_item
     */
	function get_attached_content_objects_as_html($blog_item)
    {
        $attachments = $blog_item->get_attached_content_objects();
        if (count($attachments))
        {
            $html[] = '<div class="attachments">';
            $html[] = '<div class="attachments_title">' . htmlentities(Translation :: get('Attachments')) . '</div>';
            Utilities :: order_content_objects_by_title($attachments);
            $html[] = '<ul class="attachments_list">';
            
            foreach ($attachments as $attachment)
            {
                $url = Path :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=attachment_viewer&' . RepositoryManager :: PARAM_CONTENT_OBJECT_ID . '=' . $attachment->get_id();
                $url = 'javascript:openPopup(\'' . $url . '\'); return false;';
                $html[] = '<li><a href="#" onClick="' . $url . '"><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $attachment->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($attachment->get_type()) . 'TypeName')) . '"/> ' . $attachment->get_title() . '</a></li>';
            }
            
            $html[] = '</ul>';
            $html[] = '</div>';
            
            return implode("\n", $html);
            }
            
        return '';
    }
}