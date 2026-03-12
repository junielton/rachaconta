<?php
// ============================================
// TCHURMINHA - Backend API (JSON persistence)
// ============================================
$DATA_FILE = __DIR__ . '/data.json';

function loadData($file) {
    if (!file_exists($file)) return null;
    $json = file_get_contents($file);
    return json_decode($json, true);
}

function saveData($file, $data) {
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

if (isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');

    if ($_GET['action'] === 'load') {
        $data = loadData($DATA_FILE);
        if ($data === null) {
            $data = [
                'people' => ['David', 'Malu', 'Diego', 'Josi', 'PRT', 'Juni'],
                'items' => []
            ];
            saveData($DATA_FILE, $data);
        }
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($_GET['action'] === 'save') {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input) {
            saveData($DATA_FILE, $input);
            echo json_encode(['ok' => true, 'hash' => md5_file($DATA_FILE)]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
        }
        exit;
    }

    // Lightweight check — returns only the hash of current data
    if ($_GET['action'] === 'poll') {
        if (file_exists($DATA_FILE)) {
            echo json_encode(['hash' => md5_file($DATA_FILE)]);
        } else {
            echo json_encode(['hash' => '']);
        }
        exit;
    }

    http_response_code(404);
    echo json_encode(['error' => 'Ação não encontrada']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tchurminha</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --bg: #111113;
    --surface: #19191c;
    --surface2: #222225;
    --border: #2c2c30;
    --border-subtle: #232327;
    --text: #ececef;
    --text2: #7e7e85;
    --text3: #5a5a60;
    --accent: #e8e8ec;
    --tint: #c4b5fd;
    --tint-dim: rgba(196,181,253,0.08);
    --tint-mid: rgba(196,181,253,0.15);
    --green: #34d399;
    --green-dim: rgba(52,211,153,0.1);
    --red: #f87171;
    --red-dim: rgba(248,113,113,0.1);
    --amber: #fbbf24;
    --radius: 8px;
    --font: 'DM Sans', -apple-system, sans-serif;
    --mono: 'JetBrains Mono', 'SF Mono', monospace;
  }

  * { margin: 0; padding: 0; box-sizing: border-box; }

  body {
    font-family: var(--font);
    background: var(--bg);
    color: var(--text);
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
  }

  /* === HEADER === */
  .header {
    border-bottom: 1px solid var(--border);
    padding: 20px 28px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    background: var(--surface);
  }
  .header-left { display: flex; align-items: center; gap: 14px; }
  .logo {
    width: 36px; height: 36px; border-radius: 10px;
    background: var(--tint-mid);
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; color: var(--tint);
    font-weight: 700; letter-spacing: -1px;
  }
  .header h1 {
    font-size: 18px; font-weight: 600; letter-spacing: -0.3px; color: var(--text);
  }
  .header h1 span {
    color: var(--text3); font-weight: 400; font-size: 14px; margin-left: 6px;
  }
  .save-status {
    font-size: 11px; color: var(--text3); display: flex; align-items: center; gap: 5px; margin-top: 2px;
  }
  .save-dot {
    width: 6px; height: 6px; border-radius: 50%; background: var(--green); display: inline-block;
    transition: background .2s;
  }
  .save-dot.pending { background: var(--amber); }
  .header-actions { display: flex; gap: 6px; flex-wrap: wrap; }

  /* === BUTTONS === */
  .btn {
    border: 1px solid var(--border); padding: 7px 14px; border-radius: 7px;
    font-size: 13px; font-weight: 500; cursor: pointer; display: inline-flex;
    align-items: center; gap: 6px; transition: all .15s; font-family: var(--font);
    background: var(--surface2); color: var(--text2);
  }
  .btn:hover { background: var(--border); color: var(--text); border-color: var(--text3); }
  .btn:active { transform: scale(0.98); }
  .btn-accent {
    background: var(--tint-dim); color: var(--tint); border-color: rgba(196,181,253,0.2);
  }
  .btn-accent:hover { background: var(--tint-mid); border-color: rgba(196,181,253,0.35); color: #ddd5fe; }
  .btn-green {
    background: var(--green-dim); color: var(--green); border-color: rgba(52,211,153,0.2);
  }
  .btn-green:hover { background: rgba(52,211,153,0.18); border-color: rgba(52,211,153,0.35); }
  .btn-sm { padding: 4px 10px; font-size: 12px; }
  .btn-icon {
    width: 30px; height: 30px; padding: 0; justify-content: center;
    border-radius: 6px; font-size: 14px;
  }

  /* === LAYOUT === */
  .container { padding: 24px 28px; max-width: 1400px; margin: 0 auto; }

  /* === PEOPLE === */
  .section-label {
    font-size: 11px; font-weight: 600; color: var(--text3);
    text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 10px;
  }
  .people-bar {
    display: flex; flex-wrap: wrap; gap: 6px; align-items: center;
    margin-bottom: 20px;
  }
  .chip {
    background: var(--surface2); border: 1px solid var(--border);
    border-radius: 6px; padding: 5px 10px 5px 12px; font-size: 13px;
    display: flex; align-items: center; gap: 8px; font-weight: 500;
    transition: border-color .15s;
  }
  .chip:hover { border-color: var(--text3); }
  .chip .remove-person {
    background: none; border: none; color: var(--text3); cursor: pointer;
    font-size: 14px; line-height: 1; padding: 2px; border-radius: 4px;
    transition: all .15s; display: flex; align-items: center; justify-content: center;
  }
  .chip .remove-person:hover { color: var(--red); background: var(--red-dim); }
  .add-person-input {
    background: transparent; border: 1px dashed var(--border);
    border-radius: 6px; padding: 5px 12px; color: var(--text);
    font-size: 13px; width: 120px; outline: none; font-family: var(--font);
  }
  .add-person-input::placeholder { color: var(--text3); }
  .add-person-input:focus { border-color: var(--tint); border-style: solid; }

  /* === TABLE === */
  .table-wrapper {
    border: 1px solid var(--border); border-radius: var(--radius);
    overflow: hidden;
  }
  .table-scroll { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; min-width: 600px; }
  thead th {
    background: var(--surface); padding: 10px 12px; text-align: center;
    font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.6px;
    color: var(--text3); border-bottom: 1px solid var(--border);
    position: sticky; top: 0; white-space: nowrap;
  }
  thead th:first-child { text-align: left; padding-left: 16px; min-width: 200px; }
  thead th:nth-child(2) { min-width: 100px; }

  tbody tr { border-bottom: 1px solid var(--border-subtle); transition: background .1s; }
  tbody tr:last-child { border-bottom: none; }
  tbody tr:hover { background: var(--tint-dim); }
  tbody td { padding: 8px 12px; text-align: center; vertical-align: middle; }
  tbody td:first-child { text-align: left; padding-left: 16px; }

  .item-name {
    background: transparent; border: none; color: var(--text); font-size: 13px;
    width: 100%; padding: 4px 0; font-family: var(--font); outline: none;
    border-bottom: 1px solid transparent; font-weight: 500;
  }
  .item-name::placeholder { color: var(--text3); font-weight: 400; }
  .item-name:focus { border-bottom-color: var(--tint); }

  .item-price {
    background: transparent; border: none; color: var(--green); font-size: 13px;
    width: 80px; text-align: center; padding: 4px; font-family: var(--mono);
    outline: none; border-bottom: 1px solid transparent; font-weight: 500;
  }
  .item-price:focus { border-bottom-color: var(--green); }

  /* Custom checkbox */
  .cb { display: none; }
  .cb-label {
    width: 20px; height: 20px; border: 1.5px solid var(--border);
    border-radius: 5px; display: inline-flex; align-items: center;
    justify-content: center; cursor: pointer; transition: all .15s;
    background: transparent; position: relative;
  }
  .cb-label:hover { border-color: var(--tint); background: var(--tint-dim); }
  .cb:checked + .cb-label {
    background: var(--tint); border-color: var(--tint);
  }
  .cb:checked + .cb-label::after {
    content: '';
    width: 5px; height: 9px;
    border: solid var(--bg); border-width: 0 2px 2px 0;
    transform: rotate(45deg); margin-top: -2px;
  }

  .th-person { display: flex; flex-direction: column; align-items: center; gap: 4px; }
  .th-btns { display: flex; gap: 2px; }
  .th-btn {
    background: none; border: 1px solid var(--border-subtle); color: var(--text3);
    cursor: pointer; border-radius: 3px; padding: 1px 5px; font-size: 9px;
    transition: all .15s; line-height: 1.2;
  }
  .th-btn:hover { border-color: var(--tint); color: var(--tint); }

  .div-val {
    font-family: var(--mono); font-size: 12px; font-weight: 600;
    color: var(--tint); background: var(--tint-dim);
    border-radius: 4px; padding: 2px 8px; display: inline-block;
  }
  .vid-val {
    font-family: var(--mono); font-size: 12px; font-weight: 600; color: var(--green);
  }
  .vid-val.zero { color: var(--text3); }

  .del-row {
    background: none; border: none; color: var(--text3); cursor: pointer;
    opacity: 0; font-size: 16px; transition: all .15s; padding: 4px;
    border-radius: 4px;
  }
  tbody tr:hover .del-row { opacity: 0.6; }
  .del-row:hover { opacity: 1 !important; color: var(--red); background: var(--red-dim); }

  /* === ACTION BAR === */
  .action-bar {
    display: flex; gap: 6px; flex-wrap: wrap; margin-top: 12px;
  }

  /* === PASTE ZONE === */
  .paste-zone {
    border: 1px dashed var(--border); border-radius: var(--radius);
    padding: 20px; margin-top: 16px; text-align: center;
    background: var(--surface);
  }
  .paste-zone p { color: var(--text2); font-size: 13px; }
  .paste-zone code {
    background: var(--surface2); padding: 2px 6px; border-radius: 4px;
    font-family: var(--mono); font-size: 12px;
  }
  .paste-zone textarea {
    width: 100%; max-width: 560px; height: 80px; margin-top: 12px;
    background: var(--surface2); border: 1px solid var(--border); border-radius: 6px;
    color: var(--text); padding: 12px; font-family: var(--mono); font-size: 12px;
    resize: vertical; outline: none;
  }
  .paste-zone textarea:focus { border-color: var(--tint); }

  /* === SUMMARY === */
  .summary {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 8px; margin-top: 24px;
  }
  .summary-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 16px; text-align: center;
  }
  .summary-card .name {
    font-size: 11px; color: var(--text3); text-transform: uppercase;
    letter-spacing: 0.5px; font-weight: 600;
  }
  .summary-card .total {
    font-family: var(--mono); font-size: 20px; font-weight: 600;
    color: var(--green); margin-top: 6px;
  }
  .summary-card-total {
    background: var(--surface2); border-color: var(--border);
  }
  .summary-card-total .name { color: var(--text2); }
  .summary-card-total .total { color: var(--text); }

  /* === TOAST === */
  .toast {
    position: fixed; bottom: 20px; right: 20px;
    background: var(--surface2); border: 1px solid var(--border);
    color: var(--text); padding: 10px 18px; border-radius: 8px;
    font-size: 13px; font-weight: 500; opacity: 0; transform: translateY(8px);
    transition: all .25s; z-index: 999; pointer-events: none;
    box-shadow: 0 8px 32px rgba(0,0,0,0.4);
  }
  .toast.show { opacity: 1; transform: translateY(0); }

  /* === EMPTY STATE === */
  .empty-state {
    text-align: center; padding: 48px 20px; color: var(--text3); font-size: 14px;
  }
  .empty-state strong { color: var(--text2); }

  /* === SYNC INDICATOR === */
  .sync-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; color: var(--text3); margin-left: 8px;
    padding: 2px 8px; border-radius: 4px;
    background: var(--surface2); border: 1px solid var(--border);
  }
  .sync-badge .pulse {
    width: 6px; height: 6px; border-radius: 50%; background: var(--green);
    animation: pulse 2s ease-in-out infinite;
  }
  .sync-badge.offline .pulse { background: var(--red); animation: none; }
  @keyframes pulse { 0%,100% { opacity: 1; } 50% { opacity: 0.3; } }

  /* === RESPONSIVE === */
  @media (max-width: 768px) {
    .header { padding: 14px 16px; }
    .header h1 { font-size: 16px; }
    .container { padding: 16px; }
    .summary { grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); }
    .summary-card .total { font-size: 16px; }
    .sync-badge { display: none; }
  }
</style>
</head>
<body>

<header class="header">
  <div class="header-left">
    <div class="logo">T</div>
    <div>
      <h1>Tchurminha <span>racha conta</span></h1>
      <div class="save-status">
        <span class="save-dot" id="saveDot"></span> <span id="saveText">Salvo</span>
        <span class="sync-badge" id="syncBadge"><span class="pulse"></span> ao vivo</span>
      </div>
    </div>
  </div>
  <div class="header-actions">
    <button class="btn" onclick="selectAll()">Marcar todos</button>
    <button class="btn" onclick="exportJSON()">Exportar</button>
    <button class="btn" onclick="document.getElementById('importFile').click()">Importar</button>
    <input type="file" id="importFile" accept=".json" style="display:none" onchange="importJSON(event)">
  </div>
</header>

<div class="container">
  <div class="section-label">Pessoas</div>
  <div class="people-bar" id="peopleChips"></div>

  <div class="table-wrapper">
    <div class="table-scroll">
      <table id="mainTable">
        <thead id="tableHead"></thead>
        <tbody id="tableBody"></tbody>
      </table>
    </div>
  </div>

  <div class="action-bar">
    <button class="btn btn-green" onclick="addItem()">+ Novo item</button>
    <button class="btn btn-accent" onclick="togglePaste()">Colar do Excel</button>
  </div>

  <div class="paste-zone" id="pasteZone" style="display:none">
    <p>Cole itens do Excel — formato: <code>Nome [tab] Preco</code> (uma linha por item)</p>
    <textarea id="pasteArea" placeholder="Coca-Cola&#9;8.50&#10;Pizza&#9;45.00&#10;Cerveja&#9;12.00"></textarea>
    <div style="margin-top:12px; display:flex; gap:6px; justify-content:center;">
      <button class="btn btn-green" onclick="processPaste()">Adicionar</button>
      <button class="btn" onclick="togglePaste()">Cancelar</button>
    </div>
  </div>

  <div class="summary" id="summaryCards"></div>
</div>

<div class="toast" id="toast"></div>

<script>
let state = { people: [], items: [] };
let saveTimeout = null;
let lastHash = '';       // tracks the server data version
let isSaving = false;    // prevents poll from overwriting during a save
const POLL_INTERVAL = 3000;

// === PERSISTENCE ===
async function loadState() {
  try {
    const res = await fetch('?action=load');
    const data = await res.json();
    if (data && data.people) {
      state = data;
      state.items.forEach(item => {
        while (item.checks.length < state.people.length) item.checks.push(false);
        if (item.checks.length > state.people.length) item.checks = item.checks.slice(0, state.people.length);
      });
    }
    // Get initial hash
    lastHash = await fetchHash();
    setSyncOnline(true);
  } catch (e) {
    const local = localStorage.getItem('tchurminha');
    if (local) state = JSON.parse(local);
    else state = { people: ['David','Malu','Diego','Josi','PRT','Juni'], items: [] };
    setSyncOnline(false);
  }
  render();
  startPolling();
}

function scheduleSave() {
  document.getElementById('saveDot').classList.add('pending');
  document.getElementById('saveText').textContent = 'Salvando...';
  clearTimeout(saveTimeout);
  saveTimeout = setTimeout(doSave, 600);
}

async function doSave() {
  isSaving = true;
  localStorage.setItem('tchurminha', JSON.stringify(state));
  try {
    const res = await fetch('?action=save', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(state)
    });
    const result = await res.json();
    if (result.hash) lastHash = result.hash;
    setSyncOnline(true);
  } catch (e) {
    setSyncOnline(false);
  }
  isSaving = false;
  document.getElementById('saveDot').classList.remove('pending');
  document.getElementById('saveText').textContent = 'Salvo';
}

// === REALTIME SYNC (polling) ===
async function fetchHash() {
  try {
    const res = await fetch('?action=poll');
    const data = await res.json();
    return data.hash || '';
  } catch (e) { return ''; }
}

function startPolling() {
  setInterval(async () => {
    if (isSaving) return; // don't poll while saving
    try {
      const hash = await fetchHash();
      if (hash && hash !== lastHash) {
        // Someone else changed the data — reload
        lastHash = hash;
        const res = await fetch('?action=load');
        const data = await res.json();
        if (data && data.people) {
          state = data;
          state.items.forEach(item => {
            while (item.checks.length < state.people.length) item.checks.push(false);
            if (item.checks.length > state.people.length) item.checks = item.checks.slice(0, state.people.length);
          });
          localStorage.setItem('tchurminha', JSON.stringify(state));
          render();
          toast('Atualizado por outro usuario');
        }
      }
      setSyncOnline(true);
    } catch (e) {
      setSyncOnline(false);
    }
  }, POLL_INTERVAL);
}

function setSyncOnline(online) {
  const badge = document.getElementById('syncBadge');
  if (online) {
    badge.classList.remove('offline');
    badge.innerHTML = '<span class="pulse"></span> ao vivo';
  } else {
    badge.classList.add('offline');
    badge.innerHTML = '<span class="pulse"></span> offline';
  }
}

// === RENDER ===
function render() { renderPeople(); renderTable(); renderSummary(); }

function renderPeople() {
  const c = document.getElementById('peopleChips');
  let h = '';
  state.people.forEach((p, i) => {
    h += `<div class="chip"><span>${esc(p)}</span>
      <button class="remove-person" onclick="removePerson(${i})" title="Remover">&times;</button></div>`;
  });
  h += `<input class="add-person-input" id="addPersonInput"
    placeholder="+ Adicionar" onkeydown="if(event.key==='Enter'){addPerson(this.value);this.value='';}">`;
  c.innerHTML = h;
}

function renderTable() {
  let head = '<tr><th>Item</th><th>Preco</th>';
  state.people.forEach((p,i) => {
    head += `<th><div class="th-person"><span>${esc(p)}</span>
      <div class="th-btns">
        <button class="th-btn" onclick="colSelectAll(${i})" title="Todos">all</button>
        <button class="th-btn" onclick="colDeselectAll(${i})" title="Nenhum">--</button>
      </div>
    </div></th>`;
  });
  head += '<th>Div</th><th>R$/un</th><th></th></tr>';
  document.getElementById('tableHead').innerHTML = head;

  let body = '';
  state.items.forEach((item, i) => {
    const count = item.checks.filter(Boolean).length;
    const pp = count > 0 ? (item.price || 0) / count : 0;
    body += `<tr><td><input class="item-name" value="${esc(item.name)}"
      onchange="updateItem(${i},'name',this.value)" placeholder="Nome do item..."></td>`;
    body += `<td><input class="item-price" value="${fmtNum(item.price)}"
      onchange="updateItem(${i},'price',parsePrice(this.value))" placeholder="0,00"></td>`;
    state.people.forEach((p, j) => {
      const id = `cb_${i}_${j}`;
      body += `<td><input type="checkbox" class="cb" id="${id}" ${item.checks[j]?'checked':''}
        onchange="toggleCheck(${i},${j},this.checked)"><label class="cb-label" for="${id}"></label></td>`;
    });
    body += `<td><span class="div-val">${count}</span></td>`;
    body += `<td><span class="vid-val ${pp===0?'zero':''}">${count>0?'R$ '+pp.toFixed(2).replace('.',','):'--'}</span></td>`;
    body += `<td><button class="del-row" onclick="removeItem(${i})" title="Remover">&times;</button></td></tr>`;
  });

  if (!state.items.length) {
    body = `<tr><td colspan="${state.people.length+5}">
      <div class="empty-state">Nenhum item ainda.<br>Clique <strong>+ Novo item</strong> ou <strong>Colar do Excel</strong> para comecar.</div>
    </td></tr>`;
  }
  document.getElementById('tableBody').innerHTML = body;
}

function renderSummary() {
  let totals = {}; let grand = 0;
  state.people.forEach(p => totals[p] = 0);
  state.items.forEach(item => {
    if (item.price) grand += item.price;
    const c = item.checks.filter(Boolean).length;
    if (!c || !item.price) return;
    const pp = item.price / c;
    state.people.forEach((p, j) => { if (item.checks[j]) totals[p] += pp; });
  });

  let h = `<div class="summary-card summary-card-total">
    <div class="name">Total Geral</div>
    <div class="total">R$ ${grand.toFixed(2).replace('.',',')}</div></div>`;
  state.people.forEach(p => {
    h += `<div class="summary-card"><div class="name">${esc(p)}</div>
      <div class="total">R$ ${totals[p].toFixed(2).replace('.',',')}</div></div>`;
  });
  document.getElementById('summaryCards').innerHTML = h;
}

// === ACTIONS ===
function addPerson(name) {
  name = name.trim();
  if (!name || state.people.includes(name)) return;
  state.people.push(name);
  state.items.forEach(item => item.checks.push(false));
  render(); scheduleSave();
  toast(`${name} adicionado(a)`);
}

function removePerson(i) {
  const name = state.people[i];
  if (!confirm(`Remover ${name}?`)) return;
  state.people.splice(i, 1);
  state.items.forEach(item => item.checks.splice(i, 1));
  render(); scheduleSave();
}

function addItem(name='', price=0) {
  state.items.push({ name, price, checks: state.people.map(()=>false) });
  render(); scheduleSave();
  setTimeout(() => {
    const inputs = document.querySelectorAll('.item-name');
    if (inputs.length) inputs[inputs.length-1].focus();
  }, 50);
}

function removeItem(i) { state.items.splice(i,1); render(); scheduleSave(); }
function updateItem(i, field, val) { state.items[i][field] = val; render(); scheduleSave(); }

function toggleCheck(ii, pi, checked) {
  state.items[ii].checks[pi] = checked;
  const row = document.getElementById('tableBody').rows[ii];
  if (row) {
    const item = state.items[ii];
    const c = item.checks.filter(Boolean).length;
    const pp = c > 0 ? (item.price||0)/c : 0;
    const cells = row.cells;
    cells[cells.length-3].innerHTML = `<span class="div-val">${c}</span>`;
    cells[cells.length-2].innerHTML = `<span class="vid-val ${pp===0?'zero':''}">${c>0?'R$ '+pp.toFixed(2).replace('.',','):'--'}</span>`;
  }
  renderSummary(); scheduleSave();
}

function selectAll() {
  state.items.forEach(item => item.checks = item.checks.map(()=>true));
  render(); scheduleSave();
}
function colSelectAll(pi) { state.items.forEach(item => item.checks[pi] = true); render(); scheduleSave(); }
function colDeselectAll(pi) { state.items.forEach(item => item.checks[pi] = false); render(); scheduleSave(); }

// === PASTE ===
function togglePaste() {
  const z = document.getElementById('pasteZone');
  z.style.display = z.style.display === 'none' ? 'block' : 'none';
  if (z.style.display === 'block') { document.getElementById('pasteArea').value=''; document.getElementById('pasteArea').focus(); }
}

function processPaste() {
  const text = document.getElementById('pasteArea').value.trim();
  if (!text) return;
  let count = 0;
  text.split('\n').filter(l=>l.trim()).forEach(line => {
    let parts = line.split('\t');
    if (parts.length < 2) parts = line.split(';');
    const name = parts[0].trim();
    let price = 0;
    if (parts.length >= 2) {
      price = parseFloat(parts[1].trim().replace(/[rR]\$\s*/g,'').replace(/\./g,'').replace(',','.')) || 0;
    }
    if (name) {
      state.items.push({ name, price, checks: state.people.map(()=>false) });
      count++;
    }
  });
  togglePaste(); render(); scheduleSave();
  toast(`${count} item(ns) adicionado(s)`);
}

// === GLOBAL PASTE SHORTCUT ===
document.addEventListener('paste', e => {
  const a = document.activeElement;
  if (a.tagName !== 'INPUT' && a.tagName !== 'TEXTAREA') {
    e.preventDefault();
    const text = e.clipboardData.getData('text');
    if (text.includes('\n') || text.includes('\t')) {
      document.getElementById('pasteZone').style.display = 'block';
      document.getElementById('pasteArea').value = text;
      processPaste();
    }
  }
});

// === IMPORT / EXPORT ===
function exportJSON() {
  const blob = new Blob([JSON.stringify(state,null,2)], {type:'application/json'});
  const a = document.createElement('a');
  a.href = URL.createObjectURL(blob);
  a.download = 'tchurminha-backup.json';
  a.click(); URL.revokeObjectURL(a.href);
  toast('Backup exportado');
}

function importJSON(e) {
  const file = e.target.files[0]; if(!file) return;
  const r = new FileReader();
  r.onload = ev => {
    try {
      const d = JSON.parse(ev.target.result);
      if (d.people && d.items) { state=d; render(); scheduleSave(); toast('Dados importados'); }
    } catch(err) { toast('Erro ao ler arquivo'); }
  };
  r.readAsText(file); e.target.value='';
}

// === HELPERS ===
function esc(s) { const d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
function fmtNum(n) { return (n||0).toFixed(2).replace('.',','); }
function parsePrice(s) { if(!s) return 0; return parseFloat(s.replace(/[rR]\$\s*/g,'').replace(/\s/g,'').replace(/\./g,'').replace(',','.'))||0; }
function toast(msg) {
  const t=document.getElementById('toast');
  t.textContent=msg;
  t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),2200);
}

// === INIT ===
loadState();
</script>
</body>
</html>
