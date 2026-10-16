@extends('layouts.navbar')

@section('title', 'iSERVE SILANG')

@section('content')
<!-- Hero Section -->
<section id="home" class="relative h-screen w-full overflow-hidden flex flex-col md:flex-row">
    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/frontpic.png') }}');"></div>
    <div class="absolute inset-0 bg-black/70"></div>
    <!-- Left Content -->
    <div class="relative z-10 w-full md:w-1/2 h-full flex items-center justify-center md:justify-start px-6 md:px-12 py-16 md:py-0">
        <div class="text-white z-20 max-w-xl space-y-6">
            <h1 data-animate class="text-3xl sm:text-4xl md:text-5xl font-extrabold leading-tight opacity-0 translate-y-4 transition-all duration-700">
                You Are <span class="text-orange-500">Not Alone</span>
            </h1>
            <p data-animate class="text-base sm:text-lg md:text-lg text-white/80 leading-relaxed opacity-0 translate-y-4 transition-all duration-700 delay-200">
                Violence against women and children is never acceptable. We’re here to provide support, resources, and a path to safety and justice.
            </p>
            <div data-animate class="flex flex-col sm:flex-row gap-4 opacity-0 translate-y-4 transition-all duration-700 delay-300">
                <a href="#learnmore" class="bg-orange-500 hover:bg-orange-400 text-white font-semibold px-5 py-2 rounded text-center">
                    Learn More
                </a>
            </div>
        </div>
    </div>
    <!-- Right Slanted Section -->
    <div class="relative z-10 w-full md:w-1/2 h-[300px] md:h-auto clip-diagonal bg-gradient-to-l from-purple-900 to-purple-800 flex flex-col justify-center items-center text-white px-6 py-10 md:px-0 md:py-0">
        <img data-animate src="{{ asset('images/wcpu.png') }}" alt="Stop Violence" class="w-50 md:w-100 mb-4 opacity-0 translate-y-4 transition-all duration-700 delay-300" />

        <h2 data-animate class="text-lg sm:text-xl md:text-2xl tracking-widest uppercase text-center opacity-0 translate-y-4 transition-all duration-700 delay-500">
            Stop Violence
        </h2>
        <div data-animate class="w-12 md:w-16 h-1 bg-orange-500 mt-2 opacity-0 translate-y-4 transition-all duration-700 delay-700"></div>
    </div>
</section>




<!--About WCPU-->
<section id="learnmore" class="py-20 px-6 bg-[#F2F0EF]">
<div class="max-w-6xl mx-auto">
<div class="relative mb-12 overflow-hidden rounded-xl shadow-2xl max-w-6xl mx-auto">
  <div class="carousel-images flex transition-transform duration-700 ease-in-out">
    <!-- Slide 1 -->
    <div class="relative w-full h-[400px] flex-shrink-0">
      <img src="images/2022.png" class="w-full h-full object-cover" />
      <div class="absolute inset-0 bg-black/50 flex items-center">
        <div class="text-white max-w-md pl-8 md:pl-16 py-6">
          <h2 class="text-2xl md:text-3xl font-bold mb-2 drop-shadow-lg">Empowering Families</h2>
          <p class="text-sm md:text-base text-white/90 drop-shadow-sm">
            Providing strength and support through compassion and care.
          </p>
        </div>
      </div>
    </div>
    <!-- Slide 2 -->
    <div class="relative w-full h-[400px] flex-shrink-0">
      <img src="images/2023.png" class="w-full h-full object-cover" />
      <div class="absolute inset-0 bg-black/50 flex items-center">
        <div class="text-white max-w-md pl-8 md:pl-16 py-6">
          <h2 class="text-2xl md:text-3xl font-bold mb-2 drop-shadow-lg">Protecting Children</h2>
          <p class="text-sm md:text-base text-white/90 drop-shadow-sm">
            Ensuring every child is safe, heard, and cared for.
          </p>
        </div>
      </div>
    </div>
    <!-- Slide 3 -->
    <div class="relative w-full h-[400px] flex-shrink-0">
      <img src="images/2024.png" class="w-full h-full object-cover" />
      <div class="absolute inset-0 bg-black/50 flex items-center">
        <div class="text-white max-w-md pl-8 md:pl-16 py-6">
          <h2 class="text-2xl md:text-3xl font-bold mb-2 drop-shadow-lg">Standing for Justice</h2>
          <p class="text-sm md:text-base text-white/90 drop-shadow-sm">
            Advocating for victims and holding perpetrators accountable.
          </p>
        </div>
      </div>
    </div>
  </div>
  <!-- Overlay Dots -->
  <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-20 flex space-x-2">
    <span class="dot w-3 h-3 bg-purple-600 rounded-full"></span>
    <span class="dot w-3 h-3 bg-gray-300 rounded-full"></span>
    <span class="dot w-3 h-3 bg-gray-300 rounded-full"></span>
  </div>
</div>
<!-- Cards Grid -->
<div class="grid gap-8 mt-12 grid-cols-1 sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-3">
  <!-- Card 1 -->
  <div class="bg-white border border-gray-200 shadow-md rounded-xl p-8 text-center transition duration-300 ease-in-out transform hover:-translate-y-2 hover:scale-105 hover:shadow-xl">
    <div class="flex justify-center mb-4">
      <div class="bg-purple-100 text-purple-600 rounded-full p-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 20l9-5-9-5-9 5 9 5z" />
          <path d="M12 12l9-5-9-5-9 5 9 5z" />
        </svg>
      </div>
    </div>
    <h3 class="text-xl font-semibold text-purple-800 mb-3 opacity-0 translate-y-4 transition-all duration-700 ease-out" data-animate>
      What is the Women and Children Protection Unit?
    </h3>
    <p class="text-gray-600 text-sm leading-relaxed opacity-0 translate-y-4 transition-all duration-700 ease-out" data-animate>
      The Women and Children Protection Unit (WCPU) is a specialized facility within hospitals or public 
      health institutions designed to provide comprehensive, multidisciplinary services for women and 
      children who are victims of violence, abuse, and exploitation.
    </p>
  </div>
  <!-- Card 2 -->
  <div class="bg-white border border-gray-200 shadow-md rounded-xl p-8 text-center transition duration-300 ease-in-out transform hover:-translate-y-2 hover:scale-105 hover:shadow-xl">
    <div class="flex justify-center mb-4">
      <div class="bg-green-100 text-green-600 rounded-full p-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10" />
          <path d="M9 10h.01M15 10h.01M12 17c1.5-1.5 4.5-1.5 4.5-4.5" />
        </svg>
      </div>
    </div>
    <h3 class="text-xl font-semibold text-green-700 mb-3 opacity-0 translate-y-4 transition-all duration-700 ease-out" data-animate>
      Who Can Approach WCPU?
    </h3>
    <ul class="text-gray-600 text-sm leading-relaxed opacity-0 translate-y-4 transition-all duration-700 ease-out text-center" data-animate>
      <li>Women of all ages who have experienced any form of abuse or violence.</li>
      <li>Children and adolescents who are victims of physical, sexual, or emotional abuse.</li>
      <li>Vulnerable individuals who are at risk of exploitation or neglect.</li>
      <li>Guardians, parents, or relatives of minor victims seeking assistance.</li>
      <li>Teachers, barangay officials, health workers, or any mandated reporter who suspects a case of abuse.</li>
    </ul>
  </div>
  <!-- Card 3 -->
  <div class="bg-white border border-gray-200 shadow-md rounded-xl p-8 text-center transition duration-300 ease-in-out transform hover:-translate-y-2 hover:scale-105 hover:shadow-xl">
    <div class="flex justify-center mb-4">
      <div class="bg-orange-100 text-orange-500 rounded-full p-3">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 3h18v18H3V3z" />
          <path d="M16 8h-4v8h-4" />
        </svg>
      </div>
    </div>
    <h3 class="text-xl font-semibold text-orange-600 mb-3 opacity-0 translate-y-4 transition-all duration-700 ease-out" data-animate>
      Services Offered
    </h3>
    <ul class="text-gray-600 text-sm leading-relaxed opacity-0 translate-y-4 transition-all duration-700 ease-out text-center" data-animate>
      <li>Medical Examination & Treatment</li>
      <li>Psychosocial Support & Counseling</li>
      <li>Legal Documentation</li>
      <li>Assistance in Filing Blotters & Legal Cases</li>
      <li>Referral to Partner Agencies</li>
    </ul>
  </div>
</div>
</section>





<hr class="my-12 border-t-1 border-[#C2BFBC] w-full lg:w-[70%] mx-auto">
<!--LEGAL FRAMEWORK-->
<section id="law" class="py-12 px-2 sm:px-4 bg-[#F2F0EF]">
  <div class="max-w-4xl mx-auto text-center">
    <!-- Section Title -->
    <h2 class="text-4xl sm:text-5xl font-bold text-orange-600 mb-6 opacity-0 translate-y-4 transition-all duration-700" data-animate>
      LAWS & RIGHTS
    </h2>
    <!-- Info Box -->
    <div class="w-full max-w-4xl mx-auto px-4">
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
    <!-- Card 1 -->
    <div class="rounded-md p-4 flex flex-col items-center gap-4 opacity-0 translate-y-4 transition-all duration-700" data-animate>
      <img src="/images/law.png" alt="Law Icon" class="w-40 h-40 object-contain">
      <div class="text-center text-xs sm:text-sm font-medium text-gray-800 space-y-2">
        <h3 class="text-base font-semibold text-black">Importance of Legal Protection</h3>
        <p class="text-sm text-gray-500">
          Legal protection is crucial in helping survivors of abuse find justice and safety. 
          It holds offenders accountable and ensures that victims are supported through proper 
          legal and social services.
        </p>
      </div>
    </div>
    <!-- Card 2 -->
    <div class="rounded-md p-4 flex flex-col items-center gap-4 opacity-0 translate-y-4 transition-all duration-700" data-animate>
      <img src="/images/law.png" alt="Law Icon" class="w-40 h-40 object-contain">
      <div class="text-center text-xs sm:text-sm font-medium text-gray-800 space-y-2">
        <h3 class="text-base font-semibold text-black">Victims Have Rights and Access to Justice</h3>
        <p class="text-sm text-gray-500">
          Victims of violence have the right to be protected, heard, and helped. Philippine laws 
          guarantee access to legal aid, medical care, safe shelter, and the right to file charges against abusers.
        </p>
      </div>
    </div>
  </div>
</div>
  <!-- Accordion List -->
    <div class="space-y-4 text-left">
      <!-- START Accordion Items -->
      <!-- Use this pattern 9 times with unique RA content -->
      <div class="accordion-item bg-white shadow-md rounded-md opacity-0 translate-y-4 transition-all duration-700" data-animate>
        <button type="button" class="accordion-btn w-full flex justify-between items-center px-4 py-3 font-semibold text-left text-black">
          Republic Act 9262 - Anti-Violence Against Women and Their Children Act
          <svg class="w-4 h-4 transform transition-transform duration-300 text-black" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 9l6 6 6-6" />
          </svg>
        </button>
        <div class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out px-4 text-sm text-gray-700">
          <p class="py-3">
            Penalizes abuse against women and their children by a partner, ex-partner, or someone with whom they have a child or relationship.<br>
            <strong>Who it protects:</strong> Women and children<br>
            <strong>Penalties:</strong> Up to 12 years imprisonment depending on the form of abuse (physical, psychological, economic, or sexual)
          </p>
        </div>
      </div>
      <!-- RA 8353 -->
      <div class="accordion-item bg-white shadow-md rounded-md opacity-0 translate-y-4 transition-all duration-700" data-animate>
        <button type="button" class="accordion-btn w-full flex justify-between items-center px-4 py-3 font-semibold text-left text-black">
          Republic Act 8353 - Anti-Rape Law of 1997
          <svg class="w-4 h-4 transform transition-transform duration-300 text-black" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" /></svg>
        </button>
        <div class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out px-4 text-sm text-gray-700">
          <p class="py-3">
            Redefines rape as a crime against persons and expands the legal scope.<br>
            <strong>Who it protects:</strong> Anyone, especially women and minors<br>
            <strong>Penalties:</strong> Reclusion perpetua or life imprisonment
          </p>
        </div>
      </div>
      <!-- RA 7610 -->
      <div class="accordion-item bg-white shadow-md rounded-md opacity-0 translate-y-4 transition-all duration-700" data-animate>
        <button type="button" class="accordion-btn w-full flex justify-between items-center px-4 py-3 font-semibold text-left text-black">
          Republic Act 7610 – Special Protection of Children Against Abuse, Exploitation, and Discrimination
          <svg class="w-4 h-4 transform transition-transform duration-300 text-black" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" /></svg>
        </button>
        <div class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out px-4 text-sm text-gray-700">
          <p class="py-3">
            Provides protection to children from abuse and exploitation.<br>
            <strong>Who it protects:</strong> Children under 18<br>
            <strong>Penalties:</strong> 6–20 years imprisonment depending on offense
          </p>
        </div>
      </div>
      <!-- RA 9208 / 10364 -->
      <div class="accordion-item bg-white shadow-md rounded-md opacity-0 translate-y-4 transition-all duration-700" data-animate>
        <button type="button" class="accordion-btn w-full flex justify-between items-center px-4 py-3 font-semibold text-left text-black">
          Republic Act 9208 / 10364 – Anti-Trafficking in Persons Act
          <svg class="w-4 h-4 transform transition-transform duration-300 text-black" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" /></svg>
        </button>
        <div class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out px-4 text-sm text-gray-700">
          <p class="py-3">
            Criminalizes trafficking for exploitation.<br>
            <strong>Who it protects:</strong> Victims, especially women and children<br>
            <strong>Penalties:</strong> 15 years to life imprisonment, ₱5M fine
          </p>
        </div>
      </div>
      <!-- RA 7877 -->
      <div class="accordion-item bg-white shadow-md rounded-md opacity-0 translate-y-4 transition-all duration-700" data-animate>
        <button type="button" class="accordion-btn w-full flex justify-between items-center px-4 py-3 font-semibold text-left text-black">
          Republic Act 7877 – Anti-Sexual Harassment Act
          <svg class="w-4 h-4 transform transition-transform duration-300 text-black" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" /></svg>
        </button>
        <div class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out px-4 text-sm text-gray-700">
          <p class="py-3">
            Penalizes unwanted sexual advances in professional and academic settings.<br>
            <strong>Who it protects:</strong> Employees, students, trainees<br>
            <strong>Penalties:</strong> 1–6 months or fines
          </p>
        </div>
      </div>
      <!-- RA 11313 -->
      <div class="accordion-item bg-white shadow-md rounded-md opacity-0 translate-y-4 transition-all duration-700" data-animate>
        <button type="button" class="accordion-btn w-full flex justify-between items-center px-4 py-3 font-semibold text-left text-black">
          Republic Act 11313 – Safe Spaces Act (Bawal Bastos Law)
          <svg class="w-4 h-4 transform transition-transform duration-300 text-black" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" /></svg>
        </button>
        <div class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out px-4 text-sm text-gray-700">
          <p class="py-3">
            Expands protection from harassment in public, online, work, and schools.<br>
            <strong>Who it protects:</strong> Everyone<br>
            <strong>Penalties:</strong> Fines or jail, depending on offense
          </p>
        </div>
      </div>
      <!-- RA 11596 -->
      <div class="accordion-item bg-white shadow-md rounded-md opacity-0 translate-y-4 transition-all duration-700" data-animate>
        <button type="button" class="accordion-btn w-full flex justify-between items-center px-4 py-3 font-semibold text-left text-black">
          Republic Act 11596 – Prohibition of Child Marriage
          <svg class="w-4 h-4 transform transition-transform duration-300 text-black" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" /></svg>
        </button>
        <div class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out px-4 text-sm text-gray-700">
          <p class="py-3">
            Criminalizes arranging or officiating child marriages.<br>
            <strong>Who it protects:</strong> Children (under 18)<br>
            <strong>Penalties:</strong> Up to 12 years imprisonment
          </p>
        </div>
      </div>
      <!-- RA 9775 -->
      <div class="accordion-item bg-white shadow-md rounded-md opacity-0 translate-y-4 transition-all duration-700" data-animate>
        <button type="button" class="accordion-btn w-full flex justify-between items-center px-4 py-3 font-semibold text-left text-black">
          Republic Act 11930 – Anti-Online Sexual Abuse or Exploitation of Children (OSAEC)
          <svg class="w-4 h-4 transform transition-transform duration-300 text-black" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" /></svg>
        </button>
        <div class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out px-4 text-sm text-gray-700">
          <p class="py-3">
            a law that aims to protect children from all forms of online sexual violence and exploitation, including the production, 
            dissemination, possession, and access of child sexual abuse material (CSAM) using information and communications technology (ICT). <br>
            <strong>Who it protects:</strong> Minors<br>
            <strong>Penalties:</strong> least ₱1,000.00 for a first offense, not less than ₱1,000.00 but not more than ₱5,000.00 for a second offense,
             and imprisonment of not less than six (6) months but not more than one (1) year for the third and subsequent offenses. 
          </p>
        </div>
      </div>
      <!-- RA 9344 -->
      <div class="accordion-item bg-white shadow-md rounded-md opacity-0 translate-y-4 transition-all duration-700" data-animate>
        <button type="button" class="accordion-btn w-full flex justify-between items-center px-4 py-3 font-semibold text-left text-black">
          Republic Act 9344 – Juvenile Justice and Welfare Act
          <svg class="w-4 h-4 transform transition-transform duration-300 text-black" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24"><path d="M6 9l6 6 6-6" /></svg>
        </button>
        <div class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out px-4 text-sm text-gray-700">
          <p class="py-3">
            Establishes a child-sensitive justice system for minors.<br>
            <strong>Who it protects:</strong> Children under 18 in conflict with the law<br>
            <strong>Penalties:</strong> Focus on rehabilitation, not punishment
          </p>
        </div>
      </div>
    </div>
  </div>
</section>





<!--CONTACTS-->
<hr class="my-12 border-t-1 border-[#C2BFBC] w-full lg:w-[70%] mx-auto">
<section id="contact" class="w-full max-w-6xl mx-auto px-4 py-12">
<h2 class="py-6 text-4xl sm:text-5xl font-bold text-orange-600 text-center mb-4 opacity-0 translate-y-4 transition-all duration-700" data-animate>
    CONTACTS
  </h2>
  <p class="text-gray-600 text-center mb-10 max-w-2xl mx-auto">
    Here are the important hotlines and a map locator to help you reach the nearest offices and services. 
    Use the search bar to quickly find the support you need.
  </p>
  <!-- Service selector dropdown - ABOVE everything -->
  <div class="mb-4 max-w-xs relative z-10">
    <select id="serviceSelect" class="w-full px-4 py-2 rounded-lg shadow-md bg-white text-black focus:ring-2 focus:ring-orange-500 focus:outline-none">
      <option value="none">Select service</option>
      <option value="hospital">Hospitals</option>
      <option value="police">Police Stations</option>
      <option value="barangay">Barangay Halls</option>
      <option value="orphanage">Orphanages & Children Homes</option>
      <option value="municipal">Municipal Hall</option>
    </select>
  </div>
  
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
    <!-- Map with Service Selector -->
    <div id="mapContainer" style="width:100%; height:600px; position: relative; z-index: 1;"></div>
    <!-- Hotlines -->
    <div class="bg-white border border-gray-200 shadow-lg rounded-xl p-6 flex flex-col justify-center">
      <h3 class="text-2xl font-bold text-orange-600 mb-6 text-center">Emergency Hotlines</h3>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-gray-700">
        <!-- Column 1 -->
        <ul class="space-y-3">
          <li><b>Silang Municipal Office:</b> (046) 414-0202</li>
          <li><b>PDRRMO (Silang):</b> (046) 424-0203</li>
          <li><b>PNP WCPD – Silang:</b> (046) 414-0204 / 0998-397-0222 / 0995-387-8996</li>
          <li><b>Silang PNP Mobile:</b> 0998-598-5622 / 0998-967-3372</li>
          <li><b>DSWD AICS:</b> 8962-2813 / 8951-7433</li>
          <li><b>DSWD Central Office:</b> (632) 8-931-8101 to 07</li>
          <li><b>DSWD Mobile:</b> 0919-911-6200 / 0917-110-5686 / 0917-827-2543</li>
        </ul>
        <!-- Column 2 -->
        <ul class="space-y-3">
          <li><b>Makabata Helpline:</b> 1383</li>
          <li><b>Bantay Bata Hotline:</b> 163</li>
          <li><b>Emergency (All):</b> 911</li>
          <li><b>NCMH Mental Health:</b> 1553 / 1-800-1888-1553</li>
          <li><b>Complaints Hotline:</b> 8888</li>
          <li><b>Anti-Trafficking Line:</b> 1343</li>
          <li><b>PNP Women’s Desk:</b> 117</li>
          <li><b>Medical Assistance:</b> 1555</li>
          <li><b>DOH Hotline:</b> (02) 894-COVID</li>
          <li><b>DSWD Help:</b> 0932-933-3251</li>
        </ul>
      </div>
    </div>
  </div>
</section>





<!--about us section-->
<hr class="my-12 border-t-1 border-[#C2BFBC] w-full lg:w-[70%] mx-auto">
<section id="about" class="py-16">
    <div class="container mx-auto px-6 text-center">
        <!-- Section Title -->
        <h2 class="text-4xl sm:text-5xl font-bold text-orange-600 mb-10 opacity-0 translate-y-4 transition-all duration-700" data-animate>
            About Us
        </h2>
        <!-- Small Description -->
        <p class="max-w-4xl mx-auto text-gray-600 opacity-0 translate-y-4 transition-all duration-700" data-animate>
          The Women and Children Protection Unit (WCPU) – Silang is a multidisciplinary service hub dedicated to responding 
          to cases of abuse, violence, and exploitation involving women and children. We are committed to providing 
          survivor-centered support, including medical examination, psychological first aid, case documentation, 
          legal assistance, and referrals to appropriate agencies. Located within the Silang local health and government network, the WCPU works closely with barangay SWD desks,
          the PNP Women and Children’s Protection Desk, and social workers to ensure each case is handled with dignity, 
          privacy, and urgency.
      </p>
        <!-- Vision & Mission in Columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto mb-10 mt-20">
            <div class="opacity-0 translate-y-4 transition-all duration-700" data-animate>
                <h3 class="text-2xl font-bold text-purple-800 mb-4">Vision</h3>
                <p class="text-gray-600">
                  A safer Silang community where women and children are free from violence, empowered to speak out, and supported through coordinated medical, legal, and psychosocial care.                </p>
            </div>
            <div class="opacity-0 translate-y-4 transition-all duration-700" data-animate>
                <h3 class="text-2xl font-bold text-purple-800 mb-4">Mission</h3>
                <p class="text-gray-600">
                  To protect and uphold the rights of women and children in Silang by providing prompt, compassionate, and comprehensive services to survivors of abuse and violence.                </p>
            </div>
        </div>
        <!-- Team Members -->
<div class="max-w-5xl mx-auto space-y-6">
  <div class="max-w-5xl mx-auto space-y-3">
  <div class="text-center py-10 opacity-0 translate-y-4 transition-all duration-700" data-animate>
    <h2 class="text-3xl font-bold text-center text-orange-600">Meet Our Team</h2>
    <p class="text-gray-600 text-sm">The people behind our mission</p>
  </div>

  <!-- Top Row -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto items-start">
    <div class="text-center opacity-0 translate-y-6 duration-700 flex flex-col items-center" data-animate>
      <div class="w-28 h-28 rounded-full mb-3 shadow-md overflow-hidden">
        <img src="images/sirfred.png" alt="Person 1" class="w-full h-full object-cover">
      </div>
      <h3 class="text-lg font-bold text-black">Freddie Rick O. Calos</h3>
      <p class="text-gray-600 text-sm leading-relaxed">RSW<br>OIC - Municipal Social Welfare and Development Office</p>
    </div>
    <div class="text-center opacity-0 translate-y-6 duration-700 flex flex-col items-center" data-animate>
      <div class="w-28 h-28 rounded-full mb-3 shadow-md overflow-hidden">
        <img src="images/christine.png" alt="Person 2" class="w-full h-full object-cover scale-150 object-[center_20%]">
      </div>
      <h3 class="text-lg font-bold text-black">Christine Joy Cuarteron</h3>
      <p class="text-gray-600 text-sm leading-relaxed">Social Welfare Officer I</p>
    </div>
  </div>

  <!-- Bottom Row -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-3xl mx-auto items-start mt-6">
    <div class="text-center opacity-0 translate-y-6 duration-700 flex flex-col items-center" data-animate>
      <div class="w-28 h-28 rounded-full mb-3 shadow-md overflow-hidden">
        <img src="images/mambhing.jpg" alt="Person 2" class="w-full h-full object-cover">
      </div>
      <h3 class="text-lg font-bold text-black">Elizabeth G. Villarama</h3>
      <p class="text-gray-600 text-sm leading-relaxed">Child Protection Specialist</p>
    </div>

    <div class="text-center opacity-0 translate-y-6 duration-700 flex flex-col items-center" data-animate>
      <div class="w-28 h-28 rounded-full mb-3 shadow-md overflow-hidden">
        <img src="images/sirkelvin.jpg" alt="Person 3" class="w-full h-full object-cover">
      </div>
      <h3 class="text-lg font-bold text-black">Kelvin O. Toledo</h3>
      <p class="text-gray-600 text-sm leading-relaxed">Admin Aid / Mental Health Provider</p>
    </div>
  </div>
</div>


    </div>
</section>




<!--how to get help-->
<hr class="my-12 border-t-1 border-[#C2BFBC] w-full lg:w-[70%] mx-auto">
<section id="help" class="py-16">
  <div class="max-w-6xl mx-auto px-6 text-center">
    <!-- Section Title -->
    <h2 class="text-4xl sm:text-5xl font-bold text-orange-600 mb-10 opacity-0 translate-y-4 transition-all duration-700" data-animate>
      How to Get Help?
    </h2>
    <!-- Intro Paragraph -->
    <p class="text-gray-600 max-w-2xl mx-auto mb-12 opacity-0 translate-y-4 transition-all duration-700" data-animate>
      If you or someone you know is experiencing abuse, 
      it can feel overwhelming and difficult to know where to turn.
      You are not alone—support and protection are available. To make
      the process clearer, here are the steps you can follow to get help:
    </p>
    <!-- Steps Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-20">
      <!-- Step 1 -->
      <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition duration-500 opacity-0 translate-y-4" data-animate>
        <div class="w-12 h-12 flex items-center justify-center bg-purple-500 text-white rounded-full mx-auto mb-4">
          <span class="font-bold text-lg">1</span>
        </div>
        <h3 class="text-lg font-bold mb-2 text-purple-700">Recognize the Abuse</h3>
        <p class="text-gray-600 text-sm">
          If you or someone you know is experiencing physical, sexual, verbal, or psychological abuse—whether at home, school, work, or online—it’s important to know that help is available.
        </p>
      </div>
      <!-- Step 2 -->
      <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition duration-500 opacity-0 translate-y-4" data-animate>
        <div class="w-12 h-12 flex items-center justify-center bg-purple-500 text-white rounded-full mx-auto mb-4">
          <span class="font-bold text-lg">2</span>
        </div>
        <h3 class="text-lg font-bold mb-2 text-purple-700">Reach Out for Support</h3>
        <p class="text-gray-600 text-sm text-left">
          Call or message any of the following:<br>
          • WCPU Silang Hotline<br>
          • Barangay SWD Desk<br>
          • PNP Women and Children Protection Desk<br>
          • DSWD Crisis Line<br><br>
          You can also visit your nearest help center in person.
        </p>
      </div>
      <!-- Step 3 -->
      <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition duration-500 opacity-0 translate-y-4" data-animate>
        <div class="w-12 h-12 flex items-center justify-center bg-purple-500 text-white rounded-full mx-auto mb-4">
          <span class="font-bold text-lg">3</span>
        </div>
        <h3 class="text-lg font-bold mb-2 text-purple-700">Visit the WCPU or Referral Center</h3>
        <p class="text-gray-600 text-sm">
          Go to the WCPU-Silang or a nearby hospital, police station, or barangay hall with a women’s desk. Bring a trusted adult or companion if needed. Emergency cases are prioritized.
        </p>
      </div>
      <!-- Step 4 -->
      <div class="p-6 bg-white rounded-xl shadow-md hover:shadow-xl hover:scale-105 transition duration-500 opacity-0 translate-y-4" data-animate>
        <div class="w-12 h-12 flex items-center justify-center bg-purple-500 text-white rounded-full mx-auto mb-4">
          <span class="font-bold text-lg">4</span>
        </div>
        <h3 class="text-lg font-bold mb-2 text-purple-700">Receive Services and Assistance</h3>
        <p class="text-gray-600 text-sm text-left">
          You’ll receive:<br>
          • Medical examination and treatment (if needed)<br>
          • Interview and case documentation<br>
          • Counseling and legal referrals<br>
          • Coordination with other agencies like DSWD or the police
        </p>
      </div>
    </div>
    <!-- Motivational Paragraph -->
    <p class="text-gray-600 max-w-2xl mx-auto mb-12 opacity-0 translate-y-4 transition-all duration-700" data-animate>
      Remember, speaking up is not a sign of weakness—it’s an act of courage. By reporting abuse, you are not only protecting yourself but also helping to stop the cycle of violence. 
      <b>You deserve safety, respect, and peace of mind.</b> Don’t be afraid to take the first step—your voice matters, and there are people ready to stand with you.
    </p>
  </div>
</section>




<!-- Scroll-triggered Animation Script -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  // Animate on scroll only once
  const animatedElements = document.querySelectorAll('[data-animate]');
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add("opacity-100", "translate-y-0");
        entry.target.classList.remove("opacity-0", "translate-y-4");
        // Stop observing once animated
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.2 });
  
  animatedElements.forEach(el => observer.observe(el));

  // Accordion logic
  const buttons = document.querySelectorAll(".accordion-btn");
  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      const content = btn.nextElementSibling;
      const isOpen = content.style.maxHeight;

      // Close all others
      document.querySelectorAll(".accordion-content").forEach(c => {
        c.style.maxHeight = null;
        c.previousElementSibling.querySelector("svg").classList.remove("rotate-180");
      });

      // Open this one if not already open
      if (!isOpen) {
        content.style.maxHeight = content.scrollHeight + "px";
        btn.querySelector("svg").classList.add("rotate-180");
      }
    });
  });

  // Auto Carousel Script
  const carousel = document.querySelector('.carousel-images');
  const dots = document.querySelectorAll('.dot');
  const totalSlides = carousel?.children.length || 0;
  let index = 0;

  function updateCarousel() {
    if (!carousel) return;
    carousel.style.transform = `translateX(-${index * 100}%)`;
    dots.forEach((dot, i) => {
      dot.classList.toggle('bg-purple-600', i === index);
      dot.classList.toggle('bg-gray-300', i !== index);
    });
  }

  if (carousel) {
    setInterval(() => {
      index = (index + 1) % totalSlides;
      updateCarousel();
    }, 4000);
  }

  // Smooth Scroll
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        e.preventDefault();
        window.scrollTo({
          top: target.offsetTop - 80,
          behavior: 'smooth'
        });
      }
    });
  });
});

// Map initialization - inline version
(function() {
    function initMap() {
        if (typeof L === 'undefined') {
            console.log('Waiting for Leaflet to load...');
            setTimeout(initMap, 100);
            return;
        }

        const mapContainer = document.getElementById('mapContainer');
        if (!mapContainer) return;

        const map = L.map(mapContainer).setView([14.2247, 120.9768], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        const locations = {
            hospital: [
                { name: 'Silang Rural Health Unit', coords: [14.1850, 121.0150], address: 'Silang, Cavite', phone: '(046) 414-0202' },
                { name: 'Silang Municipal Health Office', coords: [14.1820, 121.0200], address: 'Brgy. Nulong, Silang, Cavite', phone: '(046) 414-0203' },
                { name: 'Silang District Hospital', coords: [14.1900, 121.0100], address: 'Silang, Cavite', phone: '(046) 414-0204' },
                { name: 'Velazco Hospital', coords: [14.23449, 120.9767], address: 'J.P. Rizal St, Silang, Cavite', phone: 'N/A' },
                { name: 'Estrella Hospital', coords: [14.2247, 120.96867], address: 'Aguinaldo Highway, Silang, Cavite', phone: 'N/A' }
            ],
            police: [{ name: 'Silang Municipal Police Station', coords: [14.22343, 120.97436], address: 'Silang, 4118 Silang, Philippines', phone: '+639982961668' }],
            barangay: [
                { name: 'Barangay Acacia', coords: [14.2776, 120.9918], address: 'Acacia, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Adlas', coords: [14.2568, 120.9655], address: 'Adlas, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Anahaw I', coords: [14.2721, 120.9942], address: 'Anahaw I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Balite I', coords: [14.1995, 120.9815], address: 'Balite I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Balite II', coords: [14.1829, 120.9742], address: 'Balite II, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Balubad', coords: [14.1925, 120.9486], address: 'Balubad, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Banaba', coords: [14.2831, 120.9942], address: 'Banaba, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Batas', coords: [14.2347, 120.9429], address: 'Batas, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Biga I', coords: [14.2487, 120.9757], address: 'Biga I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Biga II', coords: [14.2593, 120.9708], address: 'Biga II, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Biluso', coords: [14.2414, 120.9569], address: 'Biluso, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Bucal', coords: [14.1429, 120.9725], address: 'Bucal, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Buho', coords: [14.1404, 120.9556], address: 'Buho, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Bulihan', coords: [14.2800, 120.9969], address: 'Bulihan, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Cabangaan', coords: [14.1608, 121.0072], address: 'Cabangaan, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Carmen', coords: [14.2266, 121.0357], address: 'Carmen, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Hoyo', coords: [14.1959, 121.0078], address: 'Hoyo, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Hukay', coords: [14.2169, 121.0152], address: 'Hukay, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Iba', coords: [14.2179, 120.9770], address: 'Iba, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Inchican', coords: [14.2518, 121.0529], address: 'Inchican, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Ipil I', coords: [14.2743, 120.9951], address: 'Ipil I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Ipil II', coords: [14.2722, 120.9993], address: 'Ipil II, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Kalubkob', coords: [14.2086, 120.9525], address: 'Kalubkob, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Kaong', coords: [14.2446, 120.9973], address: 'Kaong, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Lalaan I', coords: [14.1875, 120.9618], address: 'Lalaan I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Lalaan II', coords: [14.1578, 120.9579], address: 'Lalaan II, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Litlit', coords: [14.2051, 120.9432], address: 'Litlit, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Lucsuhin', coords: [14.2112, 120.9607], address: 'Lucsuhin, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Lumil', coords: [14.1806, 121.0090], address: 'Lumil, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Maguyam', coords: [14.2638, 121.0064], address: 'Maguyam, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Malabag', coords: [14.1416, 120.9649], address: 'Malabag, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Malaking Tatyao', coords: [14.2247, 120.9873], address: 'Malaking Tatyao, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Mataas na Burol', coords: [14.1662, 120.9714], address: 'Mataas na Burol, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Munting Ilog', coords: [14.2190, 120.9956], address: 'Munting Ilog, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Narra I', coords: [14.2788, 120.9946], address: 'Narra I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Narra II', coords: [14.2757, 120.9933], address: 'Narra II, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Narra III', coords: [14.2780, 120.9929], address: 'Narra III, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Paligawan', coords: [14.1771, 120.9799], address: 'Paligawan, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Pasong Langka', coords: [14.1589, 120.9961], address: 'Pasong Langka, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Pooc I', coords: [14.1848, 120.9954], address: 'Pooc I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Pooc II', coords: [14.2012, 120.9909], address: 'Pooc II, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Poblacion I', coords: [14.2253, 120.9768], address: 'Poblacion I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Poblacion II', coords: [14.2235, 120.9753], address: 'Poblacion II, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Poblacion III', coords: [14.2219, 120.9768], address: 'Poblacion III, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Poblacion IV', coords: [14.2239, 120.9725], address: 'Poblacion IV, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Poblacion V', coords: [14.2224, 120.9735], address: 'Poblacion V, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Pulong Bunga', coords: [14.1733, 120.9846], address: 'Pulong Bunga, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Pulong Saging', coords: [14.1898, 120.9821], address: 'Pulong Saging, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Puting Kahoy', coords: [14.2143, 121.0310], address: 'Puting Kahoy, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Sabutan', coords: [14.2299, 120.9747], address: 'Sabutan, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay San Miguel I', coords: [14.2241, 120.9719], address: 'San Miguel I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay San Vicente I', coords: [14.2266, 120.9743], address: 'San Vicente I, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay San Vicente II', coords: [14.2281, 120.9701], address: 'San Vicente II, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Santol', coords: [14.1693, 120.9528], address: 'Santol, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Tartaria', coords: [14.2034, 121.0232], address: 'Tartaria, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Tibig', coords: [14.2271, 121.0009], address: 'Tibig, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Ulat', coords: [14.1506, 120.9886], address: 'Ulat, Silang, Cavite', phone: 'N/A' },
                { name: 'Barangay Yakal', coords: [14.2617, 120.9952], address: 'Yakal, Silang, Cavite', phone: 'N/A' }
            ],
            orphanage: [
                { name: 'LifeChild Asia Foundation', coords: [14.2169, 121.0152], address: 'Hukay Road, Silang, Cavite', phone: 'N/A' },
                { name: 'Casa dei Bambini Home for Children', coords: [14.1875, 120.9618], address: '181 Aguinaldo Highway, Lalaan 1, Silang', phone: 'N/A' },
                { name: 'Chosen Children Village Foundation, Inc.', coords: [14.1578, 120.9579], address: 'KM. 49 Emilio Aguinaldo Highway, Lalaan 2nd, Silang', phone: 'N/A' }
            ],
            municipal: [{ name: 'Silang Municipal Hall', coords: [14.23888, 120.978], address: 'J.P. Rizal St., Biga I, Silang, Cavite', phone: 'N/A' }]
        };

        const markers = {};
        for (const category in locations) {
            markers[category] = locations[category].map(loc => {
                const marker = L.marker(loc.coords);
                marker.bindPopup(`<strong>${loc.name}</strong><br>📍 ${loc.address}<br>📞 ${loc.phone}`);
                return marker;
            });
        }

        const serviceSelect = document.getElementById('serviceSelect');
        if (serviceSelect) {
            serviceSelect.addEventListener('change', function() {
                console.log('Selected:', this.value);
                
                // Remove all markers from map
                for (const category in markers) {
                    markers[category].forEach(m => map.removeLayer(m));
                }
                
                // Add selected category markers (no zoom)
                if (this.value !== 'none' && markers[this.value]) {
                    console.log('Adding markers:', markers[this.value].length);
                    markers[this.value].forEach(m => m.addTo(map));
                }
            });
        }

        console.log('Map initialized successfully!');

        console.log('Map initialized successfully!');
    }

    initMap();
})();
</script>
@endsection






<!-- Chatbot Floating Chat Panel -->
{{-- <div x-data="{ open: false, showIcon: true }" class="fixed bottom-6 right-6 z-50" x-cloak>
    <template x-if="showIcon">
        <button
            @click="open = true; showIcon = false"
            class="bg-orange-500 hover:bg-orange-400 text-white rounded-full p-4 shadow-lg focus:outline-none transition"
            aria-label="Open Chat">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" />
            </svg>
        </button>
    </template>
    <!-- Chat Panel -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        @click.away="open = false; $nextTick(() => setTimeout(() => showIcon = true, 300))"
        class="mt-4 w-80 bg-white rounded-lg shadow-2xl overflow-hidden text-black">
        <!-- Header -->
        <div class="bg-orange-500 text-white px-4 py-3 text-sm font-semibold flex justify-between items-center">
            <span>WCPU Chat Bot</span>
            <button
                @click="open = false; $nextTick(() => setTimeout(() => showIcon = true, 300))"
                class="text-white hover:text-gray-200 text-lg leading-none transition">
                &times;
            </button>
        </div>
        <!-- Message Display -->
        <div class="p-4 h-64 overflow-y-auto text-sm space-y-2 bg-orange-50">
            <div class="bg-orange-200 text-orange-900 p-2 rounded-md w-max">
                👋 Hello! How can I help you today?
            </div>
        </div>
        <!-- Message Input -->
        <div class="border-t px-4 py-3 flex items-center gap-2 bg-white">
            <input
                type="text"
                placeholder="Type a message..."
                class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none"/>
            <button class="transition transform hover:scale-110">
                <img src="{{ asset('images/sendicon.png') }}" alt="Send" class="w-6 h-6 filter-orange">
            </button>
        </div>
    </div>
</div> --}}
