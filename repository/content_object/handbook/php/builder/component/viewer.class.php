<?php
namespace repository\content_object\handbook;

use repository\ComplexBuilderComponent;


class HandbookBuilderViewerComponent extends HandbookBuilder
{

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>