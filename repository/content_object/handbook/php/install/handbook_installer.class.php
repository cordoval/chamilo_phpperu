<?php
namespace repository\content_object\handbook;

use repository\ContentObjectInstaller;

class HandbookContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>