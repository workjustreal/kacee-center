@extends('layouts.master-layout', ['page_title' => "สร้างผู้ใช้งาน"])
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
                <h4 class="page-title">สร้างผู้ใช้งาน</h4>
            </div>
        </div>
    </div>
    <!-- end page title -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-box">
                        <form class="form-horizontal" method="POST" action="{{ route('createuser') }}">
                            {{ csrf_field() }}
                            <div class="mb-3" {{ $errors->has('name') ? ' has-error' : '' }}>
                                <label for="name" class="form-label">Name</label>
                                <input id="name" type="text" class="form-control" name="name" dvalue="{{ old('name') }}" required autofocus>
                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="mb-3" {{ $errors->has('surname') ? ' has-error' : '' }}>
                                <label for="surname" class="form-label">Surname</label>
                                <input id="surname" type="text" class="form-control" name="surname" dvalue="{{ old('surname') }}" autofocus>
                                @if ($errors->has('surname'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('surname') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="mb-3" {{ $errors->has('emp_id') ? ' has-error' : '' }}>
                                <label for="emp_id" class="form-label">Emp ID</label>
                                <input id="emp_id" type="number" class="form-control" name="emp_id" dvalue="{{ old('emp_id') }}" required autofocus>
                                @if ($errors->has('emp_id'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('emp_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="mb-3" {{ $errors->has('email') ? ' has-error' : '' }}>
                                <label for="email" class="form-label">E-Mail Address</label>
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="mb-3" {{ $errors->has('password') ? ' has-error' : '' }}>
                                <label for="password" class="form-label">Password</label>
                                <input id="password" type="password" class="form-control" name="password" autocomplete="new-password" required>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong class="text-danger">{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="mb-3" {{ $errors->has('password_confirmation') ? ' has-error' : '' }}>
                                <label for="password-confirm" class="form-label">Confirm Password</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ระดับ</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="is_admin" id="is_admin" value="1">
                                    <label class="form-check-label" for="is_admin">SUPER ADMIN</label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">บทบาท</label>
                                @foreach ($role as $list)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="{{ $list->id }}" id="role_{{ $loop->index + 1 }}" name="is_role" @if ($loop->index == 0) required @endif>
                                    <label class="form-check-label" for="role_{{ $loop->index + 1 }}">{{ $list->role }}</label>
                                </div>
                                @endforeach
                            </div>
                            <div class="mb-3">
                                <label class="form-label">สิทธิล็อกอิน</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_login" id="login_yes" value="1" checked>
                                    <label class="form-check-label" for="login_yes"> เปิด </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="is_login" id="login_no" value="0">
                                    <label class="form-check-label" for="login_no"> ปิด </label>
                                </div>
                            </div>
                            <div class="form-group mb-0 text-center">
                                <button class="btn btn-info btn-block" type="submit"> CREATE ACCOUNT </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection