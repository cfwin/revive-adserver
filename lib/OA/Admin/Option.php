<?php

/*
+---------------------------------------------------------------------------+
| Openads v${RELEASE_MAJOR_MINOR}                                           |
| ============                                                              |
|                                                                           |
| Copyright (c) 2003-2007 Openads Limited                                   |
| For contact details, see: http://www.openads.org/                         |
|                                                                           |
| Copyright (c) 2000-2003 the phpAdsNew developers                          |
| For contact details, see: http://www.phpadsnew.com/                       |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id: lib-settings.inc.php 12449 2007-11-15 13:40:06Z miguel.correa@openads.org $
*/

// Required files
require_once MAX_PATH . '/lib/OA/Admin/Settings.php';
require_once MAX_PATH . '/lib/OA/Admin/Template.php';
require_once MAX_PATH . '/lib/max/language/Default.php';
require_once MAX_PATH . '/lib/max/language/Settings.php';
require_once MAX_PATH . '/lib/max/language/SettingsHelp.php';

/**
 * A class to deal with the display of settings and preferences
 *
 * @package    OpenadsAdmin
 * @author     Miguel Correa <miguel.correa@openads.org>
 */
class OA_Admin_Option
{
    /**
     * @var object OA_Admin_Template container
     */
    var $oTpl;

    var $_optionType;

    /**
     * The constructor method.
     *
     * Requires, includes and loads the the required files.
     *
     * @param string $optionType One of "settings" or "preferences", depending on if
     *                           the options are to be displayed in the Settings or
     *                           the Preferences section.
     */
    function OA_Admin_Option($optionType)
    {
        // Load the required language files
        Language_Default::load();
        Language_Settings::load();
        Language_SettingsHelp::load();

        // Determine whether the config file is locked or not
        $phpAds_config_locked = !OA_Admin_Settings::isConfigWritable();

        // Set the supplied Settings or Preferences information
        $this->_optionType = 'account-' . $optionType;

        //	Setup template object
        $this->oTpl = new OA_Admin_Template('option.html');
    }

    /**
     * Write Javascripts functions needed on selection() method
     *
     * @access private
     */
    function _writeJavascriptFunctions()
    {
        echo "<script language='JavaScript'>\n<!--\n\n";
        echo"function options_goto_section()\n";
        echo"{\n";
        echo"    s = document.settings_selection.section.selectedIndex;\n";
        echo"    s = document.settings_selection.section.options[s].value;\n\n";

        echo"    document.location = '".$this->_optionType."-' + s + '.php';\n";
        echo"}\n\n";

        echo"function phpAds_UsertypeChange(o)\n";
        echo"{\n";
        echo"    var v = 0;\n";
        echo"    var base_name = o.name.replace(/_\d+$/, '');\n";
        echo"    var l;\n\n";

        echo"    for (var i = 1; i <= 8; i <<= 1) {\n";

        echo"        if (o = findObj(base_name + '_' + i)) {\n";
        echo"            v += o.checked ? i : 0\n\n";

        echo"            if (l = findObj(base_name + '_label[' + i +']')) {\n";
        echo"                l.disabled = !o.checked;\n";
        echo"            }\n";
        echo"            if (l = findObj(base_name + '_rank[' + i +']')) {\n";
        echo"                l.disabled = !o.checked;\n";
        echo"            }\n";
        echo"        }\n";
        echo"    }\n\n";

        echo"    if (o = findObj(base_name))\n";
        echo"        o.value = v;\n";
        echo"}\n\n";

        echo "// -->\n</script>";
    }

    /**
     * Build a menu with all settings or preferences
     *
     * @param string $section The drop down section name.
     */
    function selection($section)
    {
        global $phpAds_TextDirection, $strHelp;
        global $tabindex;
        if(!isset($tabindex)) $tabindex = 1;

        $this->_writeJavascriptFunctions();

        echo "<table border='0' width='100%' cellpadding='0' cellspacing='0'><tr>";

        /**
         *  @todo After have a way to know if a user is in the admin role, manager role, publisher role
         *        or advertiser role we'll create a personalized "Choose section" listBox
         *        check strMainSettings
         */
        if ($this->_optionType == 'account-settings') {
            $sections = array(
                'banner-delivery' => array('name' => $GLOBALS['strBannerDelivery'],       'perm' => OA_ACCOUNT_ADMIN),
                'banner-logging'  => array('name' => $GLOBALS['strBannerLogging'],        'perm' => OA_ACCOUNT_ADMIN),
                'banner-storage'  => array('name' => $GLOBALS['strBannerStorage'],        'perm' => OA_ACCOUNT_ADMIN),
                'database'        => array('name' => $GLOBALS['strDatabaseSettings'],     'perm' => OA_ACCOUNT_ADMIN),
                'debug'           => array('name' => $GLOBALS['strDebug'],                'perm' => OA_ACCOUNT_ADMIN),
                'email'           => array('name' => $GLOBALS['strEmailSettings'],        'perm' => OA_ACCOUNT_ADMIN),
                'geotargeting'    => array('name' => $GLOBALS['strGeotargetingSettings'], 'perm' => OA_ACCOUNT_ADMIN),
                'maintenance'     => array('name' => $GLOBALS['strMaintenanceSettings'],  'perm' => OA_ACCOUNT_ADMIN),
                'synchronisation' => array('name' => $GLOBALS['strSyncSettings'],         'perm' => OA_ACCOUNT_ADMIN),
                'user-interface'  => array('name' => $GLOBALS['strGuiSettings'],          'perm' => OA_ACCOUNT_ADMIN),
            );
        } elseif ($this->_optionType == 'account-preferences') {
            $sections = array(
                'account' => array(
                    'name' => $GLOBALS['strAccountPreferences'],
                    'perm' => array(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER)
                ),
                'banner' => array(
                    'name' => $GLOBALS['strBannerPreferences'],
                    'perm' => array(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER)
                ),
                'campaign-email-reports' => array(
                    'name' => $GLOBALS['strCampaignEmailReportsPreferences'],
                    'perm' => array(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER)
                ),
                'language-timezone' => array(
                    'name' => $GLOBALS['strLanguageTimezonePreferences'],
                    'perm' => array(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER)
                ),
                /*
                'tracker' => array(
                    'name' => $GLOBALS['strTrackerPreferences'],
                    'perm' => array(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER)
                ),
                */
                'user-interface' => array(
                    'name' => $GLOBALS['strUserInterfacePreferences'],
                    'perm' => array(OA_ACCOUNT_ADMIN, OA_ACCOUNT_MANAGER, OA_ACCOUNT_ADVERTISER, OA_ACCOUNT_TRAFFICKER)
                )
            );
        }

        echo "<td><form name='settings_selection'><td height='35'><b>";
        echo $GLOBALS['strChooseSection'].":&nbsp;</b>";
        echo "<select name='section' onChange='options_goto_section();' tabindex='".($tabindex++)."'>";
        foreach ($sections as $k => $v) {
            if (OA_Permission::isAccount($v['perm'])) {
                echo "<option value='{$k}'".($section == $k ? ' selected' : '').">{$v['name']}</option>";
            }
        }
        echo "</select>&nbsp;<a href='javascript:void(0)' onClick='options_goto_section();'>";
        echo "<img src='images/".$phpAds_TextDirection."/go_blue.gif' border='0'></a>";
        echo "</td></form></tr></table>";
        phpAds_ShowBreak();
    }

    /**
     * Build and display the settings or preferences user interface
     *
     * @param array $aData
     * @param array $aErrors
     * @param integer $disableSubmit
     * @param string $imgPath
     */
    function show($aData, $aErrors = array(), $disableSubmit = 0, $imgPath = "")
    {
        $aConf = $GLOBALS['_MAX']['CONF'];
        $aPref = $GLOBALS['_MAX']['PREF'];

        // Initialize tabindex (if not already done)
        global $tabindex;
        if (!isset($tabindex)) {
            $tabindex = 1;
        }

        // Determine if config file is writable
        $aConfigLocked = !OA_Admin_Settings::isConfigWritable();
        $image = $this->configLocked ? 'closed' : 'open';

        $dependbuffer   = "function phpAds_refreshEnabled() {\n";
        $checkbuffer    = '';
        $usertypebuffer = '';
        $helpbuffer     = '';
        $i = 0;
        $count = count($aData);
        for ($i = 0; $i < $count; $i++) {
            $section = $aData[$i];
            if (!isset($section['visible']) || $section['visible']) {
                if (isset($aErrors[$i])) {
                    $this->_showStartSection($section['text'], $aErrors[$i], $disableSubmit, $imgPath);
                } else {
                    $this->_showStartSection($section['text'], NULL, $disableSubmit, $imgPath);
                }
                foreach ($section['items'] as $aItem) {
                    if (!isset($aItem['visible']) || $aItem['visible']) {
                        if (!$aItem['enabled']) {
                            $aItem['enabled'] = $this->_showLocked($aItem);
                            $dependbuffer .= $this->_showCheckDependancies($aData, $aItem);
                        }
                        if (count($aErrors)) {
                            // Page is the result of an error message, get values from the input
                            $value = '';
                            if (isset($aItem['name']) && isset($GLOBALS[$aItem['name']])) {
                                $value = $GLOBALS[$aItem['name']];
                                if ($aErrors[0] != MAX_ERROR_YOU_HAVE_NO_TRACKERS && $aErrors[0] != MAX_ERROR_YOU_HAVE_NO_CAMPAIGNS) {
                                    if (isset($GLOBALS[$aItem['name'].'_defVal'])) {
                                        $value = $GLOBALS[$aItem['name'].'_defVal'];
                                    }
                                }
                            }
                        } else {
                            // Get the values from the config file
                            $value = '';
                            if (isset($aItem['name'])) {
                                // Split into config sections
                                $aConfixExploded = explode('_', $aItem['name']);
                                $aConfigLevel = isset($aConfixExploded[0]) ? $aConfixExploded[0] : null;
                                $aConfigItem = isset($aConfixExploded[1]) ? $aConfixExploded[1] : null;
                                if (isset($GLOBALS[$aItem['name'].'_defVal'])) {
                                    // Load value from globals if set
                                    $value = $GLOBALS[$aItem['name'].'_defVal'];
                                } elseif (isset($aConf[$aConfigLevel][$aConfigItem])) {
                                    // Load the configuration .php file value
                                    $value = $aConf[$aConfigLevel][$aConfigItem];
                                } elseif (isset($aConf[$aItem['name']][0])) {
                                    // Configuration .php file item is stored as an array,
                                    // re-constitute into a comma separated list
                                    $value = implode(', ', $aConf[$aItem['name']]);
                                } elseif (isset($aPref[$aItem['name']]['value'])) {
                                    // Are we displaying a preference value to a non-admin account?
                                    $accountType = OA_Permission::getAccountType();
                                    if ($accountType == OA_ACCOUNT_ADVERTISER || $accountType == OA_ACCOUNT_TRAFFICKER) {
                                        // Check to see if the preference value should be displayed or disabled
                                        $valueType = $aPref[$aItem['name']]['account_type'];
                                        if ($valueType == OA_ACCOUNT_MANAGER) {
                                            // Disable the preference, so that it can be seen, but not altered
                                            $aItem['depends'] = false;
                                        } else if ($valueType == OA_ACCOUNT_ADVERTISER && $valueType == OA_ACCOUNT_TRAFFICKER) {
                                            // Hide the preference from being seen!
                                            $aItem['visible'] = false;
                                        } else if ($valueType == OA_ACCOUNT_TRAFFICKER && $valueType == OA_ACCOUNT_ADVERTISER) {
                                            // Hide the preference from being seen!
                                            $aItem['visible'] = false;
                                        }

                                    }
                                    // Load the preference value
                                    $value = $aPref[$aItem['name']]['value'];
                                } elseif (isset($aItem['value'])) {
                                    $value = $aItem['value'];
                                }
                            }
                        }
                        switch ($aItem['type']) {
                            case 'plaintext': $this->_showPlainText($aItem); break;
                            case 'break':     $this->_showBreak($aItem, $imgPath); break;
                            case 'checkbox':  $this->_showCheckbox($aItem, $value); break;
                            case 'text':      $this->_showText($aItem, $value); break;
                            case 'url':       $this->_showUrl($aItem, $value); break;
                            case 'urln':      $this->_showUrl($aItem, $value, 'n'); break;
                            case 'urls':      $this->_showUrl($aItem, $value, 's'); break;
                            case 'textarea':  $this->_showTextarea($aItem, $value); break;
                            case 'password':  $this->_showPassword($aItem, $value); break;
                            case 'select':    $this->_showSelect($aItem, $value, $disableSubmit); break;
                            case 'usertype_textboxes':
                                $this->_showUsertypeTextboxes($aItem, $value);
                                break;
                            case 'usertype_checkboxes':
                                $this->_showUsertypeCheckboxes($aItem, $value);
                                $usertypebuffer .= "phpAds_UsertypeChange(findObj('".$aItem['name']."'));\n";
                                break;
                        }
                        if (isset($aItem['check']) || isset($aItem['req'])) {
                            if (!isset($aItem['check'])) {
                                $aItem['check'] = '';
                            }
                            if (!isset($aItem['req'])) {
                                $aItem['req'] = false;
                            }
                            $checkbuffer .= "max_formSetRequirements('".$aItem['name']."', '".addslashes($aItem['text'])."', ".($aItem['req'] ? 'true' : 'false').", '".$aItem['check']."');\n";
                            if (isset($aItem['unique'])) {
                                $checkbuffer .= "max_formSetUnique('".$aItem['name']."', '|".addslashes(implode('|', $aItem['unique']))."|');\n";
                            }
                        }
                        if (isset($aItem['name'])) {
                            $helpbuffer .= $this->_help($aItem['name']);
                        }
                    }
                }
                $this->_showEndSection();
            }
        }

        if (OA_INSTALLATION_STATUS == OA_INSTALLATION_STATUS_INSTALLED)
        {
            if ($disableSubmit != 0) {
                $max_file_size = $this->_display_to_bytes(ini_get('upload_max_filesize'));
                $max_post_size = $this->_display_to_bytes(ini_get('post_max_size'));
                if (($max_post_size > 0) && ($max_post_size < $max_file_size)) {
                    $max_file_size = $max_post_size;
                }
                $this->oTpl->assign('max_file_size',    $max_file_size);
                $this->oTpl->assign('max_post_size',    $max_post_size);
            }
        }

        $this->oTpl->assign('this',             $this);
        $this->oTpl->assign('aOption',          $this->aOption);
        $this->oTpl->assign('configLocked',     $aConfigLocked);
        $this->oTpl->assign('image',            $image);
        $this->oTpl->assign('formUrl',          $_SERVER['PHP_SELF']);
        $this->oTpl->assign('checkbuffer',      $checkbuffer);
        $this->oTpl->assign('dependbuffer',     $dependbuffer);
        $this->oTpl->assign('usertypebuffer',   $usertypebuffer);
        $this->oTpl->assign('GLOBALS',          $GLOBALS);
        $this->oTpl->display();
    }




    /*-------------------------------------------------------*/
    /* Return Settings Help HTML Code                        */
    /*-------------------------------------------------------*/

    function _help($name)
    {
        if (!isset($GLOBALS['phpAds_hlp_'.$name])) {
            $GLOBALS['phpAds_hlp_'.$name] = '';
        }
        $string = $GLOBALS['phpAds_hlp_'.$name];
        $string = ereg_replace ("[\n\r\t]", " ", $string);
        $string = ereg_replace ("[ ]+", " ", $string);
        $string = str_replace("'", "\\'", $string);
        $string = trim ($string);
        return "helpArray['$name'] = '".$string."';\n";
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $aData
     * @param unknown_type $aItem
     * @return unknown
     */
    function _showCheckDependancies($aData, $aItem)
    {
        global $phpAds_config_locked;
        $formName = empty($GLOBALS['settings_formName'])?'settingsform' :$GLOBALS['settings_formName'];
        if (isset($aItem['depends'])) {
            //$miArray  = split('[ & ]+', $aItem['depends']);
            $depends    = split('[ ]+', $aItem['depends']);
            $javascript = "\tenabled = (";
            $result     = true;
            foreach ($depends as $word) {
                if (ereg('[\&\|]{1,2}', $word)) {
                    // Operator
                    $javascript .= " ".$word." ";
                } else {
                    // Assignment
                    eregi ("^(\(?)([a-z0-9_-]+)([\=\!\<\>]{1,2})([\"\'a-z0-9_-]+)(\)?)$", $word, $regs);
                    $type          = $this->_showGetType($aData, $regs[2]);
                    if ($phpAds_config_locked) $javascript .= $regs[1]."document.".$formName.".".$regs[2].".enabled && ";
                    $javascript .= $regs[1]."document.".$formName.".".$regs[2].".";
                    switch ($type){
                        case 'checkbox':    $javascript .= 'checked'; break;
                        case 'select':      $javascript .= 'selectedIndex'; break;
                        default:            $javascript .= 'value'; break;
                    }
                    $javascript .= " ".$regs[3]." ".$regs[4].$regs[5];
                }
            }
            $javascript .= ");\n";
            $javascript .= "\tdocument.".$formName.".".$aItem['name'].".disabled = !enabled;\n";
            $javascript .= "\tobj = findObj('cell_".$aItem['name']."'); if (enabled) { obj.className = 'cellenabled'; } else { obj.className =  'celldisabled'; }\n";
            $javascript .= "\t\n";
            return ($javascript);
        }
        return ('');
    }


    /*-------------------------------------------------------*/
    /* Settings GUI Functions Wrappers                       */
    /*-------------------------------------------------------*/

    function _showGetType ($aData, $name)
    {
        foreach ($aData as $section) {
            foreach ($section['items'] as $aItem) {
                if (isset($aItem['name']) && $aItem['name'] == $name) {
                    return ($aItem['type']);
                }
            }
        }
        return false;
    }


    function _showStartSection($name, $error = array(), $disableSubmit=0, $imgPath="")
    {
        $aConf = $GLOBALS['_MAX']['CONF'];
        $icon = (OA_INSTALLATION_STATUS != OA_INSTALLATION_STATUS_INSTALLED) ? 'setup' : 'settings';

        $aItem['name'] = $name;
        $aItem['error'] = $error;
        $aItem['icon'] = $icon;
        $aItem['disabledSubmit'] = $disableSubmit;
        $aItem['imgPath'] = $imgPath;
        $this->aOption[] = array('startsection.html' => $aItem);
    }

    function _showEndSection()
    {
        $this->aOption[] = array('endsection.html' => array());
    }

    function _showPlainText($aItem)
    {
        $this->aOption[] = array('plaintext.html' => $aItem);
    }

    function _showBreak($aItem, $imgPath='')
    {
        $aItem['imgPath'] = $imgPath;

        $this->aOption[] = array('break.html' => $aItem);
    }

    function _showCheckbox($aItem, $value)
    {
        global $tabindex;

        $aItem['tabindex'] = $tabindex++;

        // Make sure that 'f' for enums is also considered
        $value = !empty($value) && (bool)strcasecmp($value, 'f');
        $aItem['value'] = $value;

        $this->aOption[] = array('checkbox.html' => $aItem);
    }

    function _showText($aItem, $value)
    {
        global $tabindex;

        $aItem['tabindex'] = $tabindex;
        $aItem['value'] = htmlspecialchars($value);

        if (!isset($aItem['size'])) {
            $aItem['size'] = 25;
        }

        $this->aOption[] = array('text.html' => $aItem);
    }

    function _showUrl($aItem, $value, $type = '')
    {
        global $tabindex;

        $aItem['tabindex'] = $tabindex;
        $aItem['value'] = htmlspecialchars($value);
        $aItem['type'] = $type;

        if (!isset($aItem['size'])) {
            $aItem['size'] = 25;
        }

        $this->aOption[] = array('url.html' => $aItem);
    }

    function _showTextarea($aItem, $value)
    {
        global $tabindex;

        $aItem['tabindex'] = $tabindex;
        $aItem['value'] = htmlspecialchars($value);

        if (!isset($aItem['rows'])) {
            $aItem['rows'] = 5;
        }

        $this->aOption[] = array('textarea.html' => $aItem);
    }

    function _showPassword($aItem, $value)
    {
        global $tabindex;

        if (!isset($aItem['size'])) {
            $aItem['size'] = 25;
        }

        //  if config file is not writeable do not display password
        $hidePassword = false;
        $writeable = OA_Admin_Settings::isConfigWritable();

        if ($aItem['name'] == 'database_password' && !$writeable) {
            $value = 'password';
            $hidePassword = true;
        }

        $aItem['value'] = $value;
        $aItem['hidePassword'] = $hidePassword;
        $aItem['tabindex'] = $tabindex;

        $this->aOption[] = array('password.html' => $aItem);
    }

    function _showSelect($aItem, $value, $showSubmitButton=0)
    {
        global $tabindex;

        $aItem['tabindex'] = $tabindex;
        $aItem['value'] = $value;
        $aItem['showSubmitButton'] = $showSubmitButton;

        foreach ($aItem['items'] as $k => $v) {
            $k = htmlspecialchars($k);
            $aItem['items'][$k] = $v;
        }

        $this->aOption[] = array('select.html' => $aItem);


    }

    function _showUsertypeCheckboxes($aItem, $value)
    {
        global $tabindex;

        $aItem['tabindex'] = $tabindex;
        $aItem['value'] = $value ? (int)$value : 0;

        $this->oTpl->assign('isAdmin', OA_Permission::isAccount(OA_ACCOUNT_ADMIN));
        $this->oTpl->assign('isManager', OA_Permission::isAccount(OA_ACCOUNT_MANAGER));
        $this->oTpl->assign('isAdvertiser', OA_Permission::isAccount(OA_ACCOUNT_ADVERTISER));
        $this->oTpl->assign('isTrafficker', OA_Permission::isAccount(OA_ACCOUNT_TRAFFICKER));
        $this->_assignAccountsIds();
        $this->aOption[] = array('usertype-checkboxes.html' => $aItem);
    }

    function _assignAccountsIds()
    {
        $this->oTpl->assign('OA_ACCOUNT_ADMIN_ID',      OA_ACCOUNT_ADMIN_ID);
        $this->oTpl->assign('OA_ACCOUNT_MANAGER_ID',    OA_ACCOUNT_MANAGER_ID);
        $this->oTpl->assign('OA_ACCOUNT_ADVERTISER_ID', OA_ACCOUNT_ADVERTISER_ID);
        $this->oTpl->assign('OA_ACCOUNT_TRAFFICKER_ID', OA_ACCOUNT_TRAFFICKER_ID);
    }

    function _showUsertypeTextboxes($aItem, $value)
    {
        global $tabindex;

        $aItem['tabindex'] = $tabindex;

        $value = unserialize($value);
        foreach ($value as $key => $value) {
            $aItem['value'][$key] = htmlspecialchars($value);
        }
        $this->_assignAccountsIds();
        $this->aOption[]    = array('usertype-textboxes.html' => $aItem);
    }

    function _showPadLock($aItem)
    {
        if ($this->_showLocked($aItem) || $aItem['enabled']==true) {
            return '<img src="images/padlock-closed.gif">';
        } else {
            return '&nbsp;';
        }
    }

    function _showLocked($aItem)
    {
        $aConf = $GLOBALS['_MAX']['CONF'];
        //if ($aConf['openads']['installed'] && isset($aItem['name']))
        if ((OA_INSTALLATION_STATUS == OA_INSTALLATION_STATUS_INSTALLED) && isset($aItem['name']))
        {
            // Split into config sections
            $aConfixExploded = explode('_', $aItem['name']);
            $aConfigLevel = isset($aConfixExploded[0]) ? $aConfixExploded[0] : null;
            $aConfigItem = isset($aConfixExploded[1]) ? $aConfixExploded[1] : null;
            //list($aConfigLevel, $aConfigItem) = explode('_', $aItem['name']);
            if (isset($aConf[$aConfigLevel][$aConfigItem]) && (!OA_Admin_Settings::isConfigWritable())) {
                return true;
            }
        }
        return false;
    }

    function _display_to_bytes($val) {
        $val = trim($val);
        $last = strtolower($val{strlen($val)-1});
        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }
        return $val;
    }

    function _bytes_to_display($val) {
        $val=(float)$val;
        if ($val < 1024) {
            return number_format($val, 0)."b";
        } elseif ($val < 1048576) {
            return number_format($val/1024, 1)."KB";
        } elseif ($val >= 1048576) {
            return number_format($val/1048576, 1)."MB";
        } else {
            return false;
        }
    }

}

?>