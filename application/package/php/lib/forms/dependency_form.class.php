<?php

namespace application\package;

use common\libraries;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\WebApplication;
use common\libraries\ObjectTableOrder;

use rights\RightsUtilities;

use user\UserDataManager;
use user\User;

/**
 * This class describes the form for a PackageLanguage object.
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 **/
class DependencyForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const PACKAGE = 'package';
    
    private $package;
    private $user;

    function __construct($form_type, $package, $action, $user)
    {
        parent :: __construct('dependency_settings', 'post', $action);
        
        $this->package = $package;
        $this->user = $user;
        $this->form_type = $form_type;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('category', Translation :: get('Properties'));
        $this->addElement('text', Dependency :: PROPERTY_ID_DEPENDENCY, Translation :: get('Id'));
        $this->addRule(Dependency :: PROPERTY_ID_DEPENDENCY, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('text', Dependency :: PROPERTY_SEVERITY, Translation :: get('Severity'));
        $this->addRule(Dependency :: PROPERTY_SEVERITY, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('text', Dependency :: PROPERTY_VERSION, Translation :: get('Version'));
        $this->addRule(Dependency :: PROPERTY_VERSION, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $url = WebApplication :: get_application_web_path('package') . 'php/xml_feeds/xml_package_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('AddPackageDependencys');
        $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);
        //$hidden = true;
        
        $elem = $this->addElement('element_finder', self :: PACKAGE, Translation :: get('Packages'), $url, $locale, $this->packages_for_element_finder());
        $this->addElement('category');
    }

    function packages_for_element_finder()
    {
        $packages = $this->package->get_packages(false);

        $return = array();
        
        while ($package = $packages->next_result())
        {
            $return_package = array();
            $return_package['id'] = 'dependency_' . $package->get_id();
            $return_package['classes'] = 'type type_package';
            $return_package['title'] = $package->get_name();
            $return_package['description'] = $package->get_name();
            $return[$package->get_id()] = $return_package;
        }
        
        return $return;
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        //$this->addElement('hidden', PackageLanguage :: PROPERTY_ID);
        

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_dependency()
    {
        $dependency = $this->package;
        $values = $this->exportValues();
        
        $dependency->set_id_dependency($values[Dependency :: PROPERTY_ID_DEPENDENCY]);
        $dependency->set_severity($values[Dependency :: PROPERTY_SEVERITY]);
        $dependency->set_version($values[Dependency :: PROPERTY_VERSION]);
        
        if (! $dependency->update())
        {
            return false;
        }

        $original_packages = $dependency->get_packages();
        $current_packages = $values[self :: PACKAGE][self :: PACKAGE];
        $packages_to_remove = array_diff($original_packages, $current_packages);
        $packages_to_add = array_diff($current_packages, $original_packages);
        
        foreach ($packages_to_add as $package)
        {
            $package_dependency = new PackageDependency();
            $package_dependency->set_dependency_id($dependency->get_id());
            $package_dependency->set_package_id($package);
            if (! $package_dependency->create())
            {
                return false;
            }
        }
        
        if (count($packages_to_remove) > 0)
        {
            $conditions = array();
            $conditions[] = new InCondition(PackageDependency :: PROPERTY_PACKAGE_ID, $packages_to_remove);
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_ID, $dependency->get_id());
            $condition = new AndCondition($conditions);
            
            if (! PackageDataManager :: get_instance()->delete_objects(PackageDependency :: get_table_name(), $condition))
            {
                return false;
            }
        }
        
        return true;
    }

    function create_dependency()
    {
        $dependency = $this->package;
        $values = $this->exportValues();
        
        $dependency->set_id_dependency($values[Dependency :: PROPERTY_ID_DEPENDENCY]);
        $dependency->set_severity($values[Dependency :: PROPERTY_SEVERITY]);
        $dependency->set_version($values[Dependency :: PROPERTY_VERSION]);

        if (! $dependency->create())
        {
            return false;
        }
        else
        {
            $packages = $values[self :: PACKAGE];
            foreach ($packages as $package)
            {
                $package_dependency = new PackageDependency();
                $package_dependency->set_author_id($dependency->get_id());
                $package_dependency->set_package_id($package);
                
                if (! $package_dependency->create())
                {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $package = $this->package;
        
        $defaults[Dependency :: PROPERTY_ID_DEPENDENCY] = $package->get_id_dependency();
        $defaults[Dependency :: PROPERTY_SEVERITY] = $package->get_severity();
        $defaults[Dependency :: PROPERTY_VERSION] = $package->get_version();
        
        parent :: setDefaults($defaults);
    }
}
?>