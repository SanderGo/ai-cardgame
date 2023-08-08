function submitAnswer() {
    var userInput = document.getElementById("code").value;
    sessionStorage.setItem("userInput", userInput);
    window.location.href = nextUrl;
}

window.onload = function() {
    var duration = 30;
    var display = document.querySelector('#time');
    startTimer(duration, display);
};

function startTimer(duration, display) {
    var timer = duration, minutes, seconds;
    var countdown = setInterval(function() {
        minutes = parseInt(timer / 60, 10);
        seconds = parseInt(timer % 60, 10);

        minutes = minutes < 10 ? "0" + minutes : minutes;
        seconds = seconds < 10 ? "0" + seconds : seconds;

        display.textContent = minutes + ":" + seconds;

        if (--timer < 0) {
            clearInterval(countdown);
            window.location.href = nextUrl;
        }
    }, 1000);
}
