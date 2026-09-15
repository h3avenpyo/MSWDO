/**
 * ==========================================================================
 * MSWDO Silang - Welcome Page Scripts
 * ==========================================================================
 */

// Helper to fetch CSRF token from meta tag
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

// =====================================
// AUTOMATIC PROGRAMS CAROUSEL
// =====================================
function initProgramsCarousel() {
    const carousel = document.getElementById('simpleCarousel');
    if (!carousel) return;

    const slides = carousel.querySelectorAll('.carousel-slide');
    const dots = carousel.querySelectorAll('.carousel-dot');
    const total = slides.length;
    let current = 0;
    let timer = null;

    function showSlide(index) {
        if (index < 0) index = total - 1;
        if (index >= total) index = 0;
        current = index;

        slides.forEach((slide, i) => {
            if (i === current) {
                slide.style.opacity = '1';
                slide.style.zIndex = '10';
                slide.style.pointerEvents = 'auto';
            } else {
                slide.style.opacity = '0';
                slide.style.zIndex = '1';
                slide.style.pointerEvents = 'none';
            }
        });

        dots.forEach((dot, i) => {
            if (i === current) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    function nextSlide() {
        showSlide(current + 1);
    }

    function prevSlide() {
        showSlide(current - 1);
    }

    function runTimer() {
        if (timer) clearInterval(timer);
        timer = setInterval(nextSlide, 3000);
    }

    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function (e) {
            e.preventDefault();
            showSlide(i);
            runTimer();
        });
    });

    // Touch swipe for mobile/tablet devices
    let startX = 0;
    let startY = 0;
    let isSwiping = false;

    carousel.addEventListener('touchstart', function (e) {
        if (e.touches.length === 1) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isSwiping = true;
        }
    }, { passive: true });

    carousel.addEventListener('touchend', function (e) {
        if (!isSwiping || e.changedTouches.length === 0) return;
        isSwiping = false;
        const endX = e.changedTouches[0].clientX;
        const endY = e.changedTouches[0].clientY;
        const diffX = startX - endX;
        const diffY = startY - endY;

        // Ensure gesture is a clear horizontal swipe, not vertical page scrolling
        if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 35) {
            if (diffX > 0) {
                nextSlide();
            } else {
                prevSlide();
            }
            runTimer();
        }
    }, { passive: true });

    // Start automatic rotation immediately
    runTimer();
}

// =====================================
// SERVICE REQUEST MODAL & FILE UPLOADS
// =====================================
let selectedFiles = [];

function openServiceRequestModal() {
    selectedFiles = [];
    const csrfToken = getCsrfToken();

    // Step 1: Who needs assistance + Beneficiary Information
    Swal.fire({
        title: 'Online Service Request',
        html: `
            <input type="hidden" name="_token" value="${csrfToken}">
            <div style="text-align: left; padding: 10px;">
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Who needs assistance?</h3>
                    <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">Who is this request for?</label>
                    <select id="requestFor" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                        <option value="">Select an option</option>
                        <option value="myself">Myself</option>
                        <option value="child">My child</option>
                        <option value="parent">My parent</option>
                        <option value="family">Another family member</option>
                        <option value="assisting">Someone I am assisting</option>
                    </select>
                </div>
                <div>
                    <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 15px;">Beneficiary Information</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div>
                            <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">First name</label>
                            <input type="text" id="firstName" placeholder="Enter first name" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                        </div>
                        <div>
                            <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Last name</label>
                            <input type="text" id="lastName" placeholder="Enter last name" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                        </div>
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Date of birth</label>
                        <input type="date" id="dob" placeholder="Select date of birth" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Barangay</label>
                        <select id="barangay" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                            <option value="">Select barangay</option>
                            <option value="ACACIA">Acacia</option>
                            <option value="ADLAS">Adlas</option>
                            <option value="ANAHAW 1">Anahaw I</option>
                            <option value="ANAHAW 2">Anahaw 2</option>
                            <option value="BALITE I">Balite I</option>
                            <option value="BALITE II">Balite II</option>
                            <option value="BALUBAD">Balubad</option>
                            <option value="BANABA">Banaba</option>
                            <option value="BATAS">Batas</option>
                            <option value="BIGA 1">Biga 1</option>
                            <option value="BIGA 2">Biga 2</option>
                            <option value="BILUSO">Biluso</option>
                            <option value="BUCAL">Bucal</option>
                            <option value="BUHO">Buho</option>
                            <option value="BULIHAN">Bulihan</option>
                            <option value="CABANGAAN">Cabangaan</option>
                            <option value="CARMEN">Carmen</option>
                            <option value="HOYO">Hoyo</option>
                            <option value="HUKAY">Hukay</option>
                            <option value="IBA">Iba</option>
                            <option value="INCHICAN">Inchican</option>
                            <option value="IPIL 1">Ipil I</option>
                            <option value="IPIL 2">Ipil 2</option>
                            <option value="KALUBKOB">Kalubkob</option>
                            <option value="KAONG">Kaong</option>
                            <option value="LALAAN I">Lalaan I</option>
                            <option value="LALAAN II">Lalaan II</option>
                            <option value="LITLIT">Litlit</option>
                            <option value="LUCSUHIN">Lucsuhin</option>
                            <option value="LUMIL">Lumil</option>
                            <option value="MAGUYAM">Maguyam</option>
                            <option value="MALABAG">Malabag</option>
                            <option value="MALAKING TATIAO">Malaking Tatiao</option>
                            <option value="MATAAS NA BUROL">Mataas na Burol</option>
                            <option value="MUNTING ILOG">Munting Ilog</option>
                            <option value="NARRA I">Narra I</option>
                            <option value="NARRA II">Narra II</option>
                            <option value="NARRA III">Narra III</option>
                            <option value="PALIGAWAN">Paligawan</option>
                            <option value="PASONG LANGKA">Pasong Langka</option>
                            <option value="POBLACION 1">Poblacion 1</option>
                            <option value="POBLACION 2">Poblacion 2</option>
                            <option value="POBLACION 3">Poblacion 3</option>
                            <option value="POBLACION 4">Poblacion 4</option>
                            <option value="POBLACION 5">Poblacion 5</option>
                            <option value="POOC I">Pooc I</option>
                            <option value="POOC II">Pooc II</option>
                            <option value="PULONG BUNGA">Pulong Bunga</option>
                            <option value="PULONG SAGING">Pulong Saging</option>
                            <option value="PUTING KAHOY">Putting Kahoy</option>
                            <option value="SABUTAN">Sabutan</option>
                            <option value="SAN MIGUEL I">San Miguel I</option>
                            <option value="SAN MIGUEL II">San Miguel II</option>
                            <option value="SAN VICENTE I">San Vicente I</option>
                            <option value="SAN VICENTE II">San Vicente II</option>
                            <option value="SANTOL">Santol</option>
                            <option value="TARTARIA">Tartaria</option>
                            <option value="TIBIG">Tibig</option>
                            <option value="TOLEDO">Toledo</option>
                            <option value="TUBUAN 1">Tubuan 1</option>
                            <option value="TUBUAN 2">Tubuan 2</option>
                            <option value="TUBUAN 3">Tubuan 3</option>
                            <option value="ULAT">Ulat</option>
                            <option value="YAKAL">Yakal</option>
                        </select>
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Contact number</label>
                        <input type="text" id="contactNumber" placeholder="Enter contact number" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Email address</label>
                        <input type="email" id="email" placeholder="Enter email address" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                    </div>
                    <div style="margin-top: 12px;">
                        <label style="color: #64748B; font-size: 13px; font-weight: 500; display: block; margin-bottom: 6px;">Address</label>
                        <input type="text" id="address" placeholder="Enter address" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                    </div>
                </div>
            </div>
        `,
        width: '600px',
        confirmButtonText: 'Next',
        confirmButtonColor: '#1A237E',
        showCancelButton: true,
        cancelButtonText: 'Cancel',
        cancelButtonColor: '#64748B',
        customClass: {
            popup: 'service-request-modal'
        },
        preConfirm: () => {
            const requestFor = Swal.getPopup().querySelector('#requestFor').value;
            const firstName = Swal.getPopup().querySelector('#firstName').value;
            const lastName = Swal.getPopup().querySelector('#lastName').value;
            const dob = Swal.getPopup().querySelector('#dob').value;
            const barangay = Swal.getPopup().querySelector('#barangay').value;
            const contactNumber = Swal.getPopup().querySelector('#contactNumber').value;
            const email = Swal.getPopup().querySelector('#email').value;
            const address = Swal.getPopup().querySelector('#address').value;

            if (!requestFor) {
                Swal.showValidationMessage('Please select who this request is for');
                return false;
            }
            if (!firstName || !lastName) {
                Swal.showValidationMessage('Please enter both first and last name');
                return false;
            }
            if (!dob) {
                Swal.showValidationMessage('Please enter date of birth');
                return false;
            }
            if (!barangay) {
                Swal.showValidationMessage('Please enter barangay');
                return false;
            }
            if (!contactNumber) {
                Swal.showValidationMessage('Please enter contact number');
                return false;
            }
            if (!email || email.trim() === '') {
                Swal.showValidationMessage('Please enter email address');
                return false;
            }

            return { 
                request_for: requestFor, 
                first_name: firstName, 
                last_name: lastName, 
                dob: dob, 
                barangay: barangay, 
                contact_number: contactNumber, 
                email: email, 
                address: address 
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Step 2: Service Details
            Swal.fire({
                title: 'Service Request Details',
                html: `
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <div style="text-align: left; padding: 10px;">
                        <div style="margin-bottom: 25px;">
                            <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Type of Service</h3>
                            <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">What type of service do you need?</label>
                            <select id="serviceType" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                                <option value="">Select service type</option>
                                <option value="financial_assistance">Financial Assistance</option>
                                <option value="social_case_study">Social Case Study</option>
                                <option value="senior_citizen">Senior Citizen Services</option>
                                <option value="vawc">VAWC Services</option>
                                <option value="bcpc">BCPC Services</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        <div style="margin-bottom: 25px;">
                            <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Assistance Type</h3>
                            <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">What type of assistance do you need?</label>
                            <select id="assistanceType" style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC;">
                                <option value="">Select assistance type</option>
                                <option value="medical">Medical Assistance</option>
                                <option value="educational">Educational Assistance</option>
                                <option value="food">Food Assistance</option>
                                <option value="transportation">Transportation Assistance</option>
                                <option value="burial">Burial Assistance</option>
                                <option value="livelihood">Livelihood Assistance</option>
                                <option value="emergency">Emergency Assistance</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                        <div>
                            <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Situation Description</h3>
                            <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">Please provide a brief description of the client's situation</label>
                            <textarea id="situation" rows="4" placeholder="Describe the situation..." style="width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 8px; font-size: 14px; background: #F8FAFC; resize: vertical;"></textarea>
                        </div>
                        <div style="margin-top: 25px;">
                            <h3 style="color: #1A237E; font-size: 18px; font-weight: 700; margin-bottom: 10px;">Upload Documents</h3>
                            <label style="color: #64748B; font-size: 14px; font-weight: 500; display: block; margin-bottom: 8px;">Upload any supporting documents (optional)</label>
                            <div id="uploadArea" style="border: 2px dashed #1A237E; border-radius: 8px; padding: 20px; text-align: center; background: #F8FAFC; cursor: pointer; transition: all 0.3s ease;">
                                <div style="margin-bottom: 10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="#1A237E" style="width: 40px; height: 40px; margin: 0 auto;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                    </svg>
                                </div>
                                <p style="color: #1A237E; font-size: 16px; font-weight: 600; margin: 0 0 6px 0;">Click to upload files</p>
                                <p style="color: #64748B; font-size: 13px; margin: 0;">or drag and drop files here</p>
                                <p style="color: #64748B; font-size: 12px; margin-top: 8px;">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG</p>
                                <p style="color: #64748B; font-size: 12px;">Maximum file size: 10MB per file</p>
                            </div>
                            <input type="file" id="documents" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" style="display: none;">
                            <div id="fileList" style="margin-top: 12px;"></div>
                        </div>
                    </div>
                `,
                width: '600px',
                confirmButtonText: 'Submit Request',
                confirmButtonColor: '#1A237E',
                showCancelButton: true,
                cancelButtonText: 'Back',
                cancelButtonColor: '#64748B',
                customClass: {
                    popup: 'service-request-modal'
                },
                didOpen: () => {
                    // Initialize drag and drop after modal opens
                    setTimeout(() => {
                        const uploadArea = document.getElementById('uploadArea');
                        const documentsInput = document.getElementById('documents');
                        
                        if (uploadArea && documentsInput) {
                            // Click handler
                            uploadArea.addEventListener('click', () => {
                                documentsInput.click();
                            });
                            
                            // Change handler
                            documentsInput.addEventListener('change', () => {
                                handleFileSelection(documentsInput);
                            });
                            
                            // Drag and drop handlers
                            uploadArea.addEventListener('dragover', (e) => {
                                e.preventDefault();
                                uploadArea.style.background = '#EEF2FF';
                                uploadArea.style.borderColor = '#1A237E';
                            });
                            
                            uploadArea.addEventListener('dragleave', (e) => {
                                e.preventDefault();
                                uploadArea.style.background = '#F8FAFC';
                                uploadArea.style.borderColor = '#1A237E';
                            });
                            
                            uploadArea.addEventListener('drop', (e) => {
                                e.preventDefault();
                                uploadArea.style.background = '#F8FAFC';
                                uploadArea.style.borderColor = '#1A237E';
                                
                                addFilesToStore(e.dataTransfer.files);
                                syncFileInput(documentsInput);
                            });
                        }
                    }, 100);
                },
                preConfirm: () => {
                    const serviceType = Swal.getPopup().querySelector('#serviceType').value;
                    const assistanceType = Swal.getPopup().querySelector('#assistanceType').value;
                    const situation = Swal.getPopup().querySelector('#situation').value;

                    if (!serviceType) {
                        Swal.showValidationMessage('Please select a service type');
                        return false;
                    }
                    if (!assistanceType) {
                        Swal.showValidationMessage('Please select an assistance type');
                        return false;
                    }
                    if (!situation) {
                        Swal.showValidationMessage('Please provide a brief description of the situation');
                        return false;
                    }

                    return { 
                        service_type: serviceType, 
                        assistance_type: assistanceType, 
                        situation: situation,
                        files: selectedFiles.slice()
                    };
                }
            }).then((result2) => {
                if (result2.isConfirmed) {
                    // Combine both steps data
                    const formData = new FormData();
                    Object.keys(result.value).forEach(key => {
                        formData.append(key, result.value[key]);
                    });
                    const { files: requestFiles, ...requestData } = result2.value;
                    Object.keys(requestData).forEach(key => {
                        formData.append(key, requestData[key]);
                    });
                    
                    // Add selected files (captured in preConfirm before the modal closed)
                    if (requestFiles && requestFiles.length > 0) {
                        for (let i = 0; i < requestFiles.length; i++) {
                            formData.append('documents[]', requestFiles[i]);
                        }
                    }

                    // Send data to backend
                    fetch('/service-request', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Request Submitted',
                                text: 'Your service request has been submitted successfully. An MSWDO officer will review your request.',
                                icon: 'success',
                                confirmButtonColor: '#1A237E',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message || 'There was an error submitting your request. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#DC2626',
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'There was an error submitting your request. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#DC2626',
                            confirmButtonText: 'OK'
                        });
                    });
                } else if (result2.dismiss === Swal.DismissReason.cancel) {
                    openServiceRequestModal();
                }
            });
        }
    });
}

function handleFileSelection(input) {
    addFilesToStore(input.files);
    syncFileInput(input);
}

function addFilesToStore(newFiles) {
    const validTypes = ['.pdf', '.doc', '.docx', '.jpg', '.jpeg', '.png'];
    for (let i = 0; i < newFiles.length; i++) {
        const file = newFiles[i];
        const extension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!validTypes.includes(extension)) {
            Swal.fire({
                title: 'Invalid File Type',
                text: file.name + ' is not a supported file type.',
                icon: 'warning',
                confirmButtonColor: '#1A237E',
                confirmButtonText: 'OK'
            });
            continue;
        }
        
        const duplicate = selectedFiles.some(existing =>
            existing.name === file.name &&
            existing.size === file.size &&
            existing.lastModified === file.lastModified
        );
        
        if (!duplicate) {
            selectedFiles.push(file);
        }
    }
}

function syncFileInput(input) {
    const dataTransfer = new DataTransfer();
    for (let i = 0; i < selectedFiles.length; i++) {
        dataTransfer.items.add(selectedFiles[i]);
    }
    input.files = dataTransfer.files;
    updateFileList(input);
}

function updateFileList(input) {
    const fileList = document.getElementById('fileList');
    if (!fileList) return;
    fileList.innerHTML = '';
    
    if (input.files.length > 0) {
        const fileListHtml = document.createElement('div');
        fileListHtml.style.cssText = 'background: #F8FAFC; border-radius: 8px; padding: 12px; border: 1px solid #E2E8F0;';
        
        const title = document.createElement('h4');
        title.textContent = 'Selected Files (' + input.files.length + ')';
        title.style.cssText = 'margin: 0 0 10px 0; color: #1A237E; font-size: 14px; font-weight: 700;';
        fileListHtml.appendChild(title);
        
        const list = document.createElement('ul');
        list.style.cssText = 'margin: 0; padding-left: 20px;';
        
        for (let i = 0; i < input.files.length; i++) {
            const file = input.files[i];
            const listItem = document.createElement('li');
            listItem.style.cssText = 'margin-bottom: 6px; color: #1F2937; font-size: 13px;';
            listItem.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
            list.appendChild(listItem);
        }
        
        fileListHtml.appendChild(list);
        fileList.appendChild(fileListHtml);
    }
}

function formatFileSize(bytes) {
    if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

// =====================================
// WELCOME PAGE MAIN INITIALIZATION
// =====================================
function initWelcomePage() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Initialize Programs Carousel
    initProgramsCarousel();

    // Mobile Menu Toggle
    const menuButton = document.getElementById('menuButton');
    const mobileMenu = document.getElementById('mobileMenu');
    if (menuButton && mobileMenu) {
menuButton.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.contains('show');
        mobileMenu.classList.toggle('show');
        document.body.classList.toggle('mobile-menu-open', !isOpen);
    });
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.remove('show');
            document.body.classList.remove('mobile-menu-open');
        });
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            mobileMenu.classList.remove('show');
            document.body.classList.remove('mobile-menu-open');
        }
    });
    }

    // Scroll To Top Button
    const scrollBtn = document.getElementById('scrollTop');
    if (scrollBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                scrollBtn.classList.remove('hidden');
            } else {
                scrollBtn.classList.add('hidden');
            }
        });
        scrollBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // Show All Emergency Hotlines button
    const showAllHotlinesBtn = document.getElementById('showAllHotlines');
    if (showAllHotlinesBtn) {
        showAllHotlinesBtn.addEventListener('click', () => {
            const hiddenItems = document.querySelectorAll('.hidden-mobile');
            if (hiddenItems.length === 0) return;
            const isHidden = !hiddenItems[0].classList.contains('visible');
            
            hiddenItems.forEach(item => {
                if (isHidden) {
                    item.classList.add('visible');
                } else {
                    item.classList.remove('visible');
                }
            });
            
            showAllHotlinesBtn.textContent = isHidden ? 'Show Less' : 'Show All';
        });
    }
}

// Attach globally accessible functions to window
window.openServiceRequestModal = openServiceRequestModal;
window.handleFileSelection = handleFileSelection;
window.initProgramsCarousel = initProgramsCarousel;
window.initWelcomePage = initWelcomePage;

// Run on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initWelcomePage);
} else {
    initWelcomePage();
}
