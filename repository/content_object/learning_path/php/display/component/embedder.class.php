<?php
namespace repository\content_object\learning_path;

require_once dirname(__FILE__) . '/../learning_path_display_embedder.class.php';

class LearningPathDisplayEmbedderComponent extends LearningPathDisplay
{

    function run()
    {
        LearningPathDisplayEmbedder :: launch($this);
    }
}
?>