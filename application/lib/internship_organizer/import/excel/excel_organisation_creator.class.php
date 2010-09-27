<?php

class ExcelOrganisationCreator
{

    function ExcelOrganisationCreator()
    {
    
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
               
        $current_organisation_id;
        
        //elke regel in het excel bestand aflopen behalve rij 1 = headers !
        for($i = 2; $i < count($excel_array)+1; $i ++)
        {
            
            $organisation_id = $excel_array[$i][0];
            if ($organisation_id != $current_organisation_id)
            {
                
                $organisation_name = $excel_array[$i][1];
                $organisation_description = $excel_array[$i][2];
                
                $organisation = new InternshipOrganizerOrganisation();
                $organisation->set_name($organisation_name);
                $organisation->set_description($organisation_description);
                if (! $organisation->create())
                {
                    continue;
                }
            	$current_organisation_id = $organisation_id;
            }
            
            //check if region exist els default region =1 = rootregion
            

            $zip_code = $excel_array[$i][9];
            $city_name = $excel_array[$i][10];
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, $city_name);
            $conditions[] = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, $zip_code);
            $condition = new AndCondition($conditions);
            $region = InternshipOrganizerDataManager :: get_instance()->retrieve_regions($condition, 1)->next_result();
            if ($region)
            {
                $region_id = $region->get_id();
            }
            else
            {
                $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, $zip_code);
                $region = InternshipOrganizerDataManager :: get_instance()->retrieve_regions($condition, 1)->next_result();
                if ($region)
                {
                    $region_id = $region->get_id();
                }
                else
                {
                    $condition = new EqualityCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, $city_name);
                    $region = InternshipOrganizerDataManager :: get_instance()->retrieve_regions($condition, 1)->next_result();
                }if($region){
                	 $region_id = $region->get_id();
                }else{
                	$region_id = 1;
                }
            }
            
            $name = $excel_array[$i][3];
            $description = $excel_array[$i][4];
            $address = $excel_array[$i][5];
            $telephone = $excel_array[$i][6];
            $fax = $excel_array[$i][7];
            $email = $excel_array[$i][8];
            
            //  $user = Session :: get_user_id();
            $location = new InternshipOrganizerLocation();
            $location->set_name($name);
            $location->set_description($description);
            $location->set_organisation_id($organisation->get_id());
            $location->set_address($address);
            $location->set_email($email);
            $location->set_fax($fax);
            $location->set_telephone($telephone);
            $location->set_region_id($region_id);
            
            $objectarray[] = $location;
        
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
