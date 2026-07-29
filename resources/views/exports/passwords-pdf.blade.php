<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>mVault - Password Export</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 9px;
        }
        h1 {
            color: #ff7a00;
            border-bottom: 2px solid #ff7a00;
            padding-bottom: 10px;
        }
        h2 {
            color: #ff7a00;
            margin-top: 20px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #fff7ed;
            color: #c2410c;
            padding: 8px;
            text-align: left;
            border: 1px solid #fdba74;
            font-weight: bold;
        }
        td {
            padding: 6px;
            border: 1px solid #f0f0f0;
        }
        tr:nth-child(even) {
            background-color: #fafafa;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #999;
            font-size: 8px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <h1>mVault - Password Manager Export</h1>
    <p><strong>Exported:</strong> {{ $data['exported_at'] }}</p>

    <h2>Folders</h2>
    @if(count($data['folders']) > 0)
    <table>
        <thead>
            <tr>
                <th>Folder</th>
                <th>Description</th>
                <th>Passwords</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['folders'] as $folder)
            <tr>
                <td>{{ $folder['name'] }}</td>
                <td>{{ $folder['description'] ?? '-' }}</td>
                <td>{{ $folder['passwords_count'] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No folders found.</p>
    @endif

    <h2>Passwords</h2>
    @if(count($data['passwords']) > 0)
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Username</th>
                <th>Password</th>
                <th>URL</th>
                <th>Folder</th>
                <th>Created By</th>
                <th>Updated By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['passwords'] as $password)
            <tr>
                <td>{{ $password['title'] ?? '-' }}</td>
                <td>{{ $password['username'] ?? '-' }}</td>
                <td><code>{{ substr($password['password'] ?? '***', 0, 10) }}...</code></td>
                <td>{{ $password['url'] ?? '-' }}</td>
                <td>{{ $password['folder']['name'] ?? 'Uncategorized' }}</td>
                <td>{{ $password['creator']['name'] ?? 'Unknown' }}</td>
                <td>{{ $password['updater']['name'] ?? 'Unknown' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>No passwords found.</p>
    @endif

    <div class="footer">
        <p>This is a confidential export of your passwords from mVault. Keep it secure.</p>
        <p>mVault - Secure Password Manager</p>
    </div>
</body>
</html>
