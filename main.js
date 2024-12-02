var contactHolder = document.getElementById("contacts-holder")

function resetAddButton(){
    $("#confirm-add").off("click");
    $("#confirm-add").on("click", addContact)
}

resetAddButton()

$("#add-button").on("click", () => {
    document.getElementById("add-contact").className = "";
    document.getElementById("confirm-add").innerHTML = "Додати"
})

function addContact(){
    const regexp = /^[\+]?[0-9]?[0-9]?[0-9]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4}$/
    if(regexp.test($("#inp_phone").val())){
        $.post(".", {
            operation: "CREATE",
            phone: $("#inp_phone").val(),
            firstname: $("#inp_firstname").val(),
            lastname: $("#inp_lastname").val(),
            description: $("#inp_description").val()
        }, loadContacts)
        document.getElementById("add-contact").className = "hidden";
    } else {
        alert("Error phone format")
    }
}

function genEditContact(id) {
    return function () {
        $.post(".", {
            operation: "UPDATE",
            id: id,
            phone: $("#inp_phone").val(),
            firstname: $("#inp_firstname").val(),
            lastname: $("#inp_lastname").val(),
            description: $("#inp_description").val()
        }, () => {
            loadContacts()
            resetAddButton();
            document.getElementById("add-contact").className = "hidden";
        })
    }
}



function completeTemplate(id, number, firstname, lastname, desription){
    return `<div class="contact" id="${id}"> 
                <img class="contact-avatar" onerror="this.onerror=null; this.src='defaultcontact.svg'" src="cntimgs/${id}.png" alt="defaultcontact.svg">
                <img class="delete-ico" src="delete.svg" alt="del" id="del-${id}">
                <img class="delete-ico" src="edit.svg" alt="edt" id="edt-${id}">
                ${firstname} ${lastname} <br>
                ${number} <br>
                ${desription}
            </div>`
}

function loadContacts () {
    $.post(".", {
        operation: "READ"
    }, (data) => {
        console.log(JSON.parse(data))
        if(data.length == 0) return
        contactHolder.innerHTML = "";
        JSON.parse(data).forEach(el => {
            console.log(el[0])
            contactHolder.innerHTML += completeTemplate(el[0], el[1], el[2], el[3], el[4])
        })
        contactHolder.childNodes.forEach(el => {
            console.log(el.id);
            $(`#del-${el.id}`).on("click", () => {
                console.log(`${el.id}`)
                $.post(".", {
                    operation: "DELETE",
                    data: el.id
                }, loadContacts)
            })
            $(`#edt-${el.id}`).on("click", () => {
                $("#confirm-add").off("click");
                $("#confirm-add").on("click", genEditContact(el.id))
                document.getElementById("confirm-add").innerHTML = "Внести зміни"
                document.getElementById("add-contact").className = "";
            })
        });

    })

    
}

loadContacts()