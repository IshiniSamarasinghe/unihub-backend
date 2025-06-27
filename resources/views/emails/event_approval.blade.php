@component('mail::message')
# 📢 New Event Approval Request

Hello,

You have a new event pending your approval.

---

**Event Name:** {{ $event->name ?? 'N/A' }}  
**Society:** {{ $event->society ?? 'N/A' }}  
**Requested by:** {{ $event->position ?? 'N/A' }}  
**Description:** {{ $event->description ?? 'N/A' }}<br>
**University:** {{ $event->university ?? 'N/A' }}<br>
**Faculty:** {{ $event->faculty ?? 'N/A' }}<br>
@php
    try {
        $formattedDate = \Carbon\Carbon::parse($event->date)->format('F j, Y');
    } catch (Exception $e) {
        $formattedDate = 'Unknown Date';
    }

    try {
        $formattedTime = \Carbon\Carbon::parse($event->time)->format('g:i A');
    } catch (Exception $e) {
        $formattedTime = 'Unknown Time';
    }
@endphp
**Date & Time:** {{ $formattedDate }} at {{ $formattedTime }}<br>

---

Please take action by clicking one of the buttons below:

@component('mail::button', ['url' => url('/approve?token=' . $event->approval_token)])
✅ Approve Event
@endcomponent

@component('mail::button', ['url' => url('/reject?token=' . $event->approval_token)])
❌ Reject Event
@endcomponent

If you have any questions, please contact the event organizer.

Thanks,  
**UniHub Team**
@endcomponent
