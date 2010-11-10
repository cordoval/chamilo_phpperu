<?php
namespace repository\content_object\handbook;

use repository\ComplexDisplayPreview;
use common\libraries\Translation;

class HandbookComplexDisplayPreview extends ComplexDisplayPreview
{

    function run()
    {
        $this->not_available(Translation :: get('HandbookPreviewNotAvailable'));
    }
}
?>