<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        return view('pages.contact');
    }

    public function submit(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'email' => 'required|email|max:120',
            'subject' => 'nullable|max:150',
            'message' => 'required'
        ]);

        ContactMessage::create([
            'name' => $request->name,
            'email' => $request->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'created_at' => now(),
            'is_read' => 0
        ]);

        return back()->with('success', 'Thank you! Your message has been sent.');
    }
}