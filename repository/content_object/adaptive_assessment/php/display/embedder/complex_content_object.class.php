<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Request;
use common\libraries\Display;
use repository\RepositoryDataManager;
use repository\ComplexDisplay;

require_once dirname(__FILE__) . '/../adaptive_assessment_display_embedder.class.php';

class AdaptiveAssessmentDisplayEmbedderComplexContentObjectComponent extends AdaptiveAssessmentDisplayEmbedder
{
    private $content_object;

    function run()
    {
//        $publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
//        $this->publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);
//        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $publication_id);

        $content_object_id = Request :: get(AdaptiveAssessmentContentObjectDisplay :: PARAM_EMBEDDED_CONTENT_OBJECT_ID);
        $this->content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);

//        $object_id = Request :: get(AdaptiveAssessmentTool :: PARAM_OBJECT_ID);
//        $this->object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
//        $this->set_parameter(AdaptiveAssessmentTool :: PARAM_OBJECT_ID, $object_id);

        Request :: set_get(ComplexDisplay :: PARAM_DISPLAY_ACTION, ComplexDisplay :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT);

        ComplexDisplay :: launch($this->content_object->get_type(), $this);
    }

    function display_header()
    {
        return Display :: small_header();
    }

    function display_footer()
    {
        return Display :: small_footer();
    }

    function get_root_content_object()
    {
        return $this->content_object;
    }
//
//    function get_publication()
//    {
//        return $this->publication;
//    }
}
?>