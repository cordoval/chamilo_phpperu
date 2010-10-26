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

use common\libraries\ResourceManager;
use common\libraries\Path;
use repository\ContentObject;
use common\libraries\Theme;

require_once dirname(__FILE__) . '/browser/handbook_browser_table_cell_renderer.class.php';

class HandbookBuilderBrowserComponent extends HandbookBuilder
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
        //NATHALIE: HIER WORDT BEPAALD WAT ER IN GESTOPT KAN WORDEN
//        $types = array(Handbook :: get_type_name(), Announcement :: get_type_name(), BlogItem :: get_type_name(), CalendarEvent :: get_type_name(), Description :: get_type_name(),
//        			  Document :: get_type_name(), Link :: get_type_name(), Note :: get_type_name(), RssFeed :: get_type_name(), Profile :: get_type_name(), Youtube :: get_type_name());
         $types = array(Handbook :: get_type_name(), WikiPage:: get_type_name(), Link :: get_type_name(),  Document::get_type_name(), Youtube :: get_type_name(), Glossary::get_type_name());

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
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $_SESSION['redirect_url']));
            return $action_bar;
        }
    }

    function get_creation_links($content_object, $types = array())
    {
        $html[] = '<div class="select_complex_element">';
        $html[] = '<span class="title">' . Theme :: get_common_image('place_content_objects') . Translation :: get('HandbookAddContentObject') . '</span>';
        $html[] = '<div id="content_object_selection">';

        if (count($types) == 0)
        {
            $types = $content_object->get_allowed_types();
        }

        foreach ($types as $type)
        {
//        	if(!RepositoryRights :: is_allowed_in_content_objects_subtree(RepositoryRights :: ADD_RIGHT, AdminDataManager :: get_registration($type, Registration :: TYPE_CONTENT_OBJECT)->get_id()))
//            {
//            	continue;
//            }
            
        	if ($type == Handbook :: get_type_name())
            {
                $url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => ($this->get_complex_content_object_item() ? $this->get_complex_content_object_item()->get_id() : null)));
            }
            else
            {
                $url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => HandbookBuilder :: ACTION_CREATE_PORTFOLIO_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
            }

            $html[] = '<a href="' . $url . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
            $html[] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName');
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div></a>';
        }

        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}

?>