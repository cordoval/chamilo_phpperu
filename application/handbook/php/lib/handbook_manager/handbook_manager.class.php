<?php
namespace application\handbook;

use common\libraries\ObjectTable;
use common\libraries\WebApplication;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ObjectTableOrder;

use group\Group;
use group\GroupDataManager;

use application\context_linker\ContextLinkerDataManager;
use application\context_linker\ContextLink;
use application\context_linker\ContextLinkerManager;

use application\metadata\MetadataManager;
use application\metadata\MetadataPropertyValue;

use repository\RepositoryDataManager;
use repository\ContentObjectDisplay;
use repository\content_object\handbook\Handbook;
use repository\content_object\document\Document;
use repository\content_object\youtube\Youtube;
use repository\content_object\link\Link;
use repository\content_object\wiki_page\WikiPage;
use repository\content_object\handbook_item\HandbookItem;
use repository\content_object\handbook_topic\HandbookTopic;
use repository\ContentObject;
use common\libraries\Request;
use repository\ComplexContentObjectItem;
use repository\content_object\glossary\Glossary;
use application\metadata\MetadataPropertyType;
use common\libraries\ArrayResultSet;
use common\libraries\SubselectCondition;
use common\libraries\InCondition;

require_once dirname(__FILE__) . '/../handbook_data_manager.class.php';
require_once dirname(__FILE__) . '/component/handbook_publication_browser/handbook_publication_browser_table.class.php';

/**
 * A handbook manager
 * @author Nathalie Blocry
 */
class HandbookManager extends WebApplication
{
    const APPLICATION_NAME = 'handbook';

    const PARAM_HANDBOOK_PUBLICATION = 'handbook_publication';
    const PARAM_DELETE_SELECTED_HANDBOOK_PUBLICATIONS = 'delete_selected_handbook_publications';
    const PARAM_HANDBOOK_PUBLICATION_ID = 'hpid';
    const PARAM_HANDBOOK_ID = 'hid';
    const PARAM_HANDBOOK_SELECTION_ID = 'hsid';
    const PARAM_TOP_HANDBOOK_ID = 'thid';
    const PARAM_SELECTION_TO_EDIT = 'ste';
    const PARAM_HANDBOOK_OWNER_ID = 'handbook_owner';
    const PARAM_SEARCH_QUERY = 'sq';

    const ACTION_DELETE_HANDBOOK_PUBLICATION = 'handbook_publication_deleter';
    const ACTION_EDIT_HANDBOOK_PUBLICATION = 'handbook_publication_editor';
    const ACTION_CREATE_HANDBOOK_PUBLICATION = 'handbook_publication_creator';
    const ACTION_BROWSE_HANDBOOK_PUBLICATIONS = 'handbook_publications_browser';
    const ACTION_VIEW_HANDBOOK = 'handbook_full_viewer';
    const ACTION_VIEW_FULL_HANDBOOK = 'handbook_full_viewer';
    const ACTION_VIEW_LIGHT_HANDBOOK = 'handbook_light_viewer';
    const ACTION_EDIT_RIGHTS = 'rights_editor';
    const ACTION_VIEW_PREFERENCES = 'handbook_preferences_viewer';
    const ACTION_VIEW_HANDBOOK_PUBLICATION = 'handbook_publications_browser';
    const ACTION_TOPIC_PICKER = 'topic_picker';
    const ACTION_EDIT_ITEM = 'handbook_item_editor';
    const ACTION_PICK_ITEM_TO_EDIT = 'handbook_item_editor_picker';
    const ACTION_CREATE_HANDBOOK_ITEM = 'handbook_item_creator';
    const ACTION_VIEW_GLOSSARY = 'handbook_glossary_viewer';
    const ACTION_CONVERT_WIKI = 'handbook_wiki_convertor';
    const ACTION_BROWSE_BOOKMARKS = 'bookmarks_browser';
    const ACTION_CREATE_BOOKMARK = 'bookmarks_creator';
    const ACTION_DELETE_BOOKMARK = 'bookmarks_deleter';
    const ACTION_SEARCH = 'search_results_browser';
    const ACTION_CREATE_PREFERENCE = 'handbook_preferences_creator';
    const ACTION_VIEW_COLLAPSED = 'toggle_menu';
    const ACTION_CREATE_ODF = 'odf_creator';

    const PARAM_COMPLEX_OBJECT_ID = 'coid';
    const PARAM_LIGHT_MODE = 'light';
    const PARAM_LANGUAGE = 'dc:Language';
    const PARAM_PUBLISHER = 'dc:publisher';

    const PARAM_MENU_STYLE = 'menu_style';
    const MENU_COMPACT = 0;
    const MENU_OPEN = 1;

    const DEFAULT_ACTION = self :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS;

    const ACTION_BROWSE = 'browse';

    static $language_metadata_properties = array('dc:Language', 'dc:language');
    static $publisher_metadata_properties = array('dc:publisher, dc:Publisher');

    static $found_glossaries = array();

    /**
     * Constructor
     * @param User $user The current user
     */
    function __construct($user = null)
    {
        parent :: __construct($user);

    }

    static function get_language_metadata_properties()
    {
        return self::$language_metadata_properties;
    }
    static function get_first_language_metadata_property()
    {
        return self::$language_metadata_properties[0];
    }
    static function get_publisher_metadata_properties()
    {
        return self::$publisher_metadata_properties;
    }
    static function get_first_publisher_metadata_property()
    {
        return self::$publisher_metadata_properties[0];
    }

    function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            switch ($_POST['action'])
            {
                case self :: PARAM_DELETE_SELECTED_HANDBOOK_PUBLICATIONS :

                    $selected_ids = $_POST[HandbookPublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

                    if (empty($selected_ids))
                    {
                        $selected_ids = array();
                    }
                    elseif (! is_array($selected_ids))
                    {
                        $selected_ids = array($selected_ids);
                    }

                    $this->set_action(self :: ACTION_DELETE_HANDBOOK_PUBLICATION);
                    $_GET[self :: PARAM_HANDBOOK_PUBLICATION] = $selected_ids;
                    break;
            }

        }
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    // Data Retrieving


    function count_handbook_publications($condition)
    {
        return HandbookDataManager :: get_instance()->count_handbook_publications($condition);
    }

    function retrieve_handbook_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return HandbookDataManager :: get_instance()->retrieve_handbook_publications($condition, $offset, $count, $order_property);
    }

    function retrieve_handbook_publication($id)
    {
        return HandbookDataManager :: get_instance()->retrieve_handbook_publication($id);
    }

    // Url Creation


    function get_create_handbook_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_HANDBOOK_PUBLICATION));
    }

    function get_update_handbook_publication_url($handbook_publication)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_EDIT_HANDBOOK_PUBLICATION,
                self :: PARAM_HANDBOOK_PUBLICATION => $handbook_publication->get_id()));
    }

    function get_delete_handbook_publication_url($handbook_publication_id)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_DELETE_HANDBOOK_PUBLICATION,
                self :: PARAM_HANDBOOK_PUBLICATION => $handbook_publication_id));
    }
  
    function get_browse_handbook_publications_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS));
    }

    function get_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    function get_view_handbook_publication_url($handbook_id, $handbook_publication_id = null)
    {
        if($handbook_publication_id == null)
        {
            $hdm = HandbookDataManager :: get_instance();
            $condition = new EqualityCondition(HandbookPublication :: PROPERTY_CONTENT_OBJECT_ID, $handbook_id);
            $publications = $hdm->retrieve_handbook_publications($condition);
            if (count($publications == 1))
            {
                $handbook_publication_id = $publications->next_result()->get_id();
            }
        }
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_HANDBOOK,
                self :: PARAM_TOP_HANDBOOK_ID => $handbook_id,
                self :: PARAM_HANDBOOK_ID => $handbook_id,
                self :: PARAM_HANDBOOK_PUBLICATION_ID => $handbook_publication_id));
    }

    function get_view_handbook_url($handbook_id)
    {
        return $this->get_url(array(
                self :: PARAM_ACTION => self :: ACTION_VIEW_HANDBOOK,
                self :: PARAM_HANDBOOK_ID => $handbook_id));
    }

    function get_edit_handbook_item_url($selection_to_edit, $thid, $hsid, $hpid, $coid, $hid)
    {
        return $this->get_url(array(
                HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_EDIT_ITEM,
                HandbookManager::PARAM_HANDBOOK_SELECTION_ID => $hsid,
                HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID => $hpid,
                HandbookManager::PARAM_TOP_HANDBOOK_ID => $thid,
                HandbookManager::PARAM_HANDBOOK_ID => $hid,
                HandbookManager::PARAM_SELECTION_TO_EDIT => $selection_to_edit,
                HandbookManager::PARAM_COMPLEX_OBJECT_ID =>$coid));

    }

    static function translate_chamilo_language_to_iso_code($language)
    {
        //should probably be put somewhere else (stored in db?) so one can easily add new languages
        //for testing the app this should do
        $iso_639_code;
        switch ($language)
        {
            case 'english' :
                $iso_639_code = 'en';
                break;
            case 'dutch' :
                $iso_639_code = 'nl';
                break;
            case 'french' :
                $iso_639_code = 'fr';
                break;
            case 'german' :
                $iso_639_code = 'de';
                break;
            case 'spanish' :
                $iso_639_code = 'es';
                break;
            default :
                $iso_639_code = $language;
                break;
        }
        return $iso_639_code;
    }

    /**
     * returns an array with all the alternatives for a content object
     * without the original object!
     * @param <type> $co_id
     * @return <type> array
     */
    static function get_alternative_items($co_id)
    {
        //GET ITEM ALTERNATIVES
        $cldm = ContextLinkerDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();
        $condition = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $co_id);
        $context_links = $cldm->retrieve_full_context_links_recursive($co_id, null, null, null, ContextLinkerManager::ARRAY_TYPE_FLAT);

        $rdm = RepositoryDataManager :: get_instance();   
             
        return $context_links;
    }

    /**
     * return a resultset with information on all the alternatives of a content-object
     * (including the original object!)
     * information includes:
     * [alt_title] = the title of the alternative
     * [org_title] = the title of the original co
     * [alt_type] = the co-type of the alternative
     * [ns_prefix][ns_name][value] = the namespace, property name and value of the metadata on wich was linked
     * [alt_id] = the id of the alternative co
     * @return ArrayResultSet with all the alternatives
     */
    static function get_resultset_with_original_and_alternatives($co_id)
    {
        //TODO: this should probably be moved to the contextlinker

        //get all alternatives for this item
        $alternatives_array = HandbookManager::get_alternative_items($co_id);


        //add original to array
        //TODO: get actual data
        $original['alt_' . ContentObject :: PROPERTY_TITLE] = 'orig';
        $original['orig_' . ContentObject :: PROPERTY_TITLE] = 'orig';
        $original['alt_' . ContentObject :: PROPERTY_TYPE] = 'orig';
        $original[MetadataPropertyType :: PROPERTY_NS_PREFIX] = 'orig';
        $original[MetadataPropertyType :: PROPERTY_NAME] = 'orig';
        $original[MetadataPropertyValue :: PROPERTY_VALUE] = 'orig';
        $original['alt_id'] = $co_id;
        $alternatives_array[] = $original;


        if ($alternatives_array != false && (count($alternatives_array) > 0))
        {
            return new ArrayResultSet($alternatives_array);
        }
        else
        {
            return null;
        }
    }

    /**
     * returns the ids of all the alternative versions of a content object
     * including the id of the original co
     * @param <type> $co_id : id of the original content object
     * @return <type> array of id's
     */
    static function get_all_alternative_ids($co_id)
    {

        //TODO: this should probably be moved to the context linker
        $alternatives_array = HandbookManager::get_alternative_items($co_id);

        $ids=array();

        foreach ($alternatives_array as $alternative)
        {
            $ids[] = $alternative['alt_id'];
        }
        $ids[] = $co_id;

        return $ids;

    }


    /**
     * return the ids of all the handbook topics in a handbook and their text alternatives
     * @param <type> $handbook_id = one handbook id or an array of handbook id's
     */
    static function get_all_text_items_of_handbook($handbook_id)
    {
        if(!is_array($handbook_id))
        {
            $handbook_id = array($handbook_id);
        }
        $top_level = self::get_handbook_children_ids($handbook_id);
        
        $all_alternatives = array();
        foreach($top_level as $orig_id)
        {
            $all_alternatives = array_merge($all_alternatives, self::get_all_alternative_ids($orig_id));
        }

        return $all_alternatives;

    }


    static function get_handbook_children_ids($ids_to_check, $recursive_array = array())
    {
        if(!is_array($ids_to_check))
        {
            $ids_to_check = array($ids_to_check);
        }
        $condition = new InCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $ids_to_check);
        $rdm = RepositoryDataManager :: get_instance();
        $object_set = $rdm->retrieve_objects(ComplexContentObjectItem :: get_table_name(), $condition, null, null, null, ComplexContentObjectItem :: CLASS_NAME);
        $recursive_array = array_merge($ids_to_check, $recursive_array);
        $new_ids_to_check = array();
        while($complex_child = $object_set->next_result())
        {
            $child_id = $complex_child->get_ref();
            if(!in_array($child_id , $recursive_array))
            {                
                $child = $rdm->retrieve_content_object($child_id);
                if($child->get_type() == Handbook::get_type_name())
                {
                 //is child a handbook -> get children
                    $new_ids_to_check[] = $child->get_id();
                }
                else if($child->get_type() == HandbookItem::get_type_name())
                {
                    //is child a handbook_item --> get co and add to array
                    $recursive_array[] = $child->get_reference();
                }
                else
                {
                    //else we have a problem as a handbook kan only contain handbooks and
                    //handbook_items
                    var_dump('oops');
                }
            }
        }
        if(count($new_ids_to_check)>0)
        {
            //the handbooks had children that have so perform a recursive check
//            $recursive_array = \array_merge($recursive_array ,self::get_handbook_children_ids($new_ids_to_check, $recursive_array));
         $recursive_array = self::get_handbook_children_ids($new_ids_to_check, $recursive_array);

        }
        return $recursive_array;
    }

    static function get_alternatives_preferences_types($co_id, $publication_id)
    {
        $context_links_resultset = self::get_alternative_items($co_id);

        //GET ITEM
        $rdm = RepositoryDataManager :: get_instance();

        $selected_object = $rdm->retrieve_content_object($co_id);
        if ($selected_object && $selected_object->get_type() == HandbookItem :: get_type_name())
        {
            $selected_object = $rdm->retrieve_content_object($selected_object->get_reference());
        }

        $texts = array();
        $images = array();
        $videos = array();
        $links = array();
        $others = array();
        $handbooks = array();
        $rdm = RepositoryDataManager :: get_instance();


        while ($context_links_resultset != false && (count($context_links_resultset) > 0) && list($key, $item) = each($context_links_resultset))
        {

            $alternative_co = $rdm->retrieve_content_object($item[ContextLinkerManager :: PROPERTY_ALT_ID]);

            if ($alternative_co)
            {
                $display = ContentObjectDisplay :: factory($alternative_co);
                if ($alternative_co->get_type() == Handbook :: get_type_name())
                {
                    $handbooks[] = $alternative_co;
                }
                else
                    if ($alternative_co->get_type() == HandbookTopic :: get_type_name())
                    {

                        $texts[] = $alternative_co;
                    }
                    else
                        if ($alternative_co->get_type() == Document :: get_type_name())
                        {
                            if ($alternative_co->is_image())
                            {
                                $images[] = $alternative_co;
                            }
                            else
                                if ($alternative_co->is_flash() || $alternative_co->is_video() || $alternative_co->is_audio())
                                {
                                    $videos[] = $alternative_co;
                                }
                                else
                                    if ($alternative_co->is_showable())
                                    {
                                        $others[] = $alternative_co;
                                    }
                                    else
                                    {
                                        $others[] = $alternative_co;
                                    }
                        }
                        else
                            if ($alternative_co->get_type() == Youtube :: get_type_name())
                            {
                                $videos[] = $alternative_co;
                            }
                            else
                                if ($alternative_co->get_type() == Link :: get_type_name())
                                {
                                    $links[] = $alternative_co;
                                }
                                else
                                    if ($alternative_co->get_type() == WikiPage :: get_type_name())
                                    {
                                        $texts[] = $alternative_co;
                                    }
                                    else
                                    {
                                        $others[] = $alternative_co;
                                    }
            }
        }

        if ($selected_object->get_type() == Handbook :: get_type_name())
        {
            $handbooks[] = $selected_object;
        }
        else
            if ($selected_object->get_type() == HandbookTopic :: get_type_name())
            {

                $texts[] = $selected_object;
            }
            else
                if ($selected_object->get_type() == Document :: get_type_name())
                {
                    if ($selected_object->is_image())
                    {
                        $images[] = $alternative_co;
                    }
                    else
                        if ($selected_object->is_flash() || $selected_object->is_video() || $selected_object->is_audio())
                        {
                            $videos[] = $alternative_co;
                        }
                        else
                        {
                            $condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID, $selected_object->get_id());
                            $metadata_property_values = MetadataManager :: retrieve_metadata_property_values($condition);

                            $metadata_array = array();

                            while ($metadata = $metadata_property_values->next_result())
                            {
                                $metadata_array[$metadata->get_property_type_id()] = $metadata->get_value();
                            }
                            $texts[] = $selected_object;
                        }
                }
                else
                    if ($selected_object->get_type() == Youtube :: get_type_name())
                    {
                        $videos[] = $selected_object;
                    }
                    else
                        if ($selected_object->get_type() == Link :: get_type_name())
                        {
                            $links[] = $selected_object;
                        }
                        else
                            if ($selected_object->get_type() == WikiPage :: get_type_name())
                            {
                                $texts[] = $selected_object;
                            }
                            else
                            {
                                $others[] = $selected_object;
                            }
        //                 $alternatives['text'] = $texts;
        //                 $alternatives['image'] = $images;
        //                 $alternatives['video'] = $videos;
        $alternatives['other'] = $others;
        $alternatives['link'] = $links;
        //                 $alternatives['handbook'] = $handbooks;


        //GET USER & PUBLICATION PREFERENCES
        $preferences = self :: get_preferences($publication_id);

        //determine wich preferences are more important:
        $preference_importance = self :: get_publication_preferences_importance($publication_id);

        //TEXT
        if (count($texts > 0))
        {
            $alternatives_text = self :: determine_most_suitable_co($texts, 'text', $preference_importance, $preferences);
            $alternatives = $alternatives + $alternatives_text;
        }
        else
        {
            $alternatives['text'] = $texts;
        }

        //IMAGE
        if (count($images > 0))
        {
            $alternatives_image = self :: determine_most_suitable_co($images, 'image', $preference_importance, $preferences);
            $alternatives = $alternatives + $alternatives_image;
        }
        else
        {
            $alternatives['image'] = $images;
        }

        //VIDEO
        if (count($videos > 0))
        {
            $alternatives_video = self :: determine_most_suitable_co($videos, 'video', $preference_importance, $preferences);
            $alternatives = $alternatives + $alternatives_video;
        }
        else
        {
            $alternatives['video'] = $videos;
        }

        //HANDBOOK
        if (count($videos > 0))
        {
            $alternatives_handbook = self :: determine_most_suitable_co($handbooks, 'handbook', $preference_importance, $preferences);
            $alternatives = $alternatives + $alternatives_handbook;
        }
        else
        {
            $alternatives['handbook'] = $handbooks;
        }


        return $alternatives;
    }

    static function determine_most_suitable_co($co_array, $label, $preference_importance, $preferences)
    {
        $alternatives = array();
        $alternatives[$label] = array();
        $best_found_flag = false;
        $array_to_check = array();
        $array_to_check = $co_array;
        $i = 1;
        $x = count($preference_importance);

        while ($best_found_flag == false && $i <= $x)
        {
            if (array_key_exists(key($preference_importance[$i]), $preferences) && array_key_exists($preference_importance[$i][key($preference_importance[$i])], $preferences[key($preference_importance[$i])]))
            {
                //if a value for this preference has been set
                $metadata_to_check = $preference_importance[$i][key($preference_importance[$i])];
                $preference_value_to_check = $preferences[key($preference_importance[$i])][$preference_importance[$i][key($preference_importance[$i])]];
                $candidates = self :: determine_most_suitable_candidates($array_to_check, $metadata_to_check, $preference_value_to_check);

                //are there one or more good candidates, based on preference?
                if (count($candidates['yes']) > 0)
                {
                    if (count($candidates['yes']) == 1)
                    {
                        //1 most suitable co found, stop looking
                        $best_found_flag = true;
                        $alternatives[$label . '_main'] = current($candidates['yes']);
                        $array_to_check = $candidates['no'];

                    }
                    else
                    { //multiple suitable co's found, keep looking
                        $array_to_check = $candidates['yes'];
                        $alternatives[$label] = array_merge($alternatives[$label], $candidates['no']);
                    }

                }
                else
                {
                    //no suitable co's found for this preference/value
                    $array_to_check = $candidates['no'];
                }

            }
            //check next preference
            $i ++;
        }

        if ($best_found_flag == false)
        {
            //no "most suitable" found by metadata & preferences: pick first one as most suitable
            $alternatives[$label . '_main'] = current($array_to_check);
            unset($array_to_check[key($array_to_check)]);

        }
        $alternatives[$label] = array_merge($alternatives[$label], $array_to_check);

        return $alternatives;

    }

    static function determine_most_suitable_candidates($candidates_array, $metadata_property, $metadata_property_value)
    {
        $meets_criteria = array();
        $doesnt_meet_criteria = array();

        if(!\is_array($metadata_property))
        {
            $metadata_property = array($metadata_property);
        }

        while (list($key, $co) = each($candidates_array))
        {
            //get metadata for co
            $metadata = MetadataManager :: retrieve_metadata_for_content_object($co->get_id());
            foreach($metadata_property as $prop)
            {
                if (array_key_exists($prop, $metadata) && $metadata[$prop] == $metadata_property_value)
                {
                    $meets_criteria[] = $co;

                }
                else
                {
                    $doesnt_meet_criteria[] = $co;
                }
            }
        }

        $result = array();

        $result['yes'] = $meets_criteria;
        $result['no'] = array_merge($doesnt_meet_criteria);
        return $result;

    }

    static function get_preferences($handbook_publication_id)
    {
        //USER PREFERENCES
        //prefered language = chosen platform language
        $user_preferences[self :: get_first_language_metadata_property()] = Translation :: get_instance()->get_language();

        //for now: get institution name from root group
        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
        $group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_NAME))->next_result();
        $user_preferences[self :: get_first_publisher_metadata_property()] = $group->get_name();
        //TODO: load other metadata for user & user's groups

        //HANDBOOK PREFERENCES
        //TODO: this should be gotten from a handbook-publication-preferences table
        $handbook_preferences = self::get_publication_preferences($pid);


        //SYSTEM PREFERENCES
        //TODO get default platform language & move publisher here (is system default, not user-specific anyway!)

        $preferences = array();
        $preferences['user'] = $user_preferences;
        $preferences['handbook'] = $handbook_preferences;

        return $preferences;
    }

    /**
     * get all the preferences set for a specific publication
     * @param <type> $pub_id : the id of the publication
     */
    static function get_publication_preferences($pub_id = null)
    {
        //TODO: implement!
        return array();
    }

    function get_publication_preferences_importance($publication_id)
    {
        //TODO: this should not be hardcoded?!
        //determinable per handbook or general system admin setting?
        //at least the other preferences should be put in the array at random

        $preference_importance = array();
        $preference_importance[1] = array('user' => self :: PARAM_LANGUAGE);
        $preference_importance[2] = array('handbook' => self :: PARAM_LANGUAGE);
        $preference_importance[3] = array('user' => self :: PARAM_PUBLISHER);
        $preference_importance[4] = array('handbook' => self :: PARAM_PUBLISHER);

        return $preference_importance;
    }

    function get_create_handbook_item_url($handbook_id, $top_handbook_id, $publication_id)
    {
         return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_HANDBOOK_ITEM, self :: PARAM_HANDBOOK_ID => $handbook_id, self::PARAM_HANDBOOK_PUBLICATION => $publication_id, self::PARAM_TOP_HANDBOOK_ID => $top_handbook_id));
    }

    function get_convert_wiki_to_handbook_item_url($handbook_id, $top_handbook_id, $publication_id, $selection_id)
    {
         return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CONVERT_WIKI, self :: PARAM_HANDBOOK_ID => $handbook_id, self::PARAM_HANDBOOK_PUBLICATION => $publication_id, self::PARAM_TOP_HANDBOOK_ID => $top_handbook_id, self::PARAM_HANDBOOK_SELECTION_ID => $selection_id));
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * return an array with the id's of all the glossaries in a handbook
     * with their language
     * @param <type> $handbook_id
     */
    static function retrieve_all_glossaries($handbook_id)
    {

        //TODO: re-think this glossary thing: how to handle different languages,
        //glossaries in alternative handbooks, ...

        //find the glossaries in this handbook
        $glossaries = self::find_glossaries_ids_only($handbook_id);

        //find the alternatives for these glossaries

        //find the glossaries in this handbook's alternatives

        //find the alternatives for these glossaries

        //get the languages for all the glossaries

        return $glossaries;
    }

    static function find_glossaries_ids_only($handbook_id)
    {
        $glossaries_array = array();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $handbook_id, ComplexContentObjectItem :: get_table_name());
        $datamanager = RepositoryDataManager::get_instance();
        $clois = $datamanager->retrieve_complex_content_object_items($condition);

        while ($cloi = $clois->next_result())
        {
            $lo = $datamanager->retrieve_content_object($cloi->get_ref());
            
            if ($lo->get_type() == HandbookItem::get_type_name())
            {
                $lo = $datamanager->retrieve_content_object($lo->get_reference());
            }
            if ($lo->get_type() == Glossary::get_type_name())
            {
                $glossaries_array[] = $lo->get_id();
            }
        }
        return $glossaries_array;

    }

    

}
?>