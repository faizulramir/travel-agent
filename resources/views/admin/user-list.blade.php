@extends('layouts.master')

@section('title') USER LIST @endsection

@section('css')
    <!-- Responsive Table css -->
    <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('content')

    @component('components.breadcrumb')
        @slot('li_1') ADMIN @endslot
        @slot('title') USER LIST @endslot
    @endcomponent

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12" style="text-align: right;">
                            <a href="{{ route('user_add') }}" class="btn btn-primary w-md" >Add User</a>
                        </div>
                    </div>
                    <br>
                    <div>
                        <table id="datatable" class="table table-bordered dt-responsive  nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th data-priority="1">Name</th>
                                    <th data-priority="3">Email</th>
                                    <th data-priority="1">Register Date</th>
                                    <th data-priority="3">Role</th>
                                    <th data-priority="3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $i => $user)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->created_at }}</td>
                                        <td>
                                            @if ($user->hasAnyRole('akc'))
                                                AKC Admin
                                            @else
                                                <input type="hidden" name="user_id{{$user->id}}" id="user_id{{$user->id}}" value="{{ $user->id }}">
                                                <select id="role{{$user->id}}" name="role{{$user->id}}" class="form-control select2-search-disable" onclick="clicked(event, {{$user->id}})" required>
                                                    <option value="" {{ isset($user->getRoleNames()[0]) ? 'selected' : '' }}>No Role (Not Activated)</option>
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}" {{ isset($user->getRoleNames()[0]) ? $user->getRoleNames()[0] == $role->name ? 'selected' : '' : ''}}>
                                                            @if ($role->name == 'ind')
                                                                Individu
                                                            @elseif ($role->name == 'ag')
                                                                Agent
                                                            @elseif ($role->name == 'tra')
                                                                Travel Agent
                                                            @elseif ($role->name == 'fin')
                                                                Finance
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('user_delete', $user->id) }}" onclick="return confirm('Do you really want to delete?');" class="waves-effect" style="color: red;">
                                                <i class="bx bx-trash-alt font-size-20" title="Delete"></i>
                                            </a>
                                            <a href="{{ route('user_edit', $user->id) }}" class="waves-effect" style="color: purple;">
                                                <i class="bx bx-book-open font-size-20" title="Edit"></i>
                                            </a>
                                            
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script>

        function clicked(e, id)
        {
            // if(!confirm('Are you sure to submit?')) {
            //     e.preventDefault();
            // } else {
                $("#role" + id).change(function () {
                    var end = this.value;
                    var userId = id;
                    var firstDropVal = $('#role' + id).val();

                    $.ajax({
                        type:'get',
                        url:'/post_role' + '/' + firstDropVal + '/' + userId,
                        data:'_token = <?php echo csrf_token() ?>',
                        success:function(data) {
                            alert(data.Data)
                        }
                    });
                });
            // }
        }
        
    </script>
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
@endsection
