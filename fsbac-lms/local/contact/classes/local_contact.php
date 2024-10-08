<?php
// This file is part of the Contact Form plugin for Moodle - http://moodle.org/
//
// Contact Form is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Contact Form is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Contact Form.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin for Moodle is used to send emails through a web form.
 *
 * @package    local_contact
 * @copyright  2016-2023 TNG Consulting Inc. - www.tngconsulting.ca
 * @author     Michael Milette
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * local_contact class. Handles processing of information submitted from a web form.
 * @copyright  2016-2023 TNG Consulting Inc. - www.tngconsulting.ca
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_contact {

    /**
     * Class constructor. Receives and validates information received through a
     * web form submission.
     *
     * @return     True  if the information received passes our spambot detection. False if it fails.
     */
    public function __construct() {
        global $CFG;

        if (isloggedin() && !isguestuser()) {
            // If logged-in as non guest, use their registered fullname and email address.
            global $USER;
            $this->fromname = get_string('fullnamedisplay', null, $USER);
            $this->fromemail = $USER->email;
             // Insert name and email address at first position in $_POST array.
            if (!empty($_POST['email'])) {
                unset($_POST['email']);
            }
            if (!empty($_POST['name'])) {
                unset($_POST['name']);
            }
            $_POST = array_merge(array('email' => $this->fromemail), $_POST);
            $_POST = array_merge(array('name' => $this->fromname), $_POST);
        } else {
            // If not logged-in as a user or logged in a guest, the name and email fields are required.
            if (empty($this->fromname  = trim(optional_param(get_string('field-name', 'local_contact'), '', PARAM_TEXT)))) {
                $this->fromname = required_param('name', PARAM_TEXT);
            }
            if (empty($this->fromemail = trim(optional_param(get_string('field-email', 'local_contact'), '', PARAM_EMAIL)))) {
                $this->fromemail = required_param('email', PARAM_TEXT);
            }
        }
        $this->fromname = trim($this->fromname ?? '');
        $this->fromemail = trim($this->fromemail ?? '');

        $this->isspambot = false;
        $this->errmsg = '';

        if ($CFG->branch >= 32) {
            // As of Moodle 3.2, $CFG->emailonlyfromnoreplyaddress has been deprecated.
            $CFG->emailonlyfromnoreplyaddress = !empty($CFG->noreplyaddress);
        }

        // Did someone forget to configure Moodle properly?

        // Validate Moodle's no-reply email address.
        if (!empty($CFG->emailonlyfromnoreplyaddress)) {
            if (!$this->isspambot && !empty($CFG->emailonlyfromnoreplyaddress)
                    && $this->isspambot = !validate_email($CFG->noreplyaddress)) {
                $this->errmsg = 'Moodle no-reply email address is invalid.';
                if ($CFG->branch >= 32) {
                    $this->errmsg .= '  (<a href="../../admin/settings.php?section=outgoingmailconfig">change</a>)';
                } else {
                    $this->errmsg .= '  (<a href="../../admin/settings.php?section=messagesettingemail">change</a>)';
                }
            }
        }

        // Use primary administrators name and email address if support name and email are not defined.
        $primaryadmin = get_admin();
        $CFG->supportemail = empty($CFG->supportemail) ? $primaryadmin->email : $CFG->supportemail;
        $CFG->supportname = empty($CFG->supportname) ? fullname($primaryadmin, true) : $CFG->supportname;

        // Validate Moodle's support email address.
        if (!$this->isspambot && $this->isspambot = !validate_email($CFG->supportemail)) {
            $this->errmsg = 'Moodle support email address is invalid.';
            $this->errmsg .= ' (<a href="../../admin/settings.php?section=supportcontact">change</a>)';
        }

        // START: Spambot detection.

        // File attachments not supported.
        if (!$this->isspambot && $this->isspambot = !empty($_FILES)) {
            $this->errmsg = 'File attachments not supported.';
        }

        // Validate submit button.
        if (!$this->isspambot && $this->isspambot = !isset($_POST['submit'])) {
            $this->errmsg = 'Missing submit button.';
        }

        // Limit maximum number of form $_POST fields to 1024.
        if (!$this->isspambot) {
            $postsize = @count($_POST);
            if ($this->isspambot = ($postsize > 1024)) {
                $this->errmsg = 'Form cannot contain more than 1024 fields.';
            } else if ($this->isspambot = ($postsize == 0)) {
                $this->errmsg = 'Form must be submitted using POST method.';
            }
        }

        // Limit maximum size of allowed form $_POST submission to 256 KB.
        if (!$this->isspambot) {
            $postsize = (int) @$_SERVER['CONTENT_LENGTH'];
            if ($this->isspambot = ($postsize > 262144)) {
                $this->errmsg = 'Form cannot contain more than 256 KB of data.';
            }
        }

        // Validate if "sesskey" field contains the correct value.
        if (!$this->isspambot && $this->isspambot = (optional_param('sesskey', '3.1415', PARAM_RAW) != sesskey())) {
            $this->errmsg = '"sesskey" field is missing or contains an incorrect value.';
        }

        // Validate referrer URL.
        if (!$this->isspambot && $this->isspambot = !isset($_SERVER['HTTP_REFERER'])) {
            $this->errmsg = 'Missing referrer.';
        }
        if (!$this->isspambot && $this->isspambot = (stripos($_SERVER['HTTP_REFERER'], $CFG->wwwroot) != 0)) {
            $this->errmsg = 'Unknown referrer - must come from this site: ' . $CFG->wwwroot;
        }

        // Validate sender's email address.
        if (!$this->isspambot && $this->isspambot = !validate_email($this->fromemail)) {
            $this->errmsg = 'Unknown sender - invalid email address or the form field name is incorrect.';
        }

        // Validate sender's name.
        if (!$this->isspambot && $this->isspambot = empty($this->fromname)) {
            $this->errmsg = 'Missing sender - invalid name or the form field name is incorrect';
        }

        // Validate against email address whitelist and blacklist.
        $skipdomaintest = false;
        // TODO: Create a plugin setting for this list.
        $whitelist = ''; // Future code: $config->whitelistemails .
        $whitelist = ',' . $whitelist . ',';
        // TODO: Create a plugin blacklistemails setting.
        $blacklist = ''; // Future code: $config->blacklistemails .
        $blacklist = ',' . $blacklist . ',';
        if (!$this->isspambot && stripos($whitelist, ',' . $this->fromemail . ',') != false) {
            $skipdomaintest = true; // Skip the upcoming domain test.
        } else {
            if (!$this->isspambot && $blacklist != ',,'
                    && $this->isspambot = ($blacklist == '*' || stripos($blacklist, ',' . $this->fromemail . ',') == false)) {
                // Nice try. We know who you are.
                $this->errmsg = 'Bad sender - Email address is blacklisted.';
            }
        }

        // Validate against domain whitelist and blacklist... except for the nice people.
        if (!$skipdomaintest && !$this->isspambot) {
            // TODO: Create a plugin whitelistdomains setting.
            $whitelist = ''; // Future code: $config->whitelistdomains .
            $whitelist = ',' . $whitelist . ',';
            $domain = substr(strrchr($this->fromemail, '@'), 1);

            if (stripos($whitelist, ',' . $domain . ',') != false) {
                // Ya, you check out. This email domain is gold here!
                $blacklist = '';
            } else {
                 // TODO: Create a plugin blacklistdomains setting.
                $blacklist = 'example.com,example.net,sample.com,test.com,specified.com'; // Future code:$config->blacklistdomains .
                $blacklist = ',' . $blacklist . ',';
                if ($blacklist != ',,'
                        && $this->isspambot = ($blacklist == '*' || stripos($blacklist, ',' . $domain . ',') != false)) {
                    // Naughty naughty. We know all about your kind.
                    $this->errmsg = 'Bad sender - Email domain is blacklisted.';
                }
            }
        }

        // TODO: Test IP address against blacklist.

        // END: Spambot detection... Wait, got some photo ID on you? ;-) .
    }

    /**
     * Creates a user info object based on provided parameters.
     *
     * @param      string  $email  email address.
     * @param      string  $name   (optional) Plain text real name.
     * @param      int     $id     (optional) Moodle user ID.
     *
     * @return     object  Moodle userinfo.
     */
    private function makeemailuser($email, $name = '', $id = -99) {
        $emailuser = new stdClass();
        $emailuser->email = trim(filter_var($email, FILTER_SANITIZE_EMAIL) ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailuser->email = '';
        }
        $emailuser->firstname = format_text($name, FORMAT_PLAIN, array('trusted' => false));
        $emailuser->lastname = '';
        $emailuser->maildisplay = true;
        $emailuser->mailformat = 1; // 0 (zero) text-only emails, 1 (one) for HTML emails.
        $emailuser->id = $id;
        $emailuser->firstnamephonetic = '';
        $emailuser->lastnamephonetic = '';
        $emailuser->middlename = '';
        $emailuser->alternatename = '';
        $emailuser->username = '';
        return $emailuser;
    }

    /**
     * Send email message and optionally autorespond.
     *
     * @param      string  $email Recipient's Email address.
     * @param      string  $name  Recipient's real name in plain text.
     * @param      boolean  $sendconfirmationemail  Set to true to also send an autorespond confirmation email back to user (TODO).
     *
     * @return     boolean  $status - True if message was successfully sent, false if not.
     */
    public function sendmessage($email, $name, $sendconfirmationemail = false) {
        global $USER, $CFG, $SITE;

        $systemcontext = context_system::instance();

        // Create the sender from the submitted name and email address.
        $from = $this->makeemailuser($this->fromemail, $this->fromname);

        // Create the recipient.
        $to = $this->makeemailuser($email, $name);

        // Create the Subject for message.
        $subject = '';
        if (empty(get_config('local_contact', 'nosubjectsitename'))) { // Not checked.
            // Include site name in subject field.
            $subject .= '[' . format_string($SITE->shortname, true, ['escape' => false, 'context' => $systemcontext]) . '] ';
        }
        $subject .= optional_param(get_string('field-subject', 'local_contact'),
                get_string('defaultsubject', 'local_contact'), PARAM_TEXT);

        // Build the body of the email using user-entered information.

        // Note: Name of message field is defined in the language pack.
        $fieldmessage = get_string('field-message', 'local_contact');

        $htmlmessage = '';

        /**
         * Callback function for array_filter.
         *
         * @param string $string Text to be chekced.
         * @return boolean true if string is not empty, otherwise false.
         */
        function filterempty($string) {
            $string = trim($string ?? '');
            return ($string !== null && $string !== false && $string !== '');
        }

        foreach ($_POST as $key => $value) {

            // Only process key conforming to valid form field ID/Name token specifications.
            if (preg_match('/^[A-Za-z][A-Za-z0-9_:\.-]*/', $key)) {

                if (is_array($value)) {
                    // Join array of values. Example: <select multiple>.
                    $value = array_filter($value, "filterempty");
                    $value = join(', ', $value);
                } else {
                    $value = trim($value ?? '');
                }
                // Exclude fields we don't want in the message and empty fields.
                if (!in_array($key, array('sesskey', 'submit')) && trim($value ?? '') != '') {

                    // Apply minor formatting of the key by replacing underscores with spaces.
                    $key = str_replace('_', ' ', $key);
					
                    switch ($key) {
                        // Make custom alterations.
                        case 'message': // Message field - use translated value from language file.
                            $key = $fieldmessage;
                        case strpos($value, "\n") !== false: // Field contains linefeeds.
                        case $fieldmessage: // Message field.
                            // Strip out excessive empty lines.
                            $value = preg_replace('/\n(\s*\n){2,}/', "\n\n", $value);
                            // Sanitize the text.
                            $value = format_text($value, FORMAT_PLAIN, array('trusted' => false));
                            // Add to email message.
                            $htmlmessage .= '<p><strong>' . ucfirst($key) . ' :</strong></p><p>' . $value . '</p>';
                            break;
                        // Don't include the following fields in the body of the message.
                        case 'recipient':                  // Recipient field.
                        case 'recaptcha challenge field':  // ReCAPTCHA related field.
                        case 'recaptcha response field':   // ReCAPTCHA related field.
                        case 'g-recaptcha-response':       // ReCAPTCHA related field.
                            break;
                        // Use language translations for the labels of the following fields.
                        case 'name':        // Name field.
                        ////NF case 'email':       // Email field.
                        case 'subject':     // Subject field.
                            $key = get_string('field-' . $key, 'local_contact');
						case 'email':  $USER->contactfromemail=$key;
                        default:            // All other fields.
                            // Sanitize the text.
                            $value = format_text($value, FORMAT_PLAIN, array('trusted' => false));
                            if (filter_var($value, FILTER_VALIDATE_URL)) {
                                // Convert URL into clickable link.
                                $value = '<a href="' . $value . '">' . $value . '</a>';
                            }
                            // Add to email message.
                            $htmlmessage .= '<strong>'.ucfirst($key) . ' :</strong> ' . $value . '<br>' . PHP_EOL;
                    }
                }
            }
        }

        // Sanitize user agent and referer.
        $httpuseragent = format_text($_SERVER['HTTP_USER_AGENT'], FORMAT_PLAIN, array('trusted' => false));
        $httpreferer = format_text($_SERVER['HTTP_REFERER'], FORMAT_PLAIN, array('trusted' => false));

        // Prepare arrays to handle substitution of embedded tags in the footer.
        $tags = array('[fromname]', '[fromemail]', '[supportname]', '[supportemail]',
                '[lang]', '[userip]', '[userstatus]',
                '[sitefullname]', '[siteshortname]', '[siteurl]',
                '[http_user_agent]', '[http_referer]'
        );

        $info = array($from->firstname, $from->email, $CFG->supportname, $CFG->supportemail,
                current_language(), getremoteaddr(), $this->moodleuserstatus($from->email),
                format_text($SITE->fullname, FORMAT_HTML, ['context' => $systemcontext, 'escape' => false]) . ': ',
                format_text($SITE->shortname, FORMAT_HTML, ['context' => $systemcontext, 'escape' => false]), $CFG->wwwroot,
                $httpuseragent, $httpreferer
        );
		

        // Create the footer - Add some system information.
        $footmessage = get_string('extrainfo', 'local_contact');
        $footmessage = format_text($footmessage, FORMAT_HTML, array('trusted' => true, 'noclean' => true, 'para' => false));
        ////NF$htmlmessage .= str_replace($tags, $info, $footmessage);

        // Override "from" email address if one was specified in the plugin's settings.
        $noreplyaddress = $CFG->noreplyaddress;
        if (!empty($customfrom = get_config('local_contact', 'senderaddress'))) {
            $CFG->noreplyaddress = $customfrom;
        }

        // Send email message to recipient and set replyto to the sender's email address and name.
        if (empty(get_config('local_contact', 'noreplyto'))) { // Not checked.
            $status = $this->emailtouser($to, $from, $subject, html_to_text($htmlmessage), $htmlmessage, '', '', true,
                    $from->email, $from->firstname);
        } else { // Checked.
            $status = $this->emailtouser($to, $from, $subject, html_to_text($htmlmessage), $htmlmessage, '', '', true);
        }
        $CFG->noreplyaddress = $noreplyaddress;

        // If successful and a confirmation email is desired, send it the original sender.
        if ($status && $sendconfirmationemail) {
            // Substitute embedded tags for some information.
            $htmlmessage = str_replace($tags, $info, get_string('confirmationemail', 'local_contact'));
            $htmlmessage = format_text($htmlmessage, FORMAT_HTML, array('trusted' => true, 'noclean' => true, 'para' => false));

            $replyname  = empty($CFG->emailonlyfromnoreplyaddress) ? $CFG->supportname : get_string('noreplyname');
            $replyemail = empty($CFG->emailonlyfromnoreplyaddress) ? $CFG->supportemail : $CFG->noreplyaddress;
            $to = $this->makeemailuser($replyemail, $replyname);

            // Send confirmation email message to the sender.
            $this->emailtouser($from, $to, $subject, html_to_text($htmlmessage), $htmlmessage, '', '', true);
        }
        return $status;
    }

    /**
     * Builds a one line status report on the user. Uses their Moodle info, if
     * logged in, or their email address to look up the information if they are
     * not.
     *
     * @param      string  $emailaddress  Plain text email address.
     *
     * @return     string  Contains what we know about the Moodle user including whether they are logged in or out.
     */
    private function moodleuserstatus($emailaddress) {
        if (isloggedin() && !isguestuser()) {
            global $USER;
            $info = get_string('fullnamedisplay', null, $USER) . ' / ' . $USER->email . ' (' . $USER->username .
                    ' / ' . get_string('eventuserloggedin', 'auth') . ')';
        } else {
            global $DB;
            $usercount = $DB->count_records('user', ['email' => $emailaddress, 'deleted' => 0]);
            switch ($usercount) {
                case 0:  // We don't know this email address.
                    $info = get_string('emailnotfound');
                    break;
                case 1: // We found exactly one match.
                    $user = get_complete_user_data('email', $emailaddress);
                    $extrainfo = '';

                    // Is user locked out?
                    if ($lockedout = get_user_preferences('login_lockout', 0, $user)) {
                        $extrainfo .= ' / ' . get_string('lockedout', 'local_contact');
                    }

                    // Has user responded to confirmation email?
                    if (empty($user->confirmed)) {
                        $extrainfo .= ' / ' . get_string('notconfirmed', 'local_contact');
                    }

                    $info = get_string('fullnamedisplay', null, $user) . ' / ' . $user->email . ' (' . $user->username .
                            ' / ' . get_string('eventuserloggedout') . $extrainfo . ')';
                    break;
                default: // We found multiple users with this email address.
                    $info = get_string('duplicateemailaddresses', 'local_contact');
            }
        }
        return $info;
    }
    /**
     * Send an email to a specified user
     *
     * @param stdClass $user  A {@link $USER} object
     * @param stdClass $from A {@link $USER} object
     * @param string $subject plain text subject line of the email
     * @param string $messagetext plain text version of the message
     * @param string $messagehtml complete html version of the message (optional)
     * @param string $attachment a file on the filesystem, either relative to $CFG->dataroot or a full path to a file in one of
     *          the following directories: $CFG->cachedir, $CFG->dataroot, $CFG->dirroot, $CFG->localcachedir, $CFG->tempdir
     * @param string $attachname the name of the file (extension indicates MIME)
     * @param bool $usetrueaddress determines whether $from email address should
     *          be sent out. Will be overruled by user profile setting for maildisplay
     * @param string $replyto Email address to reply to
     * @param string $replytoname Name of reply to recipient
     * @param int $wordwrapwidth custom word wrap width, default 79
     * @return bool Returns true if mail was sent OK and false if there was an error.
     */
    private function emailtouser($user, $from, $subject, $messagetext, $messagehtml = '', $attachment = '', $attachname = '',
                           $usetrueaddress = true, $replyto = '', $replytoname = '', $wordwrapwidth = 79) {

        global $CFG, $PAGE, $SITE;

        if (empty($user) or empty($user->id)) {
            debugging('Can not send email to null user', DEBUG_DEVELOPER);
            return false;
        }

        if (empty($user->email)) {
            debugging('Can not send email to user without email: '.$user->id, DEBUG_DEVELOPER);
            return false;
        }

        if (!empty($user->deleted)) {
            debugging('Can not send email to deleted user: '.$user->id, DEBUG_DEVELOPER);
            return false;
        }

        if (defined('BEHAT_SITE_RUNNING')) {
            // Fake email sending in behat.
            return true;
        }

        if (!empty($CFG->noemailever)) {
            // Hidden setting for development sites, set in config.php if needed.
            debugging('Not sending email due to $CFG->noemailever config setting', DEBUG_NORMAL);
            return true;
        }

        if (email_should_be_diverted($user->email)) {
            $subject = "[DIVERTED {$user->email}] $subject";
            $user = clone($user);
            $user->email = $CFG->divertallemailsto;
        }

        // Skip mail to suspended users.
        if ((isset($user->auth) && $user->auth == 'nologin') or (isset($user->suspended) && $user->suspended)) {
            return true;
        }

        if (!validate_email($user->email)) {
            // We can not send emails to invalid addresses - it might create security issue or confuse the mailer.
            debugging("email_to_user: User $user->id (".fullname($user).") email ($user->email) is invalid! Not sending.");
            return false;
        }

        if (over_bounce_threshold($user)) {
            debugging("email_to_user: User $user->id (".fullname($user).") is over bounce threshold! Not sending.");
            return false;
        }

        // TLD .invalid  is specifically reserved for invalid domain names.
        // For More information, see {@link http://tools.ietf.org/html/rfc2606#section-2}.
        if (substr($user->email, -8) == '.invalid') {
            debugging("email_to_user: User $user->id (".fullname($user).") email domain ($user->email) is invalid! Not sending.");
            return true; // This is not an error.
        }

        // If the user is a remote mnet user, parse the email text for URL to the
        // wwwroot and modify the url to direct the user's browser to login at their
        // home site (identity provider - idp) before hitting the link itself.
        if (is_mnet_remote_user($user)) {
            require_once($CFG->dirroot.'/mnet/lib.php');

            $jumpurl = mnet_get_idp_jump_url($user);
            $callback = partial('mnet_sso_apply_indirection', $jumpurl);

            $messagetext = preg_replace_callback("%($CFG->wwwroot[^[:space:]]*)%",
                    $callback,
                    $messagetext);
            $messagehtml = preg_replace_callback("%href=[\"'`]($CFG->wwwroot[\w_:\?=#&@/;.~-]*)[\"'`]%",
                    $callback,
                    $messagehtml);
        }
        $mail = get_mailer();

        if (!empty($mail->SMTPDebug)) {
            echo '<pre>' . "\n";
        }

        $temprecipients = array();
        $tempreplyto = array();

        // Make sure that we fall back onto some reasonable no-reply address.
        $noreplyaddressdefault = 'noreply@' . get_host_from_url($CFG->wwwroot);
        $noreplyaddress = empty($CFG->noreplyaddress) ? $noreplyaddressdefault : $CFG->noreplyaddress;

        if (!validate_email($noreplyaddress)) {
            debugging('email_to_user: Invalid noreply-email '.s($noreplyaddress));
            $noreplyaddress = $noreplyaddressdefault;
        }

        // Make up an email address for handling bounces.
        if (!empty($CFG->handlebounces)) {
            $modargs = 'B'.base64_encode(pack('V', $user->id)).substr(md5($user->email), 0, 16);
            $mail->Sender = generate_email_processing_address(0, $modargs);
        } else {
            $mail->Sender = $noreplyaddress;
        }

        // Make sure that the explicit replyto is valid, fall back to the implicit one.
        if (!empty($replyto) && !validate_email($replyto)) {
            debugging('email_to_user: Invalid replyto-email '.s($replyto));
            $replyto = $noreplyaddress;
        }

        if (is_string($from)) { // So we can pass whatever we want if there is need.
            $mail->From     = $noreplyaddress;
            $mail->FromName = $from;
        // Check if using the true address is true, and the email is in the list of allowed domains for sending email,
        // and that the senders email setting is either displayed to everyone, or display to only other users that are enrolled
        // in a course with the sender.
        } else {
        // } else if ($usetrueaddress && can_send_from_real_email_address($from, $user)) {
            if (!validate_email($from->email)) {
                debugging('email_to_user: Invalid from-email '.s($from->email).' - not sending');
                // Better not to use $noreplyaddress in this case.
                return false;
            }
            $mail->From = $from->email;
            $fromdetails = new stdClass();
            $fromdetails->name = fullname($from);
            $fromdetails->url = preg_replace('#^https?://#', '', $CFG->wwwroot);
            $fromdetails->siteshortname = format_string($SITE->shortname);
            $fromstring = $fromdetails->name;
            if ($CFG->emailfromvia == EMAIL_VIA_ALWAYS) {
                $fromstring = get_string('emailvia', 'core', $fromdetails);
            }
            $mail->FromName = $fromstring;
            if (empty($replyto)) {
                $tempreplyto[] = array($from->email, fullname($from));
            }
        // } else {
        //     $mail->From = $noreplyaddress;
        //     $fromdetails = new stdClass();
        //     $fromdetails->name = fullname($from);
        //     $fromdetails->url = preg_replace('#^https?://#', '', $CFG->wwwroot);
        //     $fromdetails->siteshortname = format_string($SITE->shortname);
        //     $fromstring = $fromdetails->name;
        //     if ($CFG->emailfromvia != EMAIL_VIA_NEVER) {
        //         $fromstring = get_string('emailvia', 'core', $fromdetails);
        //     }
        //     $mail->FromName = $fromstring;
        //     if (empty($replyto)) {
        //         $tempreplyto[] = array($noreplyaddress, get_string('noreplyname'));
        //     }
        }

        if (!empty($replyto)) {
            $tempreplyto[] = array($replyto, $replytoname);
        }

        $temprecipients[] = array($user->email, fullname($user));

        // Set word wrap.
        $mail->WordWrap = $wordwrapwidth;

        if (!empty($from->customheaders)) {
            // Add custom headers.
            if (is_array($from->customheaders)) {
                foreach ($from->customheaders as $customheader) {
                    $mail->addCustomHeader($customheader);
                }
            } else {
                $mail->addCustomHeader($from->customheaders);
            }
        }

        // If the X-PHP-Originating-Script email header is on then also add an additional
        // header with details of where exactly in moodle the email was triggered from,
        // either a call to message_send() or to email_to_user().
        if (ini_get('mail.add_x_header')) {

            $stack = debug_backtrace(false);
            $origin = $stack[0];

            foreach ($stack as $depth => $call) {
                if ($call['function'] == 'message_send') {
                    $origin = $call;
                }
            }

            $originheader = $CFG->wwwroot . ' => ' . gethostname() . ':'
                 . str_replace($CFG->dirroot . '/', '', $origin['file']) . ':' . $origin['line'];
            $mail->addCustomHeader('X-Moodle-Originating-Script: ' . $originheader);
        }

        if (!empty($CFG->emailheaders)) {
            $headers = array_map('trim', explode("\n", $CFG->emailheaders));
            foreach ($headers as $header) {
                if (!empty($header)) {
                    $mail->addCustomHeader($header);
                }
            }
        }

        if (!empty($from->priority)) {
            $mail->Priority = $from->priority;
        }

        $renderer = $PAGE->get_renderer('core');
        $context = array(
            'sitefullname' => $SITE->fullname,
            'siteshortname' => $SITE->shortname,
            'sitewwwroot' => $CFG->wwwroot,
            'subject' => $subject,
            'prefix' => $CFG->emailsubjectprefix,
            'to' => $user->email, 
            'toname' => fullname($user),
            'from' => $mail->From,
            'fromname' => $mail->FromName,
        );
        if (!empty($tempreplyto[0])) {
            $context['replyto'] = $tempreplyto[0][0];
            $context['replytoname'] = $tempreplyto[0][1];
        }
        if ($user->id > 0) {
            $context['touserid'] = $user->id;
            $context['tousername'] = $user->username;
        }

        if (!empty($user->mailformat) && $user->mailformat == 1) {
            // Only process html templates if the user preferences allow html email.

            if (!$messagehtml) {
                // If no html has been given, BUT there is an html wrapping template then
                // auto convert the text to html and then wrap it.
                $messagehtml = trim(text_to_html($messagetext));
            }
            $context['body'] = $messagehtml;
            $messagehtml = $renderer->render_from_template('core/email_html', $context);
        }

        $context['body'] = html_to_text(nl2br($messagetext));
        $mail->Subject = $renderer->render_from_template('core/email_subject', $context);
        $mail->FromName = $renderer->render_from_template('core/email_fromname', $context);
        $messagetext = $renderer->render_from_template('core/email_text', $context);

        // Autogenerate a MessageID if it's missing.
        if (empty($mail->MessageID)) {
            $mail->MessageID = generate_email_messageid();
        }

        if ($messagehtml && !empty($user->mailformat) && $user->mailformat == 1) {
            // Don't ever send HTML to users who don't want it.
            $mail->isHTML(true);
            $mail->Encoding = 'quoted-printable';
            $mail->Body = $messagehtml;
            $mail->AltBody = "\n$messagetext\n";
        } else {
            $mail->IsHTML(false);
            $mail->Body = "\n$messagetext\n";
        }

        if ($attachment && $attachname) {
            if (preg_match( "~\\.\\.~" , $attachment )) {
                // Security check for ".." in dir path.
                $supportuser = core_user::get_support_user();
                $temprecipients[] = array($supportuser->email, fullname($supportuser, true));
                $mail->addStringAttachment('Error in attachment.  User attempted to attach a filename with a unsafe name.', 'error.txt', '8bit', 'text/plain');
            } else {
                require_once($CFG->libdir.'/filelib.php');
                $mimetype = mimeinfo('type', $attachname);

                // Before doing the comparison, make sure that the paths are correct (Windows uses slashes in the other direction).
                // The absolute (real) path is also fetched to ensure that comparisons to allowed paths are compared equally.
                $attachpath = str_replace('\\', '/', realpath($attachment));

                // Build an array of all filepaths from which attachments can be added (normalised slashes, absolute/real path).
                $allowedpaths = array_map(function(string $path): string {
                    return str_replace('\\', '/', realpath($path));
                }, [
                    $CFG->cachedir,
                    $CFG->dataroot,
                    $CFG->dirroot,
                    $CFG->localcachedir,
                    $CFG->tempdir,
                    $CFG->localrequestdir,
                ]);

                // Set addpath to true.
                $addpath = true;

                // Check if attachment includes one of the allowed paths.
                foreach (array_filter($allowedpaths) as $allowedpath) {
                    // Set addpath to false if the attachment includes one of the allowed paths.
                    if (strpos($attachpath, $allowedpath) === 0) {
                        $addpath = false;
                        break;
                    }
                }

                // If the attachment is a full path to a file in the multiple allowed paths, use it as is,
                // otherwise assume it is a relative path from the dataroot (for backwards compatibility reasons).
                if ($addpath == true) {
                    $attachment = $CFG->dataroot . '/' . $attachment;
                }

                $mail->addAttachment($attachment, $attachname, 'base64', $mimetype);
            }
        }

        // Check if the email should be sent in an other charset then the default UTF-8.
        if ((!empty($CFG->sitemailcharset) || !empty($CFG->allowusermailcharset))) {

            // Use the defined site mail charset or eventually the one preferred by the recipient.
            $charset = $CFG->sitemailcharset;
            if (!empty($CFG->allowusermailcharset)) {
                if ($useremailcharset = get_user_preferences('mailcharset', '0', $user->id)) {
                    $charset = $useremailcharset;
                }
            }

            // Convert all the necessary strings if the charset is supported.
            $charsets = get_list_of_charsets();
            unset($charsets['UTF-8']);
            if (in_array($charset, $charsets)) {
                $mail->CharSet  = $charset;
                $mail->FromName = core_text::convert($mail->FromName, 'utf-8', strtolower($charset));
                $mail->Subject  = core_text::convert($mail->Subject, 'utf-8', strtolower($charset));
                $mail->Body     = core_text::convert($mail->Body, 'utf-8', strtolower($charset));
                $mail->AltBody  = core_text::convert($mail->AltBody, 'utf-8', strtolower($charset));

                foreach ($temprecipients as $key => $values) {
                    $temprecipients[$key][1] = core_text::convert($values[1], 'utf-8', strtolower($charset));
                }
                foreach ($tempreplyto as $key => $values) {
                    $tempreplyto[$key][1] = core_text::convert($values[1], 'utf-8', strtolower($charset));
                }
            }
        }

        foreach ($temprecipients as $values) {
            $mail->addAddress($values[0], $values[1]);
        }
        foreach ($tempreplyto as $values) {
            $mail->addReplyTo($values[0], $values[1]);
        }

        if (!empty($CFG->emaildkimselector)) {
            $domain = substr(strrchr($mail->From, "@"), 1);
            $pempath = "{$CFG->dataroot}/dkim/{$domain}/{$CFG->emaildkimselector}.private";
            if (file_exists($pempath)) {
                $mail->DKIM_domain      = $domain;
                $mail->DKIM_private     = $pempath;
                $mail->DKIM_selector    = $CFG->emaildkimselector;
                $mail->DKIM_identity    = $mail->From;
            } else {
                debugging("Email DKIM selector chosen due to {$mail->From} but no certificate found at $pempath", DEBUG_DEVELOPER);
            }
        }
//echo($CFG->supportemail);
//echo("*********************************");		
$mail->From=$CFG->supportemail;
$mail->addReplyTo($USER->contactfromemail);
//var_dump($mail);
//exit;
        if ($mail->send()) {
            set_send_count($user);
            if (!empty($mail->SMTPDebug)) {
                echo '</pre>';
            }
            return true;
        } else {
            // Trigger event for failing to send email.
            $event = \core\event\email_failed::create(array(
                'context' => context_system::instance(),
                'userid' => $from->id,
                'relateduserid' => $user->id,
                'other' => array(
                    'subject' => $subject,
                    'message' => $messagetext,
                    'errorinfo' => $mail->ErrorInfo
                )
            ));
            $event->trigger();
            if (CLI_SCRIPT) {
                mtrace('Error: lib/moodlelib.php email_to_user(): '.$mail->ErrorInfo);
            }
            if (!empty($mail->SMTPDebug)) {
                echo '</pre>';
            }
            return false;
        }
    }
}

