<?php
namespace repository\content_object\handbook;

use repository\ComplexDisplayPreview;
use common\libraries\Translation;
use common\libraries\Utilities;

class HandbookComplexDisplayPreview extends ComplexDisplayPreview
{

    function run()
    {
        $this->not_available(Translation :: get('PreviewNA', array('OBJECT' => Translation :: get('Handbook')), Utilities :: COMMON_LIBRARIES));
    }
}
?>