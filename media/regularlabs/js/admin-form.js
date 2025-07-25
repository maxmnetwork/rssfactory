/**
 * @package         Regular Labs Library
 * @version         24.6.11852
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

(function() {
    'use strict';

    window.RegularLabs = window.RegularLabs || {};

    window.RegularLabs.AdminForm = window.RegularLabs.AdminForm || {
        setToggleTitleClass: function(input, value) {
            const panel = input.closest('.rl-panel');

            if ( ! panel) {
                return;
            }

            panel.classList.remove('rl-panel-info');
            panel.classList.remove('rl-panel-success');
            panel.classList.remove('rl-panel-error');

            switch (value) {
                case 2:
                    panel.classList.add('rl-panel-error');
                    break;
                case 1:
                    panel.classList.add('rl-panel-success');
                    break;
                default:
                    panel.classList.add('rl-panel-info');
                    break;
            }
        },

        loadAjaxButton: function(id, url) {
            const button  = document.querySelector(`#${id}`);
            const icon    = button.querySelector("span:nth-child(1)");
            const message = document.querySelector(`#message_${id}`);

            icon.className    = "icon-refresh icon-spin";
            message.className = "";
            message.innerHTML = "";

            const constants = `
                const button = document.querySelector("#${id}");
                const icon = button.querySelector("span:nth-child(1)");
                const message = document.querySelector("#message_${id}");
            `;

            let success = `${constants}
                Regular.removeClass(button, "btn-warning");
                Regular.addClass(button, "btn-success");
                button.querySelector("span:nth-child(1)").className = "icon-ok";
                if (data) {
                    Regular.addClass(message, "alert alert-success alert-noclose alert-inline");
                    message.innerHTML = data;
                }
            `;

            let error = `${constants}
                Regular.removeClass(button, "btn-success");
                Regular.addClass(button, "btn-warning");
                button.querySelector("span:nth-child(1)").className = "icon-warning";
                
                if(data){
                    let error = data;
                    if(data.statusText) { 
                        error = data.statusText;
                        if(data.responseText.test(/<blockquote>/)) {
                            error = data.responseText.replace(/^[.\\s\\S]*?<blockquote>([.\\s\\S]*?)<\\/blockquote>[.\\s\\S]*$/gm, "$1");
                        }
                    }
                    Regular.addClass(message, "alert alert-danger alert-noclose alert-inline");
                    message.innerHTML = error;
                }
            `;

            success = `
                if(data == "" || data.substring(0,1) == "+") {
                    data = data.trim().replace(/^[+]/, "");
                    ${success}
                } else {
                    data = data.trim().replace(/^[-]/, "");
                    ${error}
                }
            `;

            RegularLabs.Scripts.loadAjax(url, success, error);
        },

        loadAjaxFields: function() {
            if (typeof RegularLabs.Scripts === 'undefined') {
                return;
            }

            document.querySelectorAll('textarea[data-rl-ajax]').forEach((el) => {
                this.loadAjaxField(el);
            });
        },

        loadAjaxField: function(el) {
            if (el.dataset['rlAjaxDone']) {
                return;
            }

            if ( ! this.isInView(el)) {
                return;
            }

            const wrapper = el.closest('.rl-ajax-wrapper');

            if (wrapper) {
                wrapper.classList.add('loaded');
            }

            let attributes = JSON.parse(el.dataset['rlAjax']);

            attributes.id   = el.id;
            attributes.name = el.name;

            const query_attributes = createCompressedAttributes(attributes);

            const url = `index.php?option=com_ajax&plugin=regularlabs&format=raw&fieldid=${el.id}&${query_attributes}`;

            const set_field      = `const field = document.querySelector("#${el.id}");`;
            const replace_field  = `if(field && '${el.id}'.indexOf('X__') < 0){` + 'field.parentNode.replaceChild(' + 'Regular.createElementFromHTML(data),' + `document.querySelector("#${el.id}")` + ');' + '}';
            const remove_spinner = `if(field && '${el.id}'.indexOf('X__') < 0){` + 'field.parentNode.querySelectorAll(`.rl-spinner`).forEach((el) => {' + 'el.remove();' + '})' + '}';

            let success = replace_field;

            if (attributes.treeselect) {
                success += `if(data.indexOf('rl-treeselect-') > -1){RegularLabs.TreeSelect.init('${el.id}');}`;
            }

            success += `RegularLabs.AdminForm.updateShowOn('${attributes.name}');`;

            const error = `${set_field}${remove_spinner}`;
            success     = `if(data){${set_field}${remove_spinner}${success}}`;

            el.dataset['rlAjaxDone'] = 1;

            RegularLabs.Scripts.addToLoadAjaxList(url, success, error);

            function createCompressedAttributes(object) {
                const string = JSON.stringify(object);

                const compressed   = btoa(string);
                const chunk_length = Math.ceil(compressed.length / 10);
                const chunks       = compressed.match(new RegExp('.{1,' + chunk_length + '}', 'g'));

                const attributes = [];

                chunks.forEach((chunk, i) => {
                    attributes.push(`rlatt_${i}=${encodeURIComponent(chunk)}`);
                });

                return attributes.join('&');
            }
        },

        updateShowOn: function(fieldName) {
            fieldName          = fieldName.replace(/\[\]$/g, '');
            const showonFields = document.querySelectorAll('[data-showon]');

            showonFields.forEach(field => {
                if ( ! field.hasAttribute('data-showon-initialised')) {
                    return;
                }

                const jsonData   = field.getAttribute('data-showon') || '';
                const showOnData = JSON.parse(jsonData);

                showOnData.forEach((showOnItem) => {
                    if (showOnItem.field !== fieldName) {
                        return;
                    }

                    field.removeAttribute('data-showon-initialised');
                    Joomla.Showon.initialise(field.parentElement);
                });
            });

        },

        isInView: function(el) {
            const rect       = el.getBoundingClientRect();
            const viewHeight = Math.max(document.documentElement.clientHeight, window.innerHeight);

            if ((rect.bottom < 0 || rect.top - viewHeight >= 0)) {
                return false;
            }

            // check if element is inside any hidden parents
            let parent = el.parentElement;

            while (parent) {
                if (window.getComputedStyle(parent).display === 'none') {
                    return false;
                }

                parent = parent.parentElement;
            }

            return true;
        },

        removeEmptyControlGroups: function() {
            // remove all empty control groups
            document.querySelectorAll('div.control-group > div.control-label label').forEach((el) => {
                if (el.innerHTML.trim() === '') {
                    el.remove();
                }
            });
            document.querySelectorAll('div.control-group > div.control-label,div.control-group > div.controls').forEach((el) => {
                if (el.innerHTML.trim() === '') {
                    el.remove();
                }
            });
            document.querySelectorAll('div.control-group').forEach((el) => {
                if (el.innerHTML.trim() === '') {
                    el.remove();
                }
            });
        },

        setParentClassOnCheckboxes: function() {
            document.querySelectorAll('fieldset.rl-form-checkboxes-set-parent-classes input').forEach((el) => {
                this.setParentClassOnCheckbox(el);
                document.addEventListener('change', (event) => {
                    this.setParentClassOnCheckbox(el);
                });
            });
        },

        setParentClassOnCheckbox: function(el) {
            el.parentElement.classList.toggle('rl-checkbox-checked', el.checked);
        },

        updateColoursOnSelectboxes: function() {
            document.querySelectorAll('[class*="rl-form-select-color"]').forEach((el) => {
                this.updateColoursOnSelectbox(el);
                document.addEventListener('change', (event) => {
                    this.updateColoursOnSelectbox(el);
                });
            });
        },

        updateColoursOnSelectbox: function(el) {
            const value = parseInt(el.value, 10); // Add class on page load

            el.classList.remove('form-select-success', 'form-select-danger', 'rl-form-select-info', 'rl-form-select-ghosted');

            if (value === -1) {
                el.classList.add('rl-form-select-ghosted');
                return;
            }

            if (el.classList.contains('rl-form-select-color-has-global') && value === -2) {
                el.classList.add('rl-form-select-info');
            }

            if ( ! el.classList.contains('rl-form-select-color-has-states')) {
                return;
            }

            if (value === -2 || value === 0) {
                el.classList.add('form-select-danger');
                return;
            }

            if (value === 1) {
                el.classList.add('form-select-success');
                return;
            }
        },

        updateForm: function() {
            this.loadAjaxFields();
            this.removeEmptyControlGroups();
        }
    };

    RegularLabs.AdminForm.updateForm();
    RegularLabs.AdminForm.setParentClassOnCheckboxes();
    RegularLabs.AdminForm.updateColoursOnSelectboxes();

    document.addEventListener('joomla:showon-show', (event) => {
        event.target.querySelectorAll('.CodeMirror').forEach((editor) => {
            editor.CodeMirror.refresh();
        });
    });
    document.addEventListener('joomla.tab.show', (event) => {
        document.querySelectorAll('.CodeMirror').forEach((editor) => {
            editor.CodeMirror.refresh();
        });
    });

    setInterval(() => {
        RegularLabs.AdminForm.updateForm();
    }, 1000);
})();
