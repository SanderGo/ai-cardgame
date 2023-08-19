
let y = 0;
const interval = setInterval(function(){
    y += 1;
    document.body.style.backgroundPosition = '0 ' + y + 'px';
}, 10);

window.onunload = function() {
    clearInterval(interval);
};

