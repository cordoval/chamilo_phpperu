<?php
namespace migration;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\MessageLogger;
use common\libraries\Timer;
use common\libraries\Path;
use common\libraries\Filesystem;
use common\libraries\FileLogger;

use Exception;

/**
 * General migration class to start a migration for a block
 */

abstract class MigrationBlock
{
    /**
     * The maximum number of records to be retrieved in one resultset (to make the system more performant)
     * @var int
     */
    const MAX_RETRIEVE_LIMIT = 1000;

    /**
     * @var MessageLogger $message_logger - used to log messages on the screen
     */
    private $message_logger;

    /**
     * @var FileLogger $file_logger - used to log messages in a file
     */
    private $file_logger;

    /**
     * @var Timer
     */
    private $timer;

    /**
     * The block registration
     * @var MigrationBlockRegistration
     */
    private $migration_block_registration;

    function __construct()
    {
        $this->message_logger = MessageLogger :: get_instance(__CLASS__);
        $this->timer = new Timer();
    }

    // Messages function


    /**
     * Returns the message logger
     * @return MessageLogger
     */
    function get_message_logger()
    {
        return $this->message_logger;
    }

    /**
     * Renders the message of the message logger
     */
    function render_message()
    {
        return $this->message_logger->render();
    }

    /**
     * Logs a message to both the message logger (onscreen) and to the file logger
     * @param String $message
     * @param Int $type
     */
    function log_message_and_file($message, $type)
    {
        $this->message_logger->add_message($message, $type);
        $this->get_file_logger()->log_message($message);
    }

    /**
     * Returns the file logger
     * @return FileLogger
     */
    protected function get_file_logger()
    {
        if (! $this->file_logger)
        {
            $dir = Path :: get(SYS_FILE_PATH) . '/logs/migration/';
            if (! file_exists($dir))
            {
                Filesystem :: create_dir($dir);
            }
            $this->file_logger = new FileLogger($dir . $this->get_block_name() . '.log');
        }
        return $this->file_logger;
    }

    /**
     * Returns the timer
     * @return Timer
     */
    private function get_timer()
    {
        return $this->timer;
    }

    /**
     * Factory method for migration block
     * @param String $platform - the selected platform
     * @param String $block - the block name
     * @return MigrationBlock
     */
    function factory($platform, $block)
    {
        $file = dirname(__FILE__) . '/platform/' . $platform . '/migration_block/' . $block . '_migration_block.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('CanNotFindMigrationBlock', array(
                    'PLATFORM' => $platform,
                    'BLOCK' => $block)));
        }

        require_once ($file);

        $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($block) . 'MigrationBlock';

        return new $class();
    }

    // Validation functions


    /**
     * Checks the prerequisites of the current migration block against the selected migration blocks
     * @param String[] $selected_blocks - The selected migration blocks
     */
    function check_prerequisites($selected_blocks)
    {
        $prerequisites = $this->get_prerequisites();
        foreach ($prerequisites as $prerequisite)
        {
            if (! in_array($prerequisite, $selected_blocks))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Migrate the current block
     */
    function migrate()
    {
        $migration_block_registration = $this->get_migration_block_registration();
        if ($migration_block_registration->get_is_migrated())
        {
            $this->message_logger->add_message(Translation :: get('MigrationBlockAlreadyMigrated'), MessageLogger :: TYPE_WARNING);
            return;
        }

        $this->prepare_migration();
        $this->migrate_data();

        $this->finish_migration();
    }

    /**
     * Prepares the migration
     * Logfiles & Messages
     */
    protected function prepare_migration()
    {
        $this->get_timer()->start();
        $logger = $this->get_file_logger();

        $this->log_message_and_file(Translation :: get('StartMigrationForBlock', array(
                'BLOCK' => $this->get_block_name())) . "<br />\n", MessageLogger :: TYPE_CONFIRM);
    }

    /**
     * Finish te migration process
     * Change the block registration
     * Logfiles & Messages
     */
    protected function finish_migration()
    {
        $logger = $this->get_file_logger();

        $migration_block_registration = $this->get_migration_block_registration();
        $migration_block_registration->set_is_migrated(1);
        $migration_block_registration->update();

        $this->get_timer()->stop();

        $this->log_message_and_file(Translation :: get('MigrationComplete', array(
                'TIME' => $this->get_timer()->get_time_in_hours())), MessageLogger :: TYPE_CONFIRM);

        $logger->close_file();
    }

    /**
     * Migrates the data
     */
    protected function migrate_data()
    {
        $data_classes = $this->get_data_classes();
        foreach ($data_classes as $data_class)
        {
            $this->pre_data_class_migration_messages_log($data_class);

            $total_count = $data_class->get_data_manager()->count_all_objects($data_class);
            $failed_objects = $records_migrated = 0;

            while ($records_migrated < $total_count)
            {
                $offset = $records_migrated;
                $limit = self :: MAX_RETRIEVE_LIMIT;

                $objects = $data_class->get_data_manager()->retrieve_all_objects($data_class, $offset, $limit);
                while ($object = $objects->next_result())
                {
                    if (! $this->convert_object($object))
                    {
                        $failed_objects ++;
                    }

                    $this->get_file_logger()->log_message($object->get_message());

                    $records_migrated ++;
                }
            }

            $migrated_objects = $total_count - $failed_objects;

            $this->post_data_class_migration_messages_log($migrated_objects, $failed_objects, $data_class);
        }
    }

    /**
     * Converts the object
     * @param MigrationDataClass $object
     */
    protected function convert_object($object)
    {
        $validate = $object->is_valid();
        if (! $validate)
        {
            return false;
        }

        $object->convert_data();

        return true;
    }

    /**
     * Logs messages before starting the migration of each dataclass
     * @param MigrationDataClass $data_class
     */
    protected function pre_data_class_migration_messages_log(MigrationDataClass $data_class)
    {
        $this->log_message_and_file(Translation :: get('StartMigrationForTable', array(
                'TABLE' => $data_class->get_table_name())));
    }

    /**
     * Logs the messages after the migration of each dataclass
     * @param int $migrated_objects
     * @param int $failed_objects
     * @param MigrationDataClass $data_class
     */
    protected function post_data_class_migration_messages_log($migrated_objects, $failed_objects, MigrationDataClass $data_class)
    {
        if ($migrated_objects == 1)
        {
            $message = 'ObjectMigrated';
        }
        else
        {
            $message = 'ObjectsMigrated';
        }

        $this->log_message_and_file(Translation :: get($message, array('OBJECTCOUNT' => $migrated_objects)));

        if ($failed_objects == 1)
        {
            $message = 'ObjectNotMigrated';
        }
        else
        {
            $message = 'ObjectsNotMigrated';
        }

        $this->log_message_and_file(Translation :: get($message, array('OBJECTCOUNT' => $failed_objects)));
        $this->log_message_and_file(Translation :: get('FinishedMigrationForTable', array(
                'TABLE' => $data_class->get_table_name())) . "<br />\n");
    }

    /**
     * Returns the migration block registration or retrieves it from the database if it doesn't exist
     */
    private function get_migration_block_registration()
    {
        if (! $this->migration_block_registration)
        {
            $this->migration_block_registration = MigrationDataManager :: retrieve_migration_block_registrations_by_name($this->get_block_name());
        }

        return $this->migration_block_registration;
    }

    // Abstract functions


    /**
     * Returns the prerequisites for the current block
     */
    abstract function get_prerequisites();

    /**
     * Returns the name of the block
     */
    abstract function get_block_name();

    /**
     * Returns the dataclasses that are used in a block
     * @return MigrationDataClass[] $class
     */
    abstract function get_data_classes();
}