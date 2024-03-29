<?php
namespace application\weblcms\tool\document;

use repository\content_object\calendar_event\CalendarEvent;
use repository\content_object\document\Document;
use application\weblcms\ContentObjectPublication;
use application\weblcms\WeblcmsManager;
use application\weblcms\Tool;
use common\libraries\PatternMatchCondition;
use repository\ContentObject;
use application\weblcms\ContentObjectPublicationListRenderer;
use common\libraries\SubselectCondition;
use repository\RepositoryDataManager;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\OrCondition;
use common\libraries\InequalityCondition;
use common\libraries\Request;
use application\weblcms\ToolComponent;
use common\libraries\Translation;


require_once dirname(__FILE__) . '/document_browser/document_cell_renderer.class.php';

class DocumentToolBrowserComponent extends DocumentTool
{
    const PARAM_FILTER = 'filter';
    const FILTER_TODAY = 'today';
    const FILTER_THIS_WEEK = 'week';
    const FILTER_THIS_MONTH = 'month';

    function run()
    {
        $category = $this->get_category(Request :: get(WeblcmsManager :: PARAM_CATEGORY));
        ToolComponent :: launch($this);
    }

    function get_tool_actions()
    {
        $tool_actions = array();
        $tool_actions[] = new ToolbarItem(Translation :: get('ShowToday'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null, self :: PARAM_FILTER => self :: FILTER_TODAY)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $tool_actions[] = new ToolbarItem(Translation :: get('ShowThisWeek'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null, self :: PARAM_FILTER => self :: FILTER_THIS_WEEK)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $tool_actions[] = new ToolbarItem(Translation :: get('ShowThisMonth'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null, self :: PARAM_FILTER => self :: FILTER_THIS_MONTH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $tool_actions[] = new ToolbarItem(Translation :: get('Download'), Theme :: get_common_image_path() . 'action_save.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_ZIP_AND_DOWNLOAD)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        //        $tool_actions[] = new ToolbarItem(Translation :: get('Slideshow'), Theme :: get_common_image_path() . 'action_slideshow.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_SLIDESHOW)), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        return $tool_actions;
    }

    function get_tool_conditions()
    {
        $conditions = array();
        $filter = Request :: get(self :: PARAM_FILTER);

        switch ($filter)
        {
            case self :: FILTER_TODAY :
                $time = mktime(0, 0, 0, date('m', time()), date('d', time()), date('Y', time()));
                $conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_MODIFIED_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $time);
                break;
            case self :: FILTER_THIS_WEEK :
                $time = strtotime('Next Monday', strtotime('-1 Week', time()));
                $conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_MODIFIED_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $time);
                break;
            case self :: FILTER_THIS_MONTH :
                $time = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
                $conditions[] = new InequalityCondition(ContentObjectPublication :: PROPERTY_MODIFIED_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $time);
                break;
        }

        $browser_type = $this->get_browser_type();
        if ($browser_type == ContentObjectPublicationListRenderer :: TYPE_GALLERY || $browser_type == ContentObjectPublicationListRenderer :: TYPE_SLIDESHOW)
        {
            $image_types = Document :: get_image_types();
            $image_conditions = array();
            foreach ($image_types as $image_type)
            {
                $image_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $image_type, Document :: get_type_name());
            }

            $image_condition = new OrCondition($image_conditions);

            $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, Document :: get_type_name(), $image_condition, null, RepositoryDataManager :: get_instance());
        }

        return $conditions;
    }

    function convert_content_object_publication_to_calendar_event($publication, $from_time, $to_time)
    {
        $object = $publication->get_content_object();

        $calendar_event = ContentObject :: factory(CalendarEvent :: get_type_name());
        $calendar_event->set_title($object->get_title());
        $calendar_event->set_description($object->get_description());
        $calendar_event->set_start_date($publication->get_modified_date());
        $calendar_event->set_end_date($publication->get_modified_date());
        $calendar_event->set_repeat_type(CalendarEvent :: REPEAT_TYPE_NONE);

        $publication->set_content_object($calendar_event);

        return $publication;
    }

    function get_content_object_publication_table_cell_renderer($tool_browser)
    {
        return new DocumentCellRenderer($tool_browser);
    }
}
?>