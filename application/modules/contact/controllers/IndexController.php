<?php
/**
 * This controller manages the contact page
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class Contact_IndexController extends Zend_Controller_Action
{
    // Session namespace
    private $_token;

    public function init()
    {
        $this->_token = new Zend_Session_Namespace('emailToken');
        $this->_token->setExpirationSeconds(3600);
        if (!isset($this->_token->id))
            $this->_token->id = mt_rand();
    }

    public function indexAction()
    {
        $validator = new Zend_Validate_EmailAddress();
        $cfg = Zend_Registry::get('config');
        
        // Make sure we have an address to send it to
        if (empty($cfg['sitecontact']))
            throw new Exception("This module is disabled due to missing contact info.");
        
        // Init captcha engine
        if (!empty($cfg['republickey']) && !empty($cfg['reprivatekey'])) {
            $captcha = new Zend_Form_Element_Captcha('captcha', array('captcha' => array('captcha' => 'ReCaptcha', 'PrivKey' => $cfg['reprivatekey'], 'PubKey' => $cfg['republickey'])));
        } else {
            $captcha = new Zend_Form_Element_Captcha('captcha', array('captcha' => array('captcha' => 'Dumb', 'wordLen' => 6)));
        }
        $captcha->removeDecorator('HtmlTag');
        $this->view->assign("captcha", $captcha);
        
        
        if (isset($_POST['contact_submit'])) {
            // check for our security/anti-spam token
            if (!isset($this->_token->id) || sha1($this->_token->id) != $this->_getParam('token'))
                throw new Exception("Bad token.  Please check that cookies are enabled for this site.");
            if ($captcha->isValid($_POST) && $validator->isValid($_POST['email'])) {
                mail($cfg['sitecontact'], $_REQUEST['subject'], $_REQUEST['message'], "From: " . $_POST['email'] . "\r\n") or die("Mail configuration error.\n");
                Zend_Registry::get('log')->log('Mail sent from ' . $_POST['email'] . ' with IP ' . $_SERVER['REMOTE_ADDR'], Zend_Log::NOTICE);
                $this->render('thanks');
            }
        }
        
        $this->view->assign("token", sha1($this->_token->id));
        $this->view->assign("valid", $validator);
    }
}

