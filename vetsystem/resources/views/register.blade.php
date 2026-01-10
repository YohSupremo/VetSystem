@extends('layout.base')
@section('content')
<body>
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="card border rounded shadow-sm border-primary" style="width:400px">
            <div class="card-body">
                <form action="/register/create" method="POST">
                    @csrf
                <h3 class="mb-3 text-center"> Register </h3>
                <div class="row mb-3">

                
                <div class="col-6 mb-3">
                        <label for="last_name" class="mb-3">  Last Name*  </label>
                        <input type="text" class="form-control form-control-md @error('last_name') is-invalid @enderror" name="last_name" value="{{@old('last_name')}}">
                        @error('last_name')
                        <span class="text-danger"> {{ $message }}</span> 
                        @enderror 
                </div>
                <div class="col-6 mb-3">
                        <label for="first_name" class="mb-3 ">  First Name*  </label>
                        <input type="text" class="form-control form-control-md @error('first_name') is-invalid @enderror" name="first_name"  value="{{@old('first_name')}}">
                         @error('first_name')
                        <span class="text-danger"> {{ $message }}</span> 
                        @enderror 
                </div>
                </div>
                 <div class="mb-3 row">
                    <div class="col-6">
                        <label for="age" class="mb-3">  Age:*  </label>
                        <input type="number" class="form-control form-control-md @error('age') is-invalid @enderror" name="age"  value="{{@old('age')}}">
                         @error('age')
                        <span class="text-danger"> {{ $message }}</span> 
                        @enderror 
                    </div>
                      <div class="col-6">
                       <label for="contact" class="mb-3">  Contact Number:*  </label>
                        <input type="tel" class="form-control form-control-md @error('contact_number') is-invalid @enderror" name="contact_number"  value="{{@old('contact_number')}}">
                         @error('contact_number')
                        <span class="text-danger"> {{ $message }}</span> 
                        @enderror 
                    </div>   
                </div>
                  <div class="mb-3">
                        <label for="username" class="mb-3">  Username*  </label>
                        <input type="text" class="form-control form-control-md @error('username') is-invalid @enderror" name="username"  value="{{@old('username')}}">
                         @error('username')
                        <span class="text-danger"> {{ $message }}</span> 
                        @enderror 
                </div>
                  <div class="mb-3">
                        <label for="password" class="mb-3">  Password*  </label>
                        <input type="password" class="form-control form-control-md @error('password') is-invalid @enderror" name="password"  value="{{@old('password')}}">
                         @error('password')
                        <span class="text-danger"> {{ $message }}</span> 
                        @enderror 
                </div>
                   <div class="d-grid gap-2">
                    <button class="btn btn-primary" name="submit"> Submit </button>
                    <a href="/login" class="btn btn-secondary"> Back </a>
              
              
              
                </form>
            </div>
              
        </div>
    </div>
</body>
@endsection