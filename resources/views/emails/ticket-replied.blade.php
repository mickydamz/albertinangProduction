@extends('emails.layout')
@section('title', 'New Reply on Your Ticket — AlbertinaNG')
@section('header_title', 'New Reply on Your Ticket')

@section('body')
  <p class="greeting">
    Hello {{ $ticket->user->name ?? 'there' }},<br><br>
    There's a new reply on your support ticket <strong>{{ $ticket->subject }}</strong>.
</p>

    {{-- Reply body --}}
    <div style="
        background:#f7faf3;
        border:1px solid #dcefd0;
        border-left:4px solid #4e7a1a;
        border-radius:8px;
        padding:16px 20px;
        margin:20px 0;
        font-size:14px;
        color:#333;
        line-height:1.6;
    ">
        <div style="font-size:12px;color:#888;margin-bottom:8px;text-transform:uppercase;letter-spacing:.05em;">
            {{ $reply->user->name ?? 'Support' }} replied
            &middot; {{ $reply->created_at->format('M j, Y \a\t g:i A') }}
        </div>
        <div>{{ $reply->message }}</div>
    </div>

    {{-- Ticket meta --}}
    <table width="100%" cellpadding="0" cellspacing="0" style="margin:20px 0;border-collapse:collapse;">
        <!--<tr>-->
        <!--    <td style="padding:8px 0;border-bottom:1px dashed #dcefd0;font-size:13px;color:#555;width:40%;">Ticket ID</td>-->
        <!--    <td style="padding:8px 0;border-bottom:1px dashed #dcefd0;font-size:13px;color:#1a1a1a;font-weight:600;">#{{ $ticket->id }}</td>-->
        <!--</tr>-->
        <tr>
            <td style="padding:8px 0;border-bottom:1px dashed #dcefd0;font-size:13px;color:#555;">Subject</td>
            <td style="padding:8px 0;border-bottom:1px dashed #dcefd0;font-size:13px;color:#1a1a1a;font-weight:600;">{{ $ticket->subject }}</td>
        </tr>
        <tr>
            <td style="padding:8px 0;font-size:13px;color:#555;">Priority</td>
            <td style="padding:8px 0;font-size:13px;font-weight:600;color:#1a1a1a;">{{ ucfirst($ticket->priority) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="help-box">
        <p>
            You can reply directly by visiting your support tickets page.
            If you believe this is resolved, you can close the ticket from there.
        </p>
    </div>

    <p class="greeting" style="margin-bottom:0;">
        Regards,<br>
        <strong>AlbertinaNG Support Team</strong>
    </p>
@endsection