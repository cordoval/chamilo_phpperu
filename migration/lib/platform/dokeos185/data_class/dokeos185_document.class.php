<?php

/**
 * $Id: dokeos185_document.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.platform.dokeos185
 */
require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class represents an old Dokeos 1.8.5 document
 *
 * @author David Van Wayenbergh
 */
class Dokeos185Document extends Dokeos185CourseDataMigrationDataClass
{
    /**
     * document properties
     */
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'document';

    const PROPERTY_ID = 'id';
    const PROPERTY_PATH = 'path';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SIZE = 'size';
    const PROPERTY_COMMENT = 'comment';
    const PROPERTY_FILETYPE = 'filetype';

    private $directory;
    /**
     * Default properties of the document object, stored in an associative
     * array.
     */
    private $defaultProperties;

    /**
     * Creates a new document object.
     * @param array $defaultProperties The default properties of the document
     *                                 object. Associative array.
     */
    function Dokeos185Document($defaultProperties = array())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property of this document object by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties of this document.
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Get the default properties of all documents.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_PATH, self :: PROPERTY_TITLE, self :: PROPERTY_SIZE, self :: PROPERTY_COMMENT, self :: PROPERTY_FILETYPE);
    }

    /**
     * Sets a default property of this document by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Checks if the given identifier is the name of a default document
     * property.
     * @param string $name The identifier.
     * @return boolean True if the identifier is a property name, false
     *                 otherwise.
     */
    static function is_default_property_name($name)
    {
        return in_array($name, self :: get_default_property_names());
    }

    /**
     * Returns the id of this document.
     * @return int The id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the path of this document.
     * @return String The path.
     */
    function get_path()
    {
        return $this->get_default_property(self :: PROPERTY_PATH);
    }

    /**
     * Returns the title of this document.
     * @return String The title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the size of this document.
     * @return int The size.
     */
    function get_size()
    {
        return $this->get_default_property(self :: PROPERTY_SIZE);
    }

    /**
     * Returns the comment of this document.
     * @return String The comment.
     */
    function get_comment()
    {
        return $this->get_default_property(self :: PROPERTY_COMMENT);
    }

    /**
     * Returns the filetype of this document.
     * @return String The filetype.
     */
    function get_filetype()
    {
        return $this->get_default_property(self :: PROPERTY_FILETYPE);
    }

    /**
     * Check if the document is valid
     * @param Course $course the course of the document
     * @return true if the dropbox category is valid
     */
    function is_valid()
    {
        $course = $this->get_course();
        $this->set_item_property($this->get_data_manager()->get_item_property($course, 'document', $this->get_id()));

        $pos = strrpos($this->get_path(), '/');
        $filename = substr($this->get_path(), $pos);
        $old_path = substr($this->get_path(), 0, $pos);
        unset($pos);

        $old_rel_path = 'courses/' . $course->get_directory() . '/document/' . $old_path . '/';
        unset($old_path);

        $filename = iconv("UTF-8", "ISO-8859-1", $filename);
        $old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);

        $this->directory = $this->get_data_manager()->get_sys_path() . $old_rel_path;


        if (!$this->get_id() || !$this->get_path() || !$this->get_filetype() || !$this->get_item_property() || !$this->get_item_property()->get_ref() || !$this->get_item_property()->get_insert_date() || !file_exists($this->directory . $filename)) {
            $this->create_failed_element($this->get_id());
            return false;
        }
        unset($old_rel_path);
        unset($filename);
        unset($course);
        return true;
    }

    /**
     * Convert to new document
     * @param Course $course the course of the document
     * @return the new document
     */
    function convert_data()
    {
        if ($this->get_filetype() == 'file') { //folders are converted to categories in the publication part (the correct folders are parsed from the file path)
            $course = $this->get_course();

            $new_user_id = $this->get_id_reference($this->get_item_property()->get_insert_user_id(), 'main_database.user');
            $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');

            $new_to_group_id[] = $this->get_id_reference($this->get_item_property()->get_to_group_id(), $this->get_database_name() . '.group_info');
            $new_to_user_id[] = $this->get_id_reference($this->get_item_property()->get_to_user_id(), 'main_database.user');

            if (!$new_user_id) {
                $new_user_id = $this->get_owner($new_course_code);
            }

            $filename_split = split('/', $this->get_path());
            $original_filename = $filename_split[count($filename_split) - 1];

            $base_hash = md5($original_filename);
            $new_path = Path :: get(SYS_REPO_PATH) . $new_user_id . '/' . Text :: char_at($base_hash, 0) . '/';
            $unique_hash = FileSystem :: create_unique_name($new_path, $base_hash);

            $hash_filename = $this->migrate_file($this->directory, $new_path, $original_filename, $unique_hash);

            if ($hash_filename) {
                //Create document in repository
                $chamilo_repository_document = new Document();
                $chamilo_repository_document->set_filename($original_filename);
                $chamilo_repository_document->set_path($new_user_id . '/' . Text :: char_at($base_hash, 0) . '/' . $unique_hash);  //!!!!!!!
                $chamilo_repository_document->set_filesize($this->get_size());
                $chamilo_repository_document->set_hash($unique_hash);
                if ($this->get_title())
                    $chamilo_repository_document->set_title($this->get_title());
                else
                    $chamilo_repository_document->set_title($original_filename);
                $chamilo_repository_document->set_description('...');
                $chamilo_repository_document->set_comment($this->get_comment());
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

                if ($this->get_item_property()->get_visibility() <= 1) {
                    $categories = split('/', $this->get_path());
                    array_shift($categories); //remove empty array value
                    array_pop($categories); //remove filename
                    $parent_id = 0;

                    foreach ($categories as $categorie_name) {

                        //check if the category already exists. (move to weblcmdatamanager?)
                        //(Optimalisation: cache created categories)
                        $conditions = array();
                        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_NAME, $categorie_name);
                        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $new_course_code);
                        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, 'document');
                        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_PARENT, $parent_id);

                        $condition = new AndCondition($conditions);

                        $category = WeblcmsDataManager::get_instance()->retrieve_content_object_publication_categories($condition)->next_result();

                        if (!$category) {
                            //Create category for tool in weblcms
                            $category = new ContentObjectPublicationCategory();
                            $category->set_name($categorie_name);
                            $category->set_course($new_course_code);
                            $category->set_tool('document');
                            $category->set_parent($parent_id);

                            //Create category in database
                            $category->create();
                            $parent_id = $category->get_id();
                        } else {
                            $parent_id = $category->get_id();
                        }
                    }

                    //create publication in weblcms
                    $this->create_publication($chamilo_repository_document, $new_course_code, $new_user_id, 'document', $parent_id, $new_to_user_id, $new_to_group_id);
                }
            }
        }
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