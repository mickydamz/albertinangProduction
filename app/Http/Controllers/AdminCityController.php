<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;

class AdminCityController extends Controller
{
    // Display the form to create a new city
    public function create()
    {
        $countries = Country::all(); // Get all countries
        return view('admin.cities.create', compact('countries'));
    }

    // Store the new city in the database
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id', // Ensure the country exists
        ]);

        // Create and save the city
        $city = new City();
        $city->name = $request->name;
        $city->country_id = $request->country_id; // Set the country ID
        $city->save();

        // Redirect back with success message
        return redirect()->route('admin.cities.index')->with('success', 'City added successfully!');
    }

    // Display the form to edit an existing city
    public function edit(City $city)
    {
        $countries = Country::all(); // Get all countries
        return view('admin.cities.edit', compact('countries', 'city'));
    }

    // Update the city in the database
    public function update(Request $request, City $city)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'country_id' => 'required|exists:countries,id', // Ensure the country exists
        ]);

        // Update the city
        $city->name = $request->name;
        $city->country_id = $request->country_id;
        $city->save();

        // Redirect back with success message
        return redirect()->route('admin.cities.index')->with('success', 'City updated successfully!');
    }

    // Display the list of cities
    public function index()
    {
        $cities = City::with('country')->latest()->paginate(10); // Get cities with country
        return view('admin.cities.index', compact('cities'));
    }

    // Delete a city
    public function destroy(City $city)
    {
        $city->delete();
        return redirect()->route('admin.cities.index')->with('success', 'City deleted successfully!');
    }
}
