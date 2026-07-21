@extends('admin.layouts.admin')

@section('title','Contact Messages')

@section('content')

<div class="container py-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Contact Messages</h2>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-success">
            Back to Dashboard
        </a>
    </div>

    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-success">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($messages as $msg)
                        <tr>
                            <td>{{ $msg->name }}</td>
                            <td>{{ $msg->email }}</td>
                            <td>{{ $msg->subject ?? 'N/A' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($msg->message, 80) }}</td>
                            <td>{{ $msg->created_at }}</td>
                            <td>
                                @if($msg->is_read)
                                    <span class="badge bg-success">Read</span>
                                @else
                                    <span class="badge bg-warning text-dark">Unread</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>

@endsection