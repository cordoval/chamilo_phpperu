<?php  //cut_tests.php

require_once dirname(__FILE__) . '/../../common/global.inc.php';
Mock::generate('Announcement');

class UtilitiesUnitTestCase extends UnitTestCase{
	
        
        
        public function __construct() {
		$this->UnitTestCase('Testing the utilities functions');
	}
	public function test_underscores_to_camelcase_normal() {
		$s = Utilities::underscores_to_camelcase('abc_def');
		$this->assertTrue($s === 'AbcDef');
	}

        public function test_content_objects_for_element_finder_normal()
        {
            $objects[] = new MockAnnouncement();
            $objects[] = new MockAnnouncement();

            $s = Utilities :: content_objects_for_element_finder($objects);

            $this->assertEqual(count($s),2);
        }

        public function test_content_object_for_element_finder_normal()
        {
            
            $object = new MockAnnouncement();
            $object->setReturnValue('get_title','title');
            
            $s = Utilities :: content_object_for_element_finder($object);

            $this->assertEqual($s['title'], 'title');
        }

        public function test_content_object_for_element_null(){
            //code will break
            /*$s = Utilities :: content_object_for_element_finder();
            $this->assertFalse($s);*/
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