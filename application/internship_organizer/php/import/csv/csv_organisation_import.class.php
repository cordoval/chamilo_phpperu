<?php

require_once Path :: get_application_path() . 'internship_organizer/php/import/csv/csv_organisation_creator.class.php';

class CsvOrganisationImport extends InternshipOrganizerImport
{

    function CsvOrganisationImport($internship_organizer_file, $user, $object_type)
    {
        parent :: __construct($internship_organizer_file, $user, $object_type);
    }

    public function import_internship_organizer_object()
    {
        
        $file = $this->get_internship_organizer_file();
        $array = explode('.', $file['name']);
        $type = $array[count($array) - 1];
        
        if ($type != 'csv')
        {
            return false;
        }
        
        $csvarray = Import :: read_csv($this->get_internship_organizer_file_property(self :: TEMP_FILE_NAME));
        
        $csvcreator = new CsvOrganisationCreator();
        
        $temparray = $csvcreator->csv_validate($this->get_user()->get_id(), $csvarray);
        if (! ($temparray[0] == 'faultyarrayreturn'))
        {
//            for($i = 0; $i < count($temparray); $i ++)
//            {
//                $temparray[$i]->create();
//            }
            
            return true;
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

}
?>