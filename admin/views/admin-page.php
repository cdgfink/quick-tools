<?php
if (!defined('WPINC')) die;
$active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'documentation';
?>

<div class="wrap qt-bootstrap-wrapper">
    <div class="container-fluid">
        <div class="row mb-4 align-items-center">
            <div class="col">
                <h1 class="display-6"><?php echo get_admin_page_title(); ?></h1>
                <p class="text-muted"><?php _e('Manage your documentation and custom post type shortcuts.', 'quick-tools'); ?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link <?php echo $active_tab === 'documentation' ? 'active' : ''; ?>" 
                                   href="?page=quick-tools&tab=documentation">
                                    <i class="dashicons dashicons-media-document"></i> <?php _e('Documentation', 'quick-tools'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $active_tab === 'cpt-dashboard' ? 'active' : ''; ?>" 
                                   href="?page=quick-tools&tab=cpt-dashboard">
                                    <i class="dashicons dashicons-admin-post"></i> <?php _e('CPT Locations', 'quick-tools'); ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?php echo $active_tab === 'import-export' ? 'active' : ''; ?>" 
                                   href="?page=quick-tools&tab=import-export">
                                    <i class="dashicons dashicons-download"></i> <?php _e('Import/Export', 'quick-tools'); ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <?php
                        switch ($active_tab) {
                            case 'documentation':
                                include_once QUICK_TOOLS_PLUGIN_DIR . 'admin/views/documentation-tab.php';
                                break;
                            case 'cpt-dashboard':
                                include_once QUICK_TOOLS_PLUGIN_DIR . 'admin/views/cpt-dashboard-tab.php';
                                break;
                            case 'import-export':
                                include_once QUICK_TOOLS_PLUGIN_DIR . 'admin/views/import-export-tab.php';
                                break;
                            default:
                                include_once QUICK_TOOLS_PLUGIN_DIR . 'admin/views/documentation-tab.php';
                                break;
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><?php _e('Quick Stats', 'quick-tools'); ?></h5>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php
                        $doc_count = wp_count_posts('qt_documentation');
                        $cat_count = wp_count_terms(array('taxonomy' => 'qt_documentation_category'));
                        ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Documentation
                            <span class="badge bg-primary rounded-pill"><?php echo $doc_count->publish ?? 0; ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Categories
                            <span class="badge bg-secondary rounded-pill"><?php echo is_wp_error($cat_count) ? 0 : $cat_count; ?></span>
                        </li>
                    </ul>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php _e('Support', 'quick-tools'); ?></h5>
                        <p class="card-text text-muted small">Built by Crawford Design Group.</p>
                        <a href="https://crawforddesigngp.com" target="_blank" class="btn btn-outline-secondary btn-sm w-100">Visit Website</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
