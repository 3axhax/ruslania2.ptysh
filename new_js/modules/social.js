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
    return window.open(t.getAttribute('href'), t.innerHTML, 'width=800,height=600');
}

function closeWindow(authWindow, userData, url) {
    authWindow.close();
    if ('error' in userData) {
        console.log(userData);
        //g('errors').innerHTML = userData['error'];
    }
    window.location.href = url;
}
