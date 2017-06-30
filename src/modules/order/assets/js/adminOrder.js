/**
 * @copyright Reinvently (c) 2017
 * @link http://reinvently.com/
 * @license https://opensource.org/licenses/Apache-2.0 Apache License 2.0
 */

$(function () {

    // Get user data
    $('#order-userid').on('change', function () {
        $.ajax({
            url: '/api/users/' + $(this).val(),
            headers: {'Authorization': 'Bearer ' + authToken},
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.data) {
                    $('#order-firstname').val(response.data.firstName);
                    $('#order-lastname').val(response.data.lastName);
                    $('#order-phone').val(response.data.phone);
                }
            }
        })
    });

    // Create order
    $('.order-create-form').on('click', function () {
        $.ajax({
            url: '/api/orders',
            headers: {'Authorization': 'Bearer ' + authToken},
            type: 'POST',
            data: {
                userId: $('#order-userid').val(),
                firstName: $('#order-firstname').val(),
                lastName: $('#order-lastname').val(),
                phone: $('#order-phone').val()
            },
            dataType: 'json',
            success: function (response) {
                if (response.data) {
                    window.location.href = '/admin/order/update/' + response.data.id;
                }
            }
        });

        return false;
    });

    getStateList();

    $(document).on('click', '.stateTransit', function () {
        $.ajax({
            url: '/api/order/state',
            headers: {'Authorization': 'Bearer ' + authToken},
            type: 'POST',
            data: {id: $("#stateMachine").data('id'), state: $(this).data('state')},
            dataType: 'json',
            success: function (response) {
                window.location.reload();
            }
        });
    });

});

function getStateList() {
    $.ajax({
        url: '/api/order/state-list',
        headers: {'Authorization': 'Bearer ' + authToken},
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.data) {
                addStateTransitionButtons(response.data.stateMachine);
            }
        }
    });
}

function addStateTransitionButtons(stateList) {
    var status = $('#stateMachine').data('status');
    if (typeof stateList[status] != 'undefined') {
        $.each(stateList[status], function (key, value) {
            $('#stateMachine').append('<a class="btn btn-warning stateTransit" data-state="' + value + '" href="#"> Transfer to "' + stateNames[value] + '"</a> ')
        });
        $('#stateMachine').append('<br /><br />');
    }
}