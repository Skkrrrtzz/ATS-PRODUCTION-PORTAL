<?php include 'ATS_Prod_Header.php' ?>
<?php include 'PROD_navbar.php' ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ADMINISTRATOR</title>
  <style>
    .btnAdd {
      text-align: right;
      width: 83%;
      margin-bottom: 10px;

    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <h2 class="text-center pt-2">AUTHORIZED USERS</h2>
    <div class="row">
      <div class="btnAdd">
        <a href="#!" data-id="" data-bs-toggle="modal" data-bs-target="#addUserModal" class="btn btn-success btn-sm">Add User</a>
      </div>
    </div>
    <div class="row">
      <div class="container">
        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <table id="example" class="table table-striped">
              <thead class="table-dark text-center">
                <th>ID</th>
                <th>Name</th>
                <th>Emp_ID</th>
                <th>Password</th>
                <th>Department</th>
                <th>Role</th>
                <th>Options</th>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
          <div class="col-md-2"></div>
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    $(document).ready(function() {
      $('#example').DataTable({
        "fnCreatedRow": function(nRow, aData, iDataIndex) {
          $(nRow).attr('id', aData[0]);
        },
        'serverSide': 'true',
        'processing': 'true',
        'paging': 'true',
        'order': [],
        'ajax': {
          'url': 'fetch_data.php',
          'type': 'post',
        },
        "aoColumnDefs": [{
            "bSortable": false,
            "aTargets": [6]
          },

        ]
      });
    });
    $(document).on('submit', '#addUser', function(e) {
      e.preventDefault();
      var role = $('#addRoleField').val();
      var emp_name = $('#addUserField').val();
      var password = $('#addpassField').val();
      var department = $('#addDeptField').val();
      var username = $('#addEmpField').val();
      if (role != '' && emp_name != '' && department != '' && username != '' && password != '') {
        $.ajax({
          url: "add_user.php",
          type: "post",
          data: {
            role: role,
            emp_name: emp_name,
            department: department,
            password: password,
            username: username
          },
          success: function(data) {
            var json = JSON.parse(data);
            var status = json.status;
            if (status == 'true') {
              mytable = $('#example').DataTable();
              mytable.draw();
              $('#addUserModal').modal('hide');
            } else {
              alert('failed');
            }
          }
        });
      } else {
        alert('Fill all the required fields');
      }
    });
    $(document).on('submit', '#updateUser', function(e) {
      e.preventDefault();
      //var tr = $(this).closest('tr');
      var role = $('#roleField').val();
      var emp_name = $('#nameField').val();
      var department = $('#deptField').val();
      var password = $('#passField').val();
      var username = $('#empField').val();
      var trid = $('#trid').val();
      var id = $('#id').val();
      if (role != '' && emp_name != '' && department != '' && username != '' && password != '') {
        $.ajax({
          url: "update_users.php",
          type: "post",
          data: {
            role: role,
            emp_name: emp_name,
            department: department,
            password: password,
            username: username,
            id: id
          },
          success: function(data) {
            var json = JSON.parse(data);
            var status = json.status;
            if (status == 'true') {
              table = $('#example').DataTable();
              // table.cell(parseInt(trid) - 1,0).data(id);
              // table.cell(parseInt(trid) - 1,1).data(username);
              // table.cell(parseInt(trid) - 1,2).data(email);
              // table.cell(parseInt(trid) - 1,3).data(mobile);
              // table.cell(parseInt(trid) - 1,4).data(role);
              var button = '<td><a href="javascript:void();" data-id="' + id + '" class="btn btn-info btn-sm editbtn">Edit</a>  <a href="#!"  data-id="' + id + '"  class="btn btn-danger btn-sm deleteBtn">Delete</a></td>';
              var row = table.row("[id='" + trid + "']");
              row.row("[id='" + trid + "']").data([id, emp_name, username, password, department, role, button]);
              $('#exampleModal').modal('hide');
            } else {
              alert('failed');
            }
          }
        });
      } else {
        alert('Fill all the required fields');
      }
    });
    $('#example').on('click', '.editbtn ', function(event) {
      var table = $('#example').DataTable();
      var trid = $(this).closest('tr').attr('id');
      // console.log(selectedRow);
      var id = $(this).data('id');
      $('#exampleModal').modal('show');

      $.ajax({
        url: "get_single_data.php",
        data: {
          id: id
        },
        type: 'post',
        success: function(data) {
          var json = JSON.parse(data);
          $('#nameField').val(json.emp_name);
          $('#empField').val(json.username);
          $('#passField').val(json.password);
          $('#deptField').val(json.department);
          $('#roleField').val(json.role);
          $('#id').val(id);
          $('#trid').val(trid);
        }
      })
    });

    $(document).on('click', '.deleteBtn', function(event) {
      var table = $('#example').DataTable();
      event.preventDefault();
      var id = $(this).data('id');
      if (confirm("Are you sure want to delete this User ? ")) {
        $.ajax({
          url: "delete_user.php",
          data: {
            id: id
          },
          type: "post",
          success: function(data) {
            var json = JSON.parse(data);
            status = json.status;
            if (status == 'success') {
              //table.fnDeleteRow( table.$('#' + id)[0] );
              //$("#example tbody").find(id).remove();
              //table.row($(this).closest("tr")) .remove();
              Swal.fire({
                title: "Deleted!",
                text: "Your user has been deleted.",
                icon: "success"
              });
              $("#" + id).closest('tr').remove();
            } else {
              Swal.fire({
                title: 'Error!',
                text: 'Something went wrong.',
                icon: 'error'
              });
              return;
            }
          }
        });
      } else {
        return null;
      }
    })
  </script>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Update User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="updateUser">
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="trid" id="trid" value="">
            <div class="mb-3 row">
              <label for="nameField" class="col-md-3 form-label">Name</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="nameField" name="name">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="empField" class="col-md-3 form-label">Emp_ID</label>
              <div class="col-md-9">
                <input type="number" class="form-control" id="empField" name="email">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="passField" class="col-md-3 form-label">Password</label>
              <div class="col-md-9">
                <input type="password" class="form-control" id="passField" name="password">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="deptField" class="col-md-3 form-label">Department</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="deptField" name="mobile">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="roleField" class="col-md-3 form-label">Role</label>
              <div class="col-md-9">
                <select class="form-select" id="roleField" name="role">
                  <option value="operator">Operator</option>
                  <option value="technician">Technician</option>
                  <option value="inspector">Inspector</option>
                  <option value="planner">Planner</option>
                  <option value="cable_supervisor">Supervisor</option>
                </select>
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Add user Modal -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addUser" action="">
            <div class="mb-3 row">
              <label for="addUserField" class="col-md-3 form-label">Name</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addUserField" name="name">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addEmpField" class="col-md-3 form-label">Emp_ID</label>
              <div class="col-md-9">
                <input type="number" class="form-control" id="addEmpField" name="email">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addpassField" class="col-md-3 form-label">Password</label>
              <div class="col-md-9">
                <input type="password" class="form-control" id="addpassField" name="password">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addDeptField" class="col-md-3 form-label">Department</label>
              <div class="col-md-9">
                <input type="text" class="form-control" id="addDeptField" name="mobile">
              </div>
            </div>
            <div class="mb-3 row">
              <label for="addRoleField" class="col-md-3 form-label">Role</label>
              <div class="col-md-9">
                <select class="form-select" id="addRoleField" name="role">
                  <option value="operator">Operator</option>
                  <option value="technician">Technician</option>
                  <option value="inspector">Inspector</option>
                  <option value="planner">Planner</option>
                  <option value="cable_supervisor">Supervisor</option>
                </select>
              </div>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary">Submit</button>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</body>

</html>