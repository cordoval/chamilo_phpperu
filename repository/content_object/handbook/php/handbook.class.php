<?php
namespace repository\content_object\handbook;

use repository\content_object\handbook_item\HandbookItem;

use common\libraries\ComplexContentObjectSupport;
use repository\ContentObject;
use common\libraries\Utilities;
use repository\content_object\handbook_topic\HandbookTopic;
use repository\content_object\glossary\Glossary;
use repository\content_object\link\Link;
use repository\content_object\document\Document;

require_once dirname(__FILE__) . '/uuid.class.php';

/**
 * This class represents an handbook
 */
class Handbook extends ContentObject implements ComplexContentObjectSupport
{
    const CLASS_NAME = __CLASS__;
    const PROPERTY_UUID = 'uuid';
    const TYPE_NAME = 'handbook';

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_UUID);
    }

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }

    function get_allowed_types()
    {
        return array(Handbook :: get_type_name(), HandbookItem :: get_type_name());
    }

    static function get_allowed_content()
    {
        return array(
                Handbook :: get_type_name(),
                HandbookTopic :: get_type_name(),
                Glossary :: get_type_name(),
                Link :: get_type_name(),
                Document :: get_type_name());
    }

    function get_uuid()
    {
        return $this->get_additional_property(self :: PROPERTY_UUID);
    }

    function set_uuid()
    {
        $uuid = UUID :: generate(UUID :: UUID_RANDOM, UUID :: FMT_STRING);
        $this->set_additional_property(self :: PROPERTY_UUID, $uuid);
    }

    function create()
    {
        $this->set_uuid();
        return parent::create();
    }
}
?>
