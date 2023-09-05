//适配兼容

function setRemFontSize() {
    var remSize = window.innerWidth / 7.5;
    document.querySelector("html").style.fontSize = (remSize > 100 ? 100 : remSize) + "px";
}
setRemFontSize();
        window.addEventListener("resize", function () {
            setTimeout(function () {
                setRemFontSize();
            }, 200)
        });