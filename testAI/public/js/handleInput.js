document.addEventListener("DOMContentLoaded", function() {
    if (document.getElementById("roomCodeInput")) {
        var roomCodeInput = document.getElementById("roomCodeInput");
        roomCodeInput.addEventListener("keypress", handleInput);
        roomCodeInput.addEventListener("paste", preventPaste);
    }
    else if (document.getElementById("playerNameInput")) {
        var playerNameInput = document.getElementById("playerNameInput");
        playerNameInput.addEventListener("keypress", handleInput);
        playerNameInput.addEventListener("paste", preventPaste);
    }
});


function preventPaste(event) {
    event.preventDefault();
}

function handleInput(event) {
    var charCode = event.which || event.keyCode;
    var charStr = String.fromCharCode(charCode);

    var alphanumericRegex = /^[a-zA-Z0-9]+$/;

    if (!alphanumericRegex.test(charStr)) {
        event.preventDefault();
        return false;
    }

    var maxLength = event.target.getAttribute("data-maxlength");

    if (event.target.value.length >= maxLength) {
        event.preventDefault();
        return false;
    }

    return true;
}
