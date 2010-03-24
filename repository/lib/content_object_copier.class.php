<?php
/**
 * $Id: content_object_copier.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */
/**
 * Class that makes it possible to copy a content object between two user repositories. This class takes the following things in account:
 * Simple learning object
 * The children of a complex learning object
 * The included learning objects
 * The attached learning objects
 * The physical files (documents, hotpotatoes, scorm)
 * The references of LearningPathItem & PortfolioItem
 * The LearningPath prerequisites (only for chamilo learning paths)
 * Links to other files in a description field
 *
 * @author Sven Vanpoucke
 */
class ContentObjectCopier
{
    /**
     * The repository data manager
     *
     * @var RepositoryDataManager
     */
    private $rdm;

    /**
     * The target repository
     *
     * @var Int
     */
    private $target_repository;

    /**
     * Counter to count the items that failed while copying
     *
     * @var Int
     */
    private $failed;

    /**
     * Array of already copied content objects in order to not copy content objects twice
     *
     * @var ContentObject[]
     */
    private $created_content_objects;

    /**
     * Array of file references, we need the paths for processing the fixed links in (f)ckeditor fields
     *
     * @var String[]
     */
    private $file_references;
    
    /**
     * Used to save the references in the object numbers
     * @var int[]
     * 
     * Example:
     * $object_numbers[60] = 1;
     */
    private $object_numbers;

    /**
     * Constructor
     * Initialize the repository data manager
     * Set the target repository
     *
     * @param Int $target_repository
     */
    function ContentObjectCopier($target_repository = 0)
    {
        $this->rdm = RepositoryDataManager :: get_instance();
        $this->target_repository = $target_repository;
    }

    /**
     * Copy a content object to the target repository
     *
     * @param Int $co
     * @return Int the amount of content objects that where failed to copy
     */
    function copy_content_object($co)
    {
        $this->failed = 0;

        $this->create_content_object($co);

        return $this->failed;
    }

    /**
     * Create a content object in the target repository
     *
     * @param ContentObject $co
     * @return Int the id of the new created content object
     */
    private function create_content_object($co, $is_version = false)
    {
        $old_co_id = $co->get_id();
        $old_user_id = $co->get_owner_id();

        if (array_key_exists($old_co_id, $this->created_content_objects))
        {
            return $this->created_content_objects[$old_co_id]->get_id();
        }

    	//First we copy the versions so the last version will always be copied last
        if($co->is_latest_version())
        {
            $versions = $this->rdm->retrieve_content_object_versions($co, false);
	        foreach($versions as $version)
	        {
	        	$this->create_content_object($version, true);
	        }
        }
        else
        {
        	if(!$is_version)
        	{
        		return $this->create_content_object($co->get_latest_version());
        	}
        }
            
        // Retrieve includes and attachments
        $includes = $co->get_included_content_objects();
        $attachments = $co->get_attached_content_objects();

        // Replace some properties
        $co->set_owner_id($this->target_repository);
        $co->set_parent_id(0);

        $old_object_number = $co->get_object_number();
    	$object_number_exists = array_key_exists($old_object_number, $this->object_numbers);
        if($object_number_exists)
        {
          	$co->set_object_number($this->object_numbers[$old_object_number]);
       	 	if (!$co->version())
	        {
	            $this->failed ++;
	        }
        }
        else
        {
	        // Create object
        	if (! $co->create())
	        {
	            $this->failed ++;
	        }
	        else
	        {
	        	$this->object_numbers[$old_object_number] = $co->get_object_number();
	        }
        }

        // Add object to created content objects
        $this->created_content_objects[$old_co_id] = $co;

        // Process the children
        if ($co->is_complex_content_object())
        {
            $this->copy_complex_children($old_co_id, $co->get_id());
        }

        // Process the included items and the attachments
        $this->copy_includes($co, $includes);
        $this->copy_attachments($co, $attachments);

        // Process the physical files
        $this->copy_files($co, $old_user_id);

        // Process additional stuff that has not bee processed yet
        $this->process_extra_parameters($co);

        return $co->get_id();
    }

    /**
     * Copy the children of a content object (both items and wrappers)
     *
     * @param Int $old_parent_id
     * @param Int $new_parent_id
     */
    private function copy_complex_children($old_parent_id, $new_parent_id)
    {
        $item_references = array();

        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $old_parent_id, ComplexContentObjectItem :: get_table_name());
        $items = $this->rdm->retrieve_complex_content_object_items($condition);
        while ($item = $items->next_result())
        {
            $co = $this->rdm->retrieve_content_object($item->get_ref());
            $co_id = $this->create_content_object($co);
            $old_id = $item->get_id();

            $item->set_user_id($this->target_repository);
            $item->set_parent($new_parent_id);
            $item->set_ref($co_id);
            $item->create();

            $item_references[$old_id] = $item;
        }

        foreach ($item_references as $item)
        {
            $this->process_extra_parameters_wrapper($item, $item_references);
        }
    }

    /**
     * Copy the included content objects
     *
     * @param ContentObject $co
     * @param ContentObject[]
     */
    private function copy_includes($co, $includes)
    {
        foreach ($includes as $include)
        {
            $object = $this->rdm->retrieve_content_object($include->get_id());
        	$new_include_id = $this->create_content_object($object);
            $co->include_content_object($new_include_id);
        }
    }

    /**
     * Copy the attached content objects
     *
     * @param ContentObject $co
     * @param ContentObject[]
     */
    private function copy_attachments($co, $attachments)
    {
        foreach ($attachments as $attachment)
        {
            $object = $this->rdm->retrieve_content_object($attachment->get_id());
        	$new_attachment_id = $this->create_content_object($object);
            $co->attach_content_object($new_attachment_id);
        }
    }

    /**
     * Copy the physical files
     * @param ContentObject $co;
     */
    private function copy_files($co, $old_user_id)
    {
        $type = $co->get_type();
        switch ($type)
        {
            case 'document' :
                return $this->copy_document_files($co);
            case 'hotpotatoes' :
                return $this->copy_hotpotatoes_files($co, $old_user_id);
            case 'learning_path' :
                if ($co->get_version() == 'SCORM1.2' || $co->get_version() == 'SCORM2004')
                {
                    return $this->copy_scorm_files($co, $old_user_id);
                }
            default :
                return;
        }
    }

    /**
     * Copy the files from the content object type document
     *
     * @param Document $co
     */
    private function copy_document_files($co)
    {
        $base_path = Path :: get(SYS_REPO_PATH);
        $new_path = $this->target_repository . '/' . Text :: char_at($co->get_hash(), 0);
        $new_full_path = $base_path . $new_path;
        Filesystem :: create_dir($new_full_path);

        $new_hash = Filesystem :: create_unique_name($new_full_path, $co->get_hash());
        $new_full_path .= '/' . $new_hash;

        Filesystem :: copy_file($co->get_full_path(), $new_full_path);

        $old_url = $co->get_url();

        $co->set_hash($new_hash);
        $co->set_path($new_path . '/' . $new_hash);
        $co->update();

        $this->file_references[$old_url] = $co->get_url();
    }

    /**
     * Copy the files from the content object type hotpotatoes
     *
     * @param Hotpotatoes $co
     */
    private function copy_hotpotatoes_files($co, $old_user_id)
    {
        $filename = basename($co->get_path());
        $base_path = Path :: get(SYS_HOTPOTATOES_PATH) . $this->target_repository . '/';

        $new_path = Filesystem :: create_unique_name($base_path, dirname($co->get_path()));
        $new_full_path = $base_path . $new_path;
        Filesystem :: create_dir($new_full_path);

        Filesystem :: recurse_copy(Path :: get(SYS_HOTPOTATOES_PATH) . $old_user_id . '/' . dirname($co->get_path()), $new_full_path, false);

        $co->set_path($new_path . '/' . $filename);
        $co->update();
    }

    /**
     * Copy the files from the content object type learning path
     *
     * @param LearningPath $co
     */
    private function copy_scorm_files($co, $old_user_id)
    {
        $base_path = Path :: get(SYS_SCORM_PATH) . $this->target_repository . '/';

        $new_folder = Filesystem :: create_unique_name($base_path, $co->get_path());
        $new_full_path = $base_path . $new_folder;
        Filesystem :: create_dir($new_full_path);

        Filesystem :: recurse_copy(Path :: get(SYS_SCORM_PATH) . $old_user_id . '/' . $co->get_path(), $new_full_path, false);

        $co->set_path($new_folder);
        $co->update();
    }

    /**
     * Process extra parameters (references from learning path item or portfolio item)
     *
     * @param ContentObject $co
     */
    private function process_extra_parameters($co)
    {
        $type = $co->get_type();

        $this->fix_links($co);

        switch ($type)
        {
            case 'learning_path_item' :
                $this->fix_references($co);
                return;
            case 'portfolio_item' :
                return $this->fix_references($co);
            default :
                return;
        }
    }

    /**
     * Method to fix the embedded links in fckeditor fields
     *
     * @param ContentObject $co
     */
    private function fix_links($co)
    {
        if (count($co->get_included_content_objects()) == 0)
        {
            return;
        }

        $fields = $co->get_html_editors();

        //$pattern = '/http:\/\/.*\/files\/repository\/[1-9]*\/[^\"]*/';
        //$pattern = '/http:\/\/.*\/core\.php\?go=document_downloader&display=1&object=[0-9]*&application=repository/';
        $pattern = '/core\.php\?go=document_downloader&display=1&object=[0-9]*&application=repository/';
        foreach ($fields as $field)
        {
            $value = $co->get_default_property($field); dump($value);
            $value = preg_replace_callback($pattern, array($this, 'fix_link_matches'), $value);
            $co->set_default_property($field, $value);
        }

        $co->update();
    }

    private function fix_link_matches($matches)
    {
        return $this->file_references[$matches[0]];
    }

    /**
     * Fix references from learning path item or portfolio item
     *
     * @param ContentObject $co
     */
    private function fix_references($co)
    {
        $reference = $co->get_reference();
        $lo = $this->rdm->retrieve_content_object($reference);
        $newid = $this->create_content_object($lo);

        $co->set_reference($newid);
        $co->update();
    }

    /**
     * Process extra parameters for the wrapper of a content object (prerequisites)
     *
     * @param ComplexContentObjectItem $wrapper
     */
    private function process_extra_parameters_wrapper($wrapper, $item_references)
    {
        $co = $this->rdm->retrieve_content_object($wrapper->get_ref());
        switch ($co->get_type())
        {
            case 'learning_path_item' :
                $co = $this->rdm->retrieve_content_object($co->get_reference());
                if ($co->get_type() != 'scorm_item')
                {
                    return $this->fix_prerequisites($wrapper, $item_references);
                }
            default :
                return;
        }
    }

    /**
     * Fix the prerequisites of the complex learning path item
     *
     * @param ComplexLearningPathItem $wrapper
     */
    private function fix_prerequisites($wrapper, $item_references)
    {
        $this->item_references = $item_references;
        $prerequisites = $wrapper->get_prerequisites();
        $pattern = '/[^()&|~]+/';
        $prerequisites = preg_replace_callback($pattern, array($this, 'handle_matches'), $prerequisites);
        $wrapper->set_prerequisites($prerequisites);
        $wrapper->update();
    }

    /**
     * Pushed item references to global variable because it's not possible to hand over to callback function
     *
     * @var ComplexContentObjectItem[]
     */
    private $item_references;

    /**
     * Handle the matches for the prerequisites preg replace function
     *
     * @param String[] $matches
     * @return Id of the new prerequisite
     */
    private function handle_matches($matches)
    {
        return $this->item_references[$matches[0]]->get_id();
    }
}

?>