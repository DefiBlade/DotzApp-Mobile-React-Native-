$('#editActivity').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('activity_info') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.

    // console.log(recipient)

    var modal = $(this)
    modal.find('.modal-body input#activity_id').val(recipient.activity_id)
    modal.find('.modal-body input#activity_name').val(recipient.activity_name)
    modal.find('.modal-body input#activity_description').val(recipient.activity_description)
    modal.find('.modal-body img#current_activity_img').attr("src", `/uploads/activities/${recipient.activity_img}`)

    modal.find('.modal-body input#activity_img')[0].addEventListener('input', displayInputedFile)
})

$('#resetPass').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget)
    var recipient = button.data('user_id')
    
    var modal = $(this)
    modal.find('.modal-body input#user_id').val(recipient)
})

function displayInputedFile(e) {
    // modal.find('.modal-body img#current_activity_img').attr("src", `/uploads/activities/${e.target.files[0]}`)
    var output = document.querySelector('.modal-body img#current_activity_img');
    output.src = URL.createObjectURL(e.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
    }
}