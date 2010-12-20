<?php
namespace admin;

use common\libraries\ObjectTableOrder;
use common\libraries\Display;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Path;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;

require_once Path :: get_admin_path() . 'lib/package_installer/source/package_info/package_info.class.php';

abstract class RegistrationDisplay
{
    private $component;
    private $package_info;

    function __construct($component)
    {
        $this->component = $component;
        $this->package_info = PackageInfo :: factory($this->get_registration()->get_type(), $this->get_registration()->get_name())->get_package();
    }

    function get_package_info()
    {
        return $this->package_info;
    }

    static function factory($component)
    {
        $registration = $component->get_registration();
        $file = Path :: get_admin_path() . 'lib/registration_viewer/type/' . $registration->get_type() . '.class.php';

        if (! file_exists($file) || ! is_file($file))
        {
            $message = array();
            $message[] = Translation :: get('RegistrationDisplayTypeFailedToLoad') . '<br /><br />';
            $message[] = '<b>' . Translation :: get('File') . ':</b><br />';
            $message[] = $file . '<br /><br />';
            $message[] = '<b>' . Translation :: get('Stacktrace') . ':</b>';
            $message[] = '<ul>';
            $message[] = '<li>' . Translation :: get($registration->get_type()) . '</li>';
            $message[] = '</ul>';

            Display :: header();
            Display :: error_message(implode("\n", $message));
            Display :: footer();
            exit();
        }
        else
        {
            require_once $file;
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($registration->get_type()) . 'RegistrationDisplay';
            return new $class($component);
        }
    }

    function get_component()
    {
        return $this->component;
    }

    function get_registration()
    {
        return $this->get_component()->get_registration();
    }

    function as_html()
    {
        $html = array();
        $html[] = $this->get_stability_information();
        $html[] = $this->get_action_bar()->as_html();
        $html[] = $this->get_properties_table();
        $html[] = $this->get_cycle_table();
        $html[] = $this->get_dependencies_table();
        $html[] = $this->get_update_problems();

        return implode("\n", $html);
    }

    function get_dependencies_table()
    {
        $package_info = $this->get_package_info();

        $html[] = '<h3>' . Translation :: get('Dependencies') . '</h3>';
        $dependencies = $package_info->get_dependencies();
        $html[] = '<table class="data_table data_table_no_header">';
        foreach ($dependencies as $type => $dependency)
        {
            $count = 0;
            foreach ($dependency['dependency'] as $detail)
            {
                $package_dependency = PackageDependency :: factory($type, $detail);
                $html[] = '<tr>';
                if ($count == 0)
                {
                    $html[] = '<td class="header">' . Translation :: get(Utilities :: underscores_to_camelcase($type)) . '</td>';
                }
                else
                {
                    $html[] = '<td></td>';
                }
                $html[] = '<td>' . $package_dependency->as_html() . '</td>';

                $html[] = '</tr>';
                $count ++;
            }
        }
        $html[] = '</table><br/>';
        return implode("\n", $html);
    }

    function get_update_problems()
    {
        if ($this->get_registration()->is_up_to_date())
        {
            return "";
        }

        $conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_CODE, $this->get_registration()->get_name());
        $conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_SECTION, $this->get_registration()->get_type());
        $condition = new AndCondition($conditions);

        $admin = AdminDataManager :: get_instance();
        $order_by = new ObjectTableOrder(RemotePackage :: PROPERTY_VERSION, SORT_DESC);

        $package_remote = $admin->retrieve_remote_packages($condition, $order_by, null, 1);
        if ($package_remote->size() == 1)
        {
            $package_remote = $package_remote->next_result();

            $package_update_dependency = new PackageDependencyVerifier($package_remote);
            $success = $package_update_dependency->is_updatable();
            if ($success)
            {
                $type = 'finished';
            }
            else
            {
                $type = 'failed';
            }
            $html = array();
            $html[] = '<h3>' . Translation :: get('UpdateToVersion') . $package_remote->get_version() . '</h3>';
            $html[] = '<div class="content_object" style="padding: 15px 15px 15px 76px; background-image: url(' . Theme :: get_image_path() . 'place_' . $type . '.png);">';
            $html[] = '<div class="title">' . Translation :: get(DependenciesResultVerification) . '</div>';
            $html[] = '<div class="description">';
            $html[] = $package_update_dependency->get_logger()->render();
            $html[] = '</div>';
            $html[] = '</div>';
            return implode("\n", $html);
        }
    }

    function get_cycle_table()
    {
        $package_info = $this->get_package_info();

        $html = array();
        $html[] = '<h3>' . Translation :: get('ReleaseInformation') . '</h3>';
        $html[] = '<table class="data_table data_table_no_header">';
        $html[] = '<tr><td class="header">' . Translation :: get('Version') . '</td><td>' . $package_info->get_version() . '</td></tr>';
        $html[] = '<tr><td class="header">' . Translation :: get('CyclePhase') . '</td><td>' . Translation :: get('CyclePhase' . Utilities :: underscores_to_camelcase($package_info->get_cycle_phase())) . '</td></tr>';
        $html[] = '<tr><td class="header">' . Translation :: get('CycleRealm') . '</td><td>' . Translation :: get('CycleRealm' . Utilities :: underscores_to_camelcase($package_info->get_cycle_realm())) . '</td></tr>';
        $html[] = '</table><br/>';

        return implode("\n", $html);
    }

    function get_properties_table()
    {
        $package_info = $this->get_package_info();

        $html = array();
        $html[] = '<table class="data_table data_table_no_header">';
        $properties = $package_info->get_default_property_names();

        $hidden_properties = array(
                RemotePackage :: PROPERTY_AUTHORS,
                RemotePackage :: PROPERTY_VERSION,
                RemotePackage :: PROPERTY_CYCLE,
                RemotePackage :: PROPERTY_DEPENDENCIES,
                RemotePackage :: PROPERTY_EXTRA);

        foreach ($properties as $property)
        {
            $value = $package_info->get_default_property($property);
            if (! empty($value) && ! in_array($property, $hidden_properties))
            {
                $html[] = '<tr><td class="header">' . Translation :: get(Utilities :: underscores_to_camelcase($property)) . '</td><td>' . $value . '</td></tr>';
            }
        }

        $authors = $package_info->get_authors();
        foreach ($authors as $key => $author)
        {
            $html[] = '<tr><td class="header">';
            if ($key == 0)
            {
                $html[] = Translation :: get('Authors');
            }
            $html[] = '</td><td>' . Display :: encrypted_mailto_link($author['email'], $author['name']) . ' - ' . $author['company'] . '</td></tr>';
        }

        $html[] = '</table><br/>';

        return implode("\n", $html);
    }

    function get_stability_information()
    {
        $package_info = $this->get_package_info();

        if ($this->get_registration()->is_up_to_date())
        {
            if (! $package_info->is_official() || ! $package_info->is_stable())
            {
                if (! $package_info->is_official() && $package_info->is_stable())
                {
                    $translation_variable = 'WarningPackageUnofficialStable';
                }
                elseif ($package_info->is_official() && ! $package_info->is_stable())
                {
                    $translation_variable = 'WarningPackageOfficialUnstable';
                }
                elseif (! $package_info->is_official() && ! $package_info->is_stable())
                {
                    $translation_variable = 'WarningPackageUnofficialUnstable';
                }

                return Display :: warning_message(Translation :: get($translation_variable), true);
            }
            else
            {
                return Display :: normal_message(Translation :: get('InformationPackageOfficialStable'), true);
            }
        }
        else
        {
            if (! $package_info->is_official() || ! $package_info->is_stable())
            {
                if (! $package_info->is_official() && $package_info->is_stable())
                {
                    $translation_variable = 'WarningPackageInstallUnofficialStable';
                }
                elseif ($package_info->is_official() && ! $package_info->is_stable())
                {
                    $translation_variable = 'WarningPackageInstallOfficialUnstable';
                }
                elseif (! $package_info->is_official() && ! $package_info->is_stable())
                {
                    $translation_variable = 'WarningPackageInstallUnofficialUnstable';
                }

                return Display :: warning_message(Translation :: get($translation_variable), true);
            }
            else
            {
                return Display :: normal_message(Translation :: get('InformationPackageInstallOfficialStable'), true);
            }
        }
    }

    abstract function get_action_bar();
}
?>