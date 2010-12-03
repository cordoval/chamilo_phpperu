<?php
namespace repository\content_object\learning_path;

use common\libraries\Request;
use repository\RepositoryDataManager;

require_once dirname(__FILE__) . '/../learning_path_display_embedder.class.php';

class LearningPathDisplayEmbedderDocumentComponent extends LearningPathDisplayEmbedder
{

    function run()
    {
        $content_object_id = Request :: get(LearningPathContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID);
        $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);

        header('Expires: Wed, 01 Jan 1990 00:00:00 GMT');
        header('Content-Type: ' . $content_object->get_mime_type());
        header('Content-Description: ' . $content_object->get_filename());
        readfile($content_object->get_full_path());
    }
}
?>