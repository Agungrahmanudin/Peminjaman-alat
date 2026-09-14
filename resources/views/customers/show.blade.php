@extends('layouts.master')

@section('title','Customer Detail')

@section('content')

    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Customer Detail</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('customers.index') }}">Customers</a>
                    </li>
                    <li class="breadcrumb-item active">Detail</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="{{ $customer->avatar_url }}"
                         class="rounded-circle mb-3"
                         width="120"
                         height="120"
                         style="object-fit:cover">

                    <h4>{{ $customer->name }}</h4>
                    <p class="text-muted">{{ $customer->email }}</p>

                    <hr>

                    <p><strong>Phone:</strong> {{ $customer->phone ?? '-' }}</p>
                    <p><strong>Location:</strong> {{ $customer->location ?? '-' }}</p>
                    <p><strong>Company:</strong> {{ $customer->company ?? '-' }}</p>

                    <span class="{{ $customer->status_badge_class }}">
                        {{ $customer->status_label }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="col-md-8">
            <div class="row">

                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3>{{ $stats['total_peminjaman'] }}</h3>
                            <p>Total Peminjaman</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3>{{ $stats['active_loans'] }}</h3>
                            <p>Active Loans</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h3>{{ $stats['completed_loans'] }}</h3>
                            <p>Completed</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Loans -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5>Riwayat Peminjaman</h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal Pinjam</th>
                                <th>Status</th>
                                <th>Jumlah Item</th>
                                <th>Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($customer->peminjaman as $p)
                            <tr>
                                <td>{{ $p->tanggal_pinjam }}</td>
                                <td>{{ $p->status }}</td>
                                <td>{{ $p->jumlah }}</td>
                                <td>
                                    Rp {{ number_format(optional($p->pengembalian)->denda ?? 0,0,',','.') }}
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
