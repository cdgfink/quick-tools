<?php
/**
 * Fired during plugin activation.
 */
class Quick_Tools_Activator {

    /**
     * Activate the plugin.
     */
    public static function activate() {
        // Set default options
        $default_settings = array(
            'show_documentation_widgets' => 1,
            'documentation_widget_limit' => 5,
            'show_documentation_status' => 1,
            'show_cpt_widgets' => 1,
            'selected_cpts' => array(),
            'show_recent_posts' => 1,
            'recent_posts_limit' => 3,
        );

        // Only set defaults if no settings exist
        if (!get_option('quick_tools_settings')) {
            add_option('quick_tools_settings', $default_settings);
        }

        // Load the documentation class
        require_once QUICK_TOOLS_PLUGIN_DIR . 'includes/class-documentation.php';

        // Register post type and taxonomy to create rewrite rules
        $documentation = new Quick_Tools_Documentation();
        $documentation->register_post_type();
        $documentation->register_taxonomy();

        // Force create default categories during activation only
        $documentation->force_create_default_categories();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Set activation flag for any welcome messages
        add_option('quick_tools_activated', true);
    }
}