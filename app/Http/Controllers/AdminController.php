<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\AdminEmail;

class AdminController extends Controller
{
    public function showSendAdminEmailForm()
    {
        return view('admin.send_admin_email');  // This is the view that contains the form
    }

    /**
     * Send the admin email with provided details.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
 /**
 * Send the admin email with provided details.
 *
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\Response
 */
public function sendAdminEmail(Request $request)
{
    // Validate the request data
    $validated = $request->validate([
        'title' => 'required|string|max:255',  // Validate the title
        'email' => 'required|email',           // Validate the email
        'messager' => 'required|string',        // Validate the message
    ]);

   

    // Send the email to the provided recipient
    Mail::to($validated['email'])->send(new AdminEmail($validated));

    // Return a success response
    return back()->with('status', 'Email sent successfully!');
}



}

