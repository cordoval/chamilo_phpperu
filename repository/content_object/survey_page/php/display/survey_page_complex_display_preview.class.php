<?php
namespace repository\content_object\survey_page;

use repository\ComplexDisplayPreview;
use common\libraries\Translation;

class SurveyPageComplexDisplayPreview extends ComplexDisplayPreview
{

    function run()
    {
        $this->not_available(Translation :: get('SurveyPagePreviewNotAvailable'));
    }
}
?>