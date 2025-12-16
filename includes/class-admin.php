<?php
declare(strict_types=1);

/**
 * The admin-specific functionality of the plugin.
 *
 * @package QuickTools
 * @since 1.0.0
 */
class Quick_Tools_Admin {

    /**
     * The ID of this plugin.
     *
     * @var string
     */
    private string $plugin_name;

    /**
     * The version of this plugin.
     *
     * @var string
     */
    private string $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct(string $plugin_name, string $version) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_styles(string $hook): void {
        // Only load on our admin pages and dashboard
        if ($hook !== 'toplevel_page_quick-tools' && $hook !== 'index.php' && strpos($hook, 'qt_documentation') === false) {
            return;
        }

        wp_enqueue_style(
            $this->plugin_name,
            QUICK_TOOLS_PLUGIN_URL . 'admin/css/admin-style.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @param string $hook The current admin page hook.
     */
    public function enqueue_scripts(string $hook): void {
        // Only load on our admin pages and dashboard
        if ($hook !== 'toplevel_page_quick-tools' && $hook !== 'index.php' && strpos($hook, 'qt_documentation') === false) {
            return;
        }

        wp_enqueue_script(
            $this->plugin_name,
            QUICK_TOOLS_PLUGIN_URL . 'admin/js/admin-script.js',
            array('jquery'),
            $this->version,
            true
        );

        // Localize script for AJAX
        wp_localize_script(
            $this->plugin_name,
            'quickToolsAjax',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('quick_tools_nonce'),
                'strings' => array(
                    'searching' => __('Searching...', 'quick-tools'),
                    'no_results' => __('No documentation found.', 'quick-tools'),
                    'error' => __('An error occurred. Please try again.', 'quick-tools'),
                    'rate_limit' => __('Too many search requests. Please wait a moment and try again.', 'quick-tools'),
                    'confirm_import' => __('Are you sure you want to import this documentation? This cannot be undone.', 'quick-tools'),
                    'export_success' => __('Documentation exported successfully!', 'quick-tools'),
                    'import_success' => __('Documentation imported successfully!', 'quick-tools'),
                )
            )
        );
    }

    /**
     * Add plugin admin menu.
     */
    public function add_plugin_admin_menu(): void {
        add_menu_page(
            __('Quick Tools', 'quick-tools'),
            __('Quick Tools', 'quick-tools'),
            'manage_options',
            $this->plugin_name,
            array($this, 'display_plugin_admin_page'),
            'dashicons-admin-tools',
            80
        );
    }

    /**
     * Render the settings page for this plugin.
     */
    public function display_plugin_admin_page(): void {
        include_once QUICK_TOOLS_PLUGIN_DIR . 'admin/views/admin-page.php';
    }

    /**
     * Register plugin settings.
     * Note: We're using manual form processing, so this is simplified.
     */
    public function register_settings(): void {
        // We're handling settings manually in the tab files,
        // so we don't need the complex Settings API registration
        // This prevents conflicts with our manual form processing
    }

    /**
     * Sanitize settings before saving.
     * Note: This is no longer used since we switched to manual form processing
     *
     * @param mixed $input The input to sanitize.
     * @return mixed The sanitized input.
     */
    public function sanitize_settings(mixed $input): mixed {
        // This function is kept for backwards compatibility
        // but is no longer actively used
        return $input;
    }

    /**
     * Section callbacks - No longer used since we switched to manual form processing
     */
    public function documentation_section_callback(): void {
        // Kept for backwards compatibility
    }

    public function cpt_section_callback(): void {
        // Kept for backwards compatibility
    }

    /**
     * Field callbacks - No longer used since we switched to manual form processing
     *
     * @param array $args The field arguments.
     */
    public function checkbox_field_callback(array $args): void {
        // Kept for backwards compatibility
    }

    /**
     * Number field callback.
     *
     * @param array $args The field arguments.
     */
    public function number_field_callback(array $args): void {
        // Kept for backwards compatibility
    }

    /**
     * CPT selection callback.
     */
    public function cpt_selection_callback(): void {
        // Kept for backwards compatibility
    }

    /**
     * AJAX handler for documentation search.
     */
    public function ajax_search_documentation(): void {
        check_ajax_referer('quick_tools_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions.', 'quick-tools'));
        }

        // Rate limiting: Allow max 10 searches per minute per user
        $user_id = get_current_user_id();
        $rate_limit_key = 'qt_search_rate_' . $user_id;
        $rate_limit_count = get_transient($rate_limit_key);
        
        if ($rate_limit_count === false) {
            set_transient($rate_limit_key, 1, MINUTE_IN_SECONDS);
        } elseif ($rate_limit_count >= 10) {
            wp_send_json_error(__('Too many search requests. Please wait a moment and try again.', 'quick-tools'));
        } else {
            set_transient($rate_limit_key, $rate_limit_count + 1, MINUTE_IN_SECONDS);
        }

        if (!isset($_POST['search_term'])) {
            wp_send_json_error(__('No search term provided.', 'quick-tools'));
        }

        $search_term = sanitize_text_field($_POST['search_term']);
        $documentation = new Quick_Tools_Documentation();
        $results = $documentation->search_documentation($search_term);

        wp_send_json_success($results);
    }

    /**
     * AJAX handler for documentation export.
     */
    public function ajax_export_documentation(): void {
        check_ajax_referer('quick_tools_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions.', 'quick-tools'));
        }

        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $documentation = new Quick_Tools_Documentation();
        $export_data = $documentation->export_documentation($category);

        // Set headers for file download
        $filename = 'quick-tools-documentation-' . date('Y-m-d-H-i-s') . '.json';
        
        wp_send_json_success(array(
            'data' => $export_data,
            'filename' => $filename
        ));
    }

    /**
     * AJAX handler for documentation import.
     */
    public function ajax_import_documentation(): void {
        check_ajax_referer('quick_tools_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(__('Insufficient permissions.', 'quick-tools'));
        }

        if (!isset($_FILES['import_file'])) {
            wp_send_json_error(__('No file uploaded.', 'quick-tools'));
        }

        $file = $_FILES['import_file'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(__('File upload error.', 'quick-tools'));
        }

        $file_content = file_get_contents($file['tmp_name']);
        $import_data = json_decode($file_content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error(__('Invalid JSON file.', 'quick-tools'));
        }

        $documentation = new Quick_Tools_Documentation();
        $result = $documentation->import_documentation($import_data);

        if ($result === false) {
            wp_send_json_error(__('Import failed.', 'quick-tools'));
        }

        wp_send_json_success($result);
    }
}
