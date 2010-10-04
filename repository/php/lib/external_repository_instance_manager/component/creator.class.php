<?php
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
                $this->redirect(Translation :: get($success ? 'ExternalRepositoryAdded' : 'ExternalRepositoryNotAdded'), ($success ? false : true), array(ExternalRepositoryInstanceManager :: PARAM_INSTANCE_ACTION => ExternalRepositoryInstanceManager :: ACTION_BROWSE_INSTANCES));
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
            
            $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
            $tabs = new DynamicTabsRenderer($renderer_name);
            
            $repository_types = $this->get_external_repository_types();
            
            foreach ($repository_types['sections'] as $category => $category_name)
            {
                $types_html = array();
                
                foreach ($repository_types['types'][$category] as $type => $name)
                {
                    $types_html[] = '<a href="' . $this->get_url(array(ExternalRepositoryInstanceManager :: PARAM_EXTERNAL_REPOSITORY_TYPE => $type)) . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'external_repository/' . $type . '/logo/48.png);">';
                    $types_html[] = $name;
                    $types_html[] = '</div></a>';
                }
                
                $tabs->add_tab(new DynamicContentTab($category, $category_name, Theme :: get_common_image_path() . 'place_external_repository_' . $category . '.png', implode("\n", $types_html)));
            }
            
            echo $tabs->render();
            $this->display_footer();
        }
    }

    function get_external_repository_types()
    {
        $path = Path :: get_common_extensions_path() . 'external_repository_manager/type/';
        $folders = Filesystem :: get_directory_content($path, Filesystem :: LIST_DIRECTORIES, false);
        
        $types = array();
        $sections = array();
        
        foreach ($folders as $folder)
        {
            $properties_file = Path :: get_common_extensions_path() . 'external_repository_manager/type/' . $folder . '/properties.xml';
            if (! file_exists($properties_file))
            {
                continue;
            }
            
            $doc = new DOMDocument();
            $doc->load($properties_file);
            $xml_properties = $doc->getElementsByTagname('property');
            $properties = array();
            
            $section = 'various';
            $multiple = false;
            
            foreach ($xml_properties as $index => $property)
            {
                if ($property->getAttribute('name') == 'section')
                {
                    $section = $property->getAttribute('value');
                }
                elseif($property->getAttribute('name') == 'multiple')
                {
                    $multiple = $property->getAttribute('value');
                }
            }
            
            $condition = new EqualityCondition(ExternalRepository::PROPERTY_TYPE, $folder);
            $count = $this->count_external_repositories($condition);
            if (!$multiple && $count > 0)
            {
                continue;
            }
            
            if (! in_array($section, array_keys($sections)))
            {
                $sections[$section] = Translation :: get('ExternalRepository' . Utilities :: underscores_to_camelcase($section));
            }
            
            if (! isset($types[$section]))
            {
                $types[$section] = array();
            }
            
            $types[$section][$folder] = Translation :: get(Utilities :: underscores_to_camelcase($folder));
            asort($types[$section]);
        }
        asort($sections);
        return array('sections' => $sections, 'types' => $types);
    }
}
?>