<?php
class ExternalRepositoryObjectBrowserForm extends FormValidator
{
    private $objects_list;
    private $export;
    
    public function ExternalRepositoryObjectBrowserForm($objects_list, $export)
    {
        parent :: __construct('external_repository_objects_browser');
        
        $this->objects_list = $objects_list;
        $this->export       = $export;
    }
    
    public function display()
    {
        echo $this->build_objects_list($this->objects_list);
        
        
    }
    
    private function build_objects_list($objects_list)
    {
        //DebugUtilities :: show($objects_list);
        
        $table = '<table class="data_table" border="0" cellpadding="5" cellspacing="0">';
        $table .= '<tr>';
        $table .= '<th>' . Translation :: get('ExternalRepositoryObjectId') . '</th>';
        $table .= '<th>' . Translation :: get('ExternalRepositoryObjectTitle') . '</th>';
        $table .= '<th>' . Translation :: get('ExternalRepositoryLastUpdate') . '</th>';
        $table .= '<th>' . Translation :: get('ExternalChamiloLastUpdate') . '</th>';
        $table .= '<th>' . Translation :: get('ExternalRepositoryLastSynchronization') . '</th>';
        $table .= '<th>' . Translation :: get('ExternalRepositorySyncStatus') . '</th>';
        $table .= '<th></th>';
        $table .= '</tr>';
        
        $binary_index = 0;
        foreach($objects_list as $key => $object)
        {
            $class_attribute = $binary_index == 0 ? '' : 'class="row_odd"';
            
            $object_state = null;
            if(isset($object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]))
            {
                $object_state = $object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]->get_state();
            }
            
            $classname = '';
            
            if($binary_index != 0)
            {
                $classname = 'row_odd';
            }
            
            if(isset($object_state) && $object_state == ContentObject :: STATE_RECYCLED)
            {
                $classname .= strlen($classname) > 0 ? ' recycled' : 'recycled';
            }
            
            if(StringUtilities :: has_value($classname))
            {
                $table .= '<tr class="' . $classname . '">';
            }
            else
            {
                $table .= '<tr>';
            }
            
            
            $binary_index    = $binary_index == 0 ? 1 : 0;
            
            /*** column pid *********************************************************************************************/
            
            $table .= '<td style="vertical-align:top">';
            
            if(isset($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID]))
            {
                $url_view_content_object = null;
                if(isset($object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]))
                {
                    $url_view_content_object = Redirect :: get_url(array('application' => RepositoryManager :: APPLICATION_NAME, 'go' => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]->get_id()));
                    
                    $table .= '<a href="' . $url_view_content_object . '">' . $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID] . '</a>';
                }
                else
                {
                    $table .= $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID];
                }
            }
            
            $table .= '</td>';
            
            /*** column title *********************************************************************************************/
            
            $table .= '<td style="vertical-align:top">';
            
            if(isset($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_TITLE]))
            {
                $table .= $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_TITLE];
            }
            
            $table .= '</td>';
            
            /*** column repository modif date  *********************************************************************************************/
            
            $table .= '<td style="vertical-align:top">';
            
            if(isset($object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_MODIFICATION_DATE]))
            {
                $repository_modif_datetime = $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_MODIFICATION_DATE];
                $table .= $repository_modif_datetime;
            }
            
            $table .= '</td>';
            
            /*** column chamilo modif date *********************************************************************************************/
            
            $table .= '<td style="vertical-align:top">';
            
            if(isset($object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]))
            {
                $chamilo_object_date = $object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]->get_modification_date();
                if(!isset($chamilo_object_date))
                {
                    $chamilo_object_date = $object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]->get_creation_date();
                }
                
                $table .= date('Y-m-d H:i:s', $chamilo_object_date);
            }
            
            $table .= '</td>';
            
            /*** column synchronization date *********************************************************************************************/
            
            $table .= '<td style="vertical-align:top">';
            
            if(isset($object[BaseExternalExporter :: SYNC_INFO]))
            {
                $utc_synchronized = $object[BaseExternalExporter :: SYNC_INFO]->get_utc_synchronized();
                $table .= date('Y-m-d H:i:s', strtotime($utc_synchronized . 'z'));
            }
            
            $table .= '</td>';
            
            /*** column sync status *********************************************************************************************/
            
            $table .= '<td style="vertical-align:top">';
            
            $buttons = array();
            
            if(isset($object[BaseExternalExporter :: SYNC_STATE]))
            {
                switch($object[BaseExternalExporter :: SYNC_STATE])
                {
                    case BaseExternalExporter :: SYNC_NEVER_SYNCHRONIZED:
                        
                        $table .= Translation :: get('ExternalRepositoryNeverSync');
                        $url = Redirect :: get_url(array('application' => RepositoryManager :: APPLICATION_NAME, 'go' => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_IMPORT, RepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $this->export->get_id(), RepositoryManager :: PARAM_EXTERNAL_OBJECT_ID => $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID]));
                        $buttons[] = '<a href="' . $url . '"><img src="' . Theme :: get_common_image_path() . 'import_from_repository.png' . '" />' . Translation :: get('ExternalRepositoryImport') . '</a>';
                        break;
                        
                    case BaseExternalExporter :: SYNC_IDENTICAL:
                        
                        $table .= 'identical';
                        
                        break;
                        
                    case BaseExternalExporter :: SYNC_OLDER_IN_CHAMILO:

                        $table .= Translation :: get('ExternalRepositoryOlderInChamilo');
                        $url = Redirect :: get_url(array('application' => RepositoryManager :: APPLICATION_NAME, 'go' => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_IMPORT, RepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $this->export->get_id(), RepositoryManager :: PARAM_EXTERNAL_OBJECT_ID => $object[BaseExternalExporter :: EXTERNAL_OBJECT_KEY][BaseExternalExporter :: OBJECT_ID]));
                        $buttons[] = '<a href="' . $url . '"><img src="' . Theme :: get_common_image_path() . 'import_from_repository.png' . '" />' . Translation :: get('ExternalRepositoryImport') . '</a>';                        
                        
                        break;
                        
                    case BaseExternalExporter :: SYNC_NEWER_IN_CHAMILO:
                        
                        $table .= Translation :: get('ExternalRepositoryNewerInChamilo');
                        $url = Redirect :: get_url(array('application' => RepositoryManager :: APPLICATION_NAME, 'go' => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_EXPORT, RepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $this->export->get_id(), RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object[BaseExternalExporter :: CHAMILO_OBJECT_KEY]->get_id()));
                        $buttons[] = '<a href="' . $url . '"><img src="' . Theme :: get_common_image_path() . 'export_to_repository.png' . '" />' . Translation :: get('ExternalRepositoryExport') . '</a>';
                        
                        break;
                }
            }
            
            $table .= '</td>';
            
            $table .= '<td>';
            
            if(isset($object_state) && $object_state == ContentObject :: STATE_RECYCLED)
            {
                $table .= Translation :: get('ObjectIsRecycled');
            }
            else
            {    
                $table .= implode($buttons);
            }
                
            $table .= '</td>';
            
            $table .= '</tr>';
        }
        
        $table .= '</table>';
                
        return $table;
    }
    
   
    
}
?>