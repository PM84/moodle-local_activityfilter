import Modal from 'core/modal';
import Templates from "core/templates";
import {getString} from 'core/str';
import {call as fetchMany} from 'core/ajax';

const selectors = {
    searchButton: '[data-action="activityfilter-search"]',
    resultArea: '[data-region="activityfilter-results"]',
    searchPrompt: '#activitysearchprompt',
    newContentDropdown: ".course-content .course-section .divider .dropdown-menu"
};

// WS#1: already exists, returns the JSON array you showed (dummydata shape).
const WS_PROCESS = 'local_activityfilter_filter_activities';

// WS#2: new external function that accepts that array, runs PHP processing, and
//       returns rendered HTML of your results partial.
const WS_RENDER = 'local_activityfilter_prepare_results';

/**
 *
 * @param {Element} modalRoot
 * @returns {string}
 */
function getSearchPrompt(modalRoot) {
    const searchPrompt = modalRoot.querySelector(selectors.searchPrompt);
    const prompt = searchPrompt?.value || '';
    return prompt.trim();
}

/**
 *
 * @param {Element} modalRoot
 * @returns {Promise<void>}
 */
async function search(modalRoot) {
    const results = modalRoot.querySelector(selectors.resultArea);
    const prompt = getSearchPrompt(modalRoot);

    try {
        const items = await fetchMany([{
            methodname: WS_PROCESS,
            args: {prompt: prompt}
        }])[0];

        const render = await fetchMany([{
            methodname: WS_RENDER,
            args: {items: items}
        }])[0];

        results.innerHTML = (render && render.html) ? render.html : '';
    } catch (error) {
        window.console.error(error.message);

        if (results) {
            results.innerHTML = `
            <div class="alert alert-danger">
                Something went wrong. Please try again later.
            </div>`;
        }
    }
}

/**
 * Load the header text of the modal
 *
 * @returns {Promise<string>}
 */
const getModalHeader = () => getString(
    'modal_title',
    'local_activityfilter'
);

/**
 * Load the open button text of the modal
 *
 * @returns {Promise<string>}
 */
const getOpenButtonText = () => getString(
    'open_activityfilter',
    'local_activityfilter'
);

/**
 * Opens the activity filter module
 *
 * @returns {Promise<void>}
 */
async function openActivityFilter() {
    const header = await getModalHeader();
    const bodyHTML = await Templates.render('local_activityfilter/activityfilter_modal', []);

    const modal = await Modal.create({
        title: header,
        body: bodyHTML,
        footer: '',
    });
    await modal.show();

    const modalRoot = modal.getRoot()[0];
    const searchButton = modalRoot.querySelector(selectors.searchButton);

    searchButton.addEventListener('click', async () => {
        searchButton.disabled = true;
        searchButton.classList.add('disabled');
        searchButton.querySelector('.label').classList.add('d-none');
        searchButton.querySelector('.spinner-border').classList.remove('d-none');

        await search(modalRoot);

        searchButton.disabled = false;
        searchButton.classList.remove('disabled');
        searchButton.querySelector('.label').classList.remove('d-none');
        searchButton.querySelector('.spinner-border').classList.add('d-none');
    });
}

/**
 * Initializes the activity filter in the activity add menu
 *
 * @returns {Promise<void>}
 */
export async function init() {
    const newContentDropdowns = document.querySelectorAll(selectors.newContentDropdown);
    let openButtonText = await getOpenButtonText();

    newContentDropdowns.forEach(newContentDropdown => {
        const icon = document.createElement('i');
        icon.classList.add('icon', 'fa', 'fa-search');
        icon.aria_hidden = true;

        const text = document.createTextNode(openButtonText);

        const button = document.createElement('button');
        button.classList.add('dropdown-item', 'open-activityfilter');
        button.append(icon);
        button.append(text);
        button.addEventListener('click', openActivityFilter);

        if (newContentDropdown.children.length >= 1) {
            newContentDropdown.insertBefore(button, newContentDropdown.children[1]);
        } else {
            newContentDropdown.append(button);
        }
    });
}
