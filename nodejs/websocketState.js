import {
    loginUser,
    getOnlineUSer,
    setStatus,
    stillOnline,
    enterMeeting,
    leaveMeeting,
    getUserStatus,
    getUserFromSocket, disconnectUser, checkEmptySockets
} from './login.js'
import {io} from './websocket.js'

export function websocketState(event, socket, message) {

    switch (event) {
        case 'disconnect':
            disconnectUser(socket);
            setTimeout(function () {
                if (checkEmptySockets()) {
                    io.emit('sendOnlineUser', JSON.stringify(getOnlineUSer()));
                }
            }, 7000);
            sendStatus(socket);
            break;
        case 'login':
            loginUser(socket);
            io.emit('sendOnlineUser', JSON.stringify(getOnlineUSer()));
            break;
        case 'setStatus':
            loginUser(socket);
            var tmp = setStatus(socket, message);
            sendStatus(socket);
            break;
        case 'getStatus':
            io.emit('sendOnlineUser', JSON.stringify(getOnlineUSer()));
            break;
        case 'getMyStatus':
            socket.emit('sendUserStatus', getUserStatus(socket));
            break;
        case 'stillOnline':
            stillOnline(socket);
            break;
        case 'enterMeeting':
            enterMeeting(socket);
            sendStatus(socket);
            break;
        case 'leaveMeeting':
            leaveMeeting(socket);
            sendStatus(socket);
            break;
        default:
            console.log(event);
            console.log('not known')
            break;
    }
}

function sendStatus(socket) {
    var user = getUserFromSocket(socket)
    for (var prop in user.getSockets()) {
        var tmpSocket = user.getSockets()[prop];
        tmpSocket.emit('sendUserStatus', getUserStatus(tmpSocket));
    }
    io.emit('sendOnlineUser', JSON.stringify(getOnlineUSer()));
}
