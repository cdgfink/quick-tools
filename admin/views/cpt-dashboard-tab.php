<?php
/**
 * CPT Dashboard tab content
 */

if (!defined('WPINC')) {
    die;
}

// Handle form submission
if (isset($_POST['submit_cpt'])) {
    if (!wp_verify_nonce($_POST['_wpnonce'], 'quick-tools-cpt-settings')) {
        wp_die('Security check failed');
    }
    
    $existing_settings = get_option('quick_tools_settings', array());
    $existing_settings['show_cpt_widgets'] = isset($_POST['show_cpt_widgets']) ? 1 : 0;
    $existing_settings['cpt_module_style'] = $_POST['cpt_module_style'] ?? 'informative';
    
    // Process new CPT config format
    $selected_cpts = array();
    if (isset($_POST['cpt_config']) && is_array($_POST['cpt_config'])) {
        foreach ($_POST['cpt_config'] as $cpt_slug => $config) {
            if (isset($config['enabled']) && $config['enabled'] == 1) {
                $selected_cpts[$cpt_slug] = array(
                    'location' => sanitize_text_field($config['location'])
                );
            }
        }
    }
    $existing_settings['selected_cpts'] = $selected_cpts;
    
    update_option('quick_tools_settings', $existing_settings);
    
    echo '<div class="notice notice-success is-dismissible"><p>' . __('CPT Dashboard settings saved!', 'quick-tools') . '</p></div>';
}

$settings = get_option('quick_tools_settings', array());
$post_types = Quick_Tools_CPT_Dashboard::get_available_post_types();
$available_pages = Quick_Tools_Admin::get_registered_options_pages();
$module_style = isset($settings['cpt_module_style']) ? $settings['cpt_module_style'] : 'informative';

// Normalize current settings for display
$current_cpts = array();
if (isset($settings['selected_cpts'])) {
    foreach ($settings['selected_cpts'] as $key => $val) {
        if (is_array($val)) $current_cpts[$key] = $val;
        else $current_cpts[$val] = array('location' => 'dashboard');
    }
}
?>

<div class="qt-tab-panel" id="cpt-dashboard-panel">
    <form method="post" action="">
        <?php wp_nonce_field('quick-tools-cpt-settings'); ?>
        
        <div class="qt-settings-section">
            <h2><?php _e('Custom Post Type Dashboard Widgets', 'quick-tools'); ?></h2>
            <p class="description">
                <?php _e('Add quick-creation widgets to your dashboard for custom post types.', 'quick-tools'); ?>
            </p>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><?php _e('Enable CPT Widgets', 'quick-tools'); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="show_cpt_widgets" 
                                           value="1" <?php 
                                           $checked_value = isset($settings['show_cpt_widgets']) ? $settings['show_cpt_widgets'] : 1;
                                           if ($checked_value == 1) echo 'checked="checked"'; 
                                           ?>>
                                    <?php _e('Show custom post type widgets', 'quick-tools'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php _e('Module Style', 'quick-tools'); ?></th>
                        <td>
                            <fieldset>
                                <div class="qt-module-style-options">
                                    <label class="qt-module-style-option">
                                        <input type="radio" name="cpt_module_style" 
                                               value="informative" 
                                               <?php checked($module_style, 'informative'); ?>>
                                        <span class="qt-module-style-label">
                                            <strong><?php _e('Informative', 'quick-tools'); ?></strong>
                                            <span class="qt-module-style-description">
                                                <?php _e('One widget per post type with statistics, recent posts, and manage buttons', 'quick-tools'); ?>
                                            </span>
                                        </span>
                                    </label>
                                    
                                    <label class="qt-module-style-option">
                                        <input type="radio" name="cpt_module_style" 
                                               value="minimal" 
                                               <?php checked($module_style, 'minimal'); ?>>
                                        <span class="qt-module-style-label">
                                            <strong><?php _e('Minimal', 'quick-tools'); ?></strong>
                                            <span class="qt-module-style-description">
                                                <?php _e('Single widget with one "Add Post" button per post type', 'quick-tools'); ?>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </fieldset>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="qt-settings-section">
            <h2><?php _e('Select Post Types', 'quick-tools'); ?></h2>
            <p class="description">
                <?php _e('Choose which custom post types to include and where their button should appear.', 'quick-tools'); ?>
            </p>

            <?php if (empty($post_types)) : ?>
                <div class="qt-no-cpts">
                    <p><strong><?php _e('No custom post types found.', 'quick-tools'); ?></strong></p>
                </div>
            <?php else : ?>
                <div class="qt-cpt-selection-grid">
                    <?php
                    foreach ($post_types as $post_type) {
                        $is_enabled = array_key_exists($post_type->name, $current_cpts);
                        $location = $is_enabled ? ($current_cpts[$post_type->name]['location'] ?? 'dashboard') : 'dashboard';
                        $stats = Quick_Tools_CPT_Dashboard::get_post_type_stats($post_type->name);
                        ?>
                        <div class="qt-cpt-option">
                            <label class="qt-cpt-card <?php echo $is_enabled ? 'selected' : ''; ?>">
                                <div class="qt-cpt-header-row">
                                    <div class="qt-cpt-card-header">
                                        <h4><?php echo esc_html($post_type->labels->name); ?></h4>
                                        <span class="qt-cpt-slug"><?php echo esc_html($post_type->name); ?></span>
                                    </div>
                                    <input type="checkbox" 
                                           name="cpt_config[<?php echo esc_attr($post_type->name); ?>][enabled]" 
                                           value="1" 
                                           <?php checked($is_enabled, true); ?>>
                                </div>
                                
                                <div class="qt-cpt-card-stats">
                                    <div class="qt-stat">
                                        <span class="qt-stat-number"><?php echo number_format_i18n($stats['published']); ?></span>
                                        <span class="qt-stat-label"><?php _e('Published', 'quick-tools'); ?></span>
                                    </div>
                                    <div class="qt-stat">
                                        <span class="qt-stat-number"><?php echo number_format_i18n($stats['draft']); ?></span>
                                        <span class="qt-stat-label"><?php _e('Drafts', 'quick-tools'); ?></span>
                                    </div>
                                </div>
                                
                                <div class="qt-cpt-location-select">
                                    <label><?php _e('Button Location:', 'quick-tools'); ?></label>
                                    <select name="cpt_config[<?php echo esc_attr($post_type->name); ?>][location]" onclick="event.preventDefault()">
                                        <option value="dashboard" <?php selected($location, 'dashboard'); ?>>Dashboard Widget</option>
                                        <?php if (!empty($available_pages)): ?>
                                            <optgroup label="Custom Options Pages">
                                                <?php foreach ($available_pages as $slug => $title): ?>
                                                    <option value="<?php echo esc_attr($slug); ?>" <?php selected($location, $slug); ?>>
                                                        <?php echo esc_html($title); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>

        <?php submit_button(__('Save CPT Settings', 'quick-tools'), 'primary', 'submit_cpt'); ?>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Styling helper for checkbox selection state
    $('.qt-cpt-card input[type="checkbox"]').on('change', function() {
        if ($(this).is(':checked')) {
            $(this).closest('.qt-cpt-card').addClass('selected');
        } else {
            $(this).closest('.qt-cpt-card').removeClass('selected');
        }
    });

    // Prevent clicking the select box from toggling the card checkbox
    $('.qt-cpt-location-select select').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>
