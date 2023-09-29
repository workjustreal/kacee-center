@extends('layouts.master-layout', ['page_title' => "แก้ไขระบบงาน"])
@section('css')
<!-- third party css -->
<link href="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.css')}}" rel="stylesheet" type="text/css" />
<!-- third party css end -->
@endsection
@section('content')
<div class="container-fluid">
    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">KACEE</a></li>
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Admin</a></li>
                        <li class="breadcrumb-item active">บทบาท</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขระบบงาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" action="{{ route('role.update') }}" method="POST" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="mb-3">
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                            <input id="role_id" name="role_id" type="hidden" class="form-control" value="{{ $role->id }}" />
                            <div class="mb-3">
                                <label for="role" class="form-label">ชื่อบทบาท</label>
                                <input id="role" name="role" type="text" class="form-control" value="{{ $role->role }}"
                                    placeholder="ชื่อบทบาท" autocomplete="off" required />
                            </div>
                            @if ($permission->count() > 0)
                            <div class="mb-3">
                                <label for="icon" class="form-label">สิทธิ์การใช้งาน</label><br>
                                <div class="form-check mb-2 form-check-primary">
                                    <input class="form-check-input" type="checkbox" id="checkall" name="checkall"
                                    @if (count($permission) == count($role_permission))
                                        checked
                                    @endif>
                                    <label class="form-check-label" for="checkall">เลือกทั้งหมด</label>
                                </div>
                                @foreach ($permission as $list)
                                <div class="form-check mb-2 form-check-primary">
                                    <input class="form-check-input" type="checkbox" value="{{ $list->id }}" id="permission_{{ $loop->index + 1 }}" name="permission[]"
                                    @if ($role_permission)
                                        @foreach ($role_permission as $rp)
                                            @if ($list->id == $rp->permission_id)
                                                checked
                                            @endif
                                        @endforeach
                                    @endif>
                                    <label class="form-check-label fw-normal" for="permission_{{ $loop->index + 1 }}">{{ $list->permission }}</label>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            <div class="mb-3 d-flex justify-content-between">
                                <a href="{{ url('admin/roles') }}" class="btn btn-secondary mt-3"> ย้อนกลับ</a>
                                <button type="submit" class="btn btn-primary mt-3"> บันทึก</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- third party js -->
<script src="{{asset('assets/js/ajax/jquery.min.js')}}"></script>
<script src="{{asset('assets/libs/bootstrap-table/bootstrap-table.min.js')}}"></script>
<script src="{{asset('assets/js/pages/bootstrap-tables.init.js')}}"></script>
<!-- third party js ends -->
<script type="text/javascript">
    $(document).ready(function(){
        $("input[name='checkall']").click(function(){
            $("input[name='permission[]']").prop('checked', $(this).prop('checked'));
        });
        $("input[name='permission[]']").click(function(){
            if ($("input[name='permission[]']:checked").length === $("input[name='permission[]']").length) {
                $("input[name='permission[]']").prop('checked', true);
                $("input[name='checkall']").prop('checked', true);
            } else {
                $("input[name='checkall']").prop('checked', false);
            }
        });
    });
</script>
@endsection