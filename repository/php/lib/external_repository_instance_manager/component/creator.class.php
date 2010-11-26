<?php
namespace repository;

use admin;

use admin\PackageInfo;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Theme;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;
use common\libraries\Filesystem;
use common\extensions\external_repository_manager\ExternalRepositoryManager;

use DOMDocument;

require_once dirname(__FILE__) . '/../forms/external_repository_form.class.php';

class ExternalRepositoryInstanceManagerCreatorComponent extends ExternalRepositoryInstanceManager
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('external_repository general');

        if (! $this->get_user()->is_platform_admin())
        {
            $this->not_allowed();
        }

        $type = Request :: get(ExternalRepositoryInstanceManager :: PARAM_EXTERNAL_REPOSITORY_TYPE);

        if ($type && ExternalRepositoryManager :: exists($type))
        {
            $external_repository = new ExternalRepository();
            $external_repository->set_type($type);
            $form = new ExternalRepositoryForm(ExternalRepositoryForm :: TYPE_CREATE, $external_repository, $this->get_url(array(ExternalRepositoryInstanceManager :: PARAM_EXTERNAL_REPOSITORY_TYPE => $type)));
            if ($form->validate())
            {
                $success = $form->create_external_repository();
                $this->redirect(Translation :: get($success ? 'ObjectAdded' : 'ObjectNotAdded', array('OBJECT' => Translation :: get('ExternalRepository')), Utilities :: COMMON_LIBRARIES), ($success ? false : true), array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_BROWSE_INSTANCES));
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
            $repository_types = $this->get_external_repository_types();

            $renderer_name = Utilities :: get_classname_from_object($this, true);
            $tabs = new DynamicTabsRenderer($renderer_name);

            if (count($repository_types['sections']) == 0)
            {
                $this->display_header();
                $this->display_warning_message(Translation :: get('NoExternalRepositoriesAvailable'));
                $this->display_footer();
                exit;
            }

            foreach ($repository_types['sections'] as $category => $category_name)
            {
                $types_html = array();

                foreach ($repository_types['types'][$category] as $type => $name)
                {
                    $types_html[] = '<a href="' . $this->get_url(array(ExternalRepositoryInstanceManager :: PARAM_EXTERNAL_REPOSITORY_TYPE => $type)) . '"><div class="create_block" style="background-image: url(' . Theme :: get_image_path(ExternalRepositoryManager :: get_namespace($type)) . 'logo/48.png);">';
                    $types_html[] = $name;
                    $types_html[] = '</div></a>';
                }

                $tabs->add_tab(new DynamicContentTab($category, $category_name, Theme :: get_image_path(ExternalRepositoryManager :: get_namespace()) . 'category_' . $category . '.png', implode("\n", $types_html)));
            }

            $this->display_header();
            echo $tabs->render();
            $this->display_footer();
        }
    }

    function get_external_repository_types()
    {
        $active_managers = ExternalRepositoryManager :: get_registered_types();

        $types = array();
        $sections = array();

        while ($active_manager = $active_managers->next_result())
        {
            $package_info = PackageInfo :: factory($active_manager->get_type(), $active_manager->get_name());
            $package_info = $package_info->get_package_info();

            $section = isset($package_info['package']['category']) ? $package_info['package']['category'] : 'various';
            $multiple = isset($package_info['package']['extra']['multiple']) ? $package_info['package']['extra']['multiple'] : false;

            $condition = new EqualityCondition(ExternalRepository :: PROPERTY_TYPE, $active_manager->get_name());
            $count = $this->count_external_repositories($condition);
            if (! $multiple && $count > 0)
            {
                continue;
            }

            if (! in_array($section, array_keys($sections)))
            {
                $sections[$section] = Translation :: get('Category' . Utilities :: underscores_to_camelcase($section), null, ExternalRepositoryManager :: get_namespace());
            }

            if (! isset($types[$section]))
            {
                $types[$section] = array();
            }

            $types[$section][$active_manager->get_name()] = Translation :: get('TypeName', null, ExternalRepositoryManager :: get_namespace($active_manager->get_name()));
            asort($types[$section]);
        }

        asort($sections);
        return array('sections' => $sections, 'types' => $types);
    }
}
?>