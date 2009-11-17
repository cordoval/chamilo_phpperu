<?php
/**
 * $Id: migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 * 
 * This abstract class defines a page which is used in a migration wizard.
 */
require_once dirname(__FILE__) . '/../../../../migration_data_manager.class.php';
require_once dirname(__FILE__) . '/../../../../old_migration_data_manager.class.php';

abstract class MigrationWizardPage extends HTML_QuickForm_Page
{
    /**
     * The MigrationManager component in which the wizard runs.
     */
    private $parent;
    protected $failed_elements;
    protected $succes;
    protected $logfile;
    protected $old_system;
    protected $command_execute;
    protected $passedtime;
    protected $name;
    protected $exportvalues;
    protected $old_mgdm;

    /**
     * Constructor
     * @param string $name A unique name of this page in the wizard
     * @param MigrationManagerComponent $parent The MigrationManager component
     * in which the wizard runs.
     */
    public function MigrationWizardPage($name, $parent, $command_execute = false)
    {
        $this->name = $name;
        $this->parent = $parent;
        parent :: HTML_QuickForm_Page($name, 'post');
        $this->command_execute = $command_execute;
    }

    /**
     * Returns the MigrationManager component in which this wizard runs
     * @return MigrationManager
     */
    function get_parent()
    {
        return $this->parent;
    }

    /**
     * Set the language interface of the wizard page
     * @param string $lang A name of a language 
     */
    function set_lang($lang)
    {
        global $language_interface;
        $language_interface = $lang;
    }

    /**
     * Dummy method that classes can implement
     */
    function perform()
    {
    
    }

    /**
     * Dummy method that classes can implement
     */
    function next_step_info()
    {
        $next_page = $this->get_next_page();
        $ctitle = ucfirst(substr($next_page, 8)) . '_info';
        return Translation :: get($ctitle);
    }

    /**
     * Get the info of a migration page
     */
    function get_info()
    {
        for($i = 0; $i < count($this->succes); $i ++)
        {
            $message = $message . '<br />' . $this->succes[$i] . ' ' . $this->get_message($i) . ' ' . Translation :: get('migrated');
            
            if (count($this->failed_elements[$i]) > 0)
                $message = $message . '<br / >' . count($this->failed_elements[$i]) . ' ' . $this->get_message($i) . ' ' . Translation :: get('failed');
        }
        $message = $message . '<br/><br/>Please check the <a href="' . Path :: get(WEB_PATH) . 'documentation/migration.html" target="about_blank">migration manual</a> for more information';
        $message = $message . '<br />';
        $message = $message . '<br />' . Translation :: get('Dont_forget');
        $message = $message . '<br/><br/>Time used: ' . $this->passedtime;
        return $message;
    }

    /**
     * Dummy method that some classes can implement 
     */
    function get_message()
    {
    
    }

    /**
     * General method for migration
     */
    function migrate($type, $retrieve_parms = array(), $convert_parms = array(), $course = null, $i)
    {
        $class = Import :: factory($this->old_system, strtolower($type));

        if ($course)
        {
            $this->logfile->add_message('Starting migration ' . $type . ' for course ' . $course->get_code());
            $retrieve_parms['course'] = $course;
            $convert_parms['course'] = $course;
            $final_message = $type . ' migrated for course ' . $course->get_code();
            $extra_message = ' COURSE: ' . $course->get_code();
        
        }
        else
        {
            $this->logfile->add_message('Starting migration ' . $type);
            $final_message = $type . ' migrated';
        }
        
        $database_table = $class->get_database_table($retrieve_parms);
        
        $max_records = $this->old_mgdm->count_records($database_table['database'], $database_table['table'], $retrieve_parms['condition']);
      	
        $current_record = 0;
        
        while ($max_records > 0)
        {
            if ($max_records - 1000 > 0)
            {
                $retrieve_parms['offset'] = $current_record;
                $retrieve_parms['limit'] = 1000;
            }
            else
            {
                $retrieve_parms['offset'] = $current_record;
                $retrieve_parms['limit'] = $max_records;
            }
            
            $items = array();
            $items = $class->get_all($retrieve_parms);
            
            foreach ($items as $j => $item)
            {
                if ($item->is_valid($convert_parms))
                {
                    $lcms_item = $item->convert_to_lcms($convert_parms);
                    
                    if ($lcms_item)
                    {
                        $message = $this->write_succes($lcms_item, $extra_message, $type);
                        //$message ='SUCCES: user added ( ID: ' . $lcms_item . $extra_message . ' )';
                        $this->logfile->add_message($message);
                        $this->succes[$i] ++;
                    }
                    unset($lcms_item);
                    unset($message);
                }
                else
                {
                    if (! ($item instanceof Dokeos185SettingCurrent))
                    {
                        $message = $this->write_failed($item, $extra_message, $type);
                        $this->logfile->add_message($message);
                        $this->failed_elements[$i][] = $message;
                    }
                    unset($message);
                }
                unset($item);
                unset($items[$j]);
            }
            
            $items = array();
            unset($items);
            array_values($items);
            $this->logfile->add_message($retrieve_parms['limit'] . ' records done');
            $current_record += $retrieve_parms['limit'];
            $max_records -= $retrieve_parms['limit'];
        }
        
        unset($class);
        unset($course);
        $this->logfile->add_message($final_message);
    }

    /** 
     * Standard form has a next button
     */
    function buildForm()
    {
        if ($this->get_next_page())
        {
            $this->_formBuilt = true;
            $prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
            $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        }
    }

    /**
     * Retrieves the message from an object that does not have the default get_id method to retrieve the id
     * @param object $item an object of which you want to retrieve the id
     * @param string $extra_message an extra message you want to add 
     * @param string $type the type of the item
     * @return a 'failed' message with the correct id
     */
    function write_failed($item, $extra_message, $type)
    {
        switch (true)
        {
            case ($item instanceof Dokeos185ClassUser) :
                return 'FAILED: ' . $type . ' added ( Class: ' . $item->get_class_id() . ' UserID:' . $item->get_user_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185CourseRelUser) :
                return 'FAILED: ' . $type . ' is not valid ( User: ' . $item->get_user_id() . ' Course:' . $item->get_course_code() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185DropboxFeedback) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_feedback_id() . $extra_message . ' )';
            
            case (($item instanceof Dokeos185DropboxCategory) || ($item instanceof Dokeos185ForumCategory)) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_cat_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185ForumForum) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_forum_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185ForumPost) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_post_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185ForumThread) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_thread_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185Survey) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_survey_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185SurveyAnswer) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_answer_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185SurveyQuestion) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_question_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185QuestionOption) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_question_option_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185User) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_user_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185trackelogin) :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_login_id() . $extra_message . ' )';
            
            default :
                return 'FAILED: ' . $type . ' is not valid ( ID: ' . $item->get_id() . $extra_message . ' )';
        }
    }

    function write_succes($item, $extra_message, $type)
    {
        switch (true)
        {
            case ($item instanceof User) :
                return 'SUCCES: ' . $type . ' added ( ID: ' . $item->get_id() . $extra_message . ' )';
            
            case ($item instanceof CourseUserRelation) :
                return 'SUCCES: ' . $type . ' added ( Course: ' . $item->get_course() . ' User: ' . $item->get_user() . ' )';
            
            case ($item instanceof GroupRelUser) :
                return 'SUCCES: ' . $type . ' added ( Class: ' . $item->get_group_id() . ' UserID:' . $item->get_user_id() . $extra_message . ' )';
            
            case ($item instanceof Dokeos185CourseRelUser) :
                return 'SUCCES: ' . $type . ' added ( Course: ' . $item->get_course() . ' User:' . $item->get_user() . $extra_message . ' )';
            
            default :
                return 'SUCCES: ' . $type . ' added ( ID: ' . $item->get_id() . $extra_message . ' )';
        }
    }

    function get_next_page()
    {
        $passed = false;
        
        foreach ($this->controller->exportValues() as $key => $value)
        {
            if ($passed == true)
            {
                return $key;
            }
            if (strcmp($key, 'migrate' . substr($this->name, 4)) == 0)
            {
                $passed = true;
            }
        }
        
        return null;
    }
}

?>