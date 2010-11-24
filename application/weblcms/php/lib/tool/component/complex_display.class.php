<?php
namespace application\weblcms;

use common\libraries\Request;
use repository\ComplexDisplay;
use repository\ComplexDisplaySupport;


/**
 * $Id: complex_builder.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
class ToolComponentComplexDisplayComponent extends ToolComponent implements ComplexDisplaySupport
{
    private $publication;

    function run()
    {
        $publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $publication_id);

        $this->publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);

        ComplexDisplay :: launch($this->publication->get_content_object()->get_type(), $this);
    }

    function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    function get_publication()
    {
        return $this->publication;
    }

    function is_allowed($right){
        return $this->is_allowed($right, $this->get_publication());
    }
}
?>