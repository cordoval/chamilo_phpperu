<?php
namespace test\acceptance;

class TheCodeIsLoadableTest extends \PHPUnit_Framework_TestCase
{

    private function checkAllClassesInside($path)
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\.class\.php$/i', \RecursiveRegexIterator::GET_MATCH);


        $requirer_file = __DIR__ . '/__file/requirer.php';
        foreach ($regex as $matches)
        {
            $class_file = $matches[0];
            $command_string = "php {$requirer_file} {$class_file} 2>&1";
            $output_array = null;
            $output_result;//255 is bad 0 is good
            exec($command_string, $output_array, $output_result);
            $this->assertEmpty($output_array, implode($output_array, "\n"));
        }

    }

	public function test_loadAdminCoreApp() {
        $path =  __DIR__ . '/../../admin/';
        $this->checkAllClassesInside($path);
	}

    public function test_loadApplicationAppz() {
        $path =  __DIR__ . '/../../application/';
		$this->checkAllClassesInside($path);
	}

    public function test_loadCommon() {
        $path =  __DIR__ . '/../../common/';
		$this->checkAllClassesInside($path);
	}

    public function test_loadGroupCoreApp() {
        $path =  __DIR__ . '/../../group/';
		$this->checkAllClassesInside($path);
	}


    public function test_loadHelpCoreApp() {
        $path =  __DIR__ . '/../../help/';
		$this->checkAllClassesInside($path);
	}


    public function test_loadHomeCoreApp() {
        $path =  __DIR__ . '/../../install/';
		$this->checkAllClassesInside($path);
	}


    public function test_loadInstallCoreApp() {
        $path =  __DIR__ . '/../../group/';
		$this->checkAllClassesInside($path);
	}


    public function test_loadMenuCoreApp() {
        $path =  __DIR__ . '/../../menu/';
		$this->checkAllClassesInside($path);
	}

    public function test_loadMigrationCoreApp() {
        $path =  __DIR__ . '/../../migration/';
		$this->checkAllClassesInside($path);
	}

    public function test_loadReportingCoreApp() {
        $path =  __DIR__ . '/../../reporting/';
		$this->checkAllClassesInside($path);
	}

    public function test_loadRepositoryCoreApp() {
        $path =  __DIR__ . '/../../repository/';
		$this->checkAllClassesInside($path);
	}

    public function test_loadRightsCoreApp() {
        $path =  __DIR__ . '/../../rights/';
		$this->checkAllClassesInside($path);
	}

    public function test_loadTrackingCoreApp() {
        $path =  __DIR__ . '/../../tracking/';
		$this->checkAllClassesInside($path);
	}

    public function test_loadUserCoreApp() {
        $path =  __DIR__ . '/../../user/';
		$this->checkAllClassesInside($path);
	}

    public function test_loadWebserviceCoreApp() {
        $path =  __DIR__ . '/../../webservice/';
		$this->checkAllClassesInside($path);
	}

	
}
?>
