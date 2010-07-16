<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mediamosa_external_repository_server_select_formclass
 *
 * @author jevdheyd
 */
class MediamosaExternalRepositoryManagerServerSelectForm extends FormValidator{

    const PARAM_SITUATION_BROWSE = 1;
    const PARAM_SITUATION_UPLOAD = 2;
    const PARAM_SITUATION_IMPORT = 3;
    const PARAM_SITUATION_EXPORT = 4;

    private $form_situation;
    private $servers;
    private $default_server;

    function MediamosaExternalRepositoryManagerServerSelectForm($form_situation, $component)
    {
        
        $this->form_situation = $form_situation;

        parent :: __construct('server_select-form', 'post', $component->get_url());

        //only build a form if more than one options are available
        if(count($this->get_servers()) > 1)
        {
            $this->setDefaults(array(MediamosaExternalRepositoryManager :: PARAM_SERVER => Request :: get(MediamosaExternalRepositoryManager :: PARAM_SERVER)));
            $this->build_select_form();
        }
        //otherwise redirect
        elseif(count($this->servers) == 1)
        {
            $parameters = array();
            $servers = array_keys($this->servers);
            $parameters[MediamosaExternalRepositoryManager :: PARAM_SERVER] = $servers[0];
            
            if(! Request :: get(MediamosaExternalRepositoryManager :: PARAM_SERVER) or Request :: get(MediamosaExternalRepositoryManager :: PARAM_SERVER) != $servers[0]){
                $component->redirect('', false, $parameters);
            }
        }
            //unset($_GET[MediamosaExternalRepositoryManager :: PARAM_SERVER]);
    }

    function build_select_form()
    {
        $this->addElement('select', MediamosaExternalRepositoryManager :: PARAM_SERVER, Translation :: get('Server'), $this->servers);
        $this->addElement('style_submit_button', 'submit', Translation :: get('Select'), array('class' => 'normal filter'));
    }

    function get_servers()
    {
        $dm = MediamosaExternalRepositoryDataManager :: get_instance();

        switch($this->form_situation)
        {
            case self :: PARAM_SITUATION_UPLOAD:
                 $condition = new EqualityCondition(ExternalRepositoryServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE, 1);
                break;
            case self :: PARAM_SITUATION_BROWSE:
                 //$condition = new EqualityCondition(ExternalRepositoryServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE, 1);
                $condition = null;
                break;
            case self ::PARAM_SITUATION_IMPORT:
                $condition = new EqualityCondition(ExternalRepositoryServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE, 0);
                break;
            default:
                $condition = null;
                break;
        }
        //install settings-table if it doesn't exist
        $dm->create_external_repository_server_object_table();
        $servers = $dm->retrieve_external_repository_server_objects($condition);

        while($server = $servers->next_result())
        {
            $server_array[$server->get_id()] = $server->get_title();
            if($server->get_is_default()) $this->default_server = $server->get_id();
        }
        $this->servers = $server_array;

        return $this->servers;
    }

    function get_selected_server()
    {
        return $this->exportValue(MediamosaExternalRepositoryManager :: PARAM_SERVER);
    }

    function get_default_server()
    {
        return $this->default_server;
    }
}
?>
