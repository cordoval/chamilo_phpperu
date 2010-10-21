<?php
namespace application\cas_user;

require_once dirname(__FILE__) . '/../../cas_account_manager/cas_account_manager.class.php';

class CasUserManagerAccountComponent extends CasUserManager implements DelegateComponent
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        CasAccountManager :: launch($this);
    }
}
?>