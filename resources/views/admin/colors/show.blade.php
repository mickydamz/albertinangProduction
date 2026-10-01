@extends('layouts.adminlayout')

@section('content')
<div class="container mx-auto p-6 bg-white rounded-lg shadow-md max-w-md">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">color Details</h1>

    <div class="mb-4">
        <p class="text-gray-700 text-lg mb-2"><strong class="font-semibold">ID:</strong> {{ $color->id }}</p>
        <p class="text-gray-700 text-lg mb-2"><strong class="font-semibold">Name:</strong> {{ $color->name }}</p>
        <p class="text-gray-700 text-sm mb-2"><strong class="font-semibold">Created At:</strong> {{ $color->created_at->format('M d, Y H:i A') }}</p>
        <p class="text-gray-700 text-sm mb-4"><strong class="font-semibold">Updated At:</strong> {{ $color->updated_at->format('M d, Y H:i A') }}</p>
    </div>

    <div class="flex justify-between items-center">
        <a href="{{ route('admin.colors.edit', $color->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-300 shadow-md">
            <i class="fas fa-edit mr-2"></i> Edit color
        </a>
        <a href="{{ route('admin.colors.index') }}" class="inline-block align-baseline font-bold text-sm text-gray-600 hover:text-gray-800">
            Back to colors List
        </a>
    </div>
</div>
@endsection
