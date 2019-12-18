@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">User List</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif


                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th width="280px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>


                    <div class="modal fade" id="ajaxModel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modelHeading"></h4>
                                </div>
                                <div class="modal-body">
                                    <ul class="error_val" style="color: red;"></ul>
                                    <form id="userForm" name="userForm" class="form-horizontal">
                                       <input type="hidden" name="user_id" id="user_id">
                                        <div class="form-group">
                                            <label for="name" class="col-sm-2 control-label">Name</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                                            </div>
                                        </div>
                         
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Email</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" value="" maxlength="50" required="">
                                            </div>
                                        </div>
                          
                                        <div class="col-sm-offset-2 col-sm-10">
                                         <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                                         </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="ajaxModelView" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="modelHeading"></h4>
                                </div>
                                <div class="modal-body">
                                    
                                    <table class="table table-bordered viewTable">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td id="viewid"></td>
                                                <td id="viewname"></td>
                                                <td id="viewemail"></td>
                                            </tr>
                                        </tbody>
                                    </table>


                                    <!-- <form id="userForm" name="userForm" class="form-horizontal">
                                       <input type="hidden" name="user_id" id="user_id">
                                        <div class="form-group">
                                            <label for="name" class="col-sm-2 control-label">Name</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name" value="" maxlength="50" required="">
                                            </div>
                                        </div>
                         
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Email</label>
                                            <div class="col-sm-12">
                                                <input type="text" class="form-control" id="email" name="email" placeholder="Enter Email" value="" maxlength="50" required="">
                                            </div>
                                        </div>
                          
                                        <div class="col-sm-offset-2 col-sm-10">
                                         <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                                         </button>
                                        </div>
                                    </form> -->
                                </div>
                            </div>
                        </div>
                    </div>







                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
  $(function () {
     
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });
    
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('users.index') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
    
    $('body').on('click', '.editUsers', function () {
      var user_id = $(this).data('id');
      $('.error_val').html('');
      $.get("{{ route('users.index') }}" +'/' + user_id +'/edit', function (data) {
          $('#modelHeading').html("Edit Users");
          $('#saveBtn').val("edit-user");
          $('#ajaxModel').modal('show');
          $('#user_id').val(data.id);
          $('#name').val(data.name);
          $('#email').val(data.email);
      })
   });

    $('body').on('click', '.viewUsers', function () {
      var user_id = $(this).data('id');
      $('.error_val').html('');
      $.get("{{ route('users.index') }}" +'/' + user_id +'/edit', function (data) {
          $('#ajaxModelView #modelHeading').html("View Users");
          $('#ajaxModelView').modal('show');
          $('#viewid').append(data.id);
          $('#viewname').append(data.name);
          $('#viewemail').append(data.email);
      })
   });
    
    $('#saveBtn').click(function (e) {
        e.preventDefault();
    
        $.ajax({
          data: $('#userForm').serialize(),
          url: "{{ route('users.store') }}",
          type: "POST",
          dataType: 'json',
          success: function (data) {

                if(data.status == '200'){
                    $('#userForm').trigger("reset");
                    $('#ajaxModel').modal('hide');
                    table.draw();
                }else{
                    var errors = data.errors;
                    var html = '';
                    $.each(errors, function(key,val) {
                        html += '<li>'+val+'</li>';
                    });

                    $('.error_val').html(html);
                }         
          },
          error: function (data) {
              console.log('Error:', data);
              $('#saveBtn').html('Save Changes');
          }
      });
    });
    
     
  });
</script>
@endsection
