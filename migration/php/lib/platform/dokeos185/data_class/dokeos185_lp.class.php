<?php
/**
 * $Id: dokeos185_lp.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__) . '/../dokeos185_course_data_migration_data_class.class.php';

/**
 * This class presents a Dokeos185 lp
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Lp extends Dokeos185CourseDataMigrationDataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'lp';
    
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
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_ID, self :: PROPERTY_LP_TYPE, self :: PROPERTY_NAME, self :: PROPERTY_REF, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_PATH, self :: PROPERTY_FORCE_COMMIT, self :: PROPERTY_DEFAULT_VIEW_MOD, self :: PROPERTY_DEFAULT_ENCODING, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_CONTENT_MAKER, self :: PROPERTY_CONTENT_LOCAL, self :: PROPERTY_CONTENT_LICENSE, self :: PROPERTY_PREVENT_REINIT, self :: PROPERTY_JS_LIB, self :: PROPERTY_DEBUG, self :: PROPERTY_THEME);
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
     * @return true if the learning path is valid 
     */
    function is_valid()
    {
        $this->set_item_property($this->get_data_manager()->get_item_property($this->get_course(), 'learnpath', $this->get_id()));
        
        if (! $this->get_id() || ! $this->get_lp_type() || !($this->get_name() || $this->get_description()) || ! $this->item_property || ! $this->item_property->get_ref() || ! $this->item_property->get_insert_date())
        {
            $this->create_failed_element($this->get_id());
            $this->set_message(Translation :: get('GeneralInvalidMessage', array('TYPE' => 'learningpath', 'ID' => $this->get_id())));
            return false;
        }
        return true;
    }

    /**
     * Convert to new learning path
     */
    function convert_data()
    {
        $course = $this->get_course();
        
        $new_course_code = $this->get_id_reference($course->get_code(), 'main_database.course');
        $new_user_id = $this->get_data_manager()->get_owner_id($new_course_code);
        
        $chamilo_learning_path = new LearningPath();

        $chamilo_category_id = RepositoryDataManager :: get_repository_category_by_name_or_create_new($new_user_id, Translation :: get('LearningPaths'));
        $chamilo_learning_path->set_parent_id($chamilo_category_id);
        
        if (!$this->get_name())
        {
            $chamilo_learning_path->set_title(substr($this->get_description(), 0, 20));
        }
        else
        {
            $chamilo_learning_path->set_title($this->get_name());
        }
        
        if (! $this->get_description())
        {
            $chamilo_learning_path->set_description($this->get_name());
        }
        else
        {
            $chamilo_learning_path->set_description($this->get_description());
        }
        
        $chamilo_learning_path->set_owner_id($new_user_id);
        $chamilo_learning_path->set_creation_date(strtotime($this->item_property->get_insert_date()));
        $chamilo_learning_path->set_modification_date(strtotime($this->item_property->get_lastedit_date()));
        
        if ($this->item_property->get_visibility() == 2)
        {
            $chamilo_learning_path->set_state(1);
        }
        
        $version = strtolower($this->get_content_maker());
        if($version == 'dokeos')
        {
        	$version = 'chamilo';
        }
        	
        $chamilo_learning_path->set_version($version);
            
        //create announcement in database
        $chamilo_learning_path->create_all();
        
        $this->create_publication($chamilo_learning_path, $new_course_code, $new_user_id, 'learning_path');
        
        $this->create_id_reference($this->get_id(), $chamilo_learning_path->get_id());
        $this->set_message(Translation :: get('GeneralConvertedMessage', array('TYPE' => 'learning_path', 'OLD_ID' => $this->get_id(), 'NEW_ID' => $chamilo_learning_path->get_id())));
        
    }

	static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
    
    static function get_class_name()
    {
    	return self :: CLASS_NAME;
    }
}

?>