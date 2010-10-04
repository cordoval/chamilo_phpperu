<?php

/**
 * Dataclass generator used to generate a form for a content object
 * @author Sven Vanpoucke
 */
class FormGenerator
{
    private $template;

    /**
     * Constructor
     */
    function FormGenerator()
    {
    }

    /**
     * Generate a form with the given info
     * @param string $location - The location of the class
     * @param string $object_name - The name of the object
     * @param string $properties - The properties of the object
     * @param string $author - The author
     */
    function generate_form($xml_definition, $author)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(dirname(__FILE__));
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/' . $xml_definition['name'] . '_form.class.php', 'w+');
        
        if ($file)
        {
            $classname = Utilities :: underscores_to_camelcase($xml_definition['name']);
            
            $this->template->set_filenames(array('form' => 'form.template'));
            $this->template->assign_vars(array('OBJECT_CLASS' => $classname, 'TYPE' => $xml_definition['name'], 'AUTHOR' => $author));
            
            foreach ($xml_definition['properties'] as $property => $attributes)
            {
                if ($property !== 'id')
                {
                    $property_lower = Utilities :: camelcase_to_underscores($property);
                    $property_camelcase = Utilities :: underscores_to_camelcase($property);
                    $property_const = 'PROPERTY_' . strtoupper($property);
                    
                    $this->template->assign_block_vars("PROPERTIES", array('PROPERTY' => $property_const, 'PROPERTY_LOWER_CASE' => $property_lower, 'PROPERTY_CAMEL_CASE' => $property_camelcase));
                }
            }
            
            $string = trim($this->template->pparse_return('form'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}

?>