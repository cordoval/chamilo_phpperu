<?php
/**
 * $Id: csv_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.csv
 */

/**
 * import documents from zip file
 */
class DocumentImport extends ContentObjectImport
{
    private $rdm;

    function DocumentImport($content_object_file, $user, $category)
    {
        parent :: __construct($content_object_file, $user, $category);
    	$this->rdm = RepositoryDataManager :: get_instance();
    }

    public function import_content_object()
    {
        $category = $this->get_category();
        $file = $this->get_content_object_file(); 
        
        $document = new Document();
        $document->set_title($file['name']);
        $document->set_description($file['name']);
        $document->set_owner_id($this->get_user()->get_id());
        $document->set_parent_id($category);
        $document->set_filename($file['name']);
        $document->set_temporary_file_path($file['tmp_name']);
        return $document->create();
    }

}
?>