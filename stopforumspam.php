<?php
/**
* Joomla Community Builder User Plugin: plug_stopforumspam
* @version 1.0
* @package plug_stopforumspam
* @subpackage stopforumspam.php
* @author Carsten Schmitz
* @copyright (C) Carsten Schmitz, www.liemsurvey.org
* @license Limited  http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @final 1.0
*/

/** ensure this file is being included by a parent file **/
if ( ! ( defined( '_VALID_CB' ) || defined( '_JEXEC' ) || defined( '_VALID_MOS' ) ) ) { die( 'Direct Access to this location is not allowed.' ); }


$_PLUGINS->registerFunction( 'onBeforeUserRegistration' , 'checkUserAgainstStopForumSpam', 'getstopforumspam' ); 

class getstopforumspam extends cbTabHandler {
    /**
    * Construnctor
    */
    function getstopforumspam() {
        $this->cbTabHandler();
    }

    function checkUserAgainstStopForumSpam(&$row) {
        global $_PLUGINS, $_CB_framework;

        $oParams = $this->params; // get parameters (plugin and related tab)

        $bCheckEmail = $oParams->get('check_email','1');
        $bCheckIP = $oParams->get('check_ip','1');
        $iConfidenceEmail = (int)$oParams->get('confidence_email','10');
        $iConfidenceIP = (int)$oParams->get('confidence_ip','15');
        $_SERVER['REMOTE_ADDR']='199.115.117.235';
        $sURL="http://www.stopforumspam.com/api?email=".urlencode($row->email)."&ip=".urlencode($_SERVER['REMOTE_ADDR']).'&f=json';
        $sJSON = file_get_contents($sURL);    
        if ($sJSON){
            $aInfo=json_decode($sJSON,true);
            if ($bCheckEmail && isset($aInfo['email']) && isset($aInfo['email']['confidence']))
            {
                if ($aInfo['email']['confidence']>$iConfidenceEmail)
                {
                    // found a match now error out
                    $_PLUGINS->raiseError(0);
                    $_PLUGINS->_setErrorMSG("You are banned from registering on this website.");
                }   
            }
            if ($bCheckIP && isset($aInfo['ip']) && isset($aInfo['ip']['confidence']))
            {
                if ($aInfo['ip']['confidence']>$iConfidenceIP)
                {
                    // found a match now error out
                    $_PLUGINS->raiseError(0);
                    $_PLUGINS->_setErrorMSG("You are banned from registering on this website.");
                }   
            }
        }
        return true;
    }    



} // end of getstopforumspam class
?>