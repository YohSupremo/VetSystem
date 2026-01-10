@extends('layout.base')
@section('content')
<body>
  
<div class="container d-flex align-items-center justify-content-center vh-100">
    <div class="card border rounded shadow-sm border-primary" style="width:400px">
        <div class="card-body">

        <form action="/login-success" method="POST">
            @csrf
            <h3 class="mb-3 text-center"> Login </h3>

            <div class="mb-3">
                  <label for="username"> Username: </label>

                 
                  <input type="text" class="form-control form-control-md @error('username') is-invalid @enderror" name="username">
                   @error('username')
                   <span class="text-danger"> {{ $message }}</span> 
                  @enderror
            </div>
          
            <div class="mb-3">
                  <label for="password"> Password: </label>
                  <input type="password" class="form-control form-control-md @error('password') is-invalid @enderror" name="password">
                   @error('password')
                   <span class="text-danger"> {{ $message }}</span> 
                  @enderror 
            </div>
          
            <div class="d-grid gap-2">
                <button class="btn btn-primary"> Login </button>
                <a href="/register" class="btn btn-secondary"> Register </a>
            </div>
        </form>
        </div>
    </div>
</div>












</body>
@endsection