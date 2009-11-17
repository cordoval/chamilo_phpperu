<?php
/**
 * $Id: learning_path_scorm_export.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.export.scorm.learning_path
 */
class LearningPathScormExport extends ScormExport
{

    function LearningPathScormExport($learning_path)
    {
        parent :: __construct($learning_path);
    }

    function export_content_object()
    {
        $learning_path = $this->get_content_object();
        
        $manifest_xml = $this->create_manifest($learning_path);
        echo $manifest_xml;
    }

    function create_manifest($learning_path)
    {
        $xml[] = '<manifest identifier="' . $learning_path->get_title() . '"';
        $xml[] = ' version="1.3" xml:base="LearningPathManifest"';
        $xml[] = ' xmlns="http://www.imsglobal.org/xsd/imscp_v1p1"';
        $xml[] = ' xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_v1p3"';
        $xml[] = ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
        $xml[] = ' xsi:schemaLocation="http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd http://www.adlnet.org/xsd/adlcp_v1p3 adlcp_v1p3.xsd"';
        $xml[] = ' >';
        
        $xml[] = '<!-- xml manifest contents -->';
        
        $xml[] = '<metadata>';
        $xml[] = '<schema>ADL SCORM</schema>';
        $xml[] = '<schemaversion>2004 3rd Edition</schemaversion>';
        //$xml[] = '<adlcp:location>packageMetadata.xml</adlcp:location>';
        $xml[] = '</metadata>';
        
        $xml[] = $this->export_learning_path($learning_path);
        
        $xml[] = '</manifest>';
        
        return implode('', $xml);
    }

    function export_learning_path($learning_path)
    {
        $chapters_cond = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $learning_path->get_id(), ComplexContentObjectItem :: get_table_name());
        $chapters_clos = $this->get_rdm()->retrieve_complex_content_object_items($chapters_cond);
        while ($chapter_clo = $chapters_clos->next_result())
        {
            $chapter = $this->get_rdm()->retrieve_content_object($chapter_clo->get_ref());
            $chapters[] = $chapter;
        }
        
        $lp_xml[] = '<organizations default="chamilo_scorm_export">';
        $lp_xml[] = '<organization identifier="chamilo_scorm_export">';
        
        $lp_xml[] = '<title>' . $learning_path->get_title() . '</title>';
        
        foreach ($chapters as $chapter)
        {
            $lp_xml[] = $this->export_chapter_item($chapter);
        }
        $lp_xml[] = '</organization>';
        $lp_xml[] = '</organizations>';
        
        return implode('', $lp_xml);
    }

    function export_chapter_item($chapter)
    {
        $org_xml[] = '<item identifier="ITEM_' . $chapter->get_id() . '" identifierref="RESOURCE_' . $chapter->get_id() . '" isvisible="true">';
        $org_xml[] = '<title>' . $chapter->get_title() . '</title>';
        
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $chapter->get_id(), ComplexContentObjectItem :: get_table_name());
        $items = $this->get_rdm()->retrieve_complex_content_object_items($condition);
        while ($item_clo = $items->next_result())
        {
            $item = $this->get_rdm()->retrieve_content_object($item_clo->get_ref());
            $org_xml[] = $this->export_step_item($item);
        }
        $org_xml[] = '</item>';
        return implode('', $org_xml);
    }

    function export_step_item($item)
    {
        $step_xml[] = '<item identifier="ITEM_' . $item->get_id() . '" identifierref="RESOURCE_' . $item->get_id() . '" isvisible="true">';
        $step_xml[] = '<title>' . $item->get_title() . '</title>';
        $step_xml[] = '<adlcp:completionTreshold>0.0</adlcp:completionTreshold>';
        $step_xml[] = '</item>';
        
        return implode('', $step_xml);
    }
}
?>