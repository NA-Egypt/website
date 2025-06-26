<!DOCTYPE html>
<html lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="Content-Language" content="ar" />
    <style>
        body, table, th, td {
            font-family: 'amiri', sans-serif;
            direction: rtl;
            unicode-bidi: embed;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: right;
        }
    </style>
</head>
<body>
    <h2 style="text-align:center">جدول الاجتماعات</h2>
    <table>
        <thead>
            <tr>
                <th>{{__('messages.Group Name')}}</th>
                <th>{{__('messages.Day')}}</th>
                <th colspan="2">{{__('messages.Time')}}</th>
                <th>{{__('messages.GSR Name')}}</th>
                <th>{{__('messages.Phone')}}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($meetings as $meeting)
            <tr>
                <td>{{ $meeting->group->ar_name }}</td>
                <td>{{ $meeting->day->ar_name }}</td>
                <td>{{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }}</td>
                <td>{{ \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') }}</td>
                <td>{{ $meeting->group->ar_gsr_name }}</td>
                <td>{{ $meeting->group->phone }}</td>
                <tr>
                    <th colspan="1">{{__('messages.Address')}}</th>
                    <td colspan="5">{{ $meeting->group->ar_address }}</td>
                </tr>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>