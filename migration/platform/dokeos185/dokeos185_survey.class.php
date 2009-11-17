<?php
/**
 * $Id: dokeos185_survey.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_survey.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/learning_style_survey/learning_style_survey.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/category/category.class.php';

/**
 * This class presents a Dokeos185 survey
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Survey extends ImportSurvey
{
    private static $mgdm;
    /**
     * Dokeos185Survey properties
     */
    const PROPERTY_SURVEY_ID = 'survey_id';
    const PROPERTY_CODE = 'code';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_SUBTITLE = 'subtitle';
    const PROPERTY_AUTHOR = 'author';
    const PROPERTY_LANG = 'lang';
    const PROPERTY_AVAIL_FROM = 'avail_from';
    const PROPERTY_AVAIL_TILL = 'avail_till';
    const PROPERTY_IS_SHARED = 'is_shared';
    const PROPERTY_TEMPLATE = 'template';
    const PROPERTY_INTRO = 'intro';
    const PROPERTY_SURVEYTHANKS = 'surveythanks';
    const PROPERTY_CREATION_DATE = 'creation_date';
    const PROPERTY_INVITED = 'invited';
    const PROPERTY_ANSWERED = 'answered';
    const PROPERTY_INVITE_MAIL = 'invite_mail';
    const PROPERTY_REMINDER_MAIL = 'reminder_mail';
    const PROPERTY_ANONYMOUS = 'anonymous';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185Survey object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Survey($defaultProperties = array ())
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->defaultProperties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->defaultProperties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_SURVEY_ID, self :: PROPERTY_CODE, self :: PROPERTY_TITLE, self :: PROPERTY_SUBTITLE, self :: PROPERTY_AUTHOR, self :: PROPERTY_LANG, self :: PROPERTY_AVAIL_FROM, self :: PROPERTY_AVAIL_TILL, self :: PROPERTY_IS_SHARED, self :: PROPERTY_TEMPLATE, self :: PROPERTY_INTRO, self :: PROPERTY_SURVEYTHANKS, self :: PROPERTY_CREATION_DATE, self :: PROPERTY_INVITED, self :: PROPERTY_ANSWERED, self :: PROPERTY_INVITE_MAIL, self :: PROPERTY_REMINDER_MAIL, self :: PROPERTY_ANONYMOUS);
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->defaultProperties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($defaultProperties)
    {
        $this->defaultProperties = $defaultProperties;
    }

    /**
     * Returns the survey_id of this Dokeos185Survey.
     * @return the survey_id.
     */
    function get_survey_id()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
    }

    /**
     * Returns the code of this Dokeos185Survey.
     * @return the code.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Returns the title of this Dokeos185Survey.
     * @return the title.
     */
    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    /**
     * Returns the subtitle of this Dokeos185Survey.
     * @return the subtitle.
     */
    function get_subtitle()
    {
        return $this->get_default_property(self :: PROPERTY_SUBTITLE);
    }

    /**
     * Returns the author of this Dokeos185Survey.
     * @return the author.
     */
    function get_author()
    {
        return $this->get_default_property(self :: PROPERTY_AUTHOR);
    }

    /**
     * Returns the lang of this Dokeos185Survey.
     * @return the lang.
     */
    function get_lang()
    {
        return $this->get_default_property(self :: PROPERTY_LANG);
    }

    /**
     * Returns the avail_from of this Dokeos185Survey.
     * @return the avail_from.
     */
    function get_avail_from()
    {
        return $this->get_default_property(self :: PROPERTY_AVAIL_FROM);
    }

    /**
     * Returns the avail_till of this Dokeos185Survey.
     * @return the avail_till.
     */
    function get_avail_till()
    {
        return $this->get_default_property(self :: PROPERTY_AVAIL_TILL);
    }

    /**
     * Returns the is_shared of this Dokeos185Survey.
     * @return the is_shared.
     */
    function get_is_shared()
    {
        return $this->get_default_property(self :: PROPERTY_IS_SHARED);
    }

    /**
     * Returns the template of this Dokeos185Survey.
     * @return the template.
     */
    function get_template()
    {
        return $this->get_default_property(self :: PROPERTY_TEMPLATE);
    }

    /**
     * Returns the intro of this Dokeos185Survey.
     * @return the intro.
     */
    function get_intro()
    {
        return $this->get_default_property(self :: PROPERTY_INTRO);
    }

    /**
     * Returns the surveythanks of this Dokeos185Survey.
     * @return the surveythanks.
     */
    function get_surveythanks()
    {
        return $this->get_default_property(self :: PROPERTY_SURVEYTHANKS);
    }

    /**
     * Returns the creation_date of this Dokeos185Survey.
     * @return the creation_date.
     */
    function get_creation_date()
    {
        return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
    }

    /**
     * Returns the invited of this Dokeos185Survey.
     * @return the invited.
     */
    function get_invited()
    {
        return $this->get_default_property(self :: PROPERTY_INVITED);
    }

    /**
     * Returns the answered of this Dokeos185Survey.
     * @return the answered.
     */
    function get_answered()
    {
        return $this->get_default_property(self :: PROPERTY_ANSWERED);
    }

    /**
     * Returns the invite_mail of this Dokeos185Survey.
     * @return the invite_mail.
     */
    function get_invite_mail()
    {
        return $this->get_default_property(self :: PROPERTY_INVITE_MAIL);
    }

    /**
     * Returns the reminder_mail of this Dokeos185Survey.
     * @return the reminder_mail.
     */
    function get_reminder_mail()
    {
        return $this->get_default_property(self :: PROPERTY_REMINDER_MAIL);
    }

    /**
     * Returns the anonymous of this Dokeos185Survey.
     * @return the anonymous.
     */
    function get_anonymous()
    {
        return $this->get_default_property(self :: PROPERTY_ANONYMOUS);
    }

    /**
     * Gets all the survey of a course
     * @param Array $array
     * @return Array of dokeos185survey
     */
    static function get_all($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'survey';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'survey';
        $classname = 'Dokeos185Survey';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'survey';
        return $array;
    }

    /**
     * Checks if a survey is valid
     * @param Array $array
     * @return Boolean
     */
    function is_valid($array)
    {
        $course = $array['course'];
        
        if (! $this->get_title() || ! $this->get_creation_date())
        {
            $mgdm = MigrationDataManager :: get_instance();
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.survey');
            return false;
        }
        return true;
    }

    /**
     * migrate survey, sets category
     * @param Array $array
     * @return LearningStyleSurvey
     */
    function convert_to_lcms($array)
    {
        $course = $array['course'];
        $mgdm = MigrationDataManager :: get_instance();
        
        $new_user_id = $mgdm->get_id_reference($this->get_author(), 'user_user');
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        
        if (! $new_user_id)
        {
            $new_user_id = $mgdm->get_owner($new_course_code);
        }
        
        //survey parameters
        $lcms_survey = new LearningStyleSurvey();
        
        // Category for surveys already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('surveys'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new Category();
            $lcms_repository_category->set_owner_id($new_user_id);
            $lcms_repository_category->set_title(Translation :: get('surveys'));
            $lcms_repository_category->set_description('...');
            
            //Retrieve repository id from course
            $repository_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('MyRepository'));
            $lcms_repository_category->set_parent_id($repository_id);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_survey->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_survey->set_parent_id($lcms_category_id);
        }
        
        $lcms_survey->set_title($this->get_title());
        
        $lcms_survey->set_description('...');
        
        $lcms_survey->set_owner_id($new_user_id);
        // unix time ?
        $lcms_survey->set_creation_date($this->get_creation_date());
        $lcms_survey->set_modification_date($this->get_creation_date());
        
        //create announcement in database
        $lcms_survey->create_all();
        
        //publication
        /*
		if($this->item_property->get_visibility() <= 1) 
		{
			$publication = new ContentObjectPublication();
			
			$publication->set_content_object($lcms_announcement);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('announcement');
			$publication->set_category_id(0);
			//$publication->set_from_date($mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date($mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date($mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
			//$publication->set_modified_date(0);
			//$publication->set_display_order_index($this->get_display_order());
			$publication->set_display_order_index(0);
			
			if($this->get_email_sent())
				$publication->set_email_sent($this->get_email_sent());
			else
				$publication->set_email_sent(0);
			
			$publication->set_hidden($this->item_property->get_visibility() == 1?0:1);
			
			//create publication in database
			$publication->create();
		}
		*/
        return $lcms_survey;
    }

}

?>