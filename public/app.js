(function(){
  const qs = new URLSearchParams(location.search);
  const leadId = Number(qs.get('lead_id')||0);
  const API_BASE = '/api';

  const $ = s=>document.querySelector(s);
  const tbody = $('#tbl tbody');
  const sumEl = $('#sum'), totalEl = $('#total'), cntEl = $('#cnt'), wordsEl = $('#totalWords');
  const discountEl = $('#discount'), templateEl = $('#template');
  const genBtn = $('#gen'), linkA = $('#link');
  let quoteTimer = null, quoteSeq = 0;

  const fmt = n => (Number(n)||0).toLocaleString('ru-RU');
  function toast(msg){ const t=$('#toast'); t.textContent=msg; t.hidden=false; setTimeout(()=>t.hidden=true,2500); }

  function addRow(name='', unitPrice='', qty='1', discP=''){
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td class="num"><div class="ph">${'${row_num}'}</div></td>
      <td class="left">
        <div class="cell">
          <div class="ph">${'${услуга_название}'}</div>
          <input class="n" placeholder="Наименование" value="${name}">
        </div>
      </td>
      <td>
        <div class="cell">
          <div class="ph right">${'${row_qty}'}</div>
          <input class="q" type="number" min="1" step="1" value="${qty}">
        </div>
      </td>
      <td>
        <div class="cell">
          <div class="ph right">${'${row_price}'}</div>
          <input class="u" type="number" min="0" step="1" value="${unitPrice}">
        </div>
      </td>
      <td>
        <div class="cell">
          <div class="ph right">${'${row_discount}'}</div>
          <input class="d" type="number" min="0" step="0.01" value="${discP}">
        </div>
      </td>
      <td class="right">
        <div class="cell">
          <div class="ph right">${'${row_sum}'}</div>
          <div class="s">0</div>
        </div>
      </td>
      <td><button class="icon-btn danger" title="Удалить">✕</button></td>`;
    tr.querySelector('.icon-btn').onclick = ()=>{ tr.remove(); recalc(); };
    tr.querySelectorAll('input').forEach(i=> i.oninput = recalc);
    tbody.appendChild(tr);
    recalc();
  }

  function rowData(tr){
    const name = tr.querySelector('.n').value.trim();
    const qty  = Math.max(1, Number(tr.querySelector('.q').value||1));
    const unit = Math.max(0, Number(tr.querySelector('.u').value||0));
    const dp   = Math.max(0, Number(tr.querySelector('.d').value||0));
    return {name, qty, unit, dp};
  }

  function products(){
    const arr=[];
    tbody.querySelectorAll('tr').forEach(tr=>{
      const r = rowData(tr);
      if(r.name) arr.push({
        name: r.name,
        qty: r.qty,
        unit_price: r.unit,
        discount_percent: r.dp
      });
    });
    return arr;
  }

  function recalc(){
    let idx = 1;
    tbody.querySelectorAll('tr').forEach(tr=>{
      tr.querySelector('.num').textContent = idx++; 
    });
    cntEl.textContent = tbody.querySelectorAll('tr').length;
    linkA.style.display='none';
    scheduleQuote();
  }

  function scheduleQuote(){
    clearTimeout(quoteTimer);
    quoteTimer = setTimeout(loadQuote, 200);
  }

  async function loadQuote(){
    const seq = ++quoteSeq;
    try{
      const r = await fetch(API_BASE + '/quote.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({products: products(), discount: Number(discountEl.value||0)})
      });
      const j = await r.json();
      if(seq !== quoteSeq || !r.ok) return;
      renderQuote(j);
    }catch(_){}
  }

  function renderQuote(q){
    const rows = q.rows || [];
    tbody.querySelectorAll('tr').forEach((tr, i)=>{
      const row = rows[i] || {};
      tr.querySelector('.s').textContent = fmt(row.net_sum || 0);
    });
    cntEl.textContent = q.count || 0;
    sumEl.textContent = fmt(q.sum_gross || 0);
    totalEl.textContent = fmt(q.total || 0);
    wordsEl.textContent = q.total_words || 'ноль рублей';
  }

  $('#add').onclick = ()=> addRow();
  $('#gen').onclick = async ()=>{
    const body = {
      lead_id: leadId,
      template: templateEl.value,
      products: products(),
      discount: Number(discountEl.value||0)
    };
    if(!body.products.length){ toast('Добавьте хотя бы одну позицию'); return; }
    genBtn.disabled = true; genBtn.textContent = 'Генерирую…';
    try{
      const r = await fetch(API_BASE + '/generate.php', {
        method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body)
      });
      const text = await r.text(); let j=null;
      try{ j = JSON.parse(text); }catch(_){}
      if(!r.ok || !j || !j.url) throw new Error((j&&j.error) || ('HTTP '+r.status+' '+text.slice(0,120)));
      linkA.href = j.url; linkA.style.display='inline-block'; linkA.click();
      toast('Документ создан');
    }catch(e){ toast('Ошибка: '+e.message); }
    finally{ genBtn.disabled=false; genBtn.textContent='Сформировать'; }
  };
  discountEl.oninput = recalc; templateEl.onchange = ()=>{ linkA.style.display='none'; };

  (async function(){
    if(!leadId){ addRow(); return; }
    try{
      const r = await fetch(API_BASE + '/prefill.php?lead_id='+leadId);
      const j = await r.json();
      (j.products||[]).forEach(p=>{
        addRow(p.name||'', p.unit_price||p.price||0, p.qty||p.quantity||1, p.discount_percent||0);
      });
      discountEl.value = j.discount||0;
      templateEl.value = j.template||'order';
      if(!tbody.children.length) addRow();
    }catch(_){ addRow(); }
    recalc();
  })();
})();
