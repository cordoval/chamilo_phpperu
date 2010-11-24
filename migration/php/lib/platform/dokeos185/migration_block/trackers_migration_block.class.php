<?php
namespace migration;

//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_euploads.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eopen.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eonline.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_elogin.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_elinks.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_elastaccess.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_ehotspot.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_edownloads.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eexercices.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_ehotpotatoes.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_edefault.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_ecourse_access.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eattempt.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_cproviders.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_creferers.class.php";
//
require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eaccess.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_ccountries.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_cbrowsers.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_edefault.class.php";
//
//require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eattempt.class.php";
//
//require_once dirname(__FILE__) . '/../data_class/dokeos185_track_cos.class.php';


class TrackersMigrationBlock extends MigrationBlock
{
    const MIGRATION_BLOCK_NAME = 'trackers';

    function get_prerequisites()
    {
        return array(UsersMigrationBlock :: MIGRATION_BLOCK_NAME);
    }

    function get_block_name()
    {
        return self :: MIGRATION_BLOCK_NAME;
    }

    function get_data_classes()
    {
        //        return array(new Dokeos185TrackCOs(), new Dokeos185TrackCBrowsers(), new Dokeos185TrackCCountries(),
        //            new Dokeos185TrackCProviders(), new Dokeos185TrackCReferers(), new Dokeos185TrackEAccess(),
        //            new Dokeos185TrackEAttempt(), new Dokeos185TrackECourseAccess(), new Dokeos185TrackEDefault(),
        //            new Dokeos185TrackEDownloads(), new Dokeos185TrackEExercices(), new Dokeos185TrackEHotpotatoes(),
        //            new Dokeos185TrackEHotspot(), new Dokeos185TrackELastaccess(), new Dokeos185TrackELinks(),
        //            new Dokeos185TrackELogin(), new Dokeos185TrackEOnline(), new Dokeos185TrackEOpen(), new Dokeos185TrackEUploads());
        return array(
                new Dokeos185TrackEAccess());
    }

    /**
     * TODO: Temporary function untill we find a better way to provide the count method and us the general migrate_data function
     */
    protected function migrate_data()
    {
        $data_classes = $this->get_data_classes();
        foreach ($data_classes as $data_class)
        {
            $failed_objects = 0;
            $this->pre_data_class_migration_messages_log($data_class);
            
            $objects = $data_class->get_data_manager()->retrieve_dokeos185_track_eaccess($data_class);
            $total_count = $objects->size();
            
            while ($object = $objects->next_result())
            {
                if (! $this->convert_object($object))
                {
                    $failed_objects ++;
                }
                
                $this->get_file_logger()->log_message($object->get_message());
            }
        }
        
        $migrated_objects = $total_count - $failed_objects;
        
        $this->post_data_class_migration_messages_log($migrated_objects, $failed_objects, $data_class);
    }
}
?>