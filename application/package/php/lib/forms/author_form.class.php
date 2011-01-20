<?php

namespace application\package;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\InCondition;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use rights\RightsUtilities;
use user\UserDataManager;
use common\libraries\ObjectTableOrder;
use user\User;
use common\libraries\Utilities;
use common\libraries\WebApplication;
/**
 * This class describes the form for a PackageLanguage object.
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 **/
class AuthorForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const PACKAGE = 'package';
    
    private $package;
    private $user;

    function __construct($form_type, $package, $action, $user)
    {
        parent :: __construct('author_settings', 'post', $action);
        
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
        $this->addElement('text', Author :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(Author :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('text', Author :: PROPERTY_EMAIL, Translation :: get('Email'));
        $this->addRule(Author :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $this->addElement('text', Author :: PROPERTY_COMPANY, Translation :: get('Company'));
        $this->addRule(Author :: PROPERTY_COMPANY, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        $url = WebApplication :: get_application_web_path('package') . 'php/xml_feeds/xml_package_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('AddPackageAuthors');
        $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);
        $hidden = true;
        
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
            $return_package['id'] = 'author_' . $package->get_id();
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

    function update_author()
    {
        $author = $this->package;
        $values = $this->exportValues();
        
        $author->set_name($values[Author :: PROPERTY_NAME]);
        $author->set_email($values[Author :: PROPERTY_EMAIL]);
        $author->set_company($values[Author :: PROPERTY_COMPANY]);
        
        if (! $author->update())
        {
            return false;
        }

        $original_packages = $author->get_packages();
        $current_packages = $values[self :: PACKAGE][self :: PACKAGE];
        $packages_to_remove = array_diff($original_packages, $current_packages);
        $packages_to_add = array_diff($current_packages, $original_packages);
        
        foreach ($packages_to_add as $package)
        {
            $package_author = new PackageAuthor();
            $package_author->set_author_id($author->get_id());
            $package_author->set_package_id($package);
            if (! $package_author->create())
            {
                return false;
            }
        }
        
        if (count($packages_to_remove) > 0)
        {
            $conditions = array();
            $conditions[] = new InCondition(PackageAuthor :: PROPERTY_PACKAGE_ID, $packages_to_remove);
            $conditions[] = new EqualityCondition(PackageAuthor :: PROPERTY_AUTHOR_ID, $author->get_id());
            $condition = new AndCondition($conditions);
            
            if (! PackageDataManager :: get_instance()->delete_objects(PackageAuthor :: get_table_name(), $condition))
            {
                return false;
            }
        }
        
        return true;
    }

    function create_author()
    {
        $author = $this->package;
        $values = $this->exportValues();
        
        $author->set_name($values[Author :: PROPERTY_NAME]);
        $author->set_email($values[Author :: PROPERTY_EMAIL]);
        $author->set_company($values[Author :: PROPERTY_COMPANY]);

        if (! $author->create())
        {
            return false;
        }
        else
        {
            $packages = $values[self :: PACKAGE];
            foreach ($packages as $package)
            {
                $package_author = new PackageAuthor();
                $package_author->set_author_id($author->get_id());
                $package_author->set_package_id($package);
                
                if (! $package_author->create())
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
        
        $defaults[Author :: PROPERTY_NAME] = $package->get_name();
        $defaults[Author :: PROPERTY_EMAIL] = $package->get_email();
        $defaults[Author :: PROPERTY_COMPANY] = $package->get_company();
        
        parent :: setDefaults($defaults);
    }
}
?>