<?php

require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'import/excel/excel_organisation_creator.class.php';
require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

class ExcelOrganisationImport extends InternshipOrganizerImport
{

    function ExcelOrganisationImport($internship_organizer_file, $user, $object_type)
    {
        parent :: __construct($internship_organizer_file, $user, $object_type);
    }

    public function import_internship_organizer_object()
    {
        
        $file = $this->get_internship_organizer_file();
        $array = explode('.', $file['name']);
        $type = $array[count($array) - 1];
        
        if ($type != 'xlsx')
        {
            return false;
        }
        
        $PhpReader = new PHPExcel_Reader_Excel2007();
        $excel = $PhpReader->load($this->get_internship_organizer_file_property(self :: TEMP_FILE_NAME));
        $worksheet = $excel->getActiveSheet();
        $excel_creator = new ExcelOrganisationCreator();
        
        $temparray = $excel_creator->excel_validate($this->get_user()->get_id(), $worksheet);
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