/*
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
/** Creates scope binding and registers in to ko.bindingHandlers object */
define([
    'ko',
    'uiRegistry',
    'ui/lib/knockout/template/renderer'
], function (ko, registry, renderer) {
    'use strict';

    var $t = Translate;

    /**
     * Creates child context with passed component param as $data. Extends context with $t helper.
     * Applies bindings to descendant nodes.
     * @param {HTMLElement} el - element to apply bindings to.
     * @param {ko.bindingContext} bindingContext - instance of ko.bindingContext, passed to binding initially.
     * @param {Object} component - component instance to attach to new context
     */
    function applyComponents(el, bindingContext, component) {
        component = bindingContext.createChildContext(component);

        ko.utils.extend(component, {
            $t: $t
        });

        ko.utils.arrayForEach(el.childNodes, ko.cleanNode);

        ko.applyBindingsToDescendants(component, el);
    }

    ko.bindingHandlers.scope = {

        /**
         * Scope binding's init method.
         * @returns {Object} - Knockout declaration for it to let binding control descendants.
         */
        init: function () {
            return {
                controlsDescendantBindings: true
            };
        },

        /**
         * Reads params passed to binding, parses component declarations.
         * Fetches for those found and attaches them to the new context.
         * @param {HTMLElement} el - Element to apply bindings to.
         * @param {Function} valueAccessor - Function that returns value, passed to binding.
         * @param {Object} allBindings - Object, which represents all bindings applied to element.
         * @param {Object} viewModel - Object, which represents view model binded to el.
         * @param {ko.bindingContext} bindingContext - Instance of ko.bindingContext, passed to binding initially.
         */
        update: function (el, valueAccessor, allBindings, viewModel, bindingContext) {
            var component = valueAccessor(),
                apply = applyComponents.bind(this, el, bindingContext);

            if (typeof component === 'string') {
                registry.get(component, apply);
            } else if (typeof component === 'function') {
                component(apply);
            }
        }
    };

    ko.virtualElements.allowedBindings.scope = true;

    renderer
        .addNode('scope')
        .addAttribute('scope', {
            name: 'ko-scope'
        });
});
