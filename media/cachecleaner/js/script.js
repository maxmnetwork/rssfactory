/**
 * @package         Cache Cleaner
 * @version         9.3.0PRO
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2024 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

(function() {
    'use strict';

    window.RegularLabs = window.RegularLabs || {};

    window.RegularLabs.CacheCleaner = window.RegularLabs.CacheCleaner || {
        timeout: null,

        init: function() {
            this.options = Joomla.getOptions ? Joomla.getOptions('rl_cachecleaner', {}) : Joomla.optionsStorage.rl_cachecleaner || {};

            document.body.appendChild(Regular.createElementFromHTML('<div id="cachecleaner_message"></div>'));

            this.message = document.getElementById('cachecleaner_message');
            this.message.addEventListener('click', () => {
                this.hide();
            });

            Regular.hide(this.message);
        },

        purge: function() {
            const date = new Date();

            this.show();

            Regular.loadUrl(
                'index.php',
                'cleancache=1&break=1&src=button&time=' + date.toISOString(),
                (data) => {

                    if (data.indexOf('<html') > -1) {
                        this.error(this.options.message_inactive);
                        return;
                    }

                    if (data.charAt(0) !== '+') {
                        this.error(data);
                        return;
                    }

                    this.success(data.substring(1));
                },
                (data) => {
                    this.error(this.options.message_failure);
                }
            );
        },

        error: function(text) {
            this.message.innerHTML = text;
            Regular.removeClass(this.message, 'cachecleaner-loading');
            Regular.addClass(this.message, 'cachecleaner-error');
            this.hide(10);
        },

        success: function(text) {
            this.message.innerHTML = text;
            Regular.removeClass(this.message, 'cachecleaner-loading');
            Regular.addClass(this.message, 'cachecleaner-success');
            this.hide(2);
        },

        show: function() {
            this.message.innerHTML = this.options.message_clean;
            Regular.removeClasses(this.message, 'cachecleaner-error cachecleaner-success');
            Regular.addClass(this.message, 'cachecleaner-loading');
            Regular.fadeTo(this.message, 0.8);
        },

        hide: function(delay) {
            if (delay) {
                this.timeout = setTimeout(function() {
                    RegularLabs.CacheCleaner.hide();
                }, delay * 1000);
                return;
            }

            clearTimeout(this.timeout);

            Regular.fadeOut(this.message);
        },
    };

    RegularLabs.CacheCleaner.init();
})();
//
//
// $('<span/>', {
//     id   : 'cachecleaner_msg',
//     css  : {'opacity': 0},
//     click: function() {
//         cachecleaner_show_end();
//     }
// }).appendTo('body');
