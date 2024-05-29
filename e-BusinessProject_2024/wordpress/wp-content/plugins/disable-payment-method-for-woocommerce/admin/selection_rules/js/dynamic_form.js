(function ($) {
    'use strict';

    function pisol_efrs_select_rule_main() {
        this.init = function () {
            this.slug = 'dpmw';
            this.counter();
            this.addRule();
            this.deleteRule();
            this.dynamicValueSearch();
            jQuery(".pi_values_static").selectWoo();
            this.checkConditionCount();
        }

        this.counter = function () {
            this.count = window.pi_metabox;
        }

        this.increaseCounter = function () {
            this.count = this.count + 1;

            if (this.count > 0) {
                this.noConditionMsg(false);
            }
        }

        this.addRule = function () {
            var parent = this;
            jQuery("#pi-add-" + parent.slug + "-rule").click(function () {
                parent.target = $(this).data('target');
                parent.addRow();
                parent.increaseCounter();
            });
        }

        this.deleteRule = function () {
            var parent = this;
            jQuery(document).on('click', ".pi-delete-rule", function (e) {
                e.preventDefault();
                jQuery(this).parent().parent().remove();
                parent.checkConditionCount();
            });
        }

        this.checkConditionCount = function () {
            var count = jQuery("#pisol-rules-container-dpmw > .row").length;
            if (count <= 0) {
                this.noConditionMsg(true);
            } else {
                this.noConditionMsg(false);
            }
        }

        this.noConditionMsg = function (show) {
            if (show) {
                jQuery('.pisol-no-cond-msg').fadeIn();
            } else {
                jQuery('.pisol-no-cond-msg').fadeOut();
            }
        }

        this.dynamicValueSearch = function () {
            var parent = this;
            jQuery(".pi_values_dynamic").selectWoo({
                ajax: {
                    url: window.ajaxurl,
                    dataType: 'json',
                    type: "GET",
                    delay: 1000,
                    data: function (params) {
                        return {
                            keyword: params.term,
                            action: "pi_" + parent.slug + "_options_" + jQuery(this).data("condition")
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };

                    },
                }
            });
        }

        this.addRow = function () {
            var html = '<div class="row py-3 border-bottom align-items-center" data-count="' + this.count + '">';
            html += '<div class="col-12 col-md-4">';
            html += this.addCondition();
            html += '</div>';
            html += '<div class="col-12 col-md-3 pi_logic_container">';

            html += '</div>';
            html += '<div class="col-12 col-md-4 pi_condition_value_container">';
            html += this.conditionValue();
            html += '</div>';
            html += '<div class="col-12 col-md-1 text-right ">';
            html += this.deleteRow();
            html += '</div>';
            html += '</div>';
            jQuery(this.target).append(html);
            jQuery(".pi_condition_value").selectWoo();

        }

        this.deleteRow = function () {
            var html = '<a href="javascript:void(0);" class="pi-delete-rule"><span class="dashicons dashicons-trash"></span></a>';
            return html;
        }

        this.addCondition = function () {
            var html = window.pi_conditions;
            html = html.replace("{count}", this.count);
            return html;
        }

        this.addLogic = function () {
            var html = window.pi_logic;
            html = html.replace("{count}", this.count);
            return html;
        }

        this.conditionValue = function () {
            var html = "";
            return html;
        }
    }

    jQuery(function ($) {
        var pisol_efrs_select_rule_main_obj = new pisol_efrs_select_rule_main();
        pisol_efrs_select_rule_main_obj.init();
    });

    function pisol_efrs_conditionChange() {
        this.init = function () {
            this.slug = 'dpmw';
            var parent = this;
            jQuery(document).on("change", ".pi_condition_rules", function () {

                parent.conditionValues(this);
            });
        }

        this.conditionValues = function (condition) {
            var parent = this;
            var row = jQuery(condition).parent().parent();
            var count = jQuery(row).data('count');
            var condition_val = $(condition).val();
            if (condition_val == "Select Condition") {
                jQuery(".pi_logic_container", row).html("");
                jQuery(".pi_condition_value_container", row).html("");
                return;
            }
            var logic = window['pi_logic_' + condition_val];
            logic = logic.replace("{count}", count);
            jQuery(".pi_logic_container", row).html(logic);
            this.blockUi(row);
            jQuery.post(window.ajaxurl,
                { action: 'pi_' + parent.slug + '_value_field_' + condition_val, count: count },
                function (data) {
                    jQuery(".pi_condition_value_container", row).html(data);
                    jQuery(".pi_condition_value").selectWoo();
                    parent.dynamicValueSearch();
                }
            ).always(function () {
                parent.ubBlockUi(row)
            });
        }

        this.blockUi = function (row) {
            row.addClass('pi-block-condition-row');
        }

        this.ubBlockUi = function (row) {
            row.removeClass('pi-block-condition-row');
        }

        this.dynamicValueSearch = function () {
            var parent = this;
            jQuery(".pi_values_dynamic").selectWoo({
                ajax: {
                    url: window.ajaxurl,
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            keyword: params.term,
                            action: "pi_" + parent.slug + "_options_" + jQuery(this).data("condition")
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };

                    },
                }
            });
        }
    }

    jQuery(function ($) {
        var pisol_efrs_conditionChange_obj = new pisol_efrs_conditionChange();
        pisol_efrs_conditionChange_obj.init();
    });

})(jQuery);