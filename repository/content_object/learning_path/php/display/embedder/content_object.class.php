<?php
namespace repository\content_object\learning_path;

use common\libraries\Request;
use common\libraries\Display;
use repository\RepositoryDataManager;
use repository\ContentObjectDisplay;

require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class LearningPathDisplayEmbedderContentObjectComponent extends LearningPathDisplayEmbedder
{

    function run()
    {
        $content_object_id = Request :: get(LearningPathContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID);
        $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);

        Display :: small_header();
        echo ContentObjectDisplay :: factory($content_object)->get_full_html();
        Display :: small_footer();
    }
}
?>