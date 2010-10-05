<?php

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_euploads.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eopen.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eonline.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_elogin.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_elinks.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_elastaccess.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_ehotspot.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_edownloads.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eexercices.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_ehotpotatoes.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_edefault.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_ecourse_access.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eattempt.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_cproviders.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_creferers.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eaccess.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_ccountries.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_cbrowsers.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_edefault.class.php";

require_once dirname(__FILE__) . "/../data_class/dokeos185_track_eattempt.class.php";

require_once dirname(__FILE__) . '/../data_class/dokeos185_track_cos.class.php';

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
        return array(new Dokeos185TrackCOs(), new Dokeos185TrackCBrowsers(), new Dokeos185TrackCCountries(),
            new Dokeos185TrackCProviders(), new Dokeos185TrackCReferers(), new Dokeos185TrackEAccess(),
            new Dokeos185TrackEAttempt(), new Dokeos185TrackECourseAccess(), new Dokeos185TrackEDefault(),
            new Dokeos185TrackEDownloads(), new Dokeos185TrackEExercices(), new Dokeos185TrackEHotpotatoes(),
            new Dokeos185TrackEHotspot(), new Dokeos185TrackELastaccess(), new Dokeos185TrackELinks(),
            new Dokeos185TrackELogin(), new Dokeos185TrackEOnline(), new Dokeos185TrackEOpen(), new Dokeos185TrackEUploads());
    }

}
?>