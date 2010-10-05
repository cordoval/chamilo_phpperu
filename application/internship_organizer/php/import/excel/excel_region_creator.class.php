<?php

class ExcelRegionCreator
{
	
	private $parent_region_id;
	
	function ExcelRegionCreator($parent_region_id){
		$this->parent_region_id = $parent_region_id;
	}
	
	
    /*
	 * This function will return an array , if errors occured in the csv file it will return an array
	 * with array[0]='faultyarrayreturn'
	 * An errorarray will consist of the numbers of rules where there were errors
	 * A objectarray will consist of objectsthat need to be created.
	 */
    function excel_validate($worksheet)
    {
        $errorarray = array();
        $objectarray = array();
        $errorarray[0] = '';
		
        $excel_array = $worksheet->toArray();
                
        //elke regel in het excel bestand aflopen behalve rij 1 = headers !
        for($i = 2; $i < count($excel_array)+1; $i ++)
        {
           
                $name = $excel_array[$i][0];
                $zip_code = $excel_array[$i][1];
                $description = $excel_array[$i][2];
				
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
