{{--
    Block Renderer — include on your product show page
    Usage:
        @include('partials.block-renderer', ['blocks' => $product->description_blocks])
--}}

@if(!empty($blocks) && is_array($blocks))
    <div class="be-renderer">
        @foreach($blocks as $block)
            @php $type = $block['type'] ?? ''; @endphp

            @if($type === 'paragraph' && !empty($block['text']))
                <p class="be-r-paragraph">{{ $block['text'] }}</p>

            @elseif($type === 'heading' && !empty($block['text']))
                @php $level = in_array($block['level'] ?? 2, [1,2,3,4]) ? $block['level'] : 2; @endphp
                <h{{ $level }} class="be-r-heading be-r-h{{ $level }}">{{ $block['text'] }}</h{{ $level }}>

            @elseif($type === 'image' && !empty($block['url']))
                <figure class="be-r-figure">
                    <img src="{{ $block['url'] }}"
                         alt="{{ $block['alt'] ?? '' }}"
                         class="be-r-image"
                         loading="lazy">
                    @if(!empty($block['caption']))
                        <figcaption class="be-r-caption">{{ $block['caption'] }}</figcaption>
                    @endif
                </figure>

            @elseif($type === 'video' && !empty($block['url']))
                @php
                    $videoUrl = $block['url'];
                    $videoSrc = null;

                    // YouTube: youtube.com/watch?v=ID  |  youtu.be/ID  |  youtube.com/embed/ID
                    if (preg_match('/(?:youtu\.be\/|[?&]v=|embed\/)([A-Za-z0-9_-]{11})/', $videoUrl, $ytMatch)) {
                        $videoSrc = 'https://www.youtube.com/embed/' . $ytMatch[1] . '?rel=0&modestbranding=1';
                    }
                    // Vimeo: vimeo.com/ID
                    elseif (preg_match('/vimeo\.com\/(\d+)/', $videoUrl, $viMatch)) {
                        $videoSrc = 'https://player.vimeo.com/video/' . $viMatch[1];
                    }
                @endphp
                @if($videoSrc)
                    <div class="be-r-video-wrap">
                        <div class="be-r-video-ratio">
                            <iframe src="{{ $videoSrc }}"
                                    allowfullscreen
                                    loading="lazy"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture">
                            </iframe>
                        </div>
                        @if(!empty($block['caption']))
                            <p class="be-r-caption">{{ $block['caption'] }}</p>
                        @endif
                    </div>
                @endif

            @elseif($type === 'button' && !empty($block['label']))
                @php $btnStyle = in_array($block['style'] ?? '', ['primary','secondary','outline']) ? $block['style'] : 'primary'; @endphp
                <div class="be-r-btn-wrap">
                    <a href="{{ $block['url'] ?? '#' }}"
                       class="be-r-btn be-r-btn--{{ $btnStyle }}"
                       target="_blank" rel="noopener noreferrer">
                        {{ $block['label'] }}
                    </a>
                </div>

            @elseif($type === 'divider')
                <hr class="be-r-divider">

            @elseif($type === 'callout' && !empty($block['text']))
                @php $variant = in_array($block['variant'] ?? '', ['info','warning','success','danger']) ? $block['variant'] : 'info'; @endphp
                <div class="be-r-callout be-r-callout--{{ $variant }}">
                    @if(!empty($block['icon']))
                        <span class="be-r-callout-icon">{{ $block['icon'] }}</span>
                    @endif
                    <span class="be-r-callout-text">{{ $block['text'] }}</span>
                </div>

            @elseif($type === 'columns' && !empty($block['cols']))
                @php $colCount = count($block['cols']); @endphp
                <div class="be-r-columns" style="--col-count: {{ $colCount }}">
                    @foreach($block['cols'] as $col)
                        <div class="be-r-col">
                            {{--
                                Support both formats:
                                - New: col.blocks = array of block objects (rich columns)
                                - Old: col.text = plain string (legacy)
                            --}}
                            @if(!empty($col['blocks']) && is_array($col['blocks']))
                                @foreach($col['blocks'] as $cb)
                                    @php $cbType = $cb['type'] ?? ''; @endphp

                                    @if($cbType === 'text' && isset($cb['text']))
                                        <p style="font-size:14px;color:#333;margin:0 0 8px;line-height:1.6;">{{ $cb['text'] }}</p>

                                    @elseif($cbType === 'heading' && !empty($cb['text']))
                                        @php $cbLevel = in_array($cb['level'] ?? 2, [1,2,3,4]) ? $cb['level'] : 2; @endphp
                                        <h{{ $cbLevel }} style="font-weight:700;font-size:{{ $cbLevel <= 2 ? '1.1rem' : '1rem' }};color:#111;margin:0 0 6px;line-height:1.3;">{{ $cb['text'] }}</h{{ $cbLevel }}>

                                    @elseif($cbType === 'image' && !empty($cb['url']))
                                        <figure style="margin:0 0 8px;">
                                            <img src="{{ $cb['url'] }}"
                                                 alt="{{ $cb['alt'] ?? '' }}"
                                                 style="width:100%;border-radius:8px;display:block;object-fit:cover;"
                                                 loading="lazy">
                                            @if(!empty($cb['caption']))
                                                <figcaption style="font-size:12px;color:#888;text-align:center;margin-top:4px;">{{ $cb['caption'] }}</figcaption>
                                            @endif
                                        </figure>

                                    @elseif($cbType === 'button' && !empty($cb['label']))
                                        @php $cbStyle = in_array($cb['style'] ?? '', ['primary','secondary','outline']) ? $cb['style'] : 'primary'; @endphp
                                        <div style="margin-bottom:8px;">
                                            <a href="{{ $cb['url'] ?? '#' }}"
                                               class="be-r-btn be-r-btn--{{ $cbStyle }}"
                                               style="display:inline-block;padding:8px 18px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;"
                                               target="_blank" rel="noopener noreferrer">
                                                {{ $cb['label'] }}
                                            </a>
                                        </div>

                                    @elseif($cbType === 'list' && !empty($cb['items']))
                                        @php $cbOrdered = ($cb['style'] ?? 'unordered') === 'ordered'; @endphp
                                        @if($cbOrdered)
                                            <ol style="margin:0 0 8px 1.2rem;font-size:14px;color:#333;line-height:1.65;">
                                                @foreach($cb['items'] as $item)
                                                    @if($item !== '') <li>{{ $item }}</li> @endif
                                                @endforeach
                                            </ol>
                                        @else
                                            <ul style="margin:0 0 8px 1.2rem;font-size:14px;color:#333;line-height:1.65;">
                                                @foreach($cb['items'] as $item)
                                                    @if($item !== '') <li>{{ $item }}</li> @endif
                                                @endforeach
                                            </ul>
                                        @endif

                                    @elseif($cbType === 'callout' && !empty($cb['text']))
                                        @php
                                            $cbVariant = in_array($cb['variant'] ?? '', ['info','warning','success','danger']) ? $cb['variant'] : 'info';
                                            $cbBg      = ['info'=>'#e8f4fd','warning'=>'#fff8e1','success'=>'#e8f5e9','danger'=>'#fdecea'][$cbVariant];
                                            $cbBorder  = ['info'=>'#2196f3','warning'=>'#ffc107','success'=>'#4caf50','danger'=>'#f44336'][$cbVariant];
                                        @endphp
                                        <div style="display:flex;gap:8px;padding:10px 12px;border-radius:8px;margin-bottom:8px;font-size:13px;background:{{ $cbBg }};border-left:4px solid {{ $cbBorder }};">
                                            @if(!empty($cb['icon']))<span>{{ $cb['icon'] }}</span>@endif
                                            <span>{{ $cb['text'] }}</span>
                                        </div>

                                    @endif
                                @endforeach

                            @elseif(!empty($col['text']))
                                {{-- Legacy plain-text column --}}
                                {{ $col['text'] }}
                            @endif
                        </div>
                    @endforeach
                </div>

            @elseif($type === 'list' && !empty($block['items']))
                @php $ordered = ($block['style'] ?? 'unordered') === 'ordered'; @endphp
                @if($ordered)
                    <ol class="be-r-list be-r-list--ordered">
                        @foreach($block['items'] as $item)
                            @if($item !== '') <li>{{ $item }}</li> @endif
                        @endforeach
                    </ol>
                @else
                    <ul class="be-r-list be-r-list--unordered">
                        @foreach($block['items'] as $item)
                            @if($item !== '') <li>{{ $item }}</li> @endif
                        @endforeach
                    </ul>
                @endif

            @elseif($type === 'quote' && !empty($block['text']))
                <blockquote class="be-r-quote">
                    <p>{{ $block['text'] }}</p>
                    @if(!empty($block['author']))
                        <cite class="be-r-cite">— {{ $block['author'] }}</cite>
                    @endif
                </blockquote>

            @elseif($type === 'spacer')
                @php $h = min(200, max(8, intval($block['height'] ?? 32))); @endphp
                <div class="be-r-spacer" style="height: {{ $h }}px;"></div>

            @endif
        @endforeach
    </div>

    <style>
        .be-renderer { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #1a1a1a; line-height: 1.75; max-width: 100%; }
        .be-r-paragraph { font-size: 15px; color: #333; margin: 0 0 1rem; }
        .be-r-heading { font-weight: 700; line-height: 1.3; margin: 1.5rem 0 0.5rem; color: #111; }
        .be-r-h1 { font-size: 2rem; }
        .be-r-h2 { font-size: 1.5rem; }
        .be-r-h3 { font-size: 1.2rem; }
        .be-r-h4 { font-size: 1rem; }
        .be-r-figure { margin: 1.25rem 0; }
        .be-r-image { width: 100%; max-height: 480px; object-fit: cover; border-radius: 10px; display: block; }
        .be-r-caption { font-size: 13px; color: #888; text-align: center; margin-top: 6px; font-style: italic; }

        /* Video */
        .be-r-video-wrap { margin: 1.25rem 0; }
        .be-r-video-ratio { position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 10px; background: #000; box-shadow: 0 2px 12px rgba(0,0,0,.15); }
        .be-r-video-ratio iframe { position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0; }

        .be-r-btn-wrap { margin: 1rem 0; }
        .be-r-btn { display: inline-block; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: opacity .15s, transform .12s; }
        .be-r-btn:hover { opacity: .88; transform: translateY(-1px); }
        .be-r-btn--primary   { background: #5aab1f; color: #fff; }
        .be-r-btn--secondary { background: #f0f0f0; color: #333; }
        .be-r-btn--outline   { border: 2px solid #5aab1f; color: #5aab1f; background: transparent; }
        .be-r-divider { border: none; border-top: 1.5px solid #e8e8e8; margin: 1.5rem 0; }
        .be-r-callout { display: flex; align-items: flex-start; gap: 10px; padding: 14px 16px; border-radius: 8px; margin: 1rem 0; font-size: 14px; }
        .be-r-callout--info    { background: #e8f4fd; border-left: 4px solid #2196f3; color: #0d47a1; }
        .be-r-callout--warning { background: #fff8e1; border-left: 4px solid #ffc107; color: #7a5300; }
        .be-r-callout--success { background: #e8f5e9; border-left: 4px solid #4caf50; color: #1b5e20; }
        .be-r-callout--danger  { background: #fdecea; border-left: 4px solid #f44336; color: #7f1010; }
        .be-r-callout-icon { font-size: 18px; line-height: 1.4; flex-shrink: 0; }
        .be-r-callout-text { line-height: 1.6; }
        .be-r-columns { display: grid; grid-template-columns: repeat(var(--col-count, 2), 1fr); gap: 20px; margin: 1rem 0; }
        .be-r-col { font-size: 14px; color: #333; line-height: 1.7; padding: 12px 14px; background: #f9f9f9; border-radius: 8px; border: 1px solid #ececec; }
        @media (max-width: 600px) { .be-r-columns { grid-template-columns: 1fr; } }
        .be-r-list { margin: 0.75rem 0 1rem 1.25rem; font-size: 15px; color: #333; }
        .be-r-list li { margin-bottom: 4px; line-height: 1.65; }
        .be-r-list--unordered { list-style-type: disc; }
        .be-r-list--ordered   { list-style-type: decimal; }
        .be-r-quote { border-left: 4px solid #5aab1f; margin: 1.25rem 0; padding: 10px 18px; background: #f6faf0; border-radius: 0 8px 8px 0; }
        .be-r-quote p { font-size: 16px; font-style: italic; color: #333; margin: 0 0 4px; }
        .be-r-cite { font-size: 13px; color: #888; font-style: normal; }
        .be-r-spacer { display: block; }
    </style>
@endif