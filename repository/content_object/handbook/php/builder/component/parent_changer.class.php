<?php
namespace repository\content_object\handbook;

use repository\ComplexBuilderComponent;

require_once dirname(__FILE__) . '/../handbook_builder.class.php';

class HandbookBuilderParentChangerComponent extends HandbookBuilder
{
    const PARAM_NEW_PARENT = 'new_parent';

    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>