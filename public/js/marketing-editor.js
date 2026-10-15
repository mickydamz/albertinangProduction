(() => {
 document.querySelectorAll('[data-promotion-editor]').forEach(form => {
  const editor=form.querySelector('[data-rich-message]');
  let range;
  const remember=()=>{const selection=window.getSelection(); if(selection.rangeCount && editor.contains(selection.anchorNode)) range=selection.getRangeAt(0).cloneRange();};
  const restore=()=>{editor.focus(); if(range){const selection=window.getSelection();selection.removeAllRanges();selection.addRange(range);}};
  ['keyup','mouseup','input'].forEach(event=>editor.addEventListener(event,remember));
  form.querySelectorAll('[data-command]').forEach(button=>{
   button.addEventListener('mousedown',event=>event.preventDefault());
   button.addEventListener('click',()=>{restore();document.execCommand(button.dataset.command,false,button.dataset.value || null);remember();});
  });
  form.querySelector('[data-link]').addEventListener('click',()=>{const value=window.prompt('Enter a secure website link (https://)');if(!value)return;try{const url=new URL(value);if(url.protocol!=='https:')throw new Error();restore();document.execCommand('createLink',false,url.href);remember();}catch{window.alert('Use a complete https:// website address.');}});
  form.querySelector('[data-text-color]').addEventListener('input',event=>{restore();document.execCommand('foreColor',false,event.target.value);remember();});
  editor.addEventListener('paste',event=>{event.preventDefault();document.execCommand('insertText',false,event.clipboardData.getData('text/plain'));});
  form.addEventListener('submit',event=>{
   if(!editor.innerText.trim()){event.preventDefault();editor.focus();window.alert('Write a promotion message.');return;}
   form.querySelector('[data-html-output]').value=editor.innerHTML;
  });
  const input=form.querySelector('[data-image-input]');
  input.addEventListener('change',()=>{
   const target=form.querySelector('[data-image-previews]');target.replaceChildren();
   if(input.files.length>4){window.alert('Choose up to four pictures.');input.value='';return;}
   [...input.files].forEach(file=>{
    if(!['image/jpeg','image/png'].includes(file.type)||file.size>3*1024*1024){input.value='';target.replaceChildren();window.alert('Use JPG or PNG pictures up to 3 MB each.');return;}
    const column=document.createElement('div');column.className='col-sm-3 mb-2';
    const img=document.createElement('img');img.alt=file.name;img.style='width:100%;height:120px;object-fit:contain';
    const url=URL.createObjectURL(file);img.src=url;img.onload=()=>URL.revokeObjectURL(url);column.append(img);target.append(column);
   });
  });
 });
 document.querySelectorAll('[data-preview-width]').forEach(button=>button.addEventListener('click',()=>{const frame=document.querySelector('[data-email-preview]');frame.style.width=button.dataset.previewWidth==='mobile'?'375px':'100%';}));
})();
