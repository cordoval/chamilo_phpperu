<?php
namespace repository\content_object\peer_assessment;

use repository\ContentObjectInstaller;

/**
 * $Id: peer_assessment_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class PeerAssessmentContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>