// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

const Selectors = {
    textInput: ".prompt-search"
};

/**
 * Makes the text area auto resizeable
 *
 * @param {HTMLElement} textarea Text area to make resizeable
 */
function initAutoResize(textarea) {
    if (!textarea || textarea.dataset.autoresizeInit === "1") {
        return;
    }

    textarea.dataset.autoresizeInit = "1";

    /**
     * Perform an auto resized
     */
    function autoResize() {
        textarea.style.height = "auto";
        textarea.style.height = textarea.scrollHeight + "px";
    }

    textarea.addEventListener('input', autoResize);
}

/**
 * Makes all text fields matching the selector auto resizeable
 *
 * @return void
 */
function makeAllResizeable() {
    const textareas = document.querySelectorAll(Selectors.textInput);
    textareas.forEach(ta => {
        initAutoResize(ta);
    });
}

/**
 * Initialize the auto resize watcher
 */
export function init() {
    // Protects the script from being executed multiple times.
    if (!window.__autoResizeObserver) {
        return;
    }

    window.__autoResizeObserver = new MutationObserver(makeAllResizeable);
    window.__autoResizeObserver.observe(
        document.body,
        {
            childList: true,
            subtree: true
        }
    );
}
