<?php
namespace repository\content_object\handbook;

use common\libraries\BreadcrumbTrail;
use repository\RepositoryDataManager;
use repository\content_object\wiki_page\WikiPage;
use repository\content_object\link\Link;
use repository\content_object\document\Document;
use repository\content_object\youtube\Youtube;
use repository\content_object\glossary\Glossary;
use repository\ContentObjectDisplay;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use repository\RepositoryRights;
use admin\AdminDataManager;
use admin\Registration;
use repository\ComplexBuilder;
use repository\ContentObjectTypeSelectorSupport;

use common\libraries\ResourceManager;
use common\libraries\Path;
use repository\ContentObject;
use common\libraries\Theme;
use repository\content_object\handbook_topic\HandbookTopic;

require_once dirname(__FILE__) . '/browser/handbook_browser_table_cell_renderer.class.php';

class HandbookBuilderBrowserComponent extends HandbookBuilder implements ContentObjectTypeSelectorSupport
{
    

    function run()
    {
        $html = array();
//        $this->complex_builder_browser_component = ComplexBuilderComponent::factory(ComplexBuilderComponent::BROWSER_COMPONENT, $this);
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail::get_instance();
        $trail->merge($menu_trail);
        $trail->add_help('repository handbook builder');

        if ($this->get_complex_content_object_item())
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }

        $this->display_header();
        
        


        $html[] =  '<br />';
        //         $types = array(Handbook :: get_type_name(), WikiPage:: get_type_name(), Link :: get_type_name(),  Document::get_type_name(), Youtube :: get_type_name(), Glossary::get_type_name());
        $types = array(Handbook :: get_type_name(), HandbookTopic::get_type_name(), Glossary::get_type_name(), Link::get_type_name(), Document::get_type_name());

        $html[] =  $this->get_creation_links($content_object, $types);
        $html[] =  '<div class="clear">&nbsp;</div><br />';

        $html[] =  '<div style="width: 18%; overflow: auto; float: left;">';
        $html[] =  $this->get_complex_content_object_menu();
        $html[] =  '</div><div style="width: 80%; float: right;">';
        $html[] =  $this->get_complex_content_object_table_html(false, null, new HandbookBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition()));
        $html[] =  '</div>';
        $html[] =  '<div class="clear">&nbsp;</div>';
        
        echo  implode("\n", $html);
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
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_publish.png', $_SESSION['redirect_url']));
            return $action_bar;
        }
    }
    
    function get_content_object_type_creation_url($type)
    {
        if ($type == Handbook :: get_type_name())
        {
            return $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => ($this->get_complex_content_object_item() ? $this->get_complex_content_object_item()->get_id() : null)));
        }
        else
        {
            return $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => HandbookBuilder :: ACTION_CREATE_HANDBOOK_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
        }
    }
    
    function is_allowed_to_create($type)
    {
        return true;
    }
}

?>