@extends('admin.dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Medical Records</h3>
                    <a href="{{ route('admin.medical-records.create') }}" class="btn btn-primary">Add New Record</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pet</th>
                                <th>Veterinarian</th>
                                <th>Visit Date</th>
                                <th>Diagnosis</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medicalRecords as $record)
                                <tr>
                                    <td>{{ $record->id }}</td>
                                    <td>{{ $record->pet->name ?? 'N/A' }}</td>
                                    <td>{{ $record->veterinarian->name ?? 'N/A' }}</td>
                                    <td>{{ $record->visit_date->format('M d, Y') }}</td>
                                    <td>{{ Str::limit($record->diagnosis, 50) }}</td>
                                    <td>
                                        <a href="{{ route('admin.medical-records.show', $record->id) }}" class="btn btn-sm btn-info">View</a>
                                        <a href="{{ route('admin.medical-records.edit', $record->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No medical records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    @if($medicalRecords->hasPages())
                        <div class="mt-4">
                            {{ $medicalRecords->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
