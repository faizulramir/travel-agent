<!-- JAVASCRIPT -->
<script src="{{ URL::asset('assets/libs/jquery/jquery.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/bootstrap/bootstrap.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/metismenu/metismenu.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{ URL::asset('assets/libs/node-waves/node-waves.min.js')}}"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $( document ).ready(function() {
        setInterval(getNotification, 18000);
    });

    function getNotification () {
        $.ajax({
            url: "/notification",
            type:"GET",
            success:function(data){
                $("#notificationModalbody").empty();
                console.log($("#rolesId").val());
                var roles = $("#rolesId").val();
                if (data.Data.length > 0 && (roles == 'akc' || roles == 'fin')) {
                    $("#notificationModal").modal("show");
                    data.Data.forEach(e => {
                        var status = ''
                        if (e.status == '0') {
                            status = 'Pending Submission'
                        } else if (e.status == '2') {
                            status = 'Pending AKC (Approval)'
                        } else if (e.status == '2.1' || e.status == '2.2' || e.status == '2.3') {
                            status = 'Pending AKC (Invoice)'
                        } else if (e.status == '3') {
                            status = 'Pending Payment'
                        } else if (e.status == '4') {
                            status = 'Pending AKC (Payment) Endorsement'
                        } else if (e.status == '5') {
                            status = 'COMPLETED'
                        } else if (e.status == '99') {
                            status = 'EXCEL REJECTED'
                        }
                        $("#notificationModalbody").append('<p> '+ status + ' - ' + e.file_name + '</p>');
                    });
                    
                } else {
                    $("#notificationModal").modal("hide");
                }
                
            }
        });
    }

    $('#change-password').on('submit',function(event){
        event.preventDefault();
        var Id = $('#data_id').val();
        var current_password = $('#current-password').val();
        var password = $('#password').val();
        var password_confirm = $('#password-confirm').val();
        $('#current_passwordError').text('');
        $('#passwordError').text('');
        $('#password_confirmError').text('');
        $.ajax({
            url: "{{ url('update-password') }}" + "/" + Id,
            type:"POST",
            data:{
                "current_password": current_password,
                "password": password,
                "password_confirmation": password_confirm,
                "_token": "{{ csrf_token() }}",
            },
            success:function(response){
                $('#current_passwordError').text('');
                $('#passwordError').text('');
                $('#password_confirmError').text('');
                if(response.isSuccess == false){ 
                    $('#current_passwordError').text(response.Message);
                }else if(response.isSuccess == true){
                    setTimeout(function () {   
                        window.location.href = "{{ route('root') }}"; 
                    }, 1000);
                }
            },
            error: function(response) {
                $('#current_passwordError').text(response.responseJSON.errors.current_password);
                $('#passwordError').text(response.responseJSON.errors.password);
                $('#password_confirmError').text(response.responseJSON.errors.password_confirmation);
            }
        });
    });
</script>

@yield('script')

<!-- App js -->
<script src="{{ URL::asset('assets/js/app.min.js')}}"></script>

@yield('script-bottom')