<?php
namespace application\handbook;
use common\libraries\WebApplication;
use common\libraries\EqualityCondition;
use common\libraries\Translation;
use common\libraries\Utilities;
use group\Group;
use group\GroupDataManager;
use common\libraries\ObjectTableOrder;
use application\context_linker\ContextLinkerDataManager;
use repository\RepositoryDataManager;
use application\context_linker\ContextLink;
use repository\ContentObjectDisplay;
use repository\content_object\handbook\Handbook;
use repository\content_object\document\Document;
use repository\content_object\youtube\Youtube;
use repository\content_object\link\Link;
use repository\content_object\wiki_page\WikiPage;
use application\metadata\MetadataManager;
use repository\content_object\handbook_item\HandbookItem;
use application\metadata\MetadataPropertyValue;
use repository\content_object\handbook_topic\HandbookTopic;
use repository\ContentObject;
use application\context_linker\ContextLinkerManager;


require_once dirname(__FILE__).'/../handbook_data_manager.class.php';
require_once dirname(__FILE__).'/component/handbook_publication_browser/handbook_publication_browser_table.class.php';

/**
 * A handbook manager
 *
 * @author Sven Vanpoucke
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

	const ACTION_DELETE_HANDBOOK_PUBLICATION = 'handbook_publication_deleter';
	const ACTION_EDIT_HANDBOOK_PUBLICATION = 'handbook_publication_editor';
	const ACTION_CREATE_HANDBOOK_PUBLICATION = 'handbook_publication_creator';
	const ACTION_BROWSE_HANDBOOK_PUBLICATIONS = 'handbook_publications_browser';
        const ACTION_VIEW_HANDBOOK = 'handbook_viewer';
        const ACTION_VIEW_HANDBOOK_PUBLICATION = 'handbook_publications_browser';
        const PARAM_HANDBOOK_OWNER_ID = 'handbook_owner';


        const PARAM_LANGUAGE = 'dc:language';
        const PARAM_PUBLISHER = 'dc:publisher';

        const DEFAULT_ACTION = self :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS;


	const ACTION_BROWSE = 'browse';

	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function HandbookManager($user = null)
    {
    	parent :: __construct($user);
    	
    }


    
	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED_HANDBOOK_PUBLICATIONS :

					$selected_ids = $_POST[HandbookPublicationBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
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
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_HANDBOOK_PUBLICATION,
								    self :: PARAM_HANDBOOK_PUBLICATION => $handbook_publication->get_id()));
	}

 	function get_delete_handbook_publication_url($handbook_publication)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_HANDBOOK_PUBLICATION,
								    self :: PARAM_HANDBOOK_PUBLICATION => $handbook_publication->get_id()));
	}

	function get_browse_handbook_publications_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS));
	}

	function get_browse_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
	}

        function get_view_handbook_publication_url($handbook_publication_id)
        {
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_HANDBOOK_PUBLICATION, self::PARAM_HANDBOOK_PUBLICATION_ID => $handbook_id));
	}
        function get_view_handbook_url($handbook_id)
        {
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_HANDBOOK, self::PARAM_HANDBOOK_ID => $handbook_id));
	}


         static function translate_chamilo_language_to_iso_code($language)
    {
        //should probably be put somewhere else (stored in db?) so one can easily add new languages
        //for testing the app this should do
       $iso_639_code;
       switch ($language) {
           case 'english':
               $iso_639_code =  'en';
               break;
           case 'dutch':
               $iso_639_code =  'nl';
               break;
           case 'french':
               $iso_639_code =  'fr';
               break;
           case 'german':
               $iso_639_code =  'de';
               break;
           case 'spanish':
               $iso_639_code =  'es';
               break;
           default:
               $iso_639_code =  'un';
               break;
       }
       return $iso_639_code;
    }


    static function get_alternatives($co_id, $publication_id)
    {
        //GET ITEM ALTERNATIVES
         $cldm = ContextLinkerDataManager::get_instance();
         $rdm = RepositoryDataManager::get_instance();
         $condition = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $co_id);
         $context_links_resultset = $cldm->retrieve_full_context_links($condition);
        
         //GET ITEM
         $rdm = RepositoryDataManager::get_instance();


            $selected_object = $rdm->retrieve_content_object($co_id);
            if ($selected_object && $selected_object->get_type() == HandbookItem::get_type_name())
            {
                $selected_object = $rdm->retrieve_content_object($selected_object->get_reference());
            }
        


        $texts = array();
        $images = array();
        $videos = array();
        $links = array();
        $others = array();
        $handbooks = array();
        $rdm = RepositoryDataManager::get_instance();

//        $count = count($context_links_resultset);
        
        while ($context_links_resultset != false && (count($context_links_resultset) > 0) && list($key, $item) = each($context_links_resultset))
             {
            
                 $alternative_co = $rdm->retrieve_content_object($item[ContextLinkerManager::PROPERTY_ALT_ID]);
                 

                 if($alternative_co)
                 {
                    $display = ContentObjectDisplay :: factory($alternative_co);
                     if($alternative_co->get_type() == Handbook::get_type_name())
                        {
                            $handbooks[] = $alternative_co;
                        }
                       else if($alternative_co->get_type() == HandbookTopic::get_type_name())
                        {

                            $texts[] = $alternative_co;
                        }
                     else if($alternative_co->get_type() == Document::get_type_name())
                     {
                         if($alternative_co->is_image())
                         {
                            $images[] = $alternative_co;
                         }
                          else if($alternative_co->is_flash() || $alternative_co->is_video() || $alternative_co->is_audio())
                          {
                            $videos[] = $alternative_co;
                          }
                          else if($alternative_co->is_showable())
                          {
                              $texts[] = $alternative_co;
                          }
                          else
                          {
                              $others[] = $alternative_co;
                          }
                        }
                        else if($alternative_co->get_type() == Youtube::get_type_name())
                        {
                            $videos[] = $alternative_co;
                        }
                        else if($alternative_co->get_type() == Link::get_type_name())
                        {
                            $links[] = $alternative_co;
                        }
                        else if($alternative_co->get_type() == WikiPage::get_type_name())
                        {
                            $texts[] = $alternative_co;
                        }
                        else
                        {
                            $others[] = $alternative_co;
                        }
                 }
             }

            if($selected_object->get_type() == Handbook::get_type_name())
            {
                $handbooks[] = $selected_object;
            }
            else if($selected_object->get_type() == HandbookTopic::get_type_name())
                    {

                        $texts[] = $selected_object;
                    }
             else if($selected_object->get_type() == Document::get_type_name())
             {
                 if($selected_object->is_image())
                 {
                    $images[] = $alternative_co;
                 }
                  else if($selected_object->is_flash() || $selected_object->is_video() || $selected_object->is_audio())
                  {
                    $videos[] = $alternative_co;
                  }
                  else
                  {
                      $condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID, $selected_object->get_id());
                      $metadata_property_values = MetadataManager::retrieve_metadata_property_values($condition);

                      $metadata_array = array();

                      while($metadata = $metadata_property_values->next_result())
                      {
                          $metadata_array[$metadata->get_property_type_id()]= $metadata->get_value();
                      }
                      $texts[] = $selected_object;
                  }
            }
            else if($selected_object->get_type() == Youtube::get_type_name())
            {
                $videos[] = $selected_object;
            }
            else if($selected_object->get_type() == Link::get_type_name())
            {
                $links[] = $selected_object;
            }
            else if($selected_object->get_type() == WikiPage::get_type_name())
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
                 $preferences = self::get_preferences($publication_id);

                 //determine wich preferences are more important:
                 //TODO this array could/should be a parameter
                 $preference_importance = array();
                 $preference_importance[1] = array('user'=> self::PARAM_LANGUAGE);
                 $preference_importance[2] = array('handbook'=> self::PARAM_LANGUAGE);
                 $preference_importance[3] = array('user'=> self::PARAM_PUBLISHER);
                 $preference_importance[4] = array('handbook'=> self::PARAM_PUBLISHER);


                 //TEXT
                 if(count($texts > 0))
                 {
                     $alternatives_text = self::determine_most_suitable_co($texts, 'text', $preference_importance, $preferences);
                     $alternatives = $alternatives + $alternatives_text;
                 }
                 else
                 { 
                      $alternatives['text'] = $texts;
                 }

                 //IMAGE
                 if(count($images > 0))
                 {
                     $alternatives_image = self::determine_most_suitable_co($images, 'image', $preference_importance, $preferences);
                     $alternatives = $alternatives + $alternatives_image;
                 }
                 else
                 {
                      $alternatives['image'] = $images;
                 }

                 //VIDEO
                 if(count($videos > 0))
                 {
                     $alternatives_video = self::determine_most_suitable_co($videos, 'video', $preference_importance, $preferences);
                     $alternatives = $alternatives + $alternatives_video;
                 }
                 else
                 {
                      $alternatives['video'] = $videos;
                 }

                 //HANDBOOK
                 if(count($videos > 0))
                 {
                     $alternatives_handbook = self::determine_most_suitable_co($handbooks, 'handbook', $preference_importance, $preferences);
                     $alternatives = $alternatives + $alternatives_handbook;
                 }
                 else
                 {
                      $alternatives['handbook'] = $handbooks;
                 }


                 //IMAGE & VIDEO
                 //1. user language 2. publication language
                 //3. user institution 4. publication institution
//                 $alternatives['image_main'] = current($alternatives['image']);
//                 $alternatives['video_main'] = current($alternatives['video']);

//                 $alternatives['handbook_main'] = current($alternatives['handbook']);

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

         while($best_found_flag == false && $i<=$x)
         {       
             if(array_key_exists(key($preference_importance[$i]), $preferences) && array_key_exists($preference_importance[$i][key($preference_importance[$i])], $preferences[key($preference_importance[$i])]))
            {
                 //if a value for this preference has been set
                $metadata_to_check =  $preference_importance[$i][key($preference_importance[$i])];
                $preference_value_to_check = $preferences[key($preference_importance[$i])][$preference_importance[$i][key($preference_importance[$i])]];
                $candidates = self::determine_most_suitable_candidates($array_to_check, $metadata_to_check , $preference_value_to_check);

                //are there one or more good candidates, based on user language?
                 if(count($candidates['yes'])>0)
                 {
                     if(count($candidates['yes']) == 1)
                     {
                        //most suitable co found
                         $best_found_flag = true;
                         $alternatives[$label.'_main'] = current($candidates['yes']);
                         $array_to_check = $candidates['no'];
                         
                     }
                     else
                     {                        //multiple suitable co's found
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
            $i++;
         }


         
         if($best_found_flag == false)
         {
             //no "most suitable" found by metadata & preferences: pick first one as most suitable
             //todo: get rid of double!
             $alternatives[$label.'_main'] = current($array_to_check);
             unset($array_to_check[key($array_to_check)]);
//             $alternatives[$label] = array_merge($alternatives[$label],  $array_to_check);
         }
         $alternatives[$label] = array_merge($alternatives[$label],  $array_to_check);

         return $alternatives;

    }

    static function determine_most_suitable_candidates($candidates_array, $metadata_property, $metadata_property_value)
    {
        $meets_criteria = array();
        $doesnt_meet_criteria = array();

        while(list($key, $co)= each($candidates_array))
         {
            //get metadata for co
            $metadata = MetadataManager::retrieve_metadata_for_content_object($co->get_id());
            if(array_key_exists($metadata_property, $metadata) && $metadata[$metadata_property] == $metadata_property_value)
            {
                $meets_criteria[] = $co;

            }
            else
            {
                $doesnt_meet_criteria[] = $co;
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
        //TODO: This should be gotten from a user-metadata table for now only the language and the publisher is taken into account
        $user_preferences[self::PARAM_LANGUAGE] = self::translate_chamilo_language_to_iso_code(Translation::get_instance()->get_language());

        //for now: get institution name from root group
        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
        $group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_NAME))->next_result();
        $user_preferences[self::PARAM_PUBLISHER] = $group->get_name();

        //HANDBOOK PREFERENCES
        //TODO: this should be gotten from a handbook-publication-preferences table
        $handbook_preferences = array();

        
        $preferences = array();
        $preferences['user'] = $user_preferences;
        $preferences['handbook'] = $handbook_preferences;

        return $preferences;
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

}
?>