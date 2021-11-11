import $ from 'jquery';

global.$ = global.jQuery = $;
import Push from "push.js";
import {initCircle} from './initCircle'

function masterNotify(data) {
    if (data.type === 'notification') {
        notifymoderator(data)
    } else if (data.type === 'refresh') {
        refresh(data)
    } else if (data.type === 'modal') {
        loadModal(data)
    } else if (data.type === 'redirect') {
        redirect(data)
    } else if (data.type === 'snackbar') {
        setSnackbar(data.message, data.color)
    } else {
        alert('Error, Please reload the page')
    }
}

function setSnackbar(text, color) {
    $('#snackbar').text(text).removeClass('bg-danger').removeClass('bg-warning').removeClass('bg-success').removeClass('d-none').addClass('show bg-' + color).click(function (e) {
        $('#snackbar').removeClass('show');
    })
}

function notifymoderator(data) {
    Push.Permission.request();
    Push.create(data.title, {
        body: data.message,
        icon: '/favicon.ico',
        link: 'test',
        onClick: function (ele) {
            window.focus();
            this.close();
        }
    });
}

function refresh(data) {
    var reloadUrl = data.reloadUrl;
    $('#waitingUserWrapper').load(reloadUrl, function () {
        initCircle();
    });
}

function loadModal(data) {
    $('#loadContentModal').html(data.content).modal('show');
}

function redirect(data) {
    setTimeout(function () {
        window.location.href = data.url;
    }, data.timeout)

}

export {masterNotify}