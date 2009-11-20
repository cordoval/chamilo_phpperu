<?php
/**
 * $Id: external_export_browser_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 */
class ExternalExportBrowserForm extends FormValidator
{
    private $catalogs;
    private $content_object_id;

    public function ExternalExportBrowserForm($content_object_id, $action, $catalogs)
    {
        parent :: __construct('external_export_browser', 'post', $action);
        
        $this->content_object_id = (isset($content_object_id) && strlen($content_object_id) > 0) ? $content_object_id : DataClass :: NO_UID;
        $this->catalogs = $catalogs;
        
        $this->build_form();
        
    //debug($this->catalogs);
    }

    private function build_form()
    {
        //echo '<div style="margin-left:auto;margin-right:auto;width:600px;background-color:yellow">';
        echo '<div>';
        
        echo '<div>' . Translation :: translate('ExternalExportListDescription') . '</div>';
        echo '<p>&nbsp;</p>';
        
        echo $this->format_export_list();
        
        echo '</div>';
    }

    private function format_export_list()
    {
//        $table = array();
//        $table[] = '<table border="0" cellspacing="0" cellpadding="5">';
//        
//        foreach ($this->catalogs[ExternalExport :: CATALOG_EXPORT_LIST] as $export)
//        {
//            $url = Redirect :: get_url(array('application' => RepositoryManager :: APPLICATION_NAME, 'go' => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_EXPORT, RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID => $export->get_id(), RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->content_object_id));
//            
//            $table[] = '<tr>';
//            $table[] = '<td style="vertical-align:top">';
//            $table[] = '<a href="' . $url . '">' . $export->get_title() . '</a>';
//            $table[] = '</td>';
//            $table[] = '<td style="padding-left:50px;">';
//            $table[] = $export->get_description();
//            $table[] = '</td>';
//            
//            $table[] = '</tr>';
//        }
//        
//        $table[] = '</table>';
//        return implode($table);
        
        $list = array();
        
        $list[] = '<div>';
        
        foreach ($this->catalogs[ExternalExport :: CATALOG_EXPORT_LIST] as $export)
        {
            $url_export_to_repository    = Redirect :: get_url(array('application' => RepositoryManager :: APPLICATION_NAME, 'go' => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_EXPORT, RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID => $export->get_id(), RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->content_object_id));
            $url_list_repository_objects = Redirect :: get_url(array('application' => RepositoryManager :: APPLICATION_NAME, 'go' => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_LIST_OBJECTS, RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID => $export->get_id(), RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->content_object_id));
            
            $list[] = '<fieldset style="width:50%">';
            $list[] = '<legend>' . $export->get_title() . '</legend>';
            
            $list[] = '<p>' . $export->get_description() . '</p>';
            
            $list[] = '<div style="float:right;width:50%;text-align:left">';
            $list[] = '<img src="' . Theme :: get_common_image_path() . 'action_import.png' . '" /> <a href="' . $url_list_repository_objects . '">' . Translation :: translate('ExternalExportAvailableObjects') . '</a>';
            $list[] = '</div>';
            
            $list[] = '<div style="width:50%;text-align:left">';
            $list[] = '<img src="' . Theme :: get_common_image_path() . 'action_publish.png' . '" /> <a href="' . $url_export_to_repository . '">' . Translation :: translate('ExternalExportExport') . '</a>';
            $list[] = '</div>';
            
            $list[] = '</fieldset><br/>';
            
        }
      
        $list[] = '</div>';
        
        return implode($list);
    }

}
?>