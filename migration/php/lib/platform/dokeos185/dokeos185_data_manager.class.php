<?php

namespace migration;

use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\OrCondition;
use common\libraries\PlatformSetting;
use common\libraries\Translation;
use application\weblcms\WeblcmsDataManager;
use application\weblcms\CourseUserRelation;
use user\UserDataManager;
use user\User;
use common\libraries\ObjectResultSet;
use Exception;

/**
 * Class that connects to the old dokeos185 system
 *
 * @author Sven Vanpoucke
 * @author David Van Wayenbergh
 */
class Dokeos185DataManager extends MigrationDatabase implements PlatformMigrationDataManager
{

    /**
     * The dokeos 185 configuration array
     * @var String[]
     */
    private $configuration;
    /**
     * Variable to keep track of the selected database;
     * @var String
     */
    private $current_database;
    /**
     * Singleton
     */
    private static $instance;

    static function get_instance()
    {
        if (!self :: $instance)
        {
            self :: $instance = new self();
        }

        return self :: $instance;
    }

    /**
     * Constructor
     */
    final public function __construct()
    {
        $this->configuration = $this->get_configuration();

        if (!$this->configuration)
        {
            throw new Exception(Translation :: get('PlatformConfigurationCanNotBeFound'));
        }

        $connection_string = 'mysql://' . $this->configuration['db_user'] . ':' . $this->configuration['db_password'] . '@' . $this->configuration['db_host'] . '/' . $this->get_database_name('main_database');
        $this->initialize($connection_string);
        $this->current_database = $this->get_database_name('main_database');
    }

    /**
     * Retrieves the configuration from dokeos 1.8.5
     */
    function get_configuration()
    {
        if (!$this->configuration)
        {
            $platform_path = 'file://' . PlatformSetting :: get('platform_path', MigrationManager :: APPLICATION_NAME);

            if (file_exists($platform_path) && is_dir($platform_path))
            {
                $config_file = $platform_path . '/main/inc/conf/configuration.php';
                if (file_exists($config_file) && is_file($config_file))
                {
                    $_configuration = array();
                    require_once ($config_file);
                    $this->configuration = $_configuration;
                }
            }
        }

        return $this->configuration;
    }

    /**
     * Get the database name from the configuration or use the given one
     * @param String $database_name
     */
    function get_database_name($database_name)
    {
        return isset($this->configuration[$database_name]) ? $this->configuration[$database_name] : $database_name;
    }

    /**
     * Change the database selection
     * @param String $database_name
     */
    function set_database($database_name)
    {
        $database_name = $this->get_database_name($database_name);

        if ($this->current_database == $database_name)
        {
            return;
        }

        $this->current_database = $database_name;
        $this->get_connection()->setDatabase($database_name);
    }

    /**
     * Retrieve all objects
     * @param Dokeos185MigrationDataClass $data_class
     * @param int $offset - the offset
     * @param int $count - the number of objects to retrieve
     */
    function retrieve_all_objects($data_class, $offset, $count)
    {
        $this->set_database($data_class->get_database_name());
        return $this->retrieve_objects($data_class->get_table_name(), $data_class->get_retrieve_condition(), $offset, $count, null, $data_class->get_class_name());
    }

    /**
     * Counts all objects
     * @param Dokeos185MigrationDataClass $data_class
     */
    function count_all_objects($data_class)
    {
        $this->set_database($data_class->get_database_name());
        return $this->count_objects($data_class->get_table_name(), $data_class->get_retrieve_condition());
    }

    /**
     * Check wether a user is a platform admin
     */
    function is_platform_admin($user)
    {
        $condition = new EqualityCondition(Dokeos185User :: PROPERTY_USER_ID, $user->get_user_id());
        $count = $this->count_objects('admin', $condition);

        return ($count > 0);
    }

    /**
     * Gets the system path of the dokeos185 installation
     */
    function get_sys_path()
    {
        $conf = $this->get_configuration();
        return $conf['root_sys'];
    }

    /**
     * Gets the id of the first admin of the dokeos 185 platform
     */
    function get_admin_id()
    {
        $this->set_database('main_database');

        $query = 'SELECT a.user_id FROM ' . $this->escape_table_name('admin') . ' AS a JOIN ' . $this->escape_table_name('user') . ' AS u ON a.user_id = u.user_id;';
        $result = $this->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $id = $record['user_id'];
        $result->free();

        return $id;
    }

    /**
     * Retrieves an item property of a course tool
     * @param Dokeos185Course $course
     * @param String $tool
     * @param int $id
     */
    function get_item_property($course, $tool, $id)
    {
        return $this->get_item_properties($course, $tool, $id, 0, 1)->next_result();
    }

    function get_item_properties($course, $tool, $id, $offset = null, $count = null)
    {
        $this->set_database($course->get_db_name());

        $conditions = array();
        $conditions[] = new EqualityCondition(Dokeos185ItemProperty :: PROPERTY_TOOL, $tool);
        $conditions[] = new EqualityCondition(Dokeos185ItemProperty :: PROPERTY_REF, $id);
        $condition = new AndCondition($conditions);

        return $this->retrieve_objects(Dokeos185ItemProperty :: get_table_name(), $condition, $offset, $count, null, Dokeos185ItemProperty :: get_class());
    }

    /**
     * Algorithm to determine the owner of an object in a course
     * Count the number of course teachers (with status 1) and if there is only 1. This user will become the owner
     * Retrieve the user that is subscribed as a teacher and is the titular of the course
     * Retrieve the user that has the most publications in the course
     * Return the administrator
     * @param int $course_id
     */
    function get_owner_id($course_id)
    {
        //Check if there is only one owner
        $wdm = WeblcmsDataManager :: get_instance();
        $count = $wdm->count_course_user_relations(new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_id));
        if ($count == 1)
        {
            return $wdm->retrieve_course_user_relations(new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course_id))->next_result()->get_user();
        }

        //Check for the titular
        $course = $wdm->retrieve_course($course_id);
        $user_relation = $wdm->retrieve_course_user_relation($course_id, $course->get_titular());
        if ($user_relation)
        {
            return $course->get_titular();
        }

        //Check for the user with the most publications
        $possible_owner = $wdm->get_user_with_most_publications_in_course($course_id);
        if ($possible_owner)
        {
            return $possible_owner;
        }

        $possible_admin = MigrationDataManager :: retrieve_id_reference_by_old_id_and_table($this->get_admin_id(), 'main_database.user')->get_new_id();
        if ($possible_admin)
        {
            return $possible_admin;
        }

        return UserDataManager :: get_instance()->retrieve_users(new EqualityCondition(User :: PROPERTY_PLATFORMADMIN, 1))->next_result();
    }

    /**
     * Retrieves the uers by fullname
     * @param String $fullname
     */
    function retrieve_user_by_fullname($fullname)
    {
        $name = explode(' ', $fullname);
        $firstname = $name[0];
        $lastname = $name[1];

        $conditions = array();
        $conditions1 = array();
        $conditions2 = array();

        $conditions1[] = new EqualityCondition(Dokeos185User :: PROPERTY_FIRSTNAME, $firstname);
        $conditions1[] = new EqualityCondition(Dokeos185User :: PROPERTY_LASTNAME, $lastname);
        $conditions[] = new AndCondition($conditions1);

        $conditions2[] = new EqualityCondition(Dokeos185User :: PROPERTY_FIRSTNAME, $lastname);
        $conditions2[] = new EqualityCondition(Dokeos185User :: PROPERTY_LASTNAME, $firstname);
        $conditions[] = new AndCondition($conditions2);

        $condition = new OrCondition($conditions);
        $object = $this->retrieve_object(Dokeos185User:: get_table_name(), $condition, null, Dokeos185User :: get_class_name());

        return $object;
    }

    /**
     * Retrieves all the relations from a question to the several quizzes
     * @param Dokeos185Course $course
     * @param int $question_id
     */
    function retrieve_quiz_rel_questions($course, $question_id)
    {
        $this->set_database($course->get_db_name());

        $condition = new EqualityCondition(Dokeos185QuizRelQuestion :: PROPERTY_QUESTION_ID, $question_id);
        return $this->retrieve_objects(Dokeos185QuizRelQuestion :: get_table_name(), $condition, null, null, null, Dokeos185QuizRelQuestion :: get_class_name());
    }

    /**
     * Retrieves all the dropbox persons
     * @param Dokeos185Course $course
     * @param int $question_id
     */
    function retrieve_dropbox_persons($course, $dropbox_file_id)
    {
        $this->set_database($course->get_db_name());

        $condition = new EqualityCondition(Dokeos185DropboxPerson :: PROPERTY_FILE_ID, $dropbox_file_id);
        return $this->retrieve_objects(Dokeos185DropboxPerson :: get_table_name(), $condition, null, null, null, 'Dokeos185DropboxPerson');
    }

    function retrieve_dokeos185_track_eaccess()
    {
        $this->set_database('statistics_database');

        $table = $this->get_table_name(Dokeos185TrackEAccess :: get_table_name());
        $access_date_column = $this->escape_column_name(Dokeos185TrackEAccess :: PROPERTY_ACCESS_DATE);
        $access_user_id_column = $this->escape_column_name(Dokeos185TrackEAccess :: PROPERTY_ACCESS_USER_ID);
        $access_course_code_column = $this->escape_column_name(Dokeos185TrackEAccess :: PROPERTY_ACCESS_COURS_CODE);
        $access_tool_column = $this->escape_column_name(Dokeos185TrackEAccess :: PROPERTY_ACCESS_TOOL);

        $query = 'SELECT MAX( ' . $access_date_column . ' ) AS ' . $access_date_column . ', ' . $access_user_id_column . ', ' . $access_course_code_column . ', ' . $access_tool_column . ' FROM ' . $table .
                ' WHERE ' . $access_user_id_column . ' IS NOT NULL AND ' . $access_course_code_column . ' IS NOT NULl AND ' . $access_tool_column . ' IS NOT NULL
        		 GROUP BY ' . $access_user_id_column . ', ' . $access_course_code_column . ', ' . $access_tool_column . ';';

        $result = $this->query($query);
        return new ObjectResultSet($this, $result, Dokeos185TrackEAccess :: get_class_name());
    }

}

?>