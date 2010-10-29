<?php

namespace application\peer_assessment;

use common\libraries\Installer;

require_once dirname(__FILE__) . '/../lib/peer_assessment_data_manager.class.php';

/**
 * @author Nick Van Loocke
 */
class PeerAssessmentInstaller extends Installer
{

    /**
     * Constructor
     */
    function PeerAssessmentInstaller($values)
    {
        parent :: __construct($values, PeerAssessmentDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }

}

?>