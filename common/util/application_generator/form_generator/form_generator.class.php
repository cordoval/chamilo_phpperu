<?php

/**
 * Dataclass generator used to generate a form for an object
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
    function generate_form($location, $object_name, $properties, $author)
    {
        $this->template = new Phpbb2Template();
        $this->template->set_rootdir(dirname(__FILE__));
        
        if (! is_dir($location))
            mkdir($location, 0777, true);
        
        $file = fopen($location . Utilities :: camelcase_to_underscores($object_name) . '_form.class.php', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('form' => 'form.template'));
            
            $this->template->assign_vars(array('OBJECT_CLASS' => $object_name, 'L_OBJECT_CLASS' => Utilities :: camelcase_to_underscores($object_name), 'AUTHOR' => $author));
            
            foreach ($properties as $property)
            {
                $property_lower = Utilities :: camelcase_to_underscores($property);
                $property_camelcase = Utilities :: underscores_to_camelcase($property);
                $property_const = 'PROPERTY_' . strtoupper($property);
                
                $this->template->assign_block_vars("PROPERTIES", array('PROPERTY' => $property_const, 'PROPERTY_L' => $property_lower, 'PROPERTY_C' => $property_camelcase));
            }
            
            $string = trim($this->template->pparse_return('form'));
            fwrite($file, $string);
            fclose($file);
        }
    }
}

?>