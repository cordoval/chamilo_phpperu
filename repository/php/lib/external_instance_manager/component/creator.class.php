<?php
namespace repository;

use common\extensions\external_instance_manager;

use common\libraries;

use admin;

use admin\PackageInfo;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Theme;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;
use common\libraries\Filesystem;
use common\extensions\external_instance_manager\ExternalInstanceManager;

use DOMDocument;

require_once dirname(__FILE__) . '/../forms/external_instance_form.class.php';

class ExternalInstanceManagerCreatorComponent extends ExternalInstanceManager
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('external_instance general');

        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
        }

        $type = Request :: get(ExternalInstanceManager :: PARAM_EXTERNAL_INSTANCE_TYPE);
        if ($type && ExternalInstanceManager :: exists($type))
        {
            $external_instance = new ExternalInstance();
            $external_instance->set_type($type);
            $external_instance->set_instance_type(Utilities :: get_classname_from_namespace(ExternalInstanceManager :: CLASS_NAME, true));
            $form = new ExternalInstanceForm(ExternalInstanceForm :: TYPE_CREATE, $external_instance, $this->get_url(array(
                    ExternalInstanceManager :: PARAM_EXTERNAL_INSTANCE_TYPE => $type)));
            if ($form->validate())
            {
                $success = $form->create_external_instance();
                $this->redirect(Translation :: get($success ? 'ObjectAdded' : 'ObjectNotAdded', array(
                        'OBJECT' => Translation :: get('ExternalInstance')), Utilities :: COMMON_LIBRARIES), ($success ? false : true), array(
                        ExternalInstanceManager :: PARAM_INSTANCE_ACTION => ExternalInstanceManager :: ACTION_BROWSE_INSTANCES));
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
        else
        {
            $this->display_header();

            $renderer_name = Utilities :: get_classname_from_object($this, true);
            $tabs = new DynamicTabsRenderer($renderer_name);

            $repository_types = $this->get_external_instance_types();

            foreach ($repository_types['sections'] as $category => $category_name)
            {
                $types_html = array();

                foreach ($repository_types['types'][$category] as $type => $name)
                {
                    $types_html[] = '<a href="' . $this->get_url(array(
                            ExternalInstanceManager :: PARAM_EXTERNAL_INSTANCE_TYPE => $type)) . '"><div class="create_block" style="background-image: url(' . Theme :: get_image_path(ExternalInstanceManager :: get_namespace($type)) . 'logo/48.png);">';
                    $types_html[] = $name;
                    $types_html[] = '</div></a>';
                }

                $tabs->add_tab(new DynamicContentTab($category, $category_name, Theme :: get_image_path(ExternalInstanceManager :: get_namespace()) . 'category_' . $category . '.png', implode("\n", $types_html)));
            }

            echo $tabs->render();
            $this->display_footer();
        }
    }

    function get_external_instance_types()
    {
        $active_managers = ExternalInstanceManager :: get_registered_types();

        $types = array();
        $sections = array();

        while ($active_manager = $active_managers->next_result())
        {
            $package_info = PackageInfo :: factory($active_manager->get_type(), $active_manager->get_name());
            $package_info = $package_info->get_package_info();

            $section = isset($package_info['package']['category']) ? $package_info['package']['category'] : 'various';
            $multiple = isset($package_info['package']['extra']['multiple']) ? $package_info['package']['extra']['multiple'] : false;

            $conditions = array();
            $conditions[] = new EqualityCondition(ExternalInstance :: PROPERTY_TYPE, $active_manager->get_name());
            $conditions[] = new EqualityCondition(ExternalInstance :: PROPERTY_INSTANCE_TYPE, $active_manager->get_type());
            $condition = new AndCondition($conditions);
            $count = $this->count_videos_conferencing($condition);
            if (! $multiple && $count > 0)
            {
                continue;
            }

            if (! in_array($section, array_keys($sections)))
            {
                $sections[$section] = Translation :: get('Category' . Utilities :: underscores_to_camelcase($section), null, ExternalInstanceManager :: get_namespace());
            }

            if (! isset($types[$section]))
            {
                $types[$section] = array();
            }

            $types[$section][$active_manager->get_name()] = Translation :: get('TypeName', null, ExternalInstanceManager :: get_namespace($active_manager->get_name()));
            asort($types[$section]);
        }

        asort($sections);
        return array('sections' => $sections,
                'types' => $types);
    }
}
?>