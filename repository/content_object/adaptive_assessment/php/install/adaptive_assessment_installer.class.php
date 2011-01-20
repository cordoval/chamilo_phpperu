<?php
namespace repository\content_object\adaptive_assessment;

use repository\ContentObjectInstaller;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentContentObjectInstaller extends ContentObjectInstaller
{

    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>