<?php
namespace application\package;

use common\libraries;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\WebApplication;
use common\libraries\Theme;
use common\libraries\Utilities;
use common\libraries\InCondition;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\ObjectTableOrder;
use common\libraries\ResourceManager;
use common\libraries\Request;
use common\libraries\PlatformSetting;
use common\libraries\ArrayResultSet;
use common\libraries\ResultSet;

use admin;
use admin\Registration;
use rights\RightsUtilities;
use user\UserDataManager;
use user\User;

/**
 * This class describes the form for a PackageLanguage object.
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 **/
class PackageForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const DEPENDENCY = 'dependency';
    const PACKAGE = 'package';
    const AUTHOR = 'author';
    
    const PHASE = 'phase';
    
    private $package;
    private $user;

    function __construct($form_type, $package, $action, $user)
    {
        parent :: __construct('package_settings', 'post', $action);
        
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
        $this->addElement('text', Package :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(Package :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('select', Package :: PROPERTY_SECTION, Translation :: get('Section'), Package :: get_section_types());
        $this->addRule(Package :: PROPERTY_SECTION, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('text', Package :: PROPERTY_VERSION, Translation :: get('Version'));
        $this->addRule(Package :: PROPERTY_VERSION, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('select', Package :: PROPERTY_CYCLE_PHASE, Translation :: get('Phase'), Package :: get_phases());
        $this->addRule(Package :: PROPERTY_CYCLE_PHASE, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('select', Package :: PROPERTY_CYCLE_REALM, Translation :: get('Realm'), Package :: get_realms());
        $this->addRule(Package :: PROPERTY_CYCLE_REALM, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('textarea', Package :: PROPERTY_DESCRIPTION, Translation :: get('Description'));
        
        $this->addElement('text', Package :: PROPERTY_CODE, Translation :: get('Code'));
        $this->addRule(Package :: PROPERTY_CODE, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('text', Package :: PROPERTY_CATEGORY, Translation :: get('Category'));
        $this->addRule(Package :: PROPERTY_CATEGORY, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('text', Package :: PROPERTY_HOMEPAGE, Translation :: get('Homepage'));
        
        $this->addElement('text', Package :: PROPERTY_TAGLINE, Translation :: get('Tagline'));
        
        //authors
        $url = WebApplication :: get_application_web_path('package') . 'php/xml_feeds/xml_author_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('AddPackageAuthors');
        $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);
        
        $elem = $this->addElement('element_finder', self :: AUTHOR, Translation :: get('Authors'), $url, $locale, $this->authors_for_element_finder());
        
        //dependencies
        $url_dependency = WebApplication :: get_application_web_path('package') . 'php/xml_feeds/xml_package_dependency_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('AddPackageDependencies');
        $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);
        
        $elem = $this->addElement('element_finder', self :: DEPENDENCY, Translation :: get('Dependencies'), $url_dependency, $locale, $this->dependencies_for_element_finder());
        
        $this->addElement('category');
        
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(WebApplication :: get_application_web_path('package') . 'resources/javascript/package_dependencies.js'));
        
        $this->add_dependencies();
    }

    function allow_file_type($fields)
    {
        $errors = array();
        $file = $_FILES[Package :: PROPERTY_FILENAME]['name'];
        $file = pathinfo($file);
        $type = $file['extension'];
        
        $image_types = array('zip', 'ZIP');
        
        $filtering_type = PlatformSetting :: get('type_of_filtering');
        if ($filtering_type == 'blacklist')
        {
            $blacklist = PlatformSetting :: get('blacklist');
            $items = explode(',', $blacklist);
            if (in_array($type, $items) || ! in_array($type, $image_types))
            {
                $errors[Package :: PROPERTY_FILENAME] = Translation :: get('FileTypeNotAllowed');
            }
            else
            {
                return true;
            }
        }
        else
        {
            $whitelist = PlatformSetting :: get('whitelist');
            $items = explode(',', $whitelist);
            if (in_array($type, $items) && in_array($type, $image_types))
            {
                return true;
            }
            else
            {
                $errors[Package :: PROPERTY_FILENAME] = Translation :: get('FileTypeNotAllowed');
            }
        }
        return $errors;
    }

    function authors_for_element_finder()
    {
        $return = array();
        if ($this->package->get_id())
        {
            $authors = $this->package->get_authors(false);
            
            while ($author = $authors->next_result())
            {
                $return_author = array();
                $return_author['id'] = 'author_' . $author->get_id();
                $return_author['classes'] = 'type type_author';
                $return_author['title'] = $author->get_name();
                $return_author['description'] = $author->get_name();
                $return[$author->get_id()] = $return_author;
            }
        }
        return $return;
    }

    function dependencies_for_element_finder()
    {
        $return = array();
        if ($this->package->get_id())
        {
            $dependencies = $this->package->get_dependencies(false);
            while ($dependency = $dependencies->next_result())
            {
                $data = $dependency->get_dependency(PackageDependency :: TYPE_DEPENDENCY);
                
                $return_dependency = array();
                $return_dependency['id'] = 'dependency_' . $data->get_id();
                $return_dependency['classes'] = 'type type_dependency';
                $return_dependency['title'] = $data->get_name();
                $return_dependency['description'] = $data->get_name() . ' ' . $data->get_version();
                $return[$dependency->get_id()] = $return_dependency;
            }
            
            $dependencies = $this->package->get_package_dependencies(false);
            while ($dependency = $dependencies->next_result())
            {
                $data = $dependency->get_dependency(PackageDependency :: TYPE_PACKAGE);
                
                $return_dependency = array();
                $return_dependency['id'] = 'package_' . $data->get_id();
                $return_dependency['classes'] = 'type type_package_';
                $return_dependency['title'] = $data->get_name();
                $return_dependency['description'] = $data->get_name() . ' ' . $data->get_version();
                $return[$dependency->get_id()] = $return_dependency;
            }
        }
        return $return;
    }

    function packages_for_element_finder()
    {
        $return = array();
        if ($this->package->get_id())
        {
            $dependencies = $this->package->get_package_dependencies(false);
            while ($dependency = $dependencies->next_result())
            {
                $data = $dependency->get_dependency(PackageDependency :: TYPE_PACKAGE);
                
                $return_dependency = array();
                $return_dependency['id'] = 'dependency_' . $data->get_id();
                $return_dependency['classes'] = 'type type_dependency';
                $return_dependency['title'] = $data->get_name();
                $return_dependency['description'] = $data->get_name() . ' ' . $data->get_version();
                $return[$dependency->get_id()] = $return_dependency;
            }
        }
        return $return;
    }

    function add_dependencies()
    {
        $renderer = $this->defaultRenderer();
        if ($this->isSubmitted())
        {
            //dependency
            $dependencies = $values[self :: DEPENDENCY][self :: DEPENDENCY];
            $packages = array();
            $packages[] = '-1';
            $condition = new InCondition(Package :: PROPERTY_ID, $packages);
            $packages = PackageDataManager :: get_instance()->retrieve_packages($condition);
            while ($package = $packages->next_result())
            {
                $dependencies[] = new PackageDependency(array(
                        PackageDependency :: PROPERTY_DEPENDENCY_ID => $package->get_id()));
            }
            $dependencies = new ArrayResultSet($dependencies);
            
            //package            
            $package_dependencies = $values[self :: DEPENDENCY][self :: PACKAGE];
            $packages = array();
            $packages[] = '-1';
            $condition = new InCondition(Package :: PROPERTY_ID, $packages);
            $packages = PackageDataManager :: get_instance()->retrieve_packages($condition);
            while ($package = $packages->next_result())
            {
                $package_dependencies[] = new PackageDependency(array(
                        PackageDependency :: PROPERTY_DEPENDENCY_ID => $package->get_id()));
            }
            $package_dependencies = new ArrayResultSet($package_dependencies);
        }
        else
        {
            $dependencies = $this->package->get_dependencies(false);
            
            $package_dependencies = $this->package->get_package_dependencies(false);
        }
        
        if (($dependencies instanceof ResultSet && $dependencies->size() > 0) || ($package_dependencies instanceof ResultSet && $package_dependencies->size() > 0))
        {
            $classes = '';
        }
        else
        {
            $classes = 'hidden';
        }
        
        $this->addElement('category', Translation :: get('Dependencies'), $classes);
        $table_header = array();
        $table_header[] = '<table id="dependencies_table" class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th>' . Translation :: get('Name') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Version') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Compare') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Severity') . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));
        
        if ($dependencies)
        {
            while ($dependency = $dependencies->next_result())
            {
                $dependency_data = $dependency->get_dependency(PackageDependency :: TYPE_DEPENDENCY);
                
                $option_number = $dependency->get_id();
                
                $group = array();
                $group[] = $this->createElement('static', null, null, $dependency_data->get_name());
                $group[] = $this->createElement('static', null, null, $dependency_data->get_version());
                $group[] = $this->createElement('select', 'dependency_compare_' . $option_number, null, self :: get_compare_options());
                $group[] = $this->createElement('select', 'dependency_severity_' . $option_number, null, admin\PackageDependency :: get_severity_options());
                
                $this->addGroup($group, PackageDependency :: PROPERTY_DEPENDENCY_ID . '_' . $option_number, null, '', false);
                
                $renderer->setElementTemplate('<tr id="dependency_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', PackageDependency :: PROPERTY_DEPENDENCY_ID . '_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', PackageDependency :: PROPERTY_DEPENDENCY_ID . '_' . $option_number);
            }
        }
        
        if ($package_dependencies)
        {
            while ($dependency = $package_dependencies->next_result())
            {
                $dependency_data = $dependency->get_dependency(PackageDependency :: TYPE_PACKAGE);
                
                $option_number = $dependency->get_id();
                
                $group = array();
                $group[] = $this->createElement('static', null, null, $dependency_data->get_name());
                $group[] = $this->createElement('static', null, null, $dependency_data->get_version());
                $group[] = $this->createElement('select', 'package_compare_' . $option_number, null, self :: get_compare_options());
                $group[] = $this->createElement('select', 'package_severity_' . $option_number, null, admin\PackageDependency :: get_severity_options());
                
                $this->addGroup($group, PackageDependency :: PROPERTY_DEPENDENCY_ID . '_' . $option_number, null, '', false);
                
                $renderer->setElementTemplate('<tr id="package_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', PackageDependency :: PROPERTY_DEPENDENCY_ID . '_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', PackageDependency :: PROPERTY_DEPENDENCY_ID . '_' . $option_number);
            }
        }
        
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));
        $this->addElement('category');
    }

    static function get_compare_options()
    {
        $compare_options = array();
        $compare_options[admin\PackageDependency :: COMPARE_EQUAL] = admin\PackageDependency :: get_operator_name(admin\PackageDependency :: COMPARE_EQUAL);
        $compare_options[admin\PackageDependency :: COMPARE_NOT_EQUAL] = admin\PackageDependency :: get_operator_name(admin\PackageDependency :: COMPARE_NOT_EQUAL);
        $compare_options[admin\PackageDependency :: COMPARE_GREATER_THEN] = admin\PackageDependency :: get_operator_name(admin\PackageDependency :: COMPARE_GREATER_THEN);
        $compare_options[admin\PackageDependency :: COMPARE_GREATER_THEN_OR_EQUAL] = admin\PackageDependency :: get_operator_name(admin\PackageDependency :: COMPARE_GREATER_THEN_OR_EQUAL);
        $compare_options[admin\PackageDependency :: COMPARE_LESS_THEN] = admin\PackageDependency :: get_operator_name(admin\PackageDependency :: COMPARE_LESS_THEN);
        $compare_options[admin\PackageDependency :: COMPARE_LESS_THEN_OR_EQUAL] = admin\PackageDependency :: get_operator_name(admin\PackageDependency :: COMPARE_LESS_THEN_OR_EQUAL);
        
        return $compare_options;
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $this->addElement('file', Package :: PROPERTY_FILENAME, Translation :: get('FileName'));
        $this->addRule(Package :: PROPERTY_FILENAME, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addFormRule(array($this, 'allow_file_type'));
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_package()
    {
        
        $package = $this->package;
        $values = $this->exportValues();
        
        $package->set_name($values[Package :: PROPERTY_NAME]);
        $package->set_version($values[Package :: PROPERTY_VERSION]);
        $package->set_description($values[Package :: PROPERTY_DESCRIPTION]);
        $package->set_section($values[Package :: PROPERTY_SECTION]);
        $package->set_cycle_phase($values[Package :: PROPERTY_CYCLE_PHASE]);
        $package->set_cycle_realm($values[Package :: PROPERTY_CYCLE_REALM]);
        $package->set_code($values[Package :: PROPERTY_CODE]);
        $package->set_category($values[Package :: PROPERTY_CATEGORY]);
        $package->set_filename($_FILES[Package :: PROPERTY_FILENAME]['name']);
        $package->set_temporary_file_path($_FILES[Package :: PROPERTY_FILENAME]['tmp_name']);
        $package->set_homepage($values[Package :: PROPERTY_HOMEPAGE]);
        $package->set_tagline($values[Package :: PROPERTY_TAGLINE]);
        $package->set_status($values[Package :: PROPERTY_STATUS]);
        
        if (! $package->update())
        {
            return false;
        }
        
        $original_authors = $package->get_authors();
        $current_authors = $values[self :: AUTHOR][self :: AUTHOR];
        $authors_to_remove = array_diff($original_authors, $current_authors);
        $authors_to_add = array_diff($current_authors, $original_authors);
        
        foreach ($authors_to_add as $author)
        {
            $package_author = new PackageAuthor();
            $package_author->set_author_id($author);
            $package_author->set_package_id($package->get_id());
            if (! $package_author->create())
            {
                return false;
            }
        }
        
        if (count($authors_to_remove) > 0)
        {
            $conditions = array();
            $conditions[] = new InCondition(PackageAuthor :: PROPERTY_AUTHOR_ID, $authors_to_remove);
            $conditions[] = new EqualityCondition(PackageAuthor :: PROPERTY_PACKAGE_ID, $package->get_id());
            $condition = new AndCondition($conditions);
            
            if (! PackageDataManager :: get_instance()->delete_objects(PackageAuthor :: get_table_name(), $condition))
            {
                return false;
            }
        }
        
        //dependency
        $original_dependencies = $package->get_dependencies_ids();
        $current_dependencies = (array) $values[self :: DEPENDENCY][self :: DEPENDENCY];
        $dependencies_to_remove = array_diff($original_dependencies, $current_dependencies);
        $dependencies_to_add = array_diff($current_dependencies, $original_dependencies);
        $dependencies_to_update = array_intersect($current_dependencies, $original_dependencies);
        
        foreach ($dependencies_to_add as $dependency)
        {
            $package_dependency = new PackageDependency();
            $package_dependency->set_dependency_id($dependency);
            $package_dependency->set_package_id($package->get_id());
            $package_dependency->set_compare(Request :: post('dependency_compare_' . $dependency));
            $package_dependency->set_severity(Request :: post('dependency_severity_' . $dependency));
            $package_dependency->set_dependency_type(PackageDependency :: TYPE_DEPENDENCY);
            
            if (! $package_dependency->create())
            {
                return false;
            }
        }
        
        if (count($dependencies_to_remove) > 0)
        {
            $conditions = array();
            $conditions[] = new InCondition(PackageDependency :: PROPERTY_DEPENDENCY_ID, $dependencies_to_remove);
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_PACKAGE_ID, $package->get_id());
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_TYPE, PackageDependency :: TYPE_DEPENDENCY);
            
            $condition = new AndCondition($conditions);
            
            if (! PackageDataManager :: get_instance()->delete_objects(PackageDependency :: get_table_name(), $condition))
            {
                echo ('remove dep');
                return false;
            }
        }
        
        foreach ($dependencies_to_update as $dependency)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_ID, $dependency);
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_PACKAGE_ID, $package->get_id());
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_TYPE, PackageDependency :: TYPE_DEPENDENCY);
            
            $condition = new AndCondition($conditions);
            
            $package_dependency = PackageDataManager :: get_instance()->retrieve_package_dependencies($condition)->next_result();
            $package_dependency->set_compare($values['dependency_compare_' . $dependency]);
            $package_dependency->set_severity($values['dependency_severity_' . $dependency]);
            
            if (! $package_dependency->update())
            {
                return false;
            }
        }
        
        //package
        $original_dependencies = $package->get_package_dependencies_ids();
//        dump($original_dependencies);
        $current_dependencies = (array) $values[self :: DEPENDENCY][self :: PACKAGE];
//        dump($current_dependencies);
        $dependencies_to_remove = array_diff($original_dependencies, $current_dependencies);
        $dependencies_to_add = array_diff($current_dependencies, $original_dependencies);
        $dependencies_to_update = array_intersect($current_dependencies, $original_dependencies);
//        dump($dependencies_to_add);
        foreach ($dependencies_to_add as $dependency)
        {
            $package_dependency = new PackageDependency();
            $package_dependency->set_dependency_id($dependency);
            $package_dependency->set_package_id($package->get_id());
            $package_dependency->set_compare(Request :: post('package_compare_' . $dependency));
            $package_dependency->set_severity(Request :: post('package_severity_' . $dependency));
            $package_dependency->set_dependency_type(PackageDependency :: TYPE_PACKAGE);
            
            if (! $package_dependency->create())
            {
                return false;
            }
        }
        
        if (count($dependencies_to_remove) > 0)
        {
            $conditions = array();
            $conditions[] = new InCondition(PackageDependency :: PROPERTY_DEPENDENCY_ID, $dependencies_to_remove);
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_PACKAGE_ID, $package->get_id());
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_TYPE, PackageDependency :: TYPE_PACKAGE);
            $condition = new AndCondition($conditions);
            
            if (! PackageDataManager :: get_instance()->delete_objects(PackageDependency :: get_table_name(), $condition))
            {
                echo ('remove package');
                return false;
            }
        }
        
        foreach ($dependencies_to_update as $dependency)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_ID, $dependency);
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_PACKAGE_ID, $package->get_id());
            $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_TYPE, PackageDependency :: TYPE_PACKAGE);
            
            $condition = new AndCondition($conditions);
            
            $package_dependency = PackageDataManager :: get_instance()->retrieve_package_dependencies($condition)->next_result();
            $package_dependency->set_compare($values['package_compare_' . $dependency]);
            $package_dependency->set_severity($values['package_severity_' . $dependency]);
            
            if (! $package_dependency->update())
            {
                return false;
            }
        }
        return true;
    }

    function create_package()
    {
        $package = $this->package;
        $values = $this->exportValues();
        
        $package->set_name($values[Package :: PROPERTY_NAME]);
        $package->set_version($values[Package :: PROPERTY_VERSION]);
        $package->set_description($values[Package :: PROPERTY_DESCRIPTION]);
        $package->set_section($values[Package :: PROPERTY_SECTION]);
        $package->set_cycle_phase($values[Package :: PROPERTY_CYCLE_PHASE]);
        $package->set_cycle_realm($values[Package :: PROPERTY_CYCLE_REALM]);
        $package->set_code($values[Package :: PROPERTY_CODE]);
        $package->set_category($values[Package :: PROPERTY_CATEGORY]);
        $package->set_filename($_FILES[Package :: PROPERTY_FILENAME]['name']);
        $package->set_temporary_file_path($_FILES[Package :: PROPERTY_FILENAME]['tmp_name']);
        $package->set_homepage($values[Package :: PROPERTY_HOMEPAGE]);
        $package->set_tagline($values[Package :: PROPERTY_TAGLINE]);
        $package->set_status($values[Package :: PROPERTY_STATUS]);
        
        if (! $package->create())
        {
            return false;
        }
        else
        {
            $authors = $values[self :: AUTHOR][self :: AUTHOR];
            foreach ($authors as $author)
            {
                $package_author = new PackageAuthor();
                $package_author->set_author_id($author);
                $package_author->set_package_id($package->get_id());
                
                if (! $package_author->create())
                {
                    return false;
                }
            }
            $dependencies = $values[self :: DEPENDENCY][self :: DEPENDENCY];
            foreach ($dependencies as $dependency)
            {
                $package_dependency = new PackageDependency();
                $package_dependency->set_dependency_id($dependency);
                $package_dependency->set_package_id($package->get_id());
                
                $package_dependency->set_compare(Request :: post('dependency_compare_' . $dependency));
                $package_dependency->set_severity(Request :: post('dependency_severity_' . $dependency));
                $package_dependency->set_dependency_type(PackageDependency :: TYPE_DEPENDENCY);
                if (! $package_dependency->create())
                {
                    return false;
                }
            }
            
            $other_packages = $values[self :: DEPENDENCY][self :: PACKAGE];
            foreach ($other_packages as $other_package)
            {
                $package_dependency = new PackageDependency();
                $package_dependency->set_dependency_id($other_package);
                $package_dependency->set_package_id($package->get_id());
                
                $package_dependency->set_compare(Request :: post('package_compare_' . $other_package));
                $package_dependency->set_severity(Request :: post('package_severity_' . $other_package));
                $package_dependency->set_dependency_type(PackageDependency :: TYPE_PACKAGE);
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
        
        $defaults[Package :: PROPERTY_NAME] = $package->get_name();
        $defaults[Package :: PROPERTY_VERSION] = $package->get_version();
        $defaults[Package :: PROPERTY_DESCRIPTION] = $package->get_description();
        $defaults[Package :: PROPERTY_SECTION] = $package->get_section();
        $defaults[Package :: PROPERTY_CYCLE_PHASE] = $package->get_cycle_phase();
        $defaults[Package :: PROPERTY_CYCLE_REALM] = $package->get_cycle_realm();
        $defaults[Package :: PROPERTY_CODE] = $package->get_code();
        $defaults[Package :: PROPERTY_CATEGORY] = $package->get_category();
        $defaults[Package :: PROPERTY_FILENAME] = $package->get_filename();
        $defaults[Package :: PROPERTY_HOMEPAGE] = $package->get_homepage();
        $defaults[Package :: PROPERTY_TAGLINE] = $package->get_tagline();
        $defaults[Package :: PROPERTY_STATUS] = $package->get_status();
        
        $dependencies = $this->package->get_dependencies(false);
        if ($dependencies)
        {
            while ($dependency = $dependencies->next_result())
            {
                $defaults['compare_' . $dependency->get_id()] = $dependency->get_compare();
                $defaults['severity_' . $dependency->get_id()] = $dependency->get_severity();
            }
        }
        parent :: setDefaults($defaults);
    }
}
?>