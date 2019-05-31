var vkCom = function() {
    var authWindow;
    function getUserInfo(t) {
        authWindow = openWindow(t);
        return false;
    }

    function setUserInfo(userData, url) { closeWindow(authWindow, userData, url); }
    return {
        setUserInfo: setUserInfo,
        getUserInfo: getUserInfo
    }
}();

var twitterCom = function() {
    var authWindow;
    function getUserInfo(t) {
        authWindow = openWindow(t);
        return false;
    }

    function setUserInfo(userData, url) { closeWindow(authWindow, userData, url); }
    return {
        setUserInfo: setUserInfo,
        getUserInfo: getUserInfo
    }
}();

var facebookCom = function() {
    var authWindow;
    function getUserInfo(t) {
        authWindow = openWindow(t);
        return false;
    }

    function setUserInfo(userData, url) { closeWindow(authWindow, userData, url); }
    return {
        setUserInfo: setUserInfo,
        getUserInfo: getUserInfo
    }
}();

var instagramCom = function() {
    var authWindow;
    function getUserInfo(t) {
        authWindow = openWindow(t);
        return false;
    }

    function setUserInfo(userData, url) {
        closeWindow(authWindow, userData, url);
    }
    return {
        setUserInfo: setUserInfo,
        getUserInfo: getUserInfo
    }
}();

function openWindow(t) {
    return window.open(t.getAttribute('href'), t.innerHTML);
}

function closeWindow(authWindow, userData, url) {
    authWindow.close();
    if ('error' in userData) {
        console.log(userData);
    }
    if (url != '') window.location.href = url;
    else {
        if ('email' in userData) {
            $('#User_login').val(userData['email']);
        }
        if ('name' in userData) {
            $('#User_first_name').val(userData['name']);
        }
        else if ('first_name' in userData) {
            $('#User_first_name').val(userData['first_name']);
        }
        else if ('full_name' in userData) {
            $('#User_first_name').val(userData['full_name']);
        }
        if ('last_name' in userData) {
            $('#User_last_name').val(userData['last_name']);
        }
        if ('id' in userData) {
            var $form = $('#user-register');
            if ($form.length) {
                var $socId = $('input[name=useSocial]');
                if ($socId.length) $socId.val(1);
                else $socId = $('<input type="hidden" name="useSocial" value="1">');
                $form.append($socId);
            }
        }
    }
}
