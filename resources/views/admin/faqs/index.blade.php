@extends('layouts.adminlayout')

@section('content')
<div class="app-content content">
    <div class="content-wrapper container-xxl p-0">

        <div class="content-header row">
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="content-header-title mb-0">FAQs</h2>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item active">FAQs</li>
                        </ol>
                    </div>
                    <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-50"></i> Add FAQ
                    </a>
                </div>
            </div>
        </div>

        <div class="content-body">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-header border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-question-circle me-50 text-primary"></i>
                        All FAQs <span class="badge bg-secondary ms-1">{{ $faqs->total() }}</span>
                    </h4>
                    @include('admin.partials.search-box', ['action' => route('admin.faqs.index'), 'value' => $search, 'placeholder' => 'Search questions, answers…'])
                </div>
                <div class="card-body p-0">
                    @if($faqs->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-question-circle fa-2x mb-2 d-block"></i>
                            No FAQs yet. <a href="{{ route('admin.faqs.create') }}">Add the first one.</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="faqsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:40px;">#</th>
                                        <th>Question</th>
                                        <th style="width:130px;">Category</th>
                                        <th style="width:80px;">Order</th>
                                        <th style="width:90px;">Status</th>
                                        <th style="width:110px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($faqs as $faq)
                                    <tr>
                                        <td data-label="#" class="text-muted">{{ $faq->id }}</td>
                                        <td data-label="Question">
                                            <div class="fw-semibold" style="font-size:13px;">{{ Str::limit($faq->question, 90) }}</div>
                                            <div class="text-muted" style="font-size:12px;">{{ Str::limit(strip_tags($faq->answer), 80) }}</div>
                                        </td>
                                        <td data-label="Category">
                                            <span class="badge bg-light text-dark border" style="font-size:11px;">{{ ucfirst($faq->category) }}</span>
                                        </td>
                                        <td data-label="Order" class="text-muted" style="font-size:13px;">{{ $faq->sort_order }}</td>
                                        <td data-label="Status">
                                            @if($faq->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Hidden</span>
                                            @endif
                                        </td>
                                        <td data-label="Actions">
                                            <a href="{{ route('admin.faqs.edit', $faq) }}"
                                               class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                                <i class="fas fa-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Delete this FAQ?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                @if ($faqs->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $faqs->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<style>
  /* Responsive table — stack rows into cards on mobile (matches Products) */
  @media (max-width: 768px) {
    #faqsTable thead { display: none; }
    #faqsTable tr { display: block; margin-bottom: 1rem; border: 1px solid #ebe9f1; border-radius: 6px; }
    #faqsTable td {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 0.75rem;
      padding: 0.5rem 0.75rem;
      border: none;
      border-bottom: 1px solid #f3f2f7;
      font-size: 0.875rem;
      width: auto !important;
    }
    #faqsTable td:last-child { border-bottom: none; }
    #faqsTable td::before {
      content: attr(data-label);
      font-weight: 600;
      color: #b9b9c3;
      font-size: 0.75rem;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      flex-shrink: 0;
      padding-right: 0.5rem;
    }
    #faqsTable td > :not(.badge) { text-align: right; }
  }
</style>
@endsection
