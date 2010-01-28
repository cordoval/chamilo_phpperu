<?php
require_once dirname(__FILE__) . '/../translator_application.class.php';
require_once dirname(__FILE__) . '/../cda_language.class.php';
/**
 * $Id: language_pack_browser_filter_form.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.forms
 */
class TranslatorApplicationForm extends FormValidator
{
    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function __construct($url)
    {
        parent :: __construct('translator_application_form', 'post', $url);

        $this->build_form();
        $this->setDefaults();
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {  
    	$this->addElement('category', Translation :: get('LanguageSelections'));
    	$this->addElement('select', TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE, Translation :: get('SourceLanguage'), $this->get_source_languages());
    	
//		$url = Path :: get(WEB_PATH) . 'application/lib/cda/xml_feeds/xml_cda_languages_feed.php';
//		$locale = array();
//		$locale['Display'] = Translation :: get('AddAttachments');
//		$locale['Searching'] = Translation :: get('Searching');
//		$locale['NoResults'] = Translation :: get('NoResults');
//		$locale['Error'] = Translation :: get('Error');
//		$hidden = false;
		
//		$elem = $this->addElement('element_finder', TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGES, Translation :: get('DestinationLanguages'), $url, $locale);
//		$elem->setDefaults(array());
//		$elem->excludeElements(array());

    	$this->addElement('select', TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGES, Translation :: get('DestinationLanguages'), $this->get_source_languages(), array('multiple'));
    	$this->addElement('category');
    	
        $this->addElement('style_submit_button', 'submit', Translation :: get('Apply'), array('class' => 'positive'));
    }
    
    function get_source_languages()
    {
    	$languages = CdaDataManager :: get_instance()->retrieve_cda_languages(null, null, null, array(new ObjectTableOrder(CdaLanguage :: PROPERTY_ORIGINAL_NAME)));
    	$options = array();
    	
    	while($language = $languages->next_result())
    	{
    		$options[$language->get_id()] = $language->get_original_name();
    	}
    	
        return $options;
    }

    function setDefaults($defaults = array ())
    {
    	$defaults[TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE] = PlatformSetting :: get('source_language', CdaManager :: APPLICATION_NAME);
        parent :: setDefaults($defaults);
    }
    
    function create_application()
    {
    	$values = $this->exportValues();
    	
//		$source_setting = AdminDataManager :: get_instance()->retrieve_setting_from_variable_name('source_language', CdaManager :: APPLICATION_NAME);
//    	$source_user_setting = UserDataManager :: get_instance()->retrieve_user_setting(Session :: get_user_id(), $source_setting->get_id());
//    	
//    	if(!$source_user_setting)
//    	{
//    		$source_user_setting = new UserSetting();
//    		$source_user_setting->set_setting_id($source_setting->get_id());
//    		$source_user_setting->set_value($values[TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE]);
//    		$source_user_setting->set_user_id(Session :: get_user_id());
//    		$source_user_setting->create();
//    	}
//
//    	foreach($values[TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGES] as $destination_language)
//    	{
	   		$application = new TranslatorApplication();
	   		$application->set_user_id(Session :: get_user_id());
	   		$application->set_source_language($values[TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE]);
	   		$application->set_destination_languages(serialize($values[TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGES]));
	   		$application->set_date(Utilities :: to_db_date(time()));
	   		$application->set_status(TranslatorApplication :: STATUS_PENDING);
	   		return $application->create();
//    	}
    }
}
?>