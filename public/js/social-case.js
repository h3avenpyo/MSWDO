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
  {key:"DOH", name:"Department of Health", addressee:"The Regional Director\nDepartment of Health"},
  {key:"MSWDO", name:"MSWDO File Copy", addressee:"FILE COPY"}
];
const DEFAULT_REQUIREMENTS = ["Valid government-issued ID","Barangay Certificate of Residency / Indigency","Medical certificate or prescription (if medical)","Certificate of No Property / No Income","Death certificate (if burial assistance)"];
const ELIGIBILITY_DAYS = 180;

let cases = [];
let view = {tab:"dashboard", caseId:null, docAgency:null, newCaseStep:"search", eligClientName:"", eligOverride:false, eligMatch:null, caseListPage:1, archivePage:1, archiveSearch:"", archiveFilter:"", archiveBarangay:""};
let selectedAgency = "PCSO";
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
            <div style="background:var(--surface);padding:8px 12px;border-radius:6px;font-weight:500;border:1px solid var(--border);white-space:pre-wrap;">${escapeHtml(caseRec.interview?.problemPresented)||"—"}</div>
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
function escapeHtml(s){ return String(s==null?"":s).replace(/[&<>"']/g, c=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c])); }

function rewriteProblemPresented(rawProblem, purpose, clientFullName) {
  if (!rawProblem || !rawProblem.trim()) return "";
  const p = rawProblem.trim();
  const purposeLower = (purpose || "").toLowerCase();
  const clientRef = clientFullName || "The client";

  const hasPatientMention = /\b(patient| client| beneficiary)\b/i.test(p);
  const hasAssistanceMention = /\b(assistance| aid | help| support)/i.test(p);
  const hasVisitMention = /\b(visited| went to| came to| approached)/i.test(p);
  const hasRequestMention = /\b(request| ask| seeking| needed| need)/i.test(p);
  const hasSocialCaseMention = /\b(social case study| scsr)/i.test(p);
  const hasMedicalMention = /\b(medical| hemodialysis| dialysis| surgery| hospital| medicine| prescription| treatment| illness| disease| condition| ckd| cancer)/i.test(p);
  const hasBurialMention = /\b(burial| funeral| death| deceased| died| passed away)/i.test(p);
  const hasEducationalMention = /\b(education| tuition| school| college| university| enrollment| studying)/i.test(p);
  const hasFinancialMention = /\b(financial| monetary| cash| fund| expenses| cost| payment)/i.test(p);
  const hasFoodMention = /\b(food| relief| hunger| feeding| livelihood)/i.test(p);

  let sentence1 = "";
  if (hasVisitMention || hasRequestMention || hasAssistanceMention || hasSocialCaseMention) {
    sentence1 = p;
  } else {
    let purposeDesc = purpose || "financial/medical";
    if (hasBurialMention) purposeDesc = "burial";
    else if (hasEducationalMention) purposeDesc = "educational";
    else if (hasFoodMention) purposeDesc = "food/relief";
    sentence1 = `${clientRef} is seeking ${purposeDesc} assistance.`;
    sentence1 += " " + p;
  }

  const alreadyHasClosing = /\b(please see| attached| supporting documents| for your (reference|review)| supporting this request)/i.test(p);
  if (!alreadyHasClosing) {
    sentence1 += " Please see the attached documents for your reference.";
  }

  return sentence1;
}
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
    table.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted)">
      <i data-lucide="${hasFilters ? 'search-x' : 'archive'}" style="width:32px;height:32px;margin-bottom:8px"></i>
      <div>${hasFilters ? 'No matching archived cases' : 'No archived cases'}</div>
      <div style="font-size:12px;margin-top:4px">${hasFilters ? 'Try adjusting your search or filter' : 'Archived cases will appear here'}</div>
    </td></tr>`;
    const pagInfo = document.getElementById('archivePaginationInfo');
    if(pagInfo) pagInfo.textContent = 'Showing 0 of 0 Archived Cases';
    const pagControls = document.getElementById('archivePaginationControls');
    if(pagControls) pagControls.innerHTML = '';
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
      <tr class="row-click" onclick="showCaseDetailsModal('${c.id}')">
        <td><span style="font-family:monospace;font-weight:600">${escapeHtml(c.controlNo)||"—"}</span></td>
        <td>${escapeHtml(c.client?.name)||"<span class=muted>Unnamed</span>"}</td>
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
      const maxButtons = 5;
      let startPage = Math.max(1, currentPage - Math.floor(maxButtons/2));
      let endPage = Math.min(totalPages, startPage + maxButtons - 1);
      if(endPage - startPage < maxButtons - 1) startPage = Math.max(1, endPage - maxButtons + 1);
      for(let i = startPage; i <= endPage; i++){
        pageButtons += `<button class="sc-page-btn ${i===currentPage?'active':''}" onclick="goToArchivePage(${i})">${i}</button>`;
      }
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
          proceedWithNewClient(escapedQuery);
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
    Swal.fire({ icon:'warning', title:'Input Required', text:'Please enter at least 2 characters to search.', confirmButtonColor:'#1E3A8A' });
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
      (c.client?.name || '').toLowerCase().includes(searchQuery) || 
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

  if(paginatedCases.length === 0 && filtered.length === 0){
    table.style.display = 'none';
    emptyState.style.display = 'block';
  }else{
    table.style.display = 'table';
    emptyState.style.display = 'none';
    tableBody.innerHTML = paginatedCases.map(c => `
      <tr class="row-click" onclick="showCaseDetailsModal('${c.id}')">
        <td><span class="control-no">${escapeHtml(c.controlNo)||"—"}</span></td>
        <td>${escapeHtml(c.client?.name)||"<span class=muted>Unnamed</span>"}</td>
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
  const controls = document.getElementById('paginationControls');
  let pageButtons = '';
  pageButtons += `<button class="sc-page-btn" id="prevBtn" ${page<=1?'disabled':''} onclick="goToCaseListPage(${page-1})"><i data-lucide="chevron-left" style="width:14px;height:14px"></i> Previous</button>`;
  const maxButtons = 5;
  let startPage = Math.max(1, page - Math.floor(maxButtons/2));
  let endPage = Math.min(totalPages, startPage + maxButtons - 1);
  if(endPage - startPage < maxButtons - 1) startPage = Math.max(1, endPage - maxButtons + 1);
  for(let i = startPage; i <= endPage; i++){
    pageButtons += `<button class="sc-page-btn ${i===page?'active':''}" onclick="goToCaseListPage(${i})">${i}</button>`;
  }
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
  
  const statusFilter = document.getElementById('statusFilter');
  if(statusFilter) statusFilter.value = 'All';
  
  const assistanceFilter = document.getElementById('assistanceFilter');
  if(assistanceFilter) assistanceFilter.value = 'All';
  
  const barangayFilter = document.getElementById('barangayFilter');
  if(barangayFilter) barangayFilter.value = 'All';
  
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

function printDocument(){
  markAsPrinted(view.caseId);
  window.print();
}

function downloadPDF(){
  const container = document.getElementById('documentPreviewContainer');
  if(!container) return;
  
  // Create a simple print-to-PDF by triggering print dialog
  // For actual PDF generation, you would need a library like html2pdf or jsPDF
  window.print();
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
          width: 210mm; 
          min-height: 297mm; 
          margin: 0; 
          padding: 10mm 25.4mm 32.5mm 25.4mm; 
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
        .document-footer { position: absolute; bottom: 15mm; left: 18mm; right: 18mm; display: flex; justify-content: space-between; font-size: 11px; }
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
  if (!c.household) c.household = [];

  console.log('Case data normalized, agencies:', c.agencies);

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
          size: auto;
        }
        body {
          background: white !important;
          -webkit-print-color-adjust: exact !important;
          print-color-adjust: exact !important;
        }
        .sidebar, header, .doc-toolbar, .no-print,
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
        .page {
          height: 297mm !important;
          min-height: 0 !important;
          margin: 0 !important;
          box-shadow: none !important;
          page-break-after: always;
        }
        .page:last-child {
          page-break-after: avoid;
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
      .document-viewer .page {
        margin-bottom: 24px;
      }
      .document-viewer .page:last-child {
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
        <div class="header-actions">
          <button class="header-btn" onclick="printDocument()">
            <i data-lucide="printer" style="width:16px;height:16px;"></i>
            Print
          </button>
          <button class="header-btn" onclick="downloadPDF()">
            <i data-lucide="file-down" style="width:16px;height:16px;"></i>
            Download PDF
          </button>
          <button class="header-btn" onclick="downloadWord()">
            <i data-lucide="file-text" style="width:16px;height:16px;"></i>
            Download Word
          </button>
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
      <button class="template-tab active" onclick="selectTemplateTab('PCSO', this)">PCSO</button>
      <button class="template-tab" onclick="selectTemplateTab('DSWD', this)">DSWD</button>
      <button class="template-tab" onclick="selectTemplateTab('OP', this)">Office of the President (AKAP)</button>
      <button class="template-tab" onclick="selectTemplateTab('DOH', this)">DOH</button>
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
  const homeConditionDefault = "The client resides in a modest home with his/her family. The home of the family in modest circumstances is simple but functional. While the house may not have the latest appliances or decor, it is clean and maintained to the best of the family's ability. The family may prioritize practicality over style, and although they may face financial challenges, their home remains a place of warmth, care, and togetherness.";
  const socioEconomicDefault = "The family is indigent, and the client depends on their family's income to cover daily expenses and household needs. Unfortunately, there is insufficient funds to sustain the medical expenses of the patient.";
  const evaluationDefault = "This case concerns a client in need of financial/medical assistance for urgent medical expenses. Due to the patient's socio-economic condition, the client is unable to support the medical expenses, prompting her to seek help from your good office, as reflected in the attached documents. The incurred expenses have placed a heavy burden on the family, depleting their financial resources. Consequently, they are earnestly requesting assistance from your office to alleviate their situation.";
  const recommendationDefault = "Due to the lack of sufficient income and the absence of alternative financial resources to meet the patient's needs, the undersigned worker respectfully recommends that the patient be considered for assistance from your office to cover the urgent medical expenses required.";

  const clientName = escapeHtml((c.client?.fullName || c.client?.full_name || c.client?.name || c.clientName || c.client_name || "")).toUpperCase() || notProvided;
  const clientAge = escapeHtml(String(c.client?.age || "")) || notProvided;
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

  const reportDate = fmtDate(c.interviewDate || c.interview?.reportDate || c.createdAt).toUpperCase();

  const rawProblem = c.interview?.interviewSituation || c.interview?.interview_situation || c.interview?.problemPresented || "";
  const purpose = c.purpose || "";
  const clientFirstName = (c.client?.firstName || c.client?.first_name || "").trim();
  const clientLastName = (c.client?.lastName || c.client?.last_name || "").trim();
  const clientFullName = (c.client?.fullName || c.client?.full_name || clientFirstName + " " + clientLastName).trim();
  const ip = escapeHtml(rewriteProblemPresented(rawProblem, purpose, clientFullName)) || notProvided;
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
      <table style="border-radius: 0;">
        <thead>
          <tr>
            <th style="border-radius: 0;">RELATIVES</th>
            <th style="border-radius: 0;">RELATIONSHIP</th>
            <th style="border-radius: 0;">AGE</th>
            <th style="border-radius: 0;">EDUCATIONAL<br>ATTAINMENT</th>
            <th style="border-radius: 0;">OCCUPATION</th>
            <th style="border-radius: 0;">INCOME</th>
          </tr>
        </thead>
        <tbody>
          ${famRows.map(m=>`<tr>
            <td style="border-radius: 0;">${escapeHtml((m.fullName || m.full_name || m.name || "").toUpperCase())}</td>
            <td style="border-radius: 0;">${escapeHtml((m.relationship || "—").toUpperCase())}</td>
            <td align="center" style="border-radius: 0;">${escapeHtml(String(m.age || "")) || "—"}</td>
            <td style="border-radius: 0;">${escapeHtml((m.education || "—").toUpperCase())}</td>
            <td style="border-radius: 0;">${escapeHtml((m.occupation || "N/A").toUpperCase())}</td>
            <td style="border-radius: 0;">${escapeHtml(String(m.monthlyIncome || m.income || "")) || "N/A"}</td>
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
    const parts = pageTemplate.split('<!--PAGE_BREAK-->');
    const totalPages = parts.length;
    
    // Add document-specific CSS
    const docStyles = `
      <style>
        .page {
          width: 210mm;
          min-height: 297mm;
          margin: 20px auto;
          background: white;
          position: relative;
          padding: 10mm 25.4mm 32.5mm 25.4mm;
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
        .content {
          position: relative;
          z-index: 2;
        }
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
        .gov div {
          font-size: 14px;
        }
        .gov h2 {
          margin: 6px 0 0;
          font-size: 13px;
          font-weight: bold;
          letter-spacing: .5px;
          white-space: nowrap;
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
        .right {
          text-align: right;
          font-family: Arial;
          font-size: 11px;
          font-weight: bold;
        }
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
        .row span:first-child {
          font-weight: bold;
        }
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
          font-weight: bold;
          color: black;
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
        .signature small {
          font-size: 12px;
        }
        .document-footer {
          position: absolute;
          bottom: 32.5mm;
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
      </style>
    `;
    
    container.innerHTML = docStyles + parts.map((part, i) => {
      const pn = i + 1;
      return `<div class="page">${part.replace(/{{PAGE_NUMBER}}/g, String(pn)).replace(/{{TOTAL_PAGES}}/g, String(totalPages))}</div>`;
    }).join('');
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
  const container = document.getElementById('documentContent');
  if(!container) return;

  const c = getCase(view.caseId);
  console.log('Case object:', c);
  console.log('Client object:', c.client);
  console.log('Client fields:', c.client ? Object.keys(c.client) : 'No client');
  console.log('Client name:', c.client?.name);
  console.log('Client full_name:', c.client?.full_name);
  console.log('Client age:', c.client?.age);
  console.log('Client sex:', c.client?.sex);
  if(!c){ container.innerHTML = `<div class="empty">Case not found. Case ID: ${view.caseId}</div>`; return; }

  const agenciesToPrint = view.docAgency === 'all'
    ? (c.agencies && c.agencies.length ? c.agencies : ['PCSO'])
    : [view.docAgency];

  const famRows = (c.familyMembers || c.household || []).filter(m=>m.fullName || m.name);

  const notProvided = "Not Provided";
  const homeConditionDefault = "The client resides in a modest home with his/her family. The home of the family in modest circumstances is simple but functional. While the house may not have the latest appliances or decor, it is clean and maintained to the best of the family's ability. The family may prioritize practicality over style, and although they may face financial challenges, their home remains a place of warmth, care, and togetherness.";
  const socioEconomicDefault = "The family is indigent, and the client depends on their family's income to cover daily expenses and household needs. Unfortunately, there is insufficient funds to sustain the medical expenses of the patient.";
  const evaluationDefault = "This case concerns a client in need of financial/medical assistance for urgent medical expenses. Due to the patient's socio-economic condition, the client is unable to support the medical expenses, prompting her to seek help from your good office, as reflected in the attached documents. The incurred expenses have placed a heavy burden on the family, depleting their financial resources. Consequently, they are earnestly requesting assistance from your office to alleviate their situation.";
  const recommendationDefault = "Due to the lack of sufficient income and the absence of alternative financial resources to meet the patient's needs, the undersigned worker respectfully recommends that the patient be considered for assistance from your office to cover the urgent medical expenses required.";

  let selectOptions = (c.agencies || []).map(a => {
    const agObj = AGENCIES.find(x => x.key === a);
    return `<option value="${a}" ${a === view.docAgency ? 'selected' : ''}>${agObj ? agObj.name : a}</option>`;
  }).join("");
  if (c.agencies && c.agencies.length > 1) {
    selectOptions += `<option value="all" ${view.docAgency === 'all' ? 'selected' : ''}>All Selected Agencies (${c.agencies.length} copies)</option>`;
  }

  const clientName = escapeHtml((c.client?.fullName || c.client?.full_name || c.client?.name || c.clientName || c.client_name || "")).toUpperCase() || notProvided;
  const clientAge = escapeHtml(String(c.client?.age || "")) || notProvided;
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

  const reportDate = fmtDate(c.interviewDate || c.interview?.reportDate || c.createdAt).toUpperCase();

  const rawProblem = c.interview?.interviewSituation || c.interview?.interview_situation || c.interview?.problemPresented || "";
  const purpose = c.purpose || "";
  const clientFirstName = (c.client?.firstName || c.client?.first_name || "").trim();
  const clientLastName = (c.client?.lastName || c.client?.last_name || "").trim();
  const clientFullName = (c.client?.fullName || c.client?.full_name || clientFirstName + " " + clientLastName).trim();
  const ip = escapeHtml(rewriteProblemPresented(rawProblem, purpose, clientFullName)) || notProvided;
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
      width: 210mm;
      min-height: 297mm;
      margin: 20px auto;
      background: white;
      position: relative;
      padding: 31.5mm 25.4mm 32.5mm 25.4mm;
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
      bottom: 12mm;
      left: 18mm;
      right: 18mm;
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
    @media print {
      @page { margin: 0; size: auto; }
      html, body, .app, .main { overflow: visible !important; height: auto !important; }
      .no-print { display: none !important; }
      .sidebar, .page-head, .toolbar-row, header { display: none !important; }
      .main { padding: 0; max-width: none; margin: 0; }
      body { background: #fff; }
      .page { margin: 0 !important; box-shadow: none !important; page-break-after: always; break-after: page; }
      .page:last-child { page-break-after: avoid; break-after: avoid; }
    }
  </style>
  <div class="doc-toolbar no-print" style="box-shadow:var(--shadow);max-width:210mm;margin:0 auto 20px;">
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
    const response = await fetch('/templates/social-case-report.html');
    if (!response.ok) throw new Error('Failed to load template');
    let template = await response.text();

    const familyTable = famRows.length ? `
      <table style="border-radius: 0;">
        <thead>
          <tr>
            <th style="border-radius: 0;">RELATIVES</th>
            <th style="border-radius: 0;">RELATIONSHIP</th>
            <th style="border-radius: 0;">AGE</th>
            <th style="border-radius: 0;">EDUCATIONAL<br>ATTAINMENT</th>
            <th style="border-radius: 0;">OCCUPATION</th>
            <th style="border-radius: 0;">INCOME</th>
          </tr>
        </thead>
        <tbody>
          ${famRows.map(m=>`<tr>
            <td style="border-radius: 0;">${escapeHtml((m.fullName || m.full_name || m.name || "").toUpperCase())}</td>
            <td style="border-radius: 0;">${escapeHtml((m.relationship || "—").toUpperCase())}</td>
            <td align="center" style="border-radius: 0;">${escapeHtml(String(m.age || "")) || "—"}</td>
            <td style="border-radius: 0;">${escapeHtml((m.education || "—").toUpperCase())}</td>
            <td style="border-radius: 0;">${escapeHtml((m.occupation || "N/A").toUpperCase())}</td>
            <td style="border-radius: 0;">${escapeHtml(String(m.monthlyIncome || m.income || "")) || "N/A"}</td>
          </tr>`).join("")}
        </tbody>
      </table>` : `<div style="color:#999;margin-top:8px;font-style:italic;">None listed.</div>`;

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
