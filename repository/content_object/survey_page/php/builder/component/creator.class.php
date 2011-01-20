<?php
namespace repository\content_object\survey_page;

use repository\RepositoryDataManager;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;
use repository\ComplexBuilderComponent;

/**
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyPageBuilderCreatorComponent extends SurveyPageBuilder
{

    function run()
    {

        $creator = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: CREATOR_COMPONENT, $this);
        $creator->run();
    }
}
?>