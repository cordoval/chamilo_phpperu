<?php
namespace application\internship_organizer;

use common\libraries\WebApplication;
use common\libraries\Request;

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'import/csv/csv_region_creator.class.php';

class CsvRegionImport extends InternshipOrganizerImport
{

    function __construct($internship_organizer_file, $user, $object_type)
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
        
        $region_id = Request :: get(InternshipOrganizerRegionManager :: PARAM_REGION_ID);
        
        $csvarray = Import :: read_csv($this->get_internship_organizer_file_property(self :: TEMP_FILE_NAME));
        
        $csvcreator = new CsvRegionCreator($region_id);
        
        $temparray = $csvcreator->csv_validate($csvarray);
        if (! ($temparray[0] == 'faultyarrayreturn'))
        {
            for($i = 0; $i < count($temparray); $i ++)
            {
                $temparray[$i]->create();
            }
            
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