<?php

namespace test\acceptance;

class RequirerTestCase extends \PHPUnit_Framework_TestCase
{

    private $class_file;

    public function __construct($class_file)
    {
        parent::__construct("test_requireFile");
        $this->class_file = $class_file;
    }

    public function test_requireFile()
    {
        $requirer_file = __DIR__ . '/requirer.php';
        $command_string = "php {$requirer_file} {$this->class_file} 2>&1";
        $output_array = null;
        $output_result;//255 is bad 0 is good
        exec($command_string, $output_array, $output_result);
        $this->assertEmpty($output_array, implode($output_array, "\n"));
    }
}
?>
