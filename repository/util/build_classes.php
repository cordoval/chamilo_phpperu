<?php

/**
==============================================================================
 * This script creates classes
 *
 * This script checks the learning object library (found in
 * /repository/lib/content_object) for properties files that have not been
 * translated into classes, and automatically generates those classes, with
 * generic accessor methods for the defined properties. It may be convenient to
 * you if you are creating a new type, as all you need to do is create a
 * properties file and run this script; you can then work from the newly
 * generated class file.
 *
 * @author Tim De Pauw
 * @author Tom Brutin
 * @package repository.util
==============================================================================
 */

define(HEADER, "<?php\nrequire_once dirname(__FILE__) . '/../../content_object.class.php';\n\n");
define(FOOTER, "}\n?" . ">");

$path = dirname(__FILE__) . '/../lib/content_object';
if ($handle = opendir($path))
{
    while (false !== ($file = readdir($handle)))
    {
        $p = $path . '/' . $file;
        if (strpos($file, '.') === false && is_dir($p))
        {
            $classFile = $p . '/' . $file . '.class.php';
            $propertyFile = $p . '/' . $file . '.properties';
            if (is_file($propertyFile) && ! is_file($classFile))
            {
                $properties = array_map('rtrim', file($propertyFile));
                if ($fh = fopen($p . '/' . $file . '.class.php', 'w'))
                {
                    fwrite($fh, HEADER);
                    $cls = ContentObject :: type_to_class($file);
                    fwrite($fh, 'class ' . $cls . ' extends ContentObject' . "\n" . '{' . "\n");
                    foreach ($properties as $prop)
                    {
                        fwrite($fh, "\tconst " . get_property_constant_name($prop) . " = '$prop';\n");
                    }
                    foreach ($properties as $prop)
                    {
                        $propconst = 'self :: ' . get_property_constant_name($prop);
                        fwrite($fh, "\tfunction get_$prop ()\n\t{\n" . "\t\treturn \$this->get_additional_property($propconst);\n" . "\t}\n" . "\tfunction set_$prop (\$$prop) \n\t{\n" . "\t\treturn \$this->set_additional_property($propconst, \$$prop);\n" . "\t}\n");
                    }
                    fwrite($fh, FOOTER);
                    fclose($fh);
                    echo 'Created "' . $file . '" class' . "\n";
                }
            }
        }
    }
    closedir($handle);
}

function get_property_constant_name($property)
{
    return 'PROPERTY_' . strtoupper($property);
}
?>