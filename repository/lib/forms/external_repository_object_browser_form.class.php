<?php
class ExternalRepositoryObjectBrowserForm extends FormValidator
{
    private $objects_list;
    
    
    public function ExternalRepositoryObjectBrowserForm($objects_list)
    {
        parent :: __construct('external_repository_objects_browser');
        
        $this->objects_list = $objects_list;
    }
    
    public function display()
    {
        echo $this->build_objects_list($this->objects_list);
        
        
    }
    
    private function build_objects_list($objects_list)
    {
        //DebugUtilities :: show($objects_list);
        
        $table = '<table border="0" cellpadding="5" cellspacing="0">';
        $table .= '<tr>';
        $table .= '<th>' . Translation::translate('pid') . '</th>';
        $table .= '<th>' . Translation::translate('title') . '</th>';
        $table .= '<th>' . Translation::translate('last update') . '</th>';
        $table .= '<th></th>';
        $table .= '</tr>';
        
        
        foreach($objects_list as $key => $object)
        {
            $table .= '<tr>';
            
            $table .= '<td>';
            
            if(isset($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID]))
            {
                $table .= $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID];
            }
            
            $table .= '</td>';
            
            $table .= '<td>';
            
            if(isset($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_TITLE]))
            {
                $table .= $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_TITLE];
            }
            
            $table .= '</td>';
            
//            $table .= '<td>';
//            
//            if(isset($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_CREATION_DATE]))
//            {
//                $table .= $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_CREATION_DATE];
//            }
//            
//            $table .= '</td>';
            
            $table .= '<td>';
            
            if(isset($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_MODIFICATION_DATE]))
            {
                $utc_datetime = $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_MODIFICATION_DATE];
                
                $table .= 'Fedora time to store: ' . $utc_datetime;
                $table .= '<br/>';
                
                $table .= 'Time to show to the user: ' . date('Y-m-d H:i:s', strtotime($utc_datetime));
                $table .= '<br/>';
                
                $utc_datetime = substr($utc_datetime, 0, strlen($utc_datetime) - 1);
                $table .= 'UTC time to store: ' . date('Y-m-d H:i:s', strtotime($utc_datetime));
                
                
            }
            
            $table .= '</td>';
            
            $table .= '<td>';
            
            if(isset($object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]))
            {
                $content_object_id = $object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]->get_id();
                $table .= $content_object_id;
            }
            
            $table .= '</td>';
            
            $table .= '</tr>';
        }
        
        $table .= '</table>';
                
        return $table;
    }
    
   
    
}
?>