let contactHolder = document.getElementById("contacts-holder");
let selectedFile = null; // Хранит выбранный файл для загрузки аватарки

function resetAddButton() {
    $("#confirm-add").off("click");
    $("#confirm-add").on("click", addContact);
}

resetAddButton();

// Обработчик для кнопки "Добавить контакт"
$("#add-button").on("click", () => {
    $("#add-contact").show()
    $("#preview-add").attr("src", "defaultcontact.svg"); // Устанавливаем изображение по умолчанию
    selectedFile = null;
});

$("#cancel-add").on("click", () => {
    $("#add-contact").hide()
    $("#inp_phone").val("")
    $("#inp_firstname").val("")
    $("#inp_lastname").val("")
    $("#inp_description").val("")
})

// Обработчик для выбора аватарки
$('#pic-add').on('change', function (event) {
    const image = document.getElementById('preview-add');
    selectedFile = event.target.files[0];
    if (selectedFile) {
        image.src = URL.createObjectURL(selectedFile);
    }
});

$("#exit").on("click", () => {
    $.post(".", {
        isExit: "true"
    }, () => {
        document.location.reload();
    });
});

// Добавление контакта
function addContact() {
    const phoneRegexp = /^[\+]?[0-9]?[0-9]?[0-9]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4}$/;
    if (phoneRegexp.test($("#inp_phone").val())) {
        const formData = new FormData();
        formData.append("isCreate", true);
        formData.append("phone", $("#inp_phone").val());
        formData.append("firstname", $("#inp_firstname").val());
        formData.append("lastname", $("#inp_lastname").val());
        formData.append("description", $("#inp_description").val());
        formData.append("avatar", selectedFile || "defaultcontact.svg");

        $.ajax({
            url: ".",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (data) => {
                data = JSON.parse(data)
                console.log(data)
                if(data["success"]){
                    $("#add-contact").hide()
                    loadContacts()
                } else {
                    alert(data["message"])
                }
            }
        });

        $("#add-contact").hilde()
    } else {
        alert("Error phone format");
    }
}

// Шаблон отображения контакта
function completeTemplate(id, avatar, number, firstname, lastname, description) {
    return `<div class="contact" id="contact-${id}"> 
                <img class="contact-avatar" onerror="this.onerror=null; this.src='defaultcontact.svg'" src="uploads/${avatar}" alt="defaultcontact.svg">
                <img class="delete-ico" src="delete.svg" alt="del" id="del-${id}">
                <img class="delete-ico" src="edit.svg" alt="edt" id="edt-${id}">
                ${firstname} ${lastname} <br>
                ${number} <br>
                ${description}
            </div>`;
}

// Загрузка контактов с сервера
function loadContacts() {
    $.post(".", { isRead: true }, (response) => {
        const contacts = JSON.parse(response).contacts;
        contactHolder.innerHTML = ""; // Очищаем контейнер контактов
        contacts.forEach(contact => {
            contactHolder.innerHTML += completeTemplate(contact.id, contact.avatar, contact.telephone_number, contact.firstname, contact.lastname, contact.description);

            // Обработчик для удаления
            $(`#del-${contact.id}`).on("click", () => deleteContact(contact.id));

            // Обработчик для редактирования
            $(`#edt-${contact.id}`).on("click", () => editContact(contact));
        });
    });
}

// Удаление контакта
function deleteContact(id) {
    $.post(".", { isDelete: true, id: id }, loadContacts);
}

// Редактирование контакта
function editContact(contact) {
    const editForm = document.createElement("div");
    editForm.className = "edit-form";
    editForm.innerHTML = `
        <input type="text" id="edit_phone" value="${contact.telephone_number}">
        <input type="text" id="edit_firstname" value="${contact.firstname}">
        <input type="text" id="edit_lastname" value="${contact.lastname}">
        <textarea id="edit_description">${contact.description}</textarea>
        <input type="file" id="edit_pic">
        <img id="edit_preview" src="cntimgs/${contact.avatar}" alt="defaultcontact.svg">
        <button id="confirm-edit">Confirm</button>
        <button id="cancel-edit">Cancel</button>
    `;

    document.body.appendChild(editForm);

    // Предпросмотр изображения при редактировании
    $('#edit_pic').on('change', function (event) {
        const image = document.getElementById('edit_preview');
        selectedFile = event.target.files[0];
        if (selectedFile) {
            image.src = URL.createObjectURL(selectedFile);
        }
    });

    // Обработчик подтверждения редактирования
    $('#confirm-edit').on('click', () => {
        const formData = new FormData();
        formData.append("isUpdate", true);
        formData.append("id", contact.id);
        formData.append("phone", $('#edit_phone').val());
        formData.append("firstname", $('#edit_firstname').val());
        formData.append("lastname", $('#edit_lastname').val());
        formData.append("description", $('#edit_description').val());
        formData.append("avatar", selectedFile || contact.avatar);

        $.ajax({
            url: ".",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: () => {
                loadContacts();
                document.body.removeChild(editForm);
            }
        });
    });

    // Обработчик отмены редактирования
    $('#cancel-edit').on('click', () => {
        document.body.removeChild(editForm);
    });
}

// Загрузка контактов при загрузке страницы
loadContacts();
