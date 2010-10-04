<?php
/**
 * $Id: csv_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.csv
 */

/**
 * Exports learning object to the chamilo learning object format (xml)
 */
class CsvImport extends ContentObjectImport
{
    private $rdm;

    function CsvImport()
    {
        $this->rdm = RepositoryDataManager :: get_instance();
    }

    public function import_content_object()
    {
        $category = Request :: get(RepositoryManager :: PARAM_CATEGORY_ID);
        $csvarray = Import :: read_csv($this->get_content_object_file_property('tmp_name'));
        $csvcreator = new CSVCreator();
        
        $true = $csvcreator->quota_check($csvarray, $this->get_user());
        if ($true)
        {
            $typearray = $this->rdm->get_registered_types(true);
            $temparray = $csvcreator->csv_validate($typearray, $csvarray);
            if (! ($temparray[0] == 'faultyarrayreturn'))
            {
                for($i = 0; $i < count($temparray); $i ++)
                {
                    $temparray[$i]->create_content_object();
                }
                
                return $temparray[$i];
            }
            else
            {
                $errormessage = 'The folowing rows have been reported as wrong: ';
                for($i = 1; $i < count($temparray); $i ++)
                {
                    $errormessage = $errormessage . ' ' . $temparray[$i];
                }
                
                return false;
            }
        }
        //To much to be imported
        else
        {
            return false;
        }
    }

}
?>