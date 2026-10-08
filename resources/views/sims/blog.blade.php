@extends('layouts.simslayout')

@section('title', 'Blog — AlbertinaNG')

@section('content')
<div class="main-wrap">

    {{-- Hero Header --}}
    <div style="
        background: linear-gradient(135deg, var(--g700) 0%, var(--g600) 100%);
        border-radius: 0;
        padding: 48px 52px;
        margin-bottom: 36px;
        position: relative;
        overflow: hidden;
        color: #fff;
        text-align: center;
    ">
        <div style="position:absolute;top:-60px;right:-60px;width:240px;height:240px;border-radius:50%;background:rgba(255,255,255,.05);pointer-events:none;"></div>
        <div style="position:absolute;bottom:-40px;left:-40px;width:160px;height:160px;border-radius:50%;background:rgba(90,171,31,.12);pointer-events:none;"></div>

        <div style="position:relative;z-index:1;">
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:20px;padding:6px 14px;margin-bottom:18px;">
                <i class="fas fa-blog" style="font-size:12px;color:var(--g200);"></i>
                <span style="font-size:12px;font-weight:600;letter-spacing:1.2px;text-transform:uppercase;color:var(--g200);">Albertina Blog</span>
            </div>
            <h1 style="font-family:var(--font-head);font-size:2.2rem;font-weight:800;margin-bottom:12px;line-height:1.2;">Our Latest Posts</h1>
            <p style="font-size:14.5px;color:rgba(255,255,255,.7);max-width:520px;margin:0 auto;line-height:1.7;">
                Tips, news, and guides on electronics, home appliances, solar energy, and living better in Nigeria.
            </p>
        </div>
    </div>

    {{-- Blog Grid --}}
    @if(isset($posts) && $posts->count())
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;margin-bottom:48px;">
            @foreach($posts as $post)
            <article style="
                background:var(--surface);
                border:1px solid var(--border);
                border-radius:var(--radius-lg);
                overflow:hidden;
                box-shadow:var(--shadow-sm);
                display:flex;
                flex-direction:column;
                transition:all .22s;
            " onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-md)';this.style.borderColor='var(--border2)'"
               onmouseout="this.style.transform='none';this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--border)'">

                {{-- Post Image --}}
                @if($post->image)
                <a href="{{ route('blog.show', $post->slug) }}">
                    <img src="{{ asset('storage/' . $post->image) }}"
                         alt="{{ $post->title }}"
                         style="width:100%;height:200px;object-fit:cover;display:block;">
                </a>
                @else
                <div style="width:100%;height:200px;background:var(--g50);display:flex;align-items:center;justify-content:center;">
                    <i class="fas fa-image" style="font-size:36px;color:var(--border2);"></i>
                </div>
                @endif

                {{-- Post Content --}}
                <div style="padding:20px 22px;display:flex;flex-direction:column;flex:1;gap:10px;">

                    {{-- Meta --}}
                    <div style="display:flex;align-items:center;gap:14px;font-size:12px;color:var(--ink3);">
                        <span style="display:flex;align-items:center;gap:5px;">
                            <i class="fas fa-calendar-alt" style="color:var(--g500);font-size:11px;"></i>
                            {{ \Carbon\Carbon::parse($post->created_at)->format('M d, Y') }}
                        </span>
                        @if($post->author)
                        <span style="display:flex;align-items:center;gap:5px;">
                            <i class="fas fa-user" style="color:var(--g500);font-size:11px;"></i>
                            {{ $post->author }}
                        </span>
                        @endif
                    </div>

                    {{-- Title --}}
                    <h2 style="font-family:var(--font-head);font-size:1.05rem;font-weight:700;color:var(--ink);line-height:1.35;">
                        <a href="{{ route('blog.show', $post->slug) }}"
                           style="color:inherit;transition:color .18s;"
                           onmouseover="this.style.color='var(--g600)'"
                           onmouseout="this.style.color='var(--ink)'">
                            {{ $post->title }}
                        </a>
                    </h2>

                    {{-- Excerpt --}}
                    <p style="font-size:13.5px;color:var(--ink3);line-height:1.65;flex:1;">
                        {{ Str::limit(strip_tags($post->content ?? $post->excerpt ?? ''), 130) }}
                    </p>

                    {{-- Read More --}}
                    <a href="{{ route('blog.show', $post->slug) }}" style="
                        display:inline-flex;align-items:center;gap:7px;
                        background:var(--g500);color:#fff;
                        padding:9px 18px;border-radius:var(--radius);
                        font-size:13px;font-weight:600;font-family:var(--font-head);
                        transition:background .2s;align-self:flex-start;margin-top:4px;
                    " onmouseover="this.style.background='var(--g600)'" onmouseout="this.style.background='var(--g500)'">
                        Read More <i class="fas fa-arrow-right" style="font-size:11px;"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($posts->hasPages())
        <div style="display:flex;justify-content:center;margin-bottom:48px;">
            {{ $posts->links() }}
        </div>
        @endif

    @else
        {{-- Empty state (fallback / static preview) --}}
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;margin-bottom:48px;">
            @foreach([
                [
                    'title'   => 'The Future of Online Shopping: Trends to Watch',
                    'date'    => 'August 1, 2025',
                    'author'  => 'Admin',
                    'excerpt' => 'Discover the exciting new trends shaping the e-commerce landscape, from AI-powered recommendations to immersive virtual shopping experiences.',
                    'color'   => 'var(--g50)',
                    'icon'    => 'fa-cart-shopping',
                ],
                [
                    'title'   => 'Top 5 Must-Have Gadgets for the Modern Home',
                    'date'    => 'July 25, 2025',
                    'author'  => 'Tech Team',
                    'excerpt' => 'Upgrade your living space with these innovative gadgets that combine convenience, style, and smart technology for everyday life.',
                    'color'   => 'var(--surf3)',
                    'icon'    => 'fa-tv',
                ],
                [
                    'title'   => 'Solar Energy in Nigeria: What You Need to Know',
                    'date'    => 'July 18, 2025',
                    'author'  => 'Albertina Team',
                    'excerpt' => 'With power challenges across Nigeria, solar solutions are becoming essential. Here\'s everything you need to know before investing.',
                    'color'   => 'var(--g50)',
                    'icon'    => 'fa-solar-panel',
                ],
                [
                    'title'   => 'How to Choose the Right Air Conditioner',
                    'date'    => 'July 10, 2025',
                    'author'  => 'Albertina Team',
                    'excerpt' => 'BTU ratings, inverter vs non-inverter, energy efficiency — our complete guide to picking the perfect AC for your space.',
                    'color'   => 'var(--surf3)',
                    'icon'    => 'fa-wind',
                ],
            ] as $post)
            <article style="
                background:var(--surface);
                border:1px solid var(--border);
                border-radius:var(--radius-lg);
                overflow:hidden;
                box-shadow:var(--shadow-sm);
                display:flex;flex-direction:column;
                transition:all .22s;
            " onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='var(--shadow-md)';this.style.borderColor='var(--border2)'"
               onmouseout="this.style.transform='none';this.style.boxShadow='var(--shadow-sm)';this.style.borderColor='var(--border)'">

                {{-- Placeholder image area --}}
                <div style="width:100%;height:190px;background:{{ $post['color'] }};display:flex;align-items:center;justify-content:center;border-bottom:1px solid var(--border);">
                    <i class="fas {{ $post['icon'] }}" style="font-size:42px;color:var(--g400);opacity:.6;"></i>
                </div>

                <div style="padding:20px 22px;display:flex;flex-direction:column;flex:1;gap:10px;">
                    <div style="display:flex;align-items:center;gap:14px;font-size:12px;color:var(--ink3);">
                        <span style="display:flex;align-items:center;gap:5px;">
                            <i class="fas fa-calendar-alt" style="color:var(--g500);font-size:11px;"></i>
                            {{ $post['date'] }}
                        </span>
                        <span style="display:flex;align-items:center;gap:5px;">
                            <i class="fas fa-user" style="color:var(--g500);font-size:11px;"></i>
                            {{ $post['author'] }}
                        </span>
                    </div>

                    <h2 style="font-family:var(--font-head);font-size:1.05rem;font-weight:700;color:var(--ink);line-height:1.35;">
                        {{ $post['title'] }}
                    </h2>

                    <p style="font-size:13.5px;color:var(--ink3);line-height:1.65;flex:1;">
                        {{ $post['excerpt'] }}
                    </p>

                    <a href="#" style="
                        display:inline-flex;align-items:center;gap:7px;
                        background:var(--g500);color:#fff;
                        padding:9px 18px;border-radius:var(--radius);
                        font-size:13px;font-weight:600;font-family:var(--font-head);
                        transition:background .2s;align-self:flex-start;margin-top:4px;
                    " onmouseover="this.style.background='var(--g600)'" onmouseout="this.style.background='var(--g500)'">
                        Read More <i class="fas fa-arrow-right" style="font-size:11px;"></i>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('styles')
<style>
    @media (max-width: 600px) {
        .main-wrap > div:first-child { padding: 30px 22px !important; }
        .main-wrap > div:first-child h1 { font-size: 1.7rem !important; }
    }
</style>
@endpush