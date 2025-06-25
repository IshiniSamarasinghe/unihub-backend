@component('mail::message')
# 📢 New Event Approval Request

Hello,

You have a new event pending your approval.

---

**Event Name:** {{ $event->name }}  
**Society:** {{ $event->society }}  
**Requested by:** {{ $event->position }}  
**Date & Time:** {{ $event->date }} at {{ $event->time }}  
**Description:**  
{{ $event->description ?? 'No description provided.' }}

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
