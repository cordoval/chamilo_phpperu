<?php
namespace repository;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Theme;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;
use common\libraries\EqualityCondition;

use admin\Registration;
use admin\PackageInfo;

require_once Path :: get_admin_path() . 'lib/package_installer/source/package_info/package_info.class.php';

/**
 * Render content object type selection tabs based on their category
 * 
 * @author Hans De Bisschop
 */
class ContentObjectTypeSelector
{
    const TAB_MOST_USED = 'most_used';
    const TAB_EXTRA = 'extra';
    
    private $parent;
    
    /**
     * @var array
     */
    private $content_object_types;
    
    /**
     * @var array
     */
    private $additional_links;
    
    /**
     * @var boolean
     */
    private $use_general_statistics;

    /**
     * @param array $content_object_types
     */
    function ContentObjectTypeSelector($parent, $content_object_types = array(), $additional_links = array(), $use_general_statistics = false)
    {
        $this->parent = $parent;
        $this->content_object_types = $content_object_types;
        $this->additional_links = $additional_links;
        $this->use_general_statistics = $use_general_statistics;
        $this->prepare();
    }

    function get_parent()
    {
        return $this->parent;
    }

    function as_html()
    {
        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);
        
        if (count($this->content_object_types) > 15)
        {
            $tabs->add_tab(new DynamicContentTab(self :: TAB_MOST_USED, Translation :: get('MostUsed'), Theme :: get_image_path() . 'place_mini_most_used.png', $this->render_most_used()));
        }
        
        foreach ($this->categories as $category => $category_name)
        {
            $tabs->add_tab(new DynamicContentTab($category, $category_name, Theme :: get_image_path() . 'place_mini_' . $category . '.png', $this->render_category($category)));
        }
        
        if (count($this->additional_links) > 0)
        {
            $tabs->add_tab(new DynamicContentTab(self :: TAB_EXTRA, Translation :: get('Extra'), Theme :: get_image_path() . 'place_mini_extra.png', $this->render_additional_links()));
        }
        
        return $tabs->render();
    }

    private function prepare()
    {
        if (! $this->use_general_statistics)
        {
            $condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_parent()->get_user_id());
        }
        else
        {
            $condition = null;
        }
        
        foreach ($this->content_object_types as $type)
        {
            if (! $this->get_parent()->is_allowed_to_create($type))
            {
                continue;
            }
            
            $package_info = PackageInfo :: factory(Registration :: TYPE_CONTENT_OBJECT, $type);
            $package_info = $package_info->get_package_info();
            $category = $package_info['package']['category'];
            $category_name = Translation :: get(Utilities :: underscores_to_camelcase($category));
            
            if (! in_array($category, array_keys($this->categories)))
            {
                $this->categories[$category] = $category_name;
            }
            
            if (! is_array($this->content_object_type_categories[$category]))
            {
                $this->content_object_type_categories[$category] = array();
            }
            
            $this->content_object_type_categories[$category][Translation :: get(Utilities :: get_classname_from_namespace(ContentObject :: type_to_class($type)) . 'TypeName')] = $type;
            
            $count = RepositoryDataManager :: get_instance()->count_type_content_objects($type, $condition);
            $this->content_object_type_counts[$type] = $count;
            if ($count > $this->most_used_type_count)
            {
                $this->most_used_type_count = $count;
            }
        }
        
        arsort($this->content_object_type_counts, SORT_STRING);
        asort($this->categories);
    }

    function render_most_used()
    {
        $limit = round($this->most_used_type_count / 2);
        $type_counts = array_slice($this->content_object_type_counts, 0, 10);
        
        $html = array();
        
        foreach ($type_counts as $type => $count)
        {
            if ($count > 0)
            {
                $html[] = '<a href="' . $this->get_parent()->get_content_object_type_creation_url($type) . '">';
                $html[] = '<div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
                $html[] = Translation :: get(Utilities :: get_classname_from_namespace(ContentObject :: type_to_class($type) . 'TypeName'));
                $html[] = '</div>';
                $html[] = '</a>';
            }
        }
        
        return implode("\n", $html);
    }

    function render_category($category)
    {
        $types = $this->content_object_type_categories[$category];
        ksort($types);
        
        $html = array();
        
        foreach ($types as $name => $type)
        {
            $html[] = '<a href="' . $this->get_parent()->get_content_object_type_creation_url($type) . '">';
            $html[] = '<div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
            $html[] = $name;
            $html[] = '</div>';
            $html[] = '</a>';
        }
        
        return implode("\n", $html);
    }

    function render_additional_links()
    {
        $html = array();
        
        foreach ($this->additional_links as $link)
        {
            $type = $link['type'];
            $html[] = '<a href="' . $link['url'] . '">';
            $html[] = '<div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
            $html[] = $link['title'];
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
            $html[] = '</a>';
        }
        
        return implode("\n", $html);
    }
}
?>