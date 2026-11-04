<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IdVerification;

class IdVerificationController extends Controller
{
    // Display the list of ID verifications
    public function index()
    {
        $idverifications = IdVerification::paginate(10);
        return view('idvs.index', ['idverifications' => $idverifications]);
    }

    // Display the edit form for a specific ID verification
    public function edit($id)
    {
        $verification = IdVerification::find($id);
        return view('idvs.edit', ['verification' => $verification]);
    }

   public function update(Request $request, $id)
{
    $data = IdVerification::find($id);

    // Validate the request data if needed
    // $request->validate([
    //     'status' => 'required|in:0,1',
    // ]);

    // Update the status
    $data->status = $request->input('status');
    $data->save();

    // Redirect back with a success message or to any other page
    return redirect()->route('idvs.index')->with('message', 'Status updated successfully');
}

public function destroy($id)
{
    $data = IdVerification::find($id);

    if ($data) {
        $data->delete();
        return redirect()->route('idvs.index')->with('message', 'Record deleted successfully');
    }

    return redirect()->route('idvs.index')->with('error', 'Record not found');
}

}
