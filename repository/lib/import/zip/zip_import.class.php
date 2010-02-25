<?php
/**
 * $Id: csv_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.csv
 */

/**
 * import documents from zip file
 */
class ZipImport extends ContentObjectImport
{
    private $rdm;
    private $created_categories;

    function ZipImport($content_object_file, $user, $category)
    {
        parent :: __construct($content_object_file, $user, $category);
    	$this->rdm = RepositoryDataManager :: get_instance();
    	$this->created_categories = array();
    }

    public function import_content_object()
    {
        $file = $this->get_content_object_file();
        
        $zip = Filecompression :: factory();
        $extracted_files_dir = $zip->extract_file($file['tmp_name']);
        
        $entries = Filesystem :: get_directory_content($extracted_files_dir);
        
        $failures = 0;
        
     	foreach ($entries as $entry)
        {
            $path = str_replace(realpath($extracted_files_dir), '', realpath($entry));
            if (is_dir($entry))
            {
                if(!$this->create_category($path))
                {
                	$failures++;
                }
            }
            elseif (is_file($entry))
            {
             	if (isset($this->created_categories[dirname($path)]))
                {
                    $parent_id = $this->created_categories[dirname($path)];
                }
                else
                {
                	$parent_id = $this->get_category();
                }
                
                $this->create_content_object(basename($path), $entry, $parent_id);
            }
        
        }
        
        Filesystem :: remove($extracted_files_dir);
        
        return ($failures == 0);
    }
    
    private function create_category($path)
    {
    	//Check for existing category
        $condition = new EqualityCondition(RepositoryCategory :: PROPERTY_NAME, basename($path));
        $categories = $this->rdm->retrieve_categories($condition);
        $category = $categories->next_result();
        
        if ($category == null)
        {
            $category = new RepositoryCategory();
            $category->set_name(basename($path));
            if (isset($this->created_categories[dirname($path)]))
            {
                $category->set_parent($this->created_categories[dirname($path)]);
            }
            else
            {
            	$category->set_parent($this->get_category());
            }
            $category->set_user_id($this->get_user()->get_id());
            $succes = $category->create();
        }
        else
        {
        	$succes = true;
        }
        
        $this->created_categories[$path] = $category->get_id();
        
        return $succes;
    }
    
    private function create_content_object($filename, $path, $parent)
    {
    	$document = new Document();
        $document->set_title($filename);
        $document->set_description($filename);
        $document->set_owner_id($this->get_user()->get_id());
        $document->set_parent_id($parent);
        $document->set_filename($filename);
        $document->set_temporary_file_path($path);
        return $document->create();
    }

}
?>