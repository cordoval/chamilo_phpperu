<?php

require_once Path :: get_application_path() . 'internship_organizer/php/import/excel/excel_category_creator.class.php';
require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

class ExcelCategoryImport extends InternshipOrganizerImport
{

    function ExcelCategoryImport($internship_organizer_file, $user, $object_type)
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
        
        $category_id = Request :: get(InternshipOrganizerCategoryManager :: PARAM_CATEGORY_ID);
        
        $PhpReader = new PHPExcel_Reader_Excel2007();
        $excel = $PhpReader->load($this->get_internship_organizer_file_property(self :: TEMP_FILE_NAME));
        $worksheet = $excel->getActiveSheet();
        $excel_creator = new ExcelCategoryCreator($category_id);
        
        $temparray = $excel_creator->excel_validate($worksheet);
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