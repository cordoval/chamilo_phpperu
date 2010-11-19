<?php
namespace repository\content_object\peer_assessment;

use repository\ComplexDisplayPreview;
use common\libraries\Translation;

class PeerAssessmentComplexDisplayPreview extends ComplexDisplayPreview
{

    function run()
    {
        $this->not_available(Translation :: get('PeerAssessmentPreviewNotAvailable'));
    }
}
?>