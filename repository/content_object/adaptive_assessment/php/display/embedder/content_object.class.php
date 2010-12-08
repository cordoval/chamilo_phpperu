<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Request;
use common\libraries\Display;
use repository\RepositoryDataManager;
use repository\ContentObjectDisplay;

require_once dirname(__FILE__) . '/../adaptive_assessment_display_embedder.class.php';

class AdaptiveAssessmentDisplayEmbedderContentObjectComponent extends AdaptiveAssessmentDisplayEmbedder
{

    function run()
    {
        $content_object_id = Request :: get(AdaptiveAssessmentContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID);
        $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);

        Display :: small_header();
        echo ContentObjectDisplay :: factory($content_object)->get_full_html();
        Display :: small_footer();
    }
}
?>