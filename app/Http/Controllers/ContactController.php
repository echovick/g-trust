<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * The inbox complaints/contact messages are delivered to.
     */
    private const COMPLAINTS_INBOX = 'hello@g-trustbk.com';

    /**
     * Handle a contact form submission and email it to the complaints inbox.
     */
    public function submit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        Mail::to(self::COMPLAINTS_INBOX)->send(new ContactMessageMail(
            senderName: $validated['name'],
            senderEmail: $validated['email'],
            subjectLine: $validated['subject'],
            messageBody: $validated['message'],
        ));

        return back()->with('success', 'Thanks for reaching out! Your message has been sent and our team will get back to you shortly.');
    }
}
