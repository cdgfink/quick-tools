<?php
/**
 * Define the internationalization functionality.
 */
class Quick_Tools_i18n {

    /**
     * Load the plugin text domain for translation.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'quick-tools',
            false,
            dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
        );
    }
}