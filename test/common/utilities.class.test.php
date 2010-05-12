<?php  //cut_tests.php
class UtilitiesUnitTestCase extends UnitTestCase{

	public function __construct() {
		$this->UnitTestCase('Testing the utilities functions');
	}

	public function test_underscores_to_camelcase_normal() {
		$s = Utilities::underscores_to_camelcase('abc_def');
		$this->assertTrue($s === 'AbcDef');
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

