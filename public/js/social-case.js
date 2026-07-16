/* ---------------- Constants ---------------- */
const STATUSES = ["Draft","Review","Approved","Released","Archived"];
const STATUS_CLASS = {Draft:"b-draft",Review:"b-review",Approved:"b-approved",Released:"b-released",Archived:"b-archived"};
const PURPOSES = ["Medical Assistance","Burial Assistance","Educational Assistance","Financial Assistance","Food / Relief Assistance","Livelihood Assistance","Other"];
const BARANGAYS = [
  "ACACIA",
  "ADLAS",
  "ANAHAW I",
  "ANAHAW 2",
  "BALITE I",
  "BALITE II",
  "BALUBAD",
  "BANABA",
  "BATAS",
  "BIGA 1",
  "BIGA 2",
  "BILUSO",
  "BUCAL",
  "BUHO",
  "BULIHAN",
  "CABANGAAN",
  "CARMEN",
  "HOYO",
  "HUKAY",
  "IBA",
  "INCHICAN",
  "IPIL I",
  "IPIL 2",
  "KALUBKOB",
  "KAONG",
  "LALAAN I",
  "LALAAN II",
  "LITLIT",
  "LUCSUHIN",
  "LUMIL",
  "MAGUYAM",
  "MALABAG",
  "MALAKING TATIAO",
  "MATAAS NA BUROL",
  "MUNTING ILOG",
  "NARRA I",
  "NARRA II",
  "NARRA III",
  "PALIGAWAN",
  "PASONG LANGKA",
  "POBLACION 1",
  "POBLACION 2",
  "POBLACION 3",
  "POBLACION 4",
  "POBLACION 5",
  "POOC I",
  "POOC II",
  "PULONG BUNGA",
  "PULONG SAGING",
  "PUTING KAHOY",
  "SABUTAN",
  "SAN MIGUEL I",
  "SAN MIGUEL II",
  "SAN VICENTE I",
  "SAN VICENTE II",
  "SANTOL",
  "TARTARIA",
  "TIBIG",
  "TOLEDO",
  "TUBUAN 1",
  "TUBUAN 2",
  "TUBUAN 3",
  "ULAT",
  "YAKAL"
];
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
    <div style="background:var(--background);border-radius:16px;width:100%;max-width:800px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.12);overflow:hidden;animation:slideUp 0.25s ease;">
      <!-- Header -->
      <div style="background:#1A237E;color:white;padding:16px 24px;display:flex;justify-content:space-between;align-items:center;">
        <h5 style="margin:0;font-size:1.1rem;font-weight:600;display:flex;align-items:center;gap:8px;">
          <i data-lucide="user-circle" style="width:20px;height:20px;"></i>
          Social Case Study Details
        </h5>
        <button onclick="document.getElementById('caseDetailsModal').remove()" style="background:none;border:none;color:white;cursor:pointer;opacity:0.8;transition:opacity 0.2s;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
          <i data-lucide="x" style="width:24px;height:24px;"></i>
        </button>
      </div>
      
      <!-- Body -->
      <div style="padding:24px;overflow-y:auto;flex:1;">
        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr));gap:16px;margin-bottom:16px;">
          
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
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.client.name)||"—"}</div>
          </div>
          
          <div style="margin-bottom:8px;grid-column:1/-1;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Address</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.client.address)||"—"}</div>
          </div>
          
          <div style="margin-bottom:8px;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Assistance Type</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.purpose)||"—"}</div>
          </div>
          
          <div style="margin-bottom:8px;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Age</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.client.age)||"—"}</div>
          </div>
          
          <div style="margin-bottom:8px;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Civil Status</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);">${escapeHtml(caseRec.client.civilStatus)||"—"}</div>
          </div>

          <div style="margin-bottom:8px;grid-column:1/-1;">
            <label style="font-weight:600;color:var(--text-muted);font-size:0.8rem;display:block;margin-bottom:4px;">Problem Presented</label>
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);white-space:pre-wrap;">${escapeHtml(caseRec.interview.problemPresented)||"—"}</div>
          </div>
        </div>
      </div>
      
      <!-- Footer -->
      <div style="padding:16px 24px;border-top:1px solid var(--border);background:var(--surface);display:flex;justify-content:flex-end;gap:12px;">
        <button onclick="document.getElementById('caseDetailsModal').remove()" style="padding:8px 16px;background:var(--background);border:1px solid var(--border);border-radius:6px;font-weight:500;color:var(--text-primary);cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='var(--background)'">Close</button>
        <button onclick="window.location.href='/admin/social-case/detail/${caseRec.id}'" style="padding:8px 16px;background:var(--primary);border:none;border-radius:6px;font-weight:500;color:white;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background 0.2s;" onmouseover="this.style.background='#3730A3'" onmouseout="this.style.background='var(--primary)'">
           <i data-lucide="edit" style="width:16px;height:16px;"></i> Full Details / Edit
        </button>
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
function todayISO(){ return new Date().toISOString().slice(0,10); }
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
function escapeHtml(s){ return (s||"").replace(/[&<>"']/g, c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c])); }
function findLatestByName(name){
  const n = name.trim().toLowerCase();
  if(!n) return null;
  const matches = cases.filter(c => c.client.name.trim().toLowerCase() === n && c.releasedDate);
  if(!matches.length) return null;
  matches.sort((a,b)=> new Date(b.releasedDate) - new Date(a.releasedDate));
  return matches[0];
}

function checkEligibility(clientName){
  const n = clientName.trim().toLowerCase();
  if(!n) return {eligible: true, reason: ''};
  
  // Find all cases for this client
  const clientCases = cases.filter(c => c.client.name.toLowerCase().includes(n));
  
  // Check for any approved/released cases within the last 6 months
  const sixMonthsAgo = new Date();
  sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);
  
  const recentApproved = clientCases.filter(c => {
    return (c.status === 'Approved' || c.status === 'Printed' || c.status === 'Released') &&
           new Date(c.createdAt) > sixMonthsAgo;
  });
  
  if(recentApproved.length > 0){
    const latest = recentApproved.sort((a,b) => new Date(b.createdAt) - new Date(a.createdAt))[0];
    const daysSince = Math.floor((new Date() - new Date(latest.createdAt)) / (1000 * 60 * 60 * 24));
    const daysRemaining = 180 - daysSince;
    return {
      eligible: false,
      reason: `Client received assistance on ${fmtDate(latest.createdAt)}. Must wait ${daysRemaining} more days (6-month rule).`,
      latestCase: latest,
      daysRemaining: daysRemaining
    };
  }
  
  return {eligible: true, reason: ''};
}

function eligibilityInfo(caseRec){
  console.log('eligibilityInfo called with:', caseRec.releasedDate);
  if(!caseRec || !caseRec.releasedDate) {
    console.log('No releasedDate, returning eligible');
    return {eligible:true, daysSince:0, daysLeft:0, nextEligibleDate:null, pct:0};
  }
  
  // Validate the releasedDate before processing
  try {
    const testDate = new Date(caseRec.releasedDate);
    console.log('Test date:', testDate, 'getTime:', testDate.getTime(), 'isNaN:', isNaN(testDate.getTime()));
    if(isNaN(testDate.getTime())) {
      console.warn('Invalid releasedDate:', caseRec.releasedDate);
      return {eligible:true, daysSince:0, daysLeft:0, nextEligibleDate:null, pct:0};
    }
  } catch(e) {
    console.warn('Error parsing releasedDate:', caseRec.releasedDate, e);
    return {eligible:true, daysSince:0, daysLeft:0, nextEligibleDate:null, pct:0};
  }
  
  const daysSince = daysBetween(caseRec.releasedDate, todayISO());
  const daysLeft = ELIGIBILITY_DAYS - daysSince;
  const nextDate = new Date(caseRec.releasedDate);
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
  
  // Check eligibility using the new function
  const eligibility = checkEligibility(name);
  
  if(!eligibility.eligible){
    // Client is ineligible - show error
    alert(eligibility.reason);
    return;
  }
  
  const match = findLatestByName(name);
  setView({eligMatch: match, eligOverride:false});
  renderNewCase();
  updateWorkflowStep(2);
  lucide.createIcons();
}

function proceedToIntake(){
  draftIntake = blankIntake(view.eligClientName);
  setView({newCaseStep:"intake"});
  renderNewCase();
  updateWorkflowStep(3);
  lucide.createIcons();
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
    updateWorkflowStep(4);
    const savedAgencies = data.agencies || [];
    draftIntake = null;
    if (savedAgencies.length > 1) {
      window.location.href = `/admin/social-case/document/${data.id}/all`;
    } else if (savedAgencies.length === 1) {
      window.location.href = `/admin/social-case/document/${data.id}/${savedAgencies[0]}`;
    } else {
      window.location.href = `/admin/social-case/detail/${data.id}`;
    }
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
    caseRec.status = STATUSES[idx+1];
    caseRec.updatedAt = todayISO();
    caseRec.statusHistory.push({status:caseRec.status, date: todayISO()});
    if(caseRec.status === "Released"){ caseRec.releasedDate = todayISO(); }
    saveCases();
    renderCaseDetail();
  }
}
function revertStatus(caseRec){
  const idx = STATUSES.indexOf(caseRec.status);
  if(idx > 0){
    caseRec.status = STATUSES[idx-1];
    caseRec.updatedAt = todayISO();
    caseRec.statusHistory.push({status:caseRec.status+" (reverted)", date: todayISO()});
    if(caseRec.status !== "Released"){ caseRec.releasedDate = null; }
    saveCases();
    renderCaseDetail();
  }
}
function deleteCase(id, fromList = false){
  Swal.fire({
    title: 'Archive this case?',
    text: 'This will move the case to the archive. You can still view it but it will be removed from the active cases list.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, Archive',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#DC2626',
    cancelButtonColor: '#6B7280'
  }).then((result) => {
    if (result.isConfirmed) {
      const caseRec = getCase(id);
      if(caseRec){
        caseRec.status = 'Archived';
        caseRec.updatedAt = todayISO();
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
        .then(response => response.json())
        .then(async data => {
          console.log('Case archived:', data);
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
function getCase(id){ return cases.find(c=>String(c.id) === String(id)); }

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
  renderArchive();
  lucide.createIcons();
}

function renderArchive(){
  const archivedCases = cases.filter(c => c.status === 'Archived');
  const table = document.getElementById('archiveTable');
  
  if(archivedCases.length === 0){
    table.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted)">
      <i data-lucide="archive" style="width:32px;height:32px;margin-bottom:8px"></i>
      <div>No archived cases</div>
      <div style="font-size:12px;margin-top:4px">Archived cases will appear here</div>
    </td></tr>`;
  } else {
    table.innerHTML = archivedCases.map(c => `
      <tr class="row-click" onclick="showCaseDetailsModal('${c.id}')">
        <td><span style="font-family:monospace;font-weight:600">${escapeHtml(c.controlNo)||"—"}</span></td>
        <td>${escapeHtml(c.client.name)||"<span class=muted>Unnamed</span>"}</td>
        <td>${escapeHtml(c.purpose)}</td>
        <td><span class="badge b-archived">${c.status}</span></td>
        <td>${fmtDate(c.updatedAt)}</td>
        <td>
          <div class="actions" style="display:flex; gap: 4px;">
            <button style="background-color: #1A237E; border: none; border-radius: 6px; padding: 6px 10px; cursor:pointer;" onclick="event.stopPropagation(); showCaseDetailsModal('${c.id}')" title="View">
              <i data-lucide="eye" style="width:16px;height:16px; color:#ffffff;"></i>
            </button>
            <button style="background-color: #198754; border: none; border-radius: 6px; padding: 6px 10px; cursor:pointer;" onclick="event.stopPropagation(); restoreCase('${c.id}')" title="Restore">
              <i data-lucide="rotate-ccw" style="width:16px;height:16px; color:#ffffff;"></i>
            </button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  lucide.createIcons();
}

function restoreCase(id){
  Swal.fire({
    title: 'Restore this case?',
    text: 'This will move the case back to the active cases list with Draft status.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Yes, Restore',
    cancelButtonText: 'Cancel',
    confirmButtonColor: '#4338CA',
    cancelButtonColor: '#6B7280'
  }).then((result) => {
    if (result.isConfirmed) {
      const caseRec = getCase(id);
      if(caseRec){
        caseRec.status = 'Draft';
        caseRec.updatedAt = todayISO();
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
    await loadCases();
    console.log('Dashboard cases loaded:', cases.length);
    renderDashboard();
    lucide.createIcons();
    initCharts();
  } catch(e) {
    console.error('Error loading dashboard:', e);
  }
}

function renderDashboard(){
  const byStatus = {};
  STATUSES.forEach(s=> byStatus[s] = cases.filter(c=>c.status===s).length);
  const nearingEligible = cases.filter(c=>{
    if(!c.releasedDate) return false;
    const e = eligibilityInfo(c);
    return !e.eligible && e.daysLeft <= 30;
  }).sort((a,b)=> eligibilityInfo(a).daysLeft - eligibilityInfo(b).daysLeft);

  const recent = [...cases].sort((a,b)=> new Date(b.updatedAt)-new Date(a.updatedAt)).slice(0,6);

  // Calculate KPIs
  const uniqueClients = new Set(cases.map(c => c.client.name.toLowerCase())).size;
  const currentMonth = new Date().getMonth();
  const currentYear = new Date().getFullYear();
  const casesThisMonth = cases.filter(c => {
    const caseDate = new Date(c.createdAt);
    return caseDate.getMonth() === currentMonth && caseDate.getFullYear() === currentYear;
  }).length;
  const today = new Date().toISOString().slice(0,10);
  const releasedToday = cases.filter(c => c.status === 'Released' && c.releasedDate === today).length;
  const successRate = cases.length > 0 ? Math.round(((byStatus['Released'] || 0) / cases.length) * 100) : 0;

  // Update KPIs with null checks
  const updateElement = (id, value) => {
    const el = document.getElementById(id);
    if(el) el.textContent = value;
  };
  
  updateElement('totalClients', uniqueClients);
  updateElement('casesThisMonth', casesThisMonth);
  updateElement('releasedToday', releasedToday);

  // Recent activity feed
  renderActivityFeed(recent);
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
  const today = new Date().toISOString().slice(0,10);
  
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

function renderActivityFeed(recent){
  const container = document.getElementById('activityFeed');
  if(!recent.length){
    container.innerHTML = `<div class="empty" style="padding:40px 20px;text-align:center;color:var(--text-muted)">
      <i data-lucide="bell-off" style="width:48px;height:48px;margin-bottom:12px;opacity:0.5"></i>
      <div style="font-size:14px;font-weight:500">No recent activity</div>
    </div>`;
    return;
  }
  
  const statusColors = {
    'Draft': {bg:'var(--background)', color:'var(--text-muted)'},
    'Review': {bg:'var(--warning-bg)', color:'var(--warning)'},
    'Approved': {bg:'var(--success-bg)', color:'var(--success)'},
    'Printed': {bg:'var(--info-bg)', color:'var(--info)'},
    'Released': {bg:'var(--purple-bg)', color:'var(--purple)'}
  };
  
  container.innerHTML = recent.slice(0,10).map(c=>{
    const colors = statusColors[c.status] || statusColors['Draft'];
    const timeAgo = getTimeAgo(c.updatedAt);
    const clientName = escapeHtml(c.client.name) || 'Unnamed client';
    return `
    <div class="activity-item">
      <div class="activity-icon" style="background:${colors.bg};color:${colors.color}">
        <i data-lucide="file-text"></i>
      </div>
      <div class="activity-content">
        <div class="activity-text">${clientName}'s case is ${c.status.toLowerCase()}</div>
        <div class="activity-time">${timeAgo}</div>
      </div>
    </div>`;
  }).join("");
}

function getTimeAgo(dateStr){
  const now = new Date();
  const date = new Date(dateStr+"T00:00:00");
  const diff = Math.floor((now - date) / (1000 * 60));
  if(diff < 1) return 'Just now';
  if(diff < 60) return `${diff} minute${diff > 1 ? 's' : ''} ago`;
  const hours = Math.floor(diff / 60);
  if(hours < 24) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
  const days = Math.floor(hours / 24);
  return `${days} day${days > 1 ? 's' : ''} ago`;
}

function initCharts(){
  // Assistance Type Chart
  const assistanceCtx = document.getElementById('assistanceChart');
  if(assistanceCtx){
    const purposeCounts = {};
    cases.forEach(c => {
      purposeCounts[c.purpose] = (purposeCounts[c.purpose] || 0) + 1;
    });
    const labels = Object.keys(purposeCounts).length ? Object.keys(purposeCounts) : PURPOSES.slice(0,5);
    const data = labels.map(l => purposeCounts[l] || Math.floor(Math.random() * 20) + 5);
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
  view = {tab:"newCase", newCaseStep:"search", eligClientName:"", eligOverride:false, eligMatch:null, selectedClient:null};
  renderNewCase();
  lucide.createIcons();
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
  
  // Render search results if available
  if(view.eligClientName && view.eligClientName.length >= 2){
    renderSearchResults(view.eligClientName);
  }
  
  // Render client summary if selected
  if(view.selectedClient){
    renderClientSummary(view.selectedClient);
  }
  
  // Render eligibility status if checked
  if(view.eligMatch !== undefined && view.eligMatch !== null){
    renderEligibilityStatus(view.eligMatch);
  }
}

function renderSearchResults(query){
  const container = document.getElementById('searchResults');
  if(!container) return;
  
  console.log('Searching for:', query);
  console.log('Total cases:', cases.length);
  
  // Find matching clients (mock search for now)
  const matches = cases.filter(c => 
    c.client.name.toLowerCase().includes(query.toLowerCase())
  ).slice(0,5);
  
  console.log('Matches found:', matches.length);
  
  // Check eligibility for the searched client
  const eligibility = checkEligibility(query);
  
  if(matches.length === 0){
    const escapedQuery = escapeHtml(query);
    container.style.display = 'block';
    
    if(!eligibility.eligible){
      // Client is ineligible
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
        text: `No clients found matching "${escapedQuery}". This appears to be a new client. You can proceed with the interview.`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Proceed with New Client',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#1E3A8A',
        cancelButtonColor: '#6B7280'
      }).then((result) => {
        if (result.isConfirmed) {
          proceedWithNewClient('${escapedQuery}');
        }
      });
    }
  }else{
    container.style.display = 'block';
    container.innerHTML = matches.map(c => `
      <div class="search-result-item" onclick="selectClient('${c.id}')">
        <div class="search-result-name">${escapeHtml(c.client.name)}</div>
        <div class="search-result-details">
          ${escapeHtml(c.client.sex || '')} • ${escapeHtml(String(c.client.age) || '')} • ${escapeHtml(c.purpose || '')}
        </div>
      </div>
    `).join('');
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
  
  // Show client summary
  renderClientSummary(c);
  
  // Check eligibility
  const match = findLatestByName(c.client.name);
  setView({eligMatch: match, eligOverride:false});
  renderEligibilityStatus(match);
  
  lucide.createIcons();
}

function renderClientSummary(client){
  const container = document.getElementById('clientSummary');
  if(!container) return;
  
  container.style.display = 'block';
  
  // Get initials for avatar
  const name = client.client.name || 'Unknown';
  const initials = name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0,2);
  
  document.getElementById('clientAvatar').textContent = initials;
  document.getElementById('clientNameDisplay').textContent = name;
  document.getElementById('clientAge').textContent = client.client.age || '—';
  document.getElementById('clientSex').textContent = client.client.sex || '—';
  document.getElementById('clientBarangay').textContent = 'Biluso'; // Mock data
  document.getElementById('clientLastCase').textContent = 'None'; // Will be updated by eligibility check
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
        <button class="btn primary" style="margin-top:16px;width:100%" onclick="proceedToIntake()">
          <i data-lucide="arrow-right" style="width:16px;height:16px"></i> Continue to Case Encoding
        </button>
      </div>
    `;
    
    // Update last case study in summary
    const lastCaseEl = document.getElementById('clientLastCase');
    if(lastCaseEl && match){
      lastCaseEl.textContent = fmtDate(match.releasedDate);
    }
  }else{
    // Not eligible - Show SweetAlert popup
    const clientNameEl = document.getElementById('clientNameDisplay');
    const clientAgeEl = document.getElementById('clientAge');
    const clientSexEl = document.getElementById('clientSex');
    const clientBarangayEl = document.getElementById('clientBarangay');
    
    const clientName = clientNameEl ? clientNameEl.textContent : 'Unknown';
    const clientAge = clientAgeEl ? clientAgeEl.textContent : '-';
    const clientSex = clientSexEl ? clientSexEl.textContent : '-';
    const clientBarangay = clientBarangayEl ? clientBarangayEl.textContent : '-';
    
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
              <div style="font-size: 15px; font-weight: 600; color: #111827;">${fmtDate(match.releasedDate)}</div>
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
              Previous Social Case Study was released on ${fmtDate(match.releasedDate)}.
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
              <span>${fmtDate(match.releasedDate)}</span>
              <span>${fmtDate(e.nextEligibleDate)}</span>
            </div>
          </div>
          
          <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 16px;">
            <input type="checkbox" id="overrideCheck" ${view.eligOverride ? 'checked' : ''} style="width: 18px; height: 18px; cursor: pointer;">
            <label for="overrideCheck" style="font-size: 13px; color: #374151; cursor: pointer;">Override and proceed anyway (requires supervisor approval)</label>
          </div>
        </div>
      `,
      icon: 'warning',
      iconColor: '#DC2626',
      showCancelButton: true,
      confirmButtonText: 'Continue to Case Encoding',
      cancelButtonText: 'Cancel',
      confirmButtonColor: '#1E3A8A',
      cancelButtonColor: '#6B7280',
      customClass: {
        popup: 'swal2-popup-custom',
        title: 'swal2-title-custom'
      },
      didOpen: () => {
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
        if(!view.eligOverride){
          Swal.showValidationMessage('Please check the override checkbox to proceed');
          return false;
        }
        proceedToIntake();
        return false;
      }
    });
    
    // Update last case study in summary
    const lastCaseEl = document.getElementById('clientLastCase');
    if(lastCaseEl){
      lastCaseEl.textContent = fmtDate(match.releasedDate);
    }
    
    // Clear the container since we're using SweetAlert
    container.innerHTML = '';
  }
  
  lucide.createIcons();
}

function startEligibilityCheck(){
  const name = document.getElementById('elig-name').value;
  if(!name || name.trim().length < 2){
    alert('Please enter at least 2 characters to search');
    return;
  }
  view.eligClientName = name;
  renderSearchResults(name);
  lucide.createIcons();
}

function proceedWithNewClient(clientName){
  console.log('proceedWithNewClient called with:', clientName);
  
  // Redirect to the intake page
  window.location.href = '/admin/social-case/intake';
}

// Make function globally accessible
window.proceedWithNewClient = proceedWithNewClient;

/* ---------------- Rendering: Intake form ---------------- */
async function loadIntakeForm(){
  await loadCases();
  draftIntake = blankIntake(""); // Initialize with empty form
  renderIntakeForm();
  lucide.createIcons();
}

function renderIntakeForm(){
  const container = document.getElementById('intakeFormContent');
  if(!container) return;

  const d = draftIntake;
  container.innerHTML = `
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
      <div class="field" style="grid-column:span 2"><label>Address (Barangay)</label>
        <select oninput="draftIntake.client.address=this.value">
          <option value="">Select Barangay</option>
          ${BARANGAYS.map(b=>`<option ${d.client.address===b?'selected':''}>${b}</option>`).join("")}
        </select>
      </div>
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
          ${i>0?`<button class="btn ghost btn-sm" style="align-self:flex-end" onclick="draftIntake.household.splice(${i},1); renderIntakeForm();"><i data-lucide="x" style="width:16px;height:16px"></i></button>`:""}
        </div>
      </div>`).join("")}
    <button class="btn ghost btn-sm" onclick="draftIntake.household.push({name:'',relationship:'',age:'',education:'',occupation:'',income:''}); renderIntakeForm();"><i data-lucide="plus" style="width:16px;height:16px"></i> Add family member</button>
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
    </div>
  </div>

  <div class="panel">
    <h3>Agencies & Purpose</h3>
    <div class="field"><label>Purpose</label>
      <select oninput="draftIntake.purpose=this.value">
        ${PURPOSES.map(p=>`<option ${d.purpose===p?'selected':''}>${p}</option>`).join("")}
      </select>
    </div>
    <div class="field"><label>Agencies (select all that apply)</label>
      <div style="display:flex;flex-wrap:wrap;gap:8px">
        ${AGENCIES.map(a=>`<label class="pill-check ${d.agencies.includes(a.key)?'on':''}" onclick="toggleAgency('${a.key}')">${a.name}</label>`).join("")}
      </div>
    </div>
  </div>

  <div class="panel">
    <h3>Requirements</h3>
    ${d.requirements.map((r,i)=>`
      <label class="pill-check ${r.submitted?'on':''}" style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <input type="checkbox" ${r.submitted?'checked':''} onchange="toggleRequirement(${i})"> ${escapeHtml(r.name)}
      </label>
    `).join("")}
  </div>

  <div style="display:flex;gap:12px;margin-top:20px">
    <button class="btn primary" onclick="showIntakeSummaryModal()"><i data-lucide="eye" style="width:16px;height:16px"></i> Review & Save</button>
    <button class="btn" style="background-color: #dc3545; color: white; border: 1px solid #dc3545;" onclick="window.location.href='/admin/social-case/new'"><i data-lucide="x" style="width:16px;height:16px"></i> Cancel</button>
  </div>
  `;
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

/* ---------------- Intake Summary Modal ---------------- */
function showIntakeSummaryModal(){
  const d = draftIntake;
  if(!d) return;

  // Remove any existing modal
  const existing = document.getElementById('intakeSummaryModal');
  if(existing) existing.remove();

  const val = v => escapeHtml(String(v || '')) || '<span style="color:#9CA3AF;font-style:italic">—</span>';
  const fmtDateLocal = iso => {
    if(!iso) return '<span style="color:#9CA3AF;font-style:italic">—</span>';
    try {
      const d = new Date(iso + 'T00:00:00');
      if(isNaN(d.getTime())) return '<span style="color:#9CA3AF;font-style:italic">—</span>';
      return d.toLocaleDateString('en-US', {month:'long', day:'numeric', year:'numeric'});
    } catch(e) { return '<span style="color:#9CA3AF;font-style:italic">—</span>'; }
  };

  const submittedReqs = d.requirements.filter(r => r.submitted).map(r => escapeHtml(r.name));
  const missingReqs  = d.requirements.filter(r => !r.submitted).map(r => escapeHtml(r.name));
  const selectedAgencies = AGENCIES.filter(a => d.agencies.includes(a.key));

  const householdRows = d.household.map((m, i) => `
    <tr>
      <td style="padding:8px 12px;font-size:13px;border-bottom:1px solid #F3F4F6">${val(m.name)}</td>
      <td style="padding:8px 12px;font-size:13px;border-bottom:1px solid #F3F4F6">${val(m.relationship)}</td>
      <td style="padding:8px 12px;font-size:13px;border-bottom:1px solid #F3F4F6">${val(m.age)}</td>
      <td style="padding:8px 12px;font-size:13px;border-bottom:1px solid #F3F4F6">${val(m.education)}</td>
      <td style="padding:8px 12px;font-size:13px;border-bottom:1px solid #F3F4F6">${val(m.occupation)}</td>
      <td style="padding:8px 12px;font-size:13px;border-bottom:1px solid #F3F4F6">${val(m.income)}</td>
    </tr>`);

  const sectionTitle = (label, icon='') => `
    <div style="display:flex;align-items:center;gap:8px;margin:24px 0 12px;padding-bottom:8px;border-bottom:2px solid #E5E7EB">
      ${icon ? `<div style="width:28px;height:28px;background:#EEF2FF;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
        <i data-lucide="${icon}" style="width:14px;height:14px;color:#4338CA"></i>
      </div>` : ''}
      <h3 style="font-size:13px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:0.05em;margin:0">${label}</h3>
    </div>`;

  const infoRow = (label, value) => `
    <div style="display:flex;flex-direction:column;gap:3px;min-width:0">
      <span style="font-size:11px;font-weight:600;color:#6B7280;text-transform:uppercase;letter-spacing:0.04em">${label}</span>
      <span style="font-size:14px;color:#111827;font-weight:500">${value}</span>
    </div>`;

  const modal = document.createElement('div');
  modal.id = 'intakeSummaryModal';
  modal.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(15,23,42,0.55);backdrop-filter:blur(4px);z-index:9999;display:flex;align-items:center;justify-content:center;padding:16px;box-sizing:border-box;animation:fadeIn 0.2s ease';
  modal.innerHTML = `
    <style>
      #intakeSummaryModal * { box-sizing: border-box; }
      @keyframes fadeIn { from{opacity:0} to{opacity:1} }
      @keyframes slideUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
      #intakeSummaryModal .modal-box { animation: slideUp 0.25s ease; }
    </style>
    <div class="modal-box" style="background:#FFFFFF;border-radius:16px;width:100%;max-width:780px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 64px rgba(15,23,42,0.18);overflow:hidden">

      <!-- Modal Header -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid #E5E7EB;background:#FAFAFA;flex-shrink:0">
        <div style="display:flex;align-items:center;gap:12px">
          <div style="width:36px;height:36px;background:#4338CA;border-radius:8px;display:flex;align-items:center;justify-content:center">
            <i data-lucide="file-check" style="width:18px;height:18px;color:#fff"></i>
          </div>
          <div>
            <div style="font-size:17px;font-weight:700;color:#111827;font-family:Inter,sans-serif">Review Case Summary</div>
            <div style="font-size:12px;color:#6B7280;margin-top:1px">Please verify all information before saving</div>
          </div>
        </div>
        <button onclick="closeIntakeSummaryModal()" style="width:32px;height:32px;border:none;background:#F3F4F6;border-radius:8px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.15s;font-size:18px;color:#6B7280;line-height:1" onmouseover="this.style.background='#E5E7EB'" onmouseout="this.style.background='#F3F4F6'">
          &times;
        </button>
      </div>

      <!-- Modal Body -->
      <div style="overflow-y:auto;padding:24px;flex:1">

        <!-- Control No + Date Banner -->
        <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:10px;padding:14px 18px;display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;flex-wrap:wrap;gap:8px">
          <div style="font-size:13px;color:#4338CA">
            <span style="font-weight:600;text-transform:uppercase;letter-spacing:0.04em">Control No.&nbsp;</span>
            <span style="font-family:monospace;font-size:15px;font-weight:700">${val(d.controlNo)}</span>
          </div>
          <div style="font-size:13px;color:#4338CA">
            <span style="font-weight:600">Report Date:&nbsp;</span>
            <span>${fmtDateLocal(d.interview.reportDate)}</span>
          </div>
        </div>

        <!-- Client Info -->
        ${sectionTitle('I. Client Information', 'user')}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px">
          ${infoRow('Full Name', val(d.client.name))}
          ${infoRow('Age', val(d.client.age))}
          ${infoRow('Sex', val(d.client.sex))}
          ${infoRow('Civil Status', val(d.client.civilStatus))}
          ${infoRow('Barangay (Address)', val(d.client.address))}
          ${infoRow('Birthdate', fmtDateLocal(d.client.birthdate))}
          ${infoRow('Birthplace', val(d.client.birthplace))}
          ${infoRow('Religion', val(d.client.religion))}
          ${infoRow('Education', val(d.client.education))}
          ${infoRow('Occupation', val(d.client.occupation))}
          ${infoRow('Income', val(d.client.income))}
          ${infoRow('Contact No.', val(d.client.contact))}
        </div>

        <!-- Family Composition -->
        ${sectionTitle('II. Family Composition', 'users')}
        <div style="overflow-x:auto;border:1px solid #E5E7EB;border-radius:10px;overflow:hidden">
          <table style="width:100%;border-collapse:collapse">
            <thead>
              <tr style="background:#F9FAFB">
                ${['Name','Relationship','Age','Education','Occupation','Income'].map(h =>
                  `<th style="padding:9px 12px;font-size:11px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.04em;text-align:left;border-bottom:1px solid #E5E7EB">${h}</th>`
                ).join('')}
              </tr>
            </thead>
            <tbody>
              ${householdRows.length ? householdRows.join('') : `<tr><td colspan="6" style="padding:16px;text-align:center;color:#9CA3AF;font-size:13px">No family members added</td></tr>`}
            </tbody>
          </table>
        </div>

        <!-- Narrative Sections -->
        ${sectionTitle('Narrative Sections', 'align-left')}
        ${[
          ['III. Problem Presented', d.interview.problemPresented],
          ['IV. Home Condition', d.interview.homeCondition],
          ['V. Socio-Economic Condition', d.interview.socioEconomic],
          ['VI. Evaluation', d.interview.evaluation],
          ['VII. Recommendation', d.interview.recommendation]
        ].map(([label, content]) => `
          <div style="margin-bottom:14px">
            <div style="font-size:11px;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:5px">${label}</div>
            <div style="font-size:14px;color:#${content ? '111827' : '9CA3AF'};background:#F9FAFB;border:1px solid #E5E7EB;border-radius:8px;padding:10px 12px;white-space:pre-wrap;min-height:40px;font-style:${content ? 'normal' : 'italic'}">${content ? escapeHtml(content) : 'Not provided'}</div>
          </div>`).join('')}

        <!-- Signatories -->
        ${sectionTitle('Signatories', 'pen-line')}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
          ${infoRow('Prepared By (Name)', val(d.signers.preparedByName))}
          ${infoRow('Prepared By (Title)', val(d.signers.preparedByTitle))}
          ${infoRow('Noted By (Name)', val(d.signers.notedByName))}
          ${infoRow('Noted By (Title)', val(d.signers.notedByTitle))}
        </div>

        <!-- Agencies & Purpose -->
        ${sectionTitle('Agencies & Purpose', 'building-2')}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:14px">
          ${infoRow('Purpose / Type of Assistance', val(d.purpose))}
          ${infoRow('Agencies Selected', selectedAgencies.length ? selectedAgencies.map(a => `<span style="display:inline-block;background:#EEF2FF;color:#4338CA;font-size:12px;font-weight:600;padding:2px 8px;border-radius:4px;margin:1px">${escapeHtml(a.name)}</span>`).join(' ') : '<span style="color:#9CA3AF;font-style:italic">None selected</span>')}
        </div>

        <!-- Requirements -->
        ${sectionTitle('Requirements', 'clipboard-list')}
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:8px">
          ${d.requirements.map(r => `
            <div style="display:flex;align-items:center;gap:8px;padding:8px 12px;border-radius:8px;border:1px solid ${r.submitted ? '#BBF7D0' : '#FEE2E2'};background:${r.submitted ? '#F0FDF4' : '#FFF5F5'}">
              <div style="width:18px;height:18px;border-radius:50%;background:${r.submitted ? '#16A34A' : '#EF4444'};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <span style="color:#fff;font-size:11px;font-weight:700">${r.submitted ? '✓' : '✗'}</span>
              </div>
              <span style="font-size:12px;color:${r.submitted ? '#166534' : '#991B1B'};font-weight:500">${escapeHtml(r.name)}</span>
            </div>`).join('')}
        </div>
      </div>

      <!-- Modal Footer -->
      <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 24px;border-top:1px solid #E5E7EB;background:#FAFAFA;flex-shrink:0;gap:12px;flex-wrap:wrap">
        <div style="font-size:12px;color:#6B7280">
          <i data-lucide="info" style="width:14px;height:14px;vertical-align:middle;margin-right:4px;color:#9CA3AF"></i>
          Review all fields before saving. This action cannot be undone easily.
        </div>
        <div style="display:flex;gap:10px">
          <button onclick="closeIntakeSummaryModal()" style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;border:1.5px solid #D1D5DB;background:#FFFFFF;color:#374151;font-size:14px;font-weight:600;border-radius:8px;cursor:pointer;transition:all 0.15s;font-family:Inter,sans-serif" onmouseover="this.style.background='#F9FAFB'" onmouseout="this.style.background='#FFFFFF'">
            <i data-lucide="pencil" style="width:15px;height:15px"></i> Edit
          </button>
          <button onclick="closeIntakeSummaryModal(); saveNewCase();" style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;border:none;background:#4338CA;color:#FFFFFF;font-size:14px;font-weight:600;border-radius:8px;cursor:pointer;transition:all 0.15s;font-family:Inter,sans-serif" onmouseover="this.style.background='#3730A3'" onmouseout="this.style.background='#4338CA'">
            <i data-lucide="save" style="width:15px;height:15px"></i> Save Case
          </button>
        </div>
      </div>
    </div>
  `;

  document.body.appendChild(modal);
  // Close on backdrop click
  modal.addEventListener('click', e => { if(e.target === modal) closeIntakeSummaryModal(); });
  // Re-run lucide for newly injected icons
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
  const statusFilter = document.getElementById('statusFilter')?.value || "All";
  const assistanceFilter = document.getElementById('assistanceFilter')?.value || "All";
  const barangayFilter = document.getElementById('barangayFilter')?.value || "All";

  // Filter cases (exclude archived – those live on the archive page)
  let filtered = cases.filter(c => {
    if(c.status === 'Archived') return false;
    const matchesSearch = !searchQuery || 
      c.client.name.toLowerCase().includes(searchQuery) || 
      c.controlNo.toLowerCase().includes(searchQuery) ||
      c.purpose.toLowerCase().includes(searchQuery);
    const matchesStatus = statusFilter === "All" || c.status === statusFilter;
    const matchesAssistance = assistanceFilter === "All" || c.purpose === assistanceFilter;
    const matchesBarangay = barangayFilter === "All" || true;
    return matchesSearch && matchesStatus && matchesAssistance && matchesBarangay;
  });

  // Sort by date (newest first)
  filtered = [...filtered].sort((a,b)=> new Date(b.updatedAt)-new Date(a.updatedAt));

  // Pagination
  const pageSize = 10;
  const currentPage = 1; // Would track current page in real implementation
  const startIndex = (currentPage - 1) * pageSize;
  const endIndex = startIndex + pageSize;
  const paginatedCases = filtered.slice(startIndex, endIndex);
  const totalPages = Math.ceil(filtered.length / pageSize);

  // Render table
  const tableBody = document.getElementById('casesTableBody');
  const emptyState = document.getElementById('emptyState');
  const table = document.getElementById('dataTable');

  if(paginatedCases.length === 0){
    table.style.display = 'none';
    emptyState.style.display = 'block';
  }else{
    table.style.display = 'table';
    emptyState.style.display = 'none';
    tableBody.innerHTML = paginatedCases.map(c => `
      <tr class="row-click" onclick="showCaseDetailsModal('${c.id}')">
        <td><span class="control-no">${escapeHtml(c.controlNo)||"—"}</span></td>
        <td>${escapeHtml(c.client.name)||"<span class=muted>Unnamed</span>"}</td>
        <td>${escapeHtml(c.purpose)}</td>
        <td>Biluso</td>
        <td><span class="badge ${STATUS_CLASS[c.status]}">${c.status}</span></td>
        <td>${fmtDate(c.createdAt)}</td>
        <td>
          <div class="actions" style="display:flex; gap: 4px;">
            <button style="background-color: #1A237E; border: none; border-radius: 6px; padding: 6px 10px; cursor:pointer;" onclick="event.stopPropagation(); showCaseDetailsModal('${c.id}')" title="View">
              <i data-lucide="eye" style="width:16px;height:16px; color:#ffffff;"></i>
            </button>
            ${c.status === 'Approved' ? `
              <button style="background-color: #FBC02D; border: none; border-radius: 6px; padding: 6px 10px; cursor:pointer;" onclick="event.stopPropagation(); window.location.href='/admin/social-case/document/${c.id}/PCSO'" title="Print">
                <i data-lucide="printer" style="width:16px;height:16px; color:#121858;"></i>
              </button>
            ` : ''}
            ${c.status !== 'Archived' ? `
              <button style="background-color: #dc3545; border: none; border-radius: 6px; padding: 6px 10px; cursor:pointer;" onclick="event.stopPropagation(); deleteCase('${c.id}', true)" title="Archive">
                <i data-lucide="archive" style="width:16px;height:16px; color:#ffffff;"></i>
              </button>
            ` : `
              <span style="font-size:11px;color:#9CA3AF;font-style:italic;padding:0 4px;display:flex;align-items:center;">Archived</span>
            `}
          </div>
        </td>
      </tr>
    `).join("");
  }

  // Update pagination info
  const paginationInfo = document.getElementById('paginationInfo');
  if(filtered.length === 0){
    paginationInfo.textContent = 'Showing 0 of 0 Social Case Studies';
  }else{
    const showingFrom = startIndex + 1;
    const showingTo = Math.min(endIndex, filtered.length);
    paginationInfo.textContent = `Showing ${showingFrom}–${showingTo} of ${filtered.length} Social Case Studies`;
  }

  // Update pagination controls
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  prevBtn.disabled = currentPage === 1;
  nextBtn.disabled = currentPage >= totalPages;

  lucide.createIcons();
}

function applyFilters(){
  renderCaseList();
}

function resetFilters(){
  const searchInput = document.getElementById('searchInput');
  if(searchInput) searchInput.value = '';
  
  const statusFilter = document.getElementById('statusFilter');
  if(statusFilter) statusFilter.value = 'All';
  
  const assistanceFilter = document.getElementById('assistanceFilter');
  if(assistanceFilter) assistanceFilter.value = 'All';
  
  const barangayFilter = document.getElementById('barangayFilter');
  if(barangayFilter) barangayFilter.value = 'All';
  
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
}

function markAsPrinted(caseId){
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
    fetch(`/admin/social-case/api/cases/${caseId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
      console.log('Case marked as approved and released:', data);
    })
    .catch(error => {
      console.error('Error updating case status:', error);
    });
  }
}

function printDocument(caseId){
  markAsPrinted(caseId);
  window.print();
}

/* ---------------- Rendering: Case detail ---------------- */
async function loadCaseDetail(caseId){
  await loadCases();
  view = {tab:"caseDetail", caseId:caseId};
  renderCaseDetail();
  lucide.createIcons();
}

function renderCaseDetail(){
  const container = document.getElementById('caseDetailContent');
  if(!container) return;

  const c = getCase(view.caseId);
  if(!c){
    container.innerHTML = `
      <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 20px;color:#6B7280;">
        <i data-lucide="alert-triangle" style="width:48px;height:48px;margin-bottom:16px;color:#D1D5DB;"></i>
        <p style="font-size:1rem;font-weight:600;">Case not found.</p>
        <button onclick="window.location.href='/admin/social-case/cases'" style="margin-top:16px;padding:8px 20px;background:#1A237E;color:white;border:none;border-radius:6px;cursor:pointer;font-size:14px;">← Back to Cases</button>
      </div>`;
    return;
  }

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

  container.innerHTML = `
    <!-- Case Header Bar -->
    <div class="detail-topbar">
      <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:4px;">
          <button onclick="window.location.href='/admin/social-case/cases'" style="background:none;border:none;cursor:pointer;color:#6B7280;display:flex;align-items:center;gap:4px;font-size:13px;padding:0;" onmouseover="this.style.color='#1E3A8A'" onmouseout="this.style.color='#6B7280'">
            <i data-lucide="chevron-left" style="width:16px;height:16px;"></i> Back to Cases
          </button>
        </div>
        <h1 style="margin:0;font-size:1.5rem;font-weight:700;color:#111827;">${escapeHtml(c.client.name)||'Unnamed Client'}</h1>
        <div style="display:flex;align-items:center;gap:10px;margin-top:6px;flex-wrap:wrap;">
          <span class="badge ${STATUS_CLASS[c.status]||'b-draft'}">${c.status}</span>
          <span style="font-size:13px;color:#6B7280;font-family:monospace;font-weight:600;">${escapeHtml(c.controlNo)}</span>
          <span style="font-size:13px;color:#9CA3AF;">·</span>
          <span style="font-size:13px;color:#6B7280;">Created ${fmtDate(c.createdAt)}</span>
        </div>
      </div>
      <div style="display:flex;gap:8px;align-items:center;">
        <button class="btn danger btn-sm" onclick="deleteCase('${c.id}')">
          <i data-lucide="archive" style="width:15px;height:15px;"></i> Archive
        </button>
      </div>
    </div>

    <!-- Two Column Layout -->
    <div class="detail-two-col" style="display:grid;grid-template-columns:2fr 1fr;gap:20px;align-items:start;">

      <!-- Left Column -->
      <div class="detail-col-left">
        ${card('Client Information',
          row('Control No.', `<span style="font-family:monospace;">${escapeHtml(c.controlNo)}</span>`) +
          row('Age / Sex', (escapeHtml(String(c.client.age))||'—') + ' / ' + (escapeHtml(c.client.sex)||'—')) +
          row('Birthdate', c.client.birthdate ? fmtDate(c.client.birthdate) : '—') +
          row('Birthplace', escapeHtml(c.client.birthplace)) +
          row('Civil Status', escapeHtml(c.client.civilStatus)) +
          row('Religion', escapeHtml(c.client.religion)) +
          row('Education', escapeHtml(c.client.education)) +
          row('Occupation', escapeHtml(c.client.occupation)||'N/A') +
          row('Income', escapeHtml(c.client.income)||'N/A') +
          row('Contact', escapeHtml(c.client.contact)) +
          row('Address', escapeHtml(c.client.address))
        )}

        ${card('Interview Summary',
          row('Problem Presented', `<span style="white-space:pre-wrap;">${escapeHtml(c.interview.problemPresented)}</span>`) +
          row('Home Condition', `<span style="white-space:pre-wrap;">${escapeHtml(c.interview.homeCondition)}</span>`) +
          row('Socio-Economic', `<span style="white-space:pre-wrap;">${escapeHtml(c.interview.socioEconomic)}</span>`) +
          row('Evaluation', `<span style="white-space:pre-wrap;">${escapeHtml(c.interview.evaluation)}</span>`) +
          row('Recommendation', `<span style="white-space:pre-wrap;">${escapeHtml(c.interview.recommendation)}</span>`)
        )}
      </div>

      <!-- Right Column -->
      <div class="detail-col-right">
        ${card('Signatories',
          row('Prepared By', escapeHtml(c.signers.preparedByName) + (c.signers.preparedByTitle ? `<br><span style="color:#6B7280;font-size:12px;">${escapeHtml(c.signers.preparedByTitle)}</span>` : '')) +
          row('Noted By', escapeHtml(c.signers.notedByName) + (c.signers.notedByTitle ? `<br><span style="color:#6B7280;font-size:12px;">${escapeHtml(c.signers.notedByTitle)}${c.signers.notedByLicense ? ', Lic. No. '+escapeHtml(c.signers.notedByLicense) : ''}</span>` : ''))
        )}

        ${card('Requirements',
          c.requirements.map(r => `
            <div class="req-check${r.submitted?'':' missing'}">
              <i data-lucide="${r.submitted?'check-circle':'x-circle'}" style="width:16px;height:16px;color:${r.submitted?'#16A34A':'#DC2626'};flex-shrink:0;"></i>
              <span style="font-size:13px;color:#374151;">${escapeHtml(r.name)}</span>
            </div>`).join('') +
          (missingReqs.length ? `<p style="margin:10px 0 0;font-size:12px;color:#DC2626;font-weight:500;"><i data-lucide="alert-circle" style="width:13px;height:13px;vertical-align:middle;margin-right:4px;"></i>${missingReqs.length} requirement(s) still missing.</p>` : `<p style="margin:10px 0 0;font-size:12px;color:#16A34A;font-weight:500;"><i data-lucide="check-circle" style="width:13px;height:13px;vertical-align:middle;margin-right:4px;"></i>All requirements submitted.</p>`)
        )}

        ${card('Status History',
          c.statusHistory.slice().reverse().map(h => `
            <div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #F3F4F6;">
              <span style="font-size:13px;font-weight:600;color:#374151;">${h.status}</span>
              <span style="font-size:12px;color:#6B7280;">${fmtDate(h.date)}</span>
            </div>`).join('') || '<p style="font-size:13px;color:#9CA3AF;">No status history.</p>'
        )}

        ${c.agencies.length ? card('Generate Documents',
          c.agencies.map(a => {
            const ag = AGENCIES.find(x=>x.key===a);
            return `<button onclick="window.location.href='/admin/social-case/document/${c.id}/${a}'"
              style="width:100%;display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:#F8FAFC;border:1px solid #E5E7EB;border-radius:8px;cursor:pointer;margin-bottom:8px;font-size:13px;color:#1E3A8A;font-weight:600;transition:all 0.2s ease;"
              onmouseover="this.style.background='#EEF2FF';this.style.borderColor='#1E3A8A'" onmouseout="this.style.background='#F8FAFC';this.style.borderColor='#E5E7EB'">
              <span style="display:flex;align-items:center;gap:8px;"><i data-lucide="file-text" style="width:15px;height:15px;"></i> ${ag ? ag.name : a}</span>
              <i data-lucide="chevron-right" style="width:15px;height:15px;color:#9CA3AF;"></i>
            </button>`;
          }).join('')
        ) : ''}
      </div>
    </div>
  `;

  lucide.createIcons();
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
  await loadCases();
  view = {tab:"document", caseId:caseId, docAgency:agency};
  renderDocument();
  lucide.createIcons();
}

function renderDocument(){
  const container = document.getElementById('documentContent');
  if(!container) return;

  const c = getCase(view.caseId);
  if(!c){ container.innerHTML = `<div class="empty">Case not found.</div>`; return; }
  
  const agenciesToPrint = view.docAgency === 'all'
    ? (c.agencies && c.agencies.length ? c.agencies : ['PCSO'])
    : [view.docAgency];

  const famRows = c.household.filter(m=>m.name);

  let selectOptions = c.agencies.map(a => {
    const agObj = AGENCIES.find(x => x.key === a);
    return `<option value="${a}" ${a === view.docAgency ? 'selected' : ''}>${agObj ? agObj.name : a}</option>`;
  }).join("");

  if (c.agencies.length > 1) {
    selectOptions += `<option value="all" ${view.docAgency === 'all' ? 'selected' : ''}>All Selected Agencies (${c.agencies.length} copies)</option>`;
  }

  const toolbarHtml = `
  <div class="doc-toolbar no-print" style="box-shadow:var(--shadow);">
    <button class="btn ghost btn-sm" onclick="window.location.href='/admin/social-case/detail/${c.id}'"><i data-lucide="arrow-left" style="width:16px;height:16px"></i> Back to case</button>
    <div style="display:flex;gap:10px;align-items:center;">
      <span style="font-size:13px;font-weight:500;color:var(--text-secondary);white-space:nowrap;">Print Copy:</span>
      <select style="width:auto;padding:8px 32px 8px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;background:var(--surface);cursor:pointer;height:36px;" onchange="window.location.href='/admin/social-case/document/${c.id}/'+this.value">
        ${selectOptions}
      </select>
      <button class="btn primary btn-sm" onclick="printDocument('${c.id}')"><i data-lucide="printer" style="width:15px;height:15px"></i> Print / save as PDF</button>
    </div>
  </div>`;

  const pagesHtml = agenciesToPrint.map(agencyKey => {
    const ag = AGENCIES.find(a => a.key === agencyKey) || AGENCIES[0];
    return `
    <div class="doc-page" style="margin-bottom: 20px;">
      <div style="text-align:right;font-size:13px;margin-bottom:18px">${fmtDate(c.interview.reportDate)}</div>
      <div style="font-size:12.5px;margin-bottom:16px">CONTROL NO. ${escapeHtml(c.controlNo)}</div>
      <div class="doc-title" style="font-weight:700;">Social Case Study Report</div>
      <div class="doc-sub" style="margin-bottom:24px;">(For: ${escapeHtml(c.purpose)})</div>

      <!-- Addressee Block (Very important for official files) -->
      <div style="margin: 20px 0; font-size: 14px; line-height: 1.5; border-left: 3px solid var(--primary); padding-left: 12px; font-style: italic;">
        <strong>To:</strong><br>
        ${escapeHtml(ag.addressee).replace(/\n/g, '<br>')}
      </div>

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
  }).join("");

  container.innerHTML = toolbarHtml + pagesHtml;
}
