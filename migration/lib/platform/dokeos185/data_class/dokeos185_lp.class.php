<?php
/**
 * $Id: dokeos185_lp.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../../lib/import/import_lp.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/content_object/learning_path/learning_path.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/content_object_publication.class.php';
require_once 'dokeos185_item_property.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/category_manager/repository_category.class.php';

/**
 * This class presents a Dokeos185 lp
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Lp extends Dokeos185MigrationDataClass
{
    /** 
     * Migration data manager
     */
    private static $mgdm;
    
    private $item_property;
    
    /**
     * Dokeos185Lp properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_LP_TYPE = 'lp_type';
    const PROPERTY_NAME = 'name';
    const PROPERTY_REF = 'ref';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_PATH = 'path';
    const PROPERTY_FORCE_COMMIT = 'force_commit';
    const PROPERTY_DEFAULT_VIEW_MOD = 'default_view_mod';
    const PROPERTY_DEFAULT_ENCODING = 'default_encoding';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_CONTENT_MAKER = 'content_maker';
    const PROPERTY_CONTENT_LOCAL = 'content_local';
    const PROPERTY_CONTENT_LICENSE = 'content_license';
    const PROPERTY_PREVENT_REINIT = 'prevent_reinit';
    const PROPERTY_JS_LIB = 'js_lib';
    const PROPERTY_DEBUG = 'debug';
    const PROPERTY_THEME = 'theme';
    
    /**
     * Default properties stored in an associative array.
     */
    private $defaultProperties;

    /**
     * Creates a new Dokeos185Lp object
     * @param array $defaultProperties The default properties
     */
    function Dokeos185Lp($defaultProperties = array ())
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
        return array(self :: PROPERTY_ID, self :: PROPERTY_LP_TYPE, self :: PROPERTY_NAME, self :: PROPERTY_REF, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PATH, self :: PROPERTY_FORCE_COMMIT, self :: PROPERTY_DEFAULT_VIEW_MOD, self :: PROPERTY_DEFAULT_ENCODING, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_CONTENT_MAKER, self :: PROPERTY_CONTENT_LOCAL, self :: PROPERTY_CONTENT_LICENSE, self :: PROPERTY_PREVENT_REINIT, self :: PROPERTY_JS_LIB, self :: PROPERTY_DEBUG, self :: PROPERTY_THEME);
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
     * Returns the id of this Dokeos185Lp.
     * @return the id.
     */
    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    /**
     * Returns the lp_type of this Dokeos185Lp.
     * @return the lp_type.
     */
    function get_lp_type()
    {
        return $this->get_default_property(self :: PROPERTY_LP_TYPE);
    }

    /**
     * Returns the name of this Dokeos185Lp.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Returns the ref of this Dokeos185Lp.
     * @return the ref.
     */
    function get_ref()
    {
        return $this->get_default_property(self :: PROPERTY_REF);
    }

    /**
     * Returns the description of this Dokeos185Lp.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Returns the path of this Dokeos185Lp.
     * @return the path.
     */
    function get_path()
    {
        return $this->get_default_property(self :: PROPERTY_PATH);
    }

    /**
     * Returns the force_commit of this Dokeos185Lp.
     * @return the force_commit.
     */
    function get_force_commit()
    {
        return $this->get_default_property(self :: PROPERTY_FORCE_COMMIT);
    }

    /**
     * Returns the default_view_mod of this Dokeos185Lp.
     * @return the default_view_mod.
     */
    function get_default_view_mod()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_VIEW_MOD);
    }

    /**
     * Returns the default_encoding of this Dokeos185Lp.
     * @return the default_encoding.
     */
    function get_default_encoding()
    {
        return $this->get_default_property(self :: PROPERTY_DEFAULT_ENCODING);
    }

    /**
     * Returns the display_order of this Dokeos185Lp.
     * @return the display_order.
     */
    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    /**
     * Returns the content_maker of this Dokeos185Lp.
     * @return the content_maker.
     */
    function get_content_maker()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_MAKER);
    }

    /**
     * Returns the content_local of this Dokeos185Lp.
     * @return the content_local.
     */
    function get_content_local()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_LOCAL);
    }

    /**
     * Returns the content_license of this Dokeos185Lp.
     * @return the content_license.
     */
    function get_content_license()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_LICENSE);
    }

    /**
     * Returns the prevent_reinit of this Dokeos185Lp.
     * @return the prevent_reinit.
     */
    function get_prevent_reinit()
    {
        return $this->get_default_property(self :: PROPERTY_PREVENT_REINIT);
    }

    /**
     * Returns the js_lib of this Dokeos185Lp.
     * @return the js_lib.
     */
    function get_js_lib()
    {
        return $this->get_default_property(self :: PROPERTY_JS_LIB);
    }

    /**
     * Returns the debug of this Dokeos185Lp.
     * @return the debug.
     */
    function get_debug()
    {
        return $this->get_default_property(self :: PROPERTY_DEBUG);
    }

    /**
     * Returns the theme of this Dokeos185Lp.
     * @return the theme.
     */
    function get_theme()
    {
        return $this->get_default_property(self :: PROPERTY_THEME);
    }

    /**
     * Check if the learning path is valid
     * @param array $array the parameters for the validation
     * @return true if the learning path is valid 
     */
    function is_valid($array)
    {
        $course = $array['course'];
        $old_mgdm = $array['old_mgdm'];
        $mgdm = MigrationDataManager :: get_instance();
        
        $this->item_property = $old_mgdm->get_item_property($course->get_db_name(), 'learnpath', $this->get_id());
        
        if (! $this->get_id() || ! $this->get_lp_type() || ! $this->get_name() || ! $this->item_property || ! $this->item_property->get_ref() || ! $this->item_property->get_insert_date())
        {
            $mgdm->add_failed_element($this->get_id(), $course->get_db_name() . '.lp');
            return false;
        }
        return true;
    }

    /**
     * Convert to new learning path
     * @param array $array the parameters for the conversion
     * @return the new learning path
     */
    function convert_data
    {
        $mgdm = MigrationDataManager :: get_instance();
        $course = $array['course'];
        $new_course_code = $mgdm->get_id_reference($course->get_code(), 'weblcms_course');
        $new_user_id = $mgdm->get_owner($new_course_code);
        //forum parameters
        $lcms_lp = new LearningPath();
        
        // Category for announcements already exists?
        $lcms_category_id = $mgdm->get_parent_id($new_user_id, 'category', Translation :: get('LearningPaths'));
        if (! $lcms_category_id)
        {
            //Create category for tool in lcms
            $lcms_repository_category = new RepositoryCategory();
            $lcms_repository_category->set_user_id($new_user_id);
            $lcms_repository_category->set_name(Translation :: get('LearningPaths'));
            $lcms_repository_category->set_parent(0);
            
            //Create category in database
            $lcms_repository_category->create();
            
            $lcms_lp->set_parent_id($lcms_repository_category->get_id());
        }
        else
        {
            $lcms_lp->set_parent_id($lcms_category_id);
        }
        
        if (! $this->get_name())
            $lcms_lp->set_title(substr($this->get_description(), 0, 20));
        else
            $lcms_lp->set_title($this->get_name());
        
        if (! $this->get_description())
            $lcms_lp->set_description($this->get_name());
        else
            $lcms_lp->set_description($this->get_description());
        
        $lcms_lp->set_owner_id($new_user_id);
        $lcms_lp->set_creation_date($mgdm->make_unix_time($this->item_property->get_insert_date()));
        $lcms_lp->set_modification_date($mgdm->make_unix_time($this->item_property->get_lastedit_date()));
        $lcms_lp->set_display_order_index($this->get_display_order());
        
        if ($this->item_property->get_visibility() == 2)
            $lcms_lp->set_state(1);
        
        $version = strtolower($this->get_content_maker());
        if($version == 'dokeos')
        	$version = 'chamilo';
        	
        $lcms_lp->set_version($version);
            
        //create announcement in database
        $lcms_lp->create_all();
        
        //Add id references to temp table
        $mgdm->add_id_reference($this->get_id(), $lcms_lp->get_id(), 'repository_learning_path');
        
        /*
		//publication
		if($this->item_property->get_visibility() <= 1) 
		{
			$publication = new ContentObjectPublication();
			
			$publication->set_content_object($lcms_announcement);
			$publication->set_course_id($new_course_code);
			$publication->set_publisher_id($new_user_id);
			$publication->set_tool('announcement');
			$publication->set_category_id(0);
			//$publication->set_from_date(self :: $mgdm->make_unix_time($this->item_property->get_start_visible()));
			//$publication->set_to_date(self :: $mgdm->make_unix_time($this->item_property->get_end_visible()));
			$publication->set_from_date(0);
			$publication->set_to_date(0);
			$publication->set_publication_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
			$publication->set_modified_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
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
        return $lcms_lp;
    }

    /**
     * Retrieve all learning paths from the database
     * @param array $parameters parameters for the retrieval
     * @return array of learning paths
     */
    static function retrieve_data($parameters)
    {
        $old_mgdm = $parameters['old_mgdm'];
        
        if ($parameters['del_files'] = ! 1)
            $tool_name = 'learnpath';
        
        $coursedb = $parameters['course']->get_db_name();
        $tablename = 'lp';
        $classname = 'Dokeos185Lp';
        
        return $old_mgdm->get_all($coursedb, $tablename, $classname, $tool_name, $parameters['offset'], $parameters['limit']);
    }

    static function get_database_table($parameters)
    {
        $array = array();
        $array['database'] = $parameters['course']->get_db_name();
        $array['table'] = 'lp';
        return $array;
    }
}

?>