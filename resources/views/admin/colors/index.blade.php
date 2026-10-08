@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">Standard colours</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">Colors</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.colors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add New Color
                    </a>
                </div>
            </div>
        </div>
        <div class="content-body"><div class="alert alert-info">Use short standard colour names, such as Silver, White or Black. Product colour selections and existing colour specifications feed one customer Colour filter. Keep material and finish details in Specifications.</div>
            <section id="product-list">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                                <h4 class="card-title mb-0">Colors List</h4>
                                @include('admin.partials.search-box', ['action' => route('admin.colors.index'), 'value' => $search, 'placeholder' => 'Search colors…'])
                            </div>
                            <div class="card-body p-0">
                                    <!-- Display Flash Messages -->
                                    @if (session('success'))
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            <div class="alert-body">
                                                {{ session('success') }}
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif
                                    
                                    @if (session('error'))
                                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                            <div class="alert-body">
                                                {{ session('error') }}
                                            </div>
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <div class="table-responsive">
                                        <table class="table table-hover" id="colorsTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Created At</th>
                                                    <th>Updated At</th>
                                                    <th class="text-center">Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($colors as $color)
                                                    <tr>
                                                        <td data-label="ID">{{ $color->id }}</td>
                                                        <td data-label="Name">{{ $color->name }}</td>
                                                        <td data-label="Created At">{{ $color->created_at->format('M d, Y H:i A') }}</td>
                                                        <td data-label="Updated At">{{ $color->updated_at->format('M d, Y H:i A') }}</td>
                                                        <td data-label="Actions" class="text-center">
                                                            <div class="d-flex justify-content-center gap-50">
                                                                <a href="{{ route('admin.colors.show', $color->id) }}"
                                                                   class="btn btn-sm btn-icon btn-relief-outline-info waves-effect"
                                                                   data-bs-toggle="tooltip" title="View">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                                <a href="{{ route('admin.colors.edit', $color->id) }}"
                                                                   class="btn btn-sm btn-icon btn-relief-outline-warning waves-effect"
                                                                   data-bs-toggle="tooltip" title="Edit">
                                                                    <i class="fas fa-pen"></i>
                                                                </a>
                                                                <form action="{{ route('admin.colors.destroy', $color->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this color? This action cannot be undone.');" class="d-inline-block">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm btn-icon btn-relief-outline-danger waves-effect" data-bs-toggle="tooltip" title="Delete">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center">No colors found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    @if ($colors->hasPages())
                                        <div class="card-footer d-flex justify-content-center">
                                            {{ $colors->links() }}
                                        </div>
                                    @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

<style>
  .gap-50 { gap: 0.5rem !important; }

  /* Responsive table — stack rows into cards on mobile (matches Products) */
  @media (max-width: 768px) {
    #colorsTable thead { display: none; }
    #colorsTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #colorsTable td {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.5rem 0.75rem;
      border: none;
      border-bottom: 1px solid #f3f2f7;
      font-size: 0.875rem;
    }
    #colorsTable td:last-child { border-bottom: none; }
    #colorsTable td.text-center { justify-content: flex-end; }
    #colorsTable td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #b9b9c3;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      flex-shrink: 0;
      padding-right: 0.5rem;
    }
  }
</style>
@endsection
