<!DOCTYPE html>
<html>
<head>
    <title>IT Change Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2 style="color: #1a73e8; border-bottom: 2px solid #1a73e8; padding-bottom: 10px;">New IT Change Request</h2>
    
    <p><strong>Requester Name:</strong> {{ $changeRequest->user->name ?? 'Unknown' }}</p>
    <p><strong>Requester Email:</strong> {{ $changeRequest->user->email ?? 'Unknown' }}</p>
    
    <p><strong>Associated Entity:</strong> 
        @if ($changeRequest->user && $changeRequest->user->serviceBody)
            Service Body: {{ $changeRequest->user->serviceBody->en_name }} / {{ $changeRequest->user->serviceBody->ar_name }}
        @elseif ($changeRequest->user)
            @php
                $committee = \App\Models\ServiceCommittee::where('user_id', $changeRequest->user->id)->first();
            @endphp
            @if ($committee)
                Committee: {{ $committee->en_name }} / {{ $committee->ar_name }}
            @else
                None
            @endif
        @else
            None
        @endif
    </p>

    <p><strong>Request Type:</strong> {{ ucfirst(str_replace('_', ' ', $changeRequest->request_type)) }}</p>
    <p><strong>Subject:</strong> {{ $changeRequest->subject }}</p>
    
    <h3 style="color: #1a73e8;">Description:</h3>
    <div style="background-color: #f9f9f9; padding: 15px; border-radius: 5px; border: 1px solid #ddd; white-space: pre-wrap;">{{ $changeRequest->description }}</div>
    
    <br>
    <p>You can manage this request and update its status in the admin dashboard.</p>
</body>
</html>
