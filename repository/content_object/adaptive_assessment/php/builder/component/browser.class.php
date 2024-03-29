<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\ResourceManager;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\BasicApplication;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;

use repository\content_object\adaptive_assessment\AdaptiveAssessment;
use repository\content_object\announcement\Announcement;
use repository\content_object\assessment\Assessment;
use repository\content_object\blog\Blog;
use repository\content_object\calendar_event\CalendarEvent;
use repository\content_object\description\Description;
use repository\content_object\document\Document;
use repository\content_object\forum\Forum;
use repository\content_object\glossary\Glossary;
use repository\content_object\hotpotatoes\Hotpotatoes;
use repository\content_object\link\Link;
use repository\content_object\note\Note;
use repository\content_object\wiki\Wiki;
use repository\content_object\youtube\Youtube;

use repository\ContentObjectDisplay;
use repository\RepositoryDataManager;
use repository\RepositoryRights;
use repository\ComplexBuilder;
use repository\ContentObject;
use repository\RepositoryManager;
use repository\ContentObjectTypeSelector;
use repository\ContentObjectTypeSelectorSupport;

use admin\AdminDataManager;
use admin\Registration;
use admin\PackageInfo;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentBuilderBrowserComponent extends AdaptiveAssessmentBuilder implements ContentObjectTypeSelectorSupport
{

    function run()
    {
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('repository learnpath builder');

        if ($this->get_complex_content_object_item())
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }

        $this->display_header($trail);
        $action_bar = $this->get_action_bar($content_object);

        if ($action_bar)
        {
            echo '<br />';
            echo $action_bar->as_html();
        }

        echo '<br />';
        $types = array(AdaptiveAssessment :: get_type_name(),
                Assessment :: get_type_name());
        echo $this->get_creation_links($content_object, $types);

        echo '<div style="width: 18%; overflow: auto; float: left;">';
        echo $this->get_complex_content_object_menu();
        echo '</div><div style="width: 80%; float: right;">';
        echo $this->get_complex_content_object_table_html(false, null, new AdaptiveAssessmentBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition()));
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';

        $this->display_footer();
    }

    function get_object_info()
    {
        $html = array();

        $content_object = $this->get_root_content_object();
        $display = ContentObjectDisplay :: factory($content_object);
        $content_object_display = $display->get_full_html();
        $check_empty = trim(strip_tags($content_object_display));

        if (! empty($check_empty) && $check_empty != $content_object->get_title())
        {
            $html[] = '<div class="complex_browser_display">';
            $html[] = $content_object_display;
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

    function get_action_bar()
    {
        $pub = Request :: get('publish');

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($pub && $pub != '')
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $_SESSION['redirect_url']));
            return $action_bar;
        }
    }

    function get_content_object_type_creation_url($type)
    {
        if ($type == AdaptiveAssessment :: get_type_name())
        {
            return $this->get_url(array(
                    ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM,
                    ComplexBuilder :: PARAM_TYPE => $type,
                    ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
        }
        else
        {
            return $this->get_url(array(
                    ComplexBuilder :: PARAM_BUILDER_ACTION => AdaptiveAssessmentBuilder :: ACTION_CREATE_LP_ITEM,
                    ComplexBuilder :: PARAM_TYPE => $type,
                    ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
        }
    }

    function is_allowed_to_create($type)
    {
        return RepositoryRights :: is_allowed_in_content_objects_subtree(RepositoryRights :: ADD_RIGHT, AdminDataManager :: get_registration($type, Registration :: TYPE_CONTENT_OBJECT)->get_id());
    }
}

?>