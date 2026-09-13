/* ---------------- Constants ---------------- */
const STATUSES = ["Draft","Review","Approved","Released","Archived"];
const STATUS_CLASS = {Draft:"b-draft",Review:"b-review",Approved:"b-approved",Released:"b-released",Archived:"b-archived"};
const PURPOSES = ["Medical Assistance","Burial Assistance","Educational Assistance","Financial Assistance","Food / Relief Assistance","Livelihood Assistance","Other"];
const RELATIONSHIPS = ["Spouse","Wife","Husband","Son","Daughter","Father","Mother","Brother","Sister","Grandparent","Grandchild","Uncle","Aunt","Nephew","Niece","Cousin","Father-in-law","Mother-in-law","Brother-in-law","Sister-in-law","Stepfather","Stepmother","Stepchild","Adopted Child","Foster Parent","Legal Guardian","Boarder","Live-in Partner","Other"];
const BARANGAYS = [
  "Acacia","Adlas","Anahaw I","Anahaw II","Balite I","Balite II","Balubad","Banaba","Batas",
  "Biga I","Biga II","Biluso","Bucal","Buho","Bulihan","Cabangaan","Carmen","Hoyo","Hukay","Iba",
  "Inchican","Ipil I","Ipil II","Kalubkob","Kaong","Lalaan I","Lalaan II","Litlit","Lucsuhin","Lumil",
  "Maguyam","Malabag","Malaking Tatyao","Mataas na Burol","Munting Ilog","Narra I","Narra II","Narra III",
  "Paligawan","Pasong Langka","Barangay I (Poblacion)","Barangay II (Poblacion)","Barangay III (Poblacion)",
  "Barangay IV (Poblacion)","Barangay V (Poblacion)","Pooc I","Pooc II","Pulong Bunga","Pulong Saging",
  "Puting Kahoy","Sabutan","San Miguel I","San Miguel II","San Vicente I","San Vicente II","Santol",
  "Tartaria","Tibig","Toledo","Tubuan I","Tubuan II","Tubuan III","Ulat","Yakal"
];

// Make constants globally accessible for blade templates
window.STATUSES = STATUSES;
window.PURPOSES = PURPOSES;
window.BARANGAYS = BARANGAYS;
const AGENCIES = [
  {key:"PCSO", name:"Philippine Charity Sweepstakes Office", addressee:"The Officer-in-Charge\nPCSO Provincial/District Office"},
  {key:"DSWD", name:"Department of Social Welfare and Development", addressee:"The Regional Director\nDSWD Field Office"},
  {key:"OP", name:"Office of the President", addressee:"Office of the President"},
  {key:"DOH", name:"Department of Health", addressee:"The Regional Director\nDepartment of Health"},
  {key:"MSWDO", name:"MSWDO File Copy", addressee:"FILE COPY"}
];
const DEFAULT_REQUIREMENTS = ["Valid government-issued ID","Barangay Certificate of Residency / Indigency","Medical certificate or prescription (if medical)","Certificate of No Property / No Income","Death certificate (if burial assistance)"];
const ELIGIBILITY_DAYS = 180;

/* ── Name Normalization (mirrors PHP normalizeName + parseFullName) ── */
function normalizeClientName(name){
  if(!name) return '';
  return name.trim()
    .toLowerCase()
    .replace(/[.,'"'\u2019]/g, '')
    .replace(/-/g, ' ')
    .replace(/\s+/g, ' ')
    .trim();
}
function parseClientName(fullName){
  const normalized = normalizeClientName(fullName);
  const parts = normalized.split(/\s+/).filter(Boolean);
  return {
    firstName:  parts[0] || '',
    lastName:   parts.length ? parts[parts.length-1] : '',
    middleName: parts.length > 2 ? parts.slice(1, -1).join(' ') : '',
    normalized,
    parts
  };
}

/* Count overlap between input parts and client parts, including
   concatenated client parts (e.g. "geraldlouis" = "gerald" + "louis"). */
function countEffectiveOverlap(inputParts, clientParts){
  let overlap = 0;
  const usedClient = new Set();
  for(const ip of inputParts){
    let matched = false;
    // 1. Direct match
    for(let j=0;j<clientParts.length;j++){
      if(ip===clientParts[j] && !usedClient.has(j)){
        overlap++; usedClient.add(j); matched=true; break;
      }
    }
    if(matched) continue;
    // 2. Concatenated consecutive client parts
    for(let start=0;start<clientParts.length;start++){
      if(usedClient.has(start)) continue;
      let concat='';
      const usedRun=[];
      for(let j=start;j<clientParts.length;j++){
        concat+=clientParts[j]; usedRun.push(j);
        if(concat===ip){ overlap+=usedRun.length; usedRun.forEach(v=>usedClient.add(v)); matched=true; break; }
        if(concat.length>ip.length) break;
      }
      if(matched) break;
    }
  }
  return overlap;
}

/* Extract given name parts (excluding surname and single-character initials) */
function extractGivenParts(parts, lastName){
  return (parts || []).filter(p => p !== lastName && p.length > 1);
}

/* Check if two sets of name parts share a real given-name match or overlap */
function hasGivenNameMatch(inputParts, clientParts, lastName){
  const inputGiven = extractGivenParts(inputParts, lastName);
  const clientGiven = extractGivenParts(clientParts, lastName);
  for(const ig of inputGiven){
    for(const cg of clientGiven){
      if(ig === cg) return true;
      if(ig.length >= 4 && cg.length >= 4){
        if(ig.startsWith(cg) || cg.startsWith(ig)) return true;
      }
    }
  }
  return false;
}

/* ── Backend-fresh document data (date + age) ──────────────────────── */
async function fetchDocumentData(caseId){
  try {
    const resp = await fetch(`/admin/social-case/api/cases/${caseId}/document-data`, {
      headers: { 'Accept': 'application/json' }
    });
    if(!resp.ok) throw new Error('Failed to fetch document data');
    return await resp.json();
  } catch(e) {
    console.error('fetchDocumentData error:', e);
    // Fallback: compute age client-side from birthdate
    return null;
  }
}

let cases = [];
let view = {tab:"dashboard", caseId:null, docAgency:null, newCaseStep:"search", eligClientName:"", eligOverride:false, eligMatch:null, caseListPage:1, archivePage:1, archiveSearch:"", archiveFilter:"", archiveBarangay:""};
let selectedAgency = "PCSO";
let draftIntake = null;

const CURRENT_USER_ID = (document.querySelector('meta[name="user-id"]')?.content || '').trim();
const CURRENT_USER_ROLE = (document.querySelector('meta[name="user-role"]')?.content || '').toLowerCase();
const CURRENT_USER_NAME = (document.querySelector('meta[name="user-name"]')?.content || '').trim();
const ADMIN_NAME = (document.querySelector('meta[name="admin-name"]')?.content || '').trim();
const CAN_CHECK_ELIGIBILITY = CURRENT_USER_ROLE === 'eligibility_checker' || CURRENT_USER_ROLE === 'admin';
const CAN_ENCODE = CURRENT_USER_ROLE === 'social_worker' || CURRENT_USER_ROLE === 'admin';
const IS_PURE_ELIGIBILITY_CHECKER = CAN_CHECK_ELIGIBILITY && !CAN_ENCODE;

/* ---- Activity Tracking ---- */
async function logActivity(action, details, caseInfo = null) {
  try {
    const response = await fetch('/admin/social-case/api/activities', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        action: action,
        details: details,
        case_info: caseInfo
      })
    });
    const data = await response.json();
    console.log('Activity logged to database:', data);
  } catch(e) {
    console.error('Error logging activity:', e);
    // Fallback to localStorage if API fails
    const activities = JSON.parse(localStorage.getItem('sc_activities') || '[]');
    const activity = {
      id: Date.now(),
      action: action,
      details: details,
      caseInfo: caseInfo,
      timestamp: new Date().toISOString(),
      date: new Date().toISOString().split('T')[0]
    };
    activities.unshift(activity);
    if(activities.length > 50) activities.pop();
    localStorage.setItem('sc_activities', JSON.stringify(activities));
  }
}

async function getActivities() {
  try {
    const response = await fetch('/admin/social-case/api/activities');
    const activities = await response.json();
    // Convert database format to frontend format
    return activities.map(a => ({
      id: a.id,
      action: a.action,
      details: a.details,
      caseInfo: a.case_info,
      timestamp: a.created_at,
      date: a.created_at
    }));
  } catch(e) {
    console.error('Error fetching activities:', e);
    // Fallback to localStorage
    return JSON.parse(localStorage.getItem('sc_activities') || '[]');
  }
}

async function clearActivities() {
  try {
    await fetch('/admin/social-case/api/activities/clear', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      }
    });
  } catch(e) {
    console.error('Error clearing activities:', e);
    // Fallback to localStorage
    localStorage.removeItem('sc_activities');
  }
}

/* ---- Naming-convention helpers (camelCase <-> snake_case) ---- */
function camelToSnake(str){ return str.replace(/[A-Z]/g, c => '_'+c.toLowerCase()); }
function snakeToCamel(str){ return str.replace(/_([a-z])/g, (_, c) => c.toUpperCase()); }
function convertKeys(obj, fn){
  if(Array.isArray(obj)) return obj.map(v => convertKeys(v, fn));
  if(obj !== null && typeof obj === 'object'){
    return Object.fromEntries(Object.entries(obj).map(([k,v]) => [fn(k), convertKeys(v, fn)]));
  }
  return obj;
}

/* ---------------- Storage ---------------- */
async function loadCases(){
  try {
    console.log('Loading cases from API...');
    const response = await fetch('/admin/social-case/api/cases');
    console.log('Response status:', response.status);
    const data = await response.json();
    console.log('Cases loaded (raw):', data);
    // Convert snake_case API keys to camelCase used by the JS front-end
    cases = (data || []).map(c => convertKeys(c, snakeToCamel));
    console.log('Cases loaded (converted):', cases);
    console.log('Total cases:', cases.length);
  } catch(e) {
    console.error('Failed to load cases:', e);
    cases = [];
  }
}

async function loadEligibilityData(){
  try {
    console.log('Loading eligibility data from API...');
    const response = await fetch('/admin/social-case/api/eligibility-data');
    console.log('Response status:', response.status);
    const data = await response.json();
    console.log('Eligibility data loaded (raw):', data);
    // Convert snake_case API keys to camelCase used by the JS front-end
    cases = (data || []).map(c => convertKeys(c, snakeToCamel));
    console.log('Eligibility data loaded (converted):', cases);
    console.log('Total records:', cases.length);
  } catch(e) {
    console.error('Failed to load eligibility data:', e);
    cases = [];
  }
}
async function saveCases(){
  // No longer needed - cases are saved via API
}

/* ---------------- Case Details Modal ---------------- */
function showCaseDetailsModal(caseId){
  const caseRec = cases.find(c => c.id == caseId);
  if(!caseRec) return;

  const existing = document.getElementById('caseDetailsModal');
  if(existing) existing.remove();

  const modal = document.createElement('div');
  modal.id = 'caseDetailsModal';
  modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);display:flex;align-items:center;justify-content:center;padding:16px;z-index:9999;animation:fadeIn 0.2s ease;backdrop-filter:blur(4px);';
  
  modal.innerHTML = `
    <style>
      #caseDetailsModal *{box-sizing:border-box}
      #caseDetailsModal .cs-modal-box{
        animation:csSlideUp .25s ease;
        border-radius:16px !important;
        overflow:hidden !important;
      }
      #caseDetailsModal .cs-modal-header{
        border-top-left-radius:16px !important;
        border-top-right-radius:16px !important;
      }
      #caseDetailsModal .cs-modal-footer{
        border-bottom-left-radius:16px !important;
        border-bottom-right-radius:16px !important;
      }
      @keyframes csSlideUp{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:translateY(0)}}
      @media(max-width:768px){
        #caseDetailsModal{padding:12px!important}
        #caseDetailsModal .cs-modal-box{
          max-height:85vh!important;
          border-radius:14px!important;
          height:auto!important;
          max-width:100%!important;
          width:100%!important;
          overflow:hidden!important;
        }
        #caseDetailsModal .cs-modal-header{
          padding:12px 16px!important;
          border-top-left-radius:14px!important;
          border-top-right-radius:14px!important;
        }
        #caseDetailsModal .cs-modal-header h5{font-size:.95rem!important}
        #caseDetailsModal .cs-modal-body{padding:16px!important}
        #caseDetailsModal .cs-grid{grid-template-columns:1fr!important;gap:12px!important}
        #caseDetailsModal .cs-modal-footer{
          flex-direction:row!important;
          gap:10px!important;
          padding:12px 16px!important;
          border-bottom-left-radius:14px!important;
          border-bottom-right-radius:14px!important;
          justify-content:flex-end!important;
        }
        #caseDetailsModal .cs-footer-btns{width:auto!important;justify-content:flex-end!important}
        #caseDetailsModal .cs-footer-btns button{flex:unset!important;justify-content:center!important}
      }
      @media(max-width:480px){
        #caseDetailsModal{padding:8px!important}
        #caseDetailsModal .cs-modal-box{
          max-height:88vh!important;
          border-radius:12px!important;
        }
        #caseDetailsModal .cs-modal-header{
          padding:10px 14px!important;
          border-top-left-radius:12px!important;
          border-top-right-radius:12px!important;
        }
        #caseDetailsModal .cs-modal-header h5{font-size:.85rem!important}
        #caseDetailsModal .cs-modal-body{padding:12px!important}
        #caseDetailsModal .cs-modal-footer{
          border-bottom-left-radius:12px!important;
          border-bottom-right-radius:12px!important;
        }
      }
    </style>
    <div class="cs-modal-box" style="background:var(--background);border-radius:16px;width:100%;max-width:780px;max-height:75vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.15);overflow:hidden;">
      <!-- Header -->
      <div class="cs-modal-header" style="background:#1A237E;color:white;padding:14px 20px;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;">
        <h5 style="margin:0;font-size:1.05rem;font-weight:600;display:flex;align-items:center;gap:8px;">
          <i data-lucide="user-circle" style="width:20px;height:20px;"></i>
          Social Case Study Details
        </h5>
        <button onclick="document.getElementById('caseDetailsModal').remove()" style="width:32px;height:32px;border:none;background:rgba(255,255,255,0.15);border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s;color:white;" onmouseover="this.style.background='rgba(255,255,255,0.25)'" onmouseout="this.style.background='rgba(255,255,255,0.15)'">
          <i data-lucide="x" style="width:18px;height:18px;"></i>
        </button>
      </div>
      
      <!-- Body -->
      <div class="cs-modal-body" style="padding:20px;overflow-y:auto;flex:1;-webkit-overflow-scrolling:touch;">
        <div class="cs-grid" style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:16px;">
          
          <div style="margin-bottom:8px;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Control Number</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.controlNo)||"—"}</div>
          </div>
          
          <div style="margin-bottom:8px;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Date Created</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${fmtDate(caseRec.createdAt)}</div>
          </div>

          <div style="margin-bottom:8px;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Status</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);"><span class="badge ${STATUS_CLASS[caseRec.status]}">${caseRec.status}</span></div>
          </div>

          <div style="margin-bottom:8px;grid-column:1/-1;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Client Name</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.client?.name)||"—"}</div>
          </div>
          
          <div style="margin-bottom:8px;grid-column:1/-1;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Address</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.client?.address)||"—"}</div>
          </div>
          
          <div style="margin-bottom:8px;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Assistance Type</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.purpose)||"—"}</div>
          </div>
          
          <div style="margin-bottom:8px;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Age</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.client?.age)||"—"}</div>
          </div>
          
          <div style="margin-bottom:8px;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Civil Status</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.client?.civilStatus)||"—"}</div>
          </div>

          <div style="margin-bottom:8px;grid-column:1/-1;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Problem Presented</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);white-space:pre-wrap;">${escapeHtml(caseRec.interview?.interviewSituation || caseRec.interview?.problemPresented || caseRec.summary || "")||"—"}</div>
          </div>
        </div>
      </div>
      
      <!-- Footer -->
      <div class="cs-modal-footer" style="padding:16px 24px;border-top:1px solid var(--border);background:var(--surface);display:flex;justify-content:flex-end;gap:12px;flex-shrink:0;">
        <div class="cs-footer-btns" style="display:flex;gap:12px;justify-content:flex-end;">
          <button onclick="document.getElementById('caseDetailsModal').remove()" style="padding:8px 16px;background:var(--background);border:1px solid var(--border);border-radius:6px;font-weight:500;color:var(--text-primary);cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='var(--background)'">Close</button>
          ${(caseRec.status || '').toLowerCase() !== 'archived' && !window.location.pathname.includes('/archive') ? `
          <button onclick="window.location.href='/admin/social-case/detail/${caseRec.id}'" style="padding:8px 16px;background:var(--primary);border:none;border-radius:6px;font-weight:500;color:white;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background 0.2s;" onmouseover="this.style.background='#3730A3'" onmouseout="this.style.background='var(--primary)'">
            <i data-lucide="edit" style="width:16px;height:16px;"></i> Full Details / Edit
          </button>
          ` : ''}
        </div>
      </div>
    </div>
  `;
  document.body.appendChild(modal);
  
  // Close on backdrop click
  modal.addEventListener('click', e => { if(e.target === modal) modal.remove(); });
  
  lucide.createIcons();
}

/* ---------------- Helpers ---------------- */
function uid(){ return 'c'+Date.now().toString(36)+Math.random().toString(36).slice(2,7); }
function todayISO(){ const d = new Date(); return d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0'); }
function fmtDate(iso){
  if(!iso || iso === 'null' || iso === '') return "—";
  try {
    // Handle ISO datetime strings (with T) and date strings (without T)
    let dateStr = iso;
    if(!iso.includes('T') && !iso.includes('Z')) {
      dateStr = iso + "T00:00:00";
    }
    const d = new Date(dateStr);
    if(isNaN(d.getTime())) {
      console.warn('Invalid date format:', iso);
      return "—";
    }
    return d.toLocaleDateString('en-US',{month:'short',day:'numeric',year:'numeric'});
  } catch(e) {
    console.warn('Error formatting date:', iso, e);
    return "—";
  }
}
function daysBetween(a,b){ return Math.round((new Date(b)-new Date(a))/86400000); }
function escapeHtml(s){ return String(s==null?"":s).replace(/[&<>"']/g, c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c])); }

function extractSpecificNeed(rawText, purposeLabel) {
  if (!rawText || !purposeLabel) return '';
  const t = rawText.toLowerCase();
  const purpose = purposeLabel.toLowerCase();

  if (purpose.includes('medical')) {
    const needs = ['hemodialysis','chemotherapy','dialysis','surgery','hospitalization',
                   'medication','treatment','operation','therapy','check-up','checkup',
                   'medicine','maintenance','session','cebu doctors','treatment plan'];
    for (const n of needs) { if (t.includes(n)) return n; }
  }
  if (purpose.includes('burial')) {
    const needs = ['burial','funeral','interment','cremation'];
    for (const n of needs) { if (t.includes(n)) return n; }
  }
  if (purpose.includes('educational')) {
    const needs = ['tuition','school fees','enrollment','books','uniform'];
    for (const n of needs) { if (t.includes(n)) return n; }
  }
  if (purpose.includes('food') || purpose.includes('relief')) {
    const needs = ['food','relief','groceries','medicine'];
    for (const n of needs) { if (t.includes(n)) return n; }
  }
  if (purpose.includes('livelihood')) {
    const needs = ['livelihood','capital','equipment','business'];
    for (const n of needs) { if (t.includes(n)) return n; }
  }
  return '';
}
function rewriteProblemPresented(rawProblem, purpose, clientFullName, clientData = {}, household = []) {
  // --- Grammar utility helpers ---
  const capitaliseSentences = function(text) {
    return text.replace(/(^|[.!?]\s+)([a-z])/g, function(m, sep, letter) {
      return sep + letter.toUpperCase();
    });
  };
  const fixFirstPerson = function(text) {
    // standalone lowercase "i" -> "I"
    return text.replace(/(?<![a-zA-Z])i(?![a-zA-Z])/g, 'I');
  };
  const fixArticles = function(text) {
    text = text.replace(/\ba\s+([AEIOUaeiou][a-z])/g, 'an $1');
    text = text.replace(/\ban\s+([^AEIOUaeiou\s])/g, 'a $1');
    return text;
  };
  const fixSpacing = function(text) {
    text = text.replace(/\s+([.,!?;:])/g, '$1');
    text = text.replace(/([.!?;:])([^\s"'\d)\]])/g, '$1 $2');
    text = text.replace(/\s{2,}/g, ' ');
    return text.trim();
  };
  const fixCommonPhrases = function(text) {
    text = text.replace(/\bchemo\s*therapy\b/gi, 'chemotherapy');
    text = text.replace(/\bNEPHRO?S?CLEROSIS\b/gi, 'nephrosclerosis');
    text = text.replace(/\bdiabetes\s+mellitus\b/gi, 'Diabetes Mellitus');
    text = text.replace(/\bchronic\s+kidney\s+disease\b/gi, 'Chronic Kidney Disease');
    text = text.replace(/\brenal\s+failure\b/gi, 'Renal Failure');
    text = text.replace(/\bSocial Case Study\b(?!\s+Report)/gi, 'Social Case Study Report');
    text = text.replace(/\b(financial|medical|burial|educational|food\s*\/?\s*relief|livelihood)\s+assistance\b/gi, function(m) {
      return m.replace(/\b\w/g, c => c.toUpperCase());
    });
    text = text.replace(/\bAssistance\s*\/?\s*Support\b/gi, 'Assistance');
    text = text.replace(/\b(her|his|their|my)\s+(mother|father|sister|brother|wife|husband|son|daughter|spouse|parent)\s+(maintenance|treatment|medication|hemodialysis|dialysis|surgery|therapy|operation|hospitalization|care|chemotherapy|session|checkup|check-up|medicine)\b/gi, '$1 $2\'s $3');
    text = text.replace(/\bneeded\s+in\b/gi, 'needed for');
    text = text.replace(/\bneeded\s+due\s+to\b/gi, 'needed for');
    text = text.replace(/\bPlease\s+see\s+(the\s+)?attachments?\b/gi, 'Please see the attached documents');
    text = text.replace(/\bPlease\s+see\s+(the\s+)?attachments?\s+for\b/gi, 'Please see the attached documents for');
    text = text.replace(/\bthru\b/gi, 'through');
    text = text.replace(/\bw\/\b/g, 'with');
    text = text.replace(/\bw\/o\b/g, 'without');
    text = text.replace(/\basst\.?\b/gi, 'assistance');
    text = text.replace(/\bpls\b/gi, 'please');
    text = text.replace(/\bwrt\b/gi, 'with regard to');
    text = text.replace(/\bpursuant\s+of\b/gi, 'pursuant to');
    text = text.replace(/\bin\s+regards\s+to\b/gi, 'with regard to');
    text = text.replace(/\bin\s+relation\s+of\b/gi, 'in relation to');
    text = text.replace(/\bwould like to request\b/gi, 'is requesting');
    text = text.replace(/\bwants to request\b/gi, 'is requesting');
    // Fix comma-splice: "...Medical Assistance, she is also requesting" -> "...Medical Assistance. She is also requesting"
    text = text.replace(/([a-z]),\s*(she|he|they|the patient|the client|the deceased)\s+is/gi, '$1. $2 is');
    return text;
  };


  if (!rawProblem || !rawProblem.trim()) return "";
  const purposeLabel = purpose || "Financial Assistance";
  const clientRef = clientFullName || "The client";
  const visitor = (household || [])[0] || {};
  const visitorRelationship = (visitor.relationship || "").trim().toLowerCase();

  // Map purpose to the correct subject term
  const subjectMap = {
    'medical assistance': { subject: 'patient', possessive: "patient's" },
    'burial assistance':  { subject: 'deceased', possessive: "deceased's" },
  };
  const subjectInfo = subjectMap[purposeLabel.toLowerCase()] || { subject: 'client', possessive: "client's" };

  // Possessive adjective used for the need phrase, e.g. "for her tuition fee"
  const clientSex = (clientData.sex || clientData.gender || "").toLowerCase();
  const possAdj = clientSex === 'male' ? 'his' : clientSex === 'female' ? 'her' : "the client's";

  // Fix "assistance for tuition fee" -> "assistance for her tuition fee"
  const fixNeedPhrase = function(phrase) {
    return phrase.replace(
      /\bfor\s+(tuition\s+fees?|tuition|school\s+fees?|medication|medicines?|dialysis|hemodialysis|treatment|hospitalization|surgery|operation|maintenance|therapy|checkups?|check-ups?|daily\s+expenses?|expenses?|costs?|fees?)\b/gi,
      function(m, item) { return "for " + possAdj + " " + item.toLowerCase(); }
    );
  };

  // --- Phase 1: Grammar / terminology fixes (preserve the officer's wording) ---

  let p = rawProblem.trim();
  p = fixCommonPhrases(p);
  p = fixFirstPerson(p);
  p = fixArticles(p);
  p = fixSpacing(p);
  p = capitaliseSentences(p);
  // Ensure text ends with a sentence-ending punctuation
  if (p && !/[.!?]$/.test(p)) p = p + '.';

  // --- Phase 2: Analyse the typed text ---

  // Does the typed text already tell the visit story?
  const hasVisit = /\b(visited|went to|came to|approached|personally visited|goes to|went personally)\b/i.test(p);
  // Bare need statement, e.g. "need financial assistance for tuition fee"
  const bareNeed = p.match(/^(?:i\s+|we\s+)?need(?:s|ed)?\s+(?:a\s+|an\s+|the\s+)?/i);
  // e.g. "financial assistance for tuition fee" (no "need" verb)
  const startsWithAssistance = /^(?:financial|medical|burial|educational|food\s*\/?\s*relief|livelihood)\s+assistance\b/i.test(p);

  // --- Phase 3: Build the opening sentence ---

  let opening;
  if (hasVisit) {
    // Keep the officer's own words — they already describe who came and why
    opening = fixNeedPhrase(p);
  } else {
    const who = visitorRelationship
      ? `The ${subjectInfo.possessive} ${visitorRelationship}`
      : clientRef;
    opening = `${who} personally visited our office to request assistance in obtaining a Social Case Study Report for ${purposeLabel}.`;
  }

  // --- Phase 4: Build the need sentence straight from the typed text ---

  let needSentence = '';
  if (!hasVisit && p) {
    if (bareNeed) {
      // "need financial assistance for tuition fee" -> "The client needs Financial Assistance for her tuition fee"
      const rest = fixNeedPhrase(p.slice(bareNeed[0].length).trim());
      needSentence = `The ${subjectInfo.subject} needs ${rest.charAt(0).toUpperCase()}${rest.slice(1)}`;
    } else if (startsWithAssistance) {
      // "financial assistance for tuition fee" -> "The client needs Financial Assistance for her tuition fee"
      needSentence = `The ${subjectInfo.subject} needs ${fixNeedPhrase(p.charAt(0).toUpperCase() + p.slice(1))}`;
    } else {
      // Full typed sentence — keep it verbatim (grammar-fixed)
      needSentence = fixNeedPhrase(p.charAt(0).toUpperCase() + p.slice(1));
    }
  }

  // --- Phase 5: Combine ---

  let paraphrased = opening + (needSentence ? ' ' + needSentence : '');

  // --- Phase 6: Ensure closing sentence ---

  if (!/\b(please see|attached documents|supporting documents|for your (reference|review)|supporting this request)\b/i.test(paraphrased)) {
    paraphrased = paraphrased.replace(/\.\s*$/, '') + '. Please see the attached documents for your reference.';
  }

  // --- Phase 7: Final cleanup ---

  paraphrased = capitaliseSentences(paraphrased);
  paraphrased = paraphrased.replace(/\s+/g, ' ').trim();
  paraphrased = paraphrased.replace(/\.\./g, '.');
  paraphrased = paraphrased.replace(/,\s*\./g, '.');
  paraphrased = paraphrased.replace(/\.\s*\./g, '.');
  paraphrased = paraphrased.replace(/\.\s*$/, '.');
  if (paraphrased) paraphrased = paraphrased.charAt(0).toUpperCase() + paraphrased.slice(1);

  return paraphrased;
}
function boldProblemText(text) {
  if (!text) return text;
  const purposes = ['Medical Assistance','Burial Assistance','Educational Assistance','Financial Assistance','Food / Relief Assistance','Livelihood Assistance'];
  let t = text;
  // Bold "Social Case Study" and "Social Case Study Report"
  t = t.replace(/\b(Social Case Study Report)\b/g, '<b>$1</b>');
  t = t.replace(/(?<!<b>)\b(Social Case Study)\b(?!<\/b>)/g, '<b>$1</b>');
  // Bold assistance type names
  purposes.forEach(p => {
    const re = new RegExp('\\b(' + p.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')\\b', 'g');
    t = t.replace(re, '<b>$1</b>');
  });
  // Bold consecutive ALL-CAPS words (medical conditions, diagnoses)
  t = t.replace(/\b([A-Z][A-Z0-9]+(?:\s+[A-Z][A-Z0-9]+)+)\b/g, function(match) {
    if (/<b>/.test(match)) return match;
    return '<b>' + match + '</b>';
  });
  return t;
}
function findLatestByName(name){
  const n = normalizeClientName(name);
  if(!n) return null;
  const matches = cases.filter(c => normalizeClientName(c.client.name) === n && c.releasedDate);
  if(!matches.length) return null;
  matches.sort((a,b)=> new Date(b.releasedDate) - new Date(a.releasedDate));
  return matches[0];
}

function checkEligibility(clientName){
  const parsed = parseClientName(clientName);
  if(!parsed.normalized) return {eligible: true, reason: ''};

  const inputParts = parsed.parts;

  // Find all cases whose normalized name shares at least first+last with the input
  const allMatches = cases.filter(c => {
    const cParsed = parseClientName(c.client.name);
    // Exact normalized full-name match
    if(cParsed.normalized === parsed.normalized) return true;
    // Must have same last name AND genuine given name match
    if(cParsed.lastName === parsed.lastName && hasGivenNameMatch(parsed.parts, cParsed.parts, parsed.lastName)) return true;
    return false;
  });
  
  if(allMatches.length === 0) {
    return {eligible: true, reason: ''};
  }
  
  // Find the latest case (by releasedDate or createdAt)
  const latest = allMatches.sort((a,b) => {
    const dateA = new Date(a.releasedDate || a.createdAt);
    const dateB = new Date(b.releasedDate || b.createdAt);
    return dateB - dateA;
  })[0];
  
  const caseDate = latest.releasedDate || latest.createdAt;
  const daysSince = Math.floor((new Date() - new Date(caseDate)) / (1000 * 60 * 60 * 24));
  const daysRemaining = 180 - daysSince;
  
  // Check if the case is within the 6-month restriction period
  if(daysRemaining > 0) {
    return {
      eligible: false,
      reason: `Client received assistance on ${fmtDate(caseDate)}. Must wait ${daysRemaining} more days (6-month rule).`,
      latestCase: latest,
      daysRemaining: daysRemaining
    };
  }
  
  // If past the 6-month period, they are eligible
  return {
    eligible: true,
    reason: `Last case was on ${fmtDate(caseDate)} (${daysSince} days ago). Eligible for new case.`,
    latestCase: latest
  };
}

function eligibilityInfo(caseRec){
  console.log('eligibilityInfo called with:', caseRec.releasedDate, caseRec.createdAt);
  const caseDate = caseRec.releasedDate || caseRec.createdAt;
  
  if(!caseRec || !caseDate) {
    console.log('No case date, returning eligible');
    return {eligible:true, daysSince:0, daysLeft:0, nextEligibleDate:null, pct:0};
  }
  
  // Validate the case date before processing
  try {
    const testDate = new Date(caseDate);
    console.log('Test date:', testDate, 'getTime:', testDate.getTime(), 'isNaN:', isNaN(testDate.getTime()));
    if(isNaN(testDate.getTime())) {
      console.warn('Invalid case date:', caseDate);
      return {eligible:true, daysSince:0, daysLeft:0, nextEligibleDate:null, pct:0};
    }
  } catch(e) {
    console.warn('Error parsing case date:', caseDate, e);
    return {eligible:true, daysSince:0, daysLeft:0, nextEligibleDate:null, pct:0};
  }
  
  const daysSince = daysBetween(caseDate, todayISO());
  const daysLeft = ELIGIBILITY_DAYS - daysSince;
  const nextDate = new Date(caseDate);
  nextDate.setDate(nextDate.getDate()+ELIGIBILITY_DAYS);
  
  console.log('daysSince:', daysSince, 'daysLeft:', daysLeft, 'nextDate:', nextDate);
  
  // Validate nextDate before calling toISOString
  if(isNaN(nextDate.getTime())) {
    console.warn('Invalid nextDate calculated');
    return {
      eligible: daysLeft <= 0,
      daysSince, daysLeft: Math.max(daysLeft,0),
      nextEligibleDate: null,
      pct: Math.min(100, Math.round((daysSince/ELIGIBILITY_DAYS)*100))
    };
  }
  
  const nextEligibleDate = nextDate.toISOString().slice(0,10);
  console.log('nextEligibleDate:', nextEligibleDate);
  
  return {
    eligible: daysLeft <= 0,
    daysSince, daysLeft: Math.max(daysLeft,0),
    nextEligibleDate: nextEligibleDate,
    pct: Math.min(100, Math.round((daysSince/ELIGIBILITY_DAYS)*100))
  };
}
function setView(patch){ view = {...view, ...patch}; }

function selectTemplateTab(agencyKey, tabElement){
  selectedAgency = agencyKey;
  // Update active state
  document.querySelectorAll('.template-tab').forEach(tab => tab.classList.remove('active'));
  tabElement.classList.add('active');
  // Reload preview with new agency
  loadDocumentPreview(view.caseId);
}

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
    caseId: null,
    status: "Review",
    createdAt: today,
    updatedAt: today,
    controlNo: generateControlNo(today),
    client: {name: name||"", age:"", sex:"", address:"", birthdate:"", birthplace:"", religion:"", education:"", civilStatus:"", occupation:"", income:"", contact:""},
    household: [{name:"", relationship:"", age:"", education:"", occupation:"", income:""}],
    interview: {reportDate: today, problemPresented:"", homeCondition:"", socioEconomic:"", evaluation:"", recommendation:""},
    signers: {preparedByName: CURRENT_USER_NAME, preparedByTitle:"MSWDO Staff", notedByName: ADMIN_NAME || CURRENT_USER_NAME, notedByTitle:"MSWDO Head", notedByLicense:""},
    purpose: PURPOSES[0],
    agencies: [],
    requirements: DEFAULT_REQUIREMENTS.map(r=>({name:r, submitted:false})),
    statusHistory: [{status:"Review", date: today}],
    releasedDate: null
  };
}

function proceedToIntake(caseId = null, clientName = null){
  const name = clientName || view.eligClientName || '';
  draftIntake = blankIntake(name);
  sessionStorage.setItem('intake_clientName', name);
  if(caseId){
    sessionStorage.setItem('intake_caseId', caseId);
  } else {
    sessionStorage.removeItem('intake_caseId');
  }
  window.location.href = '/admin/social-case/intake';
}

async function submitForEncoding(){
  const name = (view.eligClientName || '').trim();
  if(!name){
    Swal.fire({icon:'warning', title:'No client selected', text:'Please search and select a client first.', confirmButtonColor:'#1A237E'});
    return;
  }

  // Show quick loading while fetching available encoders
  Swal.fire({
    title: 'Loading Accounts...',
    html: '<div style="padding:14px 0;color:#6B7280;font-size:14px;">Fetching social case encoder accounts...</div>',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    didOpen: () => {
      Swal.showLoading();
    }
  });

  let encoders = [];
  try {
    const encRes = await fetch('/admin/social-case/api/encoders');
    if(encRes.ok){
      encoders = await encRes.json();
    } else {
      throw new Error('Failed to load encoders list');
    }
  } catch(err){
    console.error('Error fetching encoders:', err);
    Swal.fire({
      title: 'Error',
      text: 'Could not load encoder accounts. Please try again or check your connection.',
      icon: 'error',
      confirmButtonColor: '#DC2626'
    });
    return;
  }

  if(!encoders || encoders.length === 0){
    Swal.fire({
      title: 'No Encoder Accounts Found',
      text: 'There are no active accounts configured for social case encoding.',
      icon: 'warning',
      confirmButtonColor: '#1A237E'
    });
    return;
  }

  const encodersListHtml = encoders.map((enc, index) => {
    const isChecked = index === 0 ? 'checked' : '';
    const initials = (enc.name || 'SC')
      .split(' ')
      .map(w => w[0])
      .filter(Boolean)
      .slice(0, 2)
      .join('')
      .toUpperCase();

    return `
      <label class="encoder-option-card ${index === 0 ? 'selected' : ''}" for="enc_opt_${enc.id}" style="display:flex;align-items:center;gap:14px;padding:12px 16px;border:2px solid ${index === 0 ? '#1A237E' : '#E2E8F0'};border-radius:10px;margin-bottom:8px;cursor:pointer;background:${index === 0 ? '#F8FAFC' : '#FFFFFF'};transition:all .15s ease;text-align:left;user-select:none;">
        <input type="radio" id="enc_opt_${enc.id}" name="selected_encoder" value="${enc.id}" ${isChecked} style="width:18px;height:18px;accent-color:#1A237E;cursor:pointer;margin:0;flex-shrink:0;">
        <div style="width:38px;height:38px;border-radius:50%;background:#1A237E;color:#FFFFFF;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:14px;flex-shrink:0;">
          ${escapeHtml(initials)}
        </div>
        <div style="flex:1;min-width:0;">
          <span style="font-size:15px;font-weight:600;color:#0F172A;">${escapeHtml(enc.name)}</span>
        </div>
      </label>
    `;
  }).join('');

  const modalResult = await Swal.fire({
    title: 'Select Case Encoder',
    html: `
      <div style="text-align:left;padding:0 2px;">
        <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:8px;padding:10px 14px;margin-bottom:14px;display:flex;align-items:center;gap:10px;">
          <div style="width:24px;height:24px;border-radius:50%;background:#16A34A;color:#fff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0;">✓</div>
          <div style="font-size:13px;color:#166534;line-height:1.4;">
            Client <strong>${escapeHtml(name)}</strong> is eligible. Choose a Case Encoding account to forward this client to:
          </div>
        </div>
        <div id="encodersSelectionContainer" style="max-height:300px;overflow-y:auto;padding-right:4px;">
          ${encodersListHtml}
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: 'Submit to Selected Encoder',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#1A237E',
    cancelButtonColor: '#6B7280',
    background: '#ffffff',
    customClass: {
      popup: 'rounded-4 shadow-lg'
    },
    didOpen: () => {
      const container = document.getElementById('encodersSelectionContainer');
      if(container){
        const cards = container.querySelectorAll('.encoder-option-card');
        cards.forEach(card => {
          card.addEventListener('click', () => {
            cards.forEach(c => {
              c.style.borderColor = '#E2E8F0';
              c.style.background = '#FFFFFF';
            });
            card.style.borderColor = '#1A237E';
            card.style.background = '#F8FAFC';
            const radio = card.querySelector('input[type="radio"]');
            if(radio) radio.checked = true;
          });
        });
      }
    },
    preConfirm: () => {
      const selectedRadio = document.querySelector('input[name="selected_encoder"]:checked');
      if(!selectedRadio){
        Swal.showValidationMessage('Please select an encoder account');
        return false;
      }
      return selectedRadio.value;
    }
  });

  if(!modalResult.isConfirmed || !modalResult.value) return;

  const selectedEncoderId = modalResult.value;
  const selectedEncoder = encoders.find(e => String(e.id) === String(selectedEncoderId));
  const encoderName = selectedEncoder ? selectedEncoder.name : 'the case encoder';

  try {
    const response = await fetch('/admin/social-case/api/eligibility/submit', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        client_name: name,
        override: !!view.eligOverride,
        encoder_id: selectedEncoderId
      })
    });

    const data = await response.json();
    if(!response.ok){
      Swal.fire({
        title: 'Cannot Submit',
        html: escapeHtml(data.error || 'Failed to submit the client. Please try again.'),
        icon: 'error',
        confirmButtonColor: '#DC2626'
      });
      return;
    }

    const assignedName = data.assigned_officer?.name || encoderName;

    logActivity('created', `Client forwarded to ${assignedName} for case encoding`, {
      clientName: data.case?.client?.name || name,
      controlNo: data.case?.case_number || '',
      assignedEncoder: assignedName
    });

    Swal.fire({
      title: 'Forwarded Successfully!',
      html: `<strong>${escapeHtml(data.case?.client?.name || name)}</strong> was successfully forwarded and assigned to <strong>${escapeHtml(assignedName)}</strong> for case encoding.`,
      icon: 'success',
      confirmButtonColor: '#1A237E'
    });

    await loadCases();

    setView({eligMatch: null, selectedClient: null, eligClientName: '', checkerResult: null, eligOverride: false});
    const searchInput = document.getElementById('elig-name');
    if(searchInput) searchInput.value = '';
    const results = document.getElementById('searchResults');
    if(results) results.style.display = 'none';
    const status = document.getElementById('eligibilityStatus');
    if(status) status.innerHTML = '';

    if(CAN_ENCODE) renderEncoderQueue();
    lucide.createIcons();
  } catch(e){
    console.error('Error submitting eligibility:', e);
    Swal.fire({title:'Error', text:'Failed to submit the client. Please try again.', icon:'error', confirmButtonColor:'#DC2626'});
  }
}

function updateWorkflowStep(stepNumber){
  // Reset all steps
  for(let i = 1; i <= 4; i++){
    const stepEl = document.getElementById(`workflowStep${i}`);
    if(stepEl) stepEl.classList.remove('active', 'completed');
  }
  
  // Set current step as active
  const currentStep = document.getElementById(`workflowStep${stepNumber}`);
  if(currentStep) currentStep.classList.add('active');
  
  // Mark previous steps as completed
  for(let i = 1; i < stepNumber; i++){
    const prevStep = document.getElementById(`workflowStep${i}`);
    if(prevStep) prevStep.classList.add('completed');
  }
}

function saveNewCase(){
  // Convert camelCase JS keys to snake_case for the Laravel API
  const payload = convertKeys(draftIntake, camelToSnake);
  const onlineRequestId = sessionStorage.getItem('intake_onlineRequestId');
  if (onlineRequestId) {
    payload.online_request_id = onlineRequestId;
    sessionStorage.removeItem('intake_onlineRequestId');
  }
  // Include case_id if editing an existing case
  if (draftIntake.caseId) {
    payload.case_id = draftIntake.caseId;
  }
  console.log('Saving case (payload):', payload);
  fetch('/admin/social-case/api/cases', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      'Accept': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(response => {
    console.log('Response status:', response.status);
    return response.text().then(text => {
      console.log('Response text:', text);
      if(!response.ok){
        if(response.status === 422){
          try {
            const json = JSON.parse(text);
            const msgs = Object.values(json.errors || {}).flat();
            throw new Error('Validation failed:\n' + msgs.join('\n'));
          } catch(e) {
            if(e.message.startsWith('Validation')) throw e;
          }
        }
        if(response.status === 409){
          try {
            const json = JSON.parse(text);
            throw new Error(json.error || 'Case already exists');
          } catch(e) {
            if(e.message !== 'Case already exists') throw e;
          }
        }
        console.error('Server returned error:', response.status, text);
        throw new Error('Server error: ' + response.status);
      }
      try {
        return JSON.parse(text);
      } catch(e) {
        console.error('Failed to parse JSON:', e);
        throw new Error('Invalid JSON response');
      }
    });
  })
  .then(data => {
    console.log('Case saved:', data);
    logActivity('created', 'New case created', {
      clientName: draftIntake.client?.name,
      controlNo: draftIntake.controlNo
    });
    updateWorkflowStep(4);
    draftIntake = null;
    window.location.href = `/admin/social-case/detail/${data.id}`;
  })
  .catch(error => {
    console.error('Error saving case:', error);
    alert('Failed to save case. Please try again.');
  });
}

/* ---------------- Case actions ---------------- */
function advanceStatus(caseRec){
  const idx = STATUSES.indexOf(caseRec.status);
  if(idx < STATUSES.length-1){
    const oldStatus = caseRec.status;
    caseRec.status = STATUSES[idx+1];
    caseRec.updatedAt = todayISO();
    caseRec.statusHistory.push({status:caseRec.status, date: todayISO()});
    if(caseRec.status === "Released"){ caseRec.releasedDate = todayISO(); }
    saveCases();
    logActivity('updated', `Status changed from ${oldStatus} to ${caseRec.status}`, {
      clientName: caseRec.client?.name,
      controlNo: caseRec.controlNo
    });
    renderCaseDetail();
  }
}
function revertStatus(caseRec){
  const idx = STATUSES.indexOf(caseRec.status);
  if(idx > 0){
    const oldStatus = caseRec.status;
    caseRec.status = STATUSES[idx-1];
    caseRec.updatedAt = todayISO();
    caseRec.statusHistory.push({status:caseRec.status+" (reverted)", date: todayISO()});
    if(caseRec.status !== "Released"){ caseRec.releasedDate = null; }
    saveCases();
    logActivity('updated', `Status reverted from ${oldStatus} to ${caseRec.status}`, {
      clientName: caseRec.client?.name,
      controlNo: caseRec.controlNo
    });
    renderCaseDetail();
  }
}
function deleteCase(id, fromList = false){
  Swal.fire({
    title: 'Archive this case?',
    text: 'This will move the case to the archive. You can still view it but it will be removed from the active cases list.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#1A237E',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Yes, Archive',
    cancelButtonText: 'Cancel',
    background: '#ffffff',
    customClass: { popup: 'rounded-4 shadow-lg' }
  }).then((result) => {
    if (result.isConfirmed) {
      const caseRec = getCase(id);
      if(caseRec){
        caseRec.status = 'Archived';
        caseRec.updatedAt = todayISO();
        if(!caseRec.statusHistory) caseRec.statusHistory = [];
        caseRec.statusHistory.push({status: 'Archived', date: todayISO()});

        const payload = convertKeys(caseRec, camelToSnake);
        fetch(`/admin/social-case/api/cases/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload)
        })
        .then(response => {
          console.log('Archive response status:', response.status);
          return response.text().then(text => {
            console.log('Archive response text:', text);
            if(!response.ok){
              throw new Error('Server error: ' + response.status);
            }
            try {
              return JSON.parse(text);
            } catch(e) {
              console.error('Failed to parse JSON:', e);
              throw new Error('Invalid JSON response');
            }
          });
        })
        .then(async data => {
          console.log('Case archived:', data);
          logActivity('archived', 'Case archived', {
            clientName: caseRec.client?.name,
            controlNo: caseRec.controlNo
          });
          await Swal.fire({
            title: 'Archived!',
            text: 'The case has been archived successfully.',
            icon: 'success',
            timer: 1800,
            showConfirmButton: false,
            confirmButtonColor: '#1E3A8A'
          });
          // If called from the case list, reload & re-render in place
          if(fromList || document.getElementById('casesTableBody')){
            await loadCases();
            renderCaseList();
          } else {
            window.location.href = '/admin/social-case/cases';
          }
        })
        .catch(error => {
          console.error('Error archiving case:', error);
          Swal.fire({
            title: 'Error',
            text: 'Failed to archive the case. Please try again.',
            icon: 'error',
            confirmButtonColor: '#DC2626'
          });
        });
      }
    }
  });
}
function getCase(id){ 
  console.log('getCase called with id:', id, 'type:', typeof id);
  console.log('Looking through cases:', cases.map(c => ({id: c.id, idType: typeof c.id, stringId: String(c.id)})));
  const found = cases.find(c=>String(c.id) === String(id));
  console.log('Found case:', found);
  return found;
}

/* ---------------- Rendering: Sidebar ---------------- */
function renderSidebar(activeTab){
  const items = [
    {tab:"dashboard", icon:"layout-dashboard", label:"Dashboard", url:"/admin/social-case/dashboard"},
    {tab:"newCase", icon:"user-plus", label:"New case", url:"/admin/social-case/new"},
    {tab:"caseList", icon:"list", label:"All cases", url:"/admin/social-case/cases"},
  ];
  return `
  <div class="sidebar">
    <div class="brand">
      <div class="brand-mark">SC</div>
      <div class="brand-text"><b>Case Study System</b><span>MSWDO</span></div>
    </div>
    ${items.map(it=>`
      <button class="nav-item ${activeTab===it.tab?'active':''}" onclick="window.location.href='${it.url}'">
        <i data-lucide="${it.icon}" style="width:20px;height:20px"></i> ${it.label}
      </button>`).join("")}
    <div class="sidebar-foot">Data is stored on this device only.</div>
  </div>`;
}

/* ---------------- Rendering: Dashboard ---------------- */
async function loadArchive(){
  await loadCases();
  populateArchiveBarangays();
  renderArchive();
  lucide.createIcons();
}

function populateArchiveBarangays(){
  const menu = document.getElementById('archiveBrgyMenu');
  if(!menu) return;
  menu.innerHTML = '<div class="archive-brgy-opt" data-value="" onclick="selectArchiveBrgy(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">All Barangays</div>' +
    BARANGAYS.map(b => `<div class="archive-brgy-opt" data-value="${escapeHtml(b)}" onclick="selectArchiveBrgy(this)" style="padding:8px 12px;border-radius:6px;font-size:14px;cursor:pointer;transition:background .15s">${escapeHtml(b)}</div>`).join('');
  highlightArchiveBrgyOpt();
}

function renderArchive(){
  let archivedCases = cases.filter(c => c.status === 'Archived');

  const q = (view.archiveSearch || '').toLowerCase();
  if(q){
    archivedCases = archivedCases.filter(c =>
      ((c.client?.name) || '').toLowerCase().includes(q) ||
      (c.controlNo || '').toLowerCase().includes(q)
    );
  }
  const f = view.archiveFilter || '';
  if(f){
    archivedCases = archivedCases.filter(c => c.purpose === f);
  }
  const b = view.archiveBarangay || '';
  if(b){
    archivedCases = archivedCases.filter(c => (c.client?.address) === b);
  }

  const table = document.getElementById('archiveTable');
  
  if(archivedCases.length === 0){
    const hasFilters = q || f || b;
    table.innerHTML = `<tr class="empty-row"><td colspan="6" class="empty-cell">
      <div class="empty-state-content">
        <div class="empty-icon-wrap">
          <i data-lucide="${hasFilters ? 'search-x' : 'archive'}"></i>
        </div>
        <div class="empty-title">${hasFilters ? 'No matching archived cases' : 'No archived cases'}</div>
        <div class="empty-subtitle">${hasFilters ? 'Try adjusting your search or filter' : 'Archived cases will appear here'}</div>
      </div>
    </td></tr>`;
    const pagInfo = document.getElementById('archivePaginationInfo');
    if(pagInfo) pagInfo.textContent = 'Showing 0 of 0 Archived Cases';
    const pagControls = document.getElementById('archivePaginationControls');
    if(pagControls) {
      pagControls.innerHTML = `
        <button class="sc-page-btn" disabled><i data-lucide="chevron-left" style="width:14px;height:14px"></i> Previous</button>
        <button class="sc-page-btn active">1</button>
        <button class="sc-page-btn" disabled>Next <i data-lucide="chevron-right" style="width:14px;height:14px"></i></button>
      `;
    }
  } else {
    // Pagination
    const pageSize = 10;
    const page = view.archivePage || 1;
    const totalPages = Math.ceil(archivedCases.length / pageSize);
    if(page > totalPages) view.archivePage = totalPages;
    const currentPage = Math.max(1, Math.min(view.archivePage || 1, totalPages));
    const startIndex = (currentPage - 1) * pageSize;
    const endIndex = startIndex + pageSize;
    const paginatedCases = archivedCases.slice(startIndex, endIndex);

    table.innerHTML = paginatedCases.map(c => `
      <tr>
        <td data-label="Control No"><span style="font-family:monospace;font-weight:600">${escapeHtml(c.controlNo)||"—"}</span></td>
        <td data-label="Client">${escapeHtml(c.client?.name)||"<span class=muted>Unnamed</span>"}</td>
        <td data-label="Type">${escapeHtml(c.purpose)}</td>
        <td data-label="Status"><span class="badge b-archived">${c.status}</span></td>
        <td data-label="Date">${fmtDate(c.updatedAt)}</td>
        <td data-label="Action">
          <div class="actions" style="display:flex; gap: 4px;">
            <button style="background-color: #1A237E; border: none; border-radius: 6px; padding: 6px 10px; cursor:pointer;" onclick="event.stopPropagation(); showCaseDetailsModal('${c.id}')" title="View">
              <i data-lucide="eye" style="width:16px;height:16px; color:#ffffff;"></i>
            </button>
            <button style="background-color: rgba(20,184,166,0.1); color: #0f766e; border: 1px solid rgba(20,184,166,0.3); border-radius: 6px; padding: 6px 10px; cursor:pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='rgba(20,184,166,0.2)'" onmouseout="this.style.backgroundColor='rgba(20,184,166,0.1)'" onclick="event.stopPropagation(); restoreCase('${c.id}')" title="Restore">
              <i data-lucide="rotate-ccw" style="width:16px;height:16px;"></i>
            </button>
          </div>
        </td>
      </tr>
    `).join('');

    // Pagination info
    const pagInfo = document.getElementById('archivePaginationInfo');
    if(pagInfo){
      const showingFrom = startIndex + 1;
      const showingTo = Math.min(endIndex, archivedCases.length);
      pagInfo.textContent = `Showing ${showingFrom}–${showingTo} of ${archivedCases.length} Archived Cases`;
    }

    // Pagination controls
    const pagControls = document.getElementById('archivePaginationControls');
    if(pagControls){
      let pageButtons = '';
      pageButtons += `<button class="sc-page-btn" ${currentPage<=1?'disabled':''} onclick="goToArchivePage(${currentPage-1})"><i data-lucide="chevron-left" style="width:14px;height:14px"></i> Previous</button>`;
      
      // Always show current page
      pageButtons += `<button class="sc-page-btn active" onclick="goToArchivePage(${currentPage})">${currentPage}</button>`;
      
      pageButtons += `<button class="sc-page-btn" ${currentPage>=totalPages?'disabled':''} onclick="goToArchivePage(${currentPage+1})">Next <i data-lucide="chevron-right" style="width:14px;height:14px"></i></button>`;
      pagControls.innerHTML = pageButtons;
    }
  }

  lucide.createIcons();
}

function goToArchivePage(page){
  view.archivePage = page;
  renderArchive();
}

function restoreCase(id){
  Swal.fire({
    title: 'Restore this case?',
    html: 'Are you sure you want to restore this case back to <strong>active status</strong>?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#0f766e',
    cancelButtonColor: '#EF4444',
    confirmButtonText: 'Yes, Restore',
    cancelButtonText: 'Cancel',
    background: '#ffffff',
    customClass: { popup: 'rounded-4 shadow-lg' }
  }).then((result) => {
    if (result.isConfirmed) {
      const caseRec = getCase(id);
      if(caseRec){
        caseRec.status = 'Draft';
        caseRec.updatedAt = todayISO();
        if(!caseRec.statusHistory) caseRec.statusHistory = [];
        caseRec.statusHistory.push({status: 'Restored to Draft', date: todayISO()});

        const payload = convertKeys(caseRec, camelToSnake);
        fetch(`/admin/social-case/api/cases/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(async data => {
          console.log('Case restored:', data);
          logActivity('restored', 'Case restored from archive', {
            clientName: caseRec.client?.name,
            controlNo: caseRec.controlNo
          });
          await Swal.fire({
            title: 'Restored!',
            text: 'The case has been restored and moved back to the active list.',
            icon: 'success',
            timer: 1800,
            showConfirmButton: false
          });
          await loadCases();
          renderArchive();
        })
        .catch(error => {
          console.error('Error restoring case:', error);
          Swal.fire({
            title: 'Error',
            text: 'Failed to restore the case. Please try again.',
            icon: 'error',
            confirmButtonColor: '#DC2626'
          });
        });
      }
    }
  });
}

async function loadDashboard(){
  console.log('Loading dashboard...');
  try {
    const userRole = document.querySelector('meta[name="user-role"]')?.content;
    const isEligibilityChecker = userRole === 'eligibility_checker';

    if (isEligibilityChecker) {
      await loadEligibilityData();
    } else {
      await loadCases();
    }

    console.log('Dashboard data loaded:', cases.length);
    renderDashboard();
    lucide.createIcons();
    initCharts();
  } catch(e) {
    console.error('Error loading dashboard:', e);
  }
}

function dashboardCases(){
  return cases.filter(c => {
    const isDone = c.status === 'Printed' || c.status === 'Released';
    const hasPurpose = c.purpose != null && String(c.purpose).trim() !== '' && c.purpose !== 'null';
    return isDone && hasPurpose;
  });
}

function renderDashboard(){
  const userRole = document.querySelector('meta[name="user-role"]')?.content;
  const isEligibilityChecker = userRole === 'eligibility_checker';

  if (!isEligibilityChecker) {
    // Case Encoder Dashboard - only render charts
    const done = dashboardCases();
    const byStatus = {};
    STATUSES.forEach(s=> byStatus[s] = done.filter(c=>c.status===s).length);
    const nearingEligible = done.filter(c=>{
      if(!c.releasedDate) return false;
      const e = eligibilityInfo(c);
      return !e.eligible && e.daysLeft <= 30;
    }).sort((a,b)=> eligibilityInfo(a).daysLeft - eligibilityInfo(b).daysLeft);

    const recent = [...done].sort((a,b)=> new Date(b.updatedAt)-new Date(a.updatedAt)).slice(0,6);
  }

  // Recent activity feed - use localStorage activities
  renderActivityFeed();
}

function updateTrend(elementId, count, isMonthly = false){
  const el = document.getElementById(elementId);
  if(!el) return;
  if(count === 0){
    el.textContent = `— ${isMonthly ? 'this month' : 'this week'}`;
    el.className = 'trend neutral';
  }else{
    const change = Math.floor(Math.random() * 15) + 1;
    const isUp = Math.random() > 0.3;
    el.textContent = `${isUp ? '↑' : '↓'} ${change}% ${isMonthly ? 'this month' : 'this week'}`;
    el.className = `trend ${isUp ? 'up' : 'down'}`;
  }
}

function renderTodayActivities(byStatus){
  const container = document.getElementById('todayActivities');
  const today = todayISO();
  
  // Calculate actual today's activities
  const newToday = cases.filter(c => c.createdAt && c.createdAt.startsWith(today)).length;
  const approvedToday = cases.filter(c => {
    const lastApproved = c.statusHistory?.filter(h => h.status === 'Approved').pop();
    return lastApproved && lastApproved.date === today;
  }).length;
  const printedToday = cases.filter(c => {
    const lastPrinted = c.statusHistory?.filter(h => h.status === 'Printed').pop();
    return lastPrinted && lastPrinted.date === today;
  }).length;
  const releasedToday = cases.filter(c => c.releasedDate === today).length;
  
  const activities = [
    {icon:'user-plus', color:'var(--info-bg)', iconColor:'var(--info)', text:`${newToday} New Requests`, check:newToday > 0},
    {icon:'check-circle', color:'var(--success-bg)', iconColor:'var(--success)', text:`${approvedToday} Cases Approved`, check:approvedToday > 0},
    {icon:'printer', color:'var(--info-bg)', iconColor:'var(--info)', text:`${printedToday} Cases Printed`, check:printedToday > 0},
    {icon:'send', color:'var(--purple-bg)', iconColor:'var(--purple)', text:`${releasedToday} Cases Released`, check:releasedToday > 0},
  ];
  
  container.innerHTML = activities.map(a=>`
    <div class="activity-item">
      <div class="activity-icon" style="background:${a.color};color:${a.iconColor}">
        <i data-lucide="${a.icon}" style="width:18px;height:18px"></i>
      </div>
      <div class="activity-content">
        <div class="activity-text">${a.check ? '✓ ' : ''}${a.text}</div>
        <div class="activity-time">Today</div>
      </div>
    </div>`).join("");
}

async function renderActivityFeed(recent = null){
  const container = document.getElementById('activityFeed');
  const activities = recent ? recent : await getActivities();
  
  if(!activities.length){
    container.innerHTML = `<div style="text-align:center;padding:32px 20px;color:var(--text-muted)">
      <i data-lucide="inbox" style="width:32px;height:32px;margin:0 auto 8px;display:block;color:#D1D5DB"></i>
      <span style="font-size:13px">No recent activities</span>
    </div>`;
    return;
  }
  
  // If recent is provided (old behavior from cases data), use old rendering
  if(recent) {
    const statusConfig = {
      'Draft':    {icon:'file-edit', bg:'var(--background)', color:'var(--text-muted)'},
      'Review':   {icon:'clock',     bg:'var(--warning-bg, #FEF3C7)', color:'var(--warning, #D97706)'},
      'Approved': {icon:'check-circle', bg:'var(--success-bg)', color:'var(--success)'},
      'Printed':  {icon:'printer',   bg:'var(--info-bg)', color:'var(--info)'},
      'Released': {icon:'send',      bg:'var(--purple-bg)', color:'var(--purple)'}
    };
    
    container.innerHTML = recent.slice(0,10).map(c=>{
      const cfg = statusConfig[c.status] || statusConfig['Draft'];
      const clientName = escapeHtml(c.client.name) || 'Unnamed client';
      const controlNo = escapeHtml(c.controlNo) || '—';
      const timeAgo = getTimeAgo(c.updatedAt);
      return `
      <div class="activity-item">
        <div class="activity-icon" style="background:${cfg.bg};color:${cfg.color}">
          <i data-lucide="${cfg.icon}"></i>
        </div>
        <div class="activity-content">
          <div class="activity-text"><strong>${clientName}</strong>'s case is ${c.status.toLowerCase()}</div>
          <div class="activity-time">${controlNo} &middot; ${timeAgo}</div>
        </div>
      </div>`;
    }).join("");
    return;
  }
  
  // New behavior using database activities
  const actionConfig = {
    'created': {icon:'plus-circle', bg:'var(--success-bg)', color:'var(--success)'},
    'updated': {icon:'edit', bg:'var(--info-bg)', color:'var(--info)'},
    'viewed': {icon:'eye', bg:'var(--background)', color:'var(--text-muted)'},
    'archived': {icon:'archive', bg:'var(--danger-bg)', color:'var(--danger)'},
    'restored': {icon:'rotate-ccw', bg:'var(--success-bg)', color:'var(--success)'},
    'deleted': {icon:'trash-2', bg:'var(--danger-bg)', color:'var(--danger)'},
    'printed': {icon:'printer', bg:'var(--purple-bg)', color:'var(--purple)'},
    'released': {icon:'send', bg:'var(--success-bg)', color:'var(--success)'}
  };
  
  // Show all relevant activities: new case, archived, restored, printed, released, etc.
  const displayActivities = activities.filter(a => 
    ['created', 'updated', 'archived', 'restored', 'printed', 'released', 'deleted'].includes(a.action)
  );
  
  if(!displayActivities.length){
    container.innerHTML = `<div style="text-align:center;padding:32px 20px;color:var(--text-muted)">
      <i data-lucide="inbox" style="width:32px;height:32px;margin:0 auto 8px;display:block;color:#D1D5DB"></i>
      <span style="font-size:13px">No recent activities</span>
    </div>`;
    return;
  }
  
  container.innerHTML = displayActivities.slice(0,10).map(a=>{
    const cfg = actionConfig[a.action] || actionConfig['updated'];
    const timeAgo = getTimeAgo(a.timestamp);
    const caseInfo = a.caseInfo ? `<strong>${escapeHtml(a.caseInfo.clientName || 'Unknown')}</strong> (${escapeHtml(a.caseInfo.controlNo || 'N/A')})` : '';
    return `
    <div class="activity-item">
      <div class="activity-icon" style="background:${cfg.bg};color:${cfg.color}">
        <i data-lucide="${cfg.icon}"></i>
      </div>
      <div class="activity-content">
        <div class="activity-text">${a.details}${caseInfo ? ' - ' + caseInfo : ''}</div>
        <div class="activity-time">${timeAgo} &middot; ${formatDateTime(a.timestamp)}</div>
      </div>
    </div>`;
  }).join("");
  
  // Create Lucide icons for the activity feed
  lucide.createIcons();
}

function confirmClearActivities(){
  Swal.fire({
    title: 'Clear Recent Activities?',
    text: 'This will remove all recent activity logs. This action cannot be undone.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#DC2626',
    cancelButtonColor: '#6B7280',
    confirmButtonText: 'Yes, clear all',
    cancelButtonText: 'Cancel',
    background: '#ffffff',
    customClass: { popup: 'rounded-4 shadow-lg' }
  }).then((result) => {
    if(result.isConfirmed){
      clearActivities();
      renderActivityFeed();
      lucide.createIcons();
    }
  });
}

function getTimeAgo(dateStr){
  if(!dateStr || dateStr === 'null' || dateStr === '') return 'Unknown';
  const now = new Date();
  let date;
  
  // Try parsing the date with different formats
  try {
    // Handle ISO datetime strings (with T) and date strings (without T)
    if(dateStr.includes('T') || dateStr.includes('Z')) {
      date = new Date(dateStr);
    } else {
      date = new Date(dateStr+"T00:00:00");
    }
    
    if(isNaN(date.getTime())) {
      console.warn('Invalid date format:', dateStr);
      return 'Unknown';
    }
  } catch(e) {
    console.warn('Error parsing date:', dateStr, e);
    return 'Unknown';
  }
  
  const diff = Math.floor((now - date) / (1000 * 60));
  if(diff < 1) return 'Just now';
  if(diff < 60) return `${diff} minute${diff > 1 ? 's' : ''} ago`;
  const hours = Math.floor(diff / 60);
  if(hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
  const days = Math.floor(hours / 24);
  return `${days} day${days > 1 ? 's' : ''} ago`;
}

function formatDateTime(dateStr){
  if(!dateStr || dateStr === 'null' || dateStr === '') return 'Unknown';
  const date = new Date(dateStr);
  if(isNaN(date.getTime())) return 'Unknown';
  const d = date.toLocaleDateString('en-US', {month:'short', day:'numeric', year:'numeric'});
  const t = date.toLocaleTimeString('en-US', {hour:'numeric', minute:'2-digit', hour12:true});
  return `${d} ${t}`;
}

function formatNumberWithCommas(value){
  if(!value) return '';
  const numericValue = value.toString().replace(/,/g, '');
  if(numericValue === '' || isNaN(numericValue)) return value;
  return parseFloat(numericValue).toLocaleString('en-US');
}

function initCharts(){
  // Assistance Type Chart
  const assistanceCtx = document.getElementById('assistanceChart');
  if(assistanceCtx){
    const purposeCounts = {};
    dashboardCases().forEach(c => {
      purposeCounts[c.purpose] = (purposeCounts[c.purpose] || 0) + 1;
    });
    const labels = Object.keys(purposeCounts);
    const data = labels.map(l => purposeCounts[l]);
    const total = data.reduce((a, b) => a + b, 0);
    
    // Government color palette
    const colors = ['#1E3A8A', '#3B82F6', '#16A34A', '#F59E0B', '#DC2626'];
    
    new Chart(assistanceCtx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: colors,
          borderWidth: 0,
          hoverOffset: 8
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1F2937',
            titleColor: '#FFFFFF',
            bodyColor: '#FFFFFF',
            padding: 12,
            cornerRadius: 8,
            displayColors: true
          }
        }
      }
    });
    
    // Render custom legend
    const legendContainer = document.getElementById('chartLegend');
    if(legendContainer){
      legendContainer.innerHTML = labels.map((label, i) => {
        const count = data[i];
        const percent = total > 0 ? Math.round((count / total) * 100) : 0;
        return `
          <div class="legend-item">
            <div class="legend-color" style="background:${colors[i]}"></div>
            <div class="legend-info">
              <div class="legend-name">${label}</div>
              <div class="legend-count">${count} cases</div>
            </div>
            <div class="legend-percent">${percent}%</div>
          </div>
        `;
      }).join('');
    }
  }

  // Barangay Chart
  const barangayCtx = document.getElementById('barangayChart');
  if(barangayCtx){
    new Chart(barangayCtx, {
      type: 'bar',
      data: {
        labels: ['Biluso', 'Poblacion IV', 'Tubuan', 'Batas', 'Bigaa'],
        datasets: [{
          label: 'Cases',
          data: [35, 28, 22, 18, 15],
          backgroundColor: '#FFC107',
          borderRadius: 6
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          x: { beginAtZero: true, grid: { color: '#E5E7EB' } },
          y: { grid: { display: false } }
        }
      }
    });
  }
}

/* ---------------- Rendering: New case ---------------- */
async function loadNewCase(){
  await loadCases();
  view = {tab:"newCase", newCaseStep:"search", eligClientName:"", eligOverride:false, eligMatch:null, selectedClient:null, checkerResult:null};
  renderNewCase();
  if(CAN_ENCODE) renderEncoderQueue();
  lucide.createIcons();
}

function renderEncoderQueue(){
  const container = document.getElementById('encoderQueue');
  if(!container) return;

  const waiting = cases.filter(c => {
    const isWaiting = c.eligibilityStatus === 'eligible' && c.status === 'Draft' && c.eligibleBy && !(c.interview && c.interview.interviewSituation);
    if (!isWaiting) return false;
    if (CURRENT_USER_ROLE === 'social_worker' && CURRENT_USER_ID) {
      const assignedId = c.officerId || c.officer_id || c.officer?.id;
      return String(assignedId) === String(CURRENT_USER_ID);
    }
    return true;
  });

  if(waiting.length === 0){
    container.innerHTML = `
      <div style="text-align:center;padding:20px;color:var(--text-muted);font-size:13px">
        <i data-lucide="inbox" style="width:28px;height:28px;margin:0 auto 8px;display:block;color:#D1D5DB"></i>
        No clients are waiting for case encoding.
      </div>`;
    lucide.createIcons();
    return;
  }

  container.innerHTML = waiting.map(c => `
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--border);border-radius:10px;margin-bottom:8px;background:var(--surface)">
      <div style="min-width:0">
        <div style="font-weight:600;color:#111827;font-size:14px">${escapeHtml(c.client?.name || 'Unnamed')}</div>
        <div style="font-size:12px;color:var(--text-muted);margin-top:2px">
          ${escapeHtml(String(c.client?.age || ''))} • ${escapeHtml(c.client?.sex || c.client?.gender || '')} • ${escapeHtml(c.client?.address || c.client?.barangay || '—')}
        </div>
        <div style="font-size:11px;color:var(--text-muted);margin-top:2px">
          Forwarded by ${escapeHtml(c.eligibleByUser?.name || 'Eligibility Checker')}${c.officer?.name ? ` • Assigned to: <strong style="color:#1A237E">${escapeHtml(c.officer.name)}</strong>` : ''}${c.eligibleAt ? ' • ' + fmtDate(c.eligibleAt) : ''}
        </div>
      </div>
      <div style="display:flex;gap:6px;flex-shrink:0">
        <button class="btn primary btn-sm" onclick="startEncodingFromQueue('${c.id}', '${escapeHtml(c.client?.name || '')}')">
          <i data-lucide="pen-line" style="width:14px;height:14px"></i> Encode
        </button>
      </div>
    </div>`).join('');

  lucide.createIcons();
}

async function startEncodingFromQueue(caseId, clientName){
  if(!clientName || clientName === 'Unnamed'){
    Swal.fire({icon:'warning', title:'Missing client name', text:'This case has no client name to pre-fill. Please encode it manually.', confirmButtonColor:'#1A237E'});
    return;
  }
  sessionStorage.setItem('intake_caseId', caseId);
  sessionStorage.setItem('intake_clientName', clientName);
  window.location.href = '/admin/social-case/intake';
}

function renderNewCase(){
  const container = document.getElementById('newCaseContent');
  if(!container) return;

  // The new case page now has static HTML, so we only need to handle dynamic content
  // Search results, client summary, and eligibility status
  const searchInput = document.getElementById('elig-name');
  if(searchInput && view.eligClientName){
    searchInput.value = view.eligClientName;
  }

  // Pure eligibility checkers use the server-side result card only
  if(IS_PURE_ELIGIBILITY_CHECKER){
    if(view.checkerResult){
      renderCheckerEligibilityResult(view.checkerResult);
    }
    return;
  }

  // Render search results if available
  if(view.eligClientName && view.eligClientName.length >= 2){
    renderSearchResults(view.eligClientName);
  }
  
  // Render eligibility status if selected
  if(view.selectedClient){
    renderEligibilityStatus(view.eligMatch !== undefined && view.eligMatch !== null ? view.eligMatch : view.selectedClient);
  }
}

function renderSearchResults(query){
  const container = document.getElementById('searchResults');
  if(!container) return;
  
  console.log('Searching for:', query);
  console.log('Total cases:', cases.length);
  
  const qParsed = parseClientName(query);
  
  // Find exact normalized matches
  const exactMatches = cases.filter(c =>
    normalizeClientName(c.client.name) === qParsed.normalized
  );
  
  // Find partial matches: last name match + at least one genuine given-name match
  const partialMatches = cases.filter(c => {
    const cParsed = parseClientName(c.client.name);
    if(normalizeClientName(c.client.name) === qParsed.normalized) return false;
    // Must have same last name AND real given-name overlap
    if(cParsed.lastName === qParsed.lastName && hasGivenNameMatch(qParsed.parts, cParsed.parts, qParsed.lastName)) return true;
    return false;
  }).slice(0,5);
  
  console.log('Exact matches found:', exactMatches.length);
  console.log('Partial matches found:', partialMatches.length);
  
  // Check eligibility for the searched client
  const eligibility = checkEligibility(query);
  
  // If there are exact matches, show them
  if(exactMatches.length > 0){
    container.style.display = 'block';
    container.innerHTML = exactMatches.map(c => {
      const eligibility = eligibilityInfo(c);
      const statusBadge = !eligibility.eligible 
        ? `<span style="background:#FEF2F2;color:#DC2626;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">Not Eligible until ${fmtDate(eligibility.nextEligibleDate)}</span>`
        : `<span style="background:#ECFDF5;color:#059669;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">Eligible</span>`;
      
      return `
        <div class="search-result-item" onclick="selectClient('${c.id}')">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div class="search-result-name">${escapeHtml(c.client.name)}</div>
            ${statusBadge}
          </div>
          <div class="search-result-details">
            ${escapeHtml(c.client.sex || '')} • ${escapeHtml(String(c.client.age) || '')} • ${escapeHtml(c.purpose || '')}
          </div>
        </div>
      `;
    }).join('');
  }
  // If there are partial matches but no exact matches, warn user
  else if(partialMatches.length > 0){
    container.style.display = 'none';
    Swal.fire({
      title: 'Similar Names Found',
      html: `We found clients with similar names:<br><br>${partialMatches.map(c => `<strong>${escapeHtml(c.client.name)}</strong>`).join('<br>')}<br><br>Please verify if this is the same person or continue as a new client.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Proceed with New Client',
      cancelButtonText: 'Select Existing',
      confirmButtonColor: '#1A237E',
      cancelButtonColor: '#6B7280',
      background: '#ffffff',
      customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
      if (result.isConfirmed) {
        proceedWithNewClient(query);
      } else {
        // Show the partial matches for selection
        container.style.display = 'block';
        container.innerHTML = partialMatches.map(c => `
          <div class="search-result-item" onclick="selectClient('${c.id}')">
            <div class="search-result-name">${escapeHtml(c.client.name)}</div>
            <div class="search-result-details">
              ${escapeHtml(c.client.sex || '')} • ${escapeHtml(String(c.client.age) || '')} • ${escapeHtml(c.purpose || '')}
            </div>
          </div>
        `).join('');
        lucide.createIcons();
      }
    });
  }
  // No matches at all
  else if(!eligibility.eligible){
    // Client is ineligible
    container.style.display = 'block';
    container.innerHTML = `
      <div style="padding:16px;text-align:center;color:var(--danger)">
        <i data-lucide="alert-triangle" style="width:32px;height:32px;margin-bottom:8px"></i>
        <div style="font-weight:600">Client Not Eligible</div>
        <div style="font-size:12px;margin-top:4px;margin-bottom:16px">${escapeHtml(eligibility.reason)}</div>
        <button class="btn ghost" onclick="document.getElementById('searchResults').style.display='none'">
          <i data-lucide="x" style="width:16px;height:16px"></i> Close
        </button>
      </div>
    `;
  } else {
    // Client is eligible - show popup
    container.style.display = 'none';
    Swal.fire({
      title: 'No clients found',
      text: `No clients found matching "${escapeHtml(query)}". This appears to be a new client. You can proceed with the interview.`,
      icon: 'info',
      showCancelButton: true,
      confirmButtonText: 'Proceed with New Client',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#1A237E',
      cancelButtonColor: '#6B7280',
      background: '#ffffff',
      customClass: { popup: 'rounded-4 shadow-lg' }
    }).then((result) => {
      if (result.isConfirmed) {
        proceedWithNewClient(query);
      }
    });
  }
  lucide.createIcons();
}

function selectClient(caseId){
  const c = getCase(caseId);
  if(!c) return;
  
  view.selectedClient = c;
  view.eligClientName = c.client.name;
  
  // Update search input
  const searchInput = document.getElementById('elig-name');
  if(searchInput) searchInput.value = c.client.name;
  
  // Hide search results
  const searchResults = document.getElementById('searchResults');
  if(searchResults) searchResults.style.display = 'none';
  
  // Check eligibility for this client name
  const eligibility = checkEligibility(c.client.name);
  
  // Use the latest case from eligibility check if not eligible, otherwise use selected case
  let match = c;
  if(!eligibility.eligible) {
    match = eligibility.latestCase || c;
  }
  
  setView({eligMatch: match, eligOverride:false});
  renderEligibilityStatus(match);
  
  lucide.createIcons();
}

function renderEligibilityStatus(match){
  const container = document.getElementById('eligibilityStatus');
  if(!container) return;
  
  const e = match ? eligibilityInfo(match) : {eligible: true, daysLeft: 0, nextEligibleDate: null};
  
  if(e.eligible || !match){
    // Eligible
    container.innerHTML = `
      <div class="eligibility-card eligible">
        <div class="status-icon"><i data-lucide="check-circle" style="width:24px;height:24px"></i></div>
        <div class="status-title">Eligible</div>
        <div class="status-desc">
          ${match ? `No Social Case Study within the last 6 months. Last case study was released on ${fmtDate(match.releasedDate)}.` : 'No prior case study found for this client.'}
        </div>
        ${IS_PURE_ELIGIBILITY_CHECKER
          ? `<button class="btn primary" style="margin-top:16px;width:100%" onclick="submitForEncoding()"><i data-lucide="send" style="width:16px;height:16px"></i> Submit for Case Encoding</button>`
          : `<button class="btn primary" style="margin-top:16px;width:100%" onclick="proceedToIntake()"><i data-lucide="arrow-right" style="width:16px;height:16px"></i> Continue to Case Encoding</button>`}
      </div>
    `;
    
    // Update last case study in summary
    // (client summary removed - no longer shown)
  }else{
    // Not eligible - Show SweetAlert popup
    const clientName = (match && (match.client?.name || view.eligClientName)) || 'Unknown';
    const clientAge = match?.client?.age || '-';
    const clientSex = match?.client?.sex || match?.client?.gender || '-';
    const clientBarangay = match?.client?.address || match?.client?.barangay || '-';
    
    Swal.fire({
      title: `<strong>${clientName}</strong>`,
      html: `
        <div style="text-align: left; padding: 10px 0;">
          <div style="font-size: 13px; color: #6B7280; margin-bottom: 16px;">Selected Client</div>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
            <div>
              <div style="font-size: 12px; color: #6B7280; font-weight: 500;">Age</div>
              <div style="font-size: 15px; font-weight: 600; color: #111827;">${clientAge}</div>
            </div>
            <div>
              <div style="font-size: 12px; color: #6B7280; font-weight: 500;">Sex</div>
              <div style="font-size: 15px; font-weight: 600; color: #111827;">${clientSex}</div>
            </div>
            <div>
              <div style="font-size: 12px; color: #6B7280; font-weight: 500;">Barangay</div>
              <div style="font-size: 15px; font-weight: 600; color: #111827;">${clientBarangay}</div>
            </div>
            <div>
              <div style="font-size: 12px; color: #6B7280; font-weight: 500;">Last Case Study</div>
              <div style="font-size: 15px; font-weight: 600; color: #111827;">${fmtDate(match.releasedDate || match.createdAt)}</div>
            </div>
          </div>
          
          <div style="background: #FEF2F2; border: 2px solid #DC2626; border-radius: 12px; padding: 16px; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
              <svg style="width: 24px; height: 24px; color: #DC2626;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <strong style="color: #DC2626; font-size: 16px;">Not Eligible</strong>
            </div>
            <div style="font-size: 14px; color: #374151; line-height: 1.5;">
              Previous Social Case Study was released on ${fmtDate(match.releasedDate || match.createdAt)}.
              <br><br>
              <strong>Eligible Again:</strong> ${fmtDate(e.nextEligibleDate)} (${e.daysLeft} days from now)
            </div>
          </div>
          
          <div style="margin-bottom: 16px;">
            <div style="font-size: 12px; color: #6B7280; margin-bottom: 8px;">Restricted period</div>
            <div style="display: flex; justify-content: space-between; font-size: 12px; color: #6B7280; margin-bottom: 4px;">
              <span>${e.pct}% elapsed</span>
            </div>
            <div style="background: #E5E7EB; border-radius: 6px; height: 8px; overflow: hidden;">
              <div style="background: #DC2626; height: 100%; width: ${e.pct}%"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9CA3AF; margin-top: 4px;">
              <span>${fmtDate(match.releasedDate || match.createdAt)}</span>
              <span>${fmtDate(e.nextEligibleDate)}</span>
            </div>
          </div>
          
          ${CAN_ENCODE ? `
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <input type="checkbox" id="overrideCheck" ${view.eligOverride ? 'checked' : ''} style="width: 18px; height: 18px; cursor: pointer;">
            <label for="overrideCheck" style="font-size: 13px; color: #374151; cursor: pointer;">Override and proceed anyway (requires supervisor approval)</label>
          </div>` : ''}
        </div>
      `,
      icon: 'warning',
      iconColor: '#DC2626',
      showCancelButton: CAN_ENCODE,
      confirmButtonText: CAN_ENCODE ? 'Continue to Case Encoding' : 'OK',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#1E3A8A',
      cancelButtonColor: '#6B7280',
      customClass: {
        popup: 'swal2-popup-custom',
        title: 'swal2-title-custom'
      },
      didOpen: () => {
        if(!CAN_ENCODE) return;
        const overrideCheck = document.getElementById('overrideCheck');
        const confirmBtn = Swal.getConfirmButton();
        
        if(overrideCheck){
          overrideCheck.addEventListener('change', (e) => {
            setView({eligOverride: e.target.checked});
            confirmBtn.disabled = !e.target.checked;
            confirmBtn.style.opacity = e.target.checked ? '1' : '0.5';
          });
        }
        
        // Initial state
        confirmBtn.disabled = !view.eligOverride;
        confirmBtn.style.opacity = view.eligOverride ? '1' : '0.5';
      },
      preConfirm: () => {
        if(!CAN_ENCODE) return true;
        if(!view.eligOverride){
          Swal.showValidationMessage('Please check the override checkbox to proceed');
          return false;
        }
        proceedToIntake();
        return false;
      }
    });
    
    // Clear the container since we're using SweetAlert
    container.innerHTML = '';
  }
  
  lucide.createIcons();
}

async function startEligibilityCheck(){
  const name = document.getElementById('elig-name').value;
  if(!name || name.trim().length < 2){
    Swal.fire({
      icon: 'warning',
      title: 'Input Required',
      text: 'Please enter at least 2 characters to search.',
      confirmButtonColor: '#1A237E',
      confirmButtonText: 'OK',
      background: '#ffffff',
      customClass: { popup: 'rounded-4 shadow-lg' }
    });
    return;
  }
  
  // Validate that name contains only letters, spaces, and common name characters
  const validNameRegex = /^[a-zA-Z\s\-\.']+$/;
  if (!validNameRegex.test(name.trim())) {
    Swal.fire({
      icon: 'warning',
      title: 'Invalid Name',
      text: 'Please enter a valid name (letters, spaces, hyphens, and apostrophes only).',
      confirmButtonColor: '#1A237E',
      confirmButtonText: 'OK',
      background: '#ffffff',
      customClass: { popup: 'rounded-4 shadow-lg' }
    });
    return;
  }
  
  // Reload cases from server to get latest data before checking eligibility
  await loadCases();
  
  view.eligClientName = name;

  // Start fresh for each search: drop any previously selected client / match result
  view.selectedClient = null;
  view.eligMatch = null;
  view.checkerResult = null;

  if(IS_PURE_ELIGIBILITY_CHECKER){
    await runServerEligibilityCheck(name);
  } else {
    renderSearchResults(name);
  }
  lucide.createIcons();
}

async function runServerEligibilityCheck(name){
  const results = document.getElementById('searchResults');
  const status = document.getElementById('eligibilityStatus');

  const btn = document.querySelector('button[onclick^="startEligibilityCheck"]');
  if(btn) btn.disabled = true;
  if(status) status.innerHTML = '<div style="text-align:center;padding:16px;color:var(--text-muted)"><i data-lucide="loader-2" style="width:20px;height:20px;animation:spin 1s linear infinite;display:inline-block;vertical-align:middle;margin-right:6px"></i> Checking eligibility...</div>';

  try {
    const response = await fetch('/admin/social-case/api/eligibility/check', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({client_name: name})
    });

    const data = await response.json();
    if(!response.ok){
      throw new Error(data.error || 'Eligibility check failed');
    }

    renderCheckerEligibilityResult(data);
    view.checkerResult = data;
  } catch(e){
    console.error('Eligibility check error:', e);
    if(status){
      status.innerHTML = '';
    }
    Swal.fire({icon:'error', title:'Check Failed', text:'Unable to complete the eligibility check. Please try again.', confirmButtonColor:'#DC2626'});
  } finally {
    if(btn) btn.disabled = false;
    if(results) results.style.display = 'none';
    lucide.createIcons();
  }
}

function renderCheckerEligibilityResult(data){
  const status = document.getElementById('eligibilityStatus');
  const results = document.getElementById('searchResults');
  if(results) results.style.display = 'none';

  if(!status) return;

  // Title case function to capitalize first letter of each word while preserving periods and other characters
  const toTitleCase = (str) => {
    return str.replace(/\b[a-z]/g, char => char.toUpperCase());
  };

  const clientName = escapeHtml(toTitleCase(view.eligClientName || ''));

  const matchBadge = data.match_type === 'partial'
    ? `<span style="display:inline-block;background:#FEF3C7;color:#92400E;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;margin-left:8px">Partial Name Match</span>`
    : '';

  if(!data.eligible){
    const reason = data.blocking
      ? `Received ${escapeHtml(data.blocking.assistance_type)} assistance on ${fmtDate(data.blocking.release_date)}.`
      : 'An approved / released assistance was provided within the last 6 months.';
    status.innerHTML = `
      <div class="eligibility-card" style="border-color:#DC2626;background:#FEF2F2">
        <div class="status-icon" style="color:#DC2626"><i data-lucide="x-circle" style="width:24px;height:24px"></i></div>
        <div class="status-title" style="color:#DC2626">Not Eligible</div>
        <div class="status-desc">
          ${clientName ? `<strong style="color:#111827">${clientName}</strong>${matchBadge}<br>` : ''}
          ${reason}<br>
          ${data.eligible_again_date ? `<strong>Eligible again on:</strong> ${fmtDate(data.eligible_again_date)}` : ''}
        </div>
      </div>`;
    lucide.createIcons();
    return;
  }

  if(data.existing_case){
    const ec = data.existing_case;
    const statusBadge = `<span class="badge ${STATUS_CLASS[ec.status] || 'b-draft'}">${escapeHtml(ec.status || '—')}</span>`;
    status.innerHTML = `
      <div class="eligibility-card" style="border-color:#D97706;background:#FFFBEB">
        <div class="status-icon" style="color:#D97706"><i data-lucide="alert-circle" style="width:24px;height:24px"></i></div>
        <div class="status-title" style="color:#B45309">Already Has an Active Case</div>
        <div class="status-desc">
          ${clientName ? `<strong style="color:#111827">${clientName}</strong>${matchBadge}<br>` : ''}
          <strong style="color:#111827">${escapeHtml(ec.case_number || 'Unknown case')}</strong> ${statusBadge}<br>
          This client already has an active case record and cannot be forwarded for a new case encoding. Please review the existing case instead.
        </div>
        <div style="display:flex;gap:10px;margin-top:16px">
          <a class="btn primary" style="flex:1;text-decoration:none;justify-content:center" href="/admin/social-case/detail/${encodeURIComponent(ec.id)}">
            <i data-lucide="eye" style="width:16px;height:16px"></i> View Existing Case
          </a>
          <button class="btn" style="flex:1;justify-content:center" onclick="resetEligibilityCheck()">
            <i data-lucide="rotate-ccw" style="width:16px;height:16px"></i> Search Again
          </button>
        </div>
        <div style="display:flex;align-items:center;gap:8px;margin-top:14px;background:${view.eligOverride ? '#FEF3C7' : '#FFFFFF'};border:1px solid #FDE68A;border-radius:8px;padding:10px 12px;">
          <input type="checkbox" id="overrideActive" ${view.eligOverride ? 'checked' : ''} style="width:16px;height:16px;cursor:pointer;">
          <label for="overrideActive" style="font-size:13px;color:#374151;cursor:pointer;margin:0">Override and forward anyway (requires approval)</label>
        </div>
        <button class="btn primary" id="overrideSubmitBtn" style="margin-top:12px;width:100%;justify-content:center" ${view.eligOverride ? '' : 'disabled'} onclick="submitForEncoding()">
          <i data-lucide="send" style="width:16px;height:16px"></i> Submit for Case Encoding (Override)
        </button>
      </div>`;

    const overrideCheck = document.getElementById('overrideActive');
    const overrideSubmitBtn = document.getElementById('overrideSubmitBtn');
    if(overrideCheck && overrideSubmitBtn){
      overrideCheck.addEventListener('change', (e) => {
        setView({eligOverride: e.target.checked});
        overrideSubmitBtn.disabled = !e.target.checked;
        overrideSubmitBtn.style.opacity = e.target.checked ? '1' : '0.5';
      });
    }
  } else {
    let possibleMatchesHtml = '';
    if(data.possible_matches && data.possible_matches.length > 0){
      possibleMatchesHtml = `
        <div style="margin-top:14px;padding:12px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px">
          <div style="font-weight:600;font-size:13px;color:#374151;margin-bottom:8px">
            <i data-lucide="users" style="width:14px;height:14px;display:inline-block;vertical-align:middle;margin-right:4px"></i>
            Other Possible Matches
          </div>
          ${data.possible_matches.map(m => `<div style="font-size:12px;color:#6B7280;padding:4px 0">• ${escapeHtml(m.name)}</div>`).join('')}
          <div style="font-size:11px;color:#9CA3AF;margin-top:6px">Please verify the client's identity before proceeding.</div>
        </div>`;
    }

    status.innerHTML = `
      <div class="eligibility-card eligible">
        <div class="status-icon"><i data-lucide="check-circle" style="width:24px;height:24px"></i></div>
        <div class="status-title">Eligible</div>
        <div class="status-desc">
          ${clientName ? `<strong style="color:#111827">${clientName}</strong>${matchBadge}<br>` : ''}
          This client passed eligibility checking and can be forwarded for case encoding.
        </div>
        ${possibleMatchesHtml}
        <button class="btn primary" style="margin-top:16px;width:100%" onclick="submitForEncoding()">
          <i data-lucide="send" style="width:16px;height:16px"></i> Submit for Case Encoding
        </button>
      </div>`;
  }
  lucide.createIcons();
}

function resetEligibilityCheck(){
  setView({eligClientName: '', eligMatch: null, selectedClient: null, checkerResult: null, eligOverride: false});
  const searchInput = document.getElementById('elig-name');
  if(searchInput) searchInput.value = '';
  const results = document.getElementById('searchResults');
  if(results) results.style.display = 'none';
  const status = document.getElementById('eligibilityStatus');
  if(status) status.innerHTML = '';
}

function proceedWithNewClient(clientName){
  console.log('proceedWithNewClient called with:', clientName);
  
  // Store client name in sessionStorage for the intake page
  sessionStorage.setItem('intake_clientName', clientName);
  
  // Redirect to the intake page
  window.location.href = '/admin/social-case/intake';
}

// Make function globally accessible
window.proceedWithNewClient = proceedWithNewClient;

/* ---------------- Rendering: Intake form ---------------- */
function editCaseFromDetail(caseId){
  sessionStorage.setItem('intake_caseId', caseId);
  sessionStorage.setItem('intake_clientName', getCase(caseId)?.client?.name || '');
  window.location.href = `/admin/social-case/intake?caseId=${encodeURIComponent(caseId)}`;
}

async function loadIntakeForm(){
  await loadCases();
  const savedName = sessionStorage.getItem('intake_clientName') || '';
  sessionStorage.removeItem('intake_clientName');
  const savedAddress = sessionStorage.getItem('intake_clientAddress') || '';
  const savedContact = sessionStorage.getItem('intake_clientContact') || '';
  const savedAge = sessionStorage.getItem('intake_clientAge') || '';
  const savedBirthdate = sessionStorage.getItem('intake_clientBirthdate') || '';
  sessionStorage.removeItem('intake_clientAddress');
  sessionStorage.removeItem('intake_clientContact');
  sessionStorage.removeItem('intake_clientAge');
  sessionStorage.removeItem('intake_clientBirthdate');
  const urlParams = new URLSearchParams(window.location.search);
  const caseId = urlParams.get('caseId') || sessionStorage.getItem('intake_caseId') || null;
  sessionStorage.removeItem('intake_caseId');
  draftIntake = blankIntake(savedName);
  if(savedAddress) draftIntake.client.address = savedAddress;
  if(savedContact) draftIntake.client.contact = savedContact;
  if(savedAge) draftIntake.client.age = savedAge;
  if(savedBirthdate) draftIntake.client.birthdate = savedBirthdate;
  draftIntake.caseId = caseId;
  if(caseId){
    const existing = getCase(caseId);
    if(existing){
      draftIntake = caseToIntake(existing);
    } else {
      try {
        const response = await fetch(`/admin/social-case/api/cases/${caseId}`);
        if(response.ok){
          const caseData = await response.json();
          draftIntake = caseToIntake(convertKeys(caseData, snakeToCamel));
        }
      } catch(e){
        console.error('Failed to load case for intake:', e);
      }
    }
  }
  if(!draftIntake.signers.preparedByName || !draftIntake.signers.preparedByName.trim()) draftIntake.signers.preparedByName = CURRENT_USER_NAME;
  if(!draftIntake.signers.notedByName || !draftIntake.signers.notedByName.trim()) draftIntake.signers.notedByName = ADMIN_NAME || CURRENT_USER_NAME;
  renderIntakeForm();
  lucide.createIcons();
}

function caseToIntake(c){
  const today = todayISO();
  const client = c.client || {};
  const interview = c.interview || {};
  const signers = c.signers || {};
  const agencies = (c.agencies && c.agencies.length ? c.agencies : (c.submittedTo ? String(c.submittedTo).split(',').map(s => s.trim()).filter(Boolean) : []));
  return {
    id: c.id || uid(),
    caseId: c.id,
    status: (c.status === 'Draft' ? 'Review' : c.status) || 'Review',
    createdAt: c.createdAt || today,
    updatedAt: c.updatedAt || today,
    controlNo: c.controlNo || c.caseNumber || generateControlNo(today),
    client: {
      name: client.name || client.fullName || client.full_name || '',
      age: client.age || '',
      sex: client.sex || client.gender || '',
      address: client.address || client.barangay || '',
      birthdate: client.birthdate ? String(client.birthdate).slice(0, 10) : '',
      birthplace: client.birthplace || '',
      religion: client.religion || '',
      education: client.education || '',
      civilStatus: client.civilStatus || client.civil_status || '',
      occupation: client.occupation || '',
      income: client.income || '',
      contact: client.contact || client.contactNumber || client.contact_number || ''
    },
    household: (() => {
      const rows = (c.familyMembers || c.household || []).filter(m => m && (m.fullName || m.full_name || m.name)).map(m => ({
        name: m.fullName || m.full_name || m.name || '',
        relationship: m.relationship || '',
        age: m.age || '',
        education: m.education || '',
        occupation: m.occupation || '',
        income: m.monthlyIncome || m.income || ''
      }));
      if(rows.length === 0){
        rows.push({name:'', relationship:'', age:'', education:'', occupation:'', income:''});
      }
      return rows;
    })(),
    interview: {
      reportDate: c.interviewDate || interview.reportDate || interview.report_date || today,
      problemPresented: interview.interviewSituation || interview.interview_situation || interview.problemPresented || c.summary || '',
      homeCondition: interview.interviewHousehold || interview.interview_household || interview.homeCondition || '',
      socioEconomic: interview.interviewNotes || interview.interview_notes || interview.socioEconomic || '',
      evaluation: interview.socialWorkerAssessment || interview.social_worker_assessment || interview.evaluation || '',
      recommendation: interview.recommendation || ''
    },
    signers: {
      preparedByName: signers.preparedByName || c.officer?.name || '',
      preparedByTitle: signers.preparedByTitle || 'MSWDO Staff',
      notedByName: signers.notedByName || c.encoder?.name || '',
      notedByTitle: signers.notedByTitle || 'MSWDO Head',
      notedByLicense: signers.notedByLicense || ''
    },
    purpose: c.purpose || PURPOSES[0],
    agencies: agencies,
    requirements: DEFAULT_REQUIREMENTS.map(r => ({name: r, submitted: false})),
    statusHistory: c.statusHistory || [{status: c.status || 'Draft', date: today}],
    releasedDate: c.releasedDate || null
  };
}

function updateClientAge(){
  const bd = draftIntake.client.birthdate;
  const ageInput = document.getElementById('clientAgeInput');
  const monthInput = document.getElementById('clientMonthInput');
  if (bd && bd.trim()) {
    const b = new Date(bd + 'T00:00:00');
    if (!isNaN(b.getTime())) {
      const today = new Date();
      let age = today.getFullYear() - b.getFullYear();
      const monthDiff = today.getMonth() - b.getMonth();
      if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < b.getDate())) age--;
      draftIntake.client.age = age;
      const months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
      draftIntake.client.month = months[b.getMonth()];
    }
  } else {
    draftIntake.client.age = '';
    draftIntake.client.month = '';
  }
  if (ageInput) ageInput.value = draftIntake.client.age !== '' ? draftIntake.client.age : '';
  if (monthInput) monthInput.value = draftIntake.client.month || '';
}

function renderIntakeForm(){
  const container = document.getElementById('intakeFormContent');
  if(!container) return;

  container.style.overflow = 'visible';
  container.style.height = 'auto';

  if (container.parentElement) {
    container.parentElement.style.overflow = 'visible';
    container.parentElement.style.height = 'auto';
  }

  const d = draftIntake;
  if (!d.signers.preparedByName || !d.signers.preparedByName.trim()) d.signers.preparedByName = CURRENT_USER_NAME;
  d.signers.preparedByTitle = "MSWDO Staff";
  if (!d.signers.notedByName || !d.signers.notedByName.trim()) d.signers.notedByName = ADMIN_NAME || CURRENT_USER_NAME;
  d.signers.notedByTitle = "MSWDO Head";

  container.innerHTML = `
  <div class="form-card">
    <div class="flex items-center justify-between mb-6 pb-4 border-b" style="border-color:var(--border);">
      <div>
        <h2 class="text-xl font-bold mb-1" style="color:var(--text-primary)">Social Case Study Intake</h2>
        <p class="text-sm m-0" style="color:var(--text-secondary)">Fill in the details below to encode a new social case study record.</p>
      </div>
      <div class="hidden sm:flex items-center gap-2">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold" style="background:#EEF2FF;color:var(--primary);border:1px solid #C7D2FE;">
          <i data-lucide="file-text" style="width:14px;height:14px;margin-right:6px;"></i> Step 2: Case Encoding
        </span>
      </div>
    </div>

    <!-- Report Details & Control Info -->
    <div class="mb-8">
      <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <i data-lucide="clipboard-list" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
        Report Details & Control Information
      </h3>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div>
          <label class="form-label">Control No. <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="text" class="form-input font-mono font-medium" value="${escapeHtml(d.controlNo)}" oninput="draftIntake.controlNo=this.value" placeholder="e.g. MSWD-O-2026-09-0001" required>
        </div>
        <div>
          <label class="form-label">Report Date <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="date" class="form-input" value="${d.interview.reportDate}" oninput="draftIntake.interview.reportDate=this.value" required>
        </div>
        <div>
          <label class="form-label">Purpose / Type of Assistance <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <select class="form-input" oninput="draftIntake.purpose=this.value" required>
            <option value="">Select Purpose</option>
            ${PURPOSES.map(p=>`<option value="${escapeHtml(p)}" ${d.purpose===p?'selected':''}>${escapeHtml(p)}</option>`).join("")}
          </select>
        </div>
      </div>
    </div>

    <!-- I. Identifying Information -->
    <div class="mb-8">
      <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <i data-lucide="user" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
        I. Identifying Information
      </h3>
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2">
          <label class="form-label">Full Name <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="text" class="form-input" value="${escapeHtml(d.client.name)}" oninput="draftIntake.client.name=this.value" placeholder="Enter client's full name" required maxlength="255">
        </div>
        <div>
          <label class="form-label">Sex <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <select class="form-input" oninput="draftIntake.client.sex=this.value" required>
            <option value="">Select Sex</option>
            <option value="Male" ${d.client.sex==='Male'?'selected':''}>Male</option>
            <option value="Female" ${d.client.sex==='Female'?'selected':''}>Female</option>
          </select>
        </div>
        <div>
          <label class="form-label">Birth Date <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="date" id="clientBirthdateInput" class="form-input" value="${d.client.birthdate}" onchange="draftIntake.client.birthdate=this.value; updateClientAge()" required>
        </div>
        <div>
          <label class="form-label">Month</label>
          <input type="text" id="clientMonthInput" class="form-input" placeholder="Auto-calculated" value="${d.client.month || ''}" readonly>
        </div>
        <div>
          <label class="form-label">Age</label>
          <input type="number" id="clientAgeInput" class="form-input" placeholder="Auto-calculated" value="${escapeHtml(String(d.client.age || ''))}" readonly>
        </div>
        <div>
          <label class="form-label">Address (Barangay) <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <select class="form-input" oninput="draftIntake.client.address=this.value" required>
            <option value="">Select Barangay</option>
            ${BARANGAYS.map(b=>`<option value="${escapeHtml(b)}" ${d.client.address===b?'selected':''}>${escapeHtml(b)}</option>`).join("")}
          </select>
        </div>
        <div>
          <label class="form-label">Birthplace <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="text" class="form-input" value="${escapeHtml(d.client.birthplace || '')}" oninput="draftIntake.client.birthplace=this.value" placeholder="e.g. Silang, Cavite" required>
        </div>
        <div>
          <label class="form-label">Civil Status <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <select class="form-input" oninput="draftIntake.client.civilStatus=this.value" required>
            <option value="">Select Civil Status</option>
            ${["Single","Married","Widowed","Separated"].map(o=>`<option value="${o}" ${d.client.civilStatus===o?'selected':''}>${o}</option>`).join("")}
          </select>
        </div>
        <div>
          <label class="form-label">Religion <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="text" class="form-input" value="${escapeHtml(d.client.religion || '')}" oninput="draftIntake.client.religion=this.value" placeholder="e.g. Roman Catholic" required>
        </div>
        <div>
          <label class="form-label">Educational Attainment <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="text" class="form-input" value="${escapeHtml(d.client.education || '')}" oninput="draftIntake.client.education=this.value" placeholder="e.g. College Graduate" required>
        </div>
        <div>
          <label class="form-label">Occupation <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="text" class="form-input" value="${escapeHtml(d.client.occupation || '')}" oninput="draftIntake.client.occupation=this.value" placeholder="e.g. Teacher, Vendor, None" required>
        </div>
        <div>
          <label class="form-label">Monthly Income <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="text" class="form-input" value="${escapeHtml(d.client.income || '')}" oninput="this.value=formatNumberWithCommas(this.value); draftIntake.client.income=this.value" placeholder="e.g. 15,000 or N/A" required>
        </div>
        <div>
          <label class="form-label">Contact Number <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="tel" class="form-input" value="${escapeHtml(d.client.contact || '')}" oninput="draftIntake.client.contact=this.value" maxlength="11" placeholder="e.g. 09171234567" required>
        </div>
      </div>
    </div>

    <!-- II. Family Composition -->
    <div class="mb-8">
      <div class="flex items-center justify-between mb-4" style="border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <h3 class="text-md font-bold m-0" style="color:var(--primary)">
          <i data-lucide="users" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
          II. Family Composition
        </h3>
        <span class="text-xs text-gray-500 font-medium">Add household or immediate family members</span>
      </div>

      <div class="family-table-container">
        <table class="family-table">
          <thead>
            <tr>
              <th style="width:24%;">Full Name <span style="color:#DC2626">*</span></th>
              <th style="width:16%;">Relationship <span style="color:#DC2626">*</span></th>
              <th style="width:10%;">Age <span style="color:#DC2626">*</span></th>
              <th style="width:18%;">Education <span style="color:#DC2626">*</span></th>
              <th style="width:16%;">Occupation <span style="color:#DC2626">*</span></th>
              <th style="width:16%;">Monthly Income <span style="color:#DC2626">*</span></th>
              <th style="width:50px;text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody>
            ${d.household.map((m, i) => `
              <tr>
                <td>
                  <span class="family-mobile-label" style="display:none">Full Name</span>
                  <input type="text" class="form-input" value="${escapeHtml(m.name)}" oninput="draftIntake.household[${i}].name=this.value" placeholder="e.g. Maria Santos" required>
                </td>
                <td>
                  <span class="family-mobile-label" style="display:none">Relationship</span>
                  <select class="form-input" oninput="draftIntake.household[${i}].relationship=this.value" required>
                    <option value="">Select</option>
                    ${RELATIONSHIPS.map(o=>`<option value="${o}" ${m.relationship===o?'selected':''}>${o}</option>`).join("")}
                  </select>
                </td>
                <td>
                  <span class="family-mobile-label" style="display:none">Age</span>
                  <input type="number" class="form-input" min="0" max="150" value="${escapeHtml(String(m.age))}" oninput="draftIntake.household[${i}].age=this.value" placeholder="Age" required>
                </td>
                <td>
                  <span class="family-mobile-label" style="display:none">Education</span>
                  <input type="text" class="form-input" value="${escapeHtml(m.education)}" oninput="draftIntake.household[${i}].education=this.value" placeholder="e.g. High School" required>
                </td>
                <td>
                  <span class="family-mobile-label" style="display:none">Occupation</span>
                  <input type="text" class="form-input" value="${escapeHtml(m.occupation)}" oninput="draftIntake.household[${i}].occupation=this.value" placeholder="e.g. Driver" required>
                </td>
                <td>
                  <span class="family-mobile-label" style="display:none">Monthly Income</span>
                  <input type="text" class="form-input" value="${escapeHtml(m.income)}" oninput="this.value=formatNumberWithCommas(this.value); draftIntake.household[${i}].income=this.value" placeholder="e.g. 10,000" required>
                </td>
                <td style="text-align:center;">
                  ${i > 0 ? `
                    <button type="button" class="btn-icon-danger mx-auto" onclick="draftIntake.household.splice(${i},1); renderIntakeForm(); lucide.createIcons();" title="Remove Member">
                      <i data-lucide="trash-2" style="width:16px;height:16px;"></i>
                    </button>
                  ` : `
                    <span class="text-xs text-gray-300">—</span>
                  `}
                </td>
              </tr>
            `).join("")}
          </tbody>
        </table>
      </div>
      <div class="mt-4 flex items-center justify-between">
        <button type="button" class="btn btn-sm" style="width:auto;max-width:max-content;padding:5px 12px;height:34px;font-size:12.5px;white-space:nowrap;" onclick="draftIntake.household.push({name:'',relationship:'',age:'',education:'',occupation:'',income:''}); renderIntakeForm(); lucide.createIcons();">
          <i data-lucide="plus" style="width:14px;height:14px"></i> Add Family Member
        </button>
        <span class="text-xs text-gray-400 font-medium">Total Family Members: ${d.household.length}</span>
      </div>
    </div>

    <!-- III. Narrative Sections -->
    <div class="mb-8">
      <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <i data-lucide="file-text" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
        III. Problem Presented & Case Narrative
      </h3>
      <div>
        <label class="form-label">Problem Presented <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
        <textarea class="form-input" style="min-height:110px;" oninput="draftIntake.interview.problemPresented=this.value" placeholder="Describe the immediate problem presented, background circumstances, and assistance requested..." required>${escapeHtml(d.interview.problemPresented)}</textarea>
      </div>
    </div>

    <!-- IV. Agencies & Assistance Referrals -->
    <div class="mb-8">
      <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <i data-lucide="building-2" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
        IV. Agencies & Assistance Referrals
      </h3>
      <label class="form-label mb-2">Agencies (Select all that apply) <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mt-2">
        ${AGENCIES.map(a => {
          const checked = d.agencies.includes(a.key);
          const isOP = a.key === 'OP';
          return `
            <div class="agency-card ${checked ? 'selected' : ''}" onclick="toggleAgency('${a.key}')">
              <div class="agency-checkbox-box">
                ${checked ? '<i data-lucide="check" style="width:15px;height:15px;stroke-width:3"></i>' : ''}
              </div>
              <div style="flex:1;min-width:0">
                <div class="agency-abbr font-bold text-sm leading-tight" style="color:${checked?'#fff':'var(--text-primary)'}">${escapeHtml(isOP ? 'Office of the President' : a.key)}</div>
                ${!isOP ? `<div class="agency-name text-xs leading-tight mt-0.5" style="color:${checked?'rgba(255,255,255,0.85)':'var(--text-secondary)'}">${escapeHtml(a.name)}</div>` : ''}
              </div>
            </div>
          `;
        }).join("")}
      </div>
    </div>

    <!-- V. Verified Requirements -->
    <div class="mb-8">
      <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <i data-lucide="clipboard-check" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
        V. Verified Requirements
      </h3>
      <label class="form-label mb-2">Submitted Supporting Documents</label>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
        ${d.requirements.map((r, i) => {
          const submitted = r.submitted;
          return `
            <div class="req-card ${submitted ? 'selected' : ''}" onclick="toggleRequirement(${i})">
              <div class="req-checkbox-box">
                ${submitted ? '<i data-lucide="check" style="width:15px;height:15px;stroke-width:3"></i>' : ''}
              </div>
              <span class="req-name text-sm font-medium" style="color:${submitted?'#fff':'var(--text-primary)'}">${escapeHtml(r.name)}</span>
            </div>
          `;
        }).join("")}
      </div>
    </div>

    <!-- VI. Signatories -->
    <div class="mb-8">
      <h3 class="text-md font-bold mb-4" style="color:var(--primary);border-bottom:2px solid var(--primary);padding-bottom:8px;">
        <i data-lucide="pen-tool" style="width:18px;height:18px;display:inline-block;margin-right:8px;vertical-align:text-bottom;"></i>
        VI. Signatories
      </h3>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div>
          <label class="form-label">Prepared By (Name) <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="text" class="form-input" value="${escapeHtml(d.signers.preparedByName)}" oninput="draftIntake.signers.preparedByName=this.value" required maxlength="255" placeholder="Enter social worker name">
        </div>
        <div>
          <label class="form-label">Prepared By (Title)</label>
          <input type="text" class="form-input" value="MSWDO Staff" readonly>
        </div>
        <div>
          <label class="form-label">Noted By (Name) <span style="color:#DC2626;font-weight:700;font-size:16px">*</span></label>
          <input type="text" class="form-input" value="${escapeHtml(d.signers.notedByName)}" oninput="draftIntake.signers.notedByName=this.value" required maxlength="255" placeholder="Enter department head name">
        </div>
        <div>
          <label class="form-label">Noted By (Title)</label>
          <input type="text" class="form-input" value="MSWDO Head" readonly>
        </div>
      </div>
    </div>

    <!-- Bottom Actions -->
    <div class="flex justify-end gap-3 pt-6 border-t mt-8" style="border-color:var(--border);">
      <a href="/admin/social-case/submitted" class="btn" style="border:1px solid #E5E7EB;color:#374151;">
        <i data-lucide="x" style="width:16px;height:16px"></i>
        Cancel
      </a>
      <button type="button" class="btn primary" onclick="reviewIntake()">
        <i data-lucide="eye" style="width:16px;height:16px"></i>
        Review & Submit Case
      </button>
    </div>
  </div>
  `;

  updateClientAge();

  setTimeout(() => {
    const contactInput = document.querySelector('input[oninput*="draftIntake.client.contact"]');
    if (contactInput) {
      contactInput.addEventListener('input', function(e) {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value.length > 11) value = value.substring(0, 11);
        if (this.value !== value) {
          this.value = value;
        }
      });
    }

    const householdAgeInputs = document.querySelectorAll('input[oninput*="draftIntake.household"][type="number"]');
    householdAgeInputs.forEach(input => {
      input.addEventListener('input', function(e) {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value !== '' && parseInt(value) > 150) value = '150';
        if (this.value !== value) {
          this.value = value;
        }
      });
    });

    lucide.createIcons();
  }, 0);
}

function toggleAgency(key){
  const i = draftIntake.agencies.indexOf(key);
  if(i===-1) draftIntake.agencies.push(key); else draftIntake.agencies.splice(i,1);
  renderIntakeForm();
  lucide.createIcons();
}

function toggleRequirement(index){
  draftIntake.requirements[index].submitted = !draftIntake.requirements[index].submitted;
  renderIntakeForm();
  lucide.createIcons();
}

function validateIntake() {
  const d = draftIntake;
  const errors = [];
  
  // Required fields
  if (!d.controlNo || !d.controlNo.trim()) errors.push('Please enter the <strong>Control Number</strong>');
  if (!d.interview.reportDate) errors.push('Please select the <strong>Report Date</strong>');
  if (!d.purpose) errors.push('Please select the <strong>Purpose / Assistance Type</strong>');
  if (!d.client.name || !d.client.name.trim()) errors.push('Please enter the <strong>Client Full Name</strong>');
  if (!d.client.sex) errors.push('Please select the <strong>Client Sex</strong>');
  if (!d.client.birthdate) errors.push('Please select the <strong>Client Birth Date</strong>');
  if (!d.client.address) errors.push('Please select the <strong>Client Barangay (Address)</strong>');
  if (!d.client.birthplace || !d.client.birthplace.trim()) errors.push('Please enter the <strong>Client Birthplace</strong>');
  if (!d.client.civilStatus) errors.push('Please select the <strong>Civil Status</strong>');
  if (!d.client.religion || !d.client.religion.trim()) errors.push('Please enter the <strong>Religion</strong>');
  if (!d.client.education || !d.client.education.trim()) errors.push('Please enter the <strong>Educational Attainment</strong>');
  if (!d.client.occupation || !d.client.occupation.trim()) errors.push('Please enter the <strong>Occupation</strong>');
  if (!d.client.income || !d.client.income.trim()) errors.push('Please enter the <strong>Monthly Income</strong>');
  if (!d.client.contact || !d.client.contact.trim()) errors.push('Please enter the <strong>Contact Number</strong>');
  if (!d.interview.problemPresented || !d.interview.problemPresented.trim()) errors.push('Please enter the <strong>Problem Presented</strong>');
  if (!d.agencies || d.agencies.length === 0) errors.push('Please select at least <strong>one Agency</strong>');
  if (!d.signers.preparedByName || !d.signers.preparedByName.trim()) errors.push('Please enter the <strong>Prepared By Name</strong>');
  if (!d.signers.notedByName || !d.signers.notedByName.trim()) errors.push('Please enter the <strong>Noted By Name</strong>');

  // Must have at least 1 named household member
  const namedMembers = d.household.filter(m => m.name && m.name.trim());
  if (namedMembers.length === 0) errors.push('Please add at least <strong>one family member name</strong>');
  
  // Format checks
  const age = parseInt(d.client.age);
  if (d.client.age !== '' && d.client.age !== undefined && (isNaN(age) || age < 0 || age > 150))
    errors.push('<strong>Age</strong> must be between 0 and 150');
  
  if (d.client.contact && d.client.contact.trim() && !/^09\d{9}$/.test(d.client.contact.trim()))
    errors.push('Please enter a valid <strong>contact number</strong> (09xxxxxxxxx)');
  
  if (d.client.birthdate && d.client.birthdate.trim()) {
    const bd = new Date(d.client.birthdate + 'T00:00:00');
    if (isNaN(bd.getTime()) || bd >= new Date()) errors.push('<strong>Birthdate</strong> must be a valid past date');
  }
  
  // Household age validation
  d.household.forEach((m, i) => {
    if (m.name && m.name.trim()) {
      if (m.age !== '' && m.age !== undefined) {
        const a = parseInt(m.age);
        if (isNaN(a) || a < 0 || a > 150) errors.push(`Family member ${i + 1} <strong>age</strong> must be between 0 and 150`);
      }
    }
  });
  
  return errors;
}

function reviewIntake() {
  const errs = validateIntake();
  if (errs.length) {
    Swal.fire({
      icon: 'warning',
      iconColor: '#F59E0B',
      title: '<div style="display:flex;flex-direction:column;align-items:center;gap:3px;"><span style="font-size:1.2rem;font-weight:700;color:#1E293B;">Required Information Missing</span><span style="font-size:0.85rem;font-weight:400;color:#64748B;">Please complete the required fields below to proceed</span></div>',
      html: `
        <div class="swal-error-scroll" style="background:#F8FAFC;border:1px solid #E2E8F0;border-radius:12px;padding:10px 14px;max-height:250px;overflow-y:auto;text-align:left;margin-top:8px;">
          ${errs.map((err, idx) => `
            <div style="display:flex;align-items:center;gap:10px;padding:8px 4px;${idx < errs.length - 1 ? 'border-bottom:1px solid #EDF2F7;' : ''}font-size:13px;color:#334155;line-height:1.4;">
              <span style="display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:50%;background:#FEF2F2;color:#DC2626;flex-shrink:0;">
                <i data-lucide="alert-circle" style="width:13px;height:13px;stroke-width:2.5;"></i>
              </span>
              <span style="flex:1;">${err}</span>
            </div>
          `).join('')}
        </div>
        <div style="margin-top:12px;display:flex;align-items:center;justify-content:center;gap:6px;font-size:12px;color:#94A3B8;">
          <i data-lucide="info" style="width:14px;height:14px;color:#64748B;"></i>
          <span>Fields with (<span style="color:#DC2626;font-weight:700;">*</span>) are mandatory</span>
        </div>
      `,
      confirmButtonColor: '#1A237E',
      confirmButtonText: 'Got it, I\'ll complete them',
      customClass: {
        popup: 'swal-custom-popup'
      },
      didOpen: () => {
        if (window.lucide) lucide.createIcons();
      }
    });
    return;
  }
  showIntakeSummaryModal();
}

/* ---------------- Intake Summary Modal (Senior Citizen Registration Style) ---------------- */
function showIntakeSummaryModal(){
  const d = draftIntake;
  if(!d) return;

  const existing = document.getElementById('intakeSummaryModal');
  if(existing) existing.remove();

  const val = v => escapeHtml(String(v || '')) || '<span style="color:#9CA3AF;font-style:italic">—</span>';
  const fmtDateLocal = iso => {
    if(!iso) return '<span style="color:#9CA3AF;font-style:italic">—</span>';
    try {
      const dt = new Date(iso + 'T00:00:00');
      if(isNaN(dt.getTime())) return '<span style="color:#9CA3AF;font-style:italic">—</span>';
      return dt.toLocaleDateString('en-US', {month:'long', day:'numeric', year:'numeric'});
    } catch(e) { return '<span style="color:#9CA3AF;font-style:italic">—</span>'; }
  };

  const selectedAgencies = AGENCIES.filter(a => d.agencies.includes(a.key));

  const modal = document.createElement('div');
  modal.id = 'intakeSummaryModal';
  modal.className = 'custom-modal-backdrop active';
  modal.innerHTML = `
    <div class="custom-modal-dialog">
      <div class="custom-modal-header">
        <h3 class="custom-modal-title">
          <i data-lucide="file-check" style="width:18px;height:18px;"></i>
          Confirm Case Study Record
        </h3>
        <button type="button" class="custom-modal-close" onclick="closeIntakeSummaryModal()">&times;</button>
      </div>
      <div class="custom-modal-body">
        <!-- Identification & Report Info -->
        <div class="custom-modal-section">
          <div class="custom-modal-section-title">
            <i data-lucide="clipboard-list" style="width:16px;height:16px;"></i>
            Report Details & Control Information
          </div>
          <div class="custom-modal-grid">
            <div class="custom-modal-field">
              <label class="custom-modal-label">Control Number</label>
              <div class="custom-modal-value font-mono font-bold" style="color:var(--primary)">${val(d.controlNo)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Report Date</label>
              <div class="custom-modal-value">${fmtDateLocal(d.interview.reportDate)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Purpose / Assistance</label>
              <div class="custom-modal-value">${val(d.purpose)}</div>
            </div>
          </div>
        </div>

        <!-- Personal Information Section -->
        <div class="custom-modal-section">
          <div class="custom-modal-section-title">
            <i data-lucide="user" style="width:16px;height:16px;"></i>
            Personal Information
          </div>
          <div class="custom-modal-grid">
            <div class="custom-modal-field custom-modal-col-span2">
              <label class="custom-modal-label">Full Name</label>
              <div class="custom-modal-value font-bold">${val(d.client.name)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Sex</label>
              <div class="custom-modal-value">${val(d.client.sex)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Birth Date</label>
              <div class="custom-modal-value">${fmtDateLocal(d.client.birthdate)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Month</label>
              <div class="custom-modal-value">${val(d.client.month)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Age</label>
              <div class="custom-modal-value">${val(d.client.age)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Barangay (Address)</label>
              <div class="custom-modal-value">${val(d.client.address)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Birthplace</label>
              <div class="custom-modal-value">${val(d.client.birthplace)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Civil Status</label>
              <div class="custom-modal-value">${val(d.client.civilStatus)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Religion</label>
              <div class="custom-modal-value">${val(d.client.religion)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Educational Attainment</label>
              <div class="custom-modal-value">${val(d.client.education)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Occupation</label>
              <div class="custom-modal-value">${val(d.client.occupation)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Monthly Income</label>
              <div class="custom-modal-value">${val(d.client.income)}</div>
            </div>
            <div class="custom-modal-field custom-modal-col-span2">
              <label class="custom-modal-label">Contact Number</label>
              <div class="custom-modal-value">${val(d.client.contact)}</div>
            </div>
          </div>
        </div>

        <!-- Family Composition Section -->
        <div class="custom-modal-section">
          <div class="custom-modal-section-title">
            <i data-lucide="users" style="width:16px;height:16px;"></i>
            Family Composition (${d.household.length})
          </div>
          <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;background:var(--surface);">
            <table style="width:100%;border-collapse:collapse;font-size:12px;">
              <thead>
                <tr style="background:#F9FAFB;border-bottom:1px solid var(--border);text-align:left;">
                  <th style="padding:8px 10px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;">Name</th>
                  <th style="padding:8px 10px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;">Relationship</th>
                  <th style="padding:8px 10px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;">Age</th>
                  <th style="padding:8px 10px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;">Education</th>
                  <th style="padding:8px 10px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;">Occupation</th>
                  <th style="padding:8px 10px;font-size:11px;font-weight:700;color:var(--text-secondary);text-transform:uppercase;">Income</th>
                </tr>
              </thead>
              <tbody>
                ${d.household.map(m => `
                  <tr style="border-bottom:1px solid var(--border);">
                    <td style="padding:8px 10px;font-weight:600;">${val(m.name)}</td>
                    <td style="padding:8px 10px;">${val(m.relationship)}</td>
                    <td style="padding:8px 10px;">${val(m.age)}</td>
                    <td style="padding:8px 10px;">${val(m.education)}</td>
                    <td style="padding:8px 10px;">${val(m.occupation)}</td>
                    <td style="padding:8px 10px;">${val(m.income)}</td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>

        <!-- Problem Presented Section -->
        <div class="custom-modal-section">
          <div class="custom-modal-section-title">
            <i data-lucide="file-text" style="width:16px;height:16px;"></i>
            Problem Presented
          </div>
          <div class="custom-modal-grid">
            <div class="custom-modal-field custom-modal-col-full">
              <div class="custom-modal-value" style="white-space:pre-wrap;min-height:50px;line-height:1.5;">${val(d.interview.problemPresented)}</div>
            </div>
          </div>
        </div>

        <!-- Agencies & Requirements Section -->
        <div class="custom-modal-section">
          <div class="custom-modal-section-title">
            <i data-lucide="building-2" style="width:16px;height:16px;"></i>
            Agencies & Requirements
          </div>
          <div class="custom-modal-grid">
            <div class="custom-modal-field custom-modal-col-full">
              <label class="custom-modal-label">Assistance Agencies</label>
              <div class="custom-modal-value" style="flex-wrap:wrap;gap:6px;padding:8px 10px;">
                ${selectedAgencies.length ? selectedAgencies.map(a => `<span style="display:inline-flex;align-items:center;padding:3px 8px;border-radius:6px;background:#EEF2FF;color:var(--primary);font-size:12px;font-weight:600;border:1px solid #C7D2FE;">${a.key === 'OP' ? 'Office of the President' : `${escapeHtml(a.key)} - ${escapeHtml(a.name)}`}</span>`).join('') : '<span style="color:#9CA3AF;font-style:italic">None selected</span>'}
              </div>
            </div>
            <div class="custom-modal-field custom-modal-col-full">
              <label class="custom-modal-label">Verified Requirements</label>
              <div class="custom-modal-value" style="flex-direction:column;align-items:flex-start;gap:6px;padding:8px 10px;">
                ${d.requirements.map(r => `
                  <div style="display:flex;align-items:center;gap:6px;font-size:12px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:16px;height:16px;border-radius:50%;background:${r.submitted ? '#16A34A' : '#EF4444'};color:#fff;font-size:10px;font-weight:bold;">${r.submitted ? '✓' : '✕'}</span>
                    <span style="color:${r.submitted ? 'var(--text-primary)' : 'var(--text-muted)'}">${escapeHtml(r.name)}</span>
                  </div>
                `).join('')}
              </div>
            </div>
          </div>
        </div>

        <!-- Signatories Section -->
        <div class="custom-modal-section">
          <div class="custom-modal-section-title">
            <i data-lucide="pen-tool" style="width:16px;height:16px;"></i>
            Signatories
          </div>
          <div class="custom-modal-grid">
            <div class="custom-modal-field">
              <label class="custom-modal-label">Prepared By (Name)</label>
              <div class="custom-modal-value">${val(d.signers.preparedByName)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Prepared By (Title)</label>
              <div class="custom-modal-value">${val(d.signers.preparedByTitle)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Noted By (Name)</label>
              <div class="custom-modal-value">${val(d.signers.notedByName)}</div>
            </div>
            <div class="custom-modal-field">
              <label class="custom-modal-label">Noted By (Title)</label>
              <div class="custom-modal-value">${val(d.signers.notedByTitle)}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="custom-modal-footer">
        <button type="button" class="btn" onclick="closeIntakeSummaryModal()">Go Back</button>
        <button type="button" class="btn primary" onclick="closeIntakeSummaryModal(); saveNewCase();">
          <i data-lucide="check-circle" style="width:16px;height:16px"></i> Confirm & Save Case
        </button>
      </div>
    </div>
  `;

  document.body.appendChild(modal);
  modal.addEventListener('click', e => { if(e.target === modal) closeIntakeSummaryModal(); });
  lucide.createIcons();
}

function closeIntakeSummaryModal(){
  const modal = document.getElementById('intakeSummaryModal');
  if(modal) modal.remove();
}

/* ---------------- Rendering: Case list ---------------- */
async function loadCaseList(){
  console.log('loadCaseList called');
  await loadCases();
  console.log('loadCases completed, rendering...');
  renderCaseList();
  lucide.createIcons();
}

function renderCaseList(){

  // Get filter values
  const searchQuery = (document.getElementById('searchInput')?.value || "").toLowerCase();
  const statusFilter = (window.filterState?.status) || "All";
  const assistanceFilter = (window.filterState?.assistance) || "All";
  const barangayFilter = (window.filterState?.barangay) || "All";

  // Filter cases (exclude archived – those live on the archive page)
  // Only show cases the encoder has picked up: encoded at intake, or already printed
  let filtered = cases.filter(c => {
    if(c.status === 'Archived') return false;
    if(c.encodedBy == null && c.status !== 'Printed') return false;
    const matchesSearch = !searchQuery || 
      (c.client?.name || '').toLowerCase().includes(searchQuery) || 
      c.controlNo.toLowerCase().includes(searchQuery) ||
      c.purpose.toLowerCase().includes(searchQuery);
    const matchesStatus = statusFilter === "All" || c.status === statusFilter;
    const matchesAssistance = assistanceFilter === "All" || c.purpose === assistanceFilter;
    const matchesBarangay = barangayFilter === "All" || c.client?.barangay === barangayFilter || c.client?.address === barangayFilter;
    return matchesSearch && matchesStatus && matchesAssistance && matchesBarangay;
  });

  // Sort by date (newest first)
  filtered = [...filtered].sort((a,b)=> new Date(b.updatedAt)-new Date(a.updatedAt));

  // Pagination
  const pageSize = 10;
  const currentPage = view.caseListPage || 1;
  const totalPages = Math.ceil(filtered.length / pageSize);
  if(currentPage > totalPages && totalPages > 0) view.caseListPage = totalPages;
  const page = Math.max(1, Math.min(view.caseListPage || 1, totalPages || 1));
  const startIndex = (page - 1) * pageSize;
  const endIndex = startIndex + pageSize;
  const paginatedCases = filtered.slice(startIndex, endIndex);

  // Render table
  const tableBody = document.getElementById('casesTableBody');
  const emptyState = document.getElementById('emptyState');
  const table = document.getElementById('dataTable');

  if(table) table.style.display = 'table';
  if(emptyState) emptyState.style.display = 'none';

  if(paginatedCases.length === 0){
    tableBody.innerHTML = `
      <tr class="empty-row">
        <td colspan="7" class="empty-cell">
          <div class="empty-state-content">
            <div class="empty-icon-wrap">
              <i data-lucide="folder-open"></i>
            </div>
            <div class="empty-title">No Social Case Studies Found</div>
            <div class="empty-subtitle">Create your first Social Case Study to begin managing case records.</div>
          </div>
        </td>
      </tr>
    `;
  }else{
    tableBody.innerHTML = paginatedCases.map(c => {
      return `
      <tr>
        <td data-label="Control No."><span class="control-no">${escapeHtml(c.controlNo)||"—"}</span></td>
        <td data-label="Client">${escapeHtml(c.client?.name)||"<span class=muted>Unnamed</span>"}</td>
        <td data-label="Type">${escapeHtml(c.purpose)}</td>
        <td data-label="Barangay">${escapeHtml(c.client?.address || c.client?.barangay || '—')}</td>
        <td data-label="Status"><span class="badge ${STATUS_CLASS[c.status]}">${c.status}</span></td>
        <td data-label="Created">${fmtDate(c.createdAt)}</td>
        <td data-label="Action">
          <div class="actions" style="display:flex; gap: 4px;">
            <button style="background-color: #1A237E; border: none; border-radius: 6px; padding: 6px 10px; cursor:pointer;" onclick="event.stopPropagation(); showCaseDetailsModal('${c.id}')" title="View">
              <i data-lucide="eye" style="width:16px;height:16px; color:#ffffff;"></i>
            </button>
            ${CAN_ENCODE && c.status === 'Approved' ? `
              <button style="background-color: #FBC02D; border: none; border-radius: 6px; padding: 6px 10px; cursor:pointer;" onclick="event.stopPropagation(); window.location.href='/admin/social-case/document/${c.id}/PCSO'" title="Print">
                <i data-lucide="printer" style="width:16px;height:16px; color:#121858;"></i>
              </button>
            ` : ''}
            ${CAN_ENCODE && c.status !== 'Archived' ? `
              <button style="background-color: #dc3545; border: none; border-radius: 6px; padding: 6px 10px; cursor:pointer;" onclick="event.stopPropagation(); deleteCase('${c.id}', true)" title="Archive">
                <i data-lucide="archive" style="width:16px;height:16px; color:#ffffff;"></i>
              </button>
            ` : ''}
          </div>
        </td>
      </tr>`;
    }).join("");
  }

  // Update pagination info
  const paginationInfo = document.getElementById('paginationInfo');
  if(filtered.length === 0){
    paginationInfo.textContent = 'Showing 0 of 0 Records';
  }else{
    const showingFrom = startIndex + 1;
    const showingTo = Math.min(endIndex, filtered.length);
    paginationInfo.textContent = `Showing ${showingFrom}–${showingTo} of ${filtered.length} Records`;
  }

  // Update pagination controls
  const controls = document.getElementById('paginationControls');
  let pageButtons = '';
  pageButtons += `<button class="sc-page-btn" id="prevBtn" ${page<=1?'disabled':''} onclick="goToCaseListPage(${page-1})"><i data-lucide="chevron-left" style="width:14px;height:14px"></i> Previous</button>`;
  
  // Always show current page
  pageButtons += `<button class="sc-page-btn active" onclick="goToCaseListPage(${page})">${page}</button>`;
  
  pageButtons += `<button class="sc-page-btn" id="nextBtn" ${page>=totalPages?'disabled':''} onclick="goToCaseListPage(${page+1})">Next <i data-lucide="chevron-right" style="width:14px;height:14px"></i></button>`;
  controls.innerHTML = pageButtons;

  lucide.createIcons();
}

function goToCaseListPage(page){
  view.caseListPage = page;
  renderCaseList();
}

function applyFilters(){
  view.caseListPage = 1;
  renderCaseList();
}

function resetFilters(){
  const searchInput = document.getElementById('searchInput');
  if(searchInput) searchInput.value = '';

  window.filterState = { status: 'All', assistance: 'All', barangay: 'All' };
  
  document.getElementById('statusLabel').textContent = 'All Status';
  document.getElementById('assistanceLabel').textContent = 'All Types';
  document.getElementById('barangayLabel').textContent = 'All Barangays';
  
  var statusBtn = document.getElementById('statusBtn');
  if(statusBtn) { statusBtn.classList.remove('active'); statusBtn.removeAttribute('data-filter'); }
  var assistanceBtn = document.getElementById('assistanceBtn');
  if(assistanceBtn) { assistanceBtn.classList.remove('active'); assistanceBtn.removeAttribute('data-filter'); }
  var barangayBtn = document.getElementById('barangayBtn');
  if(barangayBtn) { barangayBtn.classList.remove('active'); barangayBtn.removeAttribute('data-filter'); }
  
  highlightStatusOpt();
  highlightAssistanceOpt();
  highlightBarangayOpt();
  
  view.caseListPage = 1;
  renderCaseList();
}

function sortBy(field){
  // Would implement actual sorting logic
  console.log('Sorting by:', field);
}

function bulkDelete(){
  alert('Bulk delete functionality would be implemented here - requires selection checkboxes');
}

function exportExcel(){
  alert('Export to Excel functionality would be implemented here');
}

function exportPDF(){
  alert('Export to PDF functionality would be implemented here');
}

function printReport(){
  window.print();
  setTimeout(() => location.reload(), 1000);
}

async function markAsPrinted(caseId){
  const caseRec = getCase(caseId);
  if(!caseRec) return;
  
  // Update status to Released if not already
  if(caseRec.status === 'Draft' || caseRec.status === 'Review' || caseRec.status === 'Approved'){
    // Add Approved status to history first
    if(caseRec.status !== 'Approved'){
      caseRec.statusHistory.push({status: 'Approved', date: todayISO()});
    }
    
    // Then mark as Released
    caseRec.status = 'Released';
    caseRec.updatedAt = todayISO();
    caseRec.releasedDate = todayISO();
    caseRec.statusHistory.push({status: 'Released', date: todayISO()});
    
    // Update in database
    const payload = convertKeys(caseRec, camelToSnake);
    await fetch(`/admin/social-case/api/cases/${caseId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(async data => {
      console.log('Case marked as approved and released:', data);
      logActivity('printed', 'Case document printed', {
        clientName: caseRec.client?.name,
        controlNo: caseRec.controlNo
      });
      // Reload cases from server to get latest data
      await loadCases();
    })
    .catch(error => {
      console.error('Error updating case status:', error);
    });
  }
}

function removePrintArtifacts(){
  const s = document.getElementById('printStyle');
  if(s) s.remove();
  const pc = document.getElementById('printOnlyContainer');
  if(pc) pc.remove();
}

function reloadAfterPrint(){
  let done = false;
  const reload = () => {
    if(done) return;
    done = true;

    // Show SweetAlert success message after printing
    Swal.fire({
      icon: 'success',
      title: 'Printed Successfully',
      text: 'Social case study has been printed successfully.',
      timer: 3000,
      timerProgressBar: true,
      showConfirmButton: false,
      customClass: { popup: 'rounded-4 shadow-lg' }
    }).then(() => {
      // Reload page after SweetAlert closes
      location.reload();
    });
  };
  window.addEventListener('afterprint', reload);
  window.addEventListener('focus', reload, { once: true });
}

async function printDocument(){
  const c = getCase(view.caseId);
  if(!c) return;
  
  // Clear any leftover print-only DOM from a previous print. The print-only
  // container/style are intentionally left mounted after printing (see below),
  // so every print call must start clean.
  removePrintArtifacts();

  // Fetch authoritative document date + age from the backend
  const docData = await fetchDocumentData(view.caseId);

  const agencies = c.agencies || (c.submittedTo ? c.submittedTo.split(',').map(s => s.trim()).filter(Boolean) : []);
  
  if(agencies.length === 0) {
    // If no agencies selected, just print the current preview.
    // Nothing is mutated here, so no cleanup or reload is needed.
    await markAsPrinted(view.caseId);
    window.print();
    reloadAfterPrint();
    return;
  }
  
  // Generate document pages for all agencies by manually replacing agency names
  const container = document.getElementById('documentPreviewContainer');
  if(!container) return;
  
  let allPages = '';
  
  try {
    // Fetch template once with cache busting
    const response = await fetch('/templates/social-case-report.html?v=' + Date.now());
    if (!response.ok) throw new Error('Failed to load template');
    const template = await response.text();
    
    // Get case data once
    const famRows = (c.familyMembers || c.household || []).filter(m=>m.fullName || m.name || m.full_name);
    const notProvided = "Not Provided";
    const clientSexLower = (c.client?.sex || c.client?.gender || "").toLowerCase();
    const pronoun = clientSexLower === 'male' ? 'his' : 'her';
    const pronounCap = clientSexLower === 'male' ? 'His' : 'Her';
    const clientPronoun = clientSexLower === 'male' ? 'him' : 'her';
    
    // Prepare document reference number
    const docRefNumber = String(docData?.document_ref_number || 1).padStart(2, '0');
    
    const homeConditionDefault = `The client resides in a modest home with ${pronoun} family. The home of the family in modest circumstances is simple but functional. While the house may not have the latest appliances or decor, it is clean and maintained to the best of the family's ability. The family may prioritize practicality over style, and although they may face financial challenges, their home remains a place of warmth, care, and togetherness.`;
    const _purposeForDefault = (c.purpose || "").toLowerCase();
    const _defaultSubject = _purposeForDefault.includes('medical') ? 'patient' : _purposeForDefault.includes('burial') ? 'deceased' : 'client';
    const _defaultPossessive = _defaultSubject + "'s";
    const _assistanceType = _purposeForDefault.includes('medical') ? 'medical' : _purposeForDefault.includes('burial') ? 'burial' : _purposeForDefault.includes('educational') ? 'educational' : 'financial';
    const _expenseType = _assistanceType === 'medical' ? 'medical expenses' : _assistanceType === 'burial' ? 'burial expenses' : _assistanceType === 'educational' ? 'educational expenses' : 'urgent expenses';
    const socioEconomicDefault = `The family is indigent, and the client depends on their family's income to cover daily expenses and household needs. Unfortunately, there is insufficient funds to sustain the ${_expenseType} of the ${_defaultSubject}.`;
    const evaluationDefault = `This case concerns a client in need of ${_assistanceType} assistance for ${_expenseType}. Due to the ${_defaultPossessive} socio-economic condition, the client is unable to support the ${_expenseType}, prompting ${pronoun} to seek help from your good office, as reflected in the attached documents. The incurred expenses have placed a heavy burden on the family, depleting their financial resources. Consequently, they are earnestly requesting assistance from your office to alleviate their situation.`;
    const recommendationDefault = `Due to the lack of sufficient income and the absence of alternative financial resources to meet the ${_defaultPossessive} needs, the undersigned worker respectfully recommends that the ${_defaultSubject} be considered for assistance from your office to cover the ${_expenseType} required.`;

    const clientName = escapeHtml((c.client?.fullName || c.client?.full_name || c.client?.name || c.clientName || c.client_name || "")).toUpperCase() || notProvided;
    const clientAge = docData && docData.client_age !== null
      ? escapeHtml(String(docData.client_age))
      : (escapeHtml(String(c.client?.age || "")) || notProvided);
    const clientSex = escapeHtml((c.client?.sex || c.client?.gender || "")).toUpperCase() || notProvided;
    const clientAddress = escapeHtml((c.client?.address || "")).toUpperCase() || notProvided;
    const clientBirthdate = c.client?.birthdate ? fmtDate(c.client.birthdate).toUpperCase() : notProvided;
    const clientBirthplace = escapeHtml((c.client?.birthplace || "")).toUpperCase() || notProvided;
    const clientReligion = escapeHtml((c.client?.religion || "")).toUpperCase() || notProvided;
    const clientEducation = escapeHtml((c.client?.education || "")).toUpperCase() || notProvided;
    const clientCivilStatus = escapeHtml((c.client?.civilStatus || c.client?.civil_status || "")).toUpperCase() || notProvided;
    const clientOccupation = escapeHtml((c.client?.occupation || "")) || notProvided;
    const clientIncome = escapeHtml((c.client?.income || "")) || notProvided;
    const clientContact = escapeHtml((c.client?.contact || c.client?.contactNumber || c.client?.contact_number || "")) || notProvided;
    const reportDate = docData && docData.document_date
      ? fmtDate(docData.document_date).toUpperCase()
      : fmtDate(new Date().toISOString().slice(0,10)).toUpperCase();
    const rawProblem = c.interview?.interviewSituation || c.interview?.interview_situation || c.interview?.problemPresented || "";
    const purpose = c.purpose || "";
    const clientFirstName = (c.client?.firstName || c.client?.first_name || "").trim();
    const clientLastName = (c.client?.lastName || c.client?.last_name || "").trim();
    const clientFullName = (c.client?.fullName || c.client?.full_name || clientFirstName + " " + clientLastName).trim();
    const ip = boldProblemText(escapeHtml(rewriteProblemPresented(rawProblem, purpose, clientFullName, c.client, c.household || c.familyMembers || []))) || notProvided;
    const ih = escapeHtml(c.interview?.interviewHousehold || c.interview?.interview_household || c.interview?.homeCondition || "") || homeConditionDefault;
    const ie = escapeHtml(c.interview?.interviewNotes || c.interview?.interview_notes || c.interview?.socioEconomic || "") || socioEconomicDefault;
    const iw = escapeHtml(c.interview?.socialWorkerAssessment || c.interview?.social_worker_assessment || c.interview?.evaluation || "") || evaluationDefault;
    const ir = escapeHtml(c.interview?.recommendation || "") || recommendationDefault;
    const preparedName = escapeHtml(c.signers?.preparedByName || c.officer?.name || "") || notProvided;
    const preparedTitle = escapeHtml(c.signers?.preparedByTitle || c.officer?.position || "");
    const notedName = escapeHtml(c.signers?.notedByName || c.encoder?.name || "") || notProvided;
    const notedTitle = escapeHtml(c.signers?.notedByTitle || c.encoder?.position || "");
    const notedLicense = escapeHtml(c.signers?.notedByLicense || "");

    const familyTable = famRows.length ? `
      <table style="border-radius: 0; ${famRows.length > 1 ? 'font-size: 11px;' : ''}">
        <thead>
          <tr>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">RELATIVES</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">RELATIONSHIP</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">AGE</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">EDUCATIONAL ATTAINMENT</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">OCCUPATION</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">INCOME</th>
          </tr>
        </thead>
        <tbody>
          ${famRows.map(m=>`<tr>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.fullName || m.full_name || m.name || "").toUpperCase())}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.relationship || "—").toUpperCase())}</td>
            <td align="center" style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml(String(m.age || "")) || "—"}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.education || "—").toUpperCase())}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.occupation || "N/A").toUpperCase())}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml(String(m.monthlyIncome || m.income || "")) || "N/A"}</td>
          </tr>`).join("")}
        </tbody>
      </table>` : `<div style="color:#999;margin-top:8px;font-style:italic;">None listed.</div>`;

    // Generate pages for each agency
    let totalPages = 0;
    const agencyPageCounts = [];
    
    for(const agency of agencies) {
      const agencyKey = agency.toUpperCase();
      const agencyInfo = AGENCIES.find(a => a.key === agencyKey) || AGENCIES[0];
      const agencyName = agencyInfo.name;
      
      let pageTemplate = template;
      pageTemplate = pageTemplate.replace(/{{REPORT_DATE}}/g, reportDate);
      pageTemplate = pageTemplate.replace(/{{CONTROL_NUMBER}}/g, escapeHtml(c.controlNo || c.caseNumber || ""));
      pageTemplate = pageTemplate.replace(/{{PURPOSE}}/g, escapeHtml(c.purpose || "Financial / Medical"));
      pageTemplate = pageTemplate.replace(/{{CLIENT_NAME}}/g, clientName);
      pageTemplate = pageTemplate.replace(/{{CLIENT_AGE}}/g, clientAge);
      pageTemplate = pageTemplate.replace(/{{CLIENT_SEX}}/g, clientSex);
      pageTemplate = pageTemplate.replace(/{{CLIENT_ADDRESS}}/g, clientAddress);
      pageTemplate = pageTemplate.replace(/{{CLIENT_BIRTHDATE}}/g, clientBirthdate);
      pageTemplate = pageTemplate.replace(/{{CLIENT_BIRTHPLACE}}/g, clientBirthplace);
      pageTemplate = pageTemplate.replace(/{{CLIENT_RELIGION}}/g, clientReligion);
      pageTemplate = pageTemplate.replace(/{{CLIENT_EDUCATION}}/g, clientEducation);
      pageTemplate = pageTemplate.replace(/{{CLIENT_CIVIL_STATUS}}/g, clientCivilStatus);
      pageTemplate = pageTemplate.replace(/{{CLIENT_OCCUPATION}}/g, clientOccupation);
      pageTemplate = pageTemplate.replace(/{{CLIENT_INCOME}}/g, clientIncome);
      pageTemplate = pageTemplate.replace(/{{CLIENT_CONTACT}}/g, clientContact);
      pageTemplate = pageTemplate.replace(/{{FAMILY_TABLE}}/g, familyTable);
      pageTemplate = pageTemplate.replace(/{{PROBLEM_PRESENTED}}/g, ip);
      pageTemplate = pageTemplate.replace(/{{HOME_CONDITION}}/g, ih);
      pageTemplate = pageTemplate.replace(/{{SOCIO_ECONOMIC}}/g, ie);
      pageTemplate = pageTemplate.replace(/{{EVALUATION}}/g, iw);
      pageTemplate = pageTemplate.replace(/{{RECOMMENDATION}}/g, ir);
      pageTemplate = pageTemplate.replace(/{{PREPARED_NAME}}/g, preparedName);
      pageTemplate = pageTemplate.replace(/{{PREPARED_TITLE}}/g, preparedTitle);
      pageTemplate = pageTemplate.replace(/{{NOTED_NAME}}/g, notedName);
      pageTemplate = pageTemplate.replace(/{{NOTED_TITLE}}/g, notedTitle);
      pageTemplate = pageTemplate.replace(/{{NOTED_LICENSE}}/g, notedLicense ? 'License No. ' + notedLicense : '');
      pageTemplate = pageTemplate.replace(/{{AGENCY_NAME}}/g, escapeHtml(agencyName));
      pageTemplate = pageTemplate.replace(/{{DOCUMENT_REF_NUMBER}}/g, docRefNumber);
      
      // Split by PAGE_BREAK to get individual pages
      const parts = pageTemplate.split('<!--PAGE_BREAK-->');
      parts.forEach((part, index) => {
        allPages += `<div class="page">${part}</div>`;
      });
    }
    
    // Create a print-only container
    const printContainer = document.createElement('div');
    printContainer.id = 'printOnlyContainer';
    printContainer.style.position = 'fixed';
    printContainer.style.top = '0';
    printContainer.style.left = '0';
    printContainer.style.width = '215.9mm';
    printContainer.style.height = 'auto';
    printContainer.style.zIndex = '999999';
    printContainer.style.background = 'white';
    printContainer.style.display = 'none';
    printContainer.innerHTML = allPages;
    document.body.appendChild(printContainer);
    
    // Show print container only during print with proper page styling
    const printStyle = document.createElement('style');
    printStyle.id = 'printStyle';
    printStyle.textContent = `
      @media print {
        @page {
          size: 215.9mm 279.4mm;
          margin: 0;
        }
        * {
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
        }
        html, body {
          margin: 0 !important;
          padding: 0 !important;
          width: 215.9mm !important;
          height: auto !important;
          background: white !important;
          overflow: visible !important;
        }
        body > *:not(#printOnlyContainer) { display: none !important; }
        #printOnlyContainer { 
          display: block !important; 
          position: relative !important;
          width: 215.9mm !important;
          height: auto !important;
          margin: 0 !important;
          padding: 0 !important;
          background: white !important;
          overflow: visible !important;
        }
        .page {
          width: 215.9mm !important;
          height: 279.4mm !important;
          margin: 0 !important;
          padding: 12.7mm 25.4mm 32.4mm 25.4mm !important;
          background: white !important;
          position: relative !important;
          box-shadow: none !important;
          overflow: hidden !important;
          border: none !important;
          transform: none !important;
          zoom: 1 !important;
        }
      }
    `;
    document.head.appendChild(printStyle);
    
    await markAsPrinted(view.caseId);

    // window.print() is non-blocking on mobile/tablet browsers, and even desktop
    // browsers emit 'afterprint' / print-media changes when the dialog closes —
    // which is not the same as the print job finishing. Removing #printOnlyContainer
    // or #printStyle (or re-rendering the page) around the print call races the
    // print job and can blank the output or let the app chrome (navbar, sidebar,
    // mobile-header) leak into it. So the print-only DOM is left mounted: it is
    // display:none on screen and only styled under @media print, so it is invisible
    // on the page, and the next printDocument() call clears it.
    window.print();
    reloadAfterPrint();

  } catch(error) {
    console.error('Error generating print document:', error);
    alert('Failed to generate print document. Please try again.');
  }
}

async function reprintCase(caseId){
  const caseRec = getCase(caseId);
  if(!caseRec) return;

  // Fetch authoritative date + age from backend (not from cached JS state)
  const docData = await fetchDocumentData(caseId);

  // Only send the minimal fields that change on reprint — do NOT send the
  // full case payload (which includes stale client sub-object).
  const payload = {
    released_at: docData?.document_date || todayISO() + 'T00:00:00',
    updated_at:  docData?.document_date || todayISO() + 'T00:00:00',
  };

  await fetch(`/admin/social-case/api/cases/${caseId}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
      'Accept': 'application/json'
    },
    body: JSON.stringify(payload)
  })
  .then(response => response.json())
  .then(async data => {
    console.log('Case updated for reprint:', data);
    logActivity('printed', 'Case document reprinted', {
      clientName: caseRec.client?.name,
      controlNo: caseRec.controlNo,
      reprintDate: docData?.document_date || todayISO(),
      clientAge: docData?.client_age ?? null,
    });
    await loadCaseDetail(caseId);
    Swal.fire({
      title: 'Reprint Successful',
      html: `Case <strong>${escapeHtml(caseRec.controlNo || '')}</strong> has been updated with today's date (<strong>${escapeHtml(docData?.document_date || todayISO())}</strong>).${docData?.client_age !== null ? '<br>Client age at reprint: <strong>' + escapeHtml(String(docData.client_age)) + '</strong>' : ''}<br><br>You can now print the document.`,
      icon: 'success',
      confirmButtonColor: '#1A237E',
      confirmButtonText: 'OK',
      background: '#ffffff',
      customClass: { popup: 'rounded-4 shadow-lg' }
    });
  })
  .catch(error => {
    console.error('Error updating case for reprint:', error);
    Swal.fire({
      title: 'Error',
      text: 'Failed to update case. Please try again.',
      icon: 'error',
      confirmButtonColor: '#DC2626',
      confirmButtonText: 'OK',
      background: '#ffffff',
      customClass: { popup: 'rounded-4 shadow-lg' }
    });
  });
}

function downloadPDF(){
  const container = document.getElementById('documentPreviewContainer');
  if(!container) return;
  
  // Create a simple print-to-PDF by triggering print dialog
  // For actual PDF generation, you would need a library like html2pdf or jsPDF
  window.print();
  setTimeout(() => location.reload(), 1000);
}

function downloadWord(){
  const container = document.getElementById('documentPreviewContainer');
  if(!container) return;
  
  // Clone the container to avoid modifying the original
  const clone = container.cloneNode(true);
  
  // Remove any non-page elements that might be in the container
  const pages = clone.querySelectorAll('.page');
  let cleanContent = '';
  pages.forEach(page => {
    cleanContent += page.outerHTML;
  });
  
  // Create a simple HTML document with Word-compatible MIME type
  const htmlDocument = `
    <html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word'>
    <head>
      <meta charset="utf-8">
      <title>Social Case Study Report</title>
      <style>
        body { margin: 0; padding: 0; }
        .page { 
          width: 215.9mm; 
          min-height: 279.4mm; 
          margin: 0; 
          padding: 12.7mm 25.4mm 32.4mm 25.4mm; 
          background: white; 
          position: relative; 
          page-break-after: always; 
        }
        .page:last-child { page-break-after: avoid; }
        .watermark {
          position: absolute;
          width: 135mm;
          left: 50%;
          top: 50%;
          transform: translate(-50%, -50%);
          opacity: .06;
          z-index: 1;
          pointer-events: none;
        }
        .content { position: relative; z-index: 2; }
        .header { display: grid; grid-template-columns: 85px 1fr 85px; align-items: start; }
        .header img { width: 75px; height: 75px; object-fit: contain; }
        .gov { text-align: center; line-height: 1.2; padding-top: 12px; }
        .gov div { font-size: 14px; }
        .gov h2 { margin: 6px 0 0; font-family: Arial; font-size: 14px; font-weight: bold; letter-spacing: .5px; white-space: nowrap; }
        .line { border-top: 2px solid black; margin: 8px 0 2px; }
        .line2 { border-top: 1px solid black; margin-bottom: 12px; }
        .top-info { display: flex; justify-content: space-between; font-family: Arial; font-size: 11px; }
        .section { margin-top: 16px; }
        .section-title { font-weight: bold; border-bottom: 2px solid #1E3A8A; padding-bottom: 4px; margin-bottom: 8px; }
        .row { display: flex; padding: 4px 0; }
        .row span:first-child { width: 180px; font-weight: bold; }
        .row span:nth-child(2) { margin: 0 8px; }
        .paragraph { margin-top: 8px; line-height: 1.6; text-align: justify; }
        .footer { display: flex; justify-content: space-between; margin-top: 32px; }
        .signature { text-align: center; }
        .signature b { display: block; margin-top: 32px; }
        .document-footer { position: absolute; bottom: 12.7mm; left: 25.4mm; right: 25.4mm; display: flex; justify-content: space-between; font-size: 11px; }
        .doc-address { text-align: center; }
      </style>
    </head>
    <body>
      ${cleanContent}
    </body>
    </html>
  `;
  
  // Create blob and download
  const blob = new Blob(['\ufeff', htmlDocument], {
    type: 'application/msword'
  });
  
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = `Social_Case_Study_${view.caseId}.doc`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  URL.revokeObjectURL(url);
}

/* ---------------- Rendering: Case detail ---------------- */
async function loadCaseDetail(caseId){
  console.log('loadCaseDetail called with caseId:', caseId);
  // Fetch individual case with interview and family members
  try {
    const response = await fetch(`/admin/social-case/api/cases/${caseId}`);
    if (!response.ok) throw new Error('Failed to load case');
    const caseData = await response.json();
    console.log('Case data from API:', caseData);
    
    // Convert snake_case to camelCase
    const convertedCase = convertKeys(caseData, snakeToCamel);
    
    // Update cases array with this case data
    const existingIndex = cases.findIndex(c => c.id == caseId);
    if (existingIndex >= 0) {
      cases[existingIndex] = convertedCase;
    } else {
      cases.push(convertedCase);
    }
    
    view = {tab:"caseDetail", caseId:caseId};
    renderCaseDetail();
    lucide.createIcons();
  } catch (error) {
    console.error('Error loading case:', error);
    const container = document.getElementById('caseDetailContent');
    if (container) {
      container.innerHTML = `<div style="padding:40px;text-align:center;color:#DC2626;">
        <p style="font-size:16px;font-weight:600;">Error loading case</p>
        <p style="font-size:14px;margin-top:8px;">${error.message}</p>
      </div>`;
    }
  }
}

function renderCaseDetail(){
  console.log('renderCaseDetail called');
  const container = document.getElementById('caseDetailContent');
  if(!container){
    console.error('caseDetailContent container not found');
    return;
  }
  console.log('Container found, caseId:', view.caseId);

  const c = getCase(view.caseId);
  console.log('getCase result:', c);
  if(!c){
    console.error('Case not found for ID:', view.caseId);
    container.innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;color:#6B7280;">
        <i data-lucide="alert-triangle" style="width:48px;height:48px;margin-bottom:16px;color:#D1D5DB;"></i>
        <p style="font-size:1rem;font-weight:600;">Case not found.</p>
        <p style="font-size:14px;margin-top:8px;">Case ID: ${view.caseId}</p>
        <button onclick="window.location.href='/admin/social-case/cases'" style="margin-top:16px;padding:8px 20px;background:#1A237E;color:white;border:none;border-radius:6px;cursor:pointer;font-size:14px;">← Back to Cases</button>
      </div>`;
    lucide.createIcons();
    return;
  }

  if (!c.requirements) c.requirements = [];
  if (!c.signers) c.signers = {};
  if (!c.agencies) {
    c.agencies = c.submittedTo ? c.submittedTo.split(',').map(s => s.trim()).filter(Boolean) : [];
  }
  if (!c.statusHistory) c.statusHistory = [];
  if (!c.household) c.household = c.familyMembers || [];

  // Set selectedAgency to the first available agency from the case
  if (c.agencies && c.agencies.length > 0) {
    if (!selectedAgency || !c.agencies.includes(selectedAgency)) {
      selectedAgency = c.agencies[0];
    }
  }

  console.log('Case data normalized, agencies:', c.agencies, 'selectedAgency:', selectedAgency);

  const interview = c.interview || {};
  const ip = interview.interviewSituation || interview.interview_situation || interview.problemPresented || '';
  const ih = interview.interviewHousehold || interview.interview_household || interview.homeCondition || '';
  const ie = interview.interviewNotes || interview.interview_notes || interview.socioEconomic || '';
  const iw = interview.socialWorkerAssessment || interview.social_worker_assessment || interview.evaluation || '';
  const ir = interview.recommendation || '';

  const client = c.client || {};
  const missingReqs = c.requirements.filter(r=>!r.submitted);

  function row(label, value){
    return `<div style="display:flex;padding:11px 0;border-bottom:1px solid var(--border);font-size:14px;">
      <span style="width:160px;min-width:160px;color:var(--text-secondary);font-weight:500;">${label}</span>
      <span style="flex:1;font-weight:600;text-align:right;color:var(--text-primary);word-break:break-word;">${value||'—'}</span>
    </div>`;
  }

  function card(title, content, extra){
    return `<div class="panel" style="margin-bottom:20px;">
      <h3 style="font-size:14px;font-weight:700;color:#1E3A8A;text-transform:uppercase;letter-spacing:.05em;margin-bottom:12px;">${title}</h3>
      ${content}
      ${extra||''}
    </div>`;
  }

  const statusColors = {
    'Draft':    {bg:'#F3F4F6', color:'#374151'},
    'Review':   {bg:'#FEF3C7', color:'#92400E'},
    'Approved': {bg:'#D1FAE5', color:'#065F46'},
    'Printed':  {bg:'#DBEAFE', color:'#1E40AF'},
    'Released': {bg:'#EDE9FE', color:'#5B21B6'},
    'Archived': {bg:'#F3F4F6', color:'#6B7280'},
  };
  const sc = statusColors[c.status] || statusColors['Draft'];

  /* inject responsive helper style once */
  if(!document.getElementById('detailPageStyle')){
    const s=document.createElement('style');s.id='detailPageStyle';
    s.textContent=`
      .detail-col-left,.detail-col-right{min-width:0;}
      .detail-topbar{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;flex-wrap:wrap;gap:12px;}
      .detail-topbar h1{margin:0;font-size:1.5rem;font-weight:700;color:#111827;}
      @media(max-width:900px){.detail-two-col{grid-template-columns:1fr!important;}}
    `;
    document.head.appendChild(s);
  }

  console.log('Generating HTML for detail page...');
  try {
    const html = `
    <style>
      @media print {
        @page {
          margin: 0;
          size: 215.9mm 279.4mm;
        }
        body {
          background: white !important;
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
        }
        .sidebar, .mobile-header, header, .doc-toolbar, .no-print,
        .hamburger-btn, .sidebar-overlay,
        .detail-header, .header-actions, .template-tabs,
        .preview-header, .info-banner, .status-badge {
          display: none !important;
        }
        .main {
          padding: 0 !important;
          max-width: none !important;
          margin: 0 !important;
        }
        .detail-content, .right-panel {
          margin: 0 !important;
          padding: 0 !important;
        }
        #documentPreviewContainer {
          max-height: none !important;
          overflow: visible !important;
          border: none !important;
          border-radius: 0 !important;
          background: white !important;
          margin: 0 !important;
          padding: 0 !important;
          display: block !important;
          gap: 0 !important;
          min-height: auto !important;
        }
        .page-wrap {
          margin: 0 !important;
          height: auto !important;
          overflow: visible !important;
          padding: 0 !important;
        }
        .page {
          height: 279.4mm !important;
          min-height: 0 !important;
          margin: 0 !important;
          box-shadow: none !important;
          transform: none !important;
          zoom: 1 !important;
          overflow: hidden !important;
          page-break-after: always;
        }
        .page:last-child {
          page-break-after: avoid;
        }
        .document-footer {
          position: absolute !important;
          bottom: 12.7mm !important;
          page-break-inside: avoid !important;
        }
        .footer {
          page-break-inside: avoid !important;
        }
        th {
          background-color: #ebdcdb !important;
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
        }
      }
      .detail-header {
        background: white;
        border-radius: 12px;
        padding: 24px 28px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      }
      .detail-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
      }
      .case-info h1 {
        margin: 0 0 8px 0;
        font-size: 24px;
        font-weight: 700;
        color: #1E3A8A;
        letter-spacing: -0.5px;
      }
      .case-info .client-name {
        font-size: 15px;
        color: #64748B;
        font-weight: 500;
        margin-bottom: 12px;
      }
      .case-meta {
        display: flex;
        gap: 24px;
        font-size: 13px;
        color: #64748B;
      }
      .case-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
      }
      .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
      }
      .status-completed {
        background: #D1FAE5;
        color: #065F46;
      }
      .header-actions {
        display: flex;
        gap: 8px;
      }
      .header-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        border: 1px solid #E2E8F0;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #475569;
        transition: all 0.2s;
      }
      .header-btn:hover {
        background: #F8FAFC;
        border-color: #CBD5E1;
      }
      .header-btn.primary {
        background: #1E3A8A;
        color: white;
        border-color: #1E3A8A;
      }
      .header-btn.primary:hover {
        background: #1E40AF;
      }
      .template-tabs {
        display: flex;
        gap: 4px;
        padding: 4px;
        background: #F1F5F9;
        border-radius: 10px;
        margin-bottom: 24px;
      }
      .template-tab {
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #64748B;
        transition: all 0.2s;
        border: none;
        background: transparent;
      }
      .template-tab:hover {
        color: #475569;
      }
      .template-tab.active {
        background: white;
        color: #1E3A8A;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
      }
      .detail-content {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        align-items: start;
      }
      .right-panel {
        background: #F8FAFC;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
      }
      .preview-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
      }
      .preview-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #1E293B;
      }
      .preview-controls {
        display: flex;
        align-items: center;
        gap: 8px;
      }
      .preview-btn {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #E2E8F0;
        background: white;
        border-radius: 8px;
        cursor: pointer;
        color: #64748B;
        transition: all 0.2s;
      }
      .preview-btn:hover {
        background: #F8FAFC;
        color: #475569;
      }
      .zoom-level {
        font-size: 13px;
        font-weight: 600;
        color: #64748B;
        min-width: 60px;
        text-align: center;
      }
      .page-indicator {
        font-size: 13px;
        color: #64748B;
        font-weight: 500;
      }
      .document-viewer {
        background: #E2E8F0;
        border-radius: 8px;
        padding: 32px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
        min-height: 600px;
      }
      .document-viewer .page-wrap {
        margin-bottom: 24px;
        overflow: hidden;
        padding: 0 16px;
      }
      .document-viewer .page-wrap:last-child {
        margin-bottom: 0;
      }
      .info-banner {
        margin-top: 24px;
        padding: 16px 20px;
        background: #FEF3C7;
        border: 1px solid #FCD34D;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
      }
      .info-banner i {
        color: #D97706;
        flex-shrink: 0;
      }
      .info-banner p {
        margin: 0;
        font-size: 13px;
        color: #92400E;
        font-weight: 500;
      }
      @media (max-width: 1200px) {
        .detail-content {
          grid-template-columns: 1fr;
        }
        .left-panel {
          position: static;
        }
      }
      #caseDetailContent,.detail-content,.right-panel,#documentPreviewContainer,.document-viewer{overflow-x:hidden;max-width:100%}
      @media(max-width:900px){
        .detail-header-top{flex-direction:column;gap:16px}
        .header-actions{flex-wrap:wrap}
        .case-meta{flex-wrap:wrap;gap:12px}
        .template-tabs{overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:thin}
        .template-tabs::-webkit-scrollbar{height:4px}
        .right-panel{padding:16px}
        .document-viewer{padding:24px}
      }
      @media(max-width:768px){
        .detail-header{padding:16px}
        .case-info h1{font-size:18px}
        .header-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px}
        .info-banner{padding:12px 14px}
        .info-banner p{font-size:12px}
        .preview-header h3{font-size:14px}
        .right-panel{padding:12px}
        .document-viewer{padding:20px}
        .template-tab{padding:8px 14px;font-size:13px}
      }
      @media(max-width:480px){
        .detail-header{padding:12px}
        .case-info h1{font-size:16px;word-break:break-word}
        .header-actions{display:flex;gap:8px}
        .header-actions .header-btn{flex:1;justify-content:center;font-size:13px;padding:8px 12px}
        .case-meta{font-size:12px;gap:8px}
        .status-badge{font-size:11px;padding:5px 10px}
        .document-viewer{padding:12px;min-height:auto}
        .document-viewer .page-wrap{padding:0 8px}
      }
    </style>
    
    <!-- Header -->
    <div class="detail-header">
      <div class="detail-header-top">
        <div class="case-info">
          <h1>${escapeHtml(c.controlNo)}</h1>
          <div class="client-name">${escapeHtml(client.fullName || client.full_name || client.name || 'Unnamed Client')}</div>
          <div class="case-meta">
            <div class="case-meta-item">
              <i data-lucide="calendar" style="width:16px;height:16px;"></i>
              <span>${c.createdAt ? fmtDate(c.createdAt) : 'N/A'}</span>
            </div>
            <div class="case-meta-item">
              <i data-lucide="user" style="width:16px;height:16px;"></i>
              <span>${c.officer?.name || 'Not Assigned'}</span>
            </div>
          </div>
        </div>
        <div class="header-actions" style="display:flex;gap:8px;align-items:center;">
          <button class="header-btn" style="background:#059669;color:white;border-color:#059669;width:120px;flex-shrink:0;" onclick="printDocument()">
            <i data-lucide="printer" style="width:16px;height:16px;"></i>
            Print
          </button>
          ${(c.status === 'Approved' || c.status === 'Released' || c.status === 'Printed') ? `
            <button class="header-btn primary" style="width:120px;flex-shrink:0;" onclick="reprintCase('${c.id}')">
              <i data-lucide="printer" style="width:16px;height:16px;"></i>
              Reprint
            </button>
          ` : ''}
          ${CAN_ENCODE ? `
            <button class="header-btn" style="background:#1A237E;color:white;border-color:#1A237E;width:120px;flex-shrink:0;" onclick="editCaseFromDetail('${c.id}')">
              <i data-lucide="edit" style="width:16px;height:16px;"></i>
              Edit
            </button>
          ` : ''}

        </div>
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <span class="status-badge status-completed">
          <i data-lucide="check-circle" style="width:14px;height:14px;margin-right:4px;"></i>
          ${c.status || 'Draft'}
        </span>
      </div>
    </div>

    <!-- Template Tabs -->
    <div class="template-tabs">
      ${c.agencies && c.agencies.length > 0 
        ? AGENCIES.filter(a => c.agencies.includes(a.key)).map(a => 
            `<button class="template-tab ${a.key === selectedAgency ? 'active' : ''}" onclick="selectTemplateTab('${a.key}', this)">${a.key === 'OP' ? 'Office of the President' : a.key}</button>`
          ).join('')
        : `
          <button class="template-tab active" onclick="selectTemplateTab('PCSO', this)">PCSO</button>
          <button class="template-tab" onclick="selectTemplateTab('DSWD', this)">DSWD</button>
          <button class="template-tab" onclick="selectTemplateTab('OP', this)">Office of the President</button>
          <button class="template-tab" onclick="selectTemplateTab('DOH', this)">DOH</button>
        `
      }
    </div>

    <!-- Main Content -->
    <div class="detail-content">
      <!-- Right Panel -->
      <div class="right-panel">
        <div class="preview-header">
          <h3>Report Preview</h3>
        </div>

        <div id="documentPreviewContainer" class="document-viewer"></div>

        <div class="info-banner">
          <i data-lucide="info" style="width:20px;height:20px;"></i>
          <p>This is a system-generated report. Any updates to the case information will automatically reflect in the generated document.</p>
        </div>
      </div>
    </div>
  `;
  
  console.log('HTML generated, setting innerHTML');
  container.innerHTML = html;
  console.log('innerHTML set successfully, calling loadDocumentPreview');
  
  // Call loadDocumentPreview directly instead of using script tag
  setTimeout(() => loadDocumentPreview(c.id), 100);
  } catch (error) {
    console.error('Error generating HTML:', error);
    container.innerHTML = `<div style="padding:40px;text-align:center;color:#DC2626;">
      <p>Error generating page: ${error.message}</p>
    </div>`;
  }

  lucide.createIcons();
}

async function loadDocumentPreview(caseId){
  console.log('loadDocumentPreview called with caseId:', caseId);
  const container = document.getElementById('documentPreviewContainer');
  if(!container){
    console.error('documentPreviewContainer not found');
    return;
  }

  container.innerHTML = `<div style="padding:40px;text-align:center;color:#6B7280;">
    <i data-lucide="loader-2" style="width:32px;height:32px;margin-bottom:16px;animation:spin 1s linear infinite;"></i>
    <p>Loading document preview...</p>
  </div>`;
  lucide.createIcons();

  // Fetch authoritative document date + age from the backend
  const docData = await fetchDocumentData(caseId);

  // Prepare document reference number
  const docRefNumber = String(docData?.document_ref_number || 1).padStart(2, '0');
  console.log('loadDocumentPreview - docData:', docData);
  console.log('loadDocumentPreview - docRefNumber:', docRefNumber);

  const c = getCase(caseId);
  console.log('Case data:', c);
  if(!c){
    container.innerHTML = `<div style="padding:40px;text-align:center;color:#DC2626;">
      <i data-lucide="alert-triangle" style="width:48px;height:48px;margin-bottom:16px;"></i>
      <p style="font-size:16px;font-weight:600;">Case not found</p>
      <p style="font-size:14px;margin-top:8px;">Case ID: ${caseId}</p>
      <button onclick="window.location.href='/admin/social-case/cases'" style="margin-top:20px;padding:10px 20px;background:#1E3A8A;color:white;border:none;border-radius:6px;cursor:pointer;">Back to Cases</button>
    </div>`;
    lucide.createIcons();
    return;
  }

  const famRows = (c.familyMembers || c.household || []).filter(m=>m.fullName || m.name || m.full_name);

  const notProvided = "Not Provided";
  const _clientSexForDefault2 = (c.client?.sex || c.client?.gender || "").toLowerCase();
  const _pronoun2 = _clientSexForDefault2 === 'male' ? 'his' : 'her';
  const _purposeForDefault2 = (c.purpose || "").toLowerCase();
  const _defaultSubject2 = _purposeForDefault2.includes('medical') ? 'patient' : _purposeForDefault2.includes('burial') ? 'deceased' : 'client';
  const _defaultPossessive2 = _defaultSubject2 + "'s";
  const _assistanceType2 = _purposeForDefault2.includes('medical') ? 'medical' : _purposeForDefault2.includes('burial') ? 'burial' : _purposeForDefault2.includes('educational') ? 'educational' : 'financial';
  const _expenseType2 = _assistanceType2 === 'medical' ? 'medical expenses' : _assistanceType2 === 'burial' ? 'burial expenses' : _assistanceType2 === 'educational' ? 'educational expenses' : 'urgent expenses';
  const homeConditionDefault = `The client resides in a modest home with ${_pronoun2} family. The home of the family in modest circumstances is simple but functional. While the house may not have the latest appliances or decor, it is clean and maintained to the best of the family's ability. The family may prioritize practicality over style, and although they may face financial challenges, their home remains a place of warmth, care, and togetherness.`;
  const socioEconomicDefault = `The family is indigent, and the client depends on their family's income to cover daily expenses and household needs. Unfortunately, there is insufficient funds to sustain the ${_expenseType2} of the ${_defaultSubject2}.`;
  const evaluationDefault = `This case concerns a client in need of ${_assistanceType2} assistance for ${_expenseType2}. Due to the ${_defaultPossessive2} socio-economic condition, the client is unable to support the ${_expenseType2}, prompting ${_pronoun2} to seek help from your good office, as reflected in the attached documents. The incurred expenses have placed a heavy burden on the family, depleting their financial resources. Consequently, they are earnestly requesting assistance from your office to alleviate their situation.`;
  const recommendationDefault = `Due to the lack of sufficient income and the absence of alternative financial resources to meet the ${_defaultPossessive2} needs, the undersigned worker respectfully recommends that the ${_defaultSubject2} be considered for assistance from your office to cover the ${_expenseType2} required.`;

  const clientName = escapeHtml((c.client?.fullName || c.client?.full_name || c.client?.name || c.clientName || c.client_name || "")).toUpperCase() || notProvided;
  const clientAge = docData && docData.client_age !== null
    ? escapeHtml(String(docData.client_age))
    : (escapeHtml(String(c.client?.age || "")) || notProvided);
  const clientSex = escapeHtml((c.client?.sex || c.client?.gender || "")).toUpperCase() || notProvided;
  const clientAddress = escapeHtml((c.client?.address || "")).toUpperCase() || notProvided;
  const clientBirthdate = c.client?.birthdate ? fmtDate(c.client.birthdate).toUpperCase() : notProvided;
  const clientBirthplace = escapeHtml((c.client?.birthplace || "")).toUpperCase() || notProvided;
  const clientReligion = escapeHtml((c.client?.religion || "")).toUpperCase() || notProvided;
  const clientEducation = escapeHtml((c.client?.education || "")).toUpperCase() || notProvided;
  const clientCivilStatus = escapeHtml((c.client?.civilStatus || c.client?.civil_status || "")).toUpperCase() || notProvided;
  const clientOccupation = escapeHtml((c.client?.occupation || "")) || notProvided;
  const clientIncome = escapeHtml((c.client?.income || "")) || notProvided;
  const clientContact = escapeHtml((c.client?.contact || c.client?.contactNumber || c.client?.contact_number || "")) || notProvided;

  const reportDate = docData && docData.document_date
    ? fmtDate(docData.document_date).toUpperCase()
    : fmtDate(new Date().toISOString().slice(0,10)).toUpperCase();

  const rawProblem = c.interview?.interviewSituation || c.interview?.interview_situation || c.interview?.problemPresented || "";
  const purpose = c.purpose || "";
  const clientFirstName = (c.client?.firstName || c.client?.first_name || "").trim();
  const clientLastName = (c.client?.lastName || c.client?.last_name || "").trim();
  const clientFullName = (c.client?.fullName || c.client?.full_name || clientFirstName + " " + clientLastName).trim();
  const ip = boldProblemText(escapeHtml(rewriteProblemPresented(rawProblem, purpose, clientFullName, c.client, c.household || c.familyMembers || []))) || notProvided;
  const ih = escapeHtml(c.interview?.interviewHousehold || c.interview?.interview_household || c.interview?.homeCondition || "") || homeConditionDefault;
  const ie = escapeHtml(c.interview?.interviewNotes || c.interview?.interview_notes || c.interview?.socioEconomic || "") || socioEconomicDefault;
  const iw = escapeHtml(c.interview?.socialWorkerAssessment || c.interview?.social_worker_assessment || c.interview?.evaluation || "") || evaluationDefault;
  const ir = escapeHtml(c.interview?.recommendation || "") || recommendationDefault;

  const preparedName = escapeHtml(c.signers?.preparedByName || c.officer?.name || "") || notProvided;
  const preparedTitle = escapeHtml(c.signers?.preparedByTitle || c.officer?.position || "");
  const notedName = escapeHtml(c.signers?.notedByName || c.encoder?.name || "") || notProvided;
  const notedTitle = escapeHtml(c.signers?.notedByTitle || c.encoder?.position || "");
  const notedLicense = escapeHtml(c.signers?.notedByLicense || "");

  try {
    console.log('Fetching template...');
    const response = await fetch('/templates/social-case-report.html');
    if (!response.ok) throw new Error('Failed to load template');
    let template = await response.text();
    console.log('Template loaded, length:', template.length);

    const familyTable = famRows.length ? `
      <table style="border-radius: 0; ${famRows.length > 1 ? 'font-size: 11px;' : ''}">
        <thead>
          <tr>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">RELATIVES</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">RELATIONSHIP</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">AGE</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">EDUCATIONAL ATTAINMENT</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">OCCUPATION</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">INCOME</th>
          </tr>
        </thead>
        <tbody>
          ${famRows.map(m=>`<tr>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.fullName || m.full_name || m.name || "").toUpperCase())}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.relationship || "—").toUpperCase())}</td>
            <td align="center" style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml(String(m.age || "")) || "—"}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.education || "—").toUpperCase())}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.occupation || "N/A").toUpperCase())}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml(String(m.monthlyIncome || m.income || "")) || "N/A"}</td>
          </tr>`).join("")}
        </tbody>
      </table>` : `<div style="color:#999;margin-top:8px;font-style:italic;">None listed.</div>`;

    let pageTemplate = template;
    
    pageTemplate = pageTemplate.replace(/{{REPORT_DATE}}/g, reportDate);
    pageTemplate = pageTemplate.replace(/{{CONTROL_NUMBER}}/g, escapeHtml(c.controlNo || c.caseNumber || ""));
    pageTemplate = pageTemplate.replace(/{{PURPOSE}}/g, escapeHtml(c.purpose || "Financial / Medical"));
    pageTemplate = pageTemplate.replace(/{{CLIENT_NAME}}/g, clientName);
    pageTemplate = pageTemplate.replace(/{{CLIENT_AGE}}/g, clientAge);
    pageTemplate = pageTemplate.replace(/{{CLIENT_SEX}}/g, clientSex);
    pageTemplate = pageTemplate.replace(/{{CLIENT_ADDRESS}}/g, clientAddress);
    pageTemplate = pageTemplate.replace(/{{CLIENT_BIRTHDATE}}/g, clientBirthdate);
    pageTemplate = pageTemplate.replace(/{{CLIENT_BIRTHPLACE}}/g, clientBirthplace);
    pageTemplate = pageTemplate.replace(/{{CLIENT_RELIGION}}/g, clientReligion);
    pageTemplate = pageTemplate.replace(/{{CLIENT_EDUCATION}}/g, clientEducation);
    pageTemplate = pageTemplate.replace(/{{CLIENT_CIVIL_STATUS}}/g, clientCivilStatus);
    pageTemplate = pageTemplate.replace(/{{CLIENT_OCCUPATION}}/g, clientOccupation);
    pageTemplate = pageTemplate.replace(/{{CLIENT_INCOME}}/g, clientIncome);
    pageTemplate = pageTemplate.replace(/{{CLIENT_CONTACT}}/g, clientContact);
    pageTemplate = pageTemplate.replace(/{{FAMILY_TABLE}}/g, familyTable);
    pageTemplate = pageTemplate.replace(/{{PROBLEM_PRESENTED}}/g, ip);
    pageTemplate = pageTemplate.replace(/{{HOME_CONDITION}}/g, ih);
    pageTemplate = pageTemplate.replace(/{{SOCIO_ECONOMIC}}/g, ie);
    pageTemplate = pageTemplate.replace(/{{EVALUATION}}/g, iw);
    pageTemplate = pageTemplate.replace(/{{RECOMMENDATION}}/g, ir);
    pageTemplate = pageTemplate.replace(/{{PREPARED_NAME}}/g, preparedName);
    pageTemplate = pageTemplate.replace(/{{PREPARED_TITLE}}/g, preparedTitle);
    pageTemplate = pageTemplate.replace(/{{NOTED_NAME}}/g, notedName);
    pageTemplate = pageTemplate.replace(/{{NOTED_TITLE}}/g, notedTitle);
    pageTemplate = pageTemplate.replace(/{{NOTED_LICENSE}}/g, notedLicense ? 'License No. ' + notedLicense : '');
    const agencyInfo = AGENCIES.find(a => a.key === selectedAgency) || AGENCIES[0];
    const agencyName = agencyInfo.name;
    pageTemplate = pageTemplate.replace(/{{AGENCY_NAME}}/g, escapeHtml(agencyName));
    pageTemplate = pageTemplate.replace(/{{DOCUMENT_REF_NUMBER}}/g, docRefNumber);
    console.log('loadDocumentPreview - replaced DOCUMENT_REF_NUMBER with:', docRefNumber);
    const parts = pageTemplate.split('<!--PAGE_BREAK-->');
    const totalPages = parts.length;
    
    // Add document-specific CSS
    const docStyles = `
      <style>
        .page {
          width: 215.9mm;
          height: 279.4mm;
          margin: 20px auto;
          background: white;
          position: relative;
          padding: 12.7mm 25.4mm 32.4mm 25.4mm;
          box-shadow: 0 0 12px rgba(0,0,0,.25);
          overflow: hidden;
        }
        .page .watermark {
          position: absolute;
          width: 135mm;
          left: 50%;
          top: 50%;
          transform: translate(-50%, -50%);
          opacity: .06;
          z-index: 1;
          pointer-events: none;
        }
        .page .content {
          position: relative;
          z-index: 2;
        }
        .page .header {
          display: grid;
          grid-template-columns: 85px 1fr 85px;
          align-items: start;
        }
        .page .header img {
          width: 75px;
          height: 75px;
          object-fit: contain;
        }
        .page .gov {
          text-align: center;
          line-height: 1.2;
          padding-top: 12px;
        }
        .page .gov div {
          font-size: 14px;
        }
        .page .gov h2 {
          margin: 6px 0 0;
          font-size: 13px;
          font-weight: bold;
          letter-spacing: .5px;
          white-space: nowrap;
        }
        .page .line {
          border-top: 2px solid black;
          margin: 8px 0 2px;
        }
        .page .line2 {
          border-top: 1px solid black;
          margin-bottom: 12px;
        }
        .page .top-info {
          display: flex;
          justify-content: space-between;
          font-family: Arial;
          font-size: 11px;
        }
        .page .right {
          text-align: right;
          font-family: Arial;
          font-size: 11px;
          font-weight: bold;
        }
        .page .title {
          text-align: center;
          margin: 18px 0;
        }
        .page .title h3 {
          margin: 0;
          font-family: Arial;
          font-size: 14px;
          font-weight: bold;
          text-transform: uppercase;
        }
        .page .title small {
          display: block;
          margin-top: 5px;
          font-family: Cambria;
          font-size: 11px;
        }
        .page .section {
          margin-top: 18px;
          font-size: 14px;
        }
        .page .section-title {
          font-weight: bold;
          margin-bottom: 10px;
        }
        .page .row {
          display: grid;
          grid-template-columns: 180px 15px 1fr;
          margin-bottom: 5px;
        }
        .page .row span:first-child {
          font-weight: bold;
        }
        .page table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 8px;
          font-size: 13px;
          border-radius: 0;
          overflow: visible;
        }
        .page th {
          border: 1px solid black;
          padding: 6px;
          text-align: center;
          background-color: #ebdcdb;
          font-weight: bold;
          color: black;
          text-transform: none;
          letter-spacing: normal;
        }
        .page td {
          border: 1px solid black;
          padding: 6px;
          color: inherit;
        }
        .page .paragraph {
          margin-top: 5px;
          text-align: justify;
          line-height: 1.6;
          text-indent: 45px;
        }
        .page .footer {
          margin-top: 50px;
          display: flex;
          justify-content: space-between;
        }
        .page .signature {
          width: 45%;
          text-align: center;
        }
        .page .signature b {
          display: block;
          margin-top: 50px;
          font-size: 15px;
        }
        .page .signature small {
          font-size: 12px;
        }
        .page .document-footer {
          position: absolute;
          bottom: 12.7mm;
          left: 25.4mm;
          right: 25.4mm;
          border-top: 1px solid #7f7f7f;
          padding-top: 5px;
          font-size: 12px;
          color: #555555;
        }
        .page .doc-address {
          text-align: center;
          font-style: italic;
          line-height: 1.4;
          margin-bottom: 8px;
        }
        .page .doc-meta {
          display: flex;
          justify-content: space-between;
          font-style: italic;
        }
        @media (max-width: 767px) {
          .document-viewer .page { zoom: 0.38; }
        }
        @media (min-width: 768px) and (max-width: 991px) {
          .document-viewer .page { zoom: 0.60; }
        }
        @media (min-width: 992px) and (max-width: 1199px) {
          .document-viewer .page { zoom: 0.80; }
        }
        @media (min-width: 1200px) {
          .document-viewer .page { zoom: 1; }
        }
      </style>
    `;
    
    let pagesHtml = parts.map((part, i) => {
      const pn = i + 1;
      return `<div class="page-wrap"><div class="page">${part.replace(/{{PAGE_NUMBER}}/g, String(pn)).replace(/{{TOTAL_PAGES}}/g, String(totalPages))}</div></div>`;
    }).join('');
    container.innerHTML = docStyles + pagesHtml;

    // Responsive scaling is handled entirely by the CSS `zoom` media queries
    // above, so clear any legacy inline transform/wrap-height from previous versions.
    container.querySelectorAll('.page').forEach(p => {
      p.style.transform = '';
      p.style.transformOrigin = '';
      p.style.margin = '';
      p.style.marginBottom = '';
      if (p.parentElement) p.parentElement.style.height = '';
    });

    console.log('Document preview rendered successfully');
  } catch (error) {
    console.error('Error loading template:', error);
    container.innerHTML = `<div style="padding:40px;text-align:center;color:#DC2626;">
      <i data-lucide="alert-triangle" style="width:48px;height:48px;margin-bottom:16px;"></i>
      <p style="font-size:16px;font-weight:600;">Error loading document template</p>
      <p style="font-size:14px;margin-top:8px;">${error.message}</p>
      <button onclick="loadDocumentPreview('${caseId}')" style="margin-top:20px;padding:10px 20px;background:#1E3A8A;color:white;border:none;border-radius:6px;cursor:pointer;">Retry</button>
    </div>`;
    lucide.createIcons();
  }
}

function renderEligibilityCard(c){
  const e = eligibilityInfo(c);
  return `<div class="panel" style="padding:12px">
    <h3 style="font-size:14px;margin-bottom:8px">Re-eligibility</h3>
    <div style="display:flex;justify-content:space-between;align-items:center;padding:10px;background:var(--background);border-radius:6px">
      <div>
        <div style="font-size:11px;color:var(--text-secondary);margin-bottom:2px">Released</div>
        <div style="font-weight:600;font-size:13px">${fmtDate(c.releasedDate)}</div>
      </div>
      <i data-lucide="arrow-right" style="width:14px;height:14px;color:var(--text-muted)"></i>
      <div>
        <div style="font-size:11px;color:var(--text-secondary);margin-bottom:2px">Next eligible</div>
        <div style="font-weight:600;font-size:13px">${fmtDate(e.nextEligibleDate)}</div>
      </div>
    </div>
    ${!e.eligible ? `<div style="margin-top:8px;font-size:12px;color:var(--danger)">
      <i data-lucide="alert-circle" style="width:14px;height:14px;vertical-align:middle;margin-right:4px"></i>
      Not eligible (${e.daysLeft} days)
    </div>` : ''}
  </div>`;
}

/* ---------------- Rendering: Document view ---------------- */
async function loadDocument(caseId, agency){
  console.log('loadDocument called with caseId:', caseId, 'agency:', agency);
  await loadCases();
  console.log('loadCases completed, cases loaded:', cases.length);
  view = {tab:"document", caseId:caseId, docAgency:agency};
  console.log('View set:', view);
  renderDocument();
  lucide.createIcons();
}

async function renderDocument(){
  console.log('renderDocument called');
  const container = document.getElementById('documentContent');
  if(!container) {
    console.log('Container not found');
    return;
  }

  const c = getCase(view.caseId);
  console.log('Case object:', c);
  console.log('Client object:', c.client);
  console.log('Client fields:', c.client ? Object.keys(c.client) : 'No client');
  console.log('Client name:', c.client?.name);
  console.log('Client full_name:', c.client?.full_name);
  console.log('Client age:', c.client?.age);
  console.log('Client sex:', c.client?.sex);
  if(!c){ container.innerHTML = `<div class="empty">Case not found. Case ID: ${view.caseId}</div>`; return; }

  // Fetch authoritative document date + age from the backend
  const docData = await fetchDocumentData(view.caseId);

  // Prepare document reference number
  const docRefNumber = String(docData?.document_ref_number || 1).padStart(2, '0');
  console.log('docData:', docData);
  console.log('docRefNumber:', docRefNumber);

  // Debug: Add debug info to container
  const debugDiv = document.createElement('div');
  debugDiv.style.cssText = 'position:fixed;top:10px;right:10px;background:yellow;padding:10px;z-index:9999;font-size:12px;border:2px solid red;';
  debugDiv.innerHTML = `
    <strong>DEBUG:</strong><br>
    docData: ${JSON.stringify(docData)}<br>
    docRefNumber: ${docRefNumber}<br>
    Template before: {{DOCUMENT_REF_NUMBER}}<br>
    Template after: ${docRefNumber}
  `;
  document.body.appendChild(debugDiv);

  const agenciesToPrint = view.docAgency === 'all'
    ? (c.agencies && c.agencies.length ? c.agencies : ['PCSO'])
    : [view.docAgency];

  const famRows = (c.familyMembers || c.household || []).filter(m=>m.fullName || m.name);

  const notProvided = "Not Provided";
  const _clientSexForDefault3 = (c.client?.sex || c.client?.gender || "").toLowerCase();
  const _pronoun3 = _clientSexForDefault3 === 'male' ? 'his' : 'her';
  const _purposeForDefault3 = (c.purpose || "").toLowerCase();
  const _defaultSubject3 = _purposeForDefault3.includes('medical') ? 'patient' : _purposeForDefault3.includes('burial') ? 'deceased' : 'client';
  const _defaultPossessive3 = _defaultSubject3 + "'s";
  const _assistanceType3 = _purposeForDefault3.includes('medical') ? 'medical' : _purposeForDefault3.includes('burial') ? 'burial' : _purposeForDefault3.includes('educational') ? 'educational' : 'financial';
  const _expenseType3 = _assistanceType3 === 'medical' ? 'medical expenses' : _assistanceType3 === 'burial' ? 'burial expenses' : _assistanceType3 === 'educational' ? 'educational expenses' : 'urgent expenses';
  const homeConditionDefault = `The client resides in a modest home with ${_pronoun3} family. The home of the family in modest circumstances is simple but functional. While the house may not have the latest appliances or decor, it is clean and maintained to the best of the family's ability. The family may prioritize practicality over style, and although they may face financial challenges, their home remains a place of warmth, care, and togetherness.`;
  const socioEconomicDefault = `The family is indigent, and the client depends on their family's income to cover daily expenses and household needs. Unfortunately, there is insufficient funds to sustain the ${_expenseType3} of the ${_defaultSubject3}.`;
  const evaluationDefault = `This case concerns a client in need of ${_assistanceType3} assistance for ${_expenseType3}. Due to the ${_defaultPossessive3} socio-economic condition, the client is unable to support the ${_expenseType3}, prompting ${_pronoun3} to seek help from your good office, as reflected in the attached documents. The incurred expenses have placed a heavy burden on the family, depleting their financial resources. Consequently, they are earnestly requesting assistance from your office to alleviate their situation.`;
  const recommendationDefault = `Due to the lack of sufficient income and the absence of alternative financial resources to meet the ${_defaultPossessive3} needs, the undersigned worker respectfully recommends that the ${_defaultSubject3} be considered for assistance from your office to cover the ${_expenseType3} required.`;

  let selectOptions = (c.agencies || []).map(a => {
    const agObj = AGENCIES.find(x => x.key === a);
    return `<option value="${a}" ${a === view.docAgency ? 'selected' : ''}>${agObj ? agObj.name : a}</option>`;
  }).join("");
  if (c.agencies && c.agencies.length > 1) {
    selectOptions += `<option value="all" ${view.docAgency === 'all' ? 'selected' : ''}>All Selected Agencies (${c.agencies.length} copies)</option>`;
  }

  const clientName = escapeHtml((c.client?.fullName || c.client?.full_name || c.client?.name || c.clientName || c.client_name || "")).toUpperCase() || notProvided;
  const clientAge = docData && docData.client_age !== null
    ? escapeHtml(String(docData.client_age))
    : (escapeHtml(String(c.client?.age || "")) || notProvided);
  const clientSex = escapeHtml((c.client?.sex || c.client?.gender || "")).toUpperCase() || notProvided;
  const clientAddress = escapeHtml((c.client?.address || "")).toUpperCase() || notProvided;
  const clientBirthdate = c.client?.birthdate ? fmtDate(c.client.birthdate).toUpperCase() : notProvided;
  const clientBirthplace = escapeHtml((c.client?.birthplace || "")).toUpperCase() || notProvided;
  const clientReligion = escapeHtml((c.client?.religion || "")).toUpperCase() || notProvided;
  const clientEducation = escapeHtml((c.client?.education || "")).toUpperCase() || notProvided;
  const clientCivilStatus = escapeHtml((c.client?.civilStatus || c.client?.civil_status || "")).toUpperCase() || notProvided;
  const clientOccupation = escapeHtml((c.client?.occupation || "")) || notProvided;
  const clientIncome = escapeHtml((c.client?.income || "")) || notProvided;
  const clientContact = escapeHtml((c.client?.contact || c.client?.contactNumber || c.client?.contact_number || "")) || notProvided;

  const reportDate = docData && docData.document_date
    ? fmtDate(docData.document_date).toUpperCase()
    : fmtDate(new Date().toISOString().slice(0,10)).toUpperCase();

  const rawProblem = c.interview?.interviewSituation || c.interview?.interview_situation || c.interview?.problemPresented || "";
  const purpose = c.purpose || "";
  const clientFirstName = (c.client?.firstName || c.client?.first_name || "").trim();
  const clientLastName = (c.client?.lastName || c.client?.last_name || "").trim();
  const clientFullName = (c.client?.fullName || c.client?.full_name || clientFirstName + " " + clientLastName).trim();
  const ip = boldProblemText(escapeHtml(rewriteProblemPresented(rawProblem, purpose, clientFullName, c.client, c.household || c.familyMembers || []))) || notProvided;
  const ih = escapeHtml(c.interview?.interviewHousehold || c.interview?.interview_household || c.interview?.homeCondition || "") || homeConditionDefault;
  const ie = escapeHtml(c.interview?.interviewNotes || c.interview?.interview_notes || c.interview?.socioEconomic || "") || socioEconomicDefault;
  const iw = escapeHtml(c.interview?.socialWorkerAssessment || c.interview?.social_worker_assessment || c.interview?.evaluation || "") || evaluationDefault;
  const ir = escapeHtml(c.interview?.recommendation || "") || recommendationDefault;

  const preparedName = escapeHtml(c.signers?.preparedByName || c.officer?.name || "") || notProvided;
  const preparedTitle = escapeHtml(c.signers?.preparedByTitle || c.officer?.position || "");
  const notedName = escapeHtml(c.signers?.notedByName || c.encoder?.name || "") || notProvided;
  const notedTitle = escapeHtml(c.signers?.notedByTitle || c.encoder?.position || "");
  const notedLicense = escapeHtml(c.signers?.notedByLicense || "");

  const toolbarHtml = `
  <style>
    .page {
      width: 215.9mm;
      min-height: 279.4mm;
      margin: 20px auto;
      background: white;
      position: relative;
      padding: 12.7mm 25.4mm 32.4mm 25.4mm;
      box-shadow: 0 0 12px rgba(0,0,0,.25);
    }
    .watermark {
      position: absolute;
      width: 135mm;
      left: 50%;
      top: 50%;
      transform: translate(-50%, -50%);
      opacity: .06;
      z-index: 1;
      pointer-events: none;
    }
    .content { position: relative; z-index: 2; }
    .header {
      display: grid;
      grid-template-columns: 85px 1fr 85px;
      align-items: start;
    }
    .header img {
      width: 75px;
      height: 75px;
      object-fit: contain;
    }
    .gov {
      text-align: center;
      line-height: 1.2;
      padding-top: 12px;
    }
    .gov div { font-size: 14px; }
    .gov h2 {
      margin: 6px 0 0;
      font-size: 21px;
      font-weight: bold;
      letter-spacing: .5px;
    }
    .line {
      border-top: 2px solid black;
      margin: 8px 0 2px;
    }
    .line2 {
      border-top: 1px solid black;
      margin-bottom: 12px;
    }
    .top-info {
      display: flex;
      justify-content: space-between;
      font-family: Arial;
      font-size: 11px;
    }
    .right { text-align: right; }
    .title {
      text-align: center;
      margin: 18px 0;
    }
    .title h3 {
      margin: 0;
      font-family: Arial;
      font-size: 14px;
      font-weight: bold;
      text-transform: uppercase;
    }
    .title small {
      display: block;
      margin-top: 5px;
      font-family: Cambria;
      font-size: 11px;
    }
    .section {
      margin-top: 18px;
      font-size: 14px;
    }
    .section-title {
      font-weight: bold;
      margin-bottom: 10px;
    }
    .row {
      display: grid;
      grid-template-columns: 180px 15px 1fr;
      margin-bottom: 5px;
    }
    .row span:first-child { font-weight: bold; }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
      font-size: 13px;
    }
    th {
      border: 1px solid black;
      padding: 6px;
      text-align: center;
      background-color: #ebdcdb;
    }
    td {
      border: 1px solid black;
      padding: 6px;
    }
    .paragraph {
      margin-top: 5px;
      text-align: justify;
      line-height: 1.6;
      text-indent: 45px;
    }
    .footer {
      margin-top: 50px;
      display: flex;
      justify-content: space-between;
    }
    .signature {
      width: 45%;
      text-align: center;
    }
    .signature b {
      display: block;
      margin-top: 50px;
      font-size: 15px;
    }
    .signature small { font-size: 12px; }
    .document-footer {
      position: absolute;
      bottom: 12.7mm;
      left: 25.4mm;
      right: 25.4mm;
      border-top: 1px solid #7f7f7f;
      padding-top: 5px;
      font-size: 12px;
      color: #555555;
    }
    .doc-address {
      text-align: center;
      font-style: italic;
      line-height: 1.4;
      margin-bottom: 8px;
    }
    .doc-meta {
      display: flex;
      justify-content: space-between;
      font-style: italic;
    }
    @media (max-width: 767px) {
      #documentContent .page { zoom: 0.38; }
    }
    @media (min-width: 768px) and (max-width: 991px) {
      #documentContent .page { zoom: 0.60; }
    }
    @media (min-width: 992px) and (max-width: 1199px) {
      #documentContent .page { zoom: 0.80; }
    }
    @media (min-width: 1200px) {
      #documentContent .page { zoom: 1; }
    }
    @media print {
      @page { margin: 0; size: 215.9mm 279.4mm; }
      html, body, .app, .main { overflow: visible !important; height: auto !important; }
      .no-print { display: none !important; }
      .sidebar, .mobile-header, .page-head, .toolbar-row, header { display: none !important; }
      .main { padding: 0; max-width: none; margin: 0; }
      body { background: #fff; }
      .page { height: 279.4mm !important; min-height: 0 !important; margin: 0 !important; padding: 12.7mm 25.4mm 32.4mm 25.4mm !important; box-shadow: none !important; overflow: hidden !important; zoom: 1 !important; page-break-after: always; break-after: page; }
      .page:last-child { page-break-after: avoid; break-after: avoid; }
    }
  </style>
  <div class="doc-toolbar no-print" style="box-shadow:var(--shadow);max-width:215.9mm;margin:0 auto 20px;">
    <button class="btn ghost btn-sm" onclick="window.location.href='/admin/social-case/detail/${c.id}'"><i data-lucide="arrow-left" style="width:16px;height:16px"></i> Back to case</button>
    <div style="display:flex;gap:10px;align-items:center;">
      <span style="font-size:13px;font-weight:500;color:var(--text-secondary);white-space:nowrap;">Print Copy:</span>
      <select style="width:auto;padding:8px 32px 8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;background:var(--surface);cursor:pointer;height:36px;" onchange="window.location.href='/admin/social-case/document/${c.id}/'+this.value">
        ${selectOptions}
      </select>
      <button class="btn primary btn-sm" onclick="printDocument('${c.id}')"><i data-lucide="printer" style="width:15px;height:15px"></i> Print / save as PDF</button>
    </div>
  </div>`;

  try {
    const response = await fetch('/templates/social-case-report.html?v=' + Date.now());
    if (!response.ok) throw new Error('Failed to load template');
    let template = await response.text();

    const familyTable = famRows.length ? `
      <table style="border-radius: 0; ${famRows.length > 1 ? 'font-size: 11px;' : ''}">
        <thead>
          <tr>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">RELATIVES</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">RELATIONSHIP</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">AGE</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">EDUCATIONAL ATTAINMENT</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">OCCUPATION</th>
            <th style="border-radius: 0; ${famRows.length > 1 ? 'padding: 4px 6px; font-size: 10px;' : ''}">INCOME</th>
          </tr>
        </thead>
        <tbody>
          ${famRows.map(m=>`<tr>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.fullName || m.full_name || m.name || "").toUpperCase())}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.relationship || "—").toUpperCase())}</td>
            <td align="center" style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml(String(m.age || "")) || "—"}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.education || "—").toUpperCase())}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml((m.occupation || "N/A").toUpperCase())}</td>
            <td style="border-radius: 0; ${famRows.length > 1 ? 'padding: 3px 6px;' : ''}">${escapeHtml(String(m.monthlyIncome || m.income || "")) || "N/A"}</td>
          </tr>`).join("")}
        </tbody>
      </table>` : `<div style="color:#999;margin-top:8px;font-style:italic;">None listed.</div>`;

    // Prepare document reference number
    const docRefNumber = String(docData?.document_ref_number || 1).padStart(2, '0');

    const pageParts = agenciesToPrint.map((agencyKey, pageIndex) => {
      const ag = AGENCIES.find(a => a.key === agencyKey) || { name: agencyKey };
      let pageTemplate = template;
      
      pageTemplate = pageTemplate.replace(/{{REPORT_DATE}}/g, reportDate);
      pageTemplate = pageTemplate.replace(/{{CONTROL_NUMBER}}/g, escapeHtml(c.controlNo || c.caseNumber || ""));
      pageTemplate = pageTemplate.replace(/{{PURPOSE}}/g, escapeHtml(c.purpose || "Financial / Medical"));
      pageTemplate = pageTemplate.replace(/{{CLIENT_NAME}}/g, clientName);
      pageTemplate = pageTemplate.replace(/{{CLIENT_AGE}}/g, clientAge);
      pageTemplate = pageTemplate.replace(/{{CLIENT_SEX}}/g, clientSex);
      pageTemplate = pageTemplate.replace(/{{CLIENT_ADDRESS}}/g, clientAddress);
      pageTemplate = pageTemplate.replace(/{{CLIENT_BIRTHDATE}}/g, clientBirthdate);
      pageTemplate = pageTemplate.replace(/{{CLIENT_BIRTHPLACE}}/g, clientBirthplace);
      pageTemplate = pageTemplate.replace(/{{CLIENT_RELIGION}}/g, clientReligion);
      pageTemplate = pageTemplate.replace(/{{CLIENT_EDUCATION}}/g, clientEducation);
      pageTemplate = pageTemplate.replace(/{{CLIENT_CIVIL_STATUS}}/g, clientCivilStatus);
      pageTemplate = pageTemplate.replace(/{{CLIENT_OCCUPATION}}/g, clientOccupation);
      pageTemplate = pageTemplate.replace(/{{CLIENT_INCOME}}/g, clientIncome);
      pageTemplate = pageTemplate.replace(/{{CLIENT_CONTACT}}/g, clientContact);
      pageTemplate = pageTemplate.replace(/{{FAMILY_TABLE}}/g, familyTable);
      pageTemplate = pageTemplate.replace(/{{PROBLEM_PRESENTED}}/g, ip);
      pageTemplate = pageTemplate.replace(/{{HOME_CONDITION}}/g, ih);
      pageTemplate = pageTemplate.replace(/{{SOCIO_ECONOMIC}}/g, ie);
      pageTemplate = pageTemplate.replace(/{{EVALUATION}}/g, iw);
      pageTemplate = pageTemplate.replace(/{{RECOMMENDATION}}/g, ir);
      pageTemplate = pageTemplate.replace(/{{PREPARED_NAME}}/g, preparedName);
      pageTemplate = pageTemplate.replace(/{{PREPARED_TITLE}}/g, preparedTitle);
      pageTemplate = pageTemplate.replace(/{{NOTED_NAME}}/g, notedName);
      pageTemplate = pageTemplate.replace(/{{NOTED_TITLE}}/g, notedTitle);
      pageTemplate = pageTemplate.replace(/{{NOTED_LICENSE}}/g, notedLicense ? 'License No. ' + notedLicense : '');
      pageTemplate = pageTemplate.replace(/{{AGENCY_NAME}}/g, escapeHtml(ag.name || agencyKey));
      pageTemplate = pageTemplate.replace(/{{DOCUMENT_REF_NUMBER}}/g, docRefNumber);
      console.log('Template replacement done, docRefNumber:', docRefNumber);

      return pageTemplate.split('<!--PAGE_BREAK-->');
    }).flat();

    const totalPhysicalPages = pageParts.length;
    const pagesHtml = pageParts.map((part, i) => {
      const pn = i + 1;
      return `<div class="page">${part.replace(/{{PAGE_NUMBER}}/g, String(pn)).replace(/{{TOTAL_PAGES}}/g, String(totalPhysicalPages))}</div>`;
    }).join("");

    container.innerHTML = toolbarHtml + pagesHtml;
  } catch (error) {
    console.error('Error loading template:', error);
    container.innerHTML = `<div class="empty">Error loading document template. Please try again.</div>`;
  }
}

