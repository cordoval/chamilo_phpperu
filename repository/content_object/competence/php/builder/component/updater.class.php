<?php
namespace repository\content_object\competence;
require_once Path :: get_repository_path() . '/lib/content_object/indicator/indicator.class.php';

class CompetenceBuilderUpdaterComponent extends CompetenceBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}
?>