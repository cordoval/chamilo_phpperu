<?php
/**
 * $Id: external_repository_export_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 */
class ExternalRepositoryExportForm extends FormValidator
{
    protected $catalogs;
    
    /**
     * @var ContentObject
     */
    protected $content_object;
    
    /**
     * @var FedoraExternalRepository
     */
    protected $export;

    protected function ExternalRepositoryExportForm($content_object, $export, $action, $catalogs)
    {
        parent :: __construct('external_repository_browser', 'post', $action);
        
        $this->content_object = $content_object;
        $this->export = $export;
        $this->catalogs = $catalogs;
        
    //$this->build_form();
    

    //debug($this->catalogs);
    }

    /**
     * Return an instance of ExternalRepositoryExportForm or a child of ExternalRepositoryExportForm
     * 
     * @param $content_object ContentObject
     * @param $export ExternalRepository
     * @param $action string
     * @param $catalogs	array 
     * @return ExternalRepositoryExportForm
     */
    public function get_instance($content_object, $export, $action, $catalogs)
    {
        $export_type = strtolower($export->get_type());
        $catalog_name = strtolower($export->get_catalog_name());
        
        $class_name = null;
        if (file_exists(Path :: get_repository_path() . '/lib/export/external_export/' . strtolower($export_type) . '/custom/' . strtolower($catalog_name) . '_external_repository_export_form.class.php'))
        {
            require_once Path :: get_repository_path() . '/lib/export/external_export/' . strtolower($export_type) . '/custom/' . strtolower($catalog_name) . '_external_repository_export_form.class.php';
            $class_name = Utilities :: underscores_to_camelcase($catalog_name) . 'ExternalRepositoryExportForm';
        }
        else
        {
            $class_name = 'ExternalRepositoryExportForm';
        }
        
        if (isset($class_name))
        {
            return new $class_name($content_object, $export, $action, $catalogs);
        }
        else
        {
            throw new Exception('Export form for \'' . $export_type . '\' not found');
        }
    }

    protected function build_form()
    {
        echo '<div>';
        
        $this->display_export_confirmation();
        
        echo '</div>';
    }

    public function display()
    {
        $this->build_form();
        
        parent :: display();
    }

    public function display_repository_details($external_repository)
    {
        //debug(array($this->export));
        

        $table = array();
        $table[] = '<table border="0" cellpadding="5" cellspacing="0">';
        
        if (method_exists($external_repository, 'get_title'))
        {
            $table[] = '<tr>';
            //$table[] = '<td><h3>' . Translation :: get('Title') . '</h3></td>';
            //$table[] = '<td></td>';
            $table[] = '<td colspan="2"><h3>' . $external_repository->get_title() . '</h3></td>';
            $table[] = '</tr>';
        }
        
        if (method_exists($external_repository, 'get_base_url'))
        {
            $table[] = '<tr>';
            $table[] = '<td>' . Translation :: get('BaseURL') . '</td>';
            $table[] = '<td>' . $external_repository->get_base_url() . '</td>';
            $table[] = '</tr>';
        }
        
        $table[] = '</table>';
        
        echo implode($table);
    }

    public function display_export_confirmation()
    {
        echo '<div>';
        
        echo '<p>' . str_replace('{ContentObject.title}', $this->content_object->get_title(), Translation :: get('ExternalRepositoryExportConfirmationText')) . '</p>';
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Confirm'), array('class' => 'positive update'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        echo '</div>';
    }

    /**
     * 
     * @param $repository_uid string
     * @return void
     */
    public function display_export_success($repository_uid)
    {
        echo '<div>';
        
        echo '<p>' . str_replace('{ExternalRepository.uid}', $repository_uid, Translation :: get('ExternalRepositoryExportSuccess')) . '</p>';
        
        echo '</div>';
    }

}
?>