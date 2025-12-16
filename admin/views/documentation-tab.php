<?php
if (!defined('WPINC')) die;

// Form Handling (Simplified for brevity, assumes logic from original file)
if (isset($_POST['submit_documentation'])) {
    // ... [Original nonce check and save logic] ...
    // Verify nonce
    if (!wp_verify_nonce($_POST['_wpnonce'], 'quick-tools-documentation-settings')) {
        wp_die('Security check failed');
    }
    
    // Get existing settings
    $existing_settings = get_option('quick_tools_settings', array());
    
    // Process documentation settings
    $existing_settings['show_documentation_widgets'] = isset($_POST['show_documentation_widgets']) ? 1 : 0;
    $existing_settings['show_documentation_status'] = isset($_POST['show_documentation_status']) ? 1 : 0;
    $existing_settings['documentation_widget_limit'] = isset($_POST['documentation_widget_limit']) ? 
        max(1, min(10, intval($_POST['documentation_widget_limit']))) : 5;
    
    // Add module style setting
    $existing_settings['documentation_module_style'] = isset($_POST['documentation_module_style']) && 
        in_array($_POST['documentation_module_style'], ['informative', 'minimal']) ? 
        $_POST['documentation_module_style'] : 'informative';
    
    // Save settings
    update_option('quick_tools_settings', $existing_settings);
    echo '<div class="alert alert-success alert-dismissible fade show">Documentation settings saved! <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
}
$settings = get_option('quick_tools_settings', array());
$module_style = $settings['documentation_module_style'] ?? 'informative';
?>

<form method="post" action="">
    <?php wp_nonce_field('quick-tools-documentation-settings'); ?>
    
    <div class="row">
        <div class="col-md-6">
            <h4 class="mb-3">Widget Settings</h4>
            
            <div class="card mb-3">
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="show_documentation_widgets" value="1" 
                               <?php checked($settings['show_documentation_widgets'] ?? 1, 1); ?>>
                        <label class="form-check-label">Show documentation widgets on dashboard</label>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Style</label>
                        <div class="btn-group w-100">
                            <input type="radio" class="btn-check" name="documentation_module_style" id="doc_style_info" value="informative" <?php checked($module_style, 'informative'); ?>>
                            <label class="btn btn-outline-primary" for="doc_style_info">Informative</label>
                            
                            <input type="radio" class="btn-check" name="documentation_module_style" id="doc_style_min" value="minimal" <?php checked($module_style, 'minimal'); ?>>
                            <label class="btn btn-outline-primary" for="doc_style_min">Minimal</label>
                        </div>
                    </div>

                    <div class="qt-informative-options" <?php echo $module_style === 'minimal' ? 'style="display:none;"' : ''; ?>>
                        <div class="mb-3">
                            <label class="form-label">Items per Widget</label>
                            <input type="number" class="form-control" name="documentation_widget_limit" max="10" min="1" 
                                   value="<?php echo esc_attr($settings['documentation_widget_limit'] ?? 5); ?>">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="show_documentation_status" value="1" 
                                   <?php checked($settings['show_documentation_status'] ?? 1, 1); ?>>
                            <label class="form-check-label">Show Status Indicators (Draft/Published)</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <h4 class="mb-3">Categories</h4>
            <div class="d-grid gap-2 mb-3">
                 <a href="<?php echo admin_url('edit-tags.php?taxonomy=qt_documentation_category&post_type=qt_documentation'); ?>" 
                   class="btn btn-outline-secondary">
                    <i class="dashicons dashicons-category"></i> Manage Categories
                </a>
                <a href="<?php echo admin_url('post-new.php?post_type=qt_documentation'); ?>" 
                   class="btn btn-success">
                    <i class="dashicons dashicons-plus"></i> Add New Documentation
                </a>
            </div>
            
            <div class="list-group">
                <?php
                $categories = get_terms(['taxonomy' => 'qt_documentation_category', 'hide_empty' => false]);
                if (!empty($categories) && !is_wp_error($categories)) {
                    foreach ($categories as $cat) {
                        echo '<div class="list-group-item d-flex justify-content-between align-items-center">';
                        echo '<span>' . esc_html($cat->name) . '</span>';
                        echo '<span class="badge bg-secondary rounded-pill">' . $cat->count . '</span>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="list-group-item">No categories found.</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <button type="submit" name="submit_documentation" class="btn btn-primary mt-3">
        Save Settings
    </button>
</form>

<script>
jQuery(document).ready(function($) {
    $('input[name="documentation_module_style"]').on('change', function() {
        if ($(this).val() === 'minimal') $('.qt-informative-options').hide();
        else $('.qt-informative-options').show();
    });
});
</script>
