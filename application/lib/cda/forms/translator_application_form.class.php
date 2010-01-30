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
     * Build the form.
     */
    private function build_form()
    {  
    	$this->addElement('category', Translation :: get('LanguageSelections'));
    	$this->addElement('select', TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID, Translation :: get('SourceLanguage'), $this->get_source_languages());
    	
//		$url = Path :: get(WEB_PATH) . 'application/lib/cda/xml_feeds/xml_cda_languages_feed.php';
//		$locale = array();
//		$locale['Display'] = Translation :: get('SelectLanguages');
//		$locale['Searching'] = Translation :: get('Searching');
//		$locale['NoResults'] = Translation :: get('NoResults');
//		$locale['Error'] = Translation :: get('Error');
//		$hidden = false;
		
//		$elem = $this->addElement('element_finder', TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID, Translation :: get('DestinationLanguages'), $url, $locale);
//		$elem->setDefaults(array());
//		$elem->excludeElements(array());

    	$this->addElement('select', TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID, Translation :: get('DestinationLanguages'), $this->get_source_languages(), array('multiple'));
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
    	$defaults[TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID] = PlatformSetting :: get('source_language', CdaManager :: APPLICATION_NAME);
        parent :: setDefaults($defaults);
    }
    
    function create_application()
    {
    	$values = $this->exportValues();
    	
    	$languages = $values[TranslatorApplication :: PROPERTY_DESTINATION_LANGUAGE_ID];
    	
    	foreach($languages as $language)
    	{
	   		$application = new TranslatorApplication();
	   		$application->set_user_id(Session :: get_user_id());
	   		$application->set_source_language_id($values[TranslatorApplication :: PROPERTY_SOURCE_LANGUAGE_ID]);
	   		$application->set_destination_language_id($language);
	   		$application->set_date(Utilities :: to_db_date(time()));
	   		$application->set_status(TranslatorApplication :: STATUS_PENDING);
	   		
	   		if (!$application->create())
	   		{
	   			return false;
	   		}
    	}
    	
   		return true;
    }
}
?>