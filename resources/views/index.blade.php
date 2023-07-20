<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
{{-- @foreach ($Test as $item)
                    <tr>
                        <th scope="row">{{ ++$i }}</th>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td> --}}
                            @foreach ($Test as $item)
                            {{ $item->id }} 
                            {{ $item->name }} 
                            {{ $item->email }}
                            <br> <br>
                            @endforeach
</body>
</html>