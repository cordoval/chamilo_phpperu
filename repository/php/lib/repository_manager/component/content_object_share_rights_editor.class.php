<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of content_object_share_browser
 *
 * @author Pieterjan Broekaert
 */
class RepositoryManagerContentObjectShareRightsEditorComponent extends RepositoryManager
{

    function run()
    {
        $ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        $target_users = Request :: get(self :: PARAM_TARGET_USER);
        $target_groups = Request :: get(self :: PARAM_TARGET_GROUP);
        
        if($ids && ($target_users || $target_groups))
        {
	        if(!is_array($ids))
	        {
	        	$ids = array($ids);
	        }
	        
	        if($target_users && !is_array($target_users))
	        {
	        	$target_users = array($target_users);
	        }
	        
        	if($target_groups && !is_array($target_groups))
	        {
	        	$target_groups = array($target_groups);
	        }
        
        	$share_form = new ContentObjectShareForm(ContentObjectShareForm :: TYPE_EDIT, $ids, $this->get_user(), $this->get_url());
        	
        	if(count($target_users) + count($target_groups) == 1 && count($ids) == 1)
        	{
        		$share_form->set_default_rights($target_users, $target_groups);
        	}
	        
	        if ($share_form->validate())
	        {
	            $succes = $share_form->update_content_object_share($target_users, $target_groups);
	            $message = $succes ? Translation :: get('ContentObjectShared') : Translation :: get('ContentObjectNotShared');
	            $this->redirect($message, !$succes, array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_CONTENT_OBJECT_SHARE_BROWSER, self :: PARAM_TARGET_GROUP => null, self :: PARAM_TARGET_USER => null));
	        }
	        else
	        {
	            $this->display_header();
	            echo $this->display_content_objects($ids);
	            $share_form->display();
	            $this->display_footer();
	        }
        }
        else
        {
        	$this->display_error_page(Translation :: get('NoObjectsSelected'));
        }
    }
    
	function display_content_objects($content_object_ids)
    {
    	$html = array();
        $html[] = '<div class="content_object padding_10">';
        $html[] = '<div class="title">' . Translation :: get('SelectedContentObjects') . '</div>';
        $html[] = '<div class="description">';
        $html[] = '<ul class="attachments_list">';

        foreach ($content_object_ids as $object_id)
        {
            $object = $this->retrieve_content_object($object_id);
            $html[] = '<li><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $object->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($object->get_type()) . 'TypeName')) . '"/> ' . $object->get_title() . '</li>';
        }

        $html[] = '</ul>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
    
    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_content_object_share_rights_creator');
    }
    
    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID, self :: PARAM_TARGET_GROUP, self :: PARAM_TARGET_USER);
    }

}

?>
