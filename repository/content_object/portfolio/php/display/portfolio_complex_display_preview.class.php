<?php
namespace repository\content_object\portfolio;

use repository\ComplexDisplayPreview;
use common\libraries\Translation;

class PortfolioComplexDisplayPreview extends ComplexDisplayPreview
{

    function run()
    {
        $this->not_available(Translation :: get('PortfolioPreviewNotAvailable'));
    }
}
?>