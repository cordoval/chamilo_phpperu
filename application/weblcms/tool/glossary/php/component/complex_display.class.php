<?php
namespace application\weblcms\tool\glossary;

use repository\content_object\glossary\GlossaryComplexDisplaySupport;
use application\weblcms\Tool;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use application\weblcms\ToolComponent;
use common\libraries\DelegateComponent;
use common\libraries\Translation;

/**
 * $Id: glossary_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.glossary.component
 */

/**
 * Represents the view component for the assessment tool.
 *
 */
class GlossaryToolComplexDisplayComponent extends GlossaryTool implements
        DelegateComponent,
        GlossaryComplexDisplaySupport
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('GlossaryToolBrowserComponent')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                Tool :: PARAM_ACTION => Tool :: ACTION_VIEW,
                Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), Translation :: get('GlossaryToolViewerComponent')));
    }

    function get_additional_parameters()
    {
        return array(
                Tool :: PARAM_PUBLICATION_ID);
    }

}

?>