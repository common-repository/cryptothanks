<?php
/*
Plugin Name:  Cryptothanks
Plugin URI:   https://mudimedia.com/wordpress-plugins/cryptothanks
Description:  This is the plugin where your visitors make payment to you. You can change the label of the button to make it either donation or payment button.
Version:      0.2.4
Author:       Mudimedia Software
Author URI:   https://mudimedia.com/
License:      GPLv2
License URI:  http://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  mudimedia
Domain Path:  /languages
*/

$CryptoThanks_minimalRequiredPhpVersion = '5.0';

/**
 * Check the PHP version and give a useful error message if the user's version is less than the required version
 * @return boolean true if version check passed. If false, triggers an error which WP will handle, by displaying
 * an error message on the Admin page
 */
function CryptoThanks_noticePhpVersionWrong() {
    global $CryptoThanks_minimalRequiredPhpVersion;
    echo '<div class="updated fade">' .
      __('Error: plugin "CryptoThanks" requires a newer version of PHP to be running.',  'cryptothanks').
            '<br/>' . __('Minimal version of PHP required: ', 'cryptothanks') . '<strong>' . $CryptoThanks_minimalRequiredPhpVersion . '</strong>' .
            '<br/>' . __('Your server\'s PHP version: ', 'cryptothanks') . '<strong>' . phpversion() . '</strong>' .
         '</div>';
}


function CryptoThanks_PhpVersionCheck() {
    global $CryptoThanks_minimalRequiredPhpVersion;
    if (version_compare(phpversion(), $CryptoThanks_minimalRequiredPhpVersion) < 0) {
        add_action('admin_notices', 'CryptoThanks_noticePhpVersionWrong');
        return false;
    }
    return true;
}


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function CryptoThanks_i18n_init() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('cryptothanks', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi','CryptoThanks_i18n_init');

// Run the version check.
// If it is successful, continue with initialization for this plugin
if (CryptoThanks_PhpVersionCheck()) {
    // Only load and run the init function if we know PHP version can parse it
    include_once('cryptothanks_init.php');
    CryptoThanks_init(__FILE__);
}
