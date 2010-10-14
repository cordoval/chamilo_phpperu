<?php
namespace repository\content_object\matterhorn;

use repository\ContentObjectInstaller;

class MatterhornContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>