<?php
namespace repository;

use common\libraries\PlatformSetting;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Theme;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;
use common\libraries\EqualityCondition;
use common\libraries\FormValidator;
use common\libraries\ResourceManager;

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

    const PARAM_CONTENT_OBJECT_TYPE = 'content_object_type';

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
     * @var FormValidator
     */
    private $form;

    /**
     * @param array $content_object_types
     */
    function __construct($parent, $content_object_types = array(), $additional_links = array(), $use_general_statistics = false)
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
        $html = array();
        $html[] = $this->render_form();
        $html[] = $this->render_tabs();
        return implode("\n", $html);
    }

    function as_tree()
    {
        $type_options = array();
        $type_options[] = '-- ' . Translation :: get('SelectAContentObjectType') . ' --';

        $prefix = (count($this->categories) > 1 ? '&mdash; ' : '');

        foreach ($this->categories as $category => $category_name)
        {
            if (count($this->categories) > 1)
            {
                $type_options[] = $category_name;
            }

            $types = $this->content_object_type_categories[$category];
            ksort($types);

            foreach ($types as $name => $type)
            {
                $type_options[$type] = $prefix . $name;
            }
        }

        return $type_options;
    }

    function render_tabs()
    {
        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);

        if (count($this->content_object_types) > 15)
        {
            $tabs->add_tab(new DynamicContentTab(self :: TAB_MOST_USED, Translation :: get('MostUsed'), Theme :: get_image_path('repository') . 'place_mini_most_used.png', $this->render_most_used()));
        }

        foreach ($this->categories as $category => $category_name)
        {
            $tabs->add_tab(new DynamicContentTab($category, $category_name, Theme :: get_image_path('repository') . 'place_mini_' . $category . '.png', $this->render_category($category)));
        }

        if (count($this->additional_links) > 0)
        {
            $tabs->add_tab(new DynamicContentTab(self :: TAB_EXTRA, Translation :: get('Extra'), Theme :: get_image_path('repository') . 'place_mini_extra.png', $this->render_additional_links()));
        }

        return $tabs->render();
    }

    function render_form()
    {
        $html = array();

        $renderer = clone $this->form->defaultRenderer();
        $renderer->setElementTemplate('{label}&nbsp;&nbsp;{element}&nbsp;');
        $this->form->accept($renderer);

        $html = array();
        $html[] = '<div style="margin-bottom: 20px;">';
        $html[] = $renderer->toHTML();
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get_web_common_libraries_path() . 'resources/javascript/postback.js');
        $html[] = '</div>';

        return implode("\n", $html);
    }

    function get_selection()
    {
        return $this->form->validate() ? $this->form->exportValue(self :: PARAM_CONTENT_OBJECT_TYPE) : null;
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

            $setting = PlatformSetting :: get('allow_' . $type . '_creation', 'repository');
            if (! $setting)
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

            $this->content_object_type_categories[$category][Translation :: get('TypeName', array(), ContentObject :: get_content_object_type_namespace($type))] = $type;

            $count = RepositoryDataManager :: get_instance()->count_type_content_objects($type, $condition);
            $this->content_object_type_counts[$type] = $count;
            if ($count > $this->most_used_type_count)
            {
                $this->most_used_type_count = $count;
            }
        }

        arsort($this->content_object_type_counts, SORT_STRING);
        asort($this->categories);

        $this->form = new FormValidator('select_content_object_type', 'post', $this->parent->get_url());
        $select = $this->form->addElement('select', self :: PARAM_CONTENT_OBJECT_TYPE, Translation :: get('CreateANew'), array(), array('class' => 'learning-object-creation-type postback'));

        foreach ($this->as_tree() as $key => $type)
        {
            $attributes = (is_integer($key) && $key != 0) ? array('disabled') : array();
            $select->addOption($type, $key, $attributes);
        }

        $this->form->addElement('style_submit_button', 'submit', Translation :: get('Select'), array('class' => 'normal select'));
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
                $namespace = ContentObject :: get_content_object_type_namespace($type);

                $html[] = '<a href="' . $this->get_parent()->get_content_object_type_creation_url($type) . '">';
                $html[] = '<div class="create_block" style="background-image: url(' . Theme :: get_image_path($namespace) . 'logo/48.png);">';
                $html[] = Translation :: get('TypeName', array(), $namespace);
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
            $namespace = ContentObject :: get_content_object_type_namespace($type);

            $html[] = '<a href="' . $this->get_parent()->get_content_object_type_creation_url($type) . '">';
            $html[] = '<div class="create_block" style="background-image: url(' . Theme :: get_image_path($namespace) . 'logo/48.png);">';
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
            $html[] = '<div class="create_block" style="background-image: url(' . Theme :: get_image_path(Utilities :: get_namespace_from_object($this->parent)) . 'type_selector_' . $type . '.png);">';
            $html[] = $link['title'];
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
            $html[] = '</a>';
        }

        return implode("\n", $html);
    }
}
?>