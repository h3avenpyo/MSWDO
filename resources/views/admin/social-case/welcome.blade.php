<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Social Case Study System</title>
<style>
  :root{
    --bg:#F6F7F5;
    --surface:#FFFFFF;
    --surface-sunken:#EFF1EE;
    --ink:#182422;
    --ink-soft:#5B665F;
    --ink-faint:#8A938C;
    --border:#DDE1D9;
    --navy:#1F3B3B;
    --navy-ink:#0F2323;
    --teal:#2F7D6B;
    --teal-bg:#E4F1EB;
    --teal-ink:#1C5346;
    --amber:#B8791E;
    --amber-bg:#FBF0DE;
    --amber-ink:#7A4F0F;
    --red:#A3403A;
    --red-bg:#FAE9E7;
    --red-ink:#6E2B27;
    --blue:#3B6EA5;
    --blue-bg:#E7EFF7;
    --blue-ink:#274A70;
    --gray:#767E76;
    --gray-bg:#ECEDE9;
    --gray-ink:#4B514B;
    --radius:10px;
    --shadow:0 1px 2px rgba(15,35,35,.06);
    font-family:-apple-system,BlinkMacSystemFont,"Inter","Segoe UI",Helvetica,Arial,sans-serif;
  }
  *{box-sizing:border-box;}
  html,body{margin:0;padding:0;background:var(--bg);color:var(--ink);}
  body{font-size:14px;line-height:1.5;}
  h1,h2,h3,h4{margin:0;font-weight:600;letter-spacing:-0.01em;}
  button{font-family:inherit;cursor:pointer;}
  input,select,textarea{font-family:inherit;font-size:14px;}
  .app{display:flex;min-height:100vh;}

  /* ---------- Sidebar ---------- */
  .sidebar{width:220px;flex-shrink:0;background:var(--navy);color:#EAF0EE;padding:20px 14px;display:flex;flex-direction:column;gap:2px;}
  .brand{display:flex;align-items:center;gap:10px;padding:4px 8px 20px;}
  .brand-mark{width:30px;height:30px;border-radius:7px;background:var(--teal);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;color:#0B2019;flex-shrink:0;}
  .brand-text{font-size:13px;line-height:1.25;}
  .brand-text b{display:block;font-size:13.5px;letter-spacing:0.01em;}
  .brand-text span{color:#9FB3AC;font-size:11px;}
  .nav-item{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:8px;color:#C7D6D1;font-size:13.5px;background:transparent;border:none;text-align:left;width:100%;transition:background .12s;}
  .nav-item:hover{background:rgba(255,255,255,.06);}
  .nav-item.active{background:rgba(255,255,255,.12);color:#fff;font-weight:500;}
  .nav-dot{width:6px;height:6px;border-radius:50%;background:currentColor;opacity:.55;flex-shrink:0;}
  .nav-section-label{font-size:10.5px;text-transform:uppercase;letter-spacing:.06em;color:#77897F;padding:16px 10px 6px;}
  .sidebar-foot{margin-top:auto;padding:10px 8px 2px;font-size:11px;color:#6E8079;border-top:1px solid rgba(255,255,255,.08);padding-top:12px;}

  /* ---------- Main ---------- */
  .main{flex:1;min-width:0;padding:28px 36px 60px;max-width:1180px;}
  .page-head{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:22px;gap:16px;flex-wrap:wrap;}
  .page-head h1{font-size:21px;}
  .page-head p{margin:4px 0 0;color:var(--ink-soft);font-size:13.5px;}
  .btn{border:1px solid var(--border);background:var(--surface);color:var(--ink);padding:8px 14px;border-radius:8px;font-size:13.5px;font-weight:500;display:inline-flex;align-items:center;gap:6px;box-shadow:var(--shadow);}
  .btn:hover{border-color:#C6CCC2;}
  .btn.primary{background:var(--navy);color:#fff;border-color:var(--navy);}
  .btn.primary:hover{background:var(--navy-ink);}
  .btn.danger{color:var(--red-ink);border-color:#E7C6C3;}
  .btn.ghost{background:transparent;box-shadow:none;border-color:transparent;color:var(--ink-soft);}
  .btn.ghost:hover{background:var(--surface-sunken);}
  .btn:disabled{opacity:.45;cursor:not-allowed;}
  .btn-sm{padding:5px 10px;font-size:12.5px;}

  /* ---------- Cards / grid ---------- */
  .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:24px;}
  .stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:14px 16px;box-shadow:var(--shadow);}
  .stat-card .num{font-size:24px;font-weight:600;line-height:1.1;}
  .stat-card .label{color:var(--ink-soft);font-size:12.5px;margin-top:4px;}
  .panel{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow);padding:18px 20px;margin-bottom:18px;}
  .panel h3{font-size:14.5px;margin-bottom:12px;}

  /* ---------- Table ---------- */
  table{width:100%;border-collapse:collapse;font-size:13.5px;}
  th{text-align:left;color:var(--ink-soft);font-weight:500;font-size:12px;text-transform:uppercase;letter-spacing:.03em;padding:8px 10px;border-bottom:1px solid var(--border);}
  td{padding:10px 10px;border-bottom:1px solid var(--border);vertical-align:middle;}
  tr.row-click{cursor:pointer;}
  tr.row-click:hover td{background:var(--surface-sunken);}
  .empty{padding:40px 20px;text-align:center;color:var(--ink-faint);}
  .empty i{font-size:26px;display:block;margin-bottom:8px;opacity:.5;}

  /* ---------- Badges ---------- */
  .badge{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:100px;font-size:12px;font-weight:500;white-space:nowrap;}
  .b-draft{background:var(--gray-bg);color:var(--gray-ink);}
  .b-review{background:var(--amber-bg);color:var(--amber-ink);}
  .b-approved{background:var(--teal-bg);color:var(--teal-ink);}
  .b-printed{background:var(--blue-bg);color:var(--blue-ink);}
  .b-released{background:#DCEEDC;color:#25542A;}
  .b-blocked{background:var(--red-bg);color:var(--red-ink);}

  /* ---------- Forms ---------- */
  .field{margin-bottom:14px;}
  .field label{display:block;font-size:12.5px;font-weight:500;color:var(--ink-soft);margin-bottom:5px;}
  .field .hint{font-size:11.5px;color:var(--ink-faint);margin-top:4px;}
  input[type=text],input[type=date],input[type=number],input[type=tel],select,textarea{
    width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:7px;background:var(--surface);color:var(--ink);
  }
  input:focus,select:focus,textarea:focus{outline:none;border-color:var(--teal);box-shadow:0 0 0 3px rgba(47,125,107,.12);}
  textarea{resize:vertical;min-height:70px;}
  .grid2{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
  .grid3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;}
  @media (max-width:720px){.grid2,.grid3{grid-template-columns:1fr;}}
  .checkbox-row{display:flex;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid var(--surface-sunken);}
  .checkbox-row input{width:auto;}
  .checkbox-row span{flex:1;font-size:13.5px;}
  .pill-check{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border:1px solid var(--border);border-radius:8px;font-size:13px;cursor:pointer;background:var(--surface);}
  .pill-check.on{background:var(--teal-bg);border-color:var(--teal);color:var(--teal-ink);}
  .pill-check input{display:none;}

  /* ---------- Stepper ---------- */
  .stepper{display:flex;align-items:center;margin-bottom:6px;}
  .step{display:flex;flex-direction:column;align-items:center;flex:1;position:relative;}
  .step .dot{width:26px;height:26px;border-radius:50%;background:var(--gray-bg);color:var(--gray-ink);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;border:2px solid var(--surface);z-index:1;}
  .step.done .dot{background:var(--teal);color:#fff;}
  .step.current .dot{background:var(--navy);color:#fff;}
  .step .lbl{font-size:11px;color:var(--ink-soft);margin-top:6px;text-align:center;}
  .step.done .lbl,.step.current .lbl{color:var(--ink);font-weight:500;}
  .step-line{position:absolute;top:13px;left:-50%;width:100%;height:2px;background:var(--border);z-index:0;}
  .step.done .step-line{background:var(--teal);}
  .step:first-child .step-line{display:none;}

  /* ---------- Eligibility timeline (signature element) ---------- */
  .elig-wrap{background:var(--surface-sunken);border-radius:8px;padding:14px 16px;}
  .elig-top{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:10px;}
  .elig-top .status-text{font-size:13px;font-weight:500;}
  .elig-track{position:relative;height:8px;background:#DFE4DB;border-radius:100px;overflow:hidden;}
  .elig-fill{position:absolute;top:0;left:0;height:100%;border-radius:100px;}
  .elig-marks{display:flex;justify-content:space-between;font-size:10.5px;color:var(--ink-faint);margin-top:6px;}

  /* ---------- Detail layout ---------- */
  .detail-grid{display:grid;grid-template-columns:2fr 1fr;gap:18px;align-items:start;}
  @media (max-width:900px){.detail-grid{grid-template-columns:1fr;}}
  .kv{display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--surface-sunken);font-size:13px;}
  .kv span:first-child{color:var(--ink-soft);}
  .kv span:last-child{font-weight:500;text-align:right;}
  .agency-tag{display:inline-block;background:var(--blue-bg);color:var(--blue-ink);font-size:11.5px;padding:3px 8px;border-radius:6px;margin:0 5px 5px 0;}
  .req-check{display:flex;align-items:center;gap:8px;padding:6px 0;}
  .req-check.missing{color:var(--red-ink);}

  /* ---------- Modal ---------- */
  .modal-overlay{position:fixed;inset:0;background:rgba(15,25,22,.42);display:flex;align-items:flex-start;justify-content:center;padding:40px 20px;overflow-y:auto;z-index:100;}
  .modal{background:var(--surface);border-radius:12px;max-width:640px;width:100%;padding:24px 26px;box-shadow:0 12px 40px rgba(0,0,0,.18);}
  .modal-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
  .modal-close{background:none;border:none;font-size:18px;color:var(--ink-faint);padding:4px 8px;}

  /* ---------- Document / print view ---------- */
  .doc-page{background:#fff;border:1px solid var(--border);border-radius:8px;padding:48px 54px;max-width:760px;margin:0 auto;font-family:Georgia,'Times New Roman',serif;color:#1a1a1a;line-height:1.6;}
  .doc-letterhead{text-align:center;border-bottom:2px solid #1a1a1a;padding-bottom:14px;margin-bottom:22px;}
  .doc-letterhead .office{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#555;}
  .doc-letterhead h2{font-family:Georgia,serif;font-size:17px;margin-top:6px;}
  .doc-letterhead .addr{font-size:11px;color:#666;margin-top:4px;}
  .doc-title{text-align:center;font-size:15px;letter-spacing:.06em;text-transform:uppercase;margin-bottom:6px;}
  .doc-sub{text-align:center;font-size:12px;color:#555;margin-bottom:24px;}
  .doc-section{margin-bottom:18px;}
  .doc-section h4{font-size:12.5px;text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid #ccc;padding-bottom:4px;margin-bottom:8px;}
  .doc-row{display:flex;font-size:13px;margin-bottom:4px;}
  .doc-row .l{width:180px;color:#555;flex-shrink:0;}
  .doc-body-text{font-size:13.3px;white-space:pre-wrap;}
  .doc-sign{margin-top:44px;display:flex;justify-content:space-between;font-size:12.5px;}
  .doc-sign .line{border-top:1px solid #333;padding-top:6px;width:220px;text-align:center;}
  .doc-toolbar{max-width:760px;margin:0 auto 14px;display:flex;justify-content:space-between;align-items:center;}
  @media print{
    .no-print{display:none !important;}
    .sidebar,.page-head,.toolbar-row{display:none !important;}
    .main{padding:0;max-width:none;}
    .doc-page{border:none;padding:0;max-width:none;}
    body{background:#fff;}
  }

  .toolbar-row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px;}
  .search-box{position:relative;flex:1;min-width:180px;}
  .search-box input{padding-left:32px;}
  .search-box i{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--ink-faint);font-size:15px;}
  .tabs{display:flex;gap:4px;background:var(--surface-sunken);padding:3px;border-radius:9px;width:fit-content;margin-bottom:16px;}
  .tab-btn{border:none;background:transparent;padding:6px 13px;border-radius:7px;font-size:13px;color:var(--ink-soft);font-weight:500;}
  .tab-btn.active{background:var(--surface);color:var(--ink);box-shadow:var(--shadow);}
  .banner{display:flex;gap:10px;align-items:flex-start;padding:12px 14px;border-radius:8px;font-size:13px;margin-bottom:16px;}
  .banner.warn{background:var(--amber-bg);color:var(--amber-ink);}
  .banner.block{background:var(--red-bg);color:var(--red-ink);}
  .banner.ok{background:var(--teal-bg);color:var(--teal-ink);}
  .banner i{margin-top:1px;flex-shrink:0;}
  .muted{color:var(--ink-soft);}
  .sr-only{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;}

  /* ── Topnav / Hamburger ── */
  .topnav{display:none;}

  /* ── Sidebar Overlay ── */
  .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;}
  .sidebar-overlay.active{display:block;}

  /* ── Responsive: Tablet (< 1024px) ── */
  @media (max-width:1023px){
    .topnav{display:flex !important;align-items:center;gap:8px;padding:12px 16px;margin-bottom:8px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);}
    .sidebar{position:fixed;top:0;left:0;bottom:0;width:220px;transform:translateX(-100%) !important;z-index:1001 !important;transition:transform .25s ease;}
    .sidebar.show{transform:translateX(0) !important;}
    .main{margin-left:0 !important;max-width:100% !important;padding:16px !important;}
  }

  /* ── Responsive: Mobile (< 768px) ── */
  @media (max-width:767px){
    .main{padding:12px !important;}
    .topnav{padding:10px 12px !important;}
    .doc-page{padding:24px !important;}
    .doc-row .l{width:120px !important;min-width:120px !important;}
    .doc-sign .line{width:160px !important;}
  }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/2.47.0/iconfont/tabler-icons.min.css">
</head>
<body>
<h1 class="sr-only">Social case study management system for tracking client eligibility, intake, workflow, and generating agency-specific reports</h1>
<div id="app" class="app"></div>

<script>
/* ---------------- Constants ---------------- */
const STATUSES = ["Draft","Review","Approved","Printed","Released"];
const STATUS_CLASS = {Draft:"b-draft",Review:"b-review",Approved:"b-approved",Printed:"b-printed",Released:"b-released"};
const PURPOSES = ["Medical Assistance","Burial Assistance","Educational Assistance","Financial Assistance","Food / Relief Assistance","Livelihood Assistance","Other"];
const AGENCIES = [
  {key:"PCSO", name:"Philippine Charity Sweepstakes Office", addressee:"The Officer-in-Charge\nPCSO Provincial/District Office"},
  {key:"DSWD", name:"Department of Social Welfare and Development", addressee:"The Regional Director\nDSWD Field Office"},
  {key:"OP", name:"Office of the President (AKAP)", addressee:"The Head, AKAP Program\nOffice of the President"},
  {key:"MSWDO", name:"MSWDO File Copy", addressee:"FILE COPY"}
];
const DEFAULT_REQUIREMENTS = ["Valid government-issued ID","Barangay Certificate of Residency / Indigency","Medical certificate or prescription (if medical)","Certificate of No Property / No Income","Death certificate (if burial assistance)"];
const ELIGIBILITY_DAYS = 180;

let cases = [];
let view = {tab:"dashboard", caseId:null, docAgency:null, newCaseStep:"search", eligClientName:"", eligOverride:false, eligMatch:null};
let draftIntake = null;

/* ---------------- Storage ---------------- */
async function loadCases(){
  try{
    const r = await window.storage.get('scs-cases', false);
    cases = r && r.value ? JSON.parse(r.value) : [];
  }catch(e){ cases = []; }
  render();
}
async function saveCases(){
  try{ await window.storage.set('scs-cases', JSON.stringify(cases), false); }
  catch(e){ console.error('Storage error', e); }
}

/* ---------------- Helpers ---------------- */
function uid(){ return 'c'+Date.now().toString(36)+Math.random().toString(36).slice(2,7); }
function todayISO(){ return new Date().toISOString().slice(0,10); }
function fmtDate(iso){
  if(!iso) return "—";
  const d = new Date(iso+"T00:00:00");
  return d.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
}
function daysBetween(a,b){ return Math.round((new Date(b)-new Date(a))/86400000); }
function escapeHtml(s){ return (s||"").replace(/[&<>"']/g, c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c])); }
function findLatestByName(name){
  const n = name.trim().toLowerCase();
  if(!n) return null;
  const matches = cases.filter(c => c.client.name.trim().toLowerCase() === n && c.releasedDate);
  if(!matches.length) return null;
  matches.sort((a,b)=> new Date(b.releasedDate) - new Date(a.releasedDate));
  return matches[0];
}
function eligibilityInfo(caseRec){
  if(!caseRec || !caseRec.releasedDate) return {eligible:true};
  const daysSince = daysBetween(caseRec.releasedDate, todayISO());
  const daysLeft = ELIGIBILITY_DAYS - daysSince;
  const nextDate = new Date(caseRec.releasedDate+"T00:00:00");
  nextDate.setDate(nextDate.getDate()+ELIGIBILITY_DAYS);
  return {
    eligible: daysLeft <= 0,
    daysSince, daysLeft: Math.max(daysLeft,0),
    nextEligibleDate: nextDate.toISOString().slice(0,10),
    pct: Math.min(100, Math.round((daysSince/ELIGIBILITY_DAYS)*100))
  };
}
function setView(patch){ view = {...view, ...patch}; render(); }

/* ---------------- New case flow ---------------- */
function generateControlNo(dateISO){
  const d = new Date(dateISO+"T00:00:00");
  const yyyy = d.getFullYear();
  const mm = String(d.getMonth()+1).padStart(2,'0');
  const seq = String(cases.length+1).padStart(4,'0');
  return `MSWD-O-${yyyy}-${mm}-${seq}`;
}
function blankIntake(name){
  const today = todayISO();
  return {
    id: uid(),
    status: "Draft",
    createdAt: today,
    updatedAt: today,
    controlNo: generateControlNo(today),
    client: {name: name||"", age:"", sex:"", address:"", birthdate:"", birthplace:"", religion:"", education:"", civilStatus:"", occupation:"", income:"", contact:""},
    household: [{name:"", relationship:"", age:"", education:"", occupation:"", income:""}],
    interview: {reportDate: today, problemPresented:"", homeCondition:"", socioEconomic:"", evaluation:"", recommendation:""},
    signers: {preparedByName:"", preparedByTitle:"MSWDO Staff", notedByName:"", notedByTitle:"MSWDO Head", notedByLicense:""},
    purpose: PURPOSES[0],
    agencies: [],
    requirements: DEFAULT_REQUIREMENTS.map(r=>({name:r, submitted:false})),
    statusHistory: [{status:"Draft", date: today}],
    releasedDate: null
  };
}

function startEligibilityCheck(){
  const name = view.eligClientName;
  const match = findLatestByName(name);
  setView({eligMatch: match, eligOverride:false});
}

function proceedToIntake(){
  draftIntake = blankIntake(view.eligClientName);
  setView({newCaseStep:"intake"});
}

function saveNewCase(){
  cases.push(draftIntake);
  saveCases();
  const id = draftIntake.id;
  draftIntake = null;
  setView({tab:"caseDetail", caseId:id, newCaseStep:"search", eligClientName:"", eligMatch:null});
}

/* ---------------- Case actions ---------------- */
function advanceStatus(caseRec){
  const idx = STATUSES.indexOf(caseRec.status);
  if(idx < STATUSES.length-1){
    caseRec.status = STATUSES[idx+1];
    caseRec.updatedAt = todayISO();
    caseRec.statusHistory.push({status:caseRec.status, date: todayISO()});
    if(caseRec.status === "Released"){ caseRec.releasedDate = todayISO(); }
    saveCases(); render();
  }
}
function revertStatus(caseRec){
  const idx = STATUSES.indexOf(caseRec.status);
  if(idx > 0){
    caseRec.status = STATUSES[idx-1];
    caseRec.updatedAt = todayISO();
    caseRec.statusHistory.push({status:caseRec.status+" (reverted)", date: todayISO()});
    if(caseRec.status !== "Released"){ caseRec.releasedDate = null; }
    saveCases(); render();
  }
}
function deleteCase(id){
  cases = cases.filter(c=>c.id!==id);
  saveCases();
  setView({tab:"caseList", caseId:null});
}
function getCase(id){ return cases.find(c=>c.id===id); }

/* ---------------- Rendering: Sidebar ---------------- */
function renderSidebar(){
  const items = [
    {tab:"dashboard", icon:"ti-layout-dashboard", label:"Dashboard"},
    {tab:"newCase", icon:"ti-user-plus", label:"New case"},
    {tab:"caseList", icon:"ti-list-details", label:"All cases"},
  ];
  return `
  <div class="sidebar">
    <div class="brand">
      <div class="brand-mark">SC</div>
      <div class="brand-text"><b>Case Study System</b><span>MSWDO</span></div>
    </div>
    ${items.map(it=>`
      <button class="nav-item ${view.tab===it.tab?'active':''}" onclick="setView({tab:'${it.tab}', newCaseStep:'search', eligClientName:'', eligMatch:null})">
        <i class="ti ${it.icon}" style="font-size:16px" aria-hidden="true"></i> ${it.label}
      </button>`).join("")}
    <div class="sidebar-foot">Data is stored on this device only.</div>
  </div><div class="sidebar-overlay" id="sidebarOverlay"></div>`;
}

/* ---------------- Rendering: Dashboard ---------------- */
function renderDashboard(){
  const byStatus = {};
  STATUSES.forEach(s=> byStatus[s] = cases.filter(c=>c.status===s).length);
  const nearingEligible = cases.filter(c=>{
    if(!c.releasedDate) return false;
    const e = eligibilityInfo(c);
    return !e.eligible && e.daysLeft <= 30;
  }).sort((a,b)=> eligibilityInfo(a).daysLeft - eligibilityInfo(b).daysLeft);

  const recent = [...cases].sort((a,b)=> new Date(b.updatedAt)-new Date(a.updatedAt)).slice(0,6);

  return `
  <div class="page-head">
    <div><h1>Dashboard</h1><p>Overview of all social case study requests.</p></div>
    <button class="btn primary" onclick="setView({tab:'newCase'})"><i class="ti ti-plus" aria-hidden="true"></i> New case</button>
  </div>
  <div class="cards">
    <div class="stat-card"><div class="num">${cases.length}</div><div class="label">Total cases</div></div>
    ${STATUSES.map(s=>`<div class="stat-card"><div class="num">${byStatus[s]}</div><div class="label">${s}</div></div>`).join("")}
  </div>

  ${nearingEligible.length ? `
  <div class="panel">
    <h3>Nearing re-eligibility (within 30 days)</h3>
    <div style="overflow-x:auto">
    <table>
      <tr><th>Client</th><th>Released</th><th>Eligible again</th><th>Days left</th><th></th></tr>
      ${nearingEligible.map(c=>{
        const e = eligibilityInfo(c);
        return `<tr class="row-click" onclick="setView({tab:'caseDetail', caseId:'${c.id}'})">
          <td>${escapeHtml(c.client.name)}</td><td>${fmtDate(c.releasedDate)}</td><td>${fmtDate(e.nextEligibleDate)}</td>
          <td><span class="badge b-review">${e.daysLeft} days</span></td>
          <td style="text-align:right"><i class="ti ti-chevron-right muted" aria-hidden="true"></i></td></tr>`;
      }).join("")}
    </table>
    </div>
  </div>` : ``}

  <div class="panel">
    <h3>Recently updated</h3>
    ${recent.length ? `<div style="overflow-x:auto"><table>
      <tr><th>Client</th><th>Purpose</th><th>Status</th><th>Updated</th></tr>
      ${recent.map(c=>`<tr class="row-click" onclick="setView({tab:'caseDetail', caseId:'${c.id}'})">
        <td>${escapeHtml(c.client.name)||"<span class=muted>Unnamed</span>"}</td>
        <td>${escapeHtml(c.purpose)}</td>
        <td><span class="badge ${STATUS_CLASS[c.status]}">${c.status}</span></td>
        <td>${fmtDate(c.updatedAt)}</td></tr>`).join("")}
    </table></div>` : `<div class="empty"><i class="ti ti-folder-open" aria-hidden="true"></i>No cases yet. Create your first one.</div>`}
  </div>`;
}

/* ---------------- Rendering: New case ---------------- */
function renderNewCase(){
  if(view.newCaseStep === "search"){
    return `
    <div class="page-head"><div><h1>New case</h1><p>Step 1 of 2 — check eligibility before starting intake.</p></div></div>
    <div class="panel" style="max-width:520px">
      <h3>Search client</h3>
      <div class="field">
        <label for="elig-name">Client full name</label>
        <input type="text" id="elig-name" placeholder="Juan Dela Cruz" value="${escapeHtml(view.eligClientName)}"
          oninput="view.eligClientName=this.value" onkeydown="if(event.key==='Enter'){startEligibilityCheck()}">
        <div class="hint">We'll check if this client received a social case study in the last 6 months.</div>
      </div>
      <button class="btn primary" onclick="startEligibilityCheck()"><i class="ti ti-search" aria-hidden="true"></i> Check eligibility</button>

      ${view.eligMatch !== undefined && view.eligMatch !== null ? renderEligResult(view.eligMatch) : (view.eligClientName && view.eligMatch===null ? renderEligClear() : "")}
    </div>`;
  }
  // intake step
  return renderIntakeForm();
}

function renderEligResult(match){
  const e = eligibilityInfo(match);
  if(e.eligible){
    return `<div class="banner ok" style="margin-top:16px"><i class="ti ti-circle-check" aria-hidden="true"></i>
      <div>Client is eligible. Last case study was released ${fmtDate(match.releasedDate)} (more than 6 months ago).</div></div>
      <button class="btn primary" style="margin-top:6px" onclick="proceedToIntake()"><i class="ti ti-arrow-right" aria-hidden="true"></i> Continue to intake</button>`;
  }
  return `
  <div class="banner block" style="margin-top:16px">
    <i class="ti ti-ban" aria-hidden="true"></i>
    <div>Not yet eligible. A case study for this client was released on ${fmtDate(match.releasedDate)}. Next eligible on <b>${fmtDate(e.nextEligibleDate)}</b> (${e.daysLeft} days from now).</div>
  </div>
  <div class="elig-wrap">
    <div class="elig-top"><span class="status-text">Restricted period</span><span class="muted">${e.pct}% elapsed</span></div>
    <div class="elig-track"><div class="elig-fill" style="width:${e.pct}%; background:var(--amber)"></div></div>
    <div class="elig-marks"><span>${fmtDate(match.releasedDate)}</span><span>${fmtDate(e.nextEligibleDate)}</span></div>
  </div>
  <label class="pill-check ${view.eligOverride?'on':''}" style="margin-top:14px" onclick="event.preventDefault(); setView({eligOverride: !view.eligOverride})">
    <input type="checkbox" ${view.eligOverride?'checked':''}> Override and proceed anyway (requires supervisor approval)
  </label><br>
  <button class="btn ${view.eligOverride?'primary':''}" style="margin-top:10px" ${view.eligOverride?'':'disabled'} onclick="proceedToIntake()">
    <i class="ti ti-arrow-right" aria-hidden="true"></i> Continue to intake
  </button>`;
}
function renderEligClear(){
  return `<div class="banner ok" style="margin-top:16px"><i class="ti ti-circle-check" aria-hidden="true"></i>
    <div>No prior case study found for this name. Client is eligible.</div></div>
    <button class="btn primary" style="margin-top:6px" onclick="proceedToIntake()"><i class="ti ti-arrow-right" aria-hidden="true"></i> Continue to intake</button>`;
}

function renderIntakeForm(){
  const d = draftIntake;
  return `
  <div class="page-head"><div><h1>New case — intake</h1><p>Step 2 of 2 — one form, used to populate every agency template.</p></div>
    <button class="btn ghost" onclick="setView({newCaseStep:'search'})"><i class="ti ti-arrow-left" aria-hidden="true"></i> Back</button>
  </div>

  <div class="panel">
    <h3>Report details</h3>
    <div class="grid2">
      <div class="field"><label>Control no.</label><input type="text" value="${escapeHtml(d.controlNo)}" oninput="draftIntake.controlNo=this.value"></div>
      <div class="field"><label>Report date</label><input type="date" value="${d.interview.reportDate}" oninput="draftIntake.interview.reportDate=this.value"></div>
    </div>
  </div>

  <div class="panel">
    <h3>I. Identifying information</h3>
    <div class="grid3">
      <div class="field"><label>Name</label><input type="text" value="${escapeHtml(d.client.name)}" oninput="draftIntake.client.name=this.value"></div>
      <div class="field"><label>Age</label><input type="number" value="${escapeHtml(String(d.client.age))}" oninput="draftIntake.client.age=this.value"></div>
      <div class="field"><label>Sex</label>
        <select oninput="draftIntake.client.sex=this.value">
          ${["","Male","Female"].map(o=>`<option ${d.client.sex===o?'selected':''}>${o}</option>`).join("")}
        </select>
      </div>
      <div class="field" style="grid-column:span 2"><label>Address</label><input type="text" value="${escapeHtml(d.client.address)}" oninput="draftIntake.client.address=this.value"></div>
      <div class="field"><label>Birthdate</label><input type="date" value="${d.client.birthdate}" oninput="draftIntake.client.birthdate=this.value"></div>
      <div class="field"><label>Birthplace</label><input type="text" value="${escapeHtml(d.client.birthplace)}" oninput="draftIntake.client.birthplace=this.value"></div>
      <div class="field"><label>Religion</label><input type="text" value="${escapeHtml(d.client.religion)}" oninput="draftIntake.client.religion=this.value"></div>
      <div class="field"><label>Educational attainment</label><input type="text" value="${escapeHtml(d.client.education)}" oninput="draftIntake.client.education=this.value"></div>
      <div class="field"><label>Civil status</label>
        <select oninput="draftIntake.client.civilStatus=this.value">
          ${["","Single","Married","Widowed","Separated"].map(o=>`<option ${d.client.civilStatus===o?'selected':''}>${o}</option>`).join("")}
        </select>
      </div>
      <div class="field"><label>Occupation</label><input type="text" value="${escapeHtml(d.client.occupation)}" oninput="draftIntake.client.occupation=this.value" placeholder="N/A"></div>
      <div class="field"><label>Income</label><input type="text" value="${escapeHtml(d.client.income)}" oninput="draftIntake.client.income=this.value" placeholder="N/A"></div>
      <div class="field"><label>Contact no.</label><input type="tel" value="${escapeHtml(d.client.contact)}" oninput="draftIntake.client.contact=this.value"></div>
    </div>
  </div>

  <div class="panel">
    <h3>II. Family composition</h3>
    ${d.household.map((m,i)=>`
      <div class="grid3" style="margin-bottom:8px;align-items:end;padding-bottom:8px;border-bottom:1px solid var(--surface-sunken)">
        <div class="field" style="margin-bottom:0"><label>${i===0?'Name':''}</label><input type="text" value="${escapeHtml(m.name)}" oninput="draftIntake.household[${i}].name=this.value"></div>
        <div class="field" style="margin-bottom:0"><label>${i===0?'Relationship':''}</label><input type="text" value="${escapeHtml(m.relationship)}" oninput="draftIntake.household[${i}].relationship=this.value"></div>
        <div class="field" style="margin-bottom:0"><label>${i===0?'Age':''}</label><input type="number" value="${escapeHtml(String(m.age))}" oninput="draftIntake.household[${i}].age=this.value"></div>
        <div class="field" style="margin-bottom:0"><label>${i===0?'Educational attainment':''}</label><input type="text" value="${escapeHtml(m.education)}" oninput="draftIntake.household[${i}].education=this.value"></div>
        <div class="field" style="margin-bottom:0"><label>${i===0?'Occupation':''}</label><input type="text" value="${escapeHtml(m.occupation)}" oninput="draftIntake.household[${i}].occupation=this.value" placeholder="N/A"></div>
        <div class="field" style="margin-bottom:0;display:flex;gap:6px">
          <div style="flex:1"><label>${i===0?'Income':''}</label><input type="text" value="${escapeHtml(m.income)}" oninput="draftIntake.household[${i}].income=this.value" placeholder="N/A"></div>
          ${i>0?`<button class="btn ghost btn-sm" style="align-self:flex-end" onclick="draftIntake.household.splice(${i},1);render()"><i class="ti ti-x" aria-hidden="true"></i></button>`:""}
        </div>
      </div>`).join("")}
    <button class="btn ghost btn-sm" onclick="draftIntake.household.push({name:'',relationship:'',age:'',education:'',occupation:'',income:''});render()"><i class="ti ti-plus" aria-hidden="true"></i> Add family member</button>
  </div>

  <div class="panel">
    <h3>Narrative sections</h3>
    <div class="field"><label>III. Problem presented</label><textarea oninput="draftIntake.interview.problemPresented=this.value">${escapeHtml(d.interview.problemPresented)}</textarea></div>
    <div class="field"><label>IV. Home condition</label><textarea oninput="draftIntake.interview.homeCondition=this.value">${escapeHtml(d.interview.homeCondition)}</textarea></div>
    <div class="field"><label>V. Socio-economic condition</label><textarea oninput="draftIntake.interview.socioEconomic=this.value">${escapeHtml(d.interview.socioEconomic)}</textarea></div>
    <div class="field"><label>VI. Evaluation</label><textarea oninput="draftIntake.interview.evaluation=this.value">${escapeHtml(d.interview.evaluation)}</textarea></div>
    <div class="field"><label>VII. Recommendation</label><textarea oninput="draftIntake.interview.recommendation=this.value">${escapeHtml(d.interview.recommendation)}</textarea></div>
  </div>

  <div class="panel">
    <h3>Signatories</h3>
    <div class="grid2">
      <div class="field"><label>Prepared by (name)</label><input type="text" value="${escapeHtml(d.signers.preparedByName)}" oninput="draftIntake.signers.preparedByName=this.value"></div>
      <div class="field"><label>Prepared by (title)</label><input type="text" value="${escapeHtml(d.signers.preparedByTitle)}" oninput="draftIntake.signers.preparedByTitle=this.value"></div>
      <div class="field"><label>Noted by (name)</label><input type="text" value="${escapeHtml(d.signers.notedByName)}" oninput="draftIntake.signers.notedByName=this.value"></div>
      <div class="field"><label>Noted by (title)</label><input type="text" value="${escapeHtml(d.signers.notedByTitle)}" oninput="draftIntake.signers.notedByTitle=this.value"></div>
      <div class="field"><label>Noted by (license no.)</label><input type="text" value="${escapeHtml(d.signers.notedByLicense)}" oninput="draftIntake.signers.notedByLicense=this.value"></div>
    </div>
  </div>

  <div class="panel">
    <h3>Purpose and agencies</h3>
    <div class="field" style="max-width:320px"><label>Purpose of assistance</label>
      <select oninput="draftIntake.purpose=this.value">${PURPOSES.map(p=>`<option ${d.purpose===p?'selected':''}>${p}</option>`).join("")}</select>
    </div>
    <div class="field"><label>Agencies this report will be prepared for</label>
      <div>${AGENCIES.map(a=>`
        <label class="pill-check ${d.agencies.includes(a.key)?'on':''}" onclick="event.preventDefault(); toggleAgency('${a.key}')">
          <input type="checkbox" ${d.agencies.includes(a.key)?'checked':''}> ${a.name}
        </label>`).join(" ")}</div>
    </div>
  </div>

  <div class="panel">
    <h3>Requirements checklist</h3>
    ${d.requirements.map((r,i)=>`
      <div class="checkbox-row">
        <input type="checkbox" ${r.submitted?'checked':''} onchange="draftIntake.requirements[${i}].submitted=this.checked">
        <span>${escapeHtml(r.name)}</span>
        <button class="btn ghost btn-sm" onclick="draftIntake.requirements.splice(${i},1);render()"><i class="ti ti-trash" aria-hidden="true"></i></button>
      </div>`).join("")}
    <div style="display:flex;gap:8px;margin-top:10px">
      <input type="text" id="new-req" placeholder="Add a requirement" style="flex:1">
      <button class="btn btn-sm" onclick="const v=document.getElementById('new-req').value.trim(); if(v){draftIntake.requirements.push({name:v, submitted:false}); render();}">Add</button>
    </div>
  </div>

  <div style="display:flex;justify-content:flex-end;gap:10px">
    <button class="btn ghost" onclick="draftIntake=null; setView({tab:'dashboard'})">Cancel</button>
    <button class="btn primary" onclick="saveNewCase()"><i class="ti ti-device-floppy" aria-hidden="true"></i> Save as draft</button>
  </div>`;
}
function toggleAgency(key){
  const i = draftIntake.agencies.indexOf(key);
  if(i===-1) draftIntake.agencies.push(key); else draftIntake.agencies.splice(i,1);
  render();
}

/* ---------------- Rendering: Case list ---------------- */
function renderCaseList(){
  const q = (view.listQuery||"").toLowerCase();
  const statusFilter = view.listStatus || "All";
  let list = cases.filter(c => !q || c.client.name.toLowerCase().includes(q) || c.purpose.toLowerCase().includes(q));
  if(statusFilter !== "All") list = list.filter(c=>c.status===statusFilter);
  list = [...list].sort((a,b)=> new Date(b.updatedAt)-new Date(a.updatedAt));

  return `
  <div class="page-head"><div><h1>All cases</h1><p>${cases.length} total records.</p></div>
    <button class="btn primary" onclick="setView({tab:'newCase'})"><i class="ti ti-plus" aria-hidden="true"></i> New case</button>
  </div>
  <div class="toolbar-row">
    <div class="search-box"><i class="ti ti-search" aria-hidden="true"></i>
      <input type="text" placeholder="Search by client name or purpose" value="${escapeHtml(view.listQuery||'')}" oninput="setView({listQuery:this.value})"></div>
    <select style="width:auto" onchange="setView({listStatus:this.value})">
      <option ${statusFilter==='All'?'selected':''}>All</option>
      ${STATUSES.map(s=>`<option ${statusFilter===s?'selected':''}>${s}</option>`).join("")}
    </select>
  </div>
  <div class="panel" style="padding:0">
    ${list.length ? `<div style="overflow-x:auto"><table>
      <tr><th>Client</th><th>Purpose</th><th>Agencies</th><th>Status</th><th>Updated</th></tr>
      ${list.map(c=>`<tr class="row-click" onclick="setView({tab:'caseDetail', caseId:'${c.id}'})">
        <td>${escapeHtml(c.client.name)||"<span class=muted>Unnamed</span>"}</td>
        <td>${escapeHtml(c.purpose)}</td>
        <td>${c.agencies.map(a=>`<span class="agency-tag">${a}</span>`).join("")||'<span class="muted">—</span>'}</td>
        <td><span class="badge ${STATUS_CLASS[c.status]}">${c.status}</span></td>
        <td>${fmtDate(c.updatedAt)}</td></tr>`).join("")}
    </table></div>` : `<div class="empty"><i class="ti ti-search-off" aria-hidden="true"></i>No cases match.</div>`}
  </div>`;
}

/* ---------------- Rendering: Case detail ---------------- */
function renderCaseDetail(){
  const c = getCase(view.caseId);
  if(!c) return `<div class="empty"><i class="ti ti-alert-triangle" aria-hidden="true"></i>Case not found.</div>`;
  const idx = STATUSES.indexOf(c.status);
  const missingReqs = c.requirements.filter(r=>!r.submitted);
  const canGenerate = c.agencies.length > 0;

  return `
  <div class="page-head">
    <div><h1>${escapeHtml(c.client.name)||"Unnamed client"}</h1><p>${escapeHtml(c.purpose)} · Created ${fmtDate(c.createdAt)}</p></div>
    <div style="display:flex;gap:8px">
      <button class="btn ghost" onclick="setView({tab:'caseList'})"><i class="ti ti-arrow-left" aria-hidden="true"></i> All cases</button>
      <button class="btn danger" onclick="if(confirm('Delete this case permanently?')) deleteCase('${c.id}')"><i class="ti ti-trash" aria-hidden="true"></i></button>
    </div>
  </div>

  <div class="panel">
    <h3>Workflow</h3>
    <div class="stepper">
      ${STATUSES.map((s,i)=>`
        <div class="step ${i<idx?'done':''} ${i===idx?'current':''}">
          <div class="step-line"></div>
          <div class="dot">${i<idx?'<i class="ti ti-check" aria-hidden="true"></i>':i+1}</div>
          <div class="lbl">${s}</div>
        </div>`).join("")}
    </div>
    <div style="display:flex;gap:8px;margin-top:14px">
      ${idx>0?`<button class="btn ghost btn-sm" onclick="revertStatus(getCase('${c.id}'))"><i class="ti ti-arrow-left" aria-hidden="true"></i> Send back</button>`:""}
      ${idx<STATUSES.length-1?`<button class="btn primary btn-sm" onclick="advanceStatus(getCase('${c.id}'))"><i class="ti ti-arrow-right" aria-hidden="true"></i> Advance to ${STATUSES[idx+1]}</button>`:`<span class="badge b-released"><i class="ti ti-flag-check" aria-hidden="true"></i> Released ${fmtDate(c.releasedDate)}</span>`}
    </div>
  </div>

  ${c.releasedDate ? renderEligibilityCard(c) : ""}

  <div class="detail-grid">
    <div>
      <div class="panel">
        <h3>Client information</h3>
        <div class="kv"><span>Age / sex</span><span>${escapeHtml(String(c.client.age))||"—"} / ${escapeHtml(c.client.sex)||"—"}</span></div>
        <div class="kv"><span>Address</span><span>${escapeHtml(c.client.address)||"—"}</span></div>
        <div class="kv"><span>Contact</span><span>${escapeHtml(c.client.contact)||"—"}</span></div>
        <div class="kv"><span>Birthdate</span><span>${c.client.birthdate?fmtDate(c.client.birthdate):"—"}</span></div>
        <div class="kv"><span>Civil status</span><span>${escapeHtml(c.client.civilStatus)||"—"}</span></div>
        <div class="kv"><span>Occupation / income</span><span>${escapeHtml(c.client.occupation)||"N/A"} / ${escapeHtml(c.client.income)||"N/A"}</span></div>
        <div class="kv"><span>Control no.</span><span>${escapeHtml(c.controlNo)}</span></div>
      </div>
      <div class="panel">
        <h3>Interview summary</h3>
        <div class="kv"><span>Problem presented</span><span style="text-align:left;max-width:340px">${escapeHtml(c.interview.problemPresented)||"—"}</span></div>
        <div class="kv"><span>Evaluation</span><span style="text-align:left;max-width:340px">${escapeHtml(c.interview.evaluation)||"—"}</span></div>
        <div class="kv"><span>Recommendation</span><span style="text-align:left;max-width:340px">${escapeHtml(c.interview.recommendation)||"—"}</span></div>
        <div class="kv"><span>Prepared by</span><span>${escapeHtml(c.signers.preparedByName)||"—"}</span></div>
        <div class="kv"><span>Noted by</span><span>${escapeHtml(c.signers.notedByName)||"—"}</span></div>
      </div>
      <div class="panel">
        <h3>History</h3>
        ${c.statusHistory.slice().reverse().map(h=>`<div class="kv"><span>${h.status}</span><span>${fmtDate(h.date)}</span></div>`).join("")}
      </div>
    </div>
    <div>
      <div class="panel">
        <h3>Requirements</h3>
        ${c.requirements.map(r=>`<div class="req-check ${r.submitted?'':'missing'}"><i class="ti ${r.submitted?'ti-circle-check':'ti-circle-x'}" aria-hidden="true"></i> ${escapeHtml(r.name)}</div>`).join("")}
        ${missingReqs.length ? `<div class="hint" style="margin-top:8px;color:var(--red-ink)">${missingReqs.length} requirement(s) missing.</div>`:""}
      </div>
      <div class="panel">
        <h3>Generate document</h3>
        ${c.agencies.length ? c.agencies.map(a=>{
          const ag = AGENCIES.find(x=>x.key===a);
          return `<button class="btn" style="width:100%;justify-content:space-between;margin-bottom:8px" onclick="setView({tab:'document', docAgency:'${a}'})">
            <span><i class="ti ti-file-description" aria-hidden="true"></i> ${ag.name}</span><i class="ti ti-chevron-right" aria-hidden="true"></i></button>`;
        }).join("") : `<div class="muted" style="font-size:13px">No agencies selected for this case.</div>`}
      </div>
    </div>
  </div>`;
}

function renderEligibilityCard(c){
  const e = eligibilityInfo(c);
  return `<div class="panel">
    <h3>Re-eligibility window</h3>
    <div class="elig-wrap">
      <div class="elig-top"><span class="status-text">${e.eligible?'Eligible now':'Restricted'}</span><span class="muted">${e.pct}% elapsed</span></div>
      <div class="elig-track"><div class="elig-fill" style="width:${e.pct}%; background:${e.eligible?'var(--teal)':'var(--amber)'}"></div></div>
      <div class="elig-marks"><span>Released ${fmtDate(c.releasedDate)}</span><span>Next eligible ${fmtDate(e.nextEligibleDate)}</span></div>
    </div>
  </div>`;
}

/* ---------------- Rendering: Document view ---------------- */
function renderDocument(){
  const c = getCase(view.caseId);
  if(!c) return `<div class="empty">Case not found.</div>`;
  const ag = AGENCIES.find(a=>a.key===view.docAgency) || AGENCIES[0];
  const famRows = c.household.filter(m=>m.name);

  return `
  <div class="doc-toolbar no-print">
    <button class="btn ghost" onclick="setView({tab:'caseDetail'})"><i class="ti ti-arrow-left" aria-hidden="true"></i> Back to case</button>
    <div style="display:flex;gap:8px">
      <select style="width:auto" onchange="setView({docAgency:this.value})">
        ${c.agencies.map(a=>`<option value="${a}" ${a===ag.key?'selected':''}>${AGENCIES.find(x=>x.key===a).name}</option>`).join("")}
      </select>
      <button class="btn primary" onclick="window.print()"><i class="ti ti-printer" aria-hidden="true"></i> Print / save as PDF</button>
    </div>
  </div>
  <div class="doc-page">
    <div style="text-align:right;font-size:13px;margin-bottom:18px">${fmtDate(c.interview.reportDate)}</div>
    <div style="font-size:12.5px;margin-bottom:16px">CONTROL NO. ${escapeHtml(c.controlNo)}</div>
    <div class="doc-title">Social Case Study Report</div>
    <div class="doc-sub">(For: ${escapeHtml(c.purpose)})</div>
    <div class="no-print" style="text-align:center;margin:-14px 0 20px"><span class="agency-tag">Copy: ${ag.name}</span></div>

    <div class="doc-section">
      <h4>I. Identifying information</h4>
      <div class="doc-row"><div class="l">Name</div><div>${escapeHtml(c.client.name)}</div></div>
      <div class="doc-row"><div class="l">Age</div><div>${escapeHtml(String(c.client.age))||"—"} yrs. old</div></div>
      <div class="doc-row"><div class="l">Sex</div><div>${escapeHtml(c.client.sex)||"—"}</div></div>
      <div class="doc-row"><div class="l">Address</div><div>${escapeHtml(c.client.address)||"—"}</div></div>
      <div class="doc-row"><div class="l">Birthdate</div><div>${c.client.birthdate?fmtDate(c.client.birthdate):"—"}</div></div>
      <div class="doc-row"><div class="l">Birthplace</div><div>${escapeHtml(c.client.birthplace)||"—"}</div></div>
      <div class="doc-row"><div class="l">Religion</div><div>${escapeHtml(c.client.religion)||"—"}</div></div>
      <div class="doc-row"><div class="l">Educ. att.</div><div>${escapeHtml(c.client.education)||"—"}</div></div>
      <div class="doc-row"><div class="l">Civil status</div><div>${escapeHtml(c.client.civilStatus)||"—"}</div></div>
      <div class="doc-row"><div class="l">Occupation</div><div>${escapeHtml(c.client.occupation)||"N/A"}</div></div>
      <div class="doc-row"><div class="l">Income</div><div>${escapeHtml(c.client.income)||"N/A"}</div></div>
      <div class="doc-row"><div class="l">Contact no.</div><div>${escapeHtml(c.client.contact)||"—"}</div></div>
    </div>

    <div class="doc-section">
      <h4>II. Family composition</h4>
      ${famRows.length ? `<table style="width:100%;font-size:12.5px;border-collapse:collapse">
        <tr>${["Relatives","Relationship","Age","Educ. attainment","Occupation","Income"].map(h=>`<th style="border-bottom:1px solid #999;text-align:left;padding:4px 6px;font-weight:600">${h}</th>`).join("")}</tr>
        ${famRows.map(m=>`<tr>
          <td style="padding:4px 6px;border-bottom:1px solid #ddd">${escapeHtml(m.name)}</td>
          <td style="padding:4px 6px;border-bottom:1px solid #ddd">${escapeHtml(m.relationship)||"—"}</td>
          <td style="padding:4px 6px;border-bottom:1px solid #ddd">${escapeHtml(String(m.age))||"—"}</td>
          <td style="padding:4px 6px;border-bottom:1px solid #ddd">${escapeHtml(m.education)||"—"}</td>
          <td style="padding:4px 6px;border-bottom:1px solid #ddd">${escapeHtml(m.occupation)||"N/A"}</td>
          <td style="padding:4px 6px;border-bottom:1px solid #ddd">${escapeHtml(m.income)||"N/A"}</td>
        </tr>`).join("")}
      </table>` : `<div class="doc-body-text muted">None listed.</div>`}
    </div>

    <div class="doc-section"><h4>III. Problem presented</h4><div class="doc-body-text">${escapeHtml(c.interview.problemPresented)||"—"}</div></div>
    <div class="doc-section"><h4>IV. Home condition</h4><div class="doc-body-text">${escapeHtml(c.interview.homeCondition)||"—"}</div></div>
    <div class="doc-section"><h4>V. Socio economic condition</h4><div class="doc-body-text">${escapeHtml(c.interview.socioEconomic)||"—"}</div></div>
    <div class="doc-section"><h4>VI. Evaluation</h4><div class="doc-body-text">${escapeHtml(c.interview.evaluation)||"—"}</div></div>
    <div class="doc-section"><h4>VII. Recommendation</h4><div class="doc-body-text">${escapeHtml(c.interview.recommendation)||"—"}</div></div>

    <div class="doc-sign">
      <div class="line">${escapeHtml(c.signers.preparedByName)||"—"}<br><span style="font-size:11px;color:#777">${escapeHtml(c.signers.preparedByTitle)}</span></div>
      <div class="line">${escapeHtml(c.signers.notedByName)||"—"}<br><span style="font-size:11px;color:#777">${escapeHtml(c.signers.notedByTitle)}${c.signers.notedByLicense?", License No. "+escapeHtml(c.signers.notedByLicense):""}</span></div>
    </div>
  </div>`;
}

/* ---------------- Sidebar toggle ---------------- */
function toggleSidebar(){
  var sidebar = document.querySelector('.sidebar');
  var overlay = document.getElementById('sidebarOverlay');
  if(sidebar.classList.contains('show')){
    sidebar.classList.remove('show');
    if(overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
  } else {
    sidebar.classList.add('show');
    if(overlay) overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

/* ---------------- Main render ---------------- */
function render(){
  let body;
  if(view.tab==="dashboard") body = renderDashboard();
  else if(view.tab==="newCase") body = renderNewCase();
  else if(view.tab==="caseList") body = renderCaseList();
  else if(view.tab==="caseDetail") body = renderCaseDetail();
  else if(view.tab==="document") body = renderDocument();
  else body = renderDashboard();

  document.getElementById('app').innerHTML = renderSidebar() + `<div class="main"><div class="topnav no-print"><button class="btn" onclick="toggleSidebar()" aria-label="Toggle navigation"><i class="ti ti-menu-2" aria-hidden="true"></i> Menu</button></div>${body}</div>`;
}

loadCases();

/* ---------------- Responsive handlers ---------------- */
(function(){
  var overlay = document.getElementById('sidebarOverlay');
  if(overlay) overlay.addEventListener('click', function(){
    var sidebar = document.querySelector('.sidebar');
    if(sidebar) sidebar.classList.remove('show');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
  });
  document.addEventListener('keydown', function(e){
    if(e.key === 'Escape'){
      var sidebar = document.querySelector('.sidebar');
      if(sidebar && sidebar.classList.contains('show')){
        sidebar.classList.remove('show');
        if(overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
      }
    }
  });
  window.addEventListener('resize', function(){
    if(window.innerWidth >= 1024){
      var sidebar = document.querySelector('.sidebar');
      var ov = document.getElementById('sidebarOverlay');
      if(sidebar && sidebar.classList.contains('show')){
        sidebar.classList.remove('show');
        if(ov) ov.classList.remove('active');
        document.body.style.overflow = '';
      }
    }
  });
})();
</script>
</body>
</html>
