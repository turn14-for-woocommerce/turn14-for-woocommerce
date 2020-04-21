<?php
/**
 * Admin Emailer Class for sending emails to admin
 *
 * @author Sam Hall https://github.com/hallsamuel90
 */

if (! defined('ABSPATH')) {
    exit;
}

/**
 * Class Admin Emailer
 */
class Admin_Emailer
{
    private $admin_address;

    /**
     * Default Constructor
     */
    public function __construct()
    {
        $this->admin_address = get_bloginfo('admin_email');
    }

    /**
     * Sends admin an email
     *
     * @param string subject of email
     * @param string body of email
     */
    public function send_admin_email($subj, $msg)
    {
        if ($subj != null && $msg != null){
            wp_mail($this->admin_address, $subj, $msg);
        }
    }
}
