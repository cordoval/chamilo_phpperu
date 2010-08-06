<?php

/**
 * $Id: dokeos185_dropbox_file.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 dropbox_file
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxFile extends Dokeos185CourseDataMigrationDataClass
{

    private $directory;
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'dropbox_file';
    /**
     * Dokeos185DropboxFile properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_UPLOADER_ID = 'uploader_id';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_FILESIZE = 'filesize';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_AUTHOR = 'author';
    const PROPERTY_UPLOAD_DATE = 'upload_date';
    const PROPERTY_LAST_UPLOAD_DATE = 'last_upload_date';
    const PROPERTY_CAT_ID = 'cat_id';
    const PROPERTY_SESSION_ID = 'session_id';

    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185DropboxFile object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185DropboxFile($defaultProperties = array())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_UPLOADER_ID, self :: PROPERTY_FILENAME, self :: PROPERTY_FILESIZE, self :: PROPERTY_TITLE, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_AUTHOR, self :: PROPERTY_UPLOAD_DATE, self :: PROPERTY_LAST_UPLOAD_DATE, self :: PROPERTY_CAT_ID, self :: PROPERTY_SESSION_ID);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the id of this Dokeos185DropboxFile.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the uploader_id of this Dokeos185DropboxFile.
     * @return the uploader_id.
     */
    function get_uploader_id()
    {
        return $this->get_default_property(self :: PROPERTY_UPLOADER_ID);
    }

    /**
     * Returns the filename of this Dokeos185DropboxFile.
     * @return the filename.
     */
    function get_filename()
    {
        return $this->get_default_property(self :: PROPERTY_FILENAME);
    }

    /**
     * Returns the filesize of this Dokeos185DropboxFile.
     * @return the filesize.
     */
    function get_filesize()
    {
        return $this->get_default_property(self :: PROPERTY_FILESIZE);
    }

    /**
     * Returns the title of this Dokeos185DropboxFile.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the description of this Dokeos185DropboxFile.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the author of this Dokeos185DropboxFile.
     * @return the author.
     */
    function get_author()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHOR);
    }

    /**
     * Returns the upload_date of this Dokeos185DropboxFile.
     * @return the upload_date.
     */
    function get_upload_date()
    {
        return $this->get_default_property(self :: PROPERTY_UPLOAD_DATE);
    }

    /**
     * Returns the last_upload_date of this Dokeos185DropboxFile.
     * @return the last_upload_date.
     */
    function get_last_upload_date()
    {
        return $this->get_default_property(self :: PROPERTY_LAST_UPLOAD_DATE);
    }

    /**
     * Returns the cat_id of this Dokeos185DropboxFile.
     * @return the cat_id.
     */
    function get_cat_id()
    {
        return $this->get_default_property(self :: PROPERTY_CAT_ID);
    }

    /**
     * Returns the session_id of this Dokeos185DropboxFile.
     * @return the session_id.
     */
    function get_session_id()
    {
        return $this->get_default_property(self :: PROPERTY_SESSION_ID);
    }

    /**
     * Check if the dropboxfile is valid
     * @param array $courses the parameters for the validation
     */
    function is_valid()
    {
        $course = $this->get_course();
        $this->set_item_property($this->get_data_manager()->get_item_property($course, 'document', $this->get_id()));

        $filename = $this->get_filename();
        $old_rel_path = 'courses/' . $course->get_directory() . '/dropbox/';

        $filename = iconv("UTF-8", "ISO-8859-1", $filename);
        $old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);

        $this->directory = $this->get_data_manager()->get_sys_path() . $old_rel_path;

        if (!$this->get_id()) {
            //echo 'Error in ID at course : ' . $course->get_db_name() .' ';
            $this->create_failed_element($this->get_id());
            return false;
        } else
        if (!$this->get_item_property()) {
            //echo 'Error in property at course : ' . $course->get_db_name() .' ';
            $this->create_failed_element($this->get_id());
            return false;
        } else
        if (!$this->get_item_property()->get_ref()) {
            //echo 'Error in reference at course : ' . $course->get_db_name() .' ';
            $this->create_failed_element($this->get_id());
            return false;
        } else
        if (!$this->get_item_property()->get_insert_date()) {
            //echo 'Error in insert_date at course : ' . $course->get_db_name() .' ';
            $this->create_failed_element($this->get_id());
            return false;
        } else
        if (!file_exists($this->directory . $filename)) {
            //echo 'Error in full_path at course : ' . $course->get_db_name() .'ID : ' . $this->get_id();
            $this->create_failed_element($this->get_id());
            return false;
        }

        return true;
    }

    /**
     * Convert to new dropbox file
     * @param array $array the parameters for the conversion
     * @return the new dropbox file
     */
    function convert_data()
    {
        if ($this->get_uploader_id())
            $new_user_id = $this->get_id_reference($this->get_uploader_id(), 'main_database.user');
        else
            $new_user_id = $this->get_id_reference($this->get_item_property()->get_insert_user_id(), 'main_database.user');

        $course = $this->get_course();
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

        $new_to_group_id[] = $this->get_id_reference($this->get_item_property()->get_to_group_id(), $this->get_database_name() . '.group_info');
        $new_to_user_id[] = $this->get_id_reference($this->get_item_property()->get_to_user_id(), 'main_database.user');

        if (!$new_user_id) {
            $new_user_id = $this->get_owner($new_course_code);
        }

        $new_path = $new_user_id . '/';
        $old_rel_path = 'courses/' . $course->get_directory() . '/dropbox/';

        $new_rel_path = 'files/repository/' . $new_path;

        //$filename_split = split('/', $this->get_);
        $original_filename = $this->get_title(); //get_file_name returns a hash

        $base_hash = md5($original_filename);
        $new_path = Path :: get(SYS_REPO_PATH) . $new_user_id . '/' . Text :: char_at($base_hash, 0) . '/';
        $unique_hash = FileSystem :: create_unique_name($new_path, $base_hash);

        $hash_filename = $this->migrate_file($this->directory, $new_path, $this->get_filename(), $unique_hash);

        if ($hash_filename) {
            //Create document in repository
            $chamilo_repository_document = new Document();
            $chamilo_repository_document->set_filename($original_filename);
            $chamilo_repository_document->set_path($new_user_id . '/' . Text :: char_at($base_hash, 0) . '/' . $unique_hash);  //!!!!!!!
            $chamilo_repository_document->set_filesize($this->get_filesize());
            $chamilo_repository_document->set_hash($unique_hash);
            if ($this->get_title())
                $chamilo_repository_document->set_title($this->get_title());
            else
                $chamilo_repository_document->set_title($original_filename);
            $chamilo_repository_document->set_description($this->get_description());
            $chamilo_repository_document->set_comment('...');
            $chamilo_repository_document->set_owner_id($new_user_id);
            $chamilo_repository_document->set_creation_date(strtotime($this->get_item_property()->get_insert_date()));
            $chamilo_repository_document->set_modification_date(strtotime($this->get_item_property()->get_lastedit_date()));

            $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('Documents'));
            $chamilo_repository_document->set_parent_id($chamilo_category_id);

            if ($this->get_item_property()->get_visibility() == 2)
                $chamilo_repository_document->set_state(1);

            //Create document in db
            $chamilo_repository_document->create();

            //Add id references to migration table

            $this->create_id_reference($this->get_id(), $chamilo_repository_document->get_id());

            //publication
            $parent_id = $this->get_id_reference($this->get_cat_id(), 'dokeos_DOKEOSCOURSE.dropbox_category');
            $this->create_publication($chamilo_repository_document, $new_course_code, $new_user_id, 'document', $parent_id, $new_to_user_id, $new_to_group_id);
        }
        return $chamilo_repository_document;
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    static function get_class_name()
    {
        return self :: CLASS_NAME;
    }

}
?>