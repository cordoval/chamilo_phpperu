<?php
namespace repository\content_object\handbook_topic;

use repository\ContentObjectInstaller;

class HandbookTopicContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>