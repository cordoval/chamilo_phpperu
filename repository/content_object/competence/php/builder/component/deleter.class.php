<?php
namespace repository\content_object\competence;

use common\libraries\Path;

require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class CompetenceBuilderDeleterComponent extends CompetenceBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>