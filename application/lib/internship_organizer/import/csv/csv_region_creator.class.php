<?php

class CsvRegionCreator
{
	
	private $parent_region_id;
	
	function CsvRegionCreator($parent_region_id){
		$this->parent_region_id = $parent_region_id;
	}
	
	
    /*
	 * This function will return an array , if errors occured in the csv file it will return an array
	 * with array[0]='faultyarrayreturn'
	 * An errorarray will consist of the numbers of rules where there were errors
	 * A objectarray will consist of objectsthat need to be created.
	 */
    function csv_validate($csvarray)
    {
        $errorarray = array();
        $objectarray = array();
        $errorarray[0] = '';

        //elke regel in het csvbestand aflopen
        for($i = 0; $i < count($csvarray); $i ++)
        {
           
                $name = $csvarray[$i][0];
                $zip_code = $csvarray[$i][1];
                $description = $csvarray[$i][2];
				
               //  $user = Session :: get_user_id();
               	$region = new InternshipOrganizerRegion();
               	$region->set_city_name($name);
               	$region->set_description($description);
               	$region->set_zip_code($zip_code);
               	$region->set_parent_id($this->parent_region_id);
 				$objectarray[] = $region;

        }
        //return the errorarray if its filled
        if ($errorarray[0] == 'faultyarrayreturn')
        {
            return $errorarray;
        }
        else
        {
            return $objectarray;
        }

    }

}
