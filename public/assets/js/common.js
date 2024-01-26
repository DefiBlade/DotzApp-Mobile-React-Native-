if (pageData.msg)
{
    msg(pageData.msg)
}

function msg( msgText = null ) {
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: msgText ? msgText : 'Your work has been saved',
        showConfirmButton: false,
        timer: 3500
    });
}