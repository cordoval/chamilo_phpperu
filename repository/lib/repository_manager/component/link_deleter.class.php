<?php
/**
 * $Id: link_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * link to a content object
 */
class RepositoryManagerLinkDeleterComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$type = Request :: get(RepositoryManager :: PARAM_LINK_TYPE);
    	$object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    	$link_ids = Request :: get(RepositoryManager :: PARAM_LINK_ID);
    	
    	if(!is_array($link_ids))
    	{
    		$link_ids = array($link_ids);
    	}
    	
    	if($type && $object_id && count($link_ids) > 0)
    	{
    		switch($type)
    		{
    			case LinkBrowserTable :: TYPE_PUBLICATIONS:
    				list($message, $is_error_message) = $this->delete_publication($object_id, $link_ids);
    				break;
    			case LinkBrowserTable :: TYPE_PARENTS:
    				list($message, $is_error_message) = $this->delete_complex_wrapper($object_id, $link_ids);
    				break;
    			case LinkBrowserTable :: TYPE_CHILDREN:
    				list($message, $is_error_message) = $this->delete_complex_wrapper($object_id, $link_ids);
    				break;
    			case LinkBrowserTable :: TYPE_ATTACHMENTS:
    				list($message, $is_error_message) = $this->delete_attachement($object_id, $link_ids);
    				break;
    		}
    		
    		$this->redirect($message, $is_error_message, array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS,
    														   RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object_id));
    		
    	}
    	else
    	{
    		$this->display_error_page(Translation :: get('NoObjectSelected'));
    	}
    }
    
    function delete_publication($object_id, $link_ids)
    {
    	$rdm = RepositoryDataManager :: get_instance();
    	$failures = 0;
    	
    	foreach($link_ids as $link_id)
    	{
    		list($application, $publication_id) = explode("|", $link_id);
    		if(!$rdm->delete_content_object_publication($application, $publication_id))
    			$failures++;
    	}	
    	
    	$message = $this->get_result($failures, count($link_ids), 'PublicationNotDeleted', 'PublicationsNotDeleted', 
    							     'PublicationDeleted', 'PublicationsDeleted');
    	
    	return array($message, ($failures > 0));
    }
    
    function delete_complex_wrapper($object_id, $link_ids)
    {
    	$rdm = RepositoryDataManager :: get_instance();
    	$failures = 0;
    	
    	foreach($link_ids as $link_id)
    	{
    		$item = $rdm->retrieve_complex_content_object_item($link_id);
    		$object = $rdm->retrieve_content_object($item->get_ref());
    		
    		if(!$item->delete())
    		{
    			$failures++;
    			continue;
    		}
    		
    		if($object->get_type() == 'portfolio_item' || $object->get_type() == 'learning_path_item')
    		{
    			if(!$object->delete())
    			{
    				$failures++;
    			}
    		}
    		
    	}
    	
    	$message = $this->get_result($failures, count($link_ids), 'ComplexContentObjectItemNotDeleted', 'ComplexContentObjectItemsNotDeleted', 
    							     'ComplexContentObjectItemDeleted', 'ComplexContentObjectItemsDeleted');
    	
    	return array($message, ($failures > 0));
    }
    
    function delete_attachement($object_id, $link_ids)
    {
    	$rdm = RepositoryDataManager :: get_instance();
    	$failures = 0;
    	
    	foreach($link_ids as $link_id)
    	{
    		$object = $rdm->retrieve_content_object($link_id);
    		if(!$rdm->detach_content_object($object, $object_id))
    			$failures++;
    	}
    	
    	$message = $this->get_result($failures, count($link_ids), 'AttachmentNotDeleted', 'AttachmentsNotDeleted', 
    							     'AttachmentDeleted', 'AttachmentsDeleted');
    	
    	return array($message, ($failures > 0));
    }
    
    function delete_include($object_id, $link_ids)
    {
    	$rdm = RepositoryDataManager :: get_instance();
    	$failures = 0;
    	
    	foreach($link_ids as $link_id)
    	{
    	}
    	
    	$message = $this->get_result($failures, count($link_ids), 'PublicationNotDeleted', 'PublicationsNotDeleted', 
    							     'PublicationDeleted', 'PublicationsDeleted');
    	
    	return array($message, ($failures > 0));
    }
}
?>