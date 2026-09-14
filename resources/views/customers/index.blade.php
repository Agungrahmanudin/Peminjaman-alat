@extends('layouts.master')

@section('title', 'Customers')

@section('content')
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="page-title">Customers</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Customers</li>
                    </ul>
                </div>

            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card card-table">
                    <div class="card-header">
                        <h4 class="card-title">List of Customers</h4>
                        <div class="card-action">
                            <form action="{{ route('customers.index') }}" method="GET" class="d-flex"
                                style="max-width: 300px;">
                                <input type="text" name="search" class="form-control form-control-sm me-2"
                                    placeholder="Search by name, email or phone..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Customer Information</th>
                                        <th>Total Alat</th>
                                        <th>Active Alat</th>
                                        <th>Completed</th>
                                        <th>Total Denda</th>
                                        <th>Status</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($customers as $customer)
                                        @php
                                            $stats = $customer->stats;
                                            $totalLoans = $stats['total_loans'];
                                            $activeLoans = $stats['active_loans'];
                                            $completedLoans = $stats['completed_loans'];
                                            $totalDenda = $stats['total_denda'];
                                            $totalDendaDibayar = $stats['total_denda_dibayar'];
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <img src="{{ $customer->avatar_url }}" alt="{{ $customer->name }}"
                                                            class="rounded-circle border" width="40" height="40"
                                                            style="object-fit: cover;">
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">
                                                            <a href="{{ route('customers.show', $customer->id) }}"
                                                                class="text-dark">
                                                                {{ $customer->name }}
                                                            </a>
                                                        </h6>
                                                        <div class="small text-muted">
                                                            <div><i class="fas fa-envelope fa-xs me-1"></i>
                                                                {{ $customer->email }}</div>
                                                            @if($customer->phone)
                                                                <div><i class="fas fa-phone fa-xs me-1"></i> {{ $customer->phone }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($totalLoans > 0)
                                                    <a href="{{ route('customers.show', $customer->id) }}#loans"
                                                        class="badge bg-info px-2 py-1 text-decoration-none">
                                                        {{ $totalLoans }} Alat
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary px-2 py-1">No Loans</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($activeLoans > 0)
                                                    <a href="{{ route('customers.show', $customer->id) }}#active-loans"
                                                        class="badge bg-warning px-2 py-1 text-decoration-none">
                                                        {{ $activeLoans }} Active
                                                    </a>
                                                @else
                                                    <span class="badge bg-success-light px-2 py-1">Tidak ada</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($completedLoans > 0)
                                                    <a href="{{ route('customers.show', $customer->id) }}#completed-loans"
                                                        class="badge bg-success px-2 py-1 text-decoration-none">
                                                        {{ $completedLoans }} Selesai
                                                    </a>
                                                @else
                                                    <span class="badge bg-secondary px-2 py-1">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($totalDenda > 0)
                                                    <span class="badge bg-danger px-2 py-1">
                                                        Rp {{ number_format($totalDenda, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-success-light px-2 py-1">Rp 0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="{{ $customer->status_badge_class }} px-2 py-1">
                                                    {{ $customer->status_label }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('customers.show', $customer->id) }}"
                                                        class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="tooltip"
                                                        title="View Details">
                                                        <i class="far fa-eye"></i>
                                                    </a>
                                                    @if($activeLoans == 0)
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            onclick="confirmDelete({{ $customer->id }})" data-bs-toggle="tooltip"
                                                            title="Delete">
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                                <form id="delete-form-{{ $customer->id }}"
                                                    action="{{ route('customers.destroy', $customer->id) }}" method="POST"
                                                    style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <h4>No customers found</h4>
                                                    <p class="text-muted">There are no customers registered yet.</p>

                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($customers->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                <nav aria-label="Page navigation">
                                    <ul class="pagination pagination-sm mb-0">
                                        @if($customers->onFirstPage())
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="fas fa-chevron-left"></i></span>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $customers->previousPageUrl() }}"
                                                    aria-label="Previous">
                                                    <i class="fas fa-chevron-left"></i>
                                                </a>
                                            </li>
                                        @endif

                                        @for($page = 1; $page <= $customers->lastPage(); $page++)
                                            @if($page == $customers->currentPage())
                                                <li class="page-item active">
                                                    <span class="page-link">{{ $page }}</span>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $customers->url($page) }}">{{ $page }}</a>
                                                </li>
                                            @endif
                                        @endfor

                                        @if($customers->hasMorePages())
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $customers->nextPageUrl() }}" aria-label="Next">
                                                    <i class="fas fa-chevron-right"></i>
                                                </a>
                                            </li>
                                        @else
                                            <li class="page-item disabled">
                                                <span class="page-link"><i class="fas fa-chevron-right"></i></span>
                                            </li>
                                        @endif
                                    </ul>
                                </nav>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function confirmDelete(customerId) {
            if (confirm('Are you sure you want to delete this customer?')) {
                event.preventDefault();
                document.getElementById('delete-form-' + customerId).submit();
            }
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <style>
        .empty-state {
            text-align: center;
            padding: 40px 20px;
        }

        .empty-state i {
            opacity: 0.5;
        }

        .badge.bg-success-light {
            background-color: rgba(40, 199, 111, 0.15);
            color: #28c76f;
            border: 1px solid rgba(40, 199, 111, 0.3);
        }

        .badge.bg-danger-light {
            background-color: rgba(234, 84, 85, 0.15);
            color: #ea5455;
            border: 1px solid rgba(234, 84, 85, 0.3);
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .table> :not(caption)>*>* {
            padding: 0.75rem 0.5rem;
            vertical-align: middle;
        }

        .border {
            border: 1px solid #dee2e6 !important;
        }
    </style>
@endpush
