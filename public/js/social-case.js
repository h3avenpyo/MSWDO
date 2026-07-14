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
  const match = findLatestByName(name);
  setView({eligMatch: match, eligOverride:false});
  renderNewCase();
  lucide.createIcons();
}

function proceedToIntake(){
  draftIntake = blankIntake(view.eligClientName);
  setView({newCaseStep:"intake"});
  renderNewCase();
  lucide.createIcons();
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
function deleteCase(id){
  cases = cases.filter(c=>String(c.id) !== String(id));
  saveCases();
  window.location.href = '/admin/social-case/cases';
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
async function loadDashboard(){
  await loadCases();
  renderDashboard();
  lucide.createIcons();
  initCharts();
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

  // Update workflow cards
  document.getElementById('draftCases').textContent = byStatus['Draft'] || 0;
  document.getElementById('reviewCases').textContent = byStatus['Review'] || 0;
  document.getElementById('approvedCases').textContent = byStatus['Approved'] || 0;
  document.getElementById('printedCases').textContent = byStatus['Printed'] || 0;
  document.getElementById('releasedCases').textContent = byStatus['Released'] || 0;

  // Update trends (mock data for now)
  updateTrend('draftTrend', byStatus['Draft'] || 0);
  updateTrend('reviewTrend', byStatus['Review'] || 0);
  updateTrend('approvedTrend', byStatus['Approved'] || 0, true);
  updateTrend('printedTrend', byStatus['Printed'] || 0);
  updateTrend('releasedTrend', byStatus['Released'] || 0, true);

  // Recent cases table
  const recentTable = document.getElementById('recentCasesTable');
  if(recent.length){
    recentTable.innerHTML = recent.map(c=>`<tr class="row-click" onclick="window.location.href='/admin/social-case/detail/${c.id}'">
      <td><span style="font-family:monospace;font-weight:600">${escapeHtml(c.controlNo)||"—"}</span></td>
      <td>${escapeHtml(c.client.name)||"<span class=muted>Unnamed</span>"}</td>
      <td>${escapeHtml(c.purpose)}</td>
      <td><span class="badge ${STATUS_CLASS[c.status]}">${c.status}</span></td>
      <td>${fmtDate(c.updatedAt)}</td></tr>`).join("");
  }else{
    recentTable.innerHTML = `<tr><td colspan="5"><div class="empty"><i data-lucide="folder-open" style="width:48px;height:48px"></i>No cases yet. Create your first one.</div></td></tr>`;
  }

  // Today's activities
  renderTodayActivities(byStatus);

  // Recent activity feed
  renderActivityFeed(recent);

  // Nearing eligibility
  const nearingPanel = document.getElementById('nearingEligiblePanel');
  const nearingTable = document.getElementById('nearingEligibleTable');
  if(nearingEligible.length){
    nearingPanel.style.display = 'block';
    nearingTable.innerHTML = nearingEligible.map(c=>{
      const e = eligibilityInfo(c);
      return `<tr class="row-click" onclick="window.location.href='/admin/social-case/detail/${c.id}'">
        <td>${escapeHtml(c.client.name)}</td><td>${fmtDate(c.releasedDate)}</td><td>${fmtDate(e.nextEligibleDate)}</td>
        <td><span class="badge b-review">${e.daysLeft} days</span></td>
        <td style="text-align:right"><i data-lucide="chevron-right" class="muted" style="width:16px;height:16px"></i></td></tr>`;
    }).join("");
  }else{
    nearingPanel.style.display = 'none';
  }
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
  const activities = [
    {icon:'user-plus', color:'var(--info-bg)', iconColor:'var(--info)', text:`${byStatus['Draft'] || 0} New Requests`, check:byStatus['Draft'] > 0},
    {icon:'check-circle', color:'var(--success-bg)', iconColor:'var(--success)', text:`${byStatus['Approved'] || 0} Cases Approved`, check:byStatus['Approved'] > 0},
    {icon:'printer', color:'var(--info-bg)', iconColor:'var(--info)', text:`${byStatus['Printed'] || 0} Cases Printed`, check:byStatus['Printed'] > 0},
    {icon:'send', color:'var(--purple-bg)', iconColor:'var(--purple)', text:`${byStatus['Released'] || 0} Cases Released`, check:byStatus['Released'] > 0},
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
    container.innerHTML = `<div class="empty" style="padding:20px"><i data-lucide="bell-off" style="width:32px;height:32px"></i>No recent activity</div>`;
    return;
  }
  
  container.innerHTML = recent.slice(0,5).map(c=>{
    const statusColors = {
      'Draft': {bg:'var(--background)', color:'var(--text-muted)'},
      'Review': {bg:'var(--warning-bg)', color:'var(--warning)'},
      'Approved': {bg:'var(--success-bg)', color:'var(--success)'},
      'Printed': {bg:'var(--info-bg)', color:'var(--info)'},
      'Released': {bg:'var(--purple-bg)', color:'var(--purple)'}
    };
    const colors = statusColors[c.status] || statusColors['Draft'];
    const timeAgo = getTimeAgo(c.updatedAt);
    return `
    <div class="activity-item">
      <div class="activity-icon" style="background:${colors.bg};color:${colors.color}">
        <i data-lucide="file-text" style="width:18px;height:18px"></i>
      </div>
      <div class="activity-content">
        <div class="activity-text">${escapeHtml(c.client.name)||'Unnamed client'} case ${c.status.toLowerCase()}</div>
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
  // Monthly Chart
  const monthlyCtx = document.getElementById('monthlyChart');
  if(monthlyCtx){
    new Chart(monthlyCtx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Cases',
          data: [12, 19, 15, 25, 22, 30],
          backgroundColor: '#232C84',
          borderRadius: 6
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#E5E7EB' } },
          x: { grid: { display: false } }
        }
      }
    });
  }

  // Assistance Type Chart
  const assistanceCtx = document.getElementById('assistanceChart');
  if(assistanceCtx){
    const purposeCounts = {};
    cases.forEach(c => {
      purposeCounts[c.purpose] = (purposeCounts[c.purpose] || 0) + 1;
    });
    const labels = Object.keys(purposeCounts).length ? Object.keys(purposeCounts) : PURPOSES.slice(0,5);
    const data = labels.map(l => purposeCounts[l] || Math.floor(Math.random() * 20) + 5);
    
    new Chart(assistanceCtx, {
      type: 'doughnut',
      data: {
        labels: labels,
        datasets: [{
          data: data,
          backgroundColor: ['#232C84', '#303F9F', '#FFC107', '#10B981', '#8B5CF6'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'right', labels: { boxWidth: 12, padding: 8 } }
        }
      }
    });
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
  
  if(matches.length === 0){
    const escapedQuery = escapeHtml(query);
    container.style.display = 'block';
    container.innerHTML = `
      <div style="padding:16px;text-align:center;color:var(--text-muted)">
        <i data-lucide="search-x" style="width:32px;height:32px;margin-bottom:8px"></i>
        <div>No clients found matching "${escapedQuery}"</div>
        <div style="font-size:12px;margin-top:4px;margin-bottom:16px">This appears to be a new client. You can proceed with the interview.</div>
        <button class="btn primary" id="proceedNewClientBtn">
          <i data-lucide="user-plus" style="width:16px;height:16px"></i> Proceed with New Client
        </button>
      </div>
    `;
    
    // Attach event listener directly
    setTimeout(() => {
      const btn = document.getElementById('proceedNewClientBtn');
      if(btn){
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          console.log('Button clicked');
          proceedWithNewClient('${escapedQuery}');
        });
      }
    }, 100);
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
    // Not eligible
    container.innerHTML = `
      <div class="eligibility-card not-eligible">
        <div class="status-icon"><i data-lucide="x-circle" style="width:24px;height:24px"></i></div>
        <div class="status-title">Not Eligible</div>
        <div class="status-desc">
          Previous Social Case Study was released on ${fmtDate(match.releasedDate)}.
          <br><br>
          <strong>Eligible Again:</strong> ${fmtDate(e.nextEligibleDate)} (${e.daysLeft} days from now)
        </div>
        <div class="elig-wrap" style="margin-top:16px">
          <div class="elig-top"><span class="status-text">Restricted period</span><span class="muted">${e.pct}% elapsed</span></div>
          <div class="elig-track"><div class="elig-fill" style="width:${e.pct}%; background:var(--danger)"></div></div>
          <div class="elig-marks"><span>${fmtDate(match.releasedDate)}</span><span>${fmtDate(e.nextEligibleDate)}</span></div>
        </div>
        <label class="pill-check ${view.eligOverride?'on':''}" style="margin-top:14px;display:flex;align-items:center;gap:8px;cursor:pointer" onclick="event.preventDefault(); setView({eligOverride: !view.eligOverride}); renderEligibilityStatus(match);">
          <input type="checkbox" ${view.eligOverride?'checked':''}> Override and proceed anyway (requires supervisor approval)
        </label>
        <button class="btn ${view.eligOverride?'primary':''}" style="margin-top:12px;width:100%" ${view.eligOverride?'':'disabled'} onclick="proceedToIntake()">
          <i data-lucide="arrow-right" style="width:16px;height:16px"></i> Continue to Case Encoding
        </button>
      </div>
    `;
    
    // Update last case study in summary
    const lastCaseEl = document.getElementById('clientLastCase');
    if(lastCaseEl){
      lastCaseEl.textContent = fmtDate(match.releasedDate);
    }
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
      <label class="pill-check ${r.submitted?'on':''}" style="display:flex;align-items:center;gap:8px;margin-bottom:8px" onclick="toggleRequirement(${i})">
        <input type="checkbox" ${r.submitted?'checked':''}> ${escapeHtml(r.name)}
      </label>
    `).join("")}
  </div>

  <div style="display:flex;gap:12px;margin-top:20px">
    <button class="btn primary" onclick="saveNewCase()"><i data-lucide="save" style="width:16px;height:16px"></i> Save Case</button>
    <button class="btn ghost" onclick="window.location.href='/admin/social-case/new'"><i data-lucide="x" style="width:16px;height:16px"></i> Cancel</button>
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

/* ---------------- Rendering: Case list ---------------- */
async function loadCaseList(){
  console.log('loadCaseList called');
  await loadCases();
  console.log('loadCases completed, rendering...');
  renderCaseList();
  lucide.createIcons();
}

function renderCaseList(){
  // Update summary cards
  const byStatus = {};
  STATUSES.forEach(s=> byStatus[s] = cases.filter(c=>c.status===s).length);
  document.getElementById('totalCases').textContent = cases.length;
  document.getElementById('draftCases').textContent = byStatus['Draft'] || 0;
  document.getElementById('reviewCases').textContent = byStatus['Review'] || 0;
  document.getElementById('approvedCases').textContent = byStatus['Approved'] || 0;
  document.getElementById('releasedCases').textContent = byStatus['Released'] || 0;

  // Get filter values
  const searchQuery = (document.getElementById('searchInput')?.value || "").toLowerCase();
  const statusFilter = document.getElementById('statusFilter')?.value || "All";
  const assistanceFilter = document.getElementById('assistanceFilter')?.value || "All";
  const barangayFilter = document.getElementById('barangayFilter')?.value || "All";

  // Filter cases
  let filtered = cases.filter(c => {
    const matchesSearch = !searchQuery || 
      c.client.name.toLowerCase().includes(searchQuery) || 
      c.controlNo.toLowerCase().includes(searchQuery) ||
      c.purpose.toLowerCase().includes(searchQuery);
    const matchesStatus = statusFilter === "All" || c.status === statusFilter;
    const matchesAssistance = assistanceFilter === "All" || c.purpose === assistanceFilter;
    const matchesBarangay = barangayFilter === "All" || true; // Mock data - would need actual barangay field
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
  const table = document.querySelector('.data-table');

  if(paginatedCases.length === 0){
    table.style.display = 'none';
    emptyState.style.display = 'block';
  }else{
    table.style.display = 'table';
    emptyState.style.display = 'none';
    tableBody.innerHTML = paginatedCases.map(c => `
      <tr class="row-click" onclick="window.location.href='/admin/social-case/detail/${c.id}'">
        <td><span class="control-no">${escapeHtml(c.controlNo)||"—"}</span></td>
        <td>${escapeHtml(c.client.name)||"<span class=muted>Unnamed</span>"}</td>
        <td>${escapeHtml(c.purpose)}</td>
        <td>Biluso</td>
        <td><span class="badge ${STATUS_CLASS[c.status]}">${c.status}</span></td>
        <td>${fmtDate(c.createdAt)}</td>
        <td>
          <div class="actions">
            <button class="action-btn" onclick="event.stopPropagation(); window.location.href='/admin/social-case/detail/${c.id}'" title="View">
              <i data-lucide="eye" style="width:14px;height:14px"></i>
            </button>
            ${c.status === 'Draft' ? `
              <button class="action-btn danger" onclick="event.stopPropagation(); if(confirm('Delete this case?')) deleteCase('${c.id}')" title="Delete">
                <i data-lucide="trash" style="width:14px;height:14px"></i>
              </button>
            ` : ''}
            ${c.status === 'Approved' ? `
              <button class="action-btn" onclick="event.stopPropagation(); window.location.href='/admin/social-case/document/${c.id}/PCSO'" title="Print">
                <i data-lucide="printer" style="width:14px;height:14px"></i>
              </button>
            ` : ''}
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
  document.getElementById('searchInput').value = '';
  document.getElementById('statusFilter').value = 'All';
  document.getElementById('assistanceFilter').value = 'All';
  document.getElementById('barangayFilter').value = 'All';
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
  if(!c){ container.innerHTML = `<div class="empty"><i data-lucide="alert-triangle" style="width:48px;height:48px"></i>Case not found.</div>`; return; }
  const idx = STATUSES.indexOf(c.status);
  const missingReqs = c.requirements.filter(r=>!r.submitted);

  container.innerHTML = `
  <div class="page-head">
    <div><h1>${escapeHtml(c.client.name)||"Unnamed client"}</h1><p>${escapeHtml(c.purpose)} · Created ${fmtDate(c.createdAt)}</p></div>
    <div style="display:flex;gap:8px">
      <button class="btn ghost" onclick="window.location.href='/admin/social-case/cases'"><i data-lucide="arrow-left" style="width:16px;height:16px"></i> All cases</button>
      <button class="btn danger" onclick="if(confirm('Delete this case permanently?')) deleteCase('${c.id}')"><i data-lucide="trash" style="width:16px;height:16px"></i></button>
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
      ${idx>0?`<button class="btn ghost btn-sm" onclick="revertStatus(getCase('${c.id}'))"><i data-lucide="arrow-left" style="width:16px;height:16px"></i> Send back</button>`:""}
      ${idx<STATUSES.length-1?`<button class="btn primary btn-sm" onclick="advanceStatus(getCase('${c.id}'))"><i data-lucide="arrow-right" style="width:16px;height:16px"></i> Advance to ${STATUSES[idx+1]}</button>`:`<span class="badge b-released"><i data-lucide="flag" style="width:16px;height:16px"></i> Released ${fmtDate(c.releasedDate)}</span>`}
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
        ${c.requirements.map(r=>`<div class="req-check ${r.submitted?'':'missing'}"><i data-lucide="${r.submitted?'check-circle':'x-circle'}" style="width:16px;height:16px"></i> ${escapeHtml(r.name)}</div>`).join("")}
        ${missingReqs.length ? `<div class="hint" style="margin-top:8px;color:var(--red-ink)">${missingReqs.length} requirement(s) missing.</div>`:""}
      </div>
      <div class="panel">
        <h3>Generate document</h3>
        ${c.agencies.length ? c.agencies.map(a=>{
          const ag = AGENCIES.find(x=>x.key===a);
          return `<button class="btn" style="width:100%;justify-content:space-between;margin-bottom:8px" onclick="window.location.href='/admin/social-case/document/${c.id}/${a}'">
            <span><i data-lucide="file-text" style="width:16px;height:16px"></i> ${ag.name}</span><i data-lucide="chevron-right" style="width:16px;height:16px"></i></button>`;
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
  <div class="doc-toolbar no-print">
    <button class="btn ghost" onclick="window.location.href='/admin/social-case/detail/${c.id}'"><i data-lucide="arrow-left" style="width:16px;height:16px"></i> Back to case</button>
    <div style="display:flex;gap:8px;align-items:center;">
      <span style="font-size:13px;font-weight:500;color:var(--text-secondary)">Print Copy:</span>
      <select style="width:auto" onchange="window.location.href='/admin/social-case/document/${c.id}/'+this.value">
        ${selectOptions}
      </select>
      <button class="btn primary" onclick="window.print()"><i data-lucide="printer" style="width:16px;height:16px"></i> Print / save as PDF</button>
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
