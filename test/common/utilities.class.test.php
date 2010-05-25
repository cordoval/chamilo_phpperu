<?php  //cut_tests.php
class UtilitiesUnitTestCase extends UnitTestCase
{
	public function __construct() 
	{
		$this->UnitTestCase('Testing the utilities functions');
	}
	public function test_underscores_to_camelcase_normal() 
	{
		$s = Utilities :: underscores_to_camelcase('abc_def');
		$this->assertTrue($s === 'AbcDef');
	}

	public function test_underscores_to_camelcase_strcmp() 
	{
		$s = Utilities::underscores_to_camelcase('abc_def');
		$this->assertTrue(strcmp($s,'AbcDef')===0);
	}
	
	public function test_format_seconds_to_hours_normal()
	{
		$s = Utilities::format_seconds_to_hours('3600');
		$this->assertTrue($s === '1:00:00');
	}
	
	public function test_format_seconds_to_minutes_normal()
	{
		$s = Utilities::format_seconds_to_minutes('60');
		$this->assertTrue($s === '01:00');
	}
	
	//null unit test cases
	public function test_format_seconds_to_hours_null()
	{
		//$variable = null;
		$s = Utilities::format_seconds_to_hours($variable);
		$this->assertNull($s);
	}
	
	public function test_format_seconds_to_minutes_null()
	{
		//$variable = null;
		$s = Utilities::format_seconds_to_minutes($variable);
		$this->assertNull($s);
		
	}
	
	//isnotnull unit test cases
	public function test_format_seconds_to_hours_not_null()
	{
		$s = Utilities::format_seconds_to_hours('3600');
		$this->assertNotNull($s === '1:00:00');
	}
	
	public function test_format_seconds_to_minutes_not_null()
	{
		$s = Utilities::format_seconds_to_minutes('60');
		$this->assertNotNull($s === '01:00');
	}
	
	public function test_camelcase_to_underscores_normal()
	{
		$s = Utilities :: camelcase_to_underscores('AbcDef');
		$this->assertTrue($s === 'abc_def');
	}
	
	public function test_camelcase_to_underscores_null()
	{
		$s = Utilities :: camelcase_to_underscores(null);
		$this->assertNull($s);
	}
	
	public function test_camelcase_to_underscores_empty()
	{
		$s = Utilities :: camelcase_to_underscores('');
		$this->assertTrue($s === '');
	}
	
	public function test_underscores_to_camelcase_with_spaces_normal()
	{
		$s = Utilities :: underscores_to_camelcase_with_spaces('abc_def');
		$this->assertTrue($s === 'Abc Def');
	}
	
	public function test_underscores_to_camelcase_with_spaces_null()
	{
		$s = Utilities :: underscores_to_camelcase_with_spaces(null);
		$this->assertNull($s);
	}
	
	public function test_underscores_to_camelcase_with_spaces_empty()
	{
		$s = Utilities :: underscores_to_camelcase_with_spaces('');
		$this->assertTrue($s === '');
	}
	
	public function test_build_toolbar_normal()
	{
		$toolbar_data = array();
		$toolbar_data[] = array('href' => 'core.php',
							    'label' => Translation :: get('Browse'), 
							    'img' => Theme :: get_common_image_path() . 'action_browser.png',
								'confirm' => true);
		
		$toolbar = Utilities :: build_toolbar($toolbar_data);
		
		$pattern = '/core\.php.*Browse.*confirm.*action_browser\.png/';
		$this->assertPattern($pattern, $toolbar);
	}
	
	public function test_build_toolbar_null()
	{
		$toolbar_data = null;
		
		$toolbar = Utilities :: build_toolbar($toolbar_data);
		
		$this->assertNull($toolbar);
	}
	
	public function test_build_toolbar_empty()
	{
		$toolbar_data = array();
		
		$toolbar = Utilities :: build_toolbar($toolbar_data);
		
		$this->assertNull($toolbar);
	}
	
	public function test_build_toolbar_normal_with_classes_and_css()
	{
		$toolbar_data = array();
		$toolbar_data[] = array('href' => 'core.php',
							    'label' => Translation :: get('Browse'), 
							    'img' => Theme :: get_common_image_path() . 'action_browser.png',
								'confirm' => true);
		
		$toolbar = Utilities :: build_toolbar($toolbar_data, 'test_class', 'width: 100%;');
		
		$pattern = '/test_class.*width: 100%;.*core\.php.*Browse.*confirm.*action_browser\.png/';
		$this->assertPattern($pattern, $toolbar);
	}
	
	public function test_by_title_normal()
	{
		$content_object1 = new ContentObject();
		$content_object2 = new ContentObject();
		$content_object1->set_title('title');
		$content_object2->set_title('title');
		
		$return = Utilities :: by_title($content_object1, $content_object2);
		
		$this->assertTrue($return == 0);
	}
	
	public function test_by_title_null()
	{
		$content_object1 = new ContentObject();
		$content_object2 = new ContentObject();
		
		$return = Utilities :: by_title($content_object1, $content_object2);
		
		$this->assertTrue($return == 0);
	}
	
	public function test_by_title_empty()
	{
		$content_object1 = new ContentObject();
		$content_object2 = new ContentObject();
		$content_object1->set_title('');
		$content_object2->set_title('');
		
		$return = Utilities :: by_title($content_object1, $content_object2);
		
		$this->assertTrue($return == 0);
	}

        public function test_time_from_datepicker_normal()
        {
            $string = '2010-5-9 5:10:10';
            $ft =  Utilities::time_from_datepicker($string);
            $phpt = mktime(5, 10, 10, 5, 9, 2010);
            $this->assertTrue($ft == $phpt);
        }

        public function test_time_from_datepicker_randomtext()
        {
            $string = 'strange test input that is not a date at all';
            $ft =  Utilities::time_from_datepicker($string);
            $pht = false;
            $this->assertTrue($ft == $phpt);
        }

        public function test_time_from_datepicker_null()
        {
            $string =  null;
            $ft =  Utilities::time_from_datepicker($string);
            $pht = false;
            $this->assertTrue($ft == $phpt);
        }

         public function test_time_from_datepicker_exception()
        {
            $string =  '2010-5-9 5:10:10';
            $message = null;
            try {
                 Utilities::time_from_datepicker($string);
            } catch (Exception $x) {
               $message = $x->getMessage();
            }


            $this->assertNull($message);
        }
        
         public function test_time_from_datepicker_strange_year()
        {
            $string = '20101-5-9 5:10:10';
            $ft =  Utilities::time_from_datepicker($string);
            $pht = false;
            $this->assertTrue($ft == $phpt);
        }

         public function test_time_from_datepicker_strange_month()
        {
            $string = '2010-75-9 5:10:10';
            $ft =  Utilities::time_from_datepicker($string);
            $pht = false;
            $this->assertTrue($ft == $phpt);
        }

         public function test_time_from_datepicker_strange_day()
        {
            $string = '2010-5-99 5:10:10';
            $ft =  Utilities::time_from_datepicker($string);
            $pht = false;
            $this->assertTrue($ft == $phpt);
        }

        public function test_time_from_datepicker_strange_hours()
        {
            $string = '2010-5-99 25:10:10';
            $ft =  Utilities::time_from_datepicker($string);
            $pht = false;
            $this->assertTrue($ft == $phpt);
        }

        public function test_time_from_datepicker_strange_minutes()
        {
            $string = '2010-5-99 5:61:10';
            $ft =  Utilities::time_from_datepicker($string);
            $pht = false;
            $this->assertTrue($ft == $phpt);
        }

        public function test_time_from_datepicker_strange_seconds()
        {
            $string = '2010-5-99 5:10:61';
            $ft =  Utilities::time_from_datepicker($string);
            $pht = false;
            $this->assertTrue($ft == $phpt);
        }

        public function test_time_from_datepicker_without_timepicker_normal()
        {
            $string = '2010-2-2';
            $ft =Utilities::time_from_datepicker_without_timepicker($string);
            $phpt = mktime(0, 0, 0, 2, 2, 2010);
            $this->assertTrue($ft == $phpt);
        }

        public function test_time_from_datepicker_without_timepicker_null()
        {
            $string = null;
            $ft =Utilities::time_from_datepicker_without_timepicker($string);
            $phpt = 'false';
            $this->assertTrue($ft == $phpt);
        }







}
?>
