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
        
        foreach($objects_list as $key => $object)
        {
            $table .= '<tr>';
            $table .= '<td>';
            
            if(isset($object['object']['title']))
            {
                $table .= $object['object']['title'];
            }
            
            $table .= '</td>';
            $table .= '</tr>';
        }
        
        $table .= '</table>';
                
        return $table;
    }
    
   
    
}
?>