let button = `
	<a href="#" class="wpcf7-submit button standard right white" id="" target="" tabindex="0"><div><svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 174 30" enable-background="new 0 0 174 30" xml:space="preserve">
	<path vector-effect="non-scaling-stroke" fill="none" stroke="#F5623F" stroke-width="2" d="M0,1h144c16,0,29,13,29,29v0" style="stroke-dashoffset: 0; stroke-dasharray: 189.552;"></path>
	</svg>
	<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 174 30" enable-background="new 0 0 174 30" xml:space="preserve">
	<path vector-effect="non-scaling-stroke" fill="none" stroke="#F5623F" stroke-width="2" d="M0,29h144c16,0,29-13,29-29v0" style="stroke-dashoffset: 0; stroke-dasharray: 189.553;"></path>
	</svg>
	</div><span>Send Enquiry<svg xmlns="http://www.w3.org/2000/svg" width="7.121" height="11.414" viewBox="0 0 7.121 11.414">
	<path id="Path_32" data-name="Path 32" d="M20.5,9l-5,5,5,5" transform="translate(21.207 19.707) rotate(180)" fill="none" stroke="#2c363d" stroke-miterlimit="10" stroke-width="2"></path>
	</svg>
	</span></a>
`;

// const formButtons = document.querySelectorAll('input[type="submit"]');
// formButtons.forEach((formButton) => {
//     const form = formButton.closest('.wpcf7-form');
//     formButton.classList.add('form-element-hidden');
//     formButton.insertAdjacentHTML('afterend', button);
//     const visibleButton = formButton.nextElementSibling;

//     visibleButton.addEventListener('click', (e) => {
//         e.preventDefault();
//         form.requestSubmit(formButton);
//     });
// });

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

        // Add dot span if not present
        if (!label.querySelector('.checkbox-dot')) {
            const dot = document.createElement('span');
            dot.className = 'checkbox-dot';
            label.insertBefore(dot, checkbox.nextSibling);
        }

        // Add label text span if not present
        if (!label.querySelector('.checkbox-label')) {
            // Try to find existing text node or use value
            let labelText = '';
            // If there's a .wpcf7-list-item-label, use its text
            const wpcf7Label = label.querySelector('.wpcf7-list-item-label');
            if (wpcf7Label) {
                labelText = wpcf7Label.textContent;
            } else {
                // Try to find a text node
                label.childNodes.forEach((node) => {
                    if (
                        node.nodeType === Node.TEXT_NODE &&
                        node.textContent.trim()
                    ) {
                        labelText = node.textContent.trim();
                    }
                });
                if (!labelText) {
                    labelText =
                        checkbox.getAttribute('data-label') ||
                        checkbox.value ||
                        '';
                }
            }
            // const textSpan = document.createElement('span');
            // textSpan.className = 'checkbox-label';
            // textSpan.textContent = labelText;
            // label.appendChild(textSpan);
        }
    });
}
customforms_checkbox();

function dropDownImage(image, el) {
    const optionImageURL = image.getAttribute('data-image');
    if (optionImageURL) {
        // console.log(optionImageURL);
        // Create <img> element for the image
        const optionImage = document.createElement('img');
        optionImage.src = optionImageURL;
        optionImage.alt = image.textContent; // Set alt text to option text
        el.appendChild(optionImage);
        // console.log(optionImageURL);
    }
}

function customforms_select() {
    // console.log('forms');

    // if (!isMobile()) {
    const selects = document.querySelectorAll('select');
    selects.forEach((select) => {
        // add class to the select
        select.classList.add('dropdown-select');

        // create custom dropdown wrapper
        const dropdownParent = document.createElement('div');
        // dropdownParent.className = 'custom-dropdown ' + select.className;
        dropdownParent.className = 'custom-dropdown ';

        // get data from select field
        const options = select.querySelectorAll('option');

        // custom dropdown text
        const customDropdownText = document.createElement('div');
        customDropdownText.className = 'custom-dropdown-text';
        dropdownParent.appendChild(customDropdownText);
        customDropdownText.textContent = options[0].textContent;

        // Set the selected value in the new custom dropdown
        const selectedOption = select.querySelector('option:checked');
        if (selectedOption) {
            customDropdownText.textContent = selectedOption.textContent;
        }

        // wrap the select inside a custom dropdown
        // select.parentNode.insertBefore(dropdownParent, select);
        // dropdownParent.appendChild(select);
        select.insertAdjacentElement('afterend', dropdownParent);

        // custom dropdown
        const dropdown = document.createElement('ul');
        dropdown.className = 'dropdown';
        dropdownParent.append(dropdown);

        // custom dropdown arrow
        const customArrow = document.createElement('div');
        customArrow.className = 'custom-arrow';
        customArrow.innerHTML =
            '<svg width="19" height="11" viewBox="0 0 19 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.5 1.5L9.5 9.5L1.5 1.5" stroke="#F14902" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
        dropdownParent.appendChild(customArrow);

        options.forEach((option, index) => {
            // create <li> list
            const listItem = document.createElement('li');
            dropdown.appendChild(listItem);

            // set values of each item list from <option>
            listItem.textContent = option.textContent;
            listItem.setAttribute('data-value', option.value);

            // Add image to custom dropdown <li>, data url grabbed from data-image=""
            dropDownImage(option, listItem);

            listItem.addEventListener('click', () => {
                const value = listItem.getAttribute('data-value');
                customDropdownText.textContent = listItem.textContent; // change custom-dropdown-text value

                // Update the selected option value of the associated <select>
                select.value = value;

                dispatchChangeEvent(select);

                // add selected class to parent when clicking listitem but the first placeholder
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
    // }
}

// Function to remove existing custom dropdowns
function removeCustomDropdowns() {
    const customDropdowns = document.querySelectorAll('.custom-dropdown');
    customDropdowns.forEach((customDropdown) => {
        customDropdown.parentNode.removeChild(customDropdown);
    });
}

// Function to handle FacetWP-loaded and FacetWP-refresh events
function handleFacetWPEvents() {
    removeCustomDropdowns(); // Remove existing custom dropdowns
    customforms_select(); // Reinitialize custom select
}

// Alternative function to dispatch a change event
function dispatchChangeEvent(element) {
    const event = document.createEvent('Event');
    event.initEvent('change', true, true);
    element.dispatchEvent(event);
}

customforms_select();
// document.addEventListener('facetwp-loaded', handleFacetWPEvents);
// document.addEventListener('facetwp-refresh', handleFacetWPEvents);
// document.addEventListener('load', () => {
// 	// customforms_select();
// });

// Global click event listener to close dropdowns when clicking outside
document.addEventListener('click', (event) => {
    const dropdowns = document.querySelectorAll('.custom-dropdown');
    dropdowns.forEach((dropdown) => {
        if (!dropdown.contains(event.target)) {
            dropdown.classList.remove('active');
        }
    });
});

function customforms_radio() {
    console.log('customforms_radio: start');
    const radioGroups = {};

    // Group radio buttons by their name attribute
    document.querySelectorAll('input[type="radio"]').forEach((radio) => {
        const name = radio.getAttribute('name');
        console.log('Found radio:', radio, 'name:', name);
        if (!radioGroups[name]) {
            radioGroups[name] = [];
        }
        radioGroups[name].push(radio);
    });

    // Process each group of radio buttons
    Object.entries(radioGroups).forEach(([groupName, radioGroup]) => {
        console.log('Processing radio group:', groupName, radioGroup);
        let anyChecked = false;

        // Check if any radio in the group is already selected
        radioGroup.forEach((radio) => {
            if (radio.checked) {
                anyChecked = true;
            }
        });
        console.log('Any checked in group', groupName, ':', anyChecked);

        // Add custom styling and event listeners
        radioGroup.forEach((radio, idx) => {
            console.log('Styling radio', idx, radio);
            // Ensure the radio is wrapped in a <label>
            let label = radio.closest('label');
            if (!label) {
                label = document.createElement('label');
                radio.parentNode.insertBefore(label, radio);
                label.appendChild(radio);
                console.log('Wrapped radio in new label:', label);
            }
            label.classList.add('custom-radio-group');

            // Check if the radio is already wrapped in a <span>
            let wrapper = radio.parentNode.querySelector('.custom-radio');
            if (!wrapper) {
                wrapper = document.createElement('span');
                wrapper.className = 'custom-radio';
                radio.parentNode.insertBefore(wrapper, radio);
                wrapper.appendChild(radio);
                console.log('Wrapped radio in new span:', wrapper);
            }

            // Add a span for the custom radio styling
            if (!wrapper.querySelector('span')) {
                const innerSpan = document.createElement('span');
                innerSpan.className = 'custom-radio-icon';
                // innerSpan.innerHTML = '<svg width="26" height="18" viewBox="0 0 26 18" fill="none" xmlns="http://www.w3.org/2000/svg" non-scaling-stroke><path d="M1.5 8L9.5 16L24.5 1" stroke="#EA2026" stroke-width="2"/></svg>';
                wrapper.appendChild(innerSpan);
                console.log('Added custom radio icon span:', innerSpan);
            }

            // Check if a span exists after .custom-radio and use it
            let labelSpan = label.querySelector(
                ':scope > span:not(.custom-radio)'
            );
            if (!labelSpan) {
                labelSpan = document.createElement('span');
                labelSpan.className = 'custom-radio-icon';
                label.appendChild(labelSpan);
                console.log('Added label span for radio text:', labelSpan);
            }
            labelSpan.className = 'custom-radio-text';
            labelSpan.textContent =
                radio.getAttribute('data-label') || radio.value;
            console.log('Set label span text:', labelSpan.textContent);

            // Set active class if the radio is checked
            if (radio.checked) {
                label.classList.add('selected');
                console.log('Radio is checked, added selected class to label');
            }

            // Update on change
            radio.addEventListener('change', () => {
                console.log('Radio changed:', radio, 'in group', groupName);
                // Deselect all other radios in the same group
                radioGroup.forEach((input) => {
                    const inputLabel = input.closest('label');
                    if (inputLabel) {
                        inputLabel.classList.remove('selected');
                    }
                });

                // Add active class to the current radio
                label.classList.add('selected');
                console.log('Added selected class to label for changed radio');
            });
        });
    });
    console.log('customforms_radio: end');
}
customforms_radio();

function handleFileChange() {
    document.querySelectorAll('input.wpcf7-file').forEach((input) => {
        // Don't double-init
        if (input.closest('.custom-file-input-wrapper')) return;

        const defaultLabel = 'No file chosen';

        // Build wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'custom-file-input-wrapper';

        // Label (shows filename / placeholder)
        const fileLabel = document.createElement('span');
        fileLabel.className = 'custom-file-input-label';
        fileLabel.textContent = defaultLabel;

        // Button
        const button = document.createElement('span');
        button.className = 'custom-file-input-button';
        button.textContent = 'Upload File';

        // Insert wrapper before the input, move input inside
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
        wrapper.appendChild(fileLabel);
        wrapper.appendChild(button);

        // Update label on file select
        input.addEventListener('change', function () {
            fileLabel.textContent =
                this.files.length > 0 ? this.files[0].name : defaultLabel;
        });
    });
}
// document.addEventListener('DOMContentLoaded', handleFileChange);
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

function moveSubmitButtonOnLoad() {
    const form = document.querySelector('.wpcf7-form');
    const submitButton = document.querySelector('.wpcf7-submit');

    // Check if the form and submit button exist
    if (form && submitButton) {
        const steps = form.querySelectorAll('fieldset'); // All the fieldsets (steps)

        if (steps.length > 0) {
            const lastStep = steps[steps.length - 1]; // Get the last fieldset

            console.log(lastStep);

            const backButton = lastStep.querySelector('.cf7mls_back'); // Find the back button in the last fieldset
            console.log(backButton);
            if (backButton) {
                // Move the submit button next to the back button if in the last step
                backButton.parentNode.appendChild(submitButton);
            } else {
                // Ensure the submit button is back in its original place if there's no back button
                const btnsContainer = form.querySelector('.cf7mls-btns');
                if (btnsContainer) {
                    btnsContainer.appendChild(submitButton);
                }
            }
        }
    }
    // initializeButtonEffect();
}
document.addEventListener('DOMContentLoaded', moveSubmitButtonOnLoad);

// Define the function to handle form submission
function onCF7FormSuccess(event) {
    const formId = event.detail.contactFormId;
    if (formId === 1414) {
        console.log('Form ID 1414 submitted successfully!');
        console.log('Thank you for your submission!');
    }
}
// document.addEventListener('wpcf7submit', onCF7FormSuccess, false);

// document.addEventListener('wpcf7submit', function(event) {
//     const formId = event.detail.contactFormId;
//     const status = event.detail.status;

//     console.log(event.detail.contactFormId)
//     console.log(event.detail.status)

//     if ('1406' == formId && status === 'mail_sent') {
//         console.log(formId);

//         // Find the form by its data-wpcf7-id attribute
//         const form = document.querySelector(`[data-wpcf7-id="${formId}"]`);
//         if (form) {
//             form.classList.add('form-submitted');
//             console.log('Class "form-submitted" added to the form.');

//             // Find .wpcf7-response-output
//             const responseOutput = form.querySelector('.wpcf7-response-output');
//             console.log(responseOutput);

//             if (responseOutput) {

//                 responseOutput.style.opacity = '0';

//                 // Create a wrapper for the new content to avoid overwriting existing content
//                 const wrapper = document.createElement('div');
//                 wrapper.classList.add('custom-buttons');
//                 wrapper.innerHTML = `
//                     <div class="c-button-group">
//                         <a href="/get-in-touch" class="c-button-outline--white">
//                             <span class="fill"></span>
//                             <span class="text">Go to home</span>
//                         </a>
//                         <button type="button" aria-label="Reset form" class="c-button-solid--white interchangeable reset-form">
//                             <span class="fill"></span>
//                             <span class="text">Reset Form</span>
//                         </button>
//                     </div>`;

//                 // Append the wrapper only if it doesn't already exist
//                 if (!responseOutput.querySelector('.custom-buttons')) {
//                     responseOutput.appendChild(wrapper);
//                     console.log('HTML added to .wpcf7-response-output.');
//                 }

//                 // Add reset functionality to the reset button
//                 const resetButton = wrapper.querySelector('.reset-form');
//                 if (resetButton) {
//                     resetButton.addEventListener('click', () => {
//                         const formElement = form.querySelector('form');
//                         if (formElement) {
//                             formElement.reset();
//                             console.log('Form has been reset.');
//                         } else {
//                             console.log('Form element not found for reset.');
//                         }
//                     });
//                 }

//                 setTimeout(() => {
//                     responseOutput.style.opacity = '1';
//                 }, 500); // 100ms delay, adjust as needed

//             } else {
//                 console.log('.wpcf7-response-output not found.');
//             }
//         } else {
//             console.log('Form not found.');
//         }
//     }
// }, false);
