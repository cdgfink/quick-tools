/**
 * CDG Core Admin Scripts
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Toggle sub-options visibility based on parent checkbox
        $('input[name="enable_post_rename"]').on('change', function() {
            $(this).closest('td').find('.cdg-sub-options, div[style*="margin"]').toggle(this.checked);
        }).trigger('change');

        $('input[name="enable_documentation"]').on('change', function() {
            $(this).closest('td').find('.cdg-sub-options, div[style*="margin"]').toggle(this.checked);
        }).trigger('change');

        $('input[name="enable_cpt_widgets"]').on('change', function() {
            $(this).closest('td').find('.cdg-sub-options, div[style*="margin"]').toggle(this.checked);
        }).trigger('change');

        // Enable limited input when limited radio is selected
        $('input[name="post_revisions_mode"]').on('change', function() {
            var limitInput = $('input[name="post_revisions_limit"]');
            limitInput.prop('disabled', this.value !== 'limited');
        }).filter(':checked').trigger('change');
    });

})(jQuery);
