$("#login-button").on("click", () => {
    $.post("./index.php", {
        isLogin: true,
        login: $("#login").val(),
        password: $("#password").val()
    }, (data)=>{
        data = JSON.parse(data)
        if(data["status"] == "success") document.location.reload()
        else alert(data["error"])
    })
})

$("#registr-button").on("click", () => {
    $.post("./index.php", {
        isRegister: true,
        login: $("#login").val(),
        password: $("#password").val()
    }, (data)=>{
        data = JSON.parse(data)
        if(data["status"] == "success") document.location.reload()
        else alert(data["error"])
    })
})

$("#swap1").on("click", () => {
    $("#registr-button").toggle("display")
    $("#login-button").toggle("display")
    $("#swap2").toggle("display")
    $("#swap1").toggle("display")
})

$("#swap2").on("click", () => {
    $("#registr-button").toggle("display")
    $("#login-button").toggle("display")
    $("#swap2").toggle("display")
    $("#swap1").toggle("display")
})