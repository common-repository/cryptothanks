<?php

class CryptoThanks_OptionsManager {

    public function getOptionNamePrefix() {
        return get_class($this) . '_';
    }


    /**
     * Define your options meta data here as an array, where each element in the array
     * @return array of key=>display-name and/or key=>array(display-name, choice1, choice2, ...)
     * key: an option name for the key (this name will be given a prefix when stored in
     * the database to ensure it does not conflict with other plugin options)
     * value: can be one of two things:
     *   (1) string display name for displaying the name of the option to the user on a web page
     *   (2) array where the first element is a display name (as above) and the rest of
     *       the elements are choices of values that the user can select
     * e.g.
     * array(
     *   'item' => 'Item:',             // key => display-name
     *   'rating' => array(             // key => array ( display-name, choice1, choice2, ...)
     *       'CanDoOperationX' => array('Can do Operation X', 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber'),
     *       'Rating:', 'Excellent', 'Good', 'Fair', 'Poor')
     */
    public function getOptionMetaData() {
        return array();
    }

    /**
     * @return array of string name of options
     */
    public function getOptionNames() {
        return array_keys($this->getOptionMetaData());
    }

    /**
     * Override this method to initialize options to default values and save to the database with add_option
     * @return void
     */
    protected function initOptions() {
    }

    /**
     * Cleanup: remove all options from the DB
     * @return void
     */
    protected function deleteSavedOptions() {
        $optionMetaData = $this->getOptionMetaData();
        if (is_array($optionMetaData)) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                $prefixedOptionName = $this->prefix($aOptionKey); // how it is stored in DB
                delete_option($prefixedOptionName);
            }
        }
    }

    /**
     * @return string display name of the plugin to show as a name/title in HTML.
     * Just returns the class name. Override this method to return something more readable
     */
    public function getPluginDisplayName() {
        return get_class($this);
    }

    /**
     * Get the prefixed version input $name suitable for storing in WP options
     * Idempotent: if $optionName is already prefixed, it is not prefixed again, it is returned without change
     * @param  $name string option name to prefix. Defined in settings.php and set as keys of $this->optionMetaData
     * @return string
     */
    public function prefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) { // 0 but not false
            return $name; // already prefixed
        }
        return $optionNamePrefix . $name;
    }

    /**
     * Remove the prefix from the input $name.
     * Idempotent: If no prefix found, just returns what was input.
     * @param  $name string
     * @return string $optionName without the prefix.
     */
    public function &unPrefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) {
            return substr($name, strlen($optionNamePrefix));
        }
        return $name;
    }

    /**
     * A wrapper function delegating to WP get_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param $default string default value to return if the option is not set
     * @return string the value from delegated call to get_option(), or optional default value
     * if option is not set.
     */
    public function getOption($optionName, $default = null) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        $retVal = get_option($prefixedOptionName);
        if (!$retVal && $default) {
            $retVal = $default;
        }
        return $retVal;
    }

    /**
     * A wrapper function delegating to WP delete_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @return bool from delegated call to delete_option()
     */
    public function deleteOption($optionName) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return delete_option($prefixedOptionName);
    }

    /**
     * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function addOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return add_option($prefixedOptionName, $value);
    }

    /**
     * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function updateOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return update_option($prefixedOptionName, $value);
    }

    /**
     * A Role Option is an option defined in getOptionMetaData() as a choice of WP standard roles, e.g.
     * 'CanDoOperationX' => array('Can do Operation X', 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber')
     * The idea is use an option to indicate what role level a user must minimally have in order to do some operation.
     * So if a Role Option 'CanDoOperationX' is set to 'Editor' then users which role 'Editor' or above should be
     * able to do Operation X.
     * Also see: canUserDoRoleOption()
     * @param  $optionName
     * @return string role name
     */
    public function getRoleOption($optionName) {
        $roleAllowed = $this->getOption($optionName);
        if (!$roleAllowed || $roleAllowed == '') {
            $roleAllowed = 'Administrator';
        }
        return $roleAllowed;
    }

    /**
     * Given a WP role name, return a WP capability which only that role and roles above it have
     * http://codex.wordpress.org/Roles_and_Capabilities
     * @param  $roleName
     * @return string a WP capability or '' if unknown input role
     */
    protected function roleToCapability($roleName) {
        switch ($roleName) {
            case 'Super Admin':
                return 'manage_options';
            case 'Administrator':
                return 'manage_options';
            case 'Editor':
                return 'publish_pages';
            case 'Author':
                return 'publish_posts';
            case 'Contributor':
                return 'edit_posts';
            case 'Subscriber':
                return 'read';
            case 'Anyone':
                return 'read';
        }
        return '';
    }

    /**
     * @param $roleName string a standard WP role name like 'Administrator'
     * @return bool
     */
    public function isUserRoleEqualOrBetterThan($roleName) {
        if ('Anyone' == $roleName) {
            return true;
        }
        $capability = $this->roleToCapability($roleName);
        return current_user_can($capability);
    }

    /**
     * @param  $optionName string name of a Role option (see comments in getRoleOption())
     * @return bool indicates if the user has adequate permissions
     */
    public function canUserDoRoleOption($optionName) {
        $roleAllowed = $this->getRoleOption($optionName);
        if ('Anyone' == $roleAllowed) {
            return true;
        }
        return $this->isUserRoleEqualOrBetterThan($roleAllowed);
    }

    /**
     * see: http://codex.wordpress.org/Creating_Options_Pages
     * @return void
     */
    public function createSettingsMenu() {
        $pluginName = $this->getPluginDisplayName();
        //create new top-level menu
        add_menu_page($pluginName . ' Plugin Settings',
                      $pluginName,
                      'administrator',
                      get_class($this),
                      array(&$this, 'settingsPage')
        /*,plugins_url('/images/icon.png', __FILE__)*/); // if you call 'plugins_url; be sure to "require_once" it

        //call register settings function
        add_action('admin_init', array(&$this, 'registerSettings'));
    }

    public function registerSettings() {
        $settingsGroup = get_class($this) . '-settings-group';
        $optionMetaData = $this->getOptionMetaData();
        foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
            register_setting($settingsGroup, $aOptionMeta);
        }
    }

    /**
     * Creates HTML for the Administration page to set options for this plugin.
     * Override this method to create a customized page.
     * @return void
     */
    public function settingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'cryptothanks'));
        }

        $optionMetaData = $this->getOptionMetaData();

        // Save Posted Options
        if ($optionMetaData != null) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                if (isset($_POST[$aOptionKey])) {
                    $this->updateOption($aOptionKey, $_POST[$aOptionKey]);
                }
            }
        }

        // HTML for the page
        $settingsGroup = get_class($this) . '-settings-group';
        ?>
        <div class="wrap">
            <h2><?php _e($this->getPluginDisplayName(), 'cryptothanks'); ?></h2>
            <p><?php _e('This is the plugin that allows your visitors show their gratefulness by donating cryptocoins. Just enter your cryptocurrency addresses below and place the plugin code into wherever you wish. It is that easy. But if you wish, you can make fine tunings.', 'cryptothanks'); ?></p>
            
            <p><?php _e('It is very easy to manage your donate button with our plugin. But still, if you have any support or feature requests, please use the <a href="https://wordpress.org/support/plugin/cryptothanks/" target="_blank">support forum</a>. If you like this plugin and want to support the further development process, please go to <a href="https://mudimedia.com/wordpress-plugins/cryptothanks" target="_blank">this page</a> and donate us. Thank you.', 'cryptothanks'); ?></p><br />

            <form method="post" action="">
            <?php settings_fields($settingsGroup); ?>
                <style type="text/css">
                    table.plugin-options-table {width: 100%; padding: 0;}
                    table.plugin-options-table tr:nth-child(even) {background: #f9f9f9}
                    table.plugin-options-table tr:nth-child(odd) {background: #FFF}
                    table.plugin-options-table tr:first-child {width: 35%;}
                    table.plugin-options-table th {text-align: left; padding-left: 5px; width: 150px}
                    table.plugin-options-table th label.coin-label { padding-left: 25px; margin-top: 10px; display: inline-block }
                    table.plugin-options-table th label svg { margin-top: 0px; display: inline-block; position: absolute; margin-left: -25px }
                    table.plugin-options-table span { color: #BBB; font-style: italic }
                    table.plugin-options-table td {vertical-align: middle; padding: 5px }
                    table.plugin-options-table td+td {width: auto}
                    table.plugin-options-table td > p {margin-top: 0; margin-bottom: 0;}
                </style>
                
                <h2><?php _e('Embed Button', 'cryptothanks'); ?></h2>
                <p><?php _e('Please copy and paste the code below to any post/page you want your button to appear.', 'cryptothanks'); ?></p>
                
                <table class="plugin-options-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><p><label for="button_code">Button Code</label></p></th>
                            <td>[cryptothanks]</td>
                        </tr>
                    </tbody>
                </table>
                
                <br />
                <h2><?php _e('Button Settings', 'cryptothanks'); ?></h2>
                
                <table class="plugin-options-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><p><label for="font_family">Font Family</label></p></th>
                            <?php $font_family = $this->getOption("font_family") ?>
                            <td>
                                <select name="font_family" id="font_family">
                                    <option value="inherit" <?php if ($font_family=="inherit") echo 'selected="selected"' ?>>Default</option>
                                    <option value="Helvetica" <?php if ($font_family=="Helvetica") echo 'selected="selected"' ?>>Helvetica</option>
                                    <option value="Arial" <?php if ($font_family=="Arial") echo 'selected="selected"' ?>>Arial</option>
                                    <option value="Verdana" <?php if ($font_family=="Verdana") echo 'selected="selected"' ?>>Verdana</option>
                                    <option value="Times New Roman" <?php if ($font_family=="Times New Roman") echo 'selected="selected"' ?>>Times New Roman</option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="button_label_color">Label Color</label></p></th>
                            <td><input type="text" name="button_label_color" id="button_label_color" value="<?=$this->getOption("button_label_color", "#424242")?>" size="10"></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="label_size">Label Font Size</label></p></th>
                            <td><input type="text" name="label_size" id="label_size" value="<?=$this->getOption("label_size", "22px")?>" size="10"></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="label_align">Label Align</label></p></th>
                            <?php $label_align = $this->getOption("label_align") ?>
                            <td>
                                <select name="label_align" id="label_align">
                                    <option value="left" <?php if ($label_align=="left") echo 'selected="selected"' ?>>Left</option>
                                    <option value="right" <?php if ($label_align=="right") echo 'selected="selected"' ?>>Right</option>
                                    <option value="center" <?php if ($label_align=="center") echo 'selected="selected"' ?>>Center</option>
                                </select>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="button_color">Button Color</label></p></th>
                            <td><input type="text" name="button_color" id="button_color" value="<?=$this->getOption("button_color", "#424242")?>" size="10"></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="button_size">Button Size</label></p></th>
                            <td><input type="text" name="button_size" id="button_size" value="<?=$this->getOption("button_size", "30px")?>" size="10"></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="symbol_font_size">Symbol Font Size</label></p></th>
                            <td><input type="text" name="symbol_font_size" id="symbol_font_size" value="<?=$this->getOption("symbol_font_size", "12px")?>" size="10"></td>
                        </tr>
                    </tbody>
                </table>
                
                <br />
                <h2><?php _e('Translations', 'cryptothanks'); ?></h2>
                
                <table class="plugin-options-table">
                    <tbody>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="button_label">Button Label</label></p></th>
                            <td><input type="text" name="button_label" id="button_label" value="<?=$this->getOption("button_label", "Donate Cryptocoin")?>" size="30"></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="donate_text">Donate</label></p></th>
                            <td><input type="text" name="donate_text" id="donate_text" value="<?=$this->getOption("donate_text", "Donate")?>" size="30"></td>
                        </tr>
                        
                    </tbody>
                </table>
                
                <br />
                <h2><?php _e('Cryptocurrency Addresses', 'cryptothanks'); ?></h2>
                <p><?php _e('Please enter cryptocurrency addresses and payment/donation amount that you want to show with your button. Amount is optional.', 'cryptothanks'); ?></p>
                
                <table class="plugin-options-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><p><label for="BTC_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M182.981 112.854c-7.3-5.498-17.699-7.697-17.699-7.697s8.8-5.102 12.396-10.199c3.6-5.099 5.399-12.999 5.7-17.098.299-4.101 1-21.296-12.399-31.193-10.364-7.658-22.241-10.698-38.19-11.687V.278h-21.396V34.57H95.096V.278H73.702V34.57H31.61v22.219h12.372c3.373 0 9.372.375 11.921 3.228 2.55 2.848 3 4.349 3 9.895l.001 88.535c0 2.099-.4 4.697-2.201 6.398-1.798 1.701-3.597 2.098-7.898 2.098H36.009l-4.399 25.698h42.092v34.195h21.395v-34.195h16.297v34.195h21.396v-34.759c5.531-.323 10.688-.742 13.696-1.136 6.1-.798 19.896-2.398 32.796-11.397 12.896-9 15.793-23.098 16.094-37.294.304-14.197-5.102-23.897-12.395-29.396zM95.096 58.766s6.798-.599 13.497-.501c6.701.099 12.597.3 21.398 3 8.797 2.701 13.992 9.3 14.196 17.099.199 7.799-3.204 12.996-9.2 16.296-5.998 3.299-14.292 5.099-22.094 5.396-7.797.301-17.797 0-17.797 0v-41.29zm47.89 102.279c-4.899 2.701-14.698 5.1-24.194 5.798-9.499.701-23.696.401-23.696.401v-45.893s13.598-.698 24.197 0c10.597.703 19.495 3.4 23.492 5.403 3.999 1.998 11 6.396 11 16.896 0 10.496-5.903 14.696-10.799 17.395z"/></svg> Bitcoin</label></p></th>
                            <td>Address: <input type="text" name="BTC_address" id="BTC_address" value="<?=$this->getOption("BTC_address")?>" size="50"> <br />
                                Amount: <input type="text" name="BTC_amount" id="BTC_amount" value="<?=$this->getOption("BTC_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="BCC_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M182.981 112.854c-7.3-5.498-17.699-7.697-17.699-7.697s8.8-5.102 12.396-10.199c3.6-5.099 5.399-12.999 5.7-17.098.299-4.101 1-21.296-12.399-31.193-10.364-7.658-22.241-10.698-38.19-11.687V.278h-21.396V34.57H95.096V.278H73.702V34.57H31.61v22.219h12.372c3.373 0 9.372.375 11.921 3.228 2.55 2.848 3 4.349 3 9.895l.001 88.535c0 2.099-.4 4.697-2.201 6.398-1.798 1.701-3.597 2.098-7.898 2.098H36.009l-4.399 25.698h42.092v34.195h21.395v-34.195h16.297v34.195h21.396v-34.759c5.531-.323 10.688-.742 13.696-1.136 6.1-.798 19.896-2.398 32.796-11.397 12.896-9 15.793-23.098 16.094-37.294.304-14.197-5.102-23.897-12.395-29.396zM95.096 58.766s6.798-.599 13.497-.501c6.701.099 12.597.3 21.398 3 8.797 2.701 13.992 9.3 14.196 17.099.199 7.799-3.204 12.996-9.2 16.296-5.998 3.299-14.292 5.099-22.094 5.396-7.797.301-17.797 0-17.797 0v-41.29zm47.89 102.279c-4.899 2.701-14.698 5.1-24.194 5.798-9.499.701-23.696.401-23.696.401v-45.893s13.598-.698 24.197 0c10.597.703 19.495 3.4 23.492 5.403 3.999 1.998 11 6.396 11 16.896 0 10.496-5.903 14.696-10.799 17.395z"/></svg> Bitcoin Cash</label></p></th>
                            <td>Address: <input type="text" name="BCC_address" id="BCC_address" value="<?=$this->getOption("BCC_address")?>" size="50"> <br />
                                Amount: <input type="text" name="BCC_amount" id="BCC_amount" value="<?=$this->getOption("BCC_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="ETH_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M112.553 157V86.977l-68.395 29.96zm0-74.837V-.056L46.362 111.156zM116.962-.09v82.253l67.121 29.403zm0 87.067v70.025l68.443-40.045zm-4.409 140.429v-56.321L44.618 131.31zm4.409 0l67.935-96.096-67.935 39.775z"/></svg> Ethereum</label></p></th>
                            <td>Address: <input type="text" name="ETH_address" id="ETH_address" value="<?=$this->getOption("ETH_address")?>" size="50"> <br />
                                Amount: <input type="text" name="ETH_amount" id="ETH_amount" value="<?=$this->getOption("ETH_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="ETC_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M112.553 157V86.977l-68.395 29.96zm0-74.837V-.056L46.362 111.156zM116.962-.09v82.253l67.121 29.403zm0 87.067v70.025l68.443-40.045zm-4.409 140.429v-56.321L44.618 131.31zm4.409 0l67.935-96.096-67.935 39.775z"/></svg> Ethereum Classic</label></p></th>
                            <td>Address: <input type="text" name="ETC_address" id="ETC_address" value="<?=$this->getOption("ETC_address")?>" size="50"> <br />
                                Amount: <input type="text" name="ETC_amount" id="ETC_amount" value="<?=$this->getOption("ETC_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="XRP_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M196.224 139.515c-7.59-4.162-15.848-6.09-23.981-6.026l.069-.039c-11.158.128-20.311-8.55-20.442-19.38-.129-10.686 8.565-19.467 19.505-19.832v-.005l-.02-.01c8.276-.04 16.649-2.145 24.277-6.547 22.81-13.165 30.309-41.787 16.749-63.93-13.563-22.144-43.047-29.423-65.857-16.258-22.81 13.164-30.308 41.787-16.747 63.93 5.648 9.227 2.526 21.152-6.978 26.638-9.369 5.407-21.433 2.534-27.188-6.383l-.005.002v.025c-4.147-6.951-10.189-12.956-17.913-17.192-23.103-12.672-52.417-4.764-65.47 17.667-13.053 22.426-4.903 50.882 18.201 63.553 23.105 12.672 52.415 4.763 65.469-17.667.079-.135.149-.275.226-.411v.033l.005.002c5.545-9.054 17.555-12.191 27.047-6.985 9.628 5.281 13.021 17.136 7.583 26.48-13.055 22.431-4.904 50.885 18.199 63.555 23.104 12.673 52.417 4.763 65.47-17.667 13.051-22.428 4.904-50.881-18.199-63.553z"/></svg> Ripple</label></p></th>
                            <td>Address: <input type="text" name="XRP_address" id="XRP_address" value="<?=$this->getOption("XRP_address")?>" size="50"> <br />
                                Amount: <input type="text" name="XRP_amount" id="XRP_amount" value="<?=$this->getOption("XRP_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="ADA_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M107.59 109.254c-5.762 0-10.956-3.196-13.543-8.337-3.752-7.467-.746-16.597 6.713-20.361a15.26 15.26 0 0 1 6.805-1.621 15.11 15.11 0 0 1 13.548 8.331c3.763 7.468.746 16.603-6.713 20.357a15.141 15.141 0 0 1-6.81 1.631M89.81 140.24c-.297 0-.583-.01-.88-.02-8.328-.486-14.734-7.662-14.258-16.004.47-8.311 7.592-14.766 15.987-14.275 8.338.465 14.734 7.651 14.264 15.993-.46 8.03-7.107 14.306-15.113 14.306m17.894 30.893a15.09 15.09 0 0 1-8.298-2.48 15.06 15.06 0 0 1-6.523-9.586c-.829-3.958-.057-8.01 2.157-11.4a15.15 15.15 0 0 1 12.684-6.849c2.95 0 5.828.865 8.298 2.486a15.027 15.027 0 0 1 6.529 9.59c.833 3.959.06 8.004-2.163 11.396a15.106 15.106 0 0 1-12.684 6.843m35.731-.072a15.089 15.089 0 0 1-13.533-8.337c-1.825-3.61-2.126-7.718-.859-11.569 1.263-3.846 3.963-6.961 7.572-8.787a15.104 15.104 0 0 1 6.81-1.627 15.075 15.075 0 0 1 13.538 8.343c3.753 7.462.746 16.592-6.713 20.356a15.223 15.223 0 0 1-6.815 1.621m17.802-30.974c-.302 0-.588-.02-.874-.03-8.349-.471-14.745-7.658-14.27-15.99.471-8.316 7.598-14.77 15.993-14.28a15.032 15.032 0 0 1 10.44 5.038 15.038 15.038 0 0 1 3.813 10.956c-.45 8.025-7.09 14.306-15.102 14.306m-17.955-30.903c-2.94 0-5.813-.854-8.293-2.486-6.994-4.588-8.941-13.999-4.36-20.99a15.136 15.136 0 0 1 12.678-6.844c2.95 0 5.824.86 8.293 2.48 6.989 4.578 8.947 13.994 4.366 20.986-2.817 4.281-7.551 6.854-12.684 6.854M92.42 52.91c1.887 3.734.379 8.296-3.348 10.168a7.564 7.564 0 0 1-10.164-3.345 7.578 7.578 0 0 1 3.349-10.178c3.727-1.872 8.287-.379 10.163 3.355m-45.859 64.7c4.177.236 7.367 3.816 7.132 7.99-.235 4.179-3.819 7.37-7.99 7.14-4.173-.245-7.358-3.82-7.128-8 .235-4.168 3.814-7.364 7.986-7.13m33.078 72.087a7.558 7.558 0 0 1 10.476-2.179c3.497 2.287 4.473 6.982 2.178 10.486-2.29 3.498-6.984 4.47-10.481 2.184a7.581 7.581 0 0 1-2.173-10.49m78.948 7.385c-1.887-3.729-.379-8.28 3.343-10.163 3.748-1.882 8.293-.378 10.17 3.345 1.886 3.734.377 8.296-3.355 10.168a7.55 7.55 0 0 1-10.158-3.35m45.854-64.7c-4.182-.236-7.362-3.817-7.127-7.99a7.562 7.562 0 0 1 7.986-7.135c4.172.23 7.372 3.81 7.137 7.994-.23 4.179-3.824 7.365-7.996 7.13m-33.068-72.086a7.569 7.569 0 1 1-12.658-8.302 7.56 7.56 0 0 1 10.475-2.184c3.492 2.297 4.474 6.987 2.183 10.486M75.867 29.68a4.902 4.902 0 0 1-2.168 6.583 4.895 4.895 0 0 1-6.58-2.174 4.9 4.9 0 0 1 2.168-6.578 4.904 4.904 0 0 1 6.58 2.169m-57.695 90.663a4.893 4.893 0 0 1 4.612 5.166c-.159 2.7-2.47 4.777-5.164 4.608a4.882 4.882 0 0 1-4.611-5.16 4.9 4.9 0 0 1 5.163-4.614M67.81 215.66a4.893 4.893 0 0 1 6.79-1.406 4.899 4.899 0 0 1 1.4 6.787 4.9 4.9 0 0 1-6.78 1.412 4.91 4.91 0 0 1-1.41-6.793m107.332 4.665c-1.217-2.43-.245-5.375 2.173-6.588a4.882 4.882 0 0 1 6.57 2.169 4.89 4.89 0 0 1-2.163 6.577 4.899 4.899 0 0 1-6.58-2.158m57.69-90.663a4.906 4.906 0 0 1-4.617-5.176 4.893 4.893 0 0 1 5.18-4.613c2.694.143 4.764 2.47 4.595 5.17-.143 2.696-2.464 4.762-5.158 4.619m-49.627-95.328c-1.493 2.266-4.53 2.9-6.785 1.412a4.908 4.908 0 0 1-1.41-6.787 4.885 4.885 0 0 1 6.778-1.402 4.9 4.9 0 0 1 1.417 6.777M77.35 87.077c4.11 2.69 5.26 8.214 2.561 12.327a8.888 8.888 0 0 1-12.331 2.562c-4.116-2.69-5.261-8.21-2.562-12.332 2.7-4.107 8.221-5.263 12.332-2.557m-8.758 60.68c4.392-2.219 9.75-.444 11.963 3.944 2.209 4.394.445 9.754-3.947 11.969-4.386 2.214-9.75.44-11.958-3.944-2.214-4.393-.45-9.754 3.942-11.968m47.51 37.925c.282-4.91 4.495-8.664 9.397-8.393 4.919.276 8.671 4.496 8.39 9.4a8.905 8.905 0 0 1-9.402 8.394c-4.908-.281-8.66-4.49-8.384-9.4m57.562-22.75c-4.11-2.707-5.261-8.225-2.572-12.343 2.7-4.107 8.22-5.252 12.326-2.557 4.116 2.69 5.261 8.214 2.567 12.326-2.695 4.123-8.216 5.263-12.321 2.573m8.752-60.69c-4.391 2.214-9.75.45-11.958-3.944-2.214-4.389-.455-9.749 3.942-11.958 4.397-2.22 9.75-.45 11.973 3.943 2.204 4.388.435 9.749-3.957 11.958m-48.144-37.925c-.282 4.905-4.494 8.67-9.413 8.388a8.907 8.907 0 0 1-8.384-9.401c.281-4.915 4.484-8.67 9.392-8.393a8.925 8.925 0 0 1 8.405 9.406m-90.088 6.777c2.883 1.882 3.68 5.754 1.794 8.633a6.221 6.221 0 0 1-8.63 1.79c-2.878-1.887-3.686-5.759-1.794-8.633 1.891-2.875 5.751-3.688 8.63-1.79m-6.007 97.404a6.225 6.225 0 0 1 8.374 2.762 6.241 6.241 0 0 1-2.766 8.378c-3.073 1.544-6.82.317-8.37-2.757-1.543-3.074-.311-6.833 2.762-8.383m80.68 53.909a6.224 6.224 0 0 1 6.575-5.872 6.231 6.231 0 0 1 5.87 6.578 6.224 6.224 0 0 1-6.576 5.876 6.226 6.226 0 0 1-5.869-6.582m87.956-43.5a6.243 6.243 0 0 1-1.794-8.634c1.891-2.875 5.756-3.688 8.635-1.796a6.248 6.248 0 0 1 1.794 8.634 6.233 6.233 0 0 1-8.635 1.795m6.018-97.399a6.247 6.247 0 0 1-8.38-2.772 6.228 6.228 0 0 1 2.766-8.368 6.23 6.23 0 0 1 8.37 2.757c1.548 3.08.311 6.833-2.756 8.383m-81.315-53.914a6.236 6.236 0 0 1-12.454-.706c.2-3.447 3.139-6.076 6.58-5.876a6.236 6.236 0 0 1 5.874 6.582"/></svg> Cardano</label></p></th>
                            <td>Address: <input type="text" name="ADA_address" id="ADA_address" value="<?=$this->getOption("ADA_address")?>" size="50"> <br />
                                Amount: <input type="text" name="ADA_amount" id="ADA_amount" value="<?=$this->getOption("ADA_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="LTC_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M94.718 184.145l12.778-60.835 64.578-44.271 7.57-36.156-64.591 44.452L133.398 0h-49.61L57.142 127.189l-27.167 18.698-6.308 34.894 25.972-17.806-13.358 63.768h158.917l8.829-42.598z"/></svg> Litecoin</label></p></th>
                            <td>Address: <input type="text" name="LTC_address" id="LTC_address" value="<?=$this->getOption("LTC_address")?>" size="50"> <br />
                                Amount: <input type="text" name="LTC_amount" id="LTC_amount" value="<?=$this->getOption("LTC_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="XLM_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M58.019 178.357a3.072 3.072 0 0 0-4.285.72L23.351 221.75a3.072 3.072 0 1 0 5.005 3.563l30.383-42.671a3.071 3.071 0 0 0-.72-4.285zm7.349 14.001a3.074 3.074 0 0 0-4.287.705L44.182 216.62a3.07 3.07 0 0 0 .706 4.287 3.072 3.072 0 0 0 4.287-.704l16.899-23.556a3.074 3.074 0 0 0-.706-4.289zM204.398 40.38c0-20.482-6.998-40.282-6.998-40.282s-26.116-1.792-47.964 9.984c-21.85 11.776-30.128 26.799-34.225 33.712S102.58 65.472 102.58 65.472s-18.69-.34-33.456 7.853C54.36 81.517 45.313 96.88 45.313 96.88s9.815-2.816 19.203-2.559c9.388.257 18.776 4.18 18.776 4.18s-2.219 3.328-1.792 8.192c.188 2.138 3.044 5.18 6.976 8.491a85.965 85.965 0 0 0-1.811 2.454c-14.018 19.806-17.22 41.635-7.151 48.763 10.07 7.126 29.596-3.15 43.613-22.952.447-.632.883-1.268 1.307-1.903 3.57 2.249 6.629 3.735 8.53 3.639 5.036-.257 9.388-5.548 9.388-5.548s9.603 12.162 12.034 18.308c2.433 6.145 3.329 17.154 3.329 17.154s10.753-12.674 14.149-31.396-4.933-31.974-4.933-31.974 9.474-11.605 19.203-23.383c9.73-11.779 18.264-27.483 18.264-47.966zm-84.581 98.473c-9.284 12.569-22.522 18.536-29.572 13.33-7.049-5.207-5.235-19.616 4.049-32.186.037-.051.076-.099.114-.15 3.75 2.803 7.731 5.59 11.074 8.013 4.403 3.193 9.669 7.301 14.527 10.717-.067.092-.124.184-.192.276zm35.508-64.42c-11.549 0-20.911-9.362-20.911-20.91 0-11.549 9.362-20.91 20.911-20.91 11.548 0 20.909 9.361 20.909 20.91 0 11.548-9.361 20.91-20.909 20.91z"/></svg> Stellar</label></p></th>
                            <td>Address: <input type="text" name="XLM_address" id="XLM_address" value="<?=$this->getOption("XLM_address")?>" size="50"> <br />
                                Amount: <input type="text" name="XLM_amount" id="XLM_amount" value="<?=$this->getOption("XLM_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="DASH_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M92.683 97.738H10.195L.65 128.765h82.488z"/><path d="M223.896 57.882c-4.539-8.829-13.874-12.097-20.937-12.097H48.831l-10.426 33.79h138.119l-20.803 67.626H16.501l-10.427 33.79h148.032c14.464 0 18.33-2.531 28.673-8.586s18.414-16.649 22.789-29.262c4.376-12.613 15.05-48.181 18.328-60.037 3.281-11.855 4.541-16.395 0-25.224z"/></svg> Dash</label></p></th>
                            <td>Address: <input type="text" name="DASH_address" id="DASH_address" value="<?=$this->getOption("DASH_address")?>" size="50"> <br />
                                Amount: <input type="text" name="DASH_amount" id="DASH_amount" value="<?=$this->getOption("DASH_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="XMR_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M39.722 149.021v-95.15l73.741 73.741 73.669-73.669v95.079h33.936a113.219 113.219 0 0 0 5.709-35.59c0-62.6-50.746-113.347-113.347-113.347C50.83.085.083 50.832.083 113.432c0 12.435 2.008 24.396 5.709 35.59h33.93z"/><path d="M162.54 172.077v-60.152l-49.495 49.495-49.148-49.148v59.806h-47.48c19.864 32.786 55.879 54.7 97.013 54.7 41.135 0 77.149-21.914 97.013-54.7H162.54z"/></svg> Monero</label></p></th>
                            <td>Address: <input type="text" name="XMR_address" id="XMR_address" value="<?=$this->getOption("XMR_address")?>" size="50"> <br />
                                Amount: <input type="text" name="XMR_amount" id="XMR_amount" value="<?=$this->getOption("XMR_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="ZEC_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 123.305 595.279 595.28"><path d="M297.582 123.305C133.231 123.305 0 256.581 0 421.006c0 164.407 133.231 297.689 297.582 297.689 164.349 0 297.582-133.282 297.582-297.689 0-164.425-133.233-297.701-297.582-297.701zm104.83 224.116c-1.869 9.971-13.072 18.691-20.545 27.423-23.654 28.043-47.312 55.464-71.589 83.513-9.336 11.219-18.672 21.19-29.254 32.403h121.388v61.078h-74.701c1.245 17.454 1.866 33.655 3.111 51.109h-62.873v-50.482H193.25c0-19.948-2.487-39.888 1.246-58.584 1.867-9.977 13.072-18.702 19.918-27.425 23.654-28.047 47.309-55.47 71.586-83.511 9.338-10.595 18.677-20.565 29.258-32.411H198.851v-61.073h69.096v-51.105h60.384v49.857h75.322c.003 20.564 2.492 40.511-1.241 59.208z"/></svg> Zcash</label></p></th>
                            <td>Address: <input type="text" name="ZEC_address" id="ZEC_address" value="<?=$this->getOption("ZEC_address")?>" size="50"> <br />
                                Amount: <input type="text" name="ZEC_amount" id="ZEC_amount" value="<?=$this->getOption("ZEC_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row"><p><label for="USDT_address" class="coin-label"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 226.777 226.777"><path d="M127.329 100.328v16.979a278.765 278.765 0 0 1-29.027-.064v-13.556h-.003v-3.307c-26.678 1.284-46.427 5.897-46.427 11.392 0 6.491 27.542 11.749 61.518 11.749 33.974 0 61.518-5.258 61.518-11.749-.001-5.576-20.321-10.239-47.579-11.444z"/><path d="M113.389-.001C50.767-.001 0 50.763 0 113.387c0 62.621 50.767 113.39 113.389 113.39 62.622 0 113.388-50.769 113.388-113.39 0-62.624-50.767-113.388-113.388-113.388zm13.938 132.639v50.016H98.298V132.57c-31.075-1.798-54.321-9.026-54.321-17.674 0-8.646 23.246-15.873 54.321-17.674V83.207H58.779V54.179H166.85v29.028h-39.523l.002 13.948c31.654 1.684 55.474 8.989 55.474 17.741-.001 8.754-23.82 16.06-55.476 17.742z"/></svg> Tether</label></p></th>
                            <td>Address: <input type="text" name="USDT_address" id="USDT_address" value="<?=$this->getOption("USDT_address")?>" size="50"> <br />
                                Amount: <input type="text" name="USDT_amount" id="USDT_amount" value="<?=$this->getOption("USDT_amount")?>" size="15"> <span>optional</span></td>
                        </tr>
                    </tbody>
                </table>
                
                <p class="submit">
                    <input type="submit" class="button-primary"
                           value="<?php _e('Save Changes', 'cryptothanks') ?>"/>
                </p>
            </form>
        </div>
        <?php

    }

    /**
     * Helper-function outputs the correct form element (input tag, select tag) for the given item
     * @param  $aOptionKey string name of the option (un-prefixed)
     * @param  $aOptionMeta mixed meta-data for $aOptionKey (either a string display-name or an array(display-name, option1, option2, ...)
     * @param  $savedOptionValue string current value for $aOptionKey
     * @return void
     */
    protected function createFormControl($aOptionKey, $aOptionMeta, $savedOptionValue) {
        if (is_array($aOptionMeta) && count($aOptionMeta) >= 2) { // Drop-down list
            $choices = array_slice($aOptionMeta, 1);
            ?>
            <p><select name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>">
            <?php
                            foreach ($choices as $aChoice) {
                $selected = ($aChoice == $savedOptionValue) ? 'selected' : '';
                ?>
                    <option value="<?php echo $aChoice ?>" <?php echo $selected ?>><?php echo $this->getOptionValueI18nString($aChoice) ?></option>
                <?php
            }
            ?>
            </select></p>
            <?php

        }
        else { // Simple input field
            ?>
            <p><input type="text" name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>"
                      value="<?php echo esc_attr($savedOptionValue) ?>" size="50"/></p>
            <?php

        }
    }

    /**
     * Override this method and follow its format.
     * The purpose of this method is to provide i18n display strings for the values of options.
     * For example, you may create a options with values 'true' or 'false'.
     * In the options page, this will show as a drop down list with these choices.
     * But when the the language is not English, you would like to display different strings
     * for 'true' and 'false' while still keeping the value of that option that is actually saved in
     * the DB as 'true' or 'false'.
     * To do this, follow the convention of defining option values in getOptionMetaData() as canonical names
     * (what you want them to literally be, like 'true') and then add each one to the switch statement in this
     * function, returning the "__()" i18n name of that string.
     * @param  $optionValue string
     * @return string __($optionValue) if it is listed in this method, otherwise just returns $optionValue
     */
    protected function getOptionValueI18nString($optionValue) {
        switch ($optionValue) {
            case 'true':
                return __('true', 'cryptothanks');
            case 'false':
                return __('false', 'cryptothanks');

            case 'Administrator':
                return __('Administrator', 'cryptothanks');
            case 'Editor':
                return __('Editor', 'cryptothanks');
            case 'Author':
                return __('Author', 'cryptothanks');
            case 'Contributor':
                return __('Contributor', 'cryptothanks');
            case 'Subscriber':
                return __('Subscriber', 'cryptothanks');
            case 'Anyone':
                return __('Anyone', 'cryptothanks');
        }
        return $optionValue;
    }

    /**
     * Query MySQL DB for its version
     * @return string|false
     */
    protected function getMySqlVersion() {
        global $wpdb;
        $rows = $wpdb->get_results('select version() as mysqlversion');
        if (!empty($rows)) {
             return $rows[0]->mysqlversion;
        }
        return false;
    }

    /**
     * If you want to generate an email address like "no-reply@your-site.com" then
     * you can use this to get the domain name part.
     * E.g.  'no-reply@' . $this->getEmailDomain();
     * This code was stolen from the wp_mail function, where it generates a default
     * from "wordpress@your-site.com"
     * @return string domain name
     */
    public function getEmailDomain() {
        // Get the site domain and get rid of www.
        $sitename = strtolower($_SERVER['SERVER_NAME']);
        if (substr($sitename, 0, 4) == 'www.') {
            $sitename = substr($sitename, 4);
        }
        return $sitename;
    }
}

