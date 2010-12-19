<?php
namespace application\cda;

use common\libraries\AndCondition;
use common\libraries\EqualityCondition;

require_once dirname(__FILE__) . '/variable_writer.class.php';

class DatabaseVariableWriter extends VariableWriter
{
    private $language_packs;

    function handle_variable($variable_name, $context)
    {
        $language_pack = $this->retrieve_or_create_language_pack($context);
        $variable = $this->retrieve_variable($variable_name, $language_pack->get_id());
        if ($variable)
        {
            return;
        }

        $variable = new Variable();
        $variable->set_language_pack_id($language_pack->get_id());
        $variable->set_variable($variable_name);
        $variable->create();
    }

    private function retrieve_variable($variable_name, $language_pack_id)
    {
        $dm = CdaDataManager :: get_instance();

        $conditions[] = new EqualityCondition(Variable :: PROPERTY_VARIABLE, $variable_name);
        $conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);

        $condition = new AndCondition($conditions);

        return $dm->retrieve_variables($condition)->next_result();
    }

    private function retrieve_or_create_language_pack($name, $type)
    {
        if ($this->language_packs[$type][$name])
        {
            $dm = CdaDataManager :: get_instance();
            $conditions[] = new EqualityCondition(LanguagePack :: PROPERTY_NAME, $name);
            $conditions[] = new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, LanguagePack :: BRANCH_LCMS);
            $condition = new AndCondition($conditions);
            $language_pack = $dm->retrieve_language_packs($condition)->next_result();

            if (! $language_pack)
            {
                $language_pack = new LanguagePack();
                $language_pack->set_branch(LanguagePack :: BRANCH_LCMS);
                $language_pack->set_name($name);
                $language_pack->set_type($type);
                $language_pack->create();
            }

            $this->language_packs[$type][$name] = $language_pack;
        }

        return $this->language_packs[$type][$name];
    }

}

?>