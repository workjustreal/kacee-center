@extends('layouts.master-layout', ['page_title' => "แก้ไขผู้ใช้งาน"])
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
                        <li class="breadcrumb-item active">ผู้ใช้งาน</li>
                    </ol>
                </div>
                <h4 class="page-title">แก้ไขผู้ใช้งาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form method="POST" action="{{ route('edit.user', [$id]) }}">
                            @csrf
                            @foreach ($errors->all() as $error)
                            <p class="text-danger">{{ $error }}</p>
                            @endforeach

                            <div class="mb-3">
                                <label for="name" class="form-label">ชื่อ</label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}">
                            </div>
                            <div class="mb-3">
                                <label for="surname" class="form-label">นามสกุล</label>
                                <input type="text" id="surname" name="surname" class="form-control" value="{{ $user->surname }}">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">อีเมล</label>
                                <input type="text" id="email" name="email" class="form-control"value="{{ $user->email }}">
                            </div>
                            <div class="mb-3">
                                <label for="empid" class="form-label">รหัสพนักงาน</label>
                                <input type="text" id="emp_id" name="emp_id" class="form-control" value="{{ $user->emp_id }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ระดับ</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" value="1"
                                    @if($user->is_admin == 1) checked @endif>
                                    <label class="form-check-label" for="is_admin">SUPER ADMIN</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">บทบาท</label>
                                @foreach ($role as $list)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="{{ $list->id }}" id="role_{{ $loop->index + 1 }}" name="is_role"
                                    @if ($loop->index == 0) required @endif @if($user->is_role == $list->id) checked @endif>
                                    <label class="form-check-label" for="role_{{ $loop->index + 1 }}">{{ $list->role }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="mb-3">
                                <label class="form-label">สิทธิล็อกอิน</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_login" id="login_yes" value="1"
                                    @if($user->is_login == 1) checked @endif>
                                    <label class="form-check-label" for="login_yes"> เปิด </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_login" id="login_no" value="0"
                                    @if($user->is_login == 0) checked @endif>
                                    <label class="form-check-label" for="login_no"> ปิด </label>
                                </div>
                            </div>
                            {{-- <a href="/admin/change-password/{{$user->id}}"> คลิกเพื่อเปลี่ยนรหัสผ่าน</a><br> --}}
                            <a href="#" class="mt-3" onclick="resetConfirmationUserPassword({{$user->id}})"> คลิกเพื่อรีเซ็ตรหัสผ่าน</a><br>
                            <button type="submit" class="btn btn-info mt-4">SAVE</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection