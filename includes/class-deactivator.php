<?php
/**
 * Fired during plugin deactivation.
 */
class Quick_Tools_Deactivator {

    /**
     * Deactivate the plugin.
     */
    public static function deactivate() {
        // Flush rewrite rules to clean up
        flush_rewrite_rules();

        // Remove activation flag
        delete_option('quick_tools_activated');

        // Note: We don't delete settings or content on deactivation
        // This preserves user data in case they reactivate
    }
}