<?php
class InvitationManagerCreatorComponent extends InvitationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $form = new InvitationForm($this->get_url());

        if ($form->validate())
        {
            //$success = $form->email();
        //$this->redirect(Translation :: get($success ? 'EmailSent' : 'EmailNotSent'), ($success ? false : true), array());


        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
}
?>