<?php
/**
 * $Id: csv_creator.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.csv
 */
class CsvCreator
{

    function quota_check($csvarray, $user)
    {
        $amount_to_add = count($csvarray);
        $quotamanagercsv = new QuotaManager($user);
        $numberofused = $quotamanagercsv->get_used_database_space();
        $maximum = $quotamanagercsv->get_max_database_space();
        if ($amount_to_add + $numberofused <= $maximum)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function parent_split($parent)
    {
        $aparent = explode('#', $parent);
        $bparent = explode(' ', $aparent[1]);
        return $bparent[0];
    }

    /*
	 * This function will return an array , if errors occured in the csv file it will return an array
	 * with array[0]='faultyarrayreturn'
	 * An errorarray will consist of the numbers of rules where there were errors
	 * A objectarray will consist of objectsforms that need to be created.
	 */
    function csv_validate($typearray, $csvarray)
    {
        $errorarray = array();
        $objectarray = array();
        $errorarray[0] = '';
        
        //elke regel in het csvbestand aflopen
        for($i = 0; $i < count($csvarray); $i ++)
        {
            //Kijken of het object in csv wel bestaat.
            if (in_array($csvarray[$i][0], $typearray))
            {
                $dataManager = RepositoryDataManager :: get_instance();
                $type = $csvarray[$i][0];
                //retrieve the root category (this is for now , can be modded later on so users can include
                // the category where they want everything to be added
                $user = Session :: get_user_id();
                //$categorystring= $dataManager->retrieve_root_category($user);
                $category = $this->parent_split($categorystring);
                //create the abstract learning object			
                $object = new AbstractContentObject($type, $user, $category);
                $message = '';
                //Create a form for the Learning object
                $lo_form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE, $object, 'create');
                $valuearray = array();
                //Fill up the valuearray with values , retrieved from the csv , id is automatically put on 					1 (root)
                for($j = 1; $j < count($csvarray[$i]); $j ++)
                {
                    if ($j == 2)
                    { //second value needs to be the category (predefined as root category here)
                        array_push($valuearray, $category);
                    }
                    array_push($valuearray, $csvarray[$i][$j]);
                }
                
                $lo_form->set_csv_values($valuearray);
                
                if ($lo_form->validate_csv($valuearray))
                {
                    array_push($objectarray, $lo_form);
                }
                else
                {
                    $errorarray[0] = 'faultyarrayreturn';
                    array_push($errorarray, ($i + 1));
                }
            
            }
            //Type not found in our list
            else
            {
                $errorarray[0] = 'faultyarrayreturn';
                array_push($errorarray, ($i + 1));
            }
        
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
