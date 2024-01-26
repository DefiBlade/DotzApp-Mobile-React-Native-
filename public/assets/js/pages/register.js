$('#editUser').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('user_info') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.

    // console.log(recipient)

    var modal = $(this)
    modal.find('.modal-body input#user_id').val(recipient.user_id)
    modal.find('.modal-body input#username').val(recipient.email)
    modal.find('.modal-body input#nice_name').val(recipient.nice_name)
    modal.find('.modal-body select#role').val(recipient.role)
})

$('#resetPass').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var recipient = button.data('user_id')
    
    var modal = $(this)
    modal.find('.modal-body input#user_id').val(recipient)
})