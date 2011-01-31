<?php
namespace application\handbook;
use common\libraries\FormValidator;
use common\libraries\Translation;
use application\metadata\MetadataDataManager;
use application\metadata\MetadataPropertyValue;
use application\metadata\MetadataForm;
use common\libraries\Request;
use application\context_linker\ContextLink;
use application\metadata\MetadataPropertyType;
use common\libraries\ResourceManager;
use common\libraries\Path;

class HandbookPreferenceForm extends MetadataForm
{
    
    const TYPE = 'content_object';

        
    function __construct()
     {
        $this->build_basic_form();
     }

    function build_basic_form()
    {
        //TODO: implement
       
            $this->retrieve_prefixes();
            $this->addElement('select', MetadataPropertyType :: PROPERTY_NS_PREFIX, Translation :: get('MetadataPropertyType'), $this->get_prefixes(), array('class' => 'ns_prefix'));
            $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/metadata/resources/javascript/format_metadata_value.js'));

     }
   
}
?>