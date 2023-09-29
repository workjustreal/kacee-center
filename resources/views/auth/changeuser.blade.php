@extends('layouts.master-layout')
@section('body')
<body class="boxed-layout">
@endsection
@section('content')
    <div class="page-title-box">
        <div class="page-title-left">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">จัดการ</a></li>
                <li class="breadcrumb-item active">ผู้ใช้งาน </li>
            </ol>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('change.user', [$id]) }}">
                @csrf

                @foreach ($errors->all() as $error)
                <p class="text-danger">{{ $error }}</p>
                @endforeach

                <label for="basic-url" class="form-label">Name</label>
                <div class="input-group mb-3">
                    <input type="text" id="name" name="name" class="form-control" value="{{ $user->name }}">
                </div>
                <label for="basic-url" class="form-label">Email</label>
                <div class="input-group mb-3">
                    <input type="text" id="email" name="email" class="form-control" value="{{ $user->email }}">
                </div>
                <label for="basic-url" class="form-label">Em ID</label>
                <div class="input-group mb-3">
                    <input type="text" id="email" name="email" class="form-control" value="{{ $user->emp_id }}">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="option" id="admin" value="1" @if($user->is_admin
                    == 1) checked @endif>
                    <label class="form-check-label" for="flexRadioDefault1">
                        Admin
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="option" id="store" value="2" @if($user->is_admin
                    == 2) checked @endif>
                    <label class="form-check-label" for="flexRadioDefault2">
                        Store
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="option" id="storeonline" value="3" @if($user->is_admin
                    == 3) checked @endif>
                    <label class="form-check-label" for="flexRadioDefault2">
                        Store Online
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="option" id="sale" value="4" @if($user->is_admin
                    == 4) checked @endif>
                    <label class="form-check-label" for="flexRadioDefault2">
                        Sale
                    </label>
                </div>
                <a href="/admin/change-password/{{$user->id}}"> คลิกเพื่อเปลี่ยนรหัสผ่าน</a><br>
                <button type="submit" class="btn btn-info mt-4">SAVE</button>
            </form>
        </div>
    </div>
@endsection