<?php
namespace repository\content_object\peer_assessment;

use common\libraries\Path;

require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class PeerAssessmentBuilderCreatorComponent extends PeerAssessmentBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>