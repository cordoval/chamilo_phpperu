<?php
require_once dirname(__FILE__) . '/../cas_password.class.php';
require_once 'Net/LDAP2.php';
require_once 'Net/LDAP2/Filter.php';

class LdapCasPassword extends CasPassword
{

    /**
     * Nothing is done with the password by default
     * This just prevents errors.
     *
     * @param String $old_password The user's old password
     * @param String $new_password The user's new password
     */
    function set_password($old_password, $new_password)
    {
        $encoded_old_password = $this->encode_ldap_password($old_password);
        $encoded_new_password = $this->encode_ldap_password($new_password);

//        $config = array('binddn' => 'user', 'bindpw' => 'password', 'basedn' => 'OU=My group, DC=My domain, DC=local', 'host' => 'xxx.xxx.xxx.xxx', 'options' => array('LDAP_OPT_REFERRALS' => 0));
//
//        // Connecting using the configuration:
//        $ldap = Net_LDAP2 :: connect($config);
//
//        // Test for connection-errors
//        if (PEAR :: isError($ldap))
//        {
//            return false;
//        }
//        else
//        {
//            // Building a filter
//            $filter = Net_LDAP2_Filter :: create('mail', 'contains', $this->get_user()->get_email());
//            $options = array('scope' => 'sub', 'attributes' => array('sn'));
//
//            $search = $ldap->search($config['basedn'], $filter, $options);
//
//            if (PEAR :: isError($search))
//            {
//                return false;
//            }
//            else
//            {
//                echo 'Found ' . $search->count() . ' entries!<br />';
//                if ($search->count() > 0)
//                {
//                    while ($entry = $search->shiftEntry())
//                    {
//                        // do something, like printing the DN of the entry;
//                        // in a real case, dont forget to test for errors!
//                        echo "ENTRY: " . $entry->dn();
//                    }
//
//                }
//                //                else
//            //                {
//            //                    return false;
//            //                }
//            }
//        }

        return true;
    }

    function is_password_changeable()
    {
        return false;
    }

    function get_password_requirements()
    {
        return Translation :: get('GeneralPasswordRequirements');
    }

    function encode_ldap_password($pw)
    {
        $newpw = '';
        $pw = "\"" . $pw . "\"";
        $len = strlen($pw);

        for($i = 0; $i < $len; $i ++)
        {
            $newpw .= "{$pw{$i}}\000";
        }

        $newpw = base64_encode($newpw);
        return $newpw;
    }
}
?>