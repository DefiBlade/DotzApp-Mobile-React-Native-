$('#editClub').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget) // Button that triggered the modal
    var recipient = button.data('club_info') // Extract info from data-* attributes
    // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
    // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.

    // console.log(recipient)

    var modal = $(this)
    modal.find('.modal-body input#club_id').val(recipient.club_id)
    modal.find('.modal-body input#club_name').val(recipient.club_name)
    modal.find('.modal-body input#club_description').val(recipient.club_description)
    modal.find('.modal-body img#current_club_img').attr("src", `/uploads/clubs/${recipient.club_img}`)

    modal.find('.modal-body input#club_img')[0].addEventListener('input', displayInputedFile)
})

function displayInputedFile(e) {
    // modal.find('.modal-body img#current_club_img').attr("src", `/uploads/clubs/${e.target.files[0]}`)
    var output = document.querySelector('.modal-body img#current_club_img');
    output.src = URL.createObjectURL(e.target.files[0]);
    output.onload = function() {
      URL.revokeObjectURL(output.src) // free memory
    }
}