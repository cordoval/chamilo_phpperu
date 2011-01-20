<?php

namespace test\acceptance;

class RequirerTestCase extends \PHPUnit_Framework_TestCase
{

    private $class_file;

    public function __construct($class_file)
    {
        parent::__construct("test_require_file_should_find_its_dependencies_and_not_clash_name");
        $this->class_file = $class_file;
    }

    public function test_require_file_should_find_its_dependencies_and_not_clash_name()
    {
        $requirer_file = __DIR__ . '/requirer.php';
        $command_string = "php {$requirer_file} {$this->class_file} 2>&1";
        $output_array = null;
        $output_result;//255 is bad 0 is good
        exec($command_string, $output_array, $output_result);
        $this->assertEmpty($output_array, "Problem with file {$this->class_file} : \n" .implode($output_array, "\n"));
    
    }
    
}
