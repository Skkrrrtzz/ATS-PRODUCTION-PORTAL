$(document).ready(function () {
  $(".send").click(function (e) {
    e.preventDefault();
    var id = $(this).val();
    var dropdown = document.getElementById("send_to");
    var value = dropdown.options[dropdown.selectedIndex].value;
    //var updated_by = document.getElementById("name");
    //alert(id);

    Swal.fire({
      title: "Are you sure you want to proceed ?",
      text: "You have selected: " + value,
      icon: "question",
      showCancelButton: true,
      confirmButtonText: "Yes, proceed!",
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          method: "POST",
          url: "cable_terminal_crimping_WS1_command.php",
          data: { wo_id: id, send_to: value, send: true },
          success: function (response) {
            //console.log(response);
            if (response == 200) {
              Swal.fire("Saved!", "Activity Submitted!", "success");
              window.location.reload();
            } else if (response == 500) {
              Swal.fire("Changes are not saved", "", "info");
            }
          },
        });
      }
    });
  });
});

// alert.js
/*Swal.fire({
  icon: "warning",
  title: "Invalid",
  text: "Cannot be sent!",
  showConfirmButton: false,
  timer: 2000,
});*/
