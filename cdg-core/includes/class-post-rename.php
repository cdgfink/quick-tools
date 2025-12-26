<?php
/**
 * Post Rename Class
 *
 * Allows renaming the default "Posts" post type to custom labels.
 *
 * @package CDG_Core
 * @since 1.0.0
 */

declare(strict_types=1);

class CDG_Core_Post_Rename
{
    /**
     * Plugin instance
     *
     * @var CDG_Core
     */
    private CDG_Core $plugin;

    /**
     * Constructor
     *
     * @param CDG_Core $plugin Plugin instance
     */
    public function __construct(CDG_Core $plugin)
    {
        $this->plugin = $plugin;
        $this->setup_hooks();
    }

    /**
     * Setup hooks
     *
     * @return void
     */
    private function setup_hooks(): void
    {
        add_action('init', [$this, 'rename_post_type'], 99);
        add_action('admin_menu', [$this, 'change_menu_icon'], 99);
        add_action('admin_init', [$this, 'add_page_attributes']);
    }

    /**
     * Rename post type labels
     *
     * @return void
     */
    public function rename_post_type(): void
    {
        $post_type_object = get_post_type_object('post');
        
        if (!$post_type_object || !$post_type_object->labels) {
            return;
        }
        
        $plural = $this->plugin->get_setting('post_rename_plural');
        $singular = $this->plugin->get_setting('post_rename_singular');
        $menu = $this->plugin->get_setting('post_rename_menu');
        
        if (empty($plural) || empty($singular)) {
            return;
        }
        
        $labels = $post_type_object->labels;
        
        $labels->name = $plural;
        $labels->singular_name = $singular;
        $labels->menu_name = !empty($menu) ? $menu : $plural;
        $labels->name_admin_bar = $singular;
        $labels->add_new = sprintf(__('Add %s', 'cdg-core'), $singular);
        $labels->add_new_item = sprintf(__('Add New %s', 'cdg-core'), $singular);
        $labels->edit_item = sprintf(__('Edit %s', 'cdg-core'), $singular);
        $labels->new_item = sprintf(__('New %s', 'cdg-core'), $singular);
        $labels->view_item = sprintf(__('View %s', 'cdg-core'), $singular);
        $labels->view_items = sprintf(__('View %s', 'cdg-core'), $plural);
        $labels->search_items = sprintf(__('Search %s', 'cdg-core'), $plural);
        $labels->not_found = sprintf(__('No %s found', 'cdg-core'), strtolower($plural));
        $labels->not_found_in_trash = sprintf(__('No %s found in Trash', 'cdg-core'), strtolower($plural));
        $labels->all_items = sprintf(__('All %s', 'cdg-core'), $plural);
        $labels->archives = sprintf(__('%s Archives', 'cdg-core'), $singular);
        $labels->attributes = sprintf(__('%s Attributes', 'cdg-core'), $singular);
        $labels->insert_into_item = sprintf(__('Insert into %s', 'cdg-core'), strtolower($singular));
        $labels->uploaded_to_this_item = sprintf(__('Uploaded to this %s', 'cdg-core'), strtolower($singular));
        $labels->filter_items_list = sprintf(__('Filter %s list', 'cdg-core'), strtolower($plural));
        $labels->items_list_navigation = sprintf(__('%s list navigation', 'cdg-core'), $plural);
        $labels->items_list = sprintf(__('%s list', 'cdg-core'), $plural);
        $labels->item_published = sprintf(__('%s published.', 'cdg-core'), $singular);
        $labels->item_published_privately = sprintf(__('%s published privately.', 'cdg-core'), $singular);
        $labels->item_reverted_to_draft = sprintf(__('%s reverted to draft.', 'cdg-core'), $singular);
        $labels->item_scheduled = sprintf(__('%s scheduled.', 'cdg-core'), $singular);
        $labels->item_updated = sprintf(__('%s updated.', 'cdg-core'), $singular);
    }

    /**
     * Change menu icon
     *
     * @return void
     */
    public function change_menu_icon(): void
    {
        global $menu;
        
        $icon = $this->plugin->get_setting('post_rename_icon');
        
        if (empty($icon)) {
            return;
        }
        
        foreach ($menu as $key => $item) {
            if (isset($item[2]) && $item[2] === 'edit.php') {
                $menu[$key][6] = $icon;
                break;
            }
        }
    }

    /**
     * Add page attributes support to posts
     *
     * @return void
     */
    public function add_page_attributes(): void
    {
        add_post_type_support('post', 'page-attributes');
    }

    /**
     * Get available dashicons for admin
     *
     * @return array
     */
    public static function get_available_icons(): array
    {
        return [
            'dashicons-admin-post' => 'Post (Default)',
            'dashicons-slides' => 'Slides',
            'dashicons-images-alt2' => 'Images',
            'dashicons-format-gallery' => 'Gallery',
            'dashicons-format-image' => 'Image',
            'dashicons-camera' => 'Camera',
            'dashicons-video-alt3' => 'Video',
            'dashicons-microphone' => 'Microphone',
            'dashicons-portfolio' => 'Portfolio',
            'dashicons-book' => 'Book',
            'dashicons-book-alt' => 'Book Alt',
            'dashicons-media-document' => 'Document',
            'dashicons-media-text' => 'Text',
            'dashicons-testimonial' => 'Testimonial',
            'dashicons-star-filled' => 'Star',
            'dashicons-heart' => 'Heart',
            'dashicons-awards' => 'Awards',
            'dashicons-calendar-alt' => 'Calendar',
            'dashicons-location' => 'Location',
            'dashicons-businessman' => 'Person',
            'dashicons-groups' => 'Groups',
            'dashicons-products' => 'Products',
            'dashicons-cart' => 'Cart',
            'dashicons-store' => 'Store',
            'dashicons-building' => 'Building',
            'dashicons-hammer' => 'Tools',
            'dashicons-clipboard' => 'Clipboard',
            'dashicons-analytics' => 'Analytics',
            'dashicons-chart-bar' => 'Chart',
            'dashicons-megaphone' => 'Megaphone',
            'dashicons-email' => 'Email',
            'dashicons-admin-links' => 'Links',
            'dashicons-admin-generic' => 'Generic',
        ];
    }
}
