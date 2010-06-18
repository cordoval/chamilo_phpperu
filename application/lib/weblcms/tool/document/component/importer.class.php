<?php

class DocumentToolImporterComponent extends DocumentTool{

   
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Import')));
        
        $import_form = new ContentObjectImportForm('import', 'post', $this->get_url(), $this->get_parameter(RepositoryManager :: PARAM_CATEGORY_ID), $this->get_user());

        if ($import_form->validate())
        {
            //$success = $import_form->import_content_object();

            $messages = array();
            $errors = array();
            if($success){
            	$messages[] = Translation::translate('ContentObjectImported');
            }else{
            	$errors[] = Translation::translate('ContentObjectNotImported');
            }

            $messages = array_merge($messages, $import_form->get_messages());
            $warnings = $import_form->get_warnings();
            $errors = array_merge($errors, $import_form->get_errors());
            $parameters = array(Application::PARAM_ACTION => RepositoryManager::ACTION_BROWSE_CONTENT_OBJECTS);
            $parameters[self::PARAM_MESSAGE] = implode('<br/>', $messages);
            $parameters[self::PARAM_WARNING_MESSAGE] = implode('<br/>', $warnings);
            $parameters[self::PARAM_ERROR_MESSAGE] = implode('<br/>', $errors);

            $this->simple_redirect($parameters);

        }
        else
        {
            $this->display_header($trail, false, true);
            $import_form->display();
            $this->display_footer();
        }
    }
}
?>