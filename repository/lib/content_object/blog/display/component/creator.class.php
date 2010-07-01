<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.blog.component
 */
require_once dirname(__FILE__) . '/../../blog.class.php';

class BlogDisplayCreatorComponent extends BlogDisplay
{
    function run()
    {
        $browser = ComplexDisplayComponent :: factory(ComplexDisplayComponent :: CREATOR_COMPONENT, $this);
        $browser->run();
    }
}

?>