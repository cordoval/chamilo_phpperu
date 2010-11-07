<?php
namespace repository\content_object\survey;

use repository\ComplexDisplayPreview;
use common\libraries\Translation;

class SurveyComplexDisplayPreview extends ComplexDisplayPreview
{

    function run()
    {
        $this->not_available(Translation :: get('SurveyPreviewNotAvailable'));
    }
}
?>