<?php
/**
 * $Id: exporter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to export a
 * learning object to the IMS CP format.
 */
class RepositoryManagerExporterCpComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run(){
    	$ids = Request::get(RepositoryManager::PARAM_CONTENT_OBJECT_ID);
        if(empty($ids)){
        	$this->display_header();
        	$this->display_error_message(Translation::get('NoObjectsSelected'));
        	$this->display_footer();
        	return;
		}
        $ids = is_array($ids) ? $ids : array($ids);
        
        if ($ids[0] == 'all'){
        	$conditions[] = new EqualityCondition(ContentObject::PROPERTY_OWNER_ID, $this->get_user_id());
            $conditions[] = new EqualityCondition(ContentObject::PROPERTY_STATE, ContentObject::STATE_NORMAL);
            $condition = new AndCondition($conditions);
       	}else{
       		$condition = new InCondition(ContentObject::PROPERTY_ID, $ids, ContentObject::get_table_name());
       	}

       	$los = $this->retrieve_content_objects($condition);
		while ($lo = $los->next_result()){
       		$content_objects[] = $lo;
        }  
        
        $exporter = ContentObjectExport::factory('cp', $content_objects);
        $path = $exporter->export_content_object();

        Filesystem :: copy_file($path, Path :: get(SYS_TEMP_PATH) . $this->get_user_id() . '/package.zip', true);
        $webpath = Path :: get(WEB_TEMP_PATH) . $this->get_user_id() . '/package.zip';

        $this->display_header();
        $this->display_message('<a href="' . $webpath . '">' . Translation::get('Download') . '</a>');
        $this->display_footer();
	}
	        
}












?>