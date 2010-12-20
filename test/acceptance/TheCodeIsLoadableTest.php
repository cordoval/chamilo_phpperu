<?php
namespace test\acceptance;

require_once __DIR__ . '/__file/RequirerTestCase.php';

class TheCodeIsLoadableTest extends \PHPUnit_Framework_TestSuite
{

    public static function suite()
    {
        return new self();
    }

    public function __construct()
    {
        parent:: __construct();
        $this->addAdminClasses();
        $this->addApplicationAppzClasses();
        $this->addCommonClasses();
        $this->addGroupCoreAppClasses();
        $this->addHelpCoreAppClasses();
        $this->addHomeCoreAppClasses();
        $this->addInstallCoreAppClasses();
        $this->addMenuCoreAppClasses();
        $this->addMigrationCoreAppClasses();
        $this->addReportingCoreAppClasses();
        $this->addRepositoryCoreAppClasses();
        $this->addRightsCoreAppClasses();
        $this->addTrackingCoreAppClasses();
        $this->addUserCoreAppClasses();
        $this->addWebserviceCoreAppClasses();
    }


    private function addTestForAllClassesInside($path)
    {
        $directory = new \RecursiveDirectoryIterator($path);
        $iterator = new \RecursiveIteratorIterator($directory);
        $regex = new \RegexIterator($iterator, '/^.+\.class\.php$/i', \RecursiveRegexIterator::GET_MATCH);


        foreach ($regex as $matches)
        {
            $class_file = $matches[0];
            $test = new RequirerTestCase($class_file);
            $this->addTest($test);
        }

    }

	public function addAdminClasses() {
        $path =  __DIR__ . '/../../admin/';
        $this->addTestForAllClassesInside($path);
	}

    public function addApplicationAppzClasses() {
        $path =  __DIR__ . '/../../application/';
		$this->addTestForAllClassesInside($path);
	}

    public function addCommonClasses() {
        $path =  __DIR__ . '/../../common/';
		$this->addTestForAllClassesInside($path . 'extensions');
        $this->addTestForAllClassesInside($path . 'libraries/php');
	}

    public function addGroupCoreAppClasses() {
        $path =  __DIR__ . '/../../group/';
		$this->addTestForAllClassesInside($path);
	}


    public function addHelpCoreAppClasses() {
        $path =  __DIR__ . '/../../help/';
		$this->addTestForAllClassesInside($path);
	}


    public function addHomeCoreAppClasses() {
        $path =  __DIR__ . '/../../install/';
		$this->addTestForAllClassesInside($path);
	}


    public function addInstallCoreAppClasses() {
        $path =  __DIR__ . '/../../group/';
		$this->addTestForAllClassesInside($path);
	}


    public function addMenuCoreAppClasses() {
        $path =  __DIR__ . '/../../menu/';
		$this->addTestForAllClassesInside($path);
	}

    public function addMigrationCoreAppClasses() {
        $path =  __DIR__ . '/../../migration/';
		$this->addTestForAllClassesInside($path);
	}

    public function addReportingCoreAppClasses() {
        $path =  __DIR__ . '/../../reporting/';
		$this->addTestForAllClassesInside($path);
	}

    public function addRepositoryCoreAppClasses() {
        $path =  __DIR__ . '/../../repository/';
		$this->addTestForAllClassesInside($path);
	}

    public function addRightsCoreAppClasses() {
        $path =  __DIR__ . '/../../rights/';
		$this->addTestForAllClassesInside($path);
	}

    public function addTrackingCoreAppClasses() {
        $path =  __DIR__ . '/../../tracking/';
		$this->addTestForAllClassesInside($path);
	}

    public function addUserCoreAppClasses() {
        $path =  __DIR__ . '/../../user/';
		$this->addTestForAllClassesInside($path);
	}

    public function addWebserviceCoreAppClasses() {
        $path =  __DIR__ . '/../../webservice/';
		$this->addTestForAllClassesInside($path);
	}

	
}
?>
