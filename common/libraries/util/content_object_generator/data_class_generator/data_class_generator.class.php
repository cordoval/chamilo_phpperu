<?php

/**
 * Dataclass generator used to generate dataclasses with given properties
 * @author Sven Vanpoucke
 */
class DataClassGenerator
{
    private $template;

    /**
     * Constructor
     */
    function DataClassGenerator()
    {
    }

    /**
     * Generate a data class file
     * @param array $xml_definition the xml definition of the object
     * @param string $author, the author
     */
    function generate_data_class($xml_definition, $author)
    {
        $this->template = new MyTemplate();
        $this->template->set_rootdir(dirname(__FILE__));
        
        $location = Path :: get_repository_path() . 'lib/content_object/' . $xml_definition['name'];
        
        if (! is_dir($location))
        {
            mkdir($location, 0777, true);
        }
        
        $file = fopen($location . '/' . $xml_definition['name'] . '.class.php', 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('data_class' => 'data_class.template'));
            
            $classname = Utilities :: underscores_to_camelcase($xml_definition['name']);
            $description = 'This class describes a ' . $classname . ' data object';
            
            $this->template->assign_vars(array('TYPE' => $xml_definition['name'], 'DESCRIPTION' => $description, 'AUTHOR' => $author, 'OBJECT_CLASS' => $classname));
            
            $property_names = array();
            
            foreach ($xml_definition['properties'] as $property => $attributes)
            {
                if ($property !== 'id')
                {
                    $property_const = 'PROPERTY_' . strtoupper($property);
                    $property_names[] = 'self :: ' . $property_const;
                    
                    $this->template->assign_block_vars("CONSTS", array('PROPERTY_CONST' => $property_const, 'PROPERTY_NAME' => $property));
                    $this->template->assign_block_vars("PROPERTY", array('PROPERTY_CONST' => $property_const, 'PROPERTY_NAME' => $property));
                }
            }
            
            $this->template->assign_vars(array('ADDITIONAL_PROPERTY_NAMES' => implode(', ', $property_names)));
            
            $string = "<?php\n" . $this->template->pparse_return('data_class') . "?>";
            fwrite($file, $string);
            fclose($file);
        }
    }
}

?>