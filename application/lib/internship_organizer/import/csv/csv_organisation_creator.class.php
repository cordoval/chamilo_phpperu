<?php

class CsvOrganisationCreator
{

    function CsvOrganisationCreator()
    {
    }

    /*
	 * This function will return an array , if errors occured in the csv file it will return an array
	 * with array[0]='faultyarrayreturn'
	 * An errorarray will consist of the numbers of rules where there were errors
	 * A objectarray will consist of objectsthat need to be created.
	 */
    function csv_validate($owner_id, $csvarray)
    {
        $errorarray = array();
        $objectarray = array();
        $errorarray[0] = '';
        
        $current_organisation_id;
        
        //elke regel in het csvbestand aflopen
        for($i = 0; $i < count($csvarray); $i ++)
        {
            
            $organisation_id = $csvarray[$i][0];
            if ($organisation_id != $current_organisation_id)
            {
                
                $organisation_name = $csvarray[$i][1];
                $organisation_description = $csvarray[$i][2];
                
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
            

            $zip_code = $csvarray[$i][9];
            $city_name = $csvarray[$i][10];
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
                }
                if ($region)
                {
                    $region_id = $region->get_id();
                }
                else
                {
                    $region_id = 1;
                }
            }
            
            $name = $csvarray[$i][3];
            $description = $csvarray[$i][4];
            $address = $csvarray[$i][5];
            $telephone = $csvarray[$i][6];
            $fax = $csvarray[$i][7];
            $email = $csvarray[$i][8];
            
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
            $location->set_owner_id($owner_id);
            
            $succes = $location->create();
            
            $categories = $csvarray[$i][11];
            $cats = explode('_', $categories);
            $category_ids = array();
            foreach ($cats as $cat)
            {
                $condition = new EqualityCondition(InternshipOrganizerCategory :: PROPERTY_NAME, $cat);
                $category = InternshipOrganizerDataManager :: get_instance()->retrieve_categories($condition, 1)->next_result();
                if ($category)
                {
                    $category_ids[] = $category->get_id();
                }
            }
            
            if (count($category_ids))
            {
                foreach ($category_ids as $id)
                {
                    $category_rel_location = new InternshipOrganizerCategoryRelLocation();
                    $category_rel_location->set_category_id($id);
                    $category_rel_location->set_location_id($location->get_id());
                    $succes = $category_rel_location->create();
                }
            }
            
            $objectarray[] = 1;
        
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
