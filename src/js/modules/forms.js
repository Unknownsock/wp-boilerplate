function customforms_checkbox() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach((checkbox) => {
        // Only wrap if not already inside a label
        let label = checkbox.closest('label');
        if (!label) {
            label = document.createElement('label');
            label.className = 'custom-checkbox';
            checkbox.parentNode.insertBefore(label, checkbox);
            label.appendChild(checkbox);
        } else {
            label.classList.add('custom-checkbox');
        }

        if (!label.querySelector('.checkbox-dot')) {
            const dot = document.createElement('span');
            dot.className = 'checkbox-dot';
            label.insertBefore(dot, checkbox.nextSibling);
        }
    });
}
customforms_checkbox();

function dropDownImage(image, el) {
    const optionImageURL = image.getAttribute('data-image');
    if (optionImageURL) {
        const optionImage = document.createElement('img');
        optionImage.src = optionImageURL;
        optionImage.alt = image.textContent;
        el.appendChild(optionImage);
    }
}

function customforms_select() {
    const selects = document.querySelectorAll('select');
    selects.forEach((select) => {
        select.classList.add('dropdown-select');

        const dropdownParent = document.createElement('div');
        dropdownParent.className = 'custom-dropdown ';

        const options = select.querySelectorAll('option');

        const customDropdownText = document.createElement('div');
        customDropdownText.className = 'custom-dropdown-text';
        dropdownParent.appendChild(customDropdownText);
        customDropdownText.textContent = options[0].textContent;

        const selectedOption = select.querySelector('option:checked');
        if (selectedOption) {
            customDropdownText.textContent = selectedOption.textContent;
        }

        select.insertAdjacentElement('afterend', dropdownParent);

        const dropdown = document.createElement('ul');
        dropdown.className = 'dropdown';
        dropdownParent.append(dropdown);

        const customArrow = document.createElement('div');
        customArrow.className = 'custom-arrow';
        customArrow.innerHTML =
            '<svg width="19" height="11" viewBox="0 0 19 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.5 1.5L9.5 9.5L1.5 1.5" stroke="#F14902" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        dropdownParent.appendChild(customArrow);

        options.forEach((option, index) => {
            const listItem = document.createElement('li');
            dropdown.appendChild(listItem);

            listItem.textContent = option.textContent;
            listItem.setAttribute('data-value', option.value);

            dropDownImage(option, listItem);

            listItem.addEventListener('click', () => {
                const value = listItem.getAttribute('data-value');
                customDropdownText.textContent = listItem.textContent;

                select.value = value;

                dispatchChangeEvent(select);

                // index 0 is the placeholder, not a real selection
                if (index > 0) {
                    dropdownParent.classList.add('selected');
                } else {
                    dropdownParent.classList.remove('selected');
                }
            });
        });

        dropdownParent.addEventListener('click', () => {
            dropdownParent.classList.toggle('active');
        });
    });
}

function dispatchChangeEvent(element) {
    const event = document.createEvent('Event');
    event.initEvent('change', true, true);
    element.dispatchEvent(event);
}

customforms_select();

document.addEventListener('click', (event) => {
    const dropdowns = document.querySelectorAll('.custom-dropdown');
    dropdowns.forEach((dropdown) => {
        if (!dropdown.contains(event.target)) {
            dropdown.classList.remove('active');
        }
    });
});

function customforms_radio() {
    const radioGroups = {};

    document.querySelectorAll('input[type="radio"]').forEach((radio) => {
        const name = radio.getAttribute('name');
        if (!radioGroups[name]) {
            radioGroups[name] = [];
        }
        radioGroups[name].push(radio);
    });

    Object.values(radioGroups).forEach((radioGroup) => {
        radioGroup.forEach((radio) => {
            let label = radio.closest('label');
            if (!label) {
                label = document.createElement('label');
                radio.parentNode.insertBefore(label, radio);
                label.appendChild(radio);
            }
            label.classList.add('custom-radio-group');

            let wrapper = radio.parentNode.querySelector('.custom-radio');
            if (!wrapper) {
                wrapper = document.createElement('span');
                wrapper.className = 'custom-radio';
                radio.parentNode.insertBefore(wrapper, radio);
                wrapper.appendChild(radio);
            }

            if (!wrapper.querySelector('span')) {
                const innerSpan = document.createElement('span');
                innerSpan.className = 'custom-radio-icon';
                wrapper.appendChild(innerSpan);
            }

            let labelSpan = label.querySelector(
                ':scope > span:not(.custom-radio)'
            );
            if (!labelSpan) {
                labelSpan = document.createElement('span');
                labelSpan.className = 'custom-radio-icon';
                label.appendChild(labelSpan);
            }
            labelSpan.className = 'custom-radio-text';
            labelSpan.textContent =
                radio.getAttribute('data-label') || radio.value;

            if (radio.checked) {
                label.classList.add('selected');
            }

            radio.addEventListener('change', () => {
                radioGroup.forEach((input) => {
                    const inputLabel = input.closest('label');
                    if (inputLabel) {
                        inputLabel.classList.remove('selected');
                    }
                });
                label.classList.add('selected');
            });
        });
    });
}
customforms_radio();

function handleFileChange() {
    document.querySelectorAll('input.wpcf7-file').forEach((input) => {
        // Don't double-init
        if (input.closest('.custom-file-input-wrapper')) return;

        const defaultLabel = 'No file chosen';

        const wrapper = document.createElement('div');
        wrapper.className = 'custom-file-input-wrapper';

        const fileLabel = document.createElement('span');
        fileLabel.className = 'custom-file-input-label';
        fileLabel.textContent = defaultLabel;

        const button = document.createElement('span');
        button.className = 'custom-file-input-button';
        button.textContent = 'Upload File';

        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        wrapper.appendChild(fileLabel);
        wrapper.appendChild(button);

        input.addEventListener('change', function () {
            fileLabel.textContent =
                this.files.length > 0 ? this.files[0].name : defaultLabel;
        });
    });
}
handleFileChange();

// Populate [hidden your-subject] fields with the current page title
function injectSubject() {
    const pageTitle = document.title
        .replace(/\s*[|\-–]\s*.+$/, '') // strip site name after separator
        .trim();

    document
        .querySelectorAll('input.wpcf7-hidden[name="your-subject"]')
        .forEach((input) => {
            input.value = pageTitle;
        });
}
injectSubject();
document.addEventListener('wpcf7mailsent', injectSubject, false);
document.addEventListener('wpcf7invalid', injectSubject, false);

// Multi-step Contact Form 7 (cf7mls plugin) keeps its own back/next
// buttons per fieldset step - this re-parents the actual submit button
// next to the back button on the last step, since cf7mls doesn't do that
// itself. No-ops harmlessly if that plugin/markup isn't present.
function moveSubmitButtonOnLoad() {
    const form = document.querySelector('.wpcf7-form');
    const submitButton = document.querySelector('.wpcf7-submit');

    if (form && submitButton) {
        const steps = form.querySelectorAll('fieldset');

        if (steps.length > 0) {
            const lastStep = steps[steps.length - 1];
            const backButton = lastStep.querySelector('.cf7mls_back');
            if (backButton) {
                backButton.parentNode.appendChild(submitButton);
            } else {
                const btnsContainer = form.querySelector('.cf7mls-btns');
                if (btnsContainer) {
                    btnsContainer.appendChild(submitButton);
                }
            }
        }
    }
}
document.addEventListener('DOMContentLoaded', moveSubmitButtonOnLoad);
