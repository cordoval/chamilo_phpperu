<?php
namespace application\weblcms;

use common\libraries\Translation;
use common\libraries\Request;

use repository\ComplexBuilder;

/**
 * $Id: complex_builder.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
class ToolComponentComplexBuilderComponent extends ToolComponent
{

    private $content_object;

    function run()
    {
        $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        if ($this->is_allowed(WeblcmsRights :: EDIT_RIGHT,$pid))
        {
            $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($pid);
            $this->content_object = $publication->get_content_object();
            $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $pid);

            ComplexBuilder :: launch($this->content_object->get_type(), $this);
        }
        else
        {
            $this->redirect(Translation :: get("NotAllowed"), '', array(Tool :: PARAM_PUBLICATION_ID => null, 'tool_action' => null));
        }
    }

    function get_root_content_object()
    {
        return $this->content_object;
    }

}

?>