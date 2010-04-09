<?php
/**
 * $Id: publisher_wizard_process.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.publication_wizard.pages
 */
/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class PublisherWizardProcess extends HTML_QuickForm_Action
{
    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs.
     */
    public function PublisherWizardProcess($parent)
    {
        $this->parent = $parent;
    }

    function perform($page, $actionName)
    {
        $values = $page->controller->exportValues();
        
        $locations = array();
        $options = array();
        
        foreach ($values as $index => $value)
        {
            $array = explode('_', $index);
            $option = array_slice($array, 2);
            $option = implode('_', $option);
            
            if ($array[1] == 'opt')
            {
                $options[$array[0]][$option] = $value;
            }
            else
            {
                $locations[$index] = $value;
            }
        }
        
        // Display the page header
        $this->parent->display_header(new BreadcrumbTrail(), false, true, 'repository publication wizard');
        
        $previous_application = '';
        $message = '';
        
        $ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        if (! is_array($ids))
            $ids = array($ids);
        
        foreach ($ids as $id)
            $los[] = $this->parent->retrieve_content_object($id);
        
        foreach ($locations as $location => $value)
        {
            if ($value == 1)
            {
                $split = explode('|', $location);
                $application_name = $split[0];
                $location_id = $split[1];
                               
                if ($application_name != $previous_application)
                {
                    if ($previous_application != '')
                    {
                        $this->process_result($previous_application, true, $message);
                    }
                    $message = '';
                    $previous_application = $application_name;
                }
                
                //$location = implode('_', $split);
                $application = Application :: factory($application_name);
                foreach ($los as $lo)
                {
                    $message .= $application->publish_content_object($lo, $location_id, $options[Utilities :: camelcase_to_underscores($application_name)]) . '<br />';
                }
            }
        }
               
        $this->process_result($previous_application, true, $message);
        
        $url = $this->parent->get_url(array(RepositoryManager :: PARAM_ACTION => null));
        echo '<a href="' . $url . '">' . Translation :: get('GoBack') . '</a>';
        
        // Display the page footer
        $this->parent->display_footer();
    }

    function display_block_header($application)
    {
        $html = array();
        $html[] = '<div class="content_object" style="padding: 15px 15px 15px 76px; background-image: url(layout/aqua/images/admin/place_' . Utilities :: camelcase_to_underscores($application) . '.png);">';
        $html[] = '<div class="title">' . Translation :: get(Application :: application_to_class($application)) . '</div>';
        $html[] = '<div class="description">';
        return implode("\n", $html);
    }

    function display_block_footer()
    {
        $html = array();
        $html[] = '</div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function process_result($application, $result, $message)
    {
        echo $this->display_block_header($application);
        echo $message;
        echo $this->display_block_footer();
        if (! $result)
        {
            $this->parent->display_footer();
            exit();
        }
    
    }
}
?>