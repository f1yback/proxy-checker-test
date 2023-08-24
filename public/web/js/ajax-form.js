$('#form-save').on('beforeSubmit', function (e) {
    const form = $(this);
    e.preventDefault();
    $.ajax({
        data: form.serialize(),
        url: "/site/save",
        dataType: "JSON",
        type: "POST"
    }).done(function (response) {
        console.log(Object.keys(response).length);
        if (Object.keys(response).length) {
            let message = '';
            for (let k in response) {
                message += response[k];
            }
            alertify.alert(message);
        } else {
            location.href = "/site/proxy-check";
        }
    });
});