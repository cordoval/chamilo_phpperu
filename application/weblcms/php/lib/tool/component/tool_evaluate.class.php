<?php
namespace application\weblcms;

use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;

use application\gradebook\EvaluationManager;
use application\gradebook\EvaluationManagerInterface;

class ToolComponentToolEvaluateComponent extends ToolComponent implements EvaluationManagerInterface
{
    private $publication_id;
    private $publisher_id;

    function run()
    {
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $tool_publication = WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
            $this->publication_id = $tool_publication->get_id();
            $this->publisher_id = $tool_publication->get_publisher_id();

            BreadcrumbTrail :: get_instance()->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE)), Translation :: get('BrowseEvaluations') . ' ' . $tool_publication->get_content_object()->get_title()));
            $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $this->publication_id);

            EvaluationManager :: launch($this);
        }
        else
        {
            $this->display_error_message(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('Publication')),Utilities:: COMMON_LIBRARIES));
        }
    }

    function get_publication_id()
    {
        return $this->publication_id;
    }

    function get_publisher_id()
    {
        return $this->publisher_id;
    }
}
?>