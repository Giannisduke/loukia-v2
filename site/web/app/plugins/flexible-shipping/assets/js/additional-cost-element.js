'use strict';

/**
 * Additional cost - rendering and events handling.
 *
 * @param {String} fieldName
 * @param {Number} ruleIndex
 * @param {Number} additionalCostIndex
 * @param {Object} additionalCostSettings
 * @param {Array.<Object>} ruleAdditionalCostFields
 * @param {Object} translations
 *
 * @constructor
 */
function AdditionalCost(fieldName, ruleIndex, additionalCostIndex, additionalCostSettings, ruleAdditionalCostFields, translations) {
    this.fieldName = fieldName;
    this.ruleIndex = ruleIndex;
    this.additionalCostIndex = additionalCostIndex;
    this.additionalCostSettings = additionalCostSettings;
    this.ruleAdditionalCostFields = ruleAdditionalCostFields;
    this.translations = translations;
}

AdditionalCost.prototype = {
    fieldName: '',
    ruleIndex: 0,
    additionalCostIndex: 0,
    additionalCostSettings: {},
    ruleAdditionalCostFields: [],
    translations: {},

    /**
     * @return {JQuery<HTMLElement>}
     */
    prepareElement: function() {
        let additional_cost_element = this;
        let $element_wrapper = jQuery('<div>').addClass('additional_cost_wrapper');
        this.ruleAdditionalCostFields.forEach(function(additional_cost_field, index){
            let $field = new HtmlField(additional_cost_field);
            $element_wrapper.append(
                $field.prepareHtmlField(
                    additional_cost_element.prepareInputName(index, additional_cost_field.name), additional_cost_element.additionalCostSettings[additional_cost_field.name]
                )
            );
        });

        let $delete_additional_cost_button = jQuery('<a>').addClass('button minus').attr('href',"#").text(this.translations.delete_cost);
        $delete_additional_cost_button.bind("click",function(event) { event.preventDefault(); additional_cost_element.deleteAdditionalCosts($element_wrapper) });
        $element_wrapper.append($delete_additional_cost_button);

        return $element_wrapper;
    },

    /**
     * @param {Number} index
     * @param {String} fieldName
     * @return {string}
     */
    prepareInputName: function(index, fieldName) {
        return this.fieldName + '[' + this.ruleIndex + '][additional_costs][' + index + '][' + fieldName + ']';
    },

    /**
     * @param {JQuery<HTMLElement>} $element_wrapper
     */
    deleteAdditionalCosts: function($element_wrapper) {
        $element_wrapper.remove();
    },

};
