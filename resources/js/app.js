require('./bootstrap');

$('#branchSearch').on('keyup', function () {
    let value = $(this).val().toLowerCase();
    $(".branch-ul .branch-name").filter(function () {
        let shouldHide = $(this).text().toLowerCase().indexOf(value) > -1;
        $(this).parent().toggle(shouldHide);
    });
});

$('input[type=radio][name=project-radio]').change(function () {
    let _token = $('meta[name="csrf-token"]').attr('content');
    let project = this.value;
    const spinner = _getBranchSpinner();

    let ul = $('.branch-ul');
    let fallbackHtml = ul.html();
    ul.empty();
    ul.parent().append(spinner);

    $.ajax({
        url: "/fetch-branches/" + project,
        type: "GET",
        data: {
            _token: _token
        },
        success: function (response) {
            $('#branch-spin').remove();
            response = Object.values(response);
            response.sort((a, b) => {
                if (a['name'] === b['name']) {
                    return 0;
                } else if (a['pull_link'] === null && b['pull_link'] === null) {
                    return 0;
                } else if (a['pull_link'] === null) {
                    return 1;
                } else if (b['pull_link'] === null) {
                    return -1;
                } else {
                    return a['name'] < b['name'] ? 1 : -1;
                }
            });
            for (let i in response) {
                if (response.hasOwnProperty(i)) {
                    let li = _buildBranchLi(response[i]);
                    $('.branch-ul').append(li);
                } else {
                    console.log('Big wtf with own property happened');
                }
            }
        },
        error: function () {
            $('#branch-spin').remove();
            ul.html(fallbackHtml);
        }
    });
});

$('.deploy-btn').on('click', function () {
    let _token = $('meta[name="csrf-token"]').attr('content');
    let branch = $(this).data('pr');
    let repo = $('input[name=project-radio]:checked', '.form-check').val();

    const spinner = _getDeploySpinner();

    let deployingSpinner = $('.deploying-spinner-block');
    deployingSpinner.append(spinner);

    $.ajax({
        url: "/deploy-branch/" + repo + "/" + branch,
        type: "GET",
        data: {
            _token: _token
        },
        success: function (response) {
            deployingSpinner.empty();
            let alertElem = _getDismissibleAlert(response, repo, branch);
            deployingSpinner.append(alertElem);
        },
        error: function () {
            deployingSpinner.empty();
            let alertElem = _getDismissibleAlert(false, repo, branch);
            deployingSpinner.append(alertElem);
        }
    });
});

function _getBranchSpinner() {
    return '<div class="d-flex justify-content-center" id="branch-spin">' +
        '<div class="spinner-border m-5" style="width: 4rem;height:4rem;" role="status">\n' +
        '<span class="sr-only">Loading...</span>\n' +
        '</div>' +
        '</div>';
}

function _getDeploySpinner() {
    return '<div class="d-flex justify-content-center" id="deploy-spin">' +
        '<div class="spinner-border text-primary m-5" style="width: 4rem;height:4rem;" role="status">\n' +
        '<span class="sr-only">Loading...</span>\n' +
        '</div>' +
        '</div>';
}

function _getDismissibleAlert(deployResult, project, branch) {
    if (deployResult) {
        return '<div class="alert alert-success alert-dismissible fade show" role="alert">\n' +
            `  <span class="badge badge-primary">${project}</span>` +
            `  <strong>${branch}.</strong> Deployed successfully.\n` +
            '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
            '    <span aria-hidden="true">&times;</span>\n' +
            '  </button>\n' +
            '</div>';
    } else {
        return '<div class="alert alert-warning alert-dismissible fade show" role="alert">\n' +
            `  <span class="badge badge-primary">${project}</span>` +
            `  Failed to deploy <strong>${branch}.</strong> Please, try again later.\n` +
            '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
            '    <span aria-hidden="true">&times;</span>\n' +
            '  </button>\n' +
            '</div>';
    }
}

function _buildBranchLi(branchInfo) {
    let li = '<li class="list-group-item" style="display: flex">';
    if (branchInfo['user'] !== null) {
        li += `<span class="badge badge-primary" style="height:16px;margin-top:14px;margin-right:5px">${branchInfo['user']}</span>`;
    }
    li += `<span class="branch-name" style="flex-grow: 1;padding-top:10px">${branchInfo['name']}</span>`
    li += '<div class="buttons-action">';
    if (branchInfo['pull_link'] !== null) {
        li += `<a href="${branchInfo['pull_link']}" class="btn btn-info" style="margin:5px;">Link to PR</a>`;
    }
    li += `<button class="btn btn-success deploy-btn" data-pr="${branchInfo['name']}" style="margin:5px;">Deploy</button>`;
    li += '</div>';
    li += '</li>';

    return li;
}
