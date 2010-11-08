<?php
namespace application\weblcms\tool\document;

use application\weblcms\WeblcmsRights;
use application\weblcms\Tool;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Display;
use common\libraries\Theme;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Translation;

/**
 * $Id: document_slideshow.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */
/*require_once dirname(__FILE__) . '/../document_tool.class.php';
require_once dirname(__FILE__) . '/../document_tool_component.class.php';*/
require_once dirname(__FILE__) . '/document_slideshow/document_slideshow_browser.class.php';
require_once dirname(__FILE__) . '/../../../category_manager/content_object_publication_category_manager.class.php';

class DocumentToolSlideshowComponent extends DocumentTool
{
    private $action_bar;

    function run()
    {

        $browser = new DocumentSlideshowBrowser($this);
        $this->action_bar = $this->get_action_bar();

        $trail = BreadcrumbTrail :: get_instance();

        $html = $browser->as_html();

        $this->display_header();
        echo $this->action_bar->as_html();
        echo $html;
        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if (Request :: get('thumbnails'))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Slideshow', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_slideshow.png', $this->get_url(array('tool_action' => 'slideshow', 'thumbnails' => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        else
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Thumbnails'), Theme :: get_common_image_path() . 'action_slideshow_thumbnail.png', $this->get_url(array('tool_action' => 'slideshow', 'thumbnails' => 1)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('SlideshowSettings'), Theme :: get_common_image_path() . 'action_config.png', $this->get_url(array('tool_action' => DocumentTool :: ACTION_SLIDESHOW_SETTINGS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('DocumentToolBrowserComponent')));
    }
}
?>