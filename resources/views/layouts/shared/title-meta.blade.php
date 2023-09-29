<meta charset="utf-8" />
<title>{{$title ?? ''}} | Kacee Application</title>
<link rel="manifest" href="{{ url('assets/manifest.json') }}" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="Kacee Application" name="description" />
<meta content="Kacee Application" name="author" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- App favicon -->
<link rel="icon" type="image/x-icon" sizes="16x16" href="{{ url('assets/kacee.ico') }}">
<link rel="icon" type="image/x-icon" sizes="64x64" href="{{ url('assets/kacee-64.ico') }}">