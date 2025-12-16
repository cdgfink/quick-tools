<?php
declare(strict_types=1);

/**
 * The documentation-specific functionality of the plugin.
 *
 * @package QuickTools
 * @since 1.0.0
 */
class Quick_Tools_Documentation {

    /**
     * The post type name for documentation.
     */
    const POST_TYPE = 'qt_documentation';

    /**
     * The taxonomy name for documentation categories.
     */
    const TAXONOMY = 'qt_doc_category';

    /**
     * Default categories for documentation.
     */
    const DEFAULT_CATEGORIES = [
        'getting-started' => 'Getting Started',
        'advanced' => 'Advanced',
        'troubleshooting' => 'Troubleshooting'
    ];

    /**
     * Module style constants
     */
    const MODULE_STYLE_INFORMATIVE = 'informative';
    const MODULE_STYLE_MINIMAL = 'minimal';

    /**
     * Get the minimum capability required for documentation access.
     * Now restricted to administrators only for editing/adding
     */
    private function get_required_capability(): string {
        return apply_filters('qt_documentation_capability', 'manage_options');
    }

    /**
     * Get the capability required for viewing documentation.
     * Editors and above can view
     */
    private function get_view_capability(): string {
        return apply_filters('qt_documentation_view_capability', 'edit_posts');
    }

    /**
     * Get the capability required for deleting documentation and categories.
     */
    private function get_delete_capability(): string {
        return apply_filters('qt_documentation_delete_capability', 'manage_options');
    }

    /**
     * Register the documentation post type.
     */
    public function register_post_type(): void {
        $labels = array(
            'name' => _x('Documentation', 'Post Type General Name', 'quick-tools'),
            'singular_name' => _x('Documentation', 'Post Type Singular Name', 'quick-tools'),
            'menu_name' => __('Documentation', 'quick-tools'),
            'name_admin_bar' => __('Documentation', 'quick-tools'),
            'archives' => __('Documentation Archives', 'quick-tools'),
            'attributes' => __('Documentation Attributes', 'quick-tools'),
            'parent_item_colon' => __('Parent Documentation:', 'quick-tools'),
            'all_items' => __('All Documentation', 'quick-tools'),
            'add_new_item' => __('Add New Documentation', 'quick-tools'),
            'add_new' => __('Add New', 'quick-tools'),
            'new_item' => __('New Documentation', 'quick-tools'),
            'edit_item' => __('Edit Documentation', 'quick-tools'),
            'update_item' => __('Update Documentation', 'quick-tools'),
            'view_item' => __('View Documentation', 'quick-tools'),
            'view_items' => __('View Documentation', 'quick-tools'),
            'search_items' => __('Search Documentation', 'quick-tools'),
        );

        $args = array(
            'label' => __('Documentation', 'quick-tools'),
            'description' => __('Internal documentation for website editors', 'quick-tools'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields'),
            'taxonomies' => array(self::TAXONOMY),
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 25,
            'menu_icon' => 'dashicons-media-document',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => false,
            'can_export' => true,
            'has_archive' => false,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability_type' => 'post',
            'capabilities' => array(
                'edit_post' => 'manage_options',
                'read_post' => $this->get_view_capability(),
                'delete_post' => $this->get_delete_capability(),
                'edit_posts' => 'manage_options',
                'edit_others_posts' => 'manage_options',
                'publish_posts' => 'manage_options',
                'read_private_posts' => $this->get_view_capability(),
            ),
            'show_in_rest' => false,
        );

        register_post_type(self::POST_TYPE, $args);
    }

    /**
     * Register the documentation category taxonomy.
     */
    public function register_taxonomy(): void {
        $labels = array(
            'name' => _x('Documentation Categories', 'Taxonomy General Name', 'quick-tools'),
            'singular_name' => _x('Documentation Category', 'Taxonomy Singular Name', 'quick-tools'),
            'menu_name' => __('Categories', 'quick-tools'),
            'all_items' => __('All Categories', 'quick-tools'),
            'parent_item' => __('Parent Category', 'quick-tools'),
            'parent_item_colon' => __('Parent Category:', 'quick-tools'),
            'new_item_name' => __('New Category Name', 'quick-tools'),
            'add_new_item' => __('Add New Category', 'quick-tools'),
            'edit_item' => __('Edit Category', 'quick-tools'),
            'update_item' => __('Update Category', 'quick-tools'),
            'view_item' => __('View Category', 'quick-tools'),
            'separate_items_with_commas' => __('Separate categories with commas', 'quick-tools'),
            'add_or_remove_items' => __('Add or remove categories', 'quick-tools'),
            'choose_from_most_used' => __('Choose from the most used', 'quick-tools'),
            'popular_items' => __('Popular Categories', 'quick-tools'),
            'search_items' => __('Search Categories', 'quick-tools'),
            'not_found' => __('Not Found', 'quick-tools'),
            'no_terms' => __('No categories', 'quick-tools'),
            'items_list' => __('Categories list', 'quick-tools'),
            'items_list_navigation' => __('Categories list navigation', 'quick-tools'),
        );

        $args = array(
            'labels' => $labels,
            'hierarchical' => false,
            'public' => false,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => false,
            'show_in_rest' => false,
            'capabilities' => array(
                'manage_terms' => 'manage_options',
                'edit_terms' => 'manage_options',
                'delete_terms' => $this->get_delete_capability(),
                'assign_terms' => 'manage_options',
            ),
        );

        register_taxonomy(self::TAXONOMY, array(self::POST_TYPE), $args);

        // Create default categories if they don't exist
        $this->create_default_categories();
    }

    /**
     * Create default documentation categories.
     */
    private function create_default_categories(): void {
        // Only create default categories if no categories exist at all
        $existing_categories = get_terms(array(
            'taxonomy' => self::TAXONOMY,
            'hide_empty' => false,
            'fields' => 'count'
        ));

        // If there are already categories, don't create defaults
        if ($existing_categories > 0) {
            return;
        }

        // Only create defaults if this is plugin activation or no categories exist
        foreach (self::DEFAULT_CATEGORIES as $slug => $name) {
            if (!term_exists($slug, self::TAXONOMY)) {
                wp_insert_term($name, self::TAXONOMY, array('slug' => $slug));
            }
        }
    }

    /**
     * Force create default categories (used during activation).
     */
    public function force_create_default_categories(): void {
        foreach (self::DEFAULT_CATEGORIES as $slug => $name) {
            if (!term_exists($slug, self::TAXONOMY)) {
                wp_insert_term($name, self::TAXONOMY, array('slug' => $slug));
            }
        }
    }

    /**
     * Add documentation dashboard widgets.
     */
    public function add_dashboard_widgets(): void {
        // Check if user can at least view documentation
        if (!current_user_can($this->get_view_capability())) {
            return;
        }

        $settings = get_option('quick_tools_settings', array());
        $show_widgets = !empty($settings['show_documentation_widgets']);

        if (!$show_widgets) {
            return;
        }

        // Get module style preference
        $module_style = $settings['documentation_module_style'] ?? self::MODULE_STYLE_INFORMATIVE;

        if ($module_style === self::MODULE_STYLE_MINIMAL) {
            // Add single minimal widget
            $this->add_minimal_dashboard_widget();
        } else {
            // Add informative widgets (one per category)
            $this->add_informative_dashboard_widgets();
        }
    }

    /**
     * Add minimal style dashboard widget (single widget with all categories)
     */
    private function add_minimal_dashboard_widget(): void {
        wp_add_dashboard_widget(
            'qt_documentation_minimal',
            __('Quick Documentation', 'quick-tools'),
            array($this, 'render_minimal_dashboard_widget')
        );
    }

    /**
     * Add informative style dashboard widgets (one per category)
     */
    private function add_informative_dashboard_widgets(): void {
        $categories = get_terms(array(
            'taxonomy' => self::TAXONOMY,
            'hide_empty' => false,
        ));

        foreach ($categories as $category) {
            wp_add_dashboard_widget(
                'qt_documentation_' . $category->slug,
                sprintf(__('Documentation: %s', 'quick-tools'), $category->name),
                array($this, 'render_informative_dashboard_widget'),
                null,
                array('category' => $category)
            );
        }
    }

    /**
     * Render minimal dashboard widget
     */
    public function render_minimal_dashboard_widget(): void {
        $categories = get_terms(array(
            'taxonomy' => self::TAXONOMY,
            'hide_empty' => false,
        ));

        echo '<div class="qt-documentation-widget qt-minimal-widget">';
        
        if (empty($categories)) {
            echo '<p>' . __('No documentation categories found.', 'quick-tools') . '</p>';
            if (current_user_can('manage_options')) {
                echo '<p><a href="' . admin_url('post-new.php?post_type=' . self::POST_TYPE) . '" class="button button-primary">' . __('Add Documentation', 'quick-tools') . '</a></p>';
            }
            echo '</div>';
            return;
        }

        echo '<div class="qt-minimal-buttons">';
        
        foreach ($categories as $category) {
            // For minimal style, always link to the category archive page
            $view_url = admin_url('admin.php?page=qt-documentation-category&category=' . $category->slug);

            echo '<a href="' . esc_url($view_url) . '" class="button button-primary button-large qt-minimal-button">';
            echo '<span class="dashicons dashicons-media-document"></span> ';
            echo esc_html($category->name);
            echo '</a>';
        }

        // Add search button
        echo '<a href="#" class="button button-secondary button-large qt-minimal-button qt-search-trigger">';
        echo '<span class="dashicons dashicons-search"></span> ';
        echo __('Search All', 'quick-tools');
        echo '</a>';

        echo '</div>'; // .qt-minimal-buttons
        echo '</div>'; // .qt-documentation-widget
    }

    /**
     * Render informative documentation dashboard widget (original style).
     */
    public function render_informative_dashboard_widget($post, $callback_args): void {
        $category = $callback_args['args']['category'];
        $settings = get_option('quick_tools_settings', array());
        $max_items = isset($settings['documentation_widget_limit']) ? intval($settings['documentation_widget_limit']) : 5;
        $show_status = !empty($settings['show_documentation_status']);

        $docs = get_posts(array(
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => $max_items,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => self::TAXONOMY,
                    'field' => 'term_id',
                    'terms' => $category->term_id,
                ),
            ),
        ));

        if (empty($docs)) {
            echo '<p>' . sprintf(__('No documentation found in %s category.', 'quick-tools'), $category->name) . '</p>';
            if (current_user_can('manage_options')) {
                echo '<p><a href="' . admin_url('post-new.php?post_type=' . self::POST_TYPE) . '" class="button">' . __('Add Documentation', 'quick-tools') . '</a></p>';
            }
            return;
        }

        echo '<div class="qt-documentation-widget qt-informative-widget">';
        echo '<div class="qt-doc-grid-layout">';
        
        foreach ($docs as $doc) {
            $view_url = admin_url('admin.php?page=qt-view-documentation&post_id=' . $doc->ID);
            
            echo '<div class="qt-doc-grid-item">';
            echo '<a href="' . esc_url($view_url) . '" class="button button-primary qt-doc-button">';
            echo esc_html($doc->post_title);
            if ($show_status && $doc->post_status !== 'publish') {
                $status_class = 'qt-status-' . $doc->post_status;
                $status_text = ucfirst($doc->post_status);
                echo '<span class="qt-doc-status ' . esc_attr($status_class) . '">' . esc_html($status_text) . '</span>';
            }
            echo '</a>';
            echo '</div>';
        }
        
        echo '</div>'; // .qt-doc-grid-layout

        // Add search and manage links
        echo '<div class="qt-widget-footer">';
        echo '<a href="#" class="qt-search-trigger button button-secondary">' . __('Search Documentation', 'quick-tools') . '</a>';
        echo '<a href="' . admin_url('edit.php?post_type=' . self::POST_TYPE) . '" class="button button-secondary">' . __('Manage All', 'quick-tools') . '</a>';
        echo '</div>';

        echo '</div>';
    }

    /**
     * Add documentation viewer menu item.
     */
    public function add_documentation_viewer(): void {
        add_submenu_page(
            'edit.php?post_type=' . self::POST_TYPE,
            __('View Documentation', 'quick-tools'),
            __('View Documentation', 'quick-tools'),
            $this->get_view_capability(),
            'qt-view-documentation',
            array($this, 'render_documentation_viewer')
        );

        // Add hidden page for category archive view
        add_submenu_page(
            null, // No parent menu (hidden)
            __('Documentation Category', 'quick-tools'),
            __('Documentation Category', 'quick-tools'),
            $this->get_view_capability(),
            'qt-documentation-category',
            array($this, 'render_category_archive')
        );
    }

    /**
     * Render the category archive page.
     */
    public function render_category_archive(): void {
        if (!current_user_can($this->get_view_capability())) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'quick-tools'));
        }

        if (!isset($_GET['category'])) {
            wp_die(__('No category specified.', 'quick-tools'));
        }

        $category_slug = sanitize_text_field($_GET['category']);
        $category = get_term_by('slug', $category_slug, self::TAXONOMY);

        if (!$category) {
            wp_die(__('Category not found.', 'quick-tools'));
        }

        // Get all documentation in this category
        $docs = get_posts(array(
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => self::TAXONOMY,
                    'field' => 'term_id',
                    'terms' => $category->term_id,
                ),
            ),
        ));

        ?>
        <div class="wrap qt-documentation-category">
            <h1><?php echo sprintf(__('Documentation: %s', 'quick-tools'), esc_html($category->name)); ?></h1>
            
            <?php if (!empty($category->description)) : ?>
                <p class="description"><?php echo esc_html($category->description); ?></p>
            <?php endif; ?>

            <?php if (empty($docs)) : ?>
                <div class="qt-no-docs">
                    <p><?php _e('No documentation found in this category.', 'quick-tools'); ?></p>
                    <?php if (current_user_can('manage_options')) : ?>
                        <p>
                            <a href="<?php echo admin_url('post-new.php?post_type=' . self::POST_TYPE . '&' . self::TAXONOMY . '=' . $category->slug); ?>" 
                               class="button button-primary">
                                <?php _e('Add Documentation', 'quick-tools'); ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php else : ?>
                <div class="qt-doc-grid">
                    <?php foreach ($docs as $doc) : 
                        $view_url = admin_url('admin.php?page=qt-view-documentation&post_id=' . $doc->ID);
                        $edit_url = admin_url('post.php?post=' . $doc->ID . '&action=edit');
                        $view_count = get_post_meta($doc->ID, '_qt_view_count', true);
                        $last_viewed = get_post_meta($doc->ID, '_qt_last_viewed', true);
                    ?>
                        <div class="qt-doc-card">
                            <h3>
                                <a href="<?php echo esc_url($view_url); ?>">
                                    <?php echo esc_html($doc->post_title); ?>
                                </a>
                            </h3>
                            
                            
                            <div class="qt-doc-actions">
                                <a href="<?php echo esc_url($view_url); ?>" class="button button-primary">
                                    <?php _e('View Documentation', 'quick-tools'); ?>
                                </a>
                                <?php if (current_user_can('edit_post', $doc->ID)) : ?>
                                    <a href="<?php echo esc_url($edit_url); ?>" class="button button-secondary">
                                        <?php _e('Edit', 'quick-tools'); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="qt-category-footer">
                <a href="<?php echo admin_url('edit.php?post_type=' . self::POST_TYPE); ?>" class="button">
                    ← <?php _e('All Documentation', 'quick-tools'); ?>
                </a>
                <a href="<?php echo admin_url(); ?>" class="button">
                    <?php _e('Back to Dashboard', 'quick-tools'); ?>
                </a>
            </div>
        </div>
        <?php
    }

    /**
     * Render the documentation viewer page.
     */
    public function render_documentation_viewer(): void {
        if (!current_user_can($this->get_view_capability())) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'quick-tools'));
        }

        if (!isset($_GET['post_id'])) {
            wp_die(__('No documentation specified.', 'quick-tools'));
        }

        $post_id = intval($_GET['post_id']);
        $post = get_post($post_id);
        
        if (!$post || $post->post_type !== self::POST_TYPE) {
            wp_die(__('Documentation not found.', 'quick-tools'));
        }

        // Track view count
        $view_count = get_post_meta($post->ID, '_qt_view_count', true);
        $view_count = $view_count ? intval($view_count) + 1 : 1;
        update_post_meta($post->ID, '_qt_view_count', $view_count);
        update_post_meta($post->ID, '_qt_last_viewed', current_time('mysql'));

        ?>
        <div class="wrap qt-documentation-viewer">
            <div class="qt-doc-header">
                <h1><?php echo esc_html($post->post_title); ?></h1>
                <div class="qt-doc-meta">
                    <span class="qt-doc-date"><?php echo sprintf(__('Last updated: %s', 'quick-tools'), get_the_modified_date('', $post)); ?></span>
                    <?php if (current_user_can('edit_post', $post->ID)) : ?>
                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $post->ID . '&action=edit')); ?>" class="button button-secondary">
                        <?php _e('Edit Documentation', 'quick-tools'); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="qt-doc-content">
                <?php echo apply_filters('the_content', $post->post_content); ?>
            </div>

            <div class="qt-doc-footer">
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=' . self::POST_TYPE)); ?>" class="button">
                    ← <?php _e('Back to All Documentation', 'quick-tools'); ?>
                </a>
            </div>
        </div>
        <?php
    }

    /**
     * Get documentation for search.
     */
    public function search_documentation(string $search_term): array {
        if (empty($search_term)) {
            return array();
        }

        $docs = get_posts(array(
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => 20,
            's' => $search_term,
            'orderby' => 'relevance',
        ));

        $results = array();
        foreach ($docs as $doc) {
            $categories = wp_get_post_terms($doc->ID, self::TAXONOMY);
            $category_names = array_map(function($cat) {
                return $cat->name;
            }, $categories);

            $results[] = array(
                'id' => $doc->ID,
                'title' => $doc->post_title,
                'excerpt' => $doc->post_excerpt ?: wp_trim_words($doc->post_content, 20),
                'categories' => $category_names,
                'view_url' => admin_url('admin.php?page=qt-view-documentation&post_id=' . $doc->ID),
                'edit_url' => admin_url('post.php?post=' . $doc->ID . '&action=edit'),
            );
        }

        return $results;
    }

    /**
     * Export documentation as JSON.
     */
    public function export_documentation(string $category_slug = ''): array {
        $args = array(
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => -1,
        );

        if (!empty($category_slug)) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => self::TAXONOMY,
                    'field' => 'slug',
                    'terms' => $category_slug,
                ),
            );
        }

        $docs = get_posts($args);
        $export_data = array();

        foreach ($docs as $doc) {
            $categories = wp_get_post_terms($doc->ID, self::TAXONOMY);
            $category_slugs = array_map(function($cat) {
                return $cat->slug;
            }, $categories);

            $export_data[] = array(
                'title' => $doc->post_title,
                'content' => $doc->post_content,
                'excerpt' => $doc->post_excerpt,
                'categories' => $category_slugs,
                'menu_order' => $doc->menu_order,
                'date' => $doc->post_date,
            );
        }

        return array(
            'version' => QUICK_TOOLS_VERSION,
            'export_date' => current_time('mysql'),
            'documentation' => $export_data,
        );
    }

    /**
     * Import documentation from JSON.
     */
    public function import_documentation(array $import_data): array {
        if (!isset($import_data['documentation']) || !is_array($import_data['documentation'])) {
            return array(
                'imported' => 0,
                'errors' => array(__('Invalid import data format', 'quick-tools'))
            );
        }

        $imported = 0;
        $errors = array();

        foreach ($import_data['documentation'] as $doc_data) {
            try {
                $post_data = array(
                    'post_type' => self::POST_TYPE,
                    'post_title' => sanitize_text_field($doc_data['title']),
                    'post_content' => wp_kses_post($doc_data['content']),
                    'post_excerpt' => sanitize_textarea_field($doc_data['excerpt']),
                    'post_status' => 'publish',
                    'menu_order' => isset($doc_data['menu_order']) ? intval($doc_data['menu_order']) : 0,
                );

                $post_id = wp_insert_post($post_data);

                if (is_wp_error($post_id)) {
                    $errors[] = sprintf(__('Failed to import "%s": %s', 'quick-tools'), $doc_data['title'], $post_id->get_error_message());
                    continue;
                }

                // Assign categories
                if (isset($doc_data['categories']) && is_array($doc_data['categories'])) {
                    wp_set_post_terms($post_id, $doc_data['categories'], self::TAXONOMY);
                }

                $imported++;
            } catch (Exception $e) {
                $errors[] = sprintf(__('Failed to import "%s": %s', 'quick-tools'), $doc_data['title'], $e->getMessage());
            }
        }

        return array(
            'imported' => $imported,
            'errors' => $errors,
        );
    }
}