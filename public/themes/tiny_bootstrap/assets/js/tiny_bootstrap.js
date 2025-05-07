document.addEventListener('DOMContentLoaded', function() {
    // additional JS
});

/** Lose Focus when closing modals */
document.addEventListener('hide.bs.modal', () => {
    document.activeElement.blur();
});

/** Trongate admin JS */
const TGUI_ADMIN = (() => {
    const UI_CONSTANTS = {
        SLIDE_NAV: {
            WIDTH: "250px",
            WIDTH_CLOSED: "0",
            TRANSITION_DELAY: 500,
            Z_INDEX: 2,
            Z_INDEX_HIDDEN: -1
        }
    };

    const body = document.querySelector("body");
    const slideNav = document.getElementById("slide-nav");
    let slideNavOpen = false;

    function autoPopulateSlideNav() {
        const slideNavLinks = document.querySelector("#slide-nav ul");
        if (slideNavLinks && slideNavLinks.getAttribute("auto-populate") === "true") {
            const navLinks = document.querySelector("#top-nav");
            if (navLinks) {
                slideNavLinks.innerHTML = navLinks.innerHTML;
            }
        }
    }

    function handleSlideNavClick(event) {
        if (slideNavOpen && event.target.id !== "open-btn" && !slideNav.contains(event.target)) {
            _adminCloseSlideNav();
        }
    }

    autoPopulateSlideNav();
    body.addEventListener("click", handleSlideNavClick);

    return {
        body,
        slideNav,
        getSlideNavOpen: () => slideNavOpen,
        setSlideNavOpen: (value) => { slideNavOpen = value; },
        UI_CONSTANTS
    };
})();

const _adminOpenSlideNav = function () {
    TGUI_ADMIN.slideNav.style.opacity = 1;
    TGUI_ADMIN.slideNav.style.width = TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.WIDTH;
    TGUI_ADMIN.slideNav.style.zIndex = TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.Z_INDEX;
    setTimeout(() => {
        TGUI_ADMIN.setSlideNavOpen(true);
    }, TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.TRANSITION_DELAY);
};

const _adminCloseSlideNav = function () {
    TGUI_ADMIN.slideNav.style.opacity = 0;
    TGUI_ADMIN.slideNav.style.width = TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.WIDTH_CLOSED;
    TGUI_ADMIN.slideNav.style.zIndex = TGUI_ADMIN.UI_CONSTANTS.SLIDE_NAV.Z_INDEX_HIDDEN;
    TGUI_ADMIN.setSlideNavOpen(false);
};

// Admin-specific functions
const _setPerPage = function () {
    const perPageSelector = document.querySelector("#results-tbl select");
    if (!perPageSelector) return;

    const selectedIndex = perPageSelector.value;
    let targetUrl = `${window.location.protocol}//${window.location.hostname}${window.location.pathname}`;
    targetUrl = targetUrl.replace("/manage/", `/set_per_page/${selectedIndex}/`);
    targetUrl = targetUrl.replace("/manage", `/set_per_page/${selectedIndex}/`);
    window.location.href = targetUrl;
};

const _fetchAssociatedRecords = function (relationName, updateId) {
    const params = {
        relationName,
        updateId,
        callingModule: segment1
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}module_relations/fetch_associated_records`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function () {
        _drawAssociatedRecords(params.relationName, JSON.parse(http.responseText));
    };
};

const _drawAssociatedRecords = function (relationName, results) {
    const targetTbl = document.getElementById(`${relationName}-records`);
    if (!targetTbl) return;

    targetTbl.innerHTML = '';

    results.forEach(result => {
        const tr = document.createElement("tr");

        const tdValue = document.createElement("td");
        tdValue.textContent = result.value;

        const tdButton = document.createElement("td");
        const disBtn = document.createElement("button");
        disBtn.innerHTML = '<i class="fa fa-ban"></i> disassociate';
        disBtn.onclick = () => _openDisassociateModal(relationName, result.id);
        disBtn.className = "btn btn-danger btn-sm";

        tdButton.appendChild(disBtn);
        tr.append(tdValue, tdButton);
        targetTbl.appendChild(tr);
    });

    _populatePotentialAssociations(relationName, results);
};

const _populatePotentialAssociations = function (relationName, results) {
    const params = {
        updateId,
        relationName,
        results,
        callingModule: segment1
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}module_relations/fetch_available_options`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function () {
        const options = JSON.parse(http.responseText);
        const associateBtn = document.getElementById(`${relationName}-create`);
        const dropdown = document.getElementById(`${relationName}-dropdown`);

        if (!associateBtn || !dropdown) return;

        if (options.length > 0) {
            associateBtn.style.display = "block";
            dropdown.innerHTML = '';

            options.forEach(option => {
                const newOption = document.createElement("option");
                newOption.value = option.key;
                newOption.textContent = option.value;
                dropdown.appendChild(newOption);
            });
        } else {
            associateBtn.style.display = "none";
        }
    };
};

const _openDisassociateModal = function (relationName, recordId) {
    setTimeout(() => {
        const elId = `${relationName}-record-to-go`;
        const element = document.getElementById(elId);
        if (element) element.value = recordId;
    }, 100);

    const targetModal = document.getElementById(`${relationName}-disassociate-modal`);
    if (targetModal) {
        const modalInstance = new bootstrap.Modal(targetModal);
        modalInstance.show();
    }
};

const _disassociate = function (relationName) {
    const targetModal = document.getElementById(`${relationName}-disassociate-modal`);
    if (targetModal) {
        const modalInstance = bootstrap.Modal.getInstance(targetModal);
        if (modalInstance) modalInstance.hide();
    }

    const elId = `${relationName}-record-to-go`;
    const element = document.getElementById(elId);
    if (!element) return;

    const params = {
        updateId: element.value,
        relationName
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}module_relations/disassociate`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function () {
        _fetchAssociatedRecords(params.relationName, updateId);
    };
};

const _submitCreateAssociation = function (relationName) {
    const dropdown = document.getElementById(`${relationName}-dropdown`);
    if (!dropdown) return;

    const params = {
        updateId,
        relationName,
        callingModule: segment1,
        value: dropdown.value
    };

    const targetModal = document.getElementById(`${relationName}-create-modal`);
    if (targetModal) {
        const modalInstance = bootstrap.Modal.getInstance(targetModal);
        if (modalInstance) modalInstance.hide();
    }

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}module_relations/submit`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function () {
        _fetchAssociatedRecords(params.relationName, params.updateId);
    };
};

const _submitComment = function () {
    const textarea = document.querySelector("#comment-modal textarea");
    if (!textarea) return;

    const comment = textarea.value.trim();
    if (comment === "") return;

    textarea.value = "";
    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('comment-modal'));
    if (modalInstance) modalInstance.hide();

    const params = {
        comment,
        target_table: segment1,
        update_id: updateId
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}api/create/trongate_comments`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function () {
        if (http.status === 401) {
            window.location.href = `${baseUrl}trongate_administrators/login`;
        } else if (http.status === 200) {
            _fetchComments();
        }
    };
};

const _fetchComments = function () {
    const commentsTbl = document.querySelector("#comments-block > table");
    if (!commentsTbl) return;

    const params = {
        target_table: segment1,
        update_id: updateId,
        orderBy: "date_created"
    };

    const http = new XMLHttpRequest();
    http.open("post", `${baseUrl}api/get/trongate_comments`);
    http.setRequestHeader("Content-type", "application/json");
    http.setRequestHeader("trongateToken", token);
    http.send(JSON.stringify(params));

    http.onload = function () {
        if (http.status === 401) {
            window.location.href = `${baseUrl}trongate_administrators/login`;
        } else if (http.status === 200) {
            const comments = JSON.parse(http.responseText);

            commentsTbl.innerHTML = '';
            comments.forEach(comment => {
                const tr = document.createElement("tr");
                const td = document.createElement("td");

                const pDate = document.createElement("p");
                pDate.textContent = comment.date_created;

                const pComment = document.createElement("p");
                pComment.innerHTML = comment.comment;

                td.append(pDate, pComment);
                tr.appendChild(td);
                commentsTbl.appendChild(tr);
            });
        }
    };
};

if (typeof drawComments === "boolean") {
    _fetchComments();
}

window.openSlideNav = window.openSlideNav || _adminOpenSlideNav;
window.closeSlideNav = window.closeSlideNav || _adminCloseSlideNav;
window.setPerPage = window.setPerPage || _setPerPage;
window.fetchAssociatedRecords = window.fetchAssociatedRecords || _fetchAssociatedRecords;
window.disassociate = window.disassociate || _disassociate;
window.submitCreateAssociation = window.submitCreateAssociation || _submitCreateAssociation;
window.submitComment = window.submitComment || _submitComment;