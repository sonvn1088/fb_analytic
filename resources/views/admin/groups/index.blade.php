@extends('adminlte::page')

@section('title', 'Manage FBAC')

@section('content_header')
    <a class="btn btn-info btn-flat pull-right" href="{{route('admin.groups.create')}}">Add Group</a>
    <h1>Groups</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="groups-table" class="table display table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Accounts</th>
                                <th>Pages</th>
                                <th>Edit</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')

@stop
@section('js')
    <script type="text/javascript">

        $(document).ready(function() {
            $('#groups-table').DataTable({
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.groups.list') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name', orderable: false},
                    {
                        data: 'accounts', name: 'accounts', orderable: false,
                        render: function ( data, type, row, meta ) {
                            return $.map( data, function ( item ) {
                                return '<a href="{{route('admin.accounts.show')}}/'+item.id+'" target="_blank">'
                                        +item.first_name + ' ' + item.last_name + '<a/> ('
                                        + '<a title="Open profile" href="javascript:$.ajax(\''+ '{{route('admin.accounts.profile')}}/'+item.profile+'\')">'
                                        + item.role.label+'<a/>)'
                                        ;
                            } ).join( '<br>' );
                        }
                    },
                    {
                        data: 'pages', name: 'pages', orderable: false,
                        render: function ( data, type, row, meta ) {
                            return $.map( data, function ( item ) {
                                return '<a href="{{route('admin.my_pages.show')}}/'+item.id+'" target="_blank">'+item.name + '</a> ('
                                        + item.status.label + ')';

                                //return '<a href="https://www.facebook.com/'+item.fb_id+'" target="_blank">'+item.name+'</a>';
                            } ).join( '<br>' );
                        }
                    },
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="{{ route('admin.groups') }}/'+row.id+ '" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                        }
                    },
                ]
            });

        });
    </script>
@stop