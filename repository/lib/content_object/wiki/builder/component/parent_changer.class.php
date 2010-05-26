<?php
/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../forum.class.php';

class FormBuilderParentChangerComponent extends FormBuilderComponent
{

    function run()
    {
        $browser = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: PARENT_CHANGER_COMPONENT, $this);
        $browser->run();
    }

}

?>