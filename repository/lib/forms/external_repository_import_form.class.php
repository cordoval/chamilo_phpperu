<?php
class ExternalRepositoryImportForm extends FormValidator
{
    private $repository_object_infos;
    private $export;
    
    function ExternalRepositoryImportForm($repository_object_infos, $export, $action_url)
    {
        parent :: __construct('external_repository_import', 'post', $action_url);
        
        $this->repository_object_infos = $repository_object_infos;
        $this->export = $export;
        
    }
    
    
    public function display()
    {
        //DebugUtilities::show($this->repository_object_infos);
        
        echo $this->build_header_text();
        //echo '<hr/>';
        echo $this->build_notification_zone();
        //echo '<hr/>';
        echo $this->build_repository_object_info_zone();
        //echo '<hr/>';
        echo $this->build_chamilo_object_info_zone();
        //echo '<hr/>';
        $this->add_buttons();
        
        parent :: display();
    }
    
    public function display_import_success()
    {
        echo '<div>';
        
        echo '<p>Import ' . Translation :: translate('ExternalImportConfirmationText') . '</p>';
        
        echo '</div>';
    }
    
    private function build_header_text()
    {
        $html = array();
        $html[] = '<div>';
        $html[] = '<p>You are about to import an object from the repository <em>' . $this->export->get_title() . '</em></p>';
        $html[] = '</div>';
        
        return implode($html);
    }
    
    private function build_repository_object_info_zone()
    {
        $external_object_infos = $this->repository_object_infos[BaseExternalExporter :: EXTERNAL_OBJECT_KEY];
        
        $html = array();
        $html[] = '<div>';
        $html[] = '<p>This repository object has the following properties:</p>';
        
        $html[] = '<table border="0" cellspacing="0" cellpadding="5">';
        
        if(isset($external_object_infos[BaseExternalExporter :: OBJECT_ID]))
        {
            $html[] = '<tr>';
            $html[] = '<td>' . Translation :: translate('ExternalRepositoryObjectId') . '</td>';
            $html[] = '<td>:</td>';
            $html[] = '<td>' . $external_object_infos[BaseExternalExporter :: OBJECT_ID] . '</td>';
            $html[] = '</tr>';
        }
        
        if(isset($external_object_infos[BaseExternalExporter :: OBJECT_TITLE]))
        {
            $html[] = '<tr>';
            $html[] = '<td>' . Translation::translate('ExternalRepositoryObjectTitle') . '</td>';
            $html[] = '<td>:</td>';
            $html[] = '<td>' . $external_object_infos[BaseExternalExporter :: OBJECT_TITLE] . '</td>';
            $html[] = '</tr>';
        }
        
        if(isset($external_object_infos[BaseExternalExporter :: OBJECT_DESCRIPTION]))
        {
            $html[] = '<tr>';
            $html[] = '<td>' . Translation::translate('ExternalRepositoryObjectDescription') . '</td>';
            $html[] = '<td>:</td>';
            $html[] = '<td>' . $external_object_infos[BaseExternalExporter :: OBJECT_DESCRIPTION] . '</td>';
            $html[] = '</tr>';
        }
        
        $external_object_date = $this->get_repository_last_modification_date();
        if(isset($external_object_date))
        {
            $html[] = '<tr>';
            $html[] = '<td>' . Translation::translate('ExternalRepositoryLastUpdate') . '</td>';
            $html[] = '<td>:</td>';    
            $html[] = '<td>' . $external_object_date . '</td>';
            $html[] = '</tr>';
        }
        
        $html[] = '</table>';
        
        $html[] = '</div>';
        
        return implode($html);
    }
    
    private function build_notification_zone()
    {
        $html = array();
        $html[] = '<div>';
        
        if(isset($this->repository_object_infos[BaseExternalExporter :: CHAMILO_OBJECT_KEY]))
        {
            $date_comparison_result_text = null;
            
            switch($this->repository_object_infos[BaseExternalExporter :: SYNC_STATE])
            {
                case BaseExternalExporter :: SYNC_NEWER_IN_CHAMILO:
                    $date_comparison_result_text = Translation :: translate('ExternalRepositoryNewerInChamilo');
                    break;
                    
                case BaseExternalExporter :: SYNC_OLDER_IN_CHAMILO:
                    $date_comparison_result_text = Translation :: translate('ExternalRepositoryOlderInChamilo');
                    break;
                    
                case BaseExternalExporter :: SYNC_IDENTICAL:
                    $date_comparison_result_text = Translation :: translate('ExternalRepositorySynchronized');
                    break;
                    
                case BaseExternalExporter :: SYNC_NEVER_SYNCHRONIZED:
                    $date_comparison_result_text = Translation :: translate('ExternalRepositoryNeverSync');
                    break;
            }
            
            $html[] = '<div class="warning-message"><p>This object already exist in Chamilo (last modification: ' . $this->get_chamilo_last_modification_date() . ')</p>';
            $html[] = '<p>' . $date_comparison_result_text . '</p>';
            $html[] = '<p>If you click on import, the actual version of the object in your Chamilo repository will be replaced</p></div>';
            
        }
        else
        {
            $html[] = '<div class="normal-message"><p>This object does not exist in Chamilo yet.</p><p>If you click on import, you will be able to use it in your Chamilo repository</p></div>';
        }
        
        $html[] = '</div>';
     
        return implode($html);
    }
    
    private function build_chamilo_object_info_zone()
    {
        
    }
    
    private function add_buttons()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive update'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
    
    private function get_repository_last_modification_date()
    {
        if(isset($this->repository_object_infos[BaseExternalExporter :: EXTERNAL_OBJECT_KEY]))
        {
            $external_object_infos = $this->repository_object_infos[BaseExternalExporter :: EXTERNAL_OBJECT_KEY];
            
            $external_object_date = null;
            if(isset($external_object_infos[BaseExternalExporter :: OBJECT_MODIFICATION_DATE]))
            {
                $external_object_date = date('Y-m-d H:i:s', strtotime($external_object_infos[BaseExternalExporter :: OBJECT_MODIFICATION_DATE]));
            }
            elseif(isset($external_object_infos[BaseExternalExporter :: OBJECT_CREATION_DATE]))
            {
                $external_object_date = date('Y-m-d H:i:s', strtotime($external_object_infos[BaseExternalExporter :: OBJECT_CREATION_DATE]));
            }
            
            return $external_object_date;
        }
        else
        {
            return null;
        }
    }
    
    private function get_chamilo_last_modification_date()
    {
        if(isset($this->repository_object_infos[BaseExternalExporter :: CHAMILO_OBJECT_KEY]))
        {
            $content_object = $this->repository_object_infos[BaseExternalExporter :: CHAMILO_OBJECT_KEY];
                
            //DebugUtilities::show($content_object);
            
            $chamilo_create_date = $content_object->get_creation_date();
            $chamilo_modification_date = $content_object->get_modification_date();
            
            $chamilo_object_date = null;
            if(isset($chamilo_modification_date))
            {
                $chamilo_object_date = date('Y-m-d H:i:s', $chamilo_modification_date);;
            }
            elseif(isset($chamilo_create_date))
            {
                $chamilo_object_date = date('Y-m-d H:i:s', $chamilo_create_date);;
            }
            
            return $chamilo_object_date;
        }
        else
        {
            return null;
        }
    }

}
?>