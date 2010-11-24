<?php
namespace repository\content_object\forum;

use repository\ComplexBuilderComponent;
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.glossary.component
 */
require_once dirname(__FILE__) . '/../../forum.class.php';

class ForumBuilderViewerComponent extends ForumBuilder
{
    function run()
    {
        ComplexBuilderComponent :: launch($this);
    }
}

?>