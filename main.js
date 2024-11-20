function loadContacts () {
    retData = {}
    $.post(".", {
        operation: "READ",
        test: "sdfnksdfl"
    }, (data, status) => {
        retData = data;
    })
    return retData
}

contacts = loadContacts()

contacts.forEach(cnt => {
    console.log(cnt)
});