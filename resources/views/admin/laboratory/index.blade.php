@extends('admin.layouts.app')

@section('title', 'Laboratory Management')

@push('styles')
<style>
    .icon-circle {
        width: 3rem;
        height: 3rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 500;
        border: none;
        padding: 0.75rem 1.5rem;
        border-bottom: 3px solid transparent;
    }
    .nav-tabs .nav-link.active {
        color: #4e73df;
        background: transparent;
        border-bottom: 3px solid #4e73df;
    }
    .nav-tabs .nav-link:hover:not(.active) {
        border-bottom: 3px solid #e3e6f0;
    }
    .test-type-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .test-type-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-dark"><i class="fas fa-flask me-2"></i>Laboratory Management</h2>
        </div>
    </div>

    <!-- Dashboard Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-primary font-weight-bold small">Pending Tests</h6>
                            <h2 class="mb-0" id="pendingTestsCount">0</h2>
                        </div>
                        <div class="icon-circle bg-primary text-white">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-success font-weight-bold small">Completed Tests</h6>
                            <h2 class="mb-0" id="completedTestsCount">0</h2>
                        </div>
                        <div class="icon-circle bg-success text-white">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase text-info font-weight-bold small">Test Types</h6>
                            <h2 class="mb-0" id="testTypesCount">0</h2>
                        </div>
                        <div class="icon-circle bg-info text-white">
                            <i class="fas fa-vial"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <ul class="nav nav-tabs border-0" id="labTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="test-requests-tab" data-bs-toggle="tab" 
                            data-bs-target="#test-requests" type="button" role="tab" 
                            aria-controls="test-requests" aria-selected="true">
                        <i class="fas fa-clipboard-list me-1"></i> Test Requests
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="test-types-tab" data-bs-toggle="tab" 
                            data-bs-target="#test-types" type="button" role="tab" 
                            aria-controls="test-types" aria-selected="false">
                        <i class="fas fa-vial me-1"></i> Test Types
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="test-results-tab" data-bs-toggle="tab" 
                            data-bs-target="#test-results" type="button" role="tab" 
                            aria-controls="test-results" aria-selected="false">
                        <i class="fas fa-file-medical-alt me-1"></i> Test Results
                    </button>
                </li>
            </ul>
            <div>
                <button class="btn btn-primary btn-sm" onclick="openModal('newTestModal')">
                    <i class="fas fa-plus me-1"></i> New Test
                </button>
                <button class="btn btn-outline-secondary btn-sm ms-2" onclick="openModal('newTestTypeModal')">
                    <i class="fas fa-plus me-1"></i> New Test Type
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <div class="tab-content" id="labTabsContent">
                <!-- Test Requests Tab -->
                <div class="tab-pane fade show active" id="test-requests" role="tabpanel" aria-labelledby="test-requests-tab">
                    <div class="table-responsive">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex">
                                <div class="input-group me-2" style="width: 250px;">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchTestRequests" placeholder="Search test requests...">
                                </div>
                                <select class="form-select" id="statusFilter" style="width: 180px;">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                            <div>
                                <input type="date" class="form-control" id="dateFilter">
                            </div>
                        </div>
                        
                        <table class="table table-hover" id="testRequestsTable">
                            <thead>
                                <tr>
                                    <th>Test ID</th>
                                    <th>Pet</th>
                                    <th>Test Type</th>
                                    <th>Requested By</th>
                                    <th>Date Requested</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated by JavaScript -->
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center" id="testRequestsPagination">
                                <!-- Pagination will be added by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
                
                <!-- Test Types Tab -->
                <div class="tab-pane fade" id="test-types" role="tabpanel" aria-labelledby="test-types-tab">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="input-group" style="max-width: 300px;">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" id="searchTestTypes" placeholder="Search test types...">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="testTypesGrid">
                        <!-- Will be populated by JavaScript -->
                        <div class="col-12 text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Test Results Tab -->
                <div class="tab-pane fade" id="test-results" role="tabpanel" aria-labelledby="test-results-tab">
                    <div class="table-responsive">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex">
                                <div class="input-group me-2" style="width: 250px;">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchTestResults" placeholder="Search test results...">
                                </div>
                                <select class="form-select" id="resultStatusFilter" style="width: 180px;">
                                    <option value="">All Status</option>
                                    <option value="normal">Normal</option>
                                    <option value="abnormal">Abnormal</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>
                            <div>
                                <div class="input-group">
                                    <span class="input-group-text">From</span>
                                    <input type="date" class="form-control" id="dateFromFilter">
                                    <span class="input-group-text">To</span>
                                    <input type="date" class="form-control" id="dateToFilter">
                                </div>
                            </div>
                        </div>
                        
                        <table class="table table-hover" id="testResultsTable">
                            <thead>
                                <tr>
                                    <th>Result ID</th>
                                    <th>Test Type</th>
                                    <th>Pet</th>
                                    <th>Result Status</th>
                                    <th>Tested By</th>
                                    <th>Test Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Will be populated by JavaScript -->
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center" id="testResultsPagination">
                                <!-- Pagination will be added by JavaScript -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
