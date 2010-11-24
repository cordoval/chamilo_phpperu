<?php
namespace application\weblcms\tool\rights;

use application\weblcms\WeblcmsRights;
use application\weblcms\Tool;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use application\weblcms\ToolComponent;
use common\libraries\DelegateComponent;
use common\libraries\Translation;

class RightsToolRightsEditorComponent extends RightsTool implements DelegateComponent
{

    function run()
    {
        ToolComponent :: factory(ToolComponent :: RIGHTS_EDITOR_COMPONENT, $this)->run();

     //ToolComponent :: launch($this,RightsTool);
    //the launch method results in the default action of the toolcomponent, not the default action of the rights tool!
    //this needs to be looked at
    }

    function get_available_rights()
    {
        return WeblcmsRights :: get_available_rights();
    }

    function get_additional_parameters()
    {
        array(Tool :: PARAM_PUBLICATION_ID);
    }
}
?>