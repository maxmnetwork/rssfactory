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

    document.querySelectorAll('rl-field-simple-category').forEach((simplecategory) => {
        const fancy_select = simplecategory.querySelector('joomla-field-fancy-select');
        const select       = simplecategory.querySelector('select');
        const input        = simplecategory.querySelector('input.choices__input');

        if ( ! fancy_select || ! select || ! input) {
            return;
        }

        const choices = fancy_select.choicesInstance;

        // fancy_select.addEventListener('change', () => {
        //     const new_category = input.value;
        //     select.add(new Option(new_category, new_category));
        //     select.value = new_category;
        //     console.log('----');
        //     console.log(select.value);
        //     choices.clearInput();
        // });

        input.addEventListener('change', (event) => {
            choices.clearInput();
        });

        input.addEventListener('keyup', (event) => {
            if (event.keyCode !== 13) {
                return;
            }

            choices.clearInput();
        });

        const setNewCategory = (() => {
            const new_category = input.value;
            choices.clearInput();

            if ( ! new_category.length) {
                return;
            }

            const new_option = new Option(new_category, new_category);

            select.add(new_option);
            select.value = new_category;

            choices._addChoice(new_option);
            choices._triggerChange(new_category);
            choices.setChoiceByValue(new_category);
            choices.clearInput();
        });
    });
})();
