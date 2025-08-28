<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #f4f4f4; }
        .btn { padding: 6px 12px; border: none; cursor: pointer; text-decoration: none; }
        .btn-approve { background: green; color: #fff; }
        .btn-reject { background: red; color: #fff; }
    </style>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <p>Welcome, {{ Auth::guard('admin')->user()->name }}</p>
    <a href="{{ route('admin.logout') }}">Logout</a>

    <h3>Agent List</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Approval</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($agents as $agent)
                <tr>
                    <td>{{ $agent->id }}</td>
                    <td>{{ $agent->name }}</td>
                    <td>{{ $agent->email }}</td>
                    <td>{{ $agent->is_approved ? '✅ Approved' : '⏳ Pending' }}</td>
                    <td>{{ $agent->status }}</td>
                    <td>
                        @if (!$agent->is_approved)
                            <a href="{{ route('admin.approve.agent', $agent->id) }}" class="btn btn-approve">Approve</a>
                        @else
                            @if ($agent->status === 'active')
                                <a href="{{ route('admin.deactivate.agent', $agent->id) }}" class="btn btn-reject">Deactivate</a>
                            @else
                                <a href="{{ route('admin.activate.agent', $agent->id) }}" class="btn btn-approve">Activate</a>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
