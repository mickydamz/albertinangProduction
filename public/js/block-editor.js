/**
 * BlockEditor v6 — Word-like editing experience
 *
 * Keyboard feel:
 *   Enter          → split block / add new block below
 *   Backspace      → at start of empty block: delete it & focus previous
 *   Tab            → move focus to next block
 *   Shift+Tab      → move focus to previous block
 *   /              → open slash-command palette
 *   Ctrl/Cmd+Z     → undo (native per-field + block-level history)
 *   Ctrl/Cmd+B     → bold (native execCommand in contentEditable)
 *   Ctrl/Cmd+I     → italic
 *
 * Copy/paste:
 *   Plain text paste → preserved as-is in contentEditable
 *   Rich paste      → stripped to plain text (no HTML bleed)
 *   Pasting between blocks → natural browser behaviour
 *
 * Mouse:
 *   Hover → ghost controls appear in left margin
 *   Drag handle → reorder blocks
 *   Click anywhere on canvas → focus nearest editable
 *
 * Options: { uploadUrl, csrfToken, hiddenName, htmlName }
 */
(function (global) {
    'use strict';

    const uid   = () => Math.random().toString(36).slice(2, 9);
    const esc   = s  => String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    const clamp = (n, lo, hi) => Math.max(lo, Math.min(hi, n));

    /* ─── Video URL helpers ───────────────────────────────────────────── */
    function parseVideoUrl(url) {
        if (!url) return null;
        const ytMatch = url.match(/(?:youtu\.be\/|[?&]v=|embed\/)([A-Za-z0-9_-]{11})/);
        if (ytMatch) return { type: 'youtube', id: ytMatch[1], src: `https://www.youtube.com/embed/${ytMatch[1]}?rel=0&modestbranding=1` };
        const viMatch = url.match(/vimeo\.com\/(\d+)/);
        if (viMatch) return { type: 'vimeo', id: viMatch[1], src: `https://player.vimeo.com/video/${viMatch[1]}` };
        return null;
    }

    /* ─── Column block types & defaults ──────────────────────────────── */
    const COL_TYPES = [
        { type:'text',    label:'¶ Text'    },
        { type:'heading', label:'H Heading' },
        { type:'image',   label:'🖼 Image'  },
        { type:'button',  label:'⬛ Button'  },
        { type:'list',    label:'• List'    },
        { type:'callout', label:'📣 Callout' },
    ];

    const COL_DEFAULTS = {
        text   : { text:'' },
        heading: { text:'', level:2 },
        image  : { url:'', caption:'', alt:'' },
        button : { label:'Learn more', url:'#', style:'primary' },
        list   : { items:[''], style:'unordered' },
        callout: { text:'', icon:'💡', variant:'info' },
    };

    /* ─── Top-level block types & defaults ───────────────────────────── */
    const BLOCK_DEFAULTS = {
        paragraph : { text:'' },
        heading   : { text:'', level:2 },
        image     : { url:'', caption:'', alt:'' },
        button    : { label:'Learn more', url:'#', style:'primary' },
        divider   : {},
        callout   : { text:'', icon:'💡', variant:'info' },
        columns   : { cols:[{blocks:[]},{blocks:[]}] },
        list      : { items:[''], style:'unordered' },
        quote     : { text:'', author:'' },
        spacer    : { height:32 },
        video     : { url:'', caption:'' },
    };

    /* Slash command menu items */
    const SLASH_ITEMS = [
        { type:'paragraph', icon:'¶', label:'Text',     desc:'Plain paragraph' },
        { type:'heading',   icon:'H', label:'Heading',  desc:'H1–H4 section title' },
        { type:'image',     icon:'🖼', label:'Image',   desc:'Photo or illustration' },
        { type:'video',     icon:'▶', label:'Video',    desc:'YouTube or Vimeo embed' },
        { type:'button',    icon:'⬛', label:'Button',  desc:'Call-to-action link' },
        { type:'divider',   icon:'─', label:'Divider',  desc:'Horizontal rule' },
        { type:'callout',   icon:'📣', label:'Callout', desc:'Info / warning box' },
        { type:'columns',   icon:'⬜', label:'Columns', desc:'Side-by-side layout' },
        { type:'list',      icon:'•', label:'List',     desc:'Bullet or numbered' },
        { type:'quote',     icon:'"', label:'Quote',    desc:'Pull quote / citation' },
        { type:'spacer',    icon:'↕', label:'Spacer',   desc:'Vertical whitespace' },
    ];

    /* ─── HTML serialiser ────────────────────────────────────────────── */
    function colBlockToHtml(b) {
        switch(b.type) {
            case 'text':    return `<p style="font-size:14px;color:#333;margin:0 0 8px;line-height:1.6;">${esc(b.text)}</p>`;
            case 'heading': return `<h${b.level||2} style="font-weight:700;font-size:${b.level<=2?'1.1rem':'1rem'};color:#111;margin:0 0 6px;">${esc(b.text)}</h${b.level||2}>`;
            case 'image':   return b.url?`<figure style="margin:0 0 8px;"><img src="${esc(b.url)}" alt="${esc(b.alt||'')}" style="width:100%;border-radius:8px;display:block;">${b.caption?`<figcaption style="font-size:12px;color:#888;text-align:center;margin-top:4px;">${esc(b.caption)}</figcaption>`:''}</figure>`:'';
            case 'button':  return `<a href="${esc(b.url||'#')}" class="be-btn be-btn--${esc(b.style||'primary')}" style="display:inline-block;padding:8px 18px;border-radius:7px;font-size:13px;font-weight:600;text-decoration:none;margin-bottom:8px;">${esc(b.label)}</a>`;
            case 'list':  { const t=b.style==='ordered'?'ol':'ul'; return `<${t} style="margin:0 0 8px 1.2rem;font-size:14px;color:#333;">${(b.items||[]).map(i=>`<li>${esc(i)}</li>`).join('')}</${t}>`; }
            case 'callout': { const bg={info:'#e8f4fd',warning:'#fff8e1',success:'#e8f5e9',danger:'#fdecea'}[b.variant]||'#e8f4fd'; const bd={info:'#2196f3',warning:'#ffc107',success:'#4caf50',danger:'#f44336'}[b.variant]||'#2196f3'; return `<div style="display:flex;gap:8px;padding:10px 12px;border-radius:8px;margin-bottom:8px;font-size:13px;background:${bg};border-left:4px solid ${bd};">${esc(b.icon||'💡')} ${esc(b.text)}</div>`; }
            default: return '';
        }
    }

    function blockToHtml(b) {
        switch(b.type) {
            case 'paragraph': return b.text?`<p>${esc(b.text)}</p>`:'';
            case 'heading':   return b.text?`<h${b.level}>${esc(b.text)}</h${b.level}>`:'';
            case 'image':     return b.url?`<figure><img src="${esc(b.url)}" alt="${esc(b.alt||'')}" style="max-width:100%">${b.caption?`<figcaption>${esc(b.caption)}</figcaption>`:''}</figure>`:'';
            case 'button':    return `<a href="${esc(b.url||'#')}" class="be-btn be-btn--${esc(b.style||'primary')}">${esc(b.label)}</a>`;
            case 'divider':   return `<hr>`;
            case 'callout':   return `<div class="be-callout be-callout--${esc(b.variant||'info')}">${esc(b.icon||'')} ${esc(b.text)}</div>`;
            case 'columns':   return `<div class="be-columns" style="display:grid;grid-template-columns:repeat(${(b.cols||[]).length},1fr);gap:20px;">${(b.cols||[]).map(col=>`<div class="be-col" style="padding:12px 14px;background:#f9f9f9;border-radius:8px;border:1px solid #ececec;">${(col.blocks||[]).map(colBlockToHtml).join('')}</div>`).join('')}</div>`;
            case 'list':      { const t=b.style==='ordered'?'ol':'ul'; return `<${t}>${(b.items||[]).map(i=>`<li>${esc(i)}</li>`).join('')}</${t}>`; }
            case 'quote':     return `<blockquote>${esc(b.text)}${b.author?`<cite>— ${esc(b.author)}</cite>`:''}</blockquote>`;
            case 'spacer':    return `<div style="height:${clamp(b.height||32,8,200)}px"></div>`;
            case 'video': {
                const v = parseVideoUrl(b.url);
                if (!v) return '';
                return `<div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:8px;margin:.75rem 0;">` +
                    `<iframe src="${esc(v.src)}" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>` +
                    `</div>${b.caption ? `<p style="font-size:13px;color:#9ca3af;text-align:center;margin-top:6px;font-style:italic;">${esc(b.caption)}</p>` : ''}`;
            }
            default:          return '';
        }
    }

    /* ════════════════════════════════════════════════════════════════════
       BlockEditor
    ════════════════════════════════════════════════════════════════════ */
    function BlockEditor(selector, initialJson, options) {
        this.root    = document.querySelector(selector);
        this.options = Object.assign({
            hiddenName:'description_blocks', htmlName:'description_html',
            uploadUrl:null,
            csrfToken:document.querySelector('meta[name="csrf-token"]')?.content||'',
        }, options||{});

        this.blocks  = [];
        this.dragSrc = null;
        this._uploads = {};
        this._history = [];  /* undo stack */
        this._slashEl = null;
        this._slashTarget = null;

        if (!this.root) return;

        /* Parse initial JSON, migrate old column format */
        try {
            const p = typeof initialJson==='string'?JSON.parse(initialJson):initialJson;
            if (Array.isArray(p)) this.blocks = p.map(b=>{
                const block=Object.assign({id:uid()},b);
                if (block.type==='columns'&&block.cols) {
                    block.cols=block.cols.map(col=>{
                        if (typeof col.text==='string') return {blocks:col.text?[{id:uid(),type:'text',text:col.text}]:[]};
                        if (!col.blocks) col.blocks=[];
                        col.blocks=col.blocks.map(cb=>Object.assign({id:uid()},cb));
                        return col;
                    });
                }
                return block;
            });
        } catch(e){}

        this._injectStyles();
        this._buildChrome();
        this._render();
        this._attachGlobalListeners();
    }

    BlockEditor.prototype._makeBlock  = function(type,extra){ return Object.assign({id:uid(),type},JSON.parse(JSON.stringify(BLOCK_DEFAULTS[type]||{})),extra||{}); };
    BlockEditor.prototype._makeColBlock= function(type){ return Object.assign({id:uid(),type},JSON.parse(JSON.stringify(COL_DEFAULTS[type]||{}))); };

    /* ─── History (block-level undo) ─────────────────────────────────── */
    BlockEditor.prototype._pushHistory = function() {
        const snap = JSON.stringify(this.blocks.map(b=>{const c=Object.assign({},b);delete c.id;return c;}));
        if (this._history[this._history.length-1]===snap) return;
        this._history.push(snap);
        if (this._history.length>50) this._history.shift();
    };

    BlockEditor.prototype._undo = function() {
        if (this._history.length<2) return;
        this._history.pop();
        const prev = JSON.parse(this._history[this._history.length-1]);
        this.blocks = prev.map(b=>Object.assign({id:uid()},b));
        this._render();
    };

    /* ─── Upload ─────────────────────────────────────────────────────── */
    BlockEditor.prototype._upload = function(file, onOk, onErr) {
        if (!this.options.uploadUrl) { onOk(URL.createObjectURL(file),true); return; }
        const fd=new FormData(); fd.append('image',file); fd.append('_token',this.options.csrfToken);
        fetch(this.options.uploadUrl,{method:'POST',body:fd})
            .then(r=>r.json()).then(d=>{ if(d.url) onOk(d.url,false); else onErr(d.message||'Upload failed'); })
            .catch(()=>onErr('Upload failed'));
    };

    /* ─── Toast ──────────────────────────────────────────────────────── */
    BlockEditor.prototype._toast = function(msg,type='info') {
        const el=document.createElement('div'); el.className=`wyg-toast wyg-toast--${type}`; el.textContent=msg;
        document.body.appendChild(el); setTimeout(()=>el.classList.add('wyg-toast--in'),10);
        setTimeout(()=>{ el.classList.remove('wyg-toast--in'); setTimeout(()=>el.remove(),300); },3500);
    };

    /* ─── Chrome ─────────────────────────────────────────────────────── */
    BlockEditor.prototype._buildChrome = function() {
        this.root.innerHTML=''; this.root.className='wyg-shell';

        /* Toolbar */
        const tb=document.createElement('div'); tb.className='wyg-toolbar';
        tb.innerHTML=`<span class="wyg-tb-label">Insert</span>`+
            SLASH_ITEMS.map(i=>`<button type="button" class="wyg-tb-btn" data-type="${i.type}" title="${i.desc}">${i.icon} ${i.label}</button>`).join('');
        tb.addEventListener('click',e=>{ const b=e.target.closest('.wyg-tb-btn'); if(b) this._addBlock(b.dataset.type); });

        /* Canvas */
        this.canvas=document.createElement('div'); this.canvas.className='wyg-canvas';
        this.canvas.setAttribute('role','document');

        /* Hidden inputs */
        this._hiddenBlocks=document.createElement('input'); this._hiddenBlocks.type='hidden'; this._hiddenBlocks.name=this.options.hiddenName;
        this._hiddenHtml=document.createElement('input'); this._hiddenHtml.type='hidden'; this._hiddenHtml.name=this.options.htmlName;

        /* Slash palette (hidden until /) */
        this._slashEl=document.createElement('div'); this._slashEl.className='wyg-slash'; this._slashEl.style.display='none';
        this._slashEl.setAttribute('role','listbox');
        document.body.appendChild(this._slashEl);

        this.root.appendChild(tb); this.root.appendChild(this.canvas);
        this.root.appendChild(this._hiddenBlocks); this.root.appendChild(this._hiddenHtml);
    };

    /* ─── Render ─────────────────────────────────────────────────────── */
    BlockEditor.prototype._render = function() {
        this.canvas.innerHTML='';
        if (!this.blocks.length) {
            const emp=document.createElement('div'); emp.className='wyg-empty';
            emp.innerHTML='<span>Start writing, or type <kbd>/</kbd> for commands</span>';
            this.canvas.appendChild(emp);
        } else {
            this.blocks.forEach(b=>this.canvas.appendChild(this._buildBlockEl(b)));
        }
        this._sync();
    };

    /* ─── Block element ──────────────────────────────────────────────── */
    BlockEditor.prototype._buildBlockEl = function(b) {
        const self=this;
        const wrap=document.createElement('div');
        wrap.className='wyg-block'; wrap.dataset.id=b.id; wrap.dataset.type=b.type;
        wrap.draggable=true;

        /* Left margin controls */
        const ctrl=document.createElement('div'); ctrl.className='wyg-ctrl';
        ctrl.innerHTML=`
            <button type="button" class="wyg-ctrl-btn wyg-handle" title="Drag to reorder" draggable="false">
                <svg width="10" height="14" viewBox="0 0 10 14"><circle cx="3" cy="2.5" r="1.3" fill="currentColor"/><circle cx="7" cy="2.5" r="1.3" fill="currentColor"/><circle cx="3" cy="7" r="1.3" fill="currentColor"/><circle cx="7" cy="7" r="1.3" fill="currentColor"/><circle cx="3" cy="11.5" r="1.3" fill="currentColor"/><circle cx="7" cy="11.5" r="1.3" fill="currentColor"/></svg>
            </button>
            <button type="button" class="wyg-ctrl-btn wyg-up"  title="Move up">↑</button>
            <button type="button" class="wyg-ctrl-btn wyg-dn"  title="Move down">↓</button>
            <button type="button" class="wyg-ctrl-btn wyg-dup" title="Duplicate">⧉</button>
            <button type="button" class="wyg-ctrl-btn wyg-del" title="Delete block">✕</button>`;

        ctrl.querySelector('.wyg-up').addEventListener('click', e=>{e.stopPropagation();this._move(b.id,-1);});
        ctrl.querySelector('.wyg-dn').addEventListener('click', e=>{e.stopPropagation();this._move(b.id,1);});
        ctrl.querySelector('.wyg-dup').addEventListener('click',e=>{e.stopPropagation();this._duplicate(b.id);});
        ctrl.querySelector('.wyg-del').addEventListener('click',e=>{e.stopPropagation();this._delete(b.id);});

        /* Content area */
        const body=document.createElement('div'); body.className='wyg-block-body';
        this._fillBody(b,body);

        wrap.appendChild(ctrl); wrap.appendChild(body);

        /* Drag & drop */
        wrap.addEventListener('dragstart',e=>{
            if(!e.target.closest('.wyg-handle')&&e.target!==wrap) { e.preventDefault(); return; }
            this.dragSrc=b.id; wrap.classList.add('wyg-dragging');
            e.dataTransfer.effectAllowed='move'; e.dataTransfer.setData('text/plain',b.id);
        });
        wrap.addEventListener('dragend',  ()=>{ this.dragSrc=null; wrap.classList.remove('wyg-dragging'); document.querySelectorAll('.wyg-drop-target').forEach(el=>el.classList.remove('wyg-drop-target')); });
        wrap.addEventListener('dragover', e=>{ e.preventDefault(); if(this.dragSrc&&this.dragSrc!==b.id) wrap.classList.add('wyg-drop-target'); });
        wrap.addEventListener('dragleave',()=>wrap.classList.remove('wyg-drop-target'));
        wrap.addEventListener('drop',e=>{ e.preventDefault(); wrap.classList.remove('wyg-drop-target'); if(this.dragSrc&&this.dragSrc!==b.id) this._reorder(this.dragSrc,b.id); });

        return wrap;
    };

    /* ─── Fill block body ────────────────────────────────────────────── */
    BlockEditor.prototype._fillBody = function(b, container) {
        const self=this;
        container.innerHTML='';

        /* Helper: make a rich contentEditable element */
        function makeEditable(tag, cls, placeholder, initialText) {
            const el=document.createElement(tag);
            el.className=(cls||'')+' wyg-editable';
            el.contentEditable='true';
            el.dataset.placeholder=placeholder||'';
            el.spellcheck=true;
            /* Set text content safely */
            if (initialText) el.textContent=initialText;

            /* Paste: strip HTML, keep plain text */
            el.addEventListener('paste', e=>{
                e.preventDefault();
                const text=(e.clipboardData||window.clipboardData).getData('text/plain');
                document.execCommand('insertText',false,text);
            });

            /* Global keyboard handling */
            el.addEventListener('keydown', ev=>self._onEditableKeydown(ev,b,el,container));
            el.addEventListener('input',   ()=>self._onEditableInput(b,el));

            /* Slash command trigger */
            el.addEventListener('keyup', ev=>{ if(ev.key==='/') self._openSlash(b,el); });

            return el;
        }

        switch(b.type) {

            case 'paragraph': {
                const el=makeEditable('p','be-r-paragraph','Write something, or type / for commands…',b.text);
                container.appendChild(el);
                break;
            }

            case 'heading': {
                const lvlRow=document.createElement('div'); lvlRow.className='wyg-heading-lvl';
                [1,2,3,4].forEach(n=>{
                    const btn=document.createElement('button'); btn.type='button';
                    btn.className='wyg-lvl-btn'+(b.level==n?' wyg-lvl-active':''); btn.textContent=`H${n}`;
                    btn.addEventListener('mousedown',e=>{ e.preventDefault(); b.level=n; self._pushHistory(); self._reRenderBlock(b); });
                    lvlRow.appendChild(btn);
                });
                const el=makeEditable(`h${b.level||2}`,`be-r-heading be-r-h${b.level||2}`,'Heading…',b.text);
                container.appendChild(lvlRow); container.appendChild(el);
                break;
            }

            case 'image': {
                if (b.url) {
                    const fig=document.createElement('figure'); fig.className='be-r-figure wyg-image-figure';
                    const img=document.createElement('img'); img.src=b.url; img.alt=b.alt||''; img.className='be-r-image';
                    img.onerror=()=>{ img.src='https://placehold.co/800x400/f3f4f6/9ca3af?text=Image+not+found'; };
                    const chg=document.createElement('button'); chg.type='button'; chg.className='wyg-img-change'; chg.textContent='⟳ Change';
                    chg.addEventListener('click',()=>{ self._pushHistory(); b.url='';b.caption='';b.alt=''; self._reRenderBlock(b); });
                    const cap=makeEditable('figcaption','be-r-caption','Add a caption (optional)…',b.caption);
                    fig.appendChild(img); fig.appendChild(chg); fig.appendChild(cap);
                    container.appendChild(fig);
                } else {
                    container.appendChild(self._makeImgZone(b,()=>self._reRenderBlock(b)));
                }
                break;
            }

            /* ─── VIDEO BLOCK ─────────────────────────────────────── */
            case 'video': {
                const wrap = document.createElement('div'); wrap.className = 'wyg-video-wrap';

                if (b.url && b.url.trim()) {
                    const v = parseVideoUrl(b.url);
                    if (v) {
                        /* Show live embed preview */
                        const fig = document.createElement('figure');
                        fig.className = 'wyg-image-figure'; fig.style.margin = '.75rem 0';

                        const ratio = document.createElement('div'); ratio.className = 'wyg-video-ratio';
                        const iframe = document.createElement('iframe');
                        iframe.src = v.src; iframe.allowFullscreen = true; iframe.loading = 'lazy';
                        iframe.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;border:0;border-radius:8px;';
                        ratio.appendChild(iframe);

                        /* Badge showing source */
                        const badge = document.createElement('span'); badge.className = 'wyg-video-badge';
                        badge.textContent = v.type === 'youtube' ? '▶ YouTube' : '▶ Vimeo';
                        ratio.appendChild(badge);

                        const chg = document.createElement('button'); chg.type = 'button';
                        chg.className = 'wyg-img-change'; chg.textContent = '⟳ Change';
                        chg.addEventListener('click', () => { b.url = ''; b.caption = ''; self._pushHistory(); self._reRenderBlock(b); });

                        const cap = makeEditable('figcaption', 'be-r-caption', 'Add a caption (optional)…', b.caption);
                        cap.addEventListener('input', () => { b.caption = cap.textContent; self._sync(); });

                        fig.appendChild(ratio); fig.appendChild(chg); fig.appendChild(cap);
                        wrap.appendChild(fig);
                    } else {
                        /* URL didn't parse — reset and show input */
                        b.url = '';
                        wrap.appendChild(makeVideoInput());
                    }
                } else {
                    wrap.appendChild(makeVideoInput());
                }

                function makeVideoInput() {
                    const zone = document.createElement('div'); zone.className = 'wyg-video-zone';
                    zone.innerHTML =
                        `<div class="wyg-video-zone-icon">` +
                            `<svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="2" y="4" width="20" height="16" rx="2"/><polygon points="10 9 15 12 10 15" fill="currentColor" stroke="none"/></svg>` +
                        `</div>` +
                        `<p class="wyg-video-zone-title">Embed a YouTube or Vimeo video</p>` +
                        `<p class="wyg-video-zone-sub">Paste the video URL below</p>`;

                    const row = document.createElement('div'); row.className = 'wyg-img-url-row';
                    const inp = document.createElement('input'); inp.type = 'text';
                    inp.placeholder = 'https://www.youtube.com/watch?v=…  or  https://vimeo.com/…';
                    inp.className = 'wyg-option-input'; inp.value = b.url || '';

                    const btn = document.createElement('button'); btn.type = 'button';
                    btn.className = 'wyg-url-btn'; btn.textContent = 'Embed';

                    btn.addEventListener('click', () => {
                        const u = inp.value.trim();
                        if (!u) return;
                        if (!parseVideoUrl(u)) {
                            self._toast('Could not recognise that URL. Try a YouTube or Vimeo link.', 'error');
                            return;
                        }
                        b.url = u; self._pushHistory(); self._reRenderBlock(b);
                    });
                    inp.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); btn.click(); } });

                    row.appendChild(inp); row.appendChild(btn);
                    zone.appendChild(row);
                    return zone;
                }

                container.appendChild(wrap);
                break;
            }
            /* ──────────────────────────────────────────────────────── */

            case 'button': {
                const wrap=document.createElement('div'); wrap.className='wyg-btn-wrap';
                const btn=makeEditable('a','be-r-btn be-r-btn--'+(b.style||'primary'),'Button label…',b.label);
                btn.href='javascript:void(0)';
                const opts=document.createElement('div'); opts.className='wyg-btn-opts';
                const urlInp=document.createElement('input'); urlInp.type='text'; urlInp.value=b.url||''; urlInp.placeholder='https://…'; urlInp.className='wyg-option-input';
                urlInp.addEventListener('input',()=>{ b.url=urlInp.value; self._sync(); });
                const stylePills=document.createElement('div'); stylePills.className='wyg-pills';
                [['primary','Primary'],['secondary','Secondary'],['outline','Outline']].forEach(([val,lbl])=>{
                    const sp=document.createElement('button'); sp.type='button';
                    sp.className='wyg-pill'+(b.style===val?' active':''); sp.textContent=lbl;
                    sp.addEventListener('click',()=>{ b.style=val; btn.className=`be-r-btn be-r-btn--${val} wyg-editable`; stylePills.querySelectorAll('.wyg-pill').forEach(x=>x.classList.remove('active')); sp.classList.add('active'); self._sync(); });
                    stylePills.appendChild(sp);
                });
                opts.appendChild(urlInp); opts.appendChild(stylePills);
                wrap.appendChild(btn); wrap.appendChild(opts);
                container.appendChild(wrap);
                break;
            }

            case 'divider': {
                const hr=document.createElement('hr'); hr.className='be-r-divider';
                container.appendChild(hr); break;
            }

            case 'callout': {
                const varRow=document.createElement('div'); varRow.className='wyg-pills wyg-callout-variants';
                const box=document.createElement('div'); box.className=`be-r-callout be-r-callout--${b.variant||'info'}`;
                [['info','ℹ Info'],['warning','⚠ Warning'],['success','✓ Success'],['danger','✕ Danger']].forEach(([val,lbl])=>{
                    const vb=document.createElement('button'); vb.type='button'; vb.className='wyg-pill'+(b.variant===val?' active':''); vb.textContent=lbl;
                    vb.addEventListener('click',()=>{ b.variant=val; box.className=`be-r-callout be-r-callout--${val}`; varRow.querySelectorAll('.wyg-pill').forEach(x=>x.classList.remove('active')); vb.classList.add('active'); self._sync(); });
                    varRow.appendChild(vb);
                });
                const iconEl=makeEditable('span','be-r-callout-icon','💡',b.icon||'💡');
                iconEl.style.cssText='min-width:28px;flex-shrink:0;';
                const textEl=makeEditable('span','be-r-callout-text','Callout message…',b.text);
                box.appendChild(iconEl); box.appendChild(textEl);
                container.appendChild(varRow); container.appendChild(box); break;
            }

            case 'columns': {
                b.cols.forEach(col=>{ if(!col.blocks) col.blocks=[]; });
                const countRow=document.createElement('div'); countRow.className='wyg-col-count-row';
                countRow.innerHTML='<span class="wyg-col-count-label">Columns</span>';
                [2,3,4].forEach(n=>{
                    const btn=document.createElement('button'); btn.type='button';
                    btn.className='wyg-col-count-btn'+(b.cols.length===n?' active':''); btn.textContent=n;
                    btn.addEventListener('click',()=>{ while(b.cols.length<n) b.cols.push({blocks:[]}); while(b.cols.length>n) b.cols.pop(); self._reRenderBlock(b); self._sync(); });
                    countRow.appendChild(btn);
                });
                const grid=document.createElement('div'); grid.className='wyg-rich-cols-grid';
                grid.style.gridTemplateColumns=`repeat(${b.cols.length},1fr)`;
                b.cols.forEach((col,ci)=>{
                    const colEl=document.createElement('div'); colEl.className='wyg-rich-col';
                    const mini=document.createElement('div'); mini.className='wyg-mini-canvas';
                    const renderMini=()=>{
                        mini.innerHTML='';
                        if(!col.blocks.length){ const e=document.createElement('div'); e.className='wyg-mini-empty'; e.textContent='Empty column'; mini.appendChild(e); }
                        else col.blocks.forEach((cb,ci2)=>mini.appendChild(self._buildColBlockEl(cb,ci2,col,renderMini)));
                    };
                    renderMini();
                    const miniTb=document.createElement('div'); miniTb.className='wyg-mini-toolbar';
                    miniTb.innerHTML='<span class="wyg-mini-tb-label">Add</span>';
                    COL_TYPES.forEach(item=>{
                        const btn=document.createElement('button'); btn.type='button'; btn.className='wyg-mini-tb-btn'; btn.textContent=item.label;
                        btn.addEventListener('click',()=>{ col.blocks.push(self._makeColBlock(item.type)); renderMini(); self._sync(); });
                        miniTb.appendChild(btn);
                    });
                    colEl.appendChild(mini); colEl.appendChild(miniTb); grid.appendChild(colEl);
                });
                container.appendChild(countRow); container.appendChild(grid); break;
            }

            case 'list': {
                const styleRow=document.createElement('div'); styleRow.className='wyg-pills wyg-list-style-row';
                [['unordered','• Bullet'],['ordered','1. Numbered']].forEach(([val,lbl])=>{
                    const sp=document.createElement('button'); sp.type='button'; sp.className='wyg-pill'+(b.style===val?' active':''); sp.textContent=lbl;
                    sp.addEventListener('click',()=>{ b.style=val; self._reRenderBlock(b); self._sync(); }); styleRow.appendChild(sp);
                });
                const tag=b.style==='ordered'?'ol':'ul';
                const listEl=document.createElement(tag); listEl.className=`be-r-list be-r-list--${b.style}`;
                const renderItems=()=>{
                    listEl.innerHTML='';
                    b.items.forEach((item,i)=>{
                        const li=document.createElement('li'); li.className='wyg-li';
                        const span=document.createElement('span');
                        span.className='wyg-editable'; span.contentEditable='true'; span.spellcheck=true;
                        span.dataset.placeholder=`Item ${i+1}…`; span.textContent=item||'';
                        span.addEventListener('input',()=>{ b.items[i]=span.textContent; self._sync(); });
                        span.addEventListener('paste',e=>{ e.preventDefault(); const t=(e.clipboardData||window.clipboardData).getData('text/plain'); document.execCommand('insertText',false,t); });
                        span.addEventListener('keydown',e=>{
                            if (e.key==='Enter') {
                                e.preventDefault(); self._pushHistory();
                                b.items.splice(i+1,0,''); renderItems(); self._sync();
                                setTimeout(()=>{ const items=listEl.querySelectorAll('[contenteditable]'); items[i+1]?.focus(); },0);
                            }
                            if (e.key==='Backspace'&&span.textContent===''&&b.items.length>1) {
                                e.preventDefault(); self._pushHistory();
                                b.items.splice(i,1); renderItems(); self._sync();
                                setTimeout(()=>{ const items=listEl.querySelectorAll('[contenteditable]'); const prev=items[Math.max(0,i-1)]; if(prev){prev.focus();self._cursorToEnd(prev);} },0);
                            }
                            if (e.key==='Tab') { e.preventDefault(); const next=listEl.querySelectorAll('[contenteditable]')[i+1]; if(next) next.focus(); else self._focusNextBlock(b.id); }
                        });
                        const rm=document.createElement('button'); rm.type='button'; rm.className='wyg-li-rm'; rm.textContent='✕';
                        rm.addEventListener('click',()=>{ if(b.items.length>1){b.items.splice(i,1);renderItems();self._sync();} });
                        li.appendChild(span); li.appendChild(rm); listEl.appendChild(li);
                    });
                };
                renderItems();
                const addBtn=document.createElement('button'); addBtn.type='button'; addBtn.className='wyg-add-item-btn'; addBtn.textContent='+ Add item';
                addBtn.addEventListener('click',()=>{ b.items.push(''); renderItems(); self._sync(); setTimeout(()=>{ const items=listEl.querySelectorAll('[contenteditable]'); items[items.length-1]?.focus(); },0); });
                container.appendChild(styleRow); container.appendChild(listEl); container.appendChild(addBtn); break;
            }

            case 'quote': {
                const bq=document.createElement('blockquote'); bq.className='be-r-quote';
                const pEl=makeEditable('p','','Type your quote…',b.text);
                const citeEl=makeEditable('cite','be-r-cite','— Author (optional)',b.author?`— ${b.author}`:'');
                /* strip leading dash from author on save */
                citeEl.addEventListener('input',()=>{ b.author=citeEl.textContent.replace(/^—\s*/,'').trim(); self._sync(); });
                bq.appendChild(pEl); bq.appendChild(citeEl);
                container.appendChild(bq); break;
            }

            case 'spacer': {
                const wrap=document.createElement('div'); wrap.className='wyg-spacer-wrap';
                const vis=document.createElement('div'); vis.className='wyg-spacer-vis'; vis.style.height=clamp(b.height||32,8,200)+'px';
                const ctrl2=document.createElement('div'); ctrl2.className='wyg-spacer-ctrl';
                const lbl=document.createElement('span'); lbl.className='wyg-spacer-lbl'; lbl.textContent=(b.height||32)+'px';
                const range=document.createElement('input'); range.type='range'; range.min=8; range.max=200; range.value=b.height||32; range.className='wyg-range';
                range.addEventListener('input',()=>{ b.height=+range.value; lbl.textContent=b.height+'px'; vis.style.height=clamp(b.height,8,200)+'px'; self._sync(); });
                ctrl2.appendChild(document.createTextNode('Height: ')); ctrl2.appendChild(lbl); ctrl2.appendChild(range);
                wrap.appendChild(ctrl2); wrap.appendChild(vis); container.appendChild(wrap); break;
            }
        }
    };

    /* ─── Keyboard handling for contenteditable blocks ───────────────── */
    BlockEditor.prototype._onEditableKeydown = function(e, b, el, container) {
        const self=this;
        const atStart = this._cursorAtStart(el);
        const atEnd   = this._cursorAtEnd(el);

        /* Ctrl/Cmd + Z → block-level undo */
        if ((e.ctrlKey||e.metaKey) && e.key==='z' && !e.shiftKey) {
            /* Let browser handle field-level undo first, block-level on empty */
            if (!el.textContent.trim()) { e.preventDefault(); this._undo(); }
            return;
        }

        /* Enter → split or insert new block below */
        if (e.key==='Enter' && !e.shiftKey) {
            /* In headings and single-line blocks: insert paragraph below */
            if (['heading','button'].includes(b.type) || el.tagName.match(/^H[1-4]$/i)) {
                e.preventDefault();
                this._pushHistory();
                const newBlock=this._makeBlock('paragraph');
                const idx=this.blocks.findIndex(x=>x.id===b.id);
                this.blocks.splice(idx+1,0,newBlock);
                this._render();
                setTimeout(()=>this._focusBlock(newBlock.id),0);
                return;
            }
            /* In paragraph at end → new paragraph below */
            if (b.type==='paragraph' && atEnd) {
                e.preventDefault(); this._pushHistory();
                const newBlock=this._makeBlock('paragraph');
                const idx=this.blocks.findIndex(x=>x.id===b.id);
                this.blocks.splice(idx+1,0,newBlock);
                this._render();
                setTimeout(()=>this._focusBlock(newBlock.id),0);
                return;
            }
            /* In paragraph mid-text → split into two paragraphs */
            if (b.type==='paragraph' && !atEnd) {
                e.preventDefault(); this._pushHistory();
                const sel=window.getSelection();
                const range=sel.getRangeAt(0);
                /* Text after cursor */
                const afterRange=document.createRange();
                afterRange.setStart(range.endContainer,range.endOffset);
                afterRange.setEnd(el,el.childNodes.length);
                const afterText=afterRange.toString();
                /* Delete from cursor to end in current block */
                range.deleteContents();
                afterRange.deleteContents();
                b.text=el.textContent;
                /* New block with the text after cursor */
                const newBlock=this._makeBlock('paragraph',{text:afterText});
                const idx=this.blocks.findIndex(x=>x.id===b.id);
                this.blocks.splice(idx+1,0,newBlock);
                this._render();
                setTimeout(()=>this._focusBlock(newBlock.id),0);
                return;
            }
        }

        /* Backspace at start of empty block → delete it */
        if (e.key==='Backspace' && atStart && !el.textContent) {
            e.preventDefault(); this._pushHistory();
            this._delete(b.id, true /* focus prev */);
            return;
        }

        /* Backspace at start of non-empty block → merge with previous */
        if (e.key==='Backspace' && atStart && el.textContent && b.type==='paragraph') {
            const idx=this.blocks.findIndex(x=>x.id===b.id);
            if (idx>0 && this.blocks[idx-1].type==='paragraph') {
                e.preventDefault(); this._pushHistory();
                const prev=this.blocks[idx-1];
                const prevLen=prev.text.length;
                prev.text = (prev.text||'') + (b.text||'');
                this.blocks.splice(idx,1);
                this._render();
                setTimeout(()=>{ const prevEl=this._getEditable(prev.id); if(prevEl){ prevEl.focus(); this._cursorAt(prevEl,prevLen); } },0);
                return;
            }
        }

        /* Tab → move to next block */
        if (e.key==='Tab' && !e.shiftKey) { e.preventDefault(); this._focusNextBlock(b.id); return; }
        if (e.key==='Tab' && e.shiftKey)  { e.preventDefault(); this._focusPrevBlock(b.id); return; }

        /* ArrowDown at end → next block */
        if (e.key==='ArrowDown' && atEnd) { e.preventDefault(); this._focusNextBlock(b.id); return; }
        /* ArrowUp at start → prev block */
        if (e.key==='ArrowUp' && atStart) { e.preventDefault(); this._focusPrevBlock(b.id); return; }

        /* Escape → close slash menu */
        if (e.key==='Escape') { this._closeSlash(); }
    };

    BlockEditor.prototype._onEditableInput = function(b, el) {
        /* Sync text to block data */
        const text = el.textContent;
        if (b.type==='paragraph') b.text=text;
        else if (b.type==='heading') b.text=text;
        else if (b.type==='callout') {
            /* handled by individual listeners */
        }
        this._sync();
    };

    /* ─── Slash command palette ──────────────────────────────────────── */
    BlockEditor.prototype._openSlash = function(b, el) {
        const self=this;
        this._slashTarget={b,el};
        const rect=el.getBoundingClientRect();

        this._slashEl.innerHTML='';
        SLASH_ITEMS.forEach((item,idx)=>{
            const row=document.createElement('div'); row.className='wyg-slash-item'; row.dataset.idx=idx;
            row.innerHTML=`<span class="wyg-slash-icon">${item.icon}</span><span class="wyg-slash-label">${item.label}</span><span class="wyg-slash-desc">${item.desc}</span>`;
            row.addEventListener('mousedown',e=>{
                e.preventDefault();
                this._closeSlash();
                /* Remove the slash from the current block */
                const cur=el.textContent;
                if (cur.endsWith('/')) { el.textContent=cur.slice(0,-1); if(b.type==='paragraph') b.text=el.textContent; }
                /* If current block is empty paragraph, convert it; otherwise insert below */
                if (b.type==='paragraph'&&!b.text.trim()) {
                    b.type=item.type;
                    Object.assign(b, JSON.parse(JSON.stringify(BLOCK_DEFAULTS[item.type]||{})));
                    self._reRenderBlock(b); setTimeout(()=>self._focusBlock(b.id),0);
                } else {
                    const newBlock=self._makeBlock(item.type);
                    const i2=self.blocks.findIndex(x=>x.id===b.id);
                    self.blocks.splice(i2+1,0,newBlock);
                    self._render(); setTimeout(()=>self._focusBlock(newBlock.id),0);
                }
            });
            this._slashEl.appendChild(row);
        });

        /* Position below cursor */
        this._slashEl.style.cssText=`display:block;top:${rect.bottom+window.scrollY+4}px;left:${rect.left+window.scrollX}px;`;
        this._slashEl.querySelector('.wyg-slash-item')?.classList.add('wyg-slash-active');

        /* Keyboard navigation in palette */
        this._slashKeyHandler=e=>{
            const items=[...this._slashEl.querySelectorAll('.wyg-slash-item')];
            const active=this._slashEl.querySelector('.wyg-slash-active');
            const idx=items.indexOf(active);
            if(e.key==='ArrowDown'){ e.preventDefault(); items[idx+1]?.classList.add('wyg-slash-active'); active?.classList.remove('wyg-slash-active'); }
            else if(e.key==='ArrowUp'){ e.preventDefault(); items[idx-1]?.classList.add('wyg-slash-active'); active?.classList.remove('wyg-slash-active'); }
            else if(e.key==='Enter'){ e.preventDefault(); active?.dispatchEvent(new MouseEvent('mousedown')); }
            else if(e.key==='Escape'){ this._closeSlash(); }
        };
        document.addEventListener('keydown',this._slashKeyHandler,{capture:true});
    };

    BlockEditor.prototype._closeSlash = function() {
        if(this._slashEl) this._slashEl.style.display='none';
        if(this._slashKeyHandler) { document.removeEventListener('keydown',this._slashKeyHandler,{capture:true}); this._slashKeyHandler=null; }
    };

    /* ─── Focus helpers ──────────────────────────────────────────────── */
    BlockEditor.prototype._getEditable = function(blockId) {
        return this.canvas.querySelector(`[data-id="${blockId}"] [contenteditable]`);
    };
    BlockEditor.prototype._focusBlock = function(blockId) {
        const el=this._getEditable(blockId);
        if (el) { el.focus(); this._cursorToEnd(el); }
    };
    BlockEditor.prototype._focusNextBlock = function(blockId) {
        const idx=this.blocks.findIndex(b=>b.id===blockId);
        if (idx<this.blocks.length-1) this._focusBlock(this.blocks[idx+1].id);
    };
    BlockEditor.prototype._focusPrevBlock = function(blockId) {
        const idx=this.blocks.findIndex(b=>b.id===blockId);
        if (idx>0) this._focusBlock(this.blocks[idx-1].id);
    };
    BlockEditor.prototype._cursorToEnd = function(el) {
        const range=document.createRange(); const sel=window.getSelection();
        range.selectNodeContents(el); range.collapse(false);
        sel.removeAllRanges(); sel.addRange(range);
    };
    BlockEditor.prototype._cursorAt = function(el, offset) {
        try {
            const range=document.createRange(); const sel=window.getSelection();
            const node=el.firstChild||el;
            range.setStart(node, Math.min(offset, node.textContent?.length||0));
            range.collapse(true); sel.removeAllRanges(); sel.addRange(range);
        } catch(e) { this._cursorToEnd(el); }
    };
    BlockEditor.prototype._cursorAtStart = function(el) {
        try { const sel=window.getSelection(); if(!sel.rangeCount) return true; const r=sel.getRangeAt(0); return r.startOffset===0&&(r.startContainer===el||r.startContainer===el.firstChild); } catch(e){return true;}
    };
    BlockEditor.prototype._cursorAtEnd = function(el) {
        try { const sel=window.getSelection(); if(!sel.rangeCount) return true; const r=sel.getRangeAt(0); const txt=el.textContent; return r.endOffset===txt.length||r.endOffset===(r.endContainer.textContent?.length||0); } catch(e){return true;}
    };

    /* ─── Image drop zone ────────────────────────────────────────────── */
    BlockEditor.prototype._makeImgZone = function(b, onDone, compact) {
        const self=this;
        const wrap=document.createElement('div');
        const zone=document.createElement('div'); zone.className=compact?'wyg-col-img-zone':'wyg-img-drop';
        const fi=document.createElement('input'); fi.type='file'; fi.accept='image/*'; fi.style.cssText='position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;';
        zone.innerHTML=compact
            ?`<div class="wyg-col-img-zone-icon">🖼</div><p class="wyg-col-img-zone-text">Drop or <span>browse</span></p>`
            :`<div class="wyg-img-drop-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg></div><p class="wyg-img-drop-title">Drop image here or <span>browse</span></p><p class="wyg-img-drop-sub">JPG, PNG, GIF, WebP</p>`;
        zone.appendChild(fi);
        const urlRow=document.createElement('div'); urlRow.className=compact?'wyg-col-url-row':'wyg-img-url-row';
        const urlInp=document.createElement('input'); urlInp.type='text'; urlInp.placeholder=compact?'or paste URL':'…or paste an image URL'; urlInp.className=compact?'wyg-col-url-inp':'wyg-option-input';
        const urlBtn=document.createElement('button'); urlBtn.type='button'; urlBtn.textContent='Use URL'; urlBtn.className=compact?'wyg-col-url-btn':'wyg-url-btn';
        urlRow.appendChild(urlInp); urlRow.appendChild(urlBtn);
        const uploadingEl=document.createElement('div'); uploadingEl.className='wyg-uploading'; uploadingEl.style.display='none';
        uploadingEl.innerHTML='<span class="wyg-spin">⟳</span> Uploading…';
        wrap.appendChild(zone); wrap.appendChild(urlRow); wrap.appendChild(uploadingEl);

        function handleFile(file) {
            if(!file||!file.type.startsWith('image/')) return;
            const prev=URL.createObjectURL(file); b.url=prev;
            zone.style.display='none'; urlRow.style.display='none'; uploadingEl.style.display='flex';
            const prevImg=document.createElement('img'); prevImg.src=prev; prevImg.className=compact?'wyg-col-img':'be-r-image'; prevImg.style.borderRadius='6px'; wrap.appendChild(prevImg);
            self._upload(file,(realUrl,isFallback)=>{ b.url=realUrl; URL.revokeObjectURL(prev); uploadingEl.style.display='none'; onDone(); self._sync(); if(isFallback) console.warn('No uploadUrl set');},
            err=>{ prevImg.remove(); uploadingEl.style.display='none'; zone.style.display=''; urlRow.style.display=''; b.url=''; self._toast(err,'error'); });
        }

        fi.addEventListener('change',function(){ handleFile(this.files[0]); });
        zone.addEventListener('dragover',e=>{e.preventDefault();e.stopPropagation();zone.classList.add(compact?'wyg-col-img-zone--over':'wyg-img-drop--over');});
        zone.addEventListener('dragleave',()=>zone.classList.remove(compact?'wyg-col-img-zone--over':'wyg-img-drop--over'));
        zone.addEventListener('drop',e=>{ e.preventDefault(); e.stopPropagation(); zone.classList.remove(compact?'wyg-col-img-zone--over':'wyg-img-drop--over'); handleFile(e.dataTransfer.files[0]); });
        urlBtn.addEventListener('click',()=>{ const u=urlInp.value.trim(); if(u){b.url=u;onDone();self._sync();} });
        urlInp.addEventListener('keydown',e=>{ if(e.key==='Enter'){e.preventDefault();urlBtn.click();} });
        return wrap;
    };

    /* ─── Column sub-block ───────────────────────────────────────────── */
    BlockEditor.prototype._buildColBlockEl = function(cb, cbIdx, col, rerenderFn) {
        const self=this;
        const wrap=document.createElement('div'); wrap.className='wyg-col-block';
        const ctrl=document.createElement('div'); ctrl.className='wyg-col-block-ctrl';
        ctrl.innerHTML=`<button type="button" class="wyg-cb-btn wyg-cb-up" title="Up">↑</button><button type="button" class="wyg-cb-btn wyg-cb-dn" title="Down">↓</button><button type="button" class="wyg-cb-btn wyg-cb-del" title="Delete">✕</button>`;
        ctrl.querySelector('.wyg-cb-up').addEventListener('click', e=>{ e.stopPropagation(); if(cbIdx>0){[col.blocks[cbIdx-1],col.blocks[cbIdx]]=[col.blocks[cbIdx],col.blocks[cbIdx-1]];rerenderFn();self._sync();} });
        ctrl.querySelector('.wyg-cb-dn').addEventListener('click', e=>{ e.stopPropagation(); if(cbIdx<col.blocks.length-1){[col.blocks[cbIdx],col.blocks[cbIdx+1]]=[col.blocks[cbIdx+1],col.blocks[cbIdx]];rerenderFn();self._sync();} });
        ctrl.querySelector('.wyg-cb-del').addEventListener('click', e=>{ e.stopPropagation(); col.blocks.splice(cbIdx,1);rerenderFn();self._sync(); });
        const body=document.createElement('div'); body.className='wyg-col-block-body';
        this._fillColBody(cb, body, rerenderFn);
        wrap.appendChild(ctrl); wrap.appendChild(body);
        return wrap;
    };

    BlockEditor.prototype._fillColBody = function(cb, container, rerenderFn) {
        const self=this;
        container.innerHTML='';

        function colEditable(tag,cls,placeholder,text){
            const el=document.createElement(tag); el.className=(cls||'')+' wyg-editable';
            el.contentEditable='true'; el.spellcheck=true;
            el.dataset.placeholder=placeholder||''; if(text) el.textContent=text;
            el.addEventListener('paste',e=>{ e.preventDefault(); const t=(e.clipboardData||window.clipboardData).getData('text/plain'); document.execCommand('insertText',false,t); });
            return el;
        }

        switch(cb.type){
            case 'text': { const el=colEditable('p','wyg-col-text','Column text…',cb.text); el.addEventListener('input',()=>{cb.text=el.textContent;self._sync();}); container.appendChild(el); break; }
            case 'heading': {
                const lvlRow=document.createElement('div'); lvlRow.className='wyg-col-lvl-row';
                [1,2,3,4].forEach(n=>{ const btn=document.createElement('button'); btn.type='button'; btn.className='wyg-lvl-btn wyg-lvl-sm'+(cb.level==n?' wyg-lvl-active':''); btn.textContent=`H${n}`; btn.addEventListener('mousedown',e=>{e.preventDefault();cb.level=n;self._fillColBody(cb,container,rerenderFn);self._sync();}); lvlRow.appendChild(btn); });
                const el=colEditable(`h${cb.level||2}`,`wyg-col-heading wyg-col-h${cb.level||2}`,'Heading…',cb.text); el.addEventListener('input',()=>{cb.text=el.textContent;self._sync();});
                container.appendChild(lvlRow); container.appendChild(el); break;
            }
            case 'image': {
                if(cb.url){
                    const fig=document.createElement('figure'); fig.className='wyg-col-img-fig';
                    const img=document.createElement('img'); img.src=cb.url; img.alt=cb.alt||''; img.className='wyg-col-img';
                    img.onerror=()=>{ img.src='https://placehold.co/400x200/f3f4f6/9ca3af?text=Image'; };
                    const chg=document.createElement('button'); chg.type='button'; chg.className='wyg-img-change wyg-col-img-chg'; chg.textContent='⟳';
                    chg.addEventListener('click',()=>{cb.url='';cb.caption='';self._fillColBody(cb,container,rerenderFn);self._sync();});
                    const cap=colEditable('figcaption','wyg-col-cap','Caption…',cb.caption);
                    cap.addEventListener('input',()=>{cb.caption=cap.textContent;self._sync();});
                    fig.appendChild(img); fig.appendChild(chg); fig.appendChild(cap); container.appendChild(fig);
                } else {
                    const zone=document.createElement('div'); zone.className='wyg-col-img-zone';
                    const fi=document.createElement('input'); fi.type='file'; fi.accept='image/*'; fi.style.cssText='position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;';
                    zone.innerHTML=`<div class="wyg-col-img-zone-icon">🖼</div><p class="wyg-col-img-zone-text">Drop or <span>browse</span></p>`; zone.appendChild(fi);
                    const urlRow2=document.createElement('div'); urlRow2.className='wyg-col-url-row';
                    const urlInp2=document.createElement('input'); urlInp2.type='text'; urlInp2.placeholder='or paste URL'; urlInp2.className='wyg-col-url-inp';
                    const urlBtn2=document.createElement('button'); urlBtn2.type='button'; urlBtn2.textContent='Use'; urlBtn2.className='wyg-col-url-btn';
                    urlRow2.appendChild(urlInp2); urlRow2.appendChild(urlBtn2);
                    const uploadingEl2=document.createElement('div'); uploadingEl2.className='wyg-uploading'; uploadingEl2.style.display='none'; uploadingEl2.innerHTML='<span class="wyg-spin">⟳</span> Uploading…';
                    container.appendChild(zone); container.appendChild(urlRow2); container.appendChild(uploadingEl2);
                    function handleColFile(file){
                        if(!file||!file.type.startsWith('image/')) return;
                        const prev=URL.createObjectURL(file); cb.url=prev;
                        zone.style.display='none'; urlRow2.style.display='none'; uploadingEl2.style.display='flex';
                        const prevImg=document.createElement('img'); prevImg.src=prev; prevImg.className='wyg-col-img'; prevImg.style.borderRadius='6px'; container.appendChild(prevImg);
                        self._upload(file,(realUrl)=>{ cb.url=realUrl; URL.revokeObjectURL(prev); uploadingEl2.style.display='none'; self._fillColBody(cb,container,rerenderFn); self._sync(); }, err=>{ prevImg.remove(); uploadingEl2.style.display='none'; zone.style.display=''; urlRow2.style.display=''; cb.url=''; self._toast(err,'error'); });
                    }
                    fi.addEventListener('change',function(){handleColFile(this.files[0]);});
                    zone.addEventListener('dragover',e=>{e.preventDefault();e.stopPropagation();zone.classList.add('wyg-col-img-zone--over');});
                    zone.addEventListener('dragleave',()=>zone.classList.remove('wyg-col-img-zone--over'));
                    zone.addEventListener('drop',e=>{e.preventDefault();e.stopPropagation();zone.classList.remove('wyg-col-img-zone--over');handleColFile(e.dataTransfer.files[0]);});
                    urlBtn2.addEventListener('click',()=>{const u=urlInp2.value.trim();if(u){cb.url=u;self._fillColBody(cb,container,rerenderFn);self._sync();}});
                    urlInp2.addEventListener('keydown',e=>{if(e.key==='Enter'){e.preventDefault();urlBtn2.click();}});
                }
                break;
            }
            case 'button': {
                const btn=colEditable('a','wyg-col-btn-el wyg-col-btn--'+(cb.style||'primary'),'Button label…',cb.label);
                btn.href='javascript:void(0)'; btn.addEventListener('input',()=>{cb.label=btn.textContent;self._sync();});
                const row=document.createElement('div'); row.className='wyg-col-btn-row';
                const ui=document.createElement('input'); ui.type='text'; ui.value=cb.url||''; ui.placeholder='URL'; ui.className='wyg-col-url-inp'; ui.addEventListener('input',()=>{cb.url=ui.value;self._sync();});
                const sw=document.createElement('div'); sw.className='wyg-pills';
                [['primary','Pri'],['secondary','Sec'],['outline','Out']].forEach(([v,l])=>{ const sp=document.createElement('button'); sp.type='button'; sp.className='wyg-pill wyg-pill-sm'+(cb.style===v?' active':''); sp.textContent=l; sp.addEventListener('click',()=>{cb.style=v;btn.className=`wyg-col-btn-el wyg-col-btn--${v} wyg-editable`;sw.querySelectorAll('.wyg-pill').forEach(x=>x.classList.remove('active'));sp.classList.add('active');self._sync();}); sw.appendChild(sp); });
                row.appendChild(ui); row.appendChild(sw); container.appendChild(btn); container.appendChild(row); break;
            }
            case 'list': {
                const sr=document.createElement('div'); sr.className='wyg-pills wyg-col-list-style';
                [['unordered','• Bullet'],['ordered','1. Number']].forEach(([v,l])=>{ const sp=document.createElement('button'); sp.type='button'; sp.className='wyg-pill wyg-pill-sm'+(cb.style===v?' active':''); sp.textContent=l; sp.addEventListener('click',()=>{cb.style=v;self._fillColBody(cb,container,rerenderFn);self._sync();}); sr.appendChild(sp); });
                const tag=cb.style==='ordered'?'ol':'ul'; const listEl=document.createElement(tag); listEl.className=`wyg-col-list wyg-col-list--${cb.style}`;
                const renderCbItems=()=>{
                    listEl.innerHTML='';
                    cb.items.forEach((item,i)=>{
                        const li=document.createElement('li'); li.className='wyg-li';
                        const span=document.createElement('span'); span.className='wyg-editable'; span.contentEditable='true'; span.spellcheck=true; span.dataset.placeholder=`Item ${i+1}…`; span.textContent=item||'';
                        span.addEventListener('input',()=>{cb.items[i]=span.textContent;self._sync();});
                        span.addEventListener('paste',e=>{e.preventDefault();const t=(e.clipboardData||window.clipboardData).getData('text/plain');document.execCommand('insertText',false,t);});
                        span.addEventListener('keydown',e=>{
                            if(e.key==='Enter'){e.preventDefault();cb.items.splice(i+1,0,'');renderCbItems();self._sync();setTimeout(()=>{listEl.querySelectorAll('[contenteditable]')[i+1]?.focus();},0);}
                            if(e.key==='Backspace'&&span.textContent===''&&cb.items.length>1){e.preventDefault();cb.items.splice(i,1);renderCbItems();self._sync();}
                        });
                        const rm=document.createElement('button'); rm.type='button'; rm.className='wyg-li-rm'; rm.textContent='✕'; rm.addEventListener('click',()=>{if(cb.items.length>1){cb.items.splice(i,1);renderCbItems();self._sync();}});
                        li.appendChild(span); li.appendChild(rm); listEl.appendChild(li);
                    });
                };
                renderCbItems();
                const addBtn=document.createElement('button'); addBtn.type='button'; addBtn.className='wyg-mini-add-item'; addBtn.textContent='+ item';
                addBtn.addEventListener('click',()=>{cb.items.push('');renderCbItems();self._sync();setTimeout(()=>{listEl.querySelectorAll('[contenteditable]')[cb.items.length-1]?.focus();},0);});
                container.appendChild(sr); container.appendChild(listEl); container.appendChild(addBtn); break;
            }
            case 'callout': {
                const vr=document.createElement('div'); vr.className='wyg-pills wyg-col-var-row';
                const box=document.createElement('div'); box.className=`wyg-col-callout wyg-col-callout--${cb.variant||'info'}`;
                [['info','ℹ'],['warning','⚠'],['success','✓'],['danger','✕']].forEach(([v,l])=>{ const vb=document.createElement('button'); vb.type='button'; vb.className='wyg-pill wyg-pill-sm'+(cb.variant===v?' active':''); vb.textContent=l; vb.addEventListener('click',()=>{cb.variant=v;box.className=`wyg-col-callout wyg-col-callout--${v}`;vr.querySelectorAll('.wyg-pill').forEach(x=>x.classList.remove('active'));vb.classList.add('active');self._sync();}); vr.appendChild(vb); });
                const ie=colEditable('span','','💡',cb.icon||'💡'); ie.style.cssText='flex-shrink:0;min-width:22px;font-size:16px;'; ie.addEventListener('input',()=>{cb.icon=ie.textContent;self._sync();}); ie.addEventListener('keydown',e=>{if(e.key==='Enter')e.preventDefault();});
                const te=colEditable('span','','Message…',cb.text); te.style.flex='1'; te.addEventListener('input',()=>{cb.text=te.textContent;self._sync();});
                box.appendChild(ie); box.appendChild(te); container.appendChild(vr); container.appendChild(box); break;
            }
        }
    };

    /* ─── Global listeners ───────────────────────────────────────────── */
    BlockEditor.prototype._attachGlobalListeners = function() {
        /* Close slash on click outside */
        document.addEventListener('click', e=>{
            if (!this._slashEl?.contains(e.target) && !this.canvas.contains(e.target)) this._closeSlash();
        });
        /* Ctrl+Z global */
        this.canvas.addEventListener('keydown', e=>{
            if ((e.ctrlKey||e.metaKey)&&e.key==='z'&&!e.shiftKey) {
                /* Let native undo run first; if field is empty do block-level undo */
                setTimeout(()=>{ const active=document.activeElement; if(active&&active.contentEditable==='true'&&!active.textContent.trim()) this._undo(); },0);
            }
        });
        /* Click on canvas background → focus last block */
        this.canvas.addEventListener('click', e=>{
            if (e.target===this.canvas&&this.blocks.length) this._focusBlock(this.blocks[this.blocks.length-1].id);
        });
        /* Push history on every input (debounced) */
        this.canvas.addEventListener('input', ()=>{
            clearTimeout(this._historyTimer);
            this._historyTimer=setTimeout(()=>this._pushHistory(),800);
        });
    };

    /* ─── Mutations ───────────────────────────────────────────────────── */
    BlockEditor.prototype._reRenderBlock = function(b) {
        const el=this.canvas.querySelector(`[data-id="${b.id}"]`);
        if (el) el.replaceWith(this._buildBlockEl(b));
        this._sync();
    };
    BlockEditor.prototype._addBlock = function(type) {
        this._pushHistory();
        const b=this._makeBlock(type); this.blocks.push(b); this._render();
        setTimeout(()=>this._focusBlock(b.id),60);
    };
    BlockEditor.prototype._delete = function(id, focusPrev) {
        const idx=this.blocks.findIndex(b=>b.id===id);
        this.blocks=this.blocks.filter(b=>b.id!==id);
        this._render();
        if (focusPrev&&idx>0) setTimeout(()=>this._focusBlock(this.blocks[idx-1]?.id),0);
    };
    BlockEditor.prototype._move = function(id,dir) {
        const i=this.blocks.findIndex(b=>b.id===id),j=i+dir;
        if(j<0||j>=this.blocks.length) return;
        this._pushHistory();
        [this.blocks[i],this.blocks[j]]=[this.blocks[j],this.blocks[i]]; this._render();
        setTimeout(()=>this._focusBlock(id),0);
    };
    BlockEditor.prototype._duplicate = function(id) {
        this._pushHistory();
        const b=this.blocks.find(x=>x.id===id);
        const dup=Object.assign(JSON.parse(JSON.stringify(b)),{id:uid()});
        this.blocks.splice(this.blocks.findIndex(x=>x.id===id)+1,0,dup);
        this._render(); setTimeout(()=>this._focusBlock(dup.id),0);
    };
    BlockEditor.prototype._reorder = function(srcId,tgtId) {
        this._pushHistory();
        const si=this.blocks.findIndex(b=>b.id===srcId),ti=this.blocks.findIndex(b=>b.id===tgtId);
        const[r]=this.blocks.splice(si,1); this.blocks.splice(ti,0,r); this._render();
    };

    /* ─── Sync ────────────────────────────────────────────────────────── */
    BlockEditor.prototype._sync = function() {
        const clean=this.blocks.map(b=>{
            const c=Object.assign({},b); delete c.id;
            if(c.type==='columns'&&c.cols) c.cols=c.cols.map(col=>({blocks:(col.blocks||[]).map(cb=>{const cc=Object.assign({},cb);delete cc.id;return cc;})}));
            return c;
        });
        this._hiddenBlocks.value=JSON.stringify(clean);
        this._hiddenHtml.value=clean.map(blockToHtml).join('\n');
    };

    /* ─── Styles ──────────────────────────────────────────────────────── */
    BlockEditor.prototype._injectStyles = function() {
        if (document.getElementById('wyg-styles')) return;
        const s=document.createElement('style'); s.id='wyg-styles';
        s.textContent=`
/* ══════════════════════════════════════════════
   BlockEditor v6 — Word-like document editor
   ══════════════════════════════════════════════ */

.wyg-shell { font-family:'Georgia','Times New Roman',serif; background:#fff; border:1px solid #e5e7eb; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 16px rgba(0,0,0,.04); }

/* Toolbar */
.wyg-toolbar { display:flex; flex-wrap:wrap; gap:3px; padding:6px 14px; background:#f9fafb; border-bottom:1px solid #e5e7eb; border-radius:4px 4px 0 0; position:sticky; top:0; z-index:100; }
.wyg-tb-label { font-size:10px; font-weight:600; letter-spacing:.06em; color:#9ca3af; align-self:center; margin-right:4px; text-transform:uppercase; font-family:-apple-system,sans-serif; }
.wyg-tb-btn { padding:3px 9px; font-size:12px; font-weight:500; background:transparent; border:1px solid transparent; border-radius:4px; cursor:pointer; color:#374151; transition:background .1s,border-color .1s; white-space:nowrap; font-family:-apple-system,sans-serif; line-height:1.6; }
.wyg-tb-btn:hover { background:#fff; border-color:#d1d5db; color:#111827; }
.wyg-tb-btn:active { background:#f3f4f6; transform:scale(.98); }

/* Document canvas */
.wyg-canvas { padding:40px 48px 60px; min-height:200px; background:#fff; border-radius:0 0 4px 4px; color:#1a1a1a; line-height:1.8; }
.wyg-empty { text-align:center; padding:60px 0; color:#9ca3af; font-size:15px; font-style:italic; font-family:'Georgia',serif; }
.wyg-empty kbd { font-family:-apple-system,sans-serif; background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:1px 6px; font-size:12px; color:#374151; font-style:normal; box-shadow:0 1px 0 #d1d5db; }

/* Block wrapper */
.wyg-block { position:relative; border-radius:3px; margin:0 -14px; padding:2px 14px 2px 14px; transition:background .12s; }
.wyg-block:hover { background:rgba(99,102,241,.025); }
.wyg-block.wyg-dragging { opacity:.3; }
.wyg-block.wyg-drop-target { border-top:2px solid #6366f1; }
.wyg-block-body { position:relative; }

/* Left controls */
.wyg-ctrl { position:absolute; left:0; top:-26px; transform:none; display:flex; flex-direction:row; gap:1px; opacity:0; transition:opacity .15s; pointer-events:none; z-index:20; background:#fff; border:1px solid #e5e7eb; border-radius:5px; box-shadow:0 2px 6px rgba(0,0,0,.08); padding:2px; }
.wyg-block:hover .wyg-ctrl { opacity:1; pointer-events:auto; }
.wyg-ctrl-btn { width:22px; height:22px; display:flex; align-items:center; justify-content:center; background:#fff; border:1px solid #e5e7eb; border-radius:3px; cursor:pointer; font-size:10px; color:#9ca3af; transition:all .1s; padding:0; box-shadow:0 1px 2px rgba(0,0,0,.04); }
.wyg-ctrl-btn:hover { background:#f9fafb; color:#374151; border-color:#9ca3af; }
.wyg-del:hover { background:#fef2f2 !important; color:#dc2626 !important; border-color:#fca5a5 !important; }
.wyg-handle { cursor:grab; } .wyg-handle:active { cursor:grabbing; }

/* Editable — seamless, Word-like */
.wyg-editable { outline:none; display:inline-block; width:100%; caret-color:#6366f1; border-radius:2px; transition:background .1s; }
.wyg-editable:focus { background:rgba(99,102,241,.02); }
.wyg-editable:empty::before { content:attr(data-placeholder); color:#d1d5db; pointer-events:none; font-style:italic; }

/* === Document typography === */
.be-r-paragraph { font-family:'Georgia','Times New Roman',serif; font-size:16px; color:#1f2937; margin:0 0 .5em; display:block; line-height:1.8; }
.be-r-heading { font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif; font-weight:700; line-height:1.25; margin:1.4rem 0 .35rem; color:#111827; display:block; letter-spacing:-.015em; }
.be-r-h1 { font-size:2.1rem; }
.be-r-h2 { font-size:1.55rem; padding-bottom:.25rem; border-bottom:1px solid #f3f4f6; }
.be-r-h3 { font-size:1.2rem; }
.be-r-h4 { font-size:1rem; color:#374151; }
.wyg-heading-lvl { display:flex; gap:3px; margin-bottom:6px; }
.wyg-lvl-btn { padding:2px 7px; font-size:11px; font-weight:700; background:#f9fafb; border:1px solid #e5e7eb; border-radius:3px; cursor:pointer; color:#6b7280; transition:all .1s; font-family:-apple-system,sans-serif; }
.wyg-lvl-btn:hover { background:#f3f4f6; color:#111827; } .wyg-lvl-active { background:#6366f1 !important; color:#fff !important; border-color:#6366f1 !important; }

/* Image */
.be-r-figure { margin:.75rem 0; position:relative; }
.be-r-image { width:100%; max-height:420px; object-fit:cover; border-radius:6px; display:block; box-shadow:0 1px 4px rgba(0,0,0,.08); }
.be-r-caption { font-size:13px; color:#9ca3af; text-align:center; margin-top:8px; display:block; min-height:20px; font-style:italic; font-family:'Georgia',serif; }
.wyg-image-figure { position:relative; }
.wyg-img-change { position:absolute; top:10px; right:10px; background:rgba(17,24,39,.6); color:#fff; border:none; border-radius:5px; padding:5px 12px; font-size:12px; font-weight:500; cursor:pointer; opacity:0; transition:opacity .15s,background .15s; font-family:-apple-system,sans-serif; }
.wyg-image-figure:hover .wyg-img-change { opacity:1; } .wyg-img-change:hover { background:#dc2626; }
.wyg-img-drop { position:relative; border:1.5px dashed #e5e7eb; border-radius:8px; padding:36px 20px; text-align:center; cursor:pointer; background:#fafafa; transition:all .2s; overflow:hidden; }
.wyg-img-drop:hover,.wyg-img-drop--over { border-color:#6366f1; background:#faf9ff; }
.wyg-img-drop-icon { width:52px; height:52px; margin:0 auto 12px; background:#ede9fe; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#6366f1; transition:all .2s; }
.wyg-img-drop:hover .wyg-img-drop-icon,.wyg-img-drop--over .wyg-img-drop-icon { background:#6366f1; color:#fff; }
.wyg-img-drop-title { font-size:14px; font-weight:600; color:#374151; margin:0 0 4px; font-family:-apple-system,sans-serif; } .wyg-img-drop-title span { color:#6366f1; text-decoration:underline; }
.wyg-img-drop-sub { font-size:12px; color:#9ca3af; margin:0; font-family:-apple-system,sans-serif; }
.wyg-img-url-row { display:flex; gap:6px; margin-top:10px; }

/* Inputs / buttons shared */
.wyg-option-input { flex:1; border:1px solid #e5e7eb; border-radius:5px; padding:7px 10px; font-size:13px; outline:none; transition:border-color .15s,box-shadow .15s; font-family:-apple-system,sans-serif; background:#fff; }
.wyg-option-input:focus { border-color:#6366f1; box-shadow:0 0 0 3px rgba(99,102,241,.08); }
.wyg-url-btn { padding:7px 14px; background:#6366f1; color:#fff; border:none; border-radius:5px; font-size:13px; font-weight:500; cursor:pointer; white-space:nowrap; font-family:-apple-system,sans-serif; transition:background .15s; }
.wyg-url-btn:hover { background:#4f46e5; }

/* Pill group */
.wyg-pills { display:flex; gap:4px; flex-wrap:wrap; }
.wyg-pill { padding:4px 10px; font-size:11px; font-weight:600; background:#f9fafb; border:1px solid #e5e7eb; border-radius:20px; cursor:pointer; color:#6b7280; transition:all .12s; font-family:-apple-system,sans-serif; }
.wyg-pill:hover { border-color:#9ca3af; color:#111827; } .wyg-pill.active { background:#6366f1; color:#fff; border-color:#6366f1; }
.wyg-pill-sm { padding:2px 7px; font-size:10px; }

/* Button block */
.be-r-btn-wrap { margin:.75rem 0; }
.be-r-btn { display:inline-block; padding:10px 24px; border-radius:6px; font-size:14px; font-weight:600; text-decoration:none; cursor:text; font-family:-apple-system,sans-serif; }
.be-r-btn--primary { background:#5aab1f; color:#fff; } .be-r-btn--secondary { background:#f3f4f6; color:#374151; } .be-r-btn--outline { border:2px solid #5aab1f; color:#5aab1f; background:transparent; }
.wyg-btn-wrap { display:flex; flex-direction:column; gap:8px; }
.wyg-btn-opts { display:flex; gap:8px; align-items:center; flex-wrap:wrap; padding:8px 10px; background:#f9fafb; border:1px dashed #e5e7eb; border-radius:6px; }

/* Divider */
.be-r-divider { border:none; border-top:1px solid #e5e7eb; margin:1.5rem 0; }

/* Callout */
.be-r-callout { display:flex; align-items:flex-start; gap:10px; padding:14px 16px; border-radius:6px; margin:.75rem 0; font-size:14px; font-family:-apple-system,sans-serif; }
.be-r-callout--info    { background:#eff6ff; border-left:3px solid #3b82f6; color:#1e40af; }
.be-r-callout--warning { background:#fffbeb; border-left:3px solid #f59e0b; color:#92400e; }
.be-r-callout--success { background:#f0fdf4; border-left:3px solid #22c55e; color:#166534; }
.be-r-callout--danger  { background:#fef2f2; border-left:3px solid #ef4444; color:#991b1b; }
.be-r-callout-icon { font-size:17px; line-height:1.5; flex-shrink:0; }
.be-r-callout-text { line-height:1.6; flex:1; }
.wyg-callout-variants { margin-bottom:6px; }

/* List */
.be-r-list { margin:.5rem 0 .75rem 1.4rem; font-size:16px; color:#1f2937; font-family:'Georgia',serif; }
.be-r-list li { margin-bottom:4px; line-height:1.7; } .be-r-list--unordered { list-style-type:disc; } .be-r-list--ordered { list-style-type:decimal; }
.wyg-li { display:flex; align-items:center; gap:5px; list-style:inherit; }
.wyg-li-rm { background:none; border:none; color:#d1d5db; cursor:pointer; font-size:10px; padding:0 2px; opacity:0; transition:all .12s; flex-shrink:0; }
.wyg-li:hover .wyg-li-rm { opacity:1; } .wyg-li-rm:hover { color:#dc2626; }
.wyg-list-style-row { margin-bottom:6px; }
.wyg-add-item-btn { margin-top:4px; padding:3px 10px; font-size:12px; font-weight:500; background:none; border:1px dashed #e5e7eb; border-radius:4px; cursor:pointer; color:#9ca3af; transition:all .15s; width:100%; text-align:left; font-family:-apple-system,sans-serif; }
.wyg-add-item-btn:hover { border-color:#6366f1; color:#6366f1; }

/* Quote */
.be-r-quote { border-left:3px solid #d1d5db; margin:1rem 0; padding:8px 20px; }
.be-r-quote p { font-size:17px; font-style:italic; color:#374151; margin:0 0 4px; display:block; min-height:24px; font-family:'Georgia',serif; line-height:1.7; }
.be-r-cite { font-size:13px; color:#9ca3af; font-style:normal; display:block; min-height:18px; font-family:-apple-system,sans-serif; }

/* Spacer */
.wyg-spacer-vis { background:repeating-linear-gradient(45deg,#f9fafb,#f9fafb 4px,#f3f4f6 4px,#f3f4f6 8px); border:1px dashed #e5e7eb; border-radius:4px; min-height:8px; transition:height .15s; }
.wyg-spacer-wrap { display:flex; flex-direction:column; gap:6px; }
.wyg-spacer-ctrl { display:flex; align-items:center; gap:8px; font-size:12px; color:#9ca3af; font-family:-apple-system,sans-serif; }
.wyg-spacer-lbl { font-weight:700; color:#6366f1; min-width:36px; } .wyg-range { flex:1; accent-color:#6366f1; }

/* ── Video block ── */
.wyg-video-wrap { margin:.5rem 0; }
.wyg-video-ratio { position:relative; padding-bottom:56.25%; height:0; overflow:hidden; border-radius:8px; background:#000; box-shadow:0 2px 8px rgba(0,0,0,.12); }
.wyg-video-ratio iframe { position:absolute; top:0; left:0; width:100%; height:100%; border:0; }
.wyg-video-ratio::after { content:''; position:absolute; inset:0; z-index:1; cursor:default; }
.wyg-video-badge { position:absolute; bottom:10px; left:10px; background:rgba(0,0,0,.55); color:#fff; font-size:11px; font-weight:600; padding:3px 9px; border-radius:20px; font-family:-apple-system,sans-serif; pointer-events:none; letter-spacing:.03em; }
.wyg-video-zone { border:1.5px dashed #e5e7eb; border-radius:8px; padding:32px 20px 22px; text-align:center; background:#fafafa; transition:all .2s; }
.wyg-video-zone:hover { border-color:#6366f1; background:#faf9ff; }
.wyg-video-zone-icon { width:52px; height:52px; margin:0 auto 12px; background:#ede9fe; border-radius:50%; display:flex; align-items:center; justify-content:center; color:#6366f1; }
.wyg-video-zone-title { font-size:14px; font-weight:600; color:#374151; margin:0 0 3px; font-family:-apple-system,sans-serif; }
.wyg-video-zone-sub { font-size:12px; color:#9ca3af; margin:0 0 14px; font-family:-apple-system,sans-serif; }

/* Uploading */
.wyg-uploading { display:flex; align-items:center; gap:8px; padding:10px 14px; background:#faf9ff; border:1px solid #e0e7ff; border-radius:6px; font-size:13px; color:#6366f1; font-weight:500; font-family:-apple-system,sans-serif; margin-top:6px; }
.wyg-spin { display:inline-block; animation:wyg-spin .7s linear infinite; }
@keyframes wyg-spin { to { transform:rotate(360deg); } }

/* Toast */
.wyg-toast { position:fixed; bottom:24px; right:24px; z-index:99999; padding:10px 16px; border-radius:6px; font-size:13px; font-weight:500; box-shadow:0 4px 16px rgba(0,0,0,.1); transform:translateY(8px); opacity:0; transition:all .22s; pointer-events:none; font-family:-apple-system,sans-serif; }
.wyg-toast--in { transform:translateY(0); opacity:1; }
.wyg-toast--error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
.wyg-toast--info  { background:#eff6ff; color:#1e40af; border:1px solid #bfdbfe; }

/* ── Slash command palette ── */
.wyg-slash { position:absolute; z-index:9999; background:#fff; border:1px solid #e5e7eb; border-radius:8px; box-shadow:0 8px 24px rgba(0,0,0,.12); width:260px; max-height:320px; overflow-y:auto; }
.wyg-slash-item { display:flex; align-items:center; gap:10px; padding:8px 12px; cursor:pointer; transition:background .1s; border-radius:4px; }
.wyg-slash-item:hover,.wyg-slash-active { background:#f5f3ff; }
.wyg-slash-icon { font-size:16px; width:24px; text-align:center; flex-shrink:0; }
.wyg-slash-label { font-size:13px; font-weight:600; color:#111827; font-family:-apple-system,sans-serif; flex:1; }
.wyg-slash-desc { font-size:11px; color:#9ca3af; font-family:-apple-system,sans-serif; }

/* ── Rich columns ── */
.wyg-col-count-row { display:flex; align-items:center; gap:6px; margin-bottom:10px; font-family:-apple-system,sans-serif; }
.wyg-col-count-label { font-size:10px; font-weight:600; color:#9ca3af; text-transform:uppercase; letter-spacing:.06em; }
.wyg-col-count-btn { width:26px; height:26px; display:flex; align-items:center; justify-content:center; background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; cursor:pointer; font-size:12px; font-weight:700; color:#6b7280; transition:all .12s; }
.wyg-col-count-btn:hover { background:#f3f4f6; } .wyg-col-count-btn.active { background:#6366f1; color:#fff; border-color:#6366f1; }
.wyg-rich-cols-grid { display:grid; gap:14px; }
.wyg-rich-col { background:#fff; border:1px solid #e5e7eb; border-radius:6px; overflow:hidden; transition:border-color .15s,box-shadow .15s; }
.wyg-rich-col:hover { border-color:#c7d2fe; box-shadow:0 0 0 3px rgba(99,102,241,.06); }
.wyg-mini-canvas { padding:10px 10px 4px; min-height:56px; }
.wyg-mini-empty { text-align:center; font-size:12px; color:#d1d5db; font-style:italic; padding:16px 0; font-family:'Georgia',serif; }
.wyg-mini-toolbar { display:flex; flex-wrap:wrap; align-items:center; gap:3px; padding:5px 8px; background:#f9fafb; border-top:1px solid #f3f4f6; }
.wyg-mini-tb-label { font-size:9px; font-weight:700; letter-spacing:.08em; color:#9ca3af; margin-right:3px; text-transform:uppercase; font-family:-apple-system,sans-serif; }
.wyg-mini-tb-btn { padding:2px 7px; font-size:10px; font-weight:500; background:#fff; border:1px solid #e5e7eb; border-radius:3px; cursor:pointer; color:#6b7280; transition:all .1s; white-space:nowrap; font-family:-apple-system,sans-serif; }
.wyg-mini-tb-btn:hover { background:#ede9fe; border-color:#c4b5fd; color:#4f46e5; }
.wyg-col-block { position:relative; padding:4px 6px 4px 22px; border-radius:4px; margin-bottom:3px; transition:background .1s; }
.wyg-col-block:hover { background:#fafafa; }
.wyg-col-block-ctrl { position:absolute; left:2px; top:50%; transform:translateY(-50%); display:flex; flex-direction:column; gap:1px; opacity:0; transition:opacity .1s; }
.wyg-col-block:hover .wyg-col-block-ctrl { opacity:1; }
.wyg-cb-btn { width:18px; height:18px; display:flex; align-items:center; justify-content:center; background:#fff; border:1px solid #e5e7eb; border-radius:2px; cursor:pointer; font-size:8px; color:#9ca3af; padding:0; transition:all .1s; }
.wyg-cb-btn:hover { background:#f3f4f6; color:#374151; } .wyg-cb-del:hover { background:#fef2f2 !important; color:#dc2626 !important; }
.wyg-col-block-body { min-width:0; }
.wyg-col-text { font-size:14px; color:#374151; margin:0; line-height:1.65; display:block; font-family:'Georgia',serif; }
.wyg-col-heading { font-weight:700; color:#111827; margin:0 0 3px; display:block; line-height:1.25; font-family:-apple-system,sans-serif; }
.wyg-col-h1 { font-size:1.35rem; } .wyg-col-h2 { font-size:1.1rem; } .wyg-col-h3 { font-size:.95rem; } .wyg-col-h4 { font-size:.875rem; }
.wyg-col-lvl-row { display:flex; gap:2px; margin-bottom:4px; } .wyg-lvl-sm { padding:1px 5px !important; font-size:9px !important; }
.wyg-col-img-fig { position:relative; margin:0 0 4px; }
.wyg-col-img { width:100%; border-radius:6px; display:block; object-fit:cover; max-height:220px; box-shadow:0 1px 3px rgba(0,0,0,.08); }
.wyg-col-img-chg { font-size:11px !important; padding:3px 8px !important; top:6px !important; right:6px !important; }
.wyg-col-cap { font-size:12px; color:#9ca3af; text-align:center; margin-top:4px; display:block; min-height:16px; font-style:italic; font-family:'Georgia',serif; }
.wyg-col-img-zone { position:relative; border:1.5px dashed #e5e7eb; border-radius:6px; padding:16px; text-align:center; cursor:pointer; background:#fafafa; transition:all .18s; overflow:hidden; }
.wyg-col-img-zone:hover,.wyg-col-img-zone--over { border-color:#6366f1; background:#faf9ff; }
.wyg-col-img-zone-icon { font-size:20px; display:block; margin-bottom:4px; opacity:.4; }
.wyg-col-img-zone-text { font-size:12px; color:#6b7280; margin:0; font-family:-apple-system,sans-serif; } .wyg-col-img-zone-text span { color:#6366f1; text-decoration:underline; }
.wyg-col-url-row { display:flex; gap:4px; margin-top:6px; }
.wyg-col-url-inp { flex:1; border:1px solid #e5e7eb; border-radius:4px; padding:5px 8px; font-size:12px; outline:none; font-family:-apple-system,sans-serif; background:#fff; }
.wyg-col-url-inp:focus { border-color:#6366f1; }
.wyg-col-url-btn { padding:5px 10px; background:#6366f1; color:#fff; border:none; border-radius:4px; font-size:11px; font-weight:500; cursor:pointer; font-family:-apple-system,sans-serif; }
.wyg-col-btn-el { display:inline-block; padding:7px 16px; border-radius:5px; font-size:13px; font-weight:600; text-decoration:none; cursor:text; margin-bottom:5px; font-family:-apple-system,sans-serif; }
.wyg-col-btn--primary { background:#5aab1f; color:#fff; } .wyg-col-btn--secondary { background:#f3f4f6; color:#374151; } .wyg-col-btn--outline { border:2px solid #5aab1f; color:#5aab1f; background:transparent; }
.wyg-col-btn-row { display:flex; gap:5px; align-items:center; flex-wrap:wrap; margin-top:4px; }
.wyg-col-list { margin:.2rem 0 .3rem 1rem; font-size:13px; color:#374151; font-family:'Georgia',serif; }
.wyg-col-list--unordered { list-style-type:disc; } .wyg-col-list--ordered { list-style-type:decimal; }
.wyg-col-list-style { margin-bottom:5px; }
.wyg-mini-add-item { padding:2px 8px; font-size:11px; font-weight:500; background:none; border:1px dashed #e5e7eb; border-radius:3px; cursor:pointer; color:#9ca3af; transition:all .15s; font-family:-apple-system,sans-serif; }
.wyg-mini-add-item:hover { border-color:#6366f1; color:#6366f1; }
.wyg-col-callout { display:flex; align-items:flex-start; gap:8px; padding:9px 11px; border-radius:5px; font-size:13px; font-family:-apple-system,sans-serif; }
.wyg-col-callout--info    { background:#eff6ff; border-left:3px solid #3b82f6; color:#1e40af; }
.wyg-col-callout--warning { background:#fffbeb; border-left:3px solid #f59e0b; color:#92400e; }
.wyg-col-callout--success { background:#f0fdf4; border-left:3px solid #22c55e; color:#166534; }
.wyg-col-callout--danger  { background:#fef2f2; border-left:3px solid #ef4444; color:#991b1b; }
.wyg-col-var-row { margin-bottom:5px; }

@media (max-width:700px) {
    .wyg-canvas { padding:20px 16px 40px; }
    .wyg-block { padding-left:10px; margin:0; }
    .wyg-ctrl { display:none; }
    .wyg-rich-cols-grid { grid-template-columns:1fr !important; }
}
        `;
        document.head.appendChild(s);
    };

    global.BlockEditor = BlockEditor;
    global.blockToHtml = blockToHtml;

}(window));