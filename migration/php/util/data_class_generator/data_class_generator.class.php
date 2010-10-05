<?php
/**
 * Dataclass generator used to generate dataclasses with given properties
 * $Id: data_class_generator.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.settings
 * @author Sven Vanpoucke
 */
class DataClassGenerator
{
    private $template;
    private $database;
    private $classname;
    private $properties;
    private $package;
    private $description;
    private $author;

    /**
     * Constructor
     * @param string $database the database
     * @param string $classname the classname
     * @param array of strings $properties the properties
     * @param string $package the package
     * @param string $description the description
     * @param string $author, the author
     */
    function DataClassGenerator($database, $classname, $properties, $package, $description, $author)
    {
        $this->template = new MyTemplate();
        $this->classname = $classname;
        $this->properties = $properties;
        $this->package = $package;
        $this->description = $description;
        $this->author = $author;
        $this->database = $database;
        $this->generate_data_class();
        echo ('dataclass ' . $this->classname . ' generated<br />');
    }

    /**
     * Generate a dataclass with the given info
     */
    function generate_data_class()
    {
        $this->classname = self :: to_camel_case($this->classname);
        
        $filename = dirname(__FILE__) . '/classes/' . $this->database . '/' . strtolower($this->classname) . '.class.php';
        $destination_dir = dirname($filename);
        
        if (! is_dir($destination_dir))
            mkdir($destination_dir, 0777, true);
        
        $file = fopen($filename, 'w+');
        
        if ($file)
        {
            $this->template->set_filenames(array('dataclass' => 'dataclass.template'));
            
            $property_names = array();
            
            $this->template->assign_vars(array('PACKAGE' => $this->package, 'DESCRIPTION' => $this->description, 'AUTHOR' => $this->author, 'CLASSNAME' => $this->classname));
            
            foreach ($this->properties as $property)
            {
                $property_const = 'PROPERTY_' . strtoupper($property);
                $property_names[] = 'self :: ' . $property_const;
                
                $this->template->assign_block_vars("CONSTS", array('PROPERTY_CONST' => $property_const, 'PROPERTY_NAME' => $property));
                
                $this->template->assign_block_vars("PROPERTY", array('PROPERTY_CONST' => $property_const, 'PROPERTY_NAME' => $property));
            }
            
            $this->template->assign_vars(array('DEFAULT_PROPERTY_NAMES' => implode(', ', $property_names)));
            
            $string = "<?php \n" . $this->template->pparse_return('dataclass') . "\n?>";
            fwrite($file, $string);
            fclose($file);
        }
    }

    /**
     * Removes underscores and replace it by camelcase
     * @param string $text old text
     * @param string $newtext converted text
     */
    static function to_camel_case($text)
    {
        $textsplit = split('_', $text);
        $newtext = '';
        
        foreach ($textsplit as $temp)
        {
            $newtext = $newtext . strtoupper(substr($temp, 0, 1)) . substr($temp, 1);
        }
        
        return $newtext;
    }
}

?>