<?php
namespace migration;
use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\PlatformSetting;
use common\libraries\Path;
use common\libraries\Text;
use common\libraries\Filesystem;
use repository\content_object\document\Document;
use repository\RepositoryDataManager;

/**
 * $Id: import.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager
 */

/**
 * Abstract migration data class
 * @author Sven Vanpoucke
 */
abstract class MigrationDataClass extends DataClass
{
	/**
	 * The failed / succes message of the validation / conversion
	 * @var String
	 */
	private $message;

	/**
	 * Caching variable for id references
	 * @var $id_references[$table][$old_id] = $new_id;
	 */
	private $id_references;

	/**
	 * Returns the message
	 */
	function get_message()
	{
		return $this->message;
	}

	/**
	 * Sets the message
	 * @param String $message
	 */
	function set_message($message)
	{
		return $this->message = $message;
	}

	/**
	 * Some help functions
	 */

	/**
	 * Creates a failed element object
	 * @param Int $id
	 * @param String $table
	 */
	function create_failed_element($id, $table = null)
	{
		$failed_element = new FailedElement();
		$failed_element->set_failed_id($id);

		if($table)
		{
			$failed_element->set_failed_table_name($table);
		}
		else
		{
			$failed_element->set_failed_table_name($this->get_table_name());
		}

		return $failed_element->create();
	}

	/**
	 * Retrieves a failed element
	 * @param Int $id
	 * @param String $table
	 */
	function get_failed_element($id, $table = null)
	{
		if(!$table)
		{
			$table = $this->get_table_name();
		}

		return MigrationDataManager :: retrieve_failed_element_by_id_and_table($id, $table);
	}

	/**
	 * Creates an id reference object
	 * @param int $old_id
	 * @param int $new_id
	 * @param String $table
	 */
	function create_id_reference($old_id, $new_id, $table = null)
	{
		$id_reference = new IdReference();
		$id_reference->set_old_id($old_id);
		$id_reference->set_new_id($new_id);

		if($table)
		{
			$id_reference->set_reference_table_name($table);
		}
		else
		{
			$id_reference->set_reference_table_name($this->get_table_name());
		}

		return $id_reference->create();
	}

	/**
	 * Retrieves an id reference
	 * @param Int $old_id
	 * @param String $table
	 */
	function get_id_reference($old_id, $table = null)
	{
		if(!$table)
		{
			$table = $this->get_table_name();
		}

		if(!$this->id_references[$table][$old_id])
		{
			$id_reference = MigrationDataManager :: retrieve_id_reference_by_old_id_and_table($old_id, $table);
			if($id_reference)
			{
				$this->id_references[$table][$old_id] = $id_reference->get_new_id();
			}
		}

		return $this->id_references[$table][$old_id];
	}

	/**
	 * Creates a file recovery object
	 * @param String $old_file
	 * @param String $new_file
	 */
	function create_file_recovery($old_file, $new_file)
	{
		$file_recovery = new FileRecovery();
		$file_recovery->set_old_path($old_file);
		$file_recovery->set_new_path($new_file);
		return $file_recovery->create();
	}

	/**
     * Migrates a file to a new place, makes use of Filesystem class
     * Built in checks for same filename
     * @param String $old_rel_path Relative path on the old system
     * @param String $new_rel_path Relative path on the chamilo system
     * @return String $new_filename
     */
    function migrate_file($old_path, $new_path, $filename, $new_filename = null)
    {
        if(!$new_filename)
        {
        	$new_filename = $filename;
        }

        $old_file = $old_path . $filename;
        $new_file = $new_path . $new_filename;

        $secure_filename = Filesystem :: copy_file_with_double_files_protection($old_path, $filename, $new_path, $new_filename, PlatformSetting :: get('move_files', MigrationManager :: APPLICATION_NAME));

    	if(!$secure_filename)
		{
			return;
		}

        if($secure_filename)
        {
        	$new_file = $new_path . $secure_filename;
        }

        $this->create_file_recovery($old_file, $new_file);

        return $secure_filename;
    }

    public function migrate_file_and_create_document($source_path, $source_filename, $user_id)
    {
        $hash = md5($source_filename);
        $destination_path = Path :: get(SYS_REPO_PATH) . $user_id . '/' . Text :: char_at($hash, 0) . '/';
        
        $source_file = $source_path . $source_filename;
        $destination_file = $destination_path . $hash;

        if (! file_exists($source_file) || ! is_file($source_file))
        {
            return null;
        }
        
        if (file_exists($destination_file) && is_file($destination_file))
        {
            if (! (md5_file($source_file) == md5_file($destination_file)))
            {
                $hash = Filesystem :: create_unique_name($destination_path, $hash);
                $destination_file = $destination_path . $hash;
            }
            else
            {
                return RepositoryDataManager :: retrieve_document_from_hash($user_id, $hash);
            }
        }
        
        $move_file = PlatformSetting :: get('move_files', MigrationManager :: APPLICATION_NAME);
        
        if ($move_file)
        {
            Filesystem :: move_file($source_file, $destination_file);
        }
        else
        {
            Filesystem :: copy_file($source_file, $destination_file);
        }
        
        $document = new Document();
        $document->set_filename($source_filename);
        $document->set_path($user_id . '/' . Text :: char_at($hash, 0) . '/' . $hash);
        $document->set_filesize(filesize($destination_file));
        $document->set_hash($hash);
        $document->set_owner_id($user_id);
        return $document;
    }

    /**
     * Factory to retrieve the correct class of an old system
     * @param string $old_system the old system
     * @param string $type the class type
     */
    static function factory($old_system, $type)
    {
        $filename = dirname(__FILE__) . '/../platform/' . $old_system . '/' . $old_system . '_' . $type . '.class.php';

        if (! file_exists($filename) || ! is_file($filename))
        {
            echo ($filename);
            die('Failed to load ' . $filename);
        }
        $class = __NAMESPACE__ . '\\' .Utilities :: underscores_to_camelcase($old_system . '_' . $type);

        require_once $filename;
        return new $class();
    }

    /**
     * Additional conditions to retrieve data from the data class
     */
    static function get_retrieve_condition()
    {
    	return null;
    }

    /**
     * Checks wether the current data is valid
     */
    abstract function is_valid();

    /**
     * Converts the current data to chamilo 2.0 data
     */
    abstract function convert_data();

    /**
     * Returns the table that is being converted
     */
    abstract static function get_table_name();

    /**
     * Returns the class name
     */
    abstract static function get_class_name();
}

?>