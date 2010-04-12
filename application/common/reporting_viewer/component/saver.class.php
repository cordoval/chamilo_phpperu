<?php
class ReportingViewerSaverComponent extends ReportingViewerComponent
{

    function run()
    {
        $template_registration = $this->get_template();
        
        $template = ReportingTemplate :: factory($template_registration, $this);
        $export_type = Request :: get(ReportingManager :: PARAM_EXPORT_TYPE);
        $export = ReportingExporter :: factory($export_type, $template);
        $file = $export->save();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_CONTENT_OBJECT);
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, 'document');
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_STATUS, true);
        $condition = new AndCondition($conditions);
        
        $registration = AdminDataManager :: get_instance()->count_registrations($condition);
        if ($registration > 0)
        {        
            require_once PATH :: get_repository_path() . 'lib/content_object/document/document.class.php';
            $html_object = new Document();
            $html_object->set_title(Utilities :: underscores_to_camelcase_with_spaces($template->get_name()));
            $html_object->set_description(Utilities :: underscores_to_camelcase_with_spaces($template->get_name()));
            $html_object->set_parent_id(0);
            $html_object->set_owner_id($this->get_user_id());
            $html_object->set_filename($export->get_file_name() . '.' . $export_type);           

            $html_object->set_in_memory_file($file);
            
            if (! $html_object->create())
            {
                $message = 'ObjectNoCreated';
                $error = true;
            }
            else
            {
                $message = 'SavedToRepository';
                $error = false;
            }
        }
        else
        {
            $message = 'DocumentNotAvailable';
            $error = true;
        }
        $parameters = $template->get_parameters(); 
        $parameters[ReportingViewer :: PARAM_REPORTING_VIEWER_ACTION] = ReportingViewer :: ACTION_VIEW_TEMPLATE;
        $parameters[ReportingManager :: PARAM_REPORTING_BLOCK_ID] = $template->get_current_block()->get_id();
        $parameters[ReportingFormatterForm::FORMATTER_TYPE] = $template->get_current_block()->get_displaymode();
        $this->redirect(Translation :: get($message), $error, $parameters);
    }
}
?>