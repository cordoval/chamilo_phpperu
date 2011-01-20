<?php
namespace repository\content_object\survey_page;

use repository\ComplexDisplayPreview;
use common\libraries\Translation;

/**
 * @package repository.content_object.survey_page
 * @author Eduard Vossen
 * @author Magali Gillard
 */
class SurveyPageComplexDisplayPreview extends ComplexDisplayPreview
{

    function run()
    {
        $this->not_available(Translation :: get('SurveyPagePreviewNotAvailable'));
    }
}
?>