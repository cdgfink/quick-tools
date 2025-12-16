<?php
/**
 * Import/Export tab content
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
?>

<div class="qt-tab-panel" id="import-export-panel">
    
    <div class="qt-settings-section">
        <h2><?php _e('Export Documentation', 'quick-tools'); ?></h2>
        <p class="description">
            <?php _e('Export your documentation to backup or transfer to another site.', 'quick-tools'); ?>
        </p>

        <div class="qt-export-options">
            <div class="qt-export-option">
                <h4><?php _e('Export All Documentation', 'quick-tools'); ?></h4>
                <p><?php _e('Download all documentation items as a JSON file.', 'quick-tools'); ?></p>
                <button type="button" class="button button-primary qt-export-btn" data-category="">
                    <span class="dashicons dashicons-download"></span>
                    <?php _e('Export All', 'quick-tools'); ?>
                </button>
            </div>

            <?php
            $categories = get_terms(array(
                'taxonomy' => Quick_Tools_Documentation::TAXONOMY,
                'hide_empty' => false,
            ));

            if (!empty($categories)) {
                echo '<h4>' . __('Export by Category', 'quick-tools') . '</h4>';
                echo '<div class="qt-category-exports">';
                
                foreach ($categories as $category) {
                    $doc_count = get_posts(array(
                        'post_type' => Quick_Tools_Documentation::POST_TYPE,
                        'post_status' => 'publish',
                        'numberposts' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => Quick_Tools_Documentation::TAXONOMY,
                                'field' => 'term_id',
                                'terms' => $category->term_id,
                            ),
                        ),
                        'fields' => 'ids'
                    ));
                    
                    echo '<div class="qt-export-option qt-category-export">';
                    echo '<h5>' . esc_html($category->name) . '</h5>';
                    echo '<p>' . sprintf(__('%d items in this category', 'quick-tools'), count($doc_count)) . '</p>';
                    echo '<button type="button" class="button button-secondary qt-export-btn" data-category="' . esc_attr($category->slug) . '">';
                    echo '<span class="dashicons dashicons-download"></span> ';
                    echo sprintf(__('Export %s', 'quick-tools'), $category->name);
                    echo '</button>';
                    echo '</div>';
                }
                
                echo '</div>';
            }
            ?>
        </div>
    </div>

    <div class="qt-settings-section">
        <h2><?php _e('Import Documentation', 'quick-tools'); ?></h2>
        <p class="description">
            <?php _e('Import documentation from a previously exported JSON file.', 'quick-tools'); ?>
        </p>

        <div class="qt-import-form">
            <div class="qt-import-upload">
                <input type="file" id="qt-import-file" accept=".json" style="display: none;">
                <button type="button" class="button button-large" id="qt-select-file-btn">
                    <span class="dashicons dashicons-upload"></span>
                    <?php _e('Select Import File', 'quick-tools'); ?>
                </button>
                <p class="qt-selected-file" style="display: none;">
                    <strong><?php _e('Selected file:', 'quick-tools'); ?></strong> 
                    <span class="qt-filename"></span>
                    <button type="button" class="button-link qt-clear-file"><?php _e('Clear', 'quick-tools'); ?></button>
                </p>
            </div>

            <div class="qt-import-preview" id="qt-import-preview" style="display: none;">
                <h4><?php _e('Import Preview', 'quick-tools'); ?></h4>
                <div class="qt-import-details"></div>
                <div class="qt-import-actions">
                    <button type="button" class="button button-primary" id="qt-import-btn">
                        <span class="dashicons dashicons-upload"></span>
                        <?php _e('Import Documentation', 'quick-tools'); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="qt-cancel-import-btn">
                        <?php _e('Cancel', 'quick-tools'); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="qt-import-notice">
            <h4><?php _e('Important Notes:', 'quick-tools'); ?></h4>
            <ul>
                <li><?php _e('Importing will create new documentation items - it will not overwrite existing ones.', 'quick-tools'); ?></li>
                <li><?php _e('Categories will be created automatically if they don\'t exist.', 'quick-tools'); ?></li>
                <li><?php _e('Only JSON files exported from Quick Tools can be imported.', 'quick-tools'); ?></li>
                <li><?php _e('Large imports may take a few moments to complete.', 'quick-tools'); ?></li>
            </ul>
        </div>
    </div>

    <div class="qt-settings-section">
        <h2><?php _e('Current Documentation Overview', 'quick-tools'); ?></h2>
        
        <?php
        $total_docs = wp_count_posts(Quick_Tools_Documentation::POST_TYPE);
        $categories = get_terms(array(
            'taxonomy' => Quick_Tools_Documentation::TAXONOMY,
            'hide_empty' => false,
        ));
        ?>
        
        <div class="qt-overview-stats">
            <div class="qt-overview-stat">
                <span class="qt-stat-number"><?php echo esc_html($total_docs->publish ?? 0); ?></span>
                <span class="qt-stat-label"><?php _e('Published Documentation', 'quick-tools'); ?></span>
            </div>
            
            <div class="qt-overview-stat">
                <span class="qt-stat-number"><?php echo esc_html($total_docs->draft ?? 0); ?></span>
                <span class="qt-stat-label"><?php _e('Draft Documentation', 'quick-tools'); ?></span>
            </div>
            
            <div class="qt-overview-stat">
                <span class="qt-stat-number"><?php echo count($categories); ?></span>
                <span class="qt-stat-label"><?php _e('Categories', 'quick-tools'); ?></span>
            </div>
        </div>

        <?php if (!empty($categories)) : ?>
        <div class="qt-category-breakdown">
            <h4><?php _e('Documentation by Category', 'quick-tools'); ?></h4>
            <div class="qt-category-list">
                <?php foreach ($categories as $category) : 
                    $category_docs = get_posts(array(
                        'post_type' => Quick_Tools_Documentation::POST_TYPE,
                        'post_status' => 'publish',
                        'numberposts' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => Quick_Tools_Documentation::TAXONOMY,
                                'field' => 'term_id',
                                'terms' => $category->term_id,
                            ),
                        ),
                        'fields' => 'ids'
                    ));
                ?>
                <div class="qt-category-item">
                    <strong><?php echo esc_html($category->name); ?></strong>
                    <span class="qt-category-count"><?php echo count($category_docs); ?> <?php _e('items', 'quick-tools'); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <p>
            <a href="<?php echo admin_url('edit.php?post_type=' . Quick_Tools_Documentation::POST_TYPE); ?>" 
               class="button button-secondary">
                <?php _e('Manage All Documentation', 'quick-tools'); ?>
            </a>
        </p>
    </div>

    <!-- Progress indicator for import/export -->
    <div id="qt-progress-modal" class="qt-modal" style="display: none;">
        <div class="qt-modal-content qt-progress-content">
            <div class="qt-progress-header">
                <h3 id="qt-progress-title"><?php _e('Processing...', 'quick-tools'); ?></h3>
            </div>
            <div class="qt-progress-body">
                <div class="qt-progress-bar">
                    <div class="qt-progress-fill"></div>
                </div>
                <p id="qt-progress-message"><?php _e('Please wait...', 'quick-tools'); ?></p>
            </div>
        </div>
    </div>
</div>