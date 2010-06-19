<?php
/**
 * $Id: document_zip_and_download.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */

class DocumentToolZipAndDownloadComponent extends DocumentTool
{
    private $action_bar;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses document tool');
        //$this->display_header($trail, true);
        $archive_url = $this->create_document_archive();
        
        $this->send_as_download($archive_url);
        Filesystem :: remove($archive_url);
    }

    private function create_document_archive()
    {
        $parent = $this->get_parent();
        $count = 0;
        
        $category_id = $parent->get_parameter(WeblcmsManager :: PARAM_CATEGORY);
        if (! isset($category_id) || is_null($category_id) || strlen($category_id) == 0)
        {
            $category_id = 0;
        }
        $category_folder_mapping = $this->create_folder_structure($category_id);
        $datamanager = WeblcmsDataManager :: get_instance();
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $user_id = null;
            $course_groups = null;
        }
        else
        {
        	$user_id = $this->get_user_id();
            $course_groups = $this->get_course_groups();
                
            $course_group_ids = array();
                
            foreach($course_groups as $course_group)
            {
             	$course_group_ids[] = $course_group->get_id();
            }
        }
        $target_path = current($category_folder_mapping);
        foreach ($category_folder_mapping as $category_id => $dir)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'document');
            $conditions[] = new InCondition(ContentObjectPublication :: PROPERTY_CATEGORY_ID, $category_id);
            
            /*$access = array();
            if (! empty($user_id))
            {
                $access[] = new InCondition('user_id', $user_id, $datamanager->get_alias('content_object_publication_user'));
            }
            if (! empty($course_group_ids))
            {
                $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_alias('content_object_publication_course_group'));
            }
            
            $conditions[] = new OrCondition($access);*/
            
            $access = array();
	        if($user_id)
	        {
	    		$access[] = new InCondition(ContentObjectPublicationUser :: PROPERTY_USER, $user_id, ContentObjectPublicationUser :: get_table_name());
	        }
	    	
	    	if(count($course_group_ids) > 0)
	    	{
	        	$access[] = new InCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, $course_group_ids, ContentObjectPublicationCourseGroup :: get_table_name());
	    	}
	        	
	        if (! empty($user_id) || ! empty($course_group_ids))
	        {
	            $access[] = new AndCondition(array(
	            			new EqualityCondition(ContentObjectPublicationUser :: PROPERTY_USER, null, ContentObjectPublicationUser :: get_table_name()), 
	            			new EqualityCondition(ContentObjectPublicationCourseGroup :: PROPERTY_COURSE_GROUP_ID, null, ContentObjectPublicationCourseGroup :: get_table_name())));
	        }
	        
	        $conditions[] = new OrCondition($access);
            
            $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Document :: get_type_name());
            $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
            $condition = new AndCondition($conditions);
            
            $publications = $datamanager->retrieve_content_object_publications($condition);
            $count += $publications->size();
            while ($publication = $publications->next_result())
            {
                $document = $publication->get_content_object();
                $document_path = $document->get_full_path();
                $archive_file_location = $dir . '/' . Filesystem :: create_unique_name($dir, $document->get_filename());
                Filesystem :: copy_file($document_path, $archive_file_location);
            }
        }
        
        if ($count == 0)
        {
            $this->display_header(BreadcrumbTrail :: get_instance());
            $this->get_parent()->display_warning_message(Translation :: get('NoDocumentsPublished'));
            $this->display_footer();
            exit();
        }
        
        $compression = Filecompression :: factory();
        $archive_file = $compression->create_archive($target_path);
        Filesystem :: remove($target_path);
        $archive_url = Path :: get(SYS_PATH) . str_replace(DIRECTORY_SEPARATOR, '/', str_replace(realpath($this->get_parent()->get_path(SYS_PATH)), '', $archive_file));
        return $archive_url;
    }

    /**
     * Creates a folder structure from the given categories.
     * @param array|int $categories
     * @param array $category_folder_mapping
     * @param $path
     * @return array An array mapping the category id to the folder.
     */
    private function create_folder_structure($parent_cat, &$category_folder_mapping = array(), $path = null)
    {
        if (is_null($path))
        {
            $path = realpath(Path :: get(SYS_TEMP_PATH)); //dump($path);
            $path = Filesystem :: create_unique_name($path . '/weblcms_document_download_' . $this->get_parent()->get_course_id());
            $category_folder_mapping[$parent_cat] = $path;
            Filesystem :: create_dir($path);
            $parent = $this->get_parent();
            $course = $parent->get_course_id();
            $tool = $parent->get_parameter(WeblcmsManager :: PARAM_TOOL);
            
            $conditions[] = new EqualityCondition('course_id', $course);
            $conditions[] = new EqualityCondition('tool', $tool);
            $conditions[] = new EqualityCondition('parent_id', $parent_cat);
            $condition = new AndCondition($conditions); //dump($condition);
            

            $categories = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication_categories($condition);
            
            while ($category = $categories->next_result())
            {
                $category_path = Filesystem :: create_unique_name($path . '/' . $category->get_name());
                $category_folder_mapping[$category->get_id()] = $category_path;
                Filesystem :: create_dir($category_path);
                $this->create_folder_structure($category->get_id(), $category_folder_mapping, $category_path);
            }
        }
        return $category_folder_mapping;
    }

    function send_as_download($file)
    {
        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Cache-Control: public');
        header('Pragma: no-cache');
        header('Content-type: application/octet-stream');
        //header('Content-Type: application/force-download');
        header('Content-length: ' . filesize($file));
        if (preg_match("/MSIE 5.5/", $_SERVER['HTTP_USER_AGENT']))
        {
            header('Content-Disposition: filename=files.zip');
        }
        else
        {
            header('Content-Disposition: attachment; filename=files.zip');
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
        {
            header('Pragma: ');
            header('Cache-Control: ');
            header('Cache-Control: public'); // IE cannot download from sessions without a cache
        }
        header('Content-Description: files.zip');
        header('Content-transfer-encoding: binary');
        $fp = fopen($file, 'r');
        fpassthru($fp);
        return true;
    }
}

?>