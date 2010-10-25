<?php
namespace repository\content_object\handbook_item;

use repository\ContentObjectInstaller;

class HandbookItemContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>