<?php

class ExcelCategoryCreator
{
    
    private $parent_category_id;

    function ExcelCategoryCreator($parent_category_id)
    {
        $this->parent_category_id = $parent_category_id;
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
            $description = $excel_array[$i][1];
            
            //  $user = Session :: get_user_id();
            $category = new InternshipOrganizerCategory();
            $category->set_name($name);
            $category->set_description($description);
            $category->set_parent_id($this->parent_category_id);
            $objectarray[] = $category;
        
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
