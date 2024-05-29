/**
 * v1.0.2
 */
(function ($) {
    'use strict';

    /**
     * Add class exclude-quick-save to the form you want to exclude from quick save
     */
    function quickSaveButton() {
        this.init = function () {
            this.form = jQuery('#pi-dpmw-new-rule').not('.exclude-quick-save');
            this.addButton();
            this.onClick();
        }

        this.addButton = function () {
            if (this.form.length == 1) {
                var button = jQuery('<button type="submit" id="pisol-quick-save" class="btn btn-danger btn-lg">Save Changes</button>').css({
                    'position': 'fixed',
                    'top': '50%',
                    'right': '-76px',
                    'z-index': '100000000000',
                    'transform': 'rotate(-90deg)',
                    'border-color': '#FFFFFF',
                    'width': '190px'
                });
                this.form.after(button);
            }
        }

        this.onClick = function () {
            var parent = this;
            jQuery(document).on('click', '#pisol-quick-save', function (e) {
                e.preventDefault();
                parent.form.trigger('click');
            });
        }

    }

    jQuery(function ($) {
        var quickSaveButtonObj = new quickSaveButton();
        quickSaveButtonObj.init();
    });

})(jQuery);