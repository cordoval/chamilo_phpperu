<?php
namespace application\package;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\DataClass;
use common\libraries\NotCondition;
use common\libraries\EqualityCondition;
/**
 * This class describes a Package data object
 *
 * $Id: remote_package.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib
 * @author Hans De Bisschop
 */
class Package extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * Package properties
     */
    const PROPERTY_CODE = 'code';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SECTION = 'section';
    const PROPERTY_CATEGORY = 'category';
//    const PROPERTY_AUTHORS = 'authors';
    const PROPERTY_VERSION = 'version';
    //    const PROPERTY_CYCLE = 'cycle';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_SIZE = 'size';
    const PROPERTY_MD5 = 'md5';
    const PROPERTY_SHA1 = 'sha1';
    const PROPERTY_SHA256 = 'sha256';
    const PROPERTY_SHA512 = 'sha512';
    const PROPERTY_TAGLINE = 'tagline';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_HOMEPAGE = 'homepage';
//    const PROPERTY_DEPENDENCIES = 'dependencies';
    const PROPERTY_EXTRA = 'extra';
    const PROPERTY_STATUS = 'status';
    
    // Sub-properties
    const PROPERTY_CYCLE_PHASE = 'cycle_phase';
    const PROPERTY_CYCLE_REALM = 'cycle_realm';
    
    // Release phases
    const PHASE_ALPHA = 'alpha';
    const PHASE_BETA = 'beta';
    const PHASE_RELEASE_CANDIDATE = 'release_candidate';
    const PHASE_GENERAL_AVAILABILITY = 'general_availability';
    
    // Release realm
    const REALM_MAIN = 'main';
    const REALM_UNIVERSE = 'universe';
    
    //status
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CODE, 
                self :: PROPERTY_NAME, 
                self :: PROPERTY_SECTION, 
                self :: PROPERTY_CATEGORY, 
//                self :: PROPERTY_AUTHORS, 
                self :: PROPERTY_VERSION, 
                self :: PROPERTY_FILENAME, 
                self :: PROPERTY_SIZE, 
                self :: PROPERTY_MD5, 
                self :: PROPERTY_SHA1, 
                self :: PROPERTY_SHA256, 
                self :: PROPERTY_SHA512, 
                self :: PROPERTY_TAGLINE, 
                self :: PROPERTY_DESCRIPTION, 
                self :: PROPERTY_HOMEPAGE, 
//                self :: PROPERTY_DEPENDENCIES, 
                self :: PROPERTY_EXTRA, 
                self :: PROPERTY_CYCLE_PHASE, 
                self :: PROPERTY_CYCLE_REALM,
                self :: PROPERTY_STATUS));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PackageDataManager :: get_instance();
    }

    /**
     * Returns the code of this Package.
     * @return the code.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Sets the code of this Package.
     * @param code
     */
    function set_code($code)
    {
        $this->set_default_property(self :: PROPERTY_CODE, $code);
    }

    /**
     * Returns the name of this Package.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this Package.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the section of this Package.
     * @return the section.
     */
    function get_section()
    {
        return $this->get_default_property(self :: PROPERTY_SECTION);
    }

    /**
     * Sets the section of this Package.
     * @param section
     */
    function set_section($section)
    {
        $this->set_default_property(self :: PROPERTY_SECTION, $section);
    }

    /**
     * Returns the category of this Package.
     * @return the category.
     */
    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Sets the category of this Package.
     * @param category
     */
    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    /**
     * Returns the authors of this Package.
     * @return the authors.
     */
    function get_authors()
    {
        return unserialize($this->get_default_property(self :: PROPERTY_AUTHORS));
    }

//    /**
//     * Sets the authors of this Package.
//     * @param authors
//     */
//    function set_authors($authors)
//    {
//        $this->set_default_property(self :: PROPERTY_AUTHORS, serialize($authors));
//    }

    /**
     * Returns the version of this Package.
     * @return the version.
     */
    function get_version()
    {
        return $this->get_default_property(self :: PROPERTY_VERSION);
    }

    /**
     * Sets the version of this Package.
     * @param version
     */
    function set_version($version)
    {
        $this->set_default_property(self :: PROPERTY_VERSION, $version);
    }

    //    /**
    //     * Returns the cycle of this Package.
    //     * @return the cycle.
    //     */
    //    function get_cycle()
    //    {
    //        return unserialize($this->get_default_property(self :: PROPERTY_CYCLE));
    //    }
    //
    //    /**
    //     * Sets the cycle of this Package.
    //     * @param cycle
    //     */
    //    function set_cycle($cycle)
    //    {
    //        $this->set_default_property(self :: PROPERTY_CYCLE, serialize($cycle));
    //    }
    

    /**
     * Returns the cycle phase of this Package.
     * @return the cycle phase.
     */
    function get_cycle_phase()
    {
        return $this->get_default_property(self :: PROPERTY_CYCLE_PHASE);
    }

    function set_cycle_phase($phase_cycle)
    {
        $this->set_default_property(self :: PROPERTY_CYCLE_PHASE, $phase_cycle);
    }

    /**
     * Returns the cycle realm of this Package.
     * @return the cycle realm.
     */
    function get_cycle_realm()
    {
        $cycle = $this->get_cycle();
        return $cycle[self :: PROPERTY_CYCLE_REALM];
    }

    static function get_phases()
    {
        return array(self :: PHASE_ALPHA => Translation :: get(self :: PHASE_ALPHA), 
                self :: PHASE_BETA => Translation :: get(self :: PHASE_BETA), 
                self :: PHASE_GENERAL_AVAILABILITY => Translation :: get(self :: PHASE_GENERAL_AVAILABILITY), 
                self :: PHASE_RELEASE_CANDIDATE => Translation :: get(self :: PHASE_RELEASE_CANDIDATE));
    }

    //    static function convert_phase_to_string($phase)
    //    {
    //        switch ($phase)
    //        {
    //            case '1' :
    //                return "general_availability";
    //                break;
    //            case '2' :
    //                return "alpha";
    //                break;
    //            case '3' :
    //                return "beta";
    //                break;
    //            case '4' :
    //                return "release_candidate";
    //                break;
    //        }
    //    }
    //
    //    function get_phase_string()
    //    {
    //        return self :: convert_phase_to_string($this->get_cycle_phase());
    //    }
    

    /**
     * Returns the filename of this Package.
     * @return the filename.
     */
    function get_filename()
    {
        return $this->get_default_property(self :: PROPERTY_FILENAME);
    }

    /**
     * Sets the filename of this Package.
     * @param filename
     */
    function set_filename($filename)
    {
        $this->set_default_property(self :: PROPERTY_FILENAME, $filename);
    }

    /**
     * Returns the size of this Package.
     * @return the size.
     */
    function get_size()
    {
        return $this->get_default_property(self :: PROPERTY_SIZE);
    }

    /**
     * Sets the size of this Package.
     * @param size
     */
    function set_size($size)
    {
        $this->set_default_property(self :: PROPERTY_SIZE, $size);
    }

    /**
     * Returns the md5 of this Package.
     * @return the md5.
     */
    function get_md5()
    {
        return $this->get_default_property(self :: PROPERTY_MD5);
    }

    /**
     * Sets the md5 of this Package.
     * @param md5
     */
    function set_md5($md5)
    {
        $this->set_default_property(self :: PROPERTY_MD5, $md5);
    }

    /**
     * Returns the sha1 of this Package.
     * @return the sha1.
     */
    function get_sha1()
    {
        return $this->get_default_property(self :: PROPERTY_SHA1);
    }

    /**
     * Sets the sha1 of this Package.
     * @param sha1
     */
    function set_sha1($sha1)
    {
        $this->set_default_property(self :: PROPERTY_SHA1, $sha1);
    }

    /**
     * Returns the sha256 of this Package.
     * @return the sha256.
     */
    function get_sha256()
    {
        return $this->get_default_property(self :: PROPERTY_SHA256);
    }

    /**
     * Sets the sha256 of this Package.
     * @param sha256
     */
    function set_sha256($sha256)
    {
        $this->set_default_property(self :: PROPERTY_SHA256, $sha256);
    }

    /**
     * Returns the sha512 of this Package.
     * @return the sha512.
     */
    function get_sha512()
    {
        return $this->get_default_property(self :: PROPERTY_SHA512);
    }

    /**
     * Sets the sha512 of this Package.
     * @param sha512
     */
    function set_sha512($sha512)
    {
        $this->set_default_property(self :: PROPERTY_SHA512, $sha512);
    }

    /**
     * Returns the tagline of this Package.
     * @return the tagline.
     */
    function get_tagline()
    {
        return $this->get_default_property(self :: PROPERTY_TAGLINE);
    }

    /**
     * Sets the tagline of this Package.
     * @param tagline
     */
    function set_tagline($tagline)
    {
        $this->set_default_property(self :: PROPERTY_TAGLINE, $tagline);
    }

    /**
     * Returns the description of this Package.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Package.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the extras of this Package.
     * @return the extras.
     */
    function get_extra()
    {
        return unserialize($this->get_default_property(self :: PROPERTY_EXTRA));
    }

    /**
     * Sets the extras of this Package.
     * @param extras
     */
    function set_extra($extra)
    {
        $this->set_default_property(self :: PROPERTY_EXTRA, serialize($extra));
    }

    /**
     * Returns the status of this Package.
     * @return the status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Sets the status of this Package.
     * @param status
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_status_string()
    {
        switch ($this->get_status())
        {
            case 1 :
                return Translation :: get('Pending');
                break;
            case 2 :
                return Translation :: get('Accepted');
                break;
            case 3 :
                return Translation :: get('Rejected');
                break;
        }
    }

    /**
     * Returns the homepage of this Package.
     * @return the homepage.
     */
    function get_homepage()
    {
        return $this->get_default_property(self :: PROPERTY_HOMEPAGE);
    }

    /**
     * Sets the homepage of this Package.
     * @param homepage
     */
    function set_homepage($homepage)
    {
        $this->set_default_property(self :: PROPERTY_HOMEPAGE, $homepage);
    }

    /**
     * Returns the dependencies of this Package.
     * @return the dependencies.
     */
    function get_dependencies()
    {
        return unserialize($this->get_default_property(self :: PROPERTY_DEPENDENCIES));
    }

//    /**
//     * Sets the dependencies of this Package.
//     * @param dependencies
//     */
//    function set_dependencies($dependencies)
//    {
//        $this->set_default_property(self :: PROPERTY_DEPENDENCIES, serialize($dependencies));
//    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function is_official()
    {
        return $this->get_cycle_realm() == self :: REALM_MAIN;
    }

    function is_stable()
    {
        return $this->get_cycle_phase() == self :: PHASE_GENERAL_AVAILABILITY;
    }

    function create()
    {
        $dm = $this->get_data_manager();
        
        $packages = $dm->retrieve_packages();
        //		while($package = $packages->next_result())
        //			if($packages->get_english_name() == $this->get_english_name())
        //				return false;
        

        $succes = parent :: create();
        
        //		$variables = $dm->retrieve_variables();
        //
        //		while($variable = $variables->next_result())
        //		{
        //			$translation = new VariableTranslation();
        //			$translation->set_user_id(0);
        //			$translation->set_language_id($this->get_id());
        //			$translation->set_variable_id($variable->get_id());
        //			$translation->set_date(time());
        //			$translation->set_rated(0);
        //			$translation->set_rating(0);
        //			$translation->set_translation(' ');
        //			$translation->set_status(VariableTranslation :: STATUS_NORMAL);
        //			if (! $translation->create())
        //			{
        //				return false;
        //			}
        //		}
        

        //	    $parent = PRights :: get_languages_subtree_root_id();
        

        //		if(!CdaRights :: create_location_in_languages_subtree($this->get_english_name(), CdaRights :: TYPE_LANGUAGE, $this->get_id(), $parent))
        //		{
        //			return false;
        //		}
        

        return $succes;
    }

    function update()
    {
        $dm = $this->get_data_manager();
        
        $condition = new NotCondition(new EqualityCondition(Package :: PROPERTY_ID, $this->get_id()));
        $packages = $dm->retrieve_packages($condition);
        //		while($package = $packages->next_result())
        //			if($packages->get_english_name() == $this->get_english_name())
        //				return false;
        

        return parent :: update();
    }

    function delete()
    {
        $succes = parent :: delete();
        $dm = $this->get_data_manager();
        
        //		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $this->get_id());
        //		$translations = $dm->retrieve_variable_translations($condition);
        //
        //		while($translation = $translations->next_result())
        //		{
        //			$succes &= $translation->delete();
        //		}
        

        return $succes;
    
    }

}

?>