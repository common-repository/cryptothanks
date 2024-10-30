<?php


include_once('CryptoThanks_LifeCycle.php');

class CryptoThanks_Plugin extends CryptoThanks_LifeCycle {

    /**
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        
        return array(
            'font_family' => array(__('XXX', 'cryptothanks')),
            'button_label_color' => array(__('XXX', 'cryptothanks')),
            'label_size' => array(__('XXX', 'cryptothanks')),
            'label_align' => array(__('XXX', 'cryptothanks')),
            'button_color' => array(__('XXX', 'cryptothanks')),
            'button_size' => array(__('XXX', 'cryptothanks')),
            'symbol_font_size' => array(__('XXX', 'cryptothanks')),
            'button_label' => array(__('XXX', 'cryptothanks')),
            'donate_text' => array(__('XXX', 'cryptothanks')),
            'BTC_address' => array(__('Bitcoin Address', 'cryptothanks')),
            'ETH_address' => array(__('Ethereum Address', 'cryptothanks')),
            'XRP_address' => array(__('Ripple Address', 'cryptothanks')),
            'BCC_address' => array(__('Bitcoin Cash Address', 'cryptothanks')),
            'ADA_address' => array(__('Cardano Address', 'cryptothanks')),
            'LTC_address' => array(__('Litecoin Address', 'cryptothanks')),
            'XLM_address' => array(__('Stellar Address', 'cryptothanks')),
            'DASH_address' => array(__('Dash Address', 'cryptothanks')),
            'XMR_address' => array(__('Monero Address', 'cryptothanks')),
            'ETC_address' => array(__('Ethereum Classic Address', 'cryptothanks')),
            'ZEC_address' => array(__('Zcash Address', 'cryptothanks')),
            'USDT_address' => array(__('Tether Address', 'cryptothanks')),
            'BTC_amount' => array(__('Bitcoin Address', 'cryptothanks')),
            'ETH_amount' => array(__('Ethereum Address', 'cryptothanks')),
            'XRP_amount' => array(__('Ripple Address', 'cryptothanks')),
            'BCC_amount' => array(__('Bitcoin Cash Address', 'cryptothanks')),
            'ADA_amount' => array(__('Cardano Address', 'cryptothanks')),
            'LTC_amount' => array(__('Litecoin Address', 'cryptothanks')),
            'XLM_amount' => array(__('Stellar Address', 'cryptothanks')),
            'DASH_amount' => array(__('Dash Address', 'cryptothanks')),
            'XMR_amount' => array(__('Monero Address', 'cryptothanks')),
            'ETC_amount' => array(__('Ethereum Classic Address', 'cryptothanks')),
            'ZEC_amount' => array(__('Zcash Address', 'cryptothanks')),
            'USDT_amount' => array(__('Tether Address', 'cryptothanks'))
        );
        
        return array();
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'CryptoThanks';
    }

    protected function getMainPluginFileName() {
        return 'cryptothanks.php';
    }

    /**
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));
        add_action('the_content', array(&$this, 'cryptoPostHook'));
        add_shortcode('cryptothanks', array($this, 'cryptoPostHook'));
    }
    
    function cryptoPostHook($content) {
        if (strpos($content, "[cryptothanks]") !== false) {
            $r = round(rand(100000, 999999));
            
            $button_code = '<style>.cryptothanks-donate-ADA circle{fill:'.$this->getOption("button_label_color").';}.cryptothanks-container{font-family:'.$this->getOption("font_family").';display:block;padding-right:30px}.cryptothanks-btn-label{font-size:'.$this->getOption("label_size").';font-weight:700;display:block;margin-bottom:5px;text-align:left}.cryptothanks-btn{display:inline-block;cursor:pointer;fill:'.$this->getOption("button_label_color").';color:'.$this->getOption("button_label_color").';font-size:'.$this->getOption("symbol_font_size").';text-align:center;margin-right:2px;margin-bottom:5px}.cryptothanks-btn svg{-webkit-transition:transform .3s ease;-moz-transition:transform .3s ease;transition:transform .3s ease;width:'.$this->getOption("button_size").';height:'.$this->getOption("button_size").';display:block}.cryptothanks-title{-webkit-transition:opacity .3s ease;-moz-transition:opacity .3s ease;transition:opacity .3s ease;opacity:.8}.cryptothanks-btn:hover svg{transform:rotate(-360deg);transform-origin:50% 50%}.cryptothanks-btn:hover .cryptothanks-title{opacity:1}.cryptothanks-donate-BCC:hover,.cryptothanks-donate-BTC:hover{fill:#F7931A}.cryptothanks-donate-ETC:hover,.cryptothanks-donate-ETH:hover{fill:#282828}.cryptothanks-donate-XRP:hover{fill:#346AA9}.cryptothanks-donate-ADA:hover circle{fill:#4b65d0}.cryptothanks-donate-LTC:hover{fill:#838383}.cryptothanks-donate-XLM:hover{fill:#5e6971}.cryptothanks-donate-DASH:hover{fill:#1c75bc}.cryptothanks-donate-ZEC:hover{fill:#e5a93d}.cryptothanks-donate-NIC:hover{fill:#18bc9c}.cryptothanks-tooltip-content{display:none;cursor:default;padding:10px;clear:both;border: solid 1px #ccc;margin-bottom: 15px; margin-right: 15px;max-width:500px}.cryptothanks-tooltip-open{display:block;width:100%;box-shadow:0 3px 6px rgba(0,0,0,.16),0 3px 6px rgba(0,0,0,.23);background-color:#fff;margin-left:0;z-index:9999}.cryptothanks-close-btn { float: right; display: inline-block; width: 20px; height:20px; background-color: #f0f0f0; border-radius: 3px; padding:5px 5px 8px 5px;box-sizing:content-box!important }.cryptothanks-close-btn img { width: 100%; height: 100% }#qr-'.$r.'{float:left;width:100px;height:100px;margin-right: 10px;background-color:#f0f0f0}.cryptothanks-tooltip-content h5{color:'.$this->getOption("button_label_color").';font-size:16px;padding:0;margin:0;display:block;clear:none!important}.cryptothanks-address { font-size: 12px; word-break:break-all }@media(max-width:767px) {#qr-'.$r.' {display:none}}</style>';
            $button_code .= '<div class="cryptothanks-container"><span class="cryptothanks-btn-label">'.$this->getOption("button_label").'</span>';
            $button_code .= '<div id="buttons-'.$r.'">';
            
            $currency_array = array(
                array("BTC", "Bitcoin", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M135.715 122.244c-2.614-1.31-8.437-3.074-15.368-3.533-6.934-.458-15.828 0-15.828 0v30.02s9.287.198 15.503-.26c6.21-.458 12.621-2.027 15.826-3.795 3.203-1.766 7.063-4.513 7.063-11.379 0-6.869-4.579-9.745-7.196-11.053zm-19.555-17.465c5.104-.197 10.532-1.373 14.453-3.532 3.925-2.158 6.148-5.557 6.02-10.66-.134-5.102-3.532-9.418-9.287-11.186-5.757-1.766-9.613-1.897-13.998-1.962-4.382-.064-8.83.328-8.83.328v27.012c.001 0 6.541.197 11.642 0z"/><path d="M113.413 0C50.777 0 0 50.776 0 113.413c0 62.636 50.777 113.413 113.413 113.413s113.411-50.777 113.411-113.413C226.824 50.776 176.049 0 113.413 0zm46.178 156.777c-8.44 5.887-17.465 6.935-21.455 7.456-1.969.259-5.342.532-8.959.744v22.738h-13.998v-22.37h-10.66v22.37H90.522v-22.37H62.987l2.877-16.812h8.371c2.814 0 3.989-.261 5.166-1.372 1.177-1.113 1.439-2.812 1.439-4.188V85.057c0-3.628-.295-4.61-1.963-6.473-1.668-1.867-5.591-2.112-7.8-2.112h-8.091V61.939h27.535V39.505h13.996v22.434h10.66V39.505h13.998v22.703c10.435.647 18.203 2.635 24.983 7.645 8.766 6.475 8.306 17.724 8.11 20.406-.195 2.682-1.372 7.85-3.729 11.183-2.352 3.337-8.108 6.673-8.108 6.673s6.801 1.438 11.578 5.036c4.771 3.598 8.307 9.941 8.106 19.229-.192 9.288-2.088 18.511-10.524 24.397z"/></svg>'), 
                array("BCC", "Bitcoin Cash", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M135.715 122.244c-2.614-1.31-8.437-3.074-15.368-3.533-6.934-.458-15.828 0-15.828 0v30.02s9.287.198 15.503-.26c6.21-.458 12.621-2.027 15.826-3.795 3.203-1.766 7.063-4.513 7.063-11.379 0-6.869-4.579-9.745-7.196-11.053zm-19.555-17.465c5.104-.197 10.532-1.373 14.453-3.532 3.925-2.158 6.148-5.557 6.02-10.66-.134-5.102-3.532-9.418-9.287-11.186-5.757-1.766-9.613-1.897-13.998-1.962-4.382-.064-8.83.328-8.83.328v27.012c.001 0 6.541.197 11.642 0z"/><path d="M113.413 0C50.777 0 0 50.776 0 113.413c0 62.636 50.777 113.413 113.413 113.413s113.411-50.777 113.411-113.413C226.824 50.776 176.049 0 113.413 0zm46.178 156.777c-8.44 5.887-17.465 6.935-21.455 7.456-1.969.259-5.342.532-8.959.744v22.738h-13.998v-22.37h-10.66v22.37H90.522v-22.37H62.987l2.877-16.812h8.371c2.814 0 3.989-.261 5.166-1.372 1.177-1.113 1.439-2.812 1.439-4.188V85.057c0-3.628-.295-4.61-1.963-6.473-1.668-1.867-5.591-2.112-7.8-2.112h-8.091V61.939h27.535V39.505h13.996v22.434h10.66V39.505h13.998v22.703c10.435.647 18.203 2.635 24.983 7.645 8.766 6.475 8.306 17.724 8.11 20.406-.195 2.682-1.372 7.85-3.729 11.183-2.352 3.337-8.108 6.673-8.108 6.673s6.801 1.438 11.578 5.036c4.771 3.598 8.307 9.941 8.106 19.229-.192 9.288-2.088 18.511-10.524 24.397z"/></svg>'), 
                array("ETH", "Ethereum", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M113.313 0C50.732 0 0 50.732 0 113.313s50.732 113.313 113.313 113.313 113.313-50.732 113.313-113.313S175.894 0 113.313 0zm-1.469 188.386l-44.78-63.344 44.78 26.218v37.126zm0-46.41l-45.083-26.408 45.083-19.748v46.156zm0-49.329l-43.631 19.11 43.631-73.306v54.196zm2.906-54.218l44.244 73.6-44.244-19.382V38.429zm0 149.957V151.26l44.78-26.218-44.78 63.344zm0-46.409V95.821l45.116 19.762-45.116 26.394z"/></svg>'), 
                array("ETC", "Ethereum Classic", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M113.313 0C50.732 0 0 50.732 0 113.313s50.732 113.313 113.313 113.313 113.313-50.732 113.313-113.313S175.894 0 113.313 0zm-1.469 188.386l-44.78-63.344 44.78 26.218v37.126zm0-46.41l-45.083-26.408 45.083-19.748v46.156zm0-49.329l-43.631 19.11 43.631-73.306v54.196zm2.906-54.218l44.244 73.6-44.244-19.382V38.429zm0 149.957V151.26l44.78-26.218-44.78 63.344zm0-46.409V95.821l45.116 19.762-45.116 26.394z"/></svg>'), 
                array("XRP", "Ripple", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M113.48.183C50.908.183.183 50.908.183 113.48c0 62.572 50.725 113.297 113.297 113.297 62.572 0 113.297-50.726 113.297-113.297C226.777 50.908 176.052.183 113.48.183zm47.419 163.777c-7.365 12.656-23.905 17.119-36.941 9.97-13.037-7.149-17.635-23.205-10.268-35.862 3.068-5.272 1.153-11.96-4.28-14.941-5.356-2.937-12.132-1.166-15.261 3.941h-.002v-.021c-.044.078-.083.155-.128.232-7.365 12.656-23.904 17.12-36.94 9.97-13.038-7.149-17.636-23.207-10.271-35.86 7.367-12.657 23.905-17.12 36.944-9.969 4.357 2.39 7.765 5.779 10.107 9.7v-.014l.002-.002c3.248 5.032 10.055 6.654 15.341 3.603 5.362-3.095 7.125-9.824 3.936-15.03-7.651-12.494-3.42-28.645 9.451-36.072 12.869-7.428 29.506-3.321 37.159 9.172 7.651 12.495 3.422 28.645-9.451 36.073a27.612 27.612 0 0 1-13.697 3.694l.012.005v.002c-6.175.207-11.081 5.161-11.007 11.191.073 6.11 5.239 11.008 11.533 10.936l-.039.021a27.627 27.627 0 0 1 13.533 3.401c13.034 7.151 17.632 23.206 10.267 35.86z"/></svg>'), 
                array("ADA", "Cardano", '<svg width="250" height="250" viewBox="0 0 250 250" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><circle fill="#000" cx="125" cy="125" r="125"/><path d="M113.06 114.978a10.055 10.055 0 0 1-9.029-5.572c-2.501-4.99-.497-11.091 4.476-13.607a10.154 10.154 0 0 1 4.536-1.083c3.841 0 7.3 2.136 9.032 5.568 2.509 4.99.498 11.095-4.475 13.603a10.075 10.075 0 0 1-4.54 1.09m-11.854 20.707c-.198 0-.389-.007-.586-.014-5.552-.324-9.823-5.12-9.506-10.695.313-5.554 5.061-9.867 10.658-9.54 5.559.312 9.823 5.114 9.509 10.689-.307 5.366-4.738 9.56-10.075 9.56m11.93 20.645c-1.967 0-3.88-.571-5.533-1.658a10.065 10.065 0 0 1-4.349-6.405 10.1 10.1 0 0 1 1.439-7.62 10.094 10.094 0 0 1 8.456-4.576c1.966 0 3.885.578 5.532 1.661a10.043 10.043 0 0 1 4.352 6.409c.556 2.646.04 5.35-1.442 7.615a10.065 10.065 0 0 1-8.456 4.574m23.821-.048a10.057 10.057 0 0 1-9.022-5.572 10.074 10.074 0 0 1-.572-7.731 10.06 10.06 0 0 1 5.047-5.872 10.05 10.05 0 0 1 4.54-1.087c3.842 0 7.301 2.136 9.026 5.575 2.501 4.986.497 11.088-4.476 13.603a10.129 10.129 0 0 1-4.543 1.084m11.868-20.7c-.201 0-.392-.013-.583-.02-5.566-.315-9.83-5.117-9.513-10.685.314-5.557 5.065-9.87 10.662-9.543a10.012 10.012 0 0 1 6.96 3.367 10.064 10.064 0 0 1 2.542 7.321c-.3 5.363-4.727 9.56-10.068 9.56m-11.97-20.651c-1.96 0-3.875-.57-5.528-1.661-4.663-3.066-5.962-9.355-2.908-14.028a10.084 10.084 0 0 1 13.981-2.915c4.66 3.059 5.965 9.351 2.911 14.024a10.092 10.092 0 0 1-8.456 4.58m-33.906-37.605c1.257 2.495.252 5.544-2.233 6.795a5.037 5.037 0 0 1-6.776-2.235 5.07 5.07 0 0 1 2.233-6.802 5.045 5.045 0 0 1 6.776 2.242m-30.573 43.238c2.784.157 4.911 2.55 4.754 5.339a5.06 5.06 0 0 1-5.327 4.771c-2.781-.164-4.905-2.553-4.751-5.346a5.054 5.054 0 0 1 5.324-4.764m22.052 48.173a5.031 5.031 0 0 1 6.983-1.456c2.332 1.528 2.983 4.665 1.452 7.007a5.042 5.042 0 0 1-6.987 1.46 5.074 5.074 0 0 1-1.448-7.011m52.631 4.935a5.064 5.064 0 0 1 2.23-6.791 5.042 5.042 0 0 1 6.779 2.235c1.257 2.495.252 5.544-2.236 6.795a5.029 5.029 0 0 1-6.773-2.239m30.57-43.237c-2.788-.157-4.908-2.55-4.751-5.339.157-2.796 2.539-4.929 5.324-4.768 2.78.154 4.914 2.546 4.758 5.342a5.054 5.054 0 0 1-5.331 4.765M155.583 82.26a5.038 5.038 0 0 1-6.987 1.46 5.066 5.066 0 0 1-1.452-7.008 5.032 5.032 0 0 1 6.983-1.46 5.074 5.074 0 0 1 1.456 7.008M91.91 61.8a3.28 3.28 0 0 1-1.445 4.399 3.26 3.26 0 0 1-4.386-1.453 3.278 3.278 0 0 1 1.445-4.396 3.266 3.266 0 0 1 4.386 1.45m-38.463 60.587a3.267 3.267 0 0 1 3.074 3.452c-.105 1.805-1.646 3.192-3.442 3.08a3.26 3.26 0 0 1-3.074-3.45 3.27 3.27 0 0 1 3.442-3.082m33.092 63.698a3.257 3.257 0 0 1 4.526-.94A3.278 3.278 0 0 1 92 189.68a3.262 3.262 0 0 1-4.52.944 3.286 3.286 0 0 1-.94-4.54m71.555 3.118a3.28 3.28 0 0 1 1.448-4.403 3.251 3.251 0 0 1 4.38 1.45 3.272 3.272 0 0 1-1.442 4.395 3.262 3.262 0 0 1-4.386-1.442m38.46-60.588a3.276 3.276 0 0 1-3.078-3.459 3.265 3.265 0 0 1 3.452-3.083 3.256 3.256 0 0 1 3.065 3.456 3.271 3.271 0 0 1-3.44 3.086M163.47 64.911a3.26 3.26 0 0 1-4.523.943 3.284 3.284 0 0 1-.94-4.535 3.252 3.252 0 0 1 4.519-.937 3.28 3.28 0 0 1 .944 4.529M92.9 100.157c2.74 1.798 3.507 5.49 1.707 8.238a5.917 5.917 0 0 1-8.22 1.712c-2.744-1.798-3.508-5.486-1.708-8.24 1.8-2.745 5.48-3.518 8.22-1.71m-5.838 40.551c2.928-1.483 6.5-.297 7.976 2.636 1.472 2.936.296 6.518-2.631 7.998-2.925 1.48-6.5.294-7.973-2.636-1.475-2.936-.3-6.518 2.628-7.998m31.674 25.345c.188-3.282 2.996-5.79 6.265-5.61 3.278.185 5.78 3.005 5.593 6.283-.184 3.285-2.99 5.797-6.268 5.609-3.272-.188-5.774-3.001-5.59-6.282m38.375-15.203a5.977 5.977 0 0 1-1.715-8.248 5.93 5.93 0 0 1 8.218-1.71c2.744 1.799 3.507 5.49 1.71 8.238a5.921 5.921 0 0 1-8.213 1.72m5.835-40.558c-2.928 1.48-6.5.3-7.972-2.636-1.476-2.932-.304-6.514 2.628-7.99 2.93-1.484 6.5-.302 7.982 2.634 1.469 2.933.29 6.515-2.638 7.992m-32.097-25.345c-.187 3.278-2.996 5.794-6.274 5.606a5.947 5.947 0 0 1-5.59-6.283c.187-3.284 2.989-5.793 6.261-5.608a5.958 5.958 0 0 1 5.603 6.285m-60.058 4.53c1.922 1.257 2.454 3.844 1.196 5.769a4.141 4.141 0 0 1-5.753 1.196 4.172 4.172 0 0 1-1.196-5.77 4.146 4.146 0 0 1 5.753-1.196m-4.005 65.092a4.146 4.146 0 0 1 5.583 1.846 4.175 4.175 0 0 1-1.844 5.599 4.151 4.151 0 0 1-5.58-1.843c-1.029-2.054-.207-4.566 1.841-5.602m53.787 36.026a4.153 4.153 0 0 1 4.383-3.924 4.16 4.16 0 0 1 3.913 4.396 4.153 4.153 0 0 1-4.383 3.927 4.157 4.157 0 0 1-3.913-4.4m58.637-29.07a4.178 4.178 0 0 1-1.196-5.769 4.153 4.153 0 0 1 5.757-1.2 4.182 4.182 0 0 1 1.196 5.77 4.15 4.15 0 0 1-5.757 1.2m4.012-65.089a4.16 4.16 0 0 1-5.586-1.852 4.167 4.167 0 0 1 1.844-5.592 4.148 4.148 0 0 1 5.58 1.842 4.18 4.18 0 0 1-1.838 5.602m-54.21-36.029a4.161 4.161 0 0 1-4.39 3.924 4.165 4.165 0 0 1-3.912-4.396 4.158 4.158 0 0 1 4.386-3.927 4.164 4.164 0 0 1 3.916 4.4" fill-rule="nonzero" fill="#FFF"/></g></svg>'), 
                array("LTC", "Litecoin", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M113.441 0C50.789 0 0 50.79 0 113.443c0 62.654 50.789 113.441 113.441 113.441 62.654 0 113.443-50.787 113.443-113.441C226.885 50.79 176.096 0 113.441 0zm44.036 168.762H68.839l7.45-35.566-14.486 9.933 3.519-19.463 15.151-10.43 14.862-70.939h27.671l-10.232 48.71L148.8 66.213l-4.222 20.167-36.02 24.693-7.126 33.93H162.4l-4.923 23.759z"/></svg>'), 
                array("XLM", "Stellar Lumens", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M130.607 70.618c-8.484 0-15.362 6.876-15.362 15.36 0 8.483 6.878 15.36 15.362 15.36 8.481 0 15.359-6.877 15.359-15.36.001-8.484-6.877-15.36-15.359-15.36zm-44.749 64.08c-.027.039-.057.072-.084.11-6.819 9.232-8.152 19.818-2.972 23.642 5.177 3.823 14.9-.558 21.722-9.792.049-.066.091-.134.14-.202-3.568-2.508-7.437-5.525-10.671-7.873-2.456-1.778-5.379-3.824-8.135-5.885z"/><path d="M113.313 0C50.732 0 0 50.732 0 113.313c0 34.818 15.709 65.963 40.42 86.749l15.562-21.855a2.256 2.256 0 1 1 3.674 2.618l-15.721 22.078c1.546 1.2 3.126 2.357 4.734 3.478a2.29 2.29 0 0 1 .295-.597l12.413-17.304a2.258 2.258 0 0 1 3.668 2.632l-12.413 17.303a2.193 2.193 0 0 1-.354.383c17.618 11.285 38.563 17.828 61.036 17.828 62.581 0 113.313-50.732 113.313-113.313S175.894 0 113.313 0zm39.925 111.559c-7.147 8.649-14.107 17.176-14.107 17.176s6.119 9.731 3.624 23.486c-2.493 13.752-10.394 23.063-10.394 23.063s-.657-8.088-2.447-12.602c-1.785-4.515-8.838-13.449-8.838-13.449s-3.196 3.889-6.895 4.077c-1.398.069-3.645-1.021-6.268-2.674-.311.468-.63.935-.959 1.397-10.296 14.547-24.64 22.095-32.036 16.862-7.397-5.238-5.047-21.272 5.252-35.819.435-.618.879-1.215 1.33-1.804-2.888-2.433-4.986-4.669-5.124-6.238-.314-3.574 1.315-6.017 1.315-6.017s-6.895-2.882-13.792-3.072c-6.895-.19-14.107 1.879-14.107 1.879s6.646-11.285 17.493-17.303c10.846-6.019 24.576-5.768 24.576-5.768s6.268-10.845 9.279-15.923c3.009-5.08 9.091-16.114 25.14-24.764 16.048-8.651 35.233-7.335 35.233-7.335s5.14 14.543 5.14 29.591c0 15.047-6.268 26.583-13.415 35.237z"/></svg>'), 
                array("DASH", "Dash", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M113.388 0C50.766 0 0 50.766 0 113.388c0 62.623 50.766 113.389 113.389 113.389s113.389-50.766 113.389-113.389C226.777 50.766 176.011 0 113.388 0zM56.562 104.802h45.266l-5.238 17.024H51.326l5.236-17.024zm117.268-8.029c-1.801 6.506-7.656 26.023-10.059 32.945-2.4 6.922-6.829 12.734-12.506 16.057-5.676 3.323-7.797 4.712-15.731 4.712H54.303l5.721-18.542h76.395l11.414-37.109h-75.79l5.722-18.541h84.577c3.875 0 8.996 1.792 11.488 6.639 2.491 4.841 1.799 7.333 0 13.839z"/></svg>'), 
                array("XMR", "Monero", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M39.722 149.021v-95.15l73.741 73.741 73.669-73.669v95.079h33.936a113.219 113.219 0 0 0 5.709-35.59c0-62.6-50.746-113.347-113.347-113.347C50.83.085.083 50.832.083 113.432c0 12.435 2.008 24.396 5.709 35.59h33.93z"/><path d="M162.54 172.077v-60.152l-49.495 49.495-49.148-49.148v59.806h-47.48c19.864 32.786 55.879 54.7 97.013 54.7 41.135 0 77.149-21.914 97.013-54.7H162.54z"/></svg>'), 
                array("ZEC", "Zcash", '<svg xmlns="http://www.w3.org/2000/svg" width="595.279" height="595.28" viewBox="0 123.305 595.279 595.28"><path d="M297.582 123.305C133.231 123.305 0 256.581 0 421.006c0 164.407 133.231 297.689 297.582 297.689 164.349 0 297.582-133.282 297.582-297.689 0-164.425-133.233-297.701-297.582-297.701zm104.83 224.116c-1.869 9.971-13.072 18.691-20.545 27.423-23.654 28.043-47.312 55.464-71.589 83.513-9.336 11.219-18.672 21.19-29.254 32.403h121.388v61.078h-74.701c1.245 17.454 1.866 33.655 3.111 51.109h-62.873v-50.482H193.25c0-19.948-2.487-39.888 1.246-58.584 1.867-9.977 13.072-18.702 19.918-27.425 23.654-28.047 47.309-55.47 71.586-83.511 9.338-10.595 18.677-20.565 29.258-32.411H198.851v-61.073h69.096v-51.105h60.384v49.857h75.322c.003 20.564 2.492 40.511-1.241 59.208z"/></svg>'), 
                array("USDT", "Tether", '<svg xmlns="http://www.w3.org/2000/svg" width="226.777" height="226.777" viewBox="0 0 226.777 226.777"><path d="M127.329 100.328v16.979a278.765 278.765 0 0 1-29.027-.064v-13.556h-.003v-3.307c-26.678 1.284-46.427 5.897-46.427 11.392 0 6.491 27.542 11.749 61.518 11.749 33.974 0 61.518-5.258 61.518-11.749-.001-5.576-20.321-10.239-47.579-11.444z"/><path d="M113.389-.001C50.767-.001 0 50.763 0 113.387c0 62.621 50.767 113.39 113.389 113.39 62.622 0 113.388-50.769 113.388-113.39 0-62.624-50.767-113.388-113.388-113.388zm13.938 132.639v50.016H98.298V132.57c-31.075-1.798-54.321-9.026-54.321-17.674 0-8.646 23.246-15.873 54.321-17.674V83.207H58.779V54.179H166.85v29.028h-39.523l.002 13.948c31.654 1.684 55.474 8.989 55.474 17.741-.001 8.754-23.82 16.06-55.476 17.742z"/></svg>')
            );
            foreach ($currency_array as $currency) {
                if ($this->getOption($currency[0]."_address") !='') {
                    $button_code .= '<a id="cryptothanks-link-'.$currency[0].'" href="javascript:cryptothanks_popup_open(\''.$currency[0].'\', \''.$currency[1].'\', \''.$this->getOption($currency[0]."_address").'\', \''.$this->getOption($currency[0]."_amount").'\');" class="cryptothanks-tooltip"><span class="cryptothanks-btn cryptothanks-donate-'.$currency[0].'" title="'.$currency[1].'">'.$currency[2].'<span class="cryptothanks-title">'.$currency[0].'</span></span></a>';
                }
            }
            $button_code .= '</div><span class="cryptothanks-tooltip-content" id="cryptothanks-tooltip-content-'.$r.'"><span id="qr-'.$r.'"></span><a href="javascript:cryptothanks_popup_close();" class="cryptothanks-close-btn"><img src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxNy4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjwhRE9DVFlQRSBzdmcgUFVCTElDICItLy9XM0MvL0RURCBTVkcgMS4xLy9FTiIgImh0dHA6Ly93d3cudzMub3JnL0dyYXBoaWNzL1NWRy8xLjEvRFREL3N2ZzExLmR0ZCI+DQo8c3ZnIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHg9IjBweCIgeT0iMHB4Ig0KCSB3aWR0aD0iMjAwcHgiIGhlaWdodD0iMjAwcHgiIHZpZXdCb3g9IjAgMCAyMDAgMjAwIiBlbmFibGUtYmFja2dyb3VuZD0ibmV3IDAgMCAyMDAgMjAwIiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxwYXRoIGZpbGwtcnVsZT0iZXZlbm9kZCIgY2xpcC1ydWxlPSJldmVub2RkIiBmaWxsPSIjODM4MzgzIiBkPSJNMTM1LjExNyw5OS40MWw2MC43MjIsNjAuNzI1YzMuOTQ3LDMuOTQ3LDMuOTQ3LDEwLjM0MiwwLDE0LjI5Mw0KCWwtMjEuNDMxLDIxLjQyOWMtMy45NDMsMy45NDUtMTAuMzQsMy45NDUtMTQuMjgzLDBMOTkuNDAyLDEzNS4xM2wtNjAuNzI2LDYwLjcyOGMtMy45NDMsMy45NDUtMTAuMzM4LDMuOTQ1LTE0LjI4NiwwTDIuOTU5LDE3NC40MjkNCgljLTMuOTQ1LTMuOTUxLTMuOTQ1LTEwLjM0NiwwLTE0LjI5M0w2My42ODEsOTkuNDFMMi45NTksMzguNjhjLTMuOTQ1LTMuOTQzLTMuOTQ1LTEwLjM0LDAtMTQuMjg3TDI0LjM5LDIuOTYNCgljMy45NDctMy45NDcsMTAuMzQyLTMuOTQ3LDE0LjI4NiwwbDYwLjcyNiw2MC43MjVMMTYwLjEyMywyLjk2YzMuOTQzLTMuOTQ3LDEwLjM0LTMuOTQ3LDE0LjI4MywwbDIxLjQzMSwyMS40MzMNCgljMy45NDcsMy45NDcsMy45NDcsMTAuMzQ0LDAsMTQuMjg3TDEzNS4xMTcsOTkuNDF6Ii8+DQo8L3N2Zz4NCg==" /></a><h5 id="h5-'.$r.'"></h5><br/><span id="address-'.$r.'" class="cryptothanks-address"></span><div style="clear:both"></div></span>';
            $button_code .= '</div><script>function cryptothanks_popup_open(currency, currency_name, currency_address, currency_amount) {document.getElementById(\'cryptothanks-tooltip-content-'.$r.'\').className=\'cryptothanks-tooltip-content cryptothanks-tooltip-open\';document.getElementById(\'h5-'.$r.'\').innerHTML = \''.$this->getOption("donate_text").' \'+currency_amount+\' \'+currency_name;document.getElementById(\'address-'.$r.'\').innerHTML = currency_address;document.getElementById(\'buttons-'.$r.'\').style.display=\'none\';document.getElementById(\'qr-'.$r.'\').innerHTML = \'<img src=\\\'http://api.qrserver.com/v1/create-qr-code/?data=\'+currency_address+\'\\\'>\';}function cryptothanks_popup_close() {document.getElementById(\'cryptothanks-tooltip-content-'.$r.'\').className=\'cryptothanks-tooltip-content\';document.getElementById(\'buttons-'.$r.'\').style.display=\'inline-block\';}</script>';
            
            $newcontent = str_replace("[cryptothanks]", $button_code, $content);
        } else {
            $newcontent = $content;
        }
        return $newcontent;
    }
    
    public function getOption($optionName, $default = null) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        $retVal = get_option($prefixedOptionName);
        if (!$retVal && $default) {
            $retVal = $default;
        }
        return $retVal;
    }
    
    public function prefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) { // 0 but not false
            return $name; // already prefixed
        }
        return $optionNamePrefix . $name;
    }
    
    public function getOptionNamePrefix() {
        return get_class($this) . '_';
    }
}
