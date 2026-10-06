<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $previousScheduleAt ? 'Round Rescheduled' : 'Round Scheduled' }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background-color:#ffffff; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td style="background-color:#0c2e2d; padding:20px 28px;">
                            <span style="color:#ffffff; font-size:16px; font-weight:bold;">Appdid Technologies</span>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:28px;">
                            <p style="margin:0 0 16px; font-size:15px; color:#1e293b;">Hi {{ $candidateName }},</p>

                            <p style="margin:0 0 20px; font-size:15px; color:#1e293b; line-height:1.5;">
                                @if ($previousScheduleAt)
                                    Your <strong>{{ $roundType }}</strong> has been <strong>rescheduled</strong>. Please note the new date and time below:
                                @else
                                    Your <strong>{{ $roundType }}</strong> has been scheduled. Here are the details:
                                @endif
                            </p>

                            @if ($previousScheduleAt)
                                <p style="margin:0 0 20px; font-size:13px; color:#b45309; background-color:#fffbeb; border-radius:8px; padding:12px;">
                                    Previously scheduled for
                                    <span style="text-decoration:line-through;">{{ $previousScheduleAt->format('l, F j, Y') }} at {{ $previousScheduleAt->format('h:i A') }}</span>
                                </p>
                            @endif

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px; border-collapse:collapse;">
                                <tr>
                                    <td style="padding:8px 0; font-size:13px; color:#64748b; width:120px;">{{ $previousScheduleAt ? 'New Date' : 'Date' }}</td>
                                    <td style="padding:8px 0; font-size:14px; color:#0f1b2a; font-weight:bold;">{{ $scheduleAt->format('l, F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; font-size:13px; color:#64748b;">{{ $previousScheduleAt ? 'New Time' : 'Time' }}</td>
                                    <td style="padding:8px 0; font-size:14px; color:#0f1b2a; font-weight:bold;">{{ $scheduleAt->format('h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:8px 0; font-size:13px; color:#64748b;">Mode</td>
                                    <td style="padding:8px 0; font-size:14px; color:#0f1b2a; font-weight:bold;">{{ $mode }}</td>
                                </tr>
                                @if ($interviewerName)
                                    <tr>
                                        <td style="padding:8px 0; font-size:13px; color:#64748b;">Interviewer</td>
                                        <td style="padding:8px 0; font-size:14px; color:#0f1b2a; font-weight:bold;">{{ $interviewerName }}</td>
                                    </tr>
                                @endif
                            </table>

                            @if ($mode === 'Virtual')
                                @if ($meetingLink)
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
                                        <tr>
                                            <td align="center" style="background-color:#0f7a78; border-radius:8px;">
                                                <a href="{{ $meetingLink }}" style="display:inline-block; padding:12px 28px; font-size:14px; font-weight:bold; color:#ffffff; text-decoration:none;">
                                                    Join Meeting
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style="margin:0 0 20px; font-size:12px; color:#94a3b8; word-break:break-all;">
                                        Or copy this link: {{ $meetingLink }}
                                    </p>
                                @else
                                    <p style="margin:0 0 20px; font-size:13px; color:#b45309; background-color:#fffbeb; border-radius:8px; padding:12px;">
                                        The meeting link is still being generated — we'll follow up with it shortly.
                                    </p>
                                @endif
                            @endif

                            @if ($notes)
                                <p style="margin:0 0 20px; font-size:13px; color:#475569; background-color:#f8fafc; border-radius:8px; padding:12px;">
                                    <strong>Notes:</strong> {{ $notes }}
                                </p>
                            @endif

                            <p style="margin:24px 0 0; font-size:13px; color:#64748b;">
                                If you have any questions, just reply to this email.
                            </p>

                            <p style="margin:16px 0 0; font-size:14px; color:#1e293b;">
                                Thanks,<br>
                                Appdid Technologies HR Team
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
