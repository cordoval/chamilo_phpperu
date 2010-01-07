<?php
/**
 * $Id: qti_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.qti
 */
require_once dirname(__FILE__) . '/assessment/assessment_qti_import.class.php';
require_once dirname(__FILE__) . '/question/question_qti_import.class.php';

class QtiImport extends ContentObjectImport
{

    function import_content_object()
    {
        $file = $this->get_content_object_file();
        $user = $this->get_user();
        
        $zip = Filecompression :: factory();
        $temp = $zip->extract_file($this->get_content_object_file_property('tmp_name'));
        
        $dir = $temp . '/';
        if (file_exists($dir))
        {
            $files = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES_AND_DIRECTORIES, false);
            foreach ($files as $f)
            {
                $type = split('_', $f);
                if ($type[0] == 'qti')
                {
                    $importer = self :: factory_qti($f, $this->get_user(), $this->get_category(), $dir);
                    if ($importer != null)
                    {
                        $returnvalue = $importer->import_content_object();
                    }
                }
            }
        }
        
        if($temp)
        {
        	Filesystem :: remove($temp);
        }
        
        return $returnvalue;
    }

    function factory_qti($lo_file, $user, $category, $dir)
    {
        $type = split('_', $lo_file);
        switch ($type[0])
        {
            case 'qti' :
                return new AssessmentQtiImport($dir . $lo_file, $user, $category);
            case 'question' :
                return new QuestionQtiImport($dir . $lo_file, $user, $category);
            default :
                return null;
        }
    }

    function get_file_content_array($substring)
    {
        $file = parent :: get_content_object_file();
        
        if (file_exists($file) || ! is_null($substring))
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('hotspotChoice'));
            
            // userialize the document
            

            if ($substring)
                $unserializer->unserialize($substring);
            else
                $status = $unserializer->unserialize($file, true);
            
            if (PEAR :: isError($status))
            {
                echo 'Error: ' . $status->getMessage();
            }
            else
            {
                $data = $unserializer->getUnserializedData();
            }
        }
        return $data;
    }
}
?>