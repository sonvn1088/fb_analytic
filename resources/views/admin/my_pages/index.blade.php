@extends('adminlte::page')

@section('title', 'Manage Pages')

@section('content_header')
    <a class="btn btn-info btn-flat pull-right" href="{{route('admin.my_pages.create')}}">Add Page</a>
    <h1>Pages</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <table id="pages-table" class="table display table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Group</th>
                                <th>Name</th>
                                <th>Follow</th>
                                <th>Accounts</th>
                                <th>In Scheduled</th>
                                <th>In Published</th>
                                <th>Blocked At</th>
                                <th>Status</th>
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
<style>
    #pages-table tr td, #pages-table tr th{
        vertical-align: middle;
    }
</style>
@stop
@section('js')
    <script src="//cdn.rawgit.com/ashl1/datatables-rowsgroup/v1.0.0/dataTables.rowsGroup.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#pages-table').DataTable({
                order: [[ 0, 'asc']],
                processing: true,
                pageLength: 35,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('admin.my_pages.list') }}",
                columns: [
                    {
                        data: 'group_id', name: 'group_id',  searchable : false, className: "text-center"
                    },
                    {
                        data: 'name', name: 'name', orderable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="https://www.facebook.com/'+row.fb_id+'" target="_bank" title="View on Facebook">'
                                    + data + ' (' + (row.username == 'null'?'':row.username) + ')'+'</a>';
                        }
                    },
                    {
                        data: 'follow', name: 'follow', className: "text-center",
                        render: $.fn.dataTable.render.number(',')
                    },


                    {
                        data: 'group_id', name: 'accounts', className: "text-center", orderable: false, searchable: false,
                        render: function ( data, type, row, meta ) {
                            return $.map( row.accounts, function ( item ) {
                                return '<a href="{{route('admin.accounts.show')}}/'+item.id+'" target="_blank">'
                                        +item.first_name + ' ' + item.last_name + '<a/> ('
                                        + '<a title="Open profile" href="javascript:$.ajax(\''+ '{{route('admin.accounts.profile')}}/'+item.profile+'\')">'
                                        + item.role.label+'<a/>)'
                                        ;
                            } ).join( '<br>' );
                        }

                    },
                    {
                        data: 'scheduled_posts', name: 'scheduled_posts', orderable: false, searchable: false,
                    },
                    {
                        data: 'published_posts', name: 'published_posts', orderable: false, searchable: false,
                    },
                    {
                        data: 'blocked_at', name: 'blocked_at', className: "text-center",
                    },
                    {
                        data: 'status', name: 'status', className: "text-center",
                        render: function ( data, type, row, meta ) {
                            return '<span class="text-'+(data.value?'success':'danger')+'">'+data.label+'</span>'
                        }
                    },
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false,
                        render: function ( data, type, row, meta ) {
                            return '<a href="{{ route('admin.my_pages') }}/'+row.id+ '" class="btn btn-xs btn-primary">' +
                                    '<i class="glyphicon glyphicon-edit"></i> Edit</a>';
                        }
                    },
                ],
                rowsGroup: [
                    'group_id:name',
                    'accounts:name'
                ],
            });

        });
    </script>
@stop