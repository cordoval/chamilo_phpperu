<?php

namespace common\libraries;

require_once dirname(__FILE__) . '/rest_message_renderer.class.php';
require_once dirname(__FILE__) . '/success_rest_message.class.php';
require_once dirname(__FILE__) . '/rest_server.class.php';

/**
 * Extension to the rest server to automatically determine the application - the object and check for authentication + rights
 */
class ChamiloRestServer extends RestServer
{
    const PARAM_ID = 'id';
    const PARAM_APPLICATION = 'application';
    const PARAM_OBJECT = 'object';

    private $webservice_handler;
    private $result;

    function handle()
    {
        $this->process_request();
        $this->manipulate_retrieved_data();

        if($this->determine_webservice_handler())
        {
            $this->call_webservice_handler();
        }

        $this->handle_result();
    }

    private function manipulate_retrieved_data()
    {
        unset($this->data[self :: PARAM_APPLICATION]);
        unset($this->data[self :: PARAM_OBJECT]);
        unset($this->data[self :: PARAM_ID]);
    }

    private function determine_webservice_handler()
    {
        $application = Request :: get(self :: PARAM_APPLICATION);
        $object = Request :: get(self :: PARAM_OBJECT);

        if($application && $object)
        {
            $type = Application :: get_type($application);
            $path = $type :: get_application_path($application) . 'php/webservices/' . $object . '/webservice_handler.class.php';
            require_once($path);
            $class = Application :: determine_namespace($application) . '\\' . Utilities :: underscores_to_camelcase($object) . 'WebserviceHandler';

            $this->webservice_handler = new $class();
            return true;
        }
        else
        {
            $this->result = new SuccessRestMessage(false, Translation :: get('ApplicationAndObjectShouldNotBeEmpty'));
            return false;
        }

    }

    private function call_webservice_handler()
    {
        $id = Request :: get(self :: PARAM_ID);

        switch ($this->get_method())
        {
            case self :: METHOD_GET :
                if ($id)
                {
                    $function = 'get';
                    $parameters = array($id);
                }
                else
                {
                    $function = 'get_list';
                }
                break;
            case self :: METHOD_POST :
                $function = 'update';
                $parameters = array($id, $this->data);
                break;
            case self :: METHOD_PUT :
                $function = 'create';
                $parameters = array($this->data);
                break;
            case self :: METHOD_DELETE :
                $function = 'delete';
                $parameters = array($this->id);
                break;
        }

        if (method_exists($this->webservice_handler, $function))
        {
            if($this->is_allowed(Request :: get(self :: PARAM_APPLICATION), Request :: get(self :: PARAM_OBJECT), $function))
            {
                $this->result = call_user_func(array($this->webservice_handler, $function), $parameters);
            }
            else
            {
                $this->result = new SuccessRestMessage(false, Translation :: get('NotAllowedToExecuteWebservice'));
            }
        }
        else
        {
            $this->result = new SuccessRestMessage(false, Translation :: get('MethodNotImplemented'));
        }
        
    }

    private function is_allowed($application, $object, $function)
    {
        return true;
    }

    private function handle_result()
    {
        $renderer = RestMessageRenderer :: factory($this->format);
        $renderer->render($this->result);
    }
}

?>
