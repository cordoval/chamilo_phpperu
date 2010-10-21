<?php
/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
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