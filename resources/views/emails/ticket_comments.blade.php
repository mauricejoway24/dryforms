<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suppor Ticket</title>
</head>
<body>
    <p>
        {!! $comment->comment !!}
    </p>

    ---
    <p>Replied by: {{ $user->name }}</p>

    <p>Title: {{ $ticket->title }}</p>
    <p>Title: {{ $ticket->ticket_id }}</p>
    <p>Status: {{ $ticket->status }}</p>
</body>
</html>