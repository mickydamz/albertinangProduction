@if(request()->is('checkout'))
@php($shopState = \App\Services\ShoppingLocation::state())
<button type="button" id="changeShoppingLocation" hidden>Choose state</button>
<dialog id="shoppingLocationDialog" aria-labelledby="shoppingLocationTitle" style="margin:auto;width:min(94vw,480px);max-height:90vh;overflow:auto;border:0;border-radius:18px;padding:26px;color:#20301a;box-shadow:0 20px 80px #0004">
 <h2 id="shoppingLocationTitle" style="font-size:23px;margin-bottom:10px">Choose your state</h2>
 <p>Browse all published products. Choose a state for delivery or collection preferences; collection availability and transfer charges appear at checkout.</p>
 <form id="shoppingLocationForm">
  <label for="shoppingState">State</label><select required id="shoppingState" style="display:block;width:100%;padding:12px;margin:6px 0 16px;border:1px solid #bdcbb5;border-radius:8px"></select>
  <p id="shoppingLocationError" role="alert" style="color:#a32d25;font-size:14px;margin-bottom:12px"></p>
  <button id="shoppingApply" type="submit" style="width:100%;padding:13px;background:#426b25;color:white;border:0;border-radius:9px;font-weight:700">Apply state</button>
  <button id="shoppingCancel" type="button" style="width:100%;padding:10px;background:transparent;border:0;margin-top:6px" >Continue browsing</button>
 </form>
</dialog>
<style>#shoppingLocationDialog::backdrop{background:rgba(16,28,13,.65);backdrop-filter:blur(3px)}</style>
<script>
(function(){
 const selected=@json($shopState?->id), required=false;
 window.shoppingState=@json($shopState ? ['id'=>$shopState->id,'name'=>$shopState->name] : null);
 const dialog=document.getElementById('shoppingLocationDialog'), state=document.getElementById('shoppingState'), error=document.getElementById('shoppingLocationError');
 function cart(){try{const c=JSON.parse(localStorage.getItem('shoppingCart')||'[]');return Array.isArray(c)?c:[];}catch(e){return [];}}
 async function open(){dialog.showModal();error.textContent='Loading states…';try{const res=await fetch('/shopping/locations',{headers:{Accept:'application/json'},cache:'no-store'});if(!res.ok)throw Error();const data=await res.json();state.replaceChildren(new Option('Choose a state',''));data.states.forEach(s=>state.add(new Option(s.name,String(s.id))));state.value=selected?String(selected):'';error.textContent='';}catch(e){error.textContent='Could not load states. Please try again.';}}
 async function apply(){
 error.textContent='';const button=document.getElementById('shoppingApply');button.disabled=true;
 try{const res=await fetch('/shopping/location',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify({state_id:Number(state.value),items:cart().map(i=>Number(i.id))})});const data=await res.json();if(!res.ok)throw Error(data.message||'Please choose a state.');
 localStorage.setItem('shoppingUnavailableIds',JSON.stringify(data.incompatible_ids||[]));
 ['pickupLocation','pickupLocationId','pickupPointId','pickupPointName','pickupPointAddress','deliveryStateId','deliveryStateName','deliveryLocationId','deliveryLocationName','deliveryFeeNgn','deliveryTruckFeeNgn','deliveryBaseFeeNgn'].forEach(k=>localStorage.removeItem(k));
 localStorage.setItem('shoppingLocationVersion',String(Date.now()));
 // Keep the current product, search, category, basket or checkout URL.
 location.reload();
 }catch(e){error.textContent=e.message||'State could not be changed. Please retry.';}finally{button.disabled=false;}}
 document.getElementById('changeShoppingLocation').onclick=open;
 document.getElementById('shoppingLocationForm').onsubmit=e=>{e.preventDefault();apply();};
 document.getElementById('shoppingCancel').onclick=()=>dialog.close();dialog.addEventListener('cancel',e=>{if(!selected&&required)e.preventDefault();});
 window.addEventListener('storage',e=>{if(e.key==='shoppingLocationVersion')location.reload();});
 try{const ids=JSON.parse(localStorage.getItem('shoppingUnavailableIds')||'[]'), bad=cart().filter(i=>ids.includes(Number(i.id)));if(bad.length){const note=document.createElement('p');note.setAttribute('role','alert');note.style.cssText='margin:0;padding:12px 20px;background:#fff1e8;color:#8a3419';note.textContent='Some basket products are unavailable in this state. Remove the marked items at checkout to proceed. ';const link=document.createElement('a');link.href='/checkout';link.textContent='Review basket';note.appendChild(link);document.getElementById('fulfillmentSection')?.prepend(note);}}catch(e){}
 if(!selected&&required)open();
})();
</script>

@endif
