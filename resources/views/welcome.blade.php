<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MSWDO Silang</title>

    <meta name="description"
        content="Municipal Social Welfare and Development Office - Municipality of Silang">

    <link rel="preconnect" href="https://fonts.bunny.net">

    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800"
        rel="stylesheet" />

    @vite(['resources/css/app.css','resources/js/app.js'])

</head>

<body class="bg-[#F8FAFC] text-[#1F2937] antialiased">

<!-- ========================= -->
<!-- NAVBAR -->
<!-- ========================= -->

<header class="fixed top-0 z-50 w-full bg-primary bg-opacity-95 backdrop-blur shadow-lg">

<div class="max-w-7xl mx-auto px-6">

<div class="flex items-center justify-between h-20">

<!-- Logo -->

<a href="#" class="flex items-center gap-4">

<div class="h-14 w-14 rounded-full bg-warm-gold p-1">

@php

$logo = null;

if(file_exists(public_path('images/mswdo-logo.png'))){

$logo='mswdo-logo.png';

}else{

$files=glob(public_path('images/*.{png,jpg,jpeg,svg}'),GLOB_BRACE);

if(!empty($files))

$logo=basename($files[0]);

}

@endphp

@if($logo)

<img src="{{ asset('images/'.$logo) }}" class="rounded-full h-full w-full object-cover">

@endif

</div>

<div>

<h1 class="text-white font-bold text-lg">

MSWDO SILANG

</h1>

<p class="text-offwhite text-xs">

Municipal Social Welfare & Development Office

</p>

</div>

</a>

<!-- Desktop Menu -->

<nav class="hidden lg:flex items-center gap-8 text-offwhite">

<a href="#home" class="hover:text-warm-gold transition">Home</a>

<a href="#services" class="hover:text-warm-gold transition">Services</a>

<a href="#about" class="hover:text-warm-gold transition">About</a>

<a href="#programs" class="hover:text-warm-gold transition">Programs</a>

<a href="#contact" class="hover:text-warm-gold transition">Contact</a>

<!-- <a href="#"

class="bg-warm-gold text-[#1F2937] px-5 py-2 rounded-full font-semibold hover:bg-[#f4c243] transition">

Login

</a> -->

</nav>

<button id="menuButton"

class="lg:hidden text-white">

<svg xmlns="http://www.w3.org/2000/svg"

fill="none"

viewBox="0 0 24 24"

stroke-width="1.5"

stroke="currentColor"

class="w-8 h-8">

<path stroke-linecap="round"

stroke-linejoin="round"

d="M3.75 6.75h16.5m-16.5 5.25h16.5m-16.5 5.25h16.5" />

</svg>

</button>

</div>

</div>

<!-- Mobile Menu -->

<div id="mobileMenu"

class="hidden bg-primary text-white lg:hidden">

<div class="flex flex-col p-6 gap-4">

<a href="#home">Home</a>

<a href="#services">Services</a>

<a href="#about">About</a>

<a href="#programs">Programs</a>

<a href="#contact">Contact</a>

<a href="#" class="bg-warm-gold text-[#1F2937] rounded-lg py-2 text-center font-semibold">

Login

</a>

</div>

</div>

</header>

<!-- ========================= -->

<!-- HERO -->

<!-- ========================= -->

<section id="home"

class="relative overflow-hidden bg-gradient-to-r from-[#1A237E] via-[#1A237E] to-[#1A237E] pt-36 pb-24">

<div class="absolute inset-0 opacity-10">

<div class="absolute h-96 w-96 rounded-full bg-warm-gold blur-3xl -top-20 -left-20"></div>

<div class="absolute h-96 w-96 rounded-full bg-offwhite blur-3xl bottom-0 right-0"></div>

</div>

<div class="relative max-w-7xl mx-auto px-6">

<div class="grid lg:grid-cols-2 gap-14 items-center">

<div>

<p class="uppercase tracking-[5px] text-warm-gold">

WELCOME TO

</p>

<h1 class="mt-4 text-6xl font-extrabold text-white leading-tight">

MSWDO

<span class="text-warm-gold">

SILANG

</span>

</h1>

<p class="mt-6 text-offwhite text-lg leading-8">

The Municipal Social Welfare and Development Office is committed to

empowering vulnerable sectors through quality social protection,

community development, and accessible public services.

</p>

<div class="mt-10 flex flex-wrap gap-4">

<a href="#services"

class="bg-warm-gold px-8 py-4 rounded-xl font-bold hover:scale-105 transition">

Explore Services

</a>

<a href="#contact"

class="border border-offwhite text-offwhite px-8 py-4 rounded-xl hover:bg-offwhite hover:text-primary transition">

Contact Us

</a>

</div>

<div class="grid grid-cols-3 gap-8 mt-14">

<div>

<h2 class="text-4xl font-bold text-warm-gold">

10K+

</h2>

<p class="text-offwhite">

Families Assisted

</p>

</div>

<div>

<h2 class="text-4xl font-bold text-warm-gold">

25+

</h2>

<p class="text-offwhite">

Programs

</p>

</div>

<div>

<h2 class="text-4xl font-bold text-warm-gold">

24/7

</h2>

<p class="text-offwhite">

Online Support

</p>

</div>

</div>

</div>

<div class="flex justify-center">

<div class="bg-offwhite bg-opacity-70 backdrop-blur-lg rounded-3xl p-10 shadow-2xl">

@if($logo)

<img src="{{ asset('images/'.$logo) }}"

class="w-80">

@else

<div class="w-80 h-80 rounded-full bg-card-white"></div>

@endif

</div>

</div>

</div>

</div>

</section>

<!-- ========================= -->

<!-- QUICK SERVICES -->

<!-- ========================= -->

<section id="services" class="py-20 bg-offwhite">

<div class="max-w-7xl mx-auto px-6">

<div class="text-center">

<h2 class="text-4xl font-bold">

Our Services

</h2>

<p class="text-secondary mt-3">

Providing quality welfare services for every Silangueño.

</p>

</div>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-14 justify-items-center mx-auto max-w-4xl">

<div class="bg-card-white rounded-2xl shadow-lg p-8 hover:-translate-y-2 transition flex flex-col items-center text-center">

<div class="text-5xl mb-4"> </div>

<h3 class="font-bold text-xl">

Financial Assistance

</h3>

<p class="text-secondary mt-3">

Emergency and medical financial support for qualified residents.

</p>

</div>



<div class="bg-card-white rounded-2xl shadow-lg p-8 hover:-translate-y-2 transition flex flex-col items-center text-center">

<div class="text-5xl mb-4"></div>

<h3 class="font-bold text-xl">

Senior Citizens

</h3>

<p class="text-secondary mt-3">

Programs and benefits dedicated to senior citizens.

</p>

</div>

</div>

</div>

</section>

<!-- ===================================== -->
<!-- ABOUT MSWDO -->
<!-- ===================================== -->

<section id="about" class="py-24 bg-offwhite">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid lg:grid-cols-2 gap-16 items-center">

            <div>

                <span class="text-primary font-semibold uppercase tracking-widest">
                    About Us
                </span>

                <h2 class="text-5xl font-bold mt-4">
                    Serving the People of
                    <span class="text-primary">Silang</span>
                </h2>

                <p class="mt-8 text-lg leading-8 text-secondary">

                    The Municipal Social Welfare and Development Office (MSWDO)
                    is dedicated to uplifting the lives of individuals,
                    families, and communities through responsive social welfare
                    programs and inclusive development initiatives.

                </p>

                <p class="mt-5 text-lg leading-8 text-secondary">

                    We promote social justice, protect vulnerable sectors,
                    and ensure that every citizen receives quality services
                    regardless of age, gender, or social status.

                </p>

            </div>

            <div>

                <div class="grid grid-cols-2 gap-6">

                    <div class="bg-card-white rounded-2xl shadow-lg p-8">

                        <div class="text-5xl mb-4">
                            
                        </div>

                        <h3 class="font-bold text-xl">
                            Compassion
                        </h3>

<p class="text-secondary mt-3">

                            Delivering services with empathy and dignity.

                        </p>

                    </div>

                    <div class="bg-card-white rounded-2xl shadow-lg p-8">

                        <div class="text-5xl mb-4">
                            
                        </div>

                        <h3 class="font-bold text-xl">
                            Integrity
                        </h3>

<p class="text-secondary mt-3">

                            Transparent and accountable public service.

                        </p>

                    </div>

                    <div class="bg-card-white rounded-2xl shadow-lg p-8">

                        <div class="text-5xl mb-4">
                            
                        </div>

                        <h3 class="font-bold text-xl">
                            Development
                        </h3>

<p class="text-secondary mt-3">

                            Empowering individuals to become self-sufficient.

                        </p>

                    </div>

                    <div class="bg-card-white rounded-2xl shadow-lg p-8">

                        <div class="text-5xl mb-4">

                        </div>

                        <h3 class="font-bold text-xl">
                            Public Service
                        </h3>

<p class="text-secondary mt-3">

                            Committed to excellent government service.

                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<!-- ===================================== -->
<!-- MISSION & VISION -->
<!-- ===================================== -->

<section class="py-24 bg-offwhite">

<div class="max-w-7xl mx-auto px-6">

<div class="grid lg:grid-cols-2 gap-10">

<div class="bg-primary text-white rounded-3xl p-12 shadow-xl">

<h2 class="text-4xl font-bold mb-8">

Mission

</h2>

<p class="text-lg leading-8">

To provide efficient, compassionate, and accessible social welfare
services that improve the quality of life of every Silangueño through
people-centered programs, community participation, and sustainable
development.

</p>

</div>

<div class="bg-warm-gold rounded-3xl p-12 shadow-xl">

<h2 class="text-4xl font-bold text-slate-900 mb-8">

Vision

</h2>

<p class="text-lg leading-8 text-slate-800">

A resilient, inclusive, and empowered municipality where every citizen
has equal access to opportunities, protection, and quality social
services.

</p>

</div>

</div>

</div>

</section>

<!-- ===================================== -->
<!-- PROCESS FLOW -->
<!-- ===================================== -->

<section class="py-24 bg-offwhite">

<div class="max-w-7xl mx-auto px-6">

<div class="text-center">

<h2 class="text-4xl font-bold">

How to Apply

</h2>

<p class="text-secondary mt-4">

Simple steps to request assistance.

</p>

</div>

<div class="grid md:grid-cols-5 gap-8 mt-16">

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">

1

</div>

<h3 class="font-bold mt-6">

Submit Request

</h3>

</div>

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">

2

</div>

<h3 class="font-bold mt-6">

Document Review

</h3>

</div>

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">

3

</div>

<h3 class="font-bold mt-6">

Assessment

</h3>

</div>

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-primary text-white flex items-center justify-center text-3xl">

4

</div>

<h3 class="font-bold mt-6">

Approval

</h3>

</div>

<div class="text-center">

<div class="w-20 h-20 mx-auto rounded-full bg-accent text-white flex items-center justify-center text-3xl">

5

</div>

<h3 class="font-bold mt-6">

Release Assistance

</h3>

</div>

</div>

</div>

</section>

<!-- ===================================== -->
<!-- LATEST ANNOUNCEMENTS -->
<!-- ===================================== -->

<section id="programs" class="py-24 bg-offwhite">

<div class="max-w-7xl mx-auto px-6">

<div class="text-center">

<h2 class="text-4xl font-bold">

Latest Announcements

</h2>

<p class="text-secondary mt-4">

Stay updated with MSWDO activities and schedules.

</p>

</div>

<div class="grid lg:grid-cols-3 gap-8 mt-14">

<div class="rounded-2xl shadow-lg overflow-hidden">

<div class="bg-primary h-3"></div>

<div class="p-8">

<span class="text-sm text-primary font-semibold">

June 2026

</span>

<h3 class="text-2xl font-bold mt-3">

Senior Citizen Pension Distribution

</h3>

<p class="text-secondary mt-4">

The payout schedule for qualified senior citizens will begin this month.

</p>

</div>

</div>

<div class="welcome-card rounded-2xl shadow-lg overflow-hidden">

<div class="bg-warm-gold h-3"></div>

<div class="p-8">

<span class="text-sm text-warm-gold font-semibold">

June 2026

</span>

<h3 class="text-2xl font-bold mt-3">

Financial Assistance Applications

</h3>

<p class="text-secondary mt-4">

Qualified residents may now submit their applications online.

</p>

</div>

</div>

<div class="welcome-card rounded-2xl shadow-lg overflow-hidden">

<div class="bg-accent h-3"></div>

<div class="p-8">

<span class="text-sm text-accent font-semibold">

June 2026

</span>

<h3 class="text-2xl font-bold mt-3">

VAWC Awareness Seminar

</h3>

<p class="text-secondary mt-4">

Join our advocacy program promoting safe families and communities.

</p>

</div>

</div>

</div>

</div>

</section>

<!-- ===================================== -->
<!-- CALL TO ACTION -->
<!-- ===================================== -->

<section class="py-20 bg-primary">

<div class="max-w-5xl mx-auto text-center px-6">

<h2 class="text-5xl font-bold text-white">

Need Social Assistance?

</h2>

<p class="text-offwhite mt-6 text-xl">

Our dedicated team is ready to assist you with your concerns.

</p>

<div class="mt-10 flex flex-wrap justify-center gap-6">

<a href="#contact"

class="bg-warm-gold px-8 py-4 rounded-xl font-bold hover:scale-105 transition">

Contact MSWDO

</a>

<a href="#services"

class="border border-offwhite px-8 py-4 rounded-xl text-white hover:bg-offwhite hover:text-primary transition">

View Services

</a>

</div>

</div>

</section>

<!-- ===================================== -->
<!-- CONTACT SECTION -->
<!-- ===================================== -->
<style>
.contact-section {
    padding: 7rem 1.5rem;
    background-color: #F8FAFC; /* Match site off-white */
    border-top: 1px solid #E2E8F0;
}
.contact-container {
    max-width: 76rem;
    margin: 0 auto;
}
.contact-header {
    text-align: center;
    margin-bottom: 5rem;
}
.contact-subtitle {
    color: #B45309; /* Deep amber gold */
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.2em;
    font-size: 0.75rem;
    display: block;
    margin-bottom: 0.75rem;
}
.contact-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1A237E; /* Brand primary blue */
    margin-bottom: 1.25rem;
    letter-spacing: -0.03em;
}
.contact-description {
    color: #475569; /* Slate secondary */
    font-size: 1.0625rem;
    max-width: 36rem;
    margin: 0 auto;
    line-height: 1.7;
}
.contact-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 3rem;
    margin-bottom: 4rem;
}
@media (min-width: 1024px) {
    .contact-grid {
        grid-template-columns: 1fr 1.2fr;
    }
}
.contact-card {
    background: #FFFFFF;
    border-radius: 0.75rem;
    border: 1px solid #E2E8F0;
    padding: 3rem;
    display: flex;
    flex-direction: column;
}
.card-title {
    font-size: 1.375rem;
    font-weight: 800;
    color: #1A237E; /* Brand primary blue */
    margin-bottom: 2.5rem;
    letter-spacing: -0.01em;
}
.info-list {
    display: flex;
    flex-direction: column;
    gap: 2.5rem;
    flex: 1;
}
.info-item {
    display: flex;
    align-items: start;
    gap: 1.25rem;
}
.info-icon {
    width: 1.5rem;
    height: 1.5rem;
    color: #1A237E; /* Brand primary blue */
    margin-top: 0.125rem;
    flex-shrink: 0;
}
.info-content {
    flex: 1;
}
.info-label {
    font-weight: 700;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #B45309; /* Amber gold */
    margin-bottom: 0.375rem;
}
.info-text {
    color: #1F2937;
    font-size: 1.0625rem;
    line-height: 1.5;
}
.contact-form {
    display: flex;
    flex-direction: column;
    gap: 2rem;
    flex: 1;
}
.form-row-2 {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
@media (min-width: 640px) {
    .form-row-2 {
        grid-template-columns: 1fr 1fr;
    }
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.form-field-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #475569;
}
.form-input {
    width: 100%;
    padding: 0.875rem 1rem;
    font-size: 0.95rem;
    border: 1px solid #CBD5E1;
    border-radius: 0.5rem;
    outline: none;
    background-color: #F8FAFC;
    color: #1F2937;
    transition: border-color 0.2s ease, background-color 0.2s ease;
}
.form-input:focus {
    border-color: #1A237E;
    background-color: #FFFFFF;
}
.form-textarea {
    resize: none;
    min-height: 8rem;
}
.submit-button {
    background-color: #1A237E; /* Brand primary blue */
    color: #FFFFFF;
    font-weight: 600;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 1rem 2rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    transition: background-color 0.2s ease;
    margin-top: 1rem;
}
.submit-button:hover {
    background-color: #111827;
}
.submit-button:active {
    transform: translateY(1px);
}
.button-icon {
    width: 1.1rem;
    height: 1.1rem;
}

/* Emergency Card - High Contrast Brand Navy & Gold */
.emergency-card {
    background-color: #1A237E; /* Brand primary blue */
    border-radius: 0.75rem;
    padding: 3rem;
    color: #FFFFFF;
    border: 1px solid rgba(255, 255, 255, 0.1);
}
.emergency-header {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 2.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    padding-bottom: 1.5rem;
}
@media (min-width: 640px) {
    .emergency-header {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
    }
}
.emergency-title-wrapper {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}
.emergency-badge {
    border: 1px solid #FBC02D; /* Brand warm gold */
    color: #FBC02D;
    font-weight: 700;
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.25rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    align-self: start;
}
.emergency-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: #FFFFFF;
    margin: 0;
    letter-spacing: -0.02em;
}
.emergency-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.25rem 3rem;
}
@media (min-width: 768px) {
    .emergency-grid {
        grid-template-columns: 1fr 1fr;
    }
}
@media (min-width: 1200px) {
    .emergency-grid {
        grid-template-columns: 1fr 1fr 1fr;
    }
}
.hotline-item {
    font-size: 0.95rem;
    line-height: 1.6;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.hotline-label {
    color: #E2E8F0;
    font-weight: 500;
}
.hotline-value {
    color: #FBC02D; /* Brand warm gold */
    font-weight: 700;
    font-family: monospace;
}
@media (max-width: 639px) {
    .contact-card {
        padding: 2rem;
    }
    .emergency-card {
        padding: 2rem;
    }
    .hotline-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}
</style>

<section id="contact" class="contact-section">
    <div class="contact-container">
        <div class="contact-header">
            <span class="contact-subtitle">Get In Touch</span>
            <h2 class="contact-title">Contact Us</h2>
            <p class="contact-description">
                Reach out to the Municipal Social Welfare and Development Office. We're here to help and answer any questions you may have.
            </p>
        </div>

        <div class="contact-grid">
            <!-- Office Info Card -->
            <div class="contact-card">
                <h3 class="card-title">Office Information</h3>
                <div class="info-list">
                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="info-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        <div class="info-content">
                            <h4 class="info-label">Address</h4>
                            <p class="info-text">Municipal Hall, Silang, Cavite</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="info-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.802-5.188-4.166-7-7l1.3-1.3c.362-.272.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.11-1.008H5.036a2.25 2.25 0 0 0-2.25 2.25v1.356Z" />
                        </svg>
                        <div class="info-content">
                            <h4 class="info-label">Phone</h4>
                            <p class="info-text">(046) XXX-XXXX</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="info-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <div class="info-content">
                            <h4 class="info-label">Email</h4>
                            <p class="info-text">mswdo@silang.gov.ph</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="info-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <div class="info-content">
                            <h4 class="info-label">Office Hours</h4>
                            <p class="info-text">Monday - Friday<br>8:00 AM – 5:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form Card -->
            <div class="contact-card">
                <h3 class="card-title">Send us a Message</h3>
                <form class="contact-form">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label for="fullname" class="form-field-label">Full Name</label>
                            <input type="text" id="fullname" name="name" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-field-label">Email Address</label>
                            <input type="email" id="email" name="email" required class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="subject" class="form-field-label">Subject</label>
                        <input type="text" id="subject" name="subject" required class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="message" class="form-field-label">Your Message</label>
                        <textarea id="message" name="message" rows="5" required class="form-input form-textarea"></textarea>
                    </div>
                    <button type="submit" class="submit-button">
                        <span>Send Message</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="button-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Emergency Hotlines -->
        <div class="emergency-card">
            <div class="emergency-header">
                <div class="emergency-title-wrapper">
                    <h3 class="emergency-title">Emergency Hotlines</h3>
                </div>
                <span class="emergency-badge">Emergency 24/7</span>
            </div>
            <div class="emergency-grid">
                <div class="hotline-item"><span class="hotline-label">Silang Municipal Office:</span> <span class="hotline-value">(046) 414-0202</span></div>
                <div class="hotline-item"><span class="hotline-label">PDRRMO (Silang):</span> <span class="hotline-value">(046) 424-0203</span></div>
                <div class="hotline-item"><span class="hotline-label">PNP WCPD – Silang:</span> <span class="hotline-value">0998-397-0222</span></div>
                <div class="hotline-item"><span class="hotline-label">Silang PNP Mobile:</span> <span class="hotline-value">0998-598-5622</span></div>
                <div class="hotline-item"><span class="hotline-label">DSWD AICS:</span> <span class="hotline-value">8962-2813</span></div>
                <div class="hotline-item"><span class="hotline-label">DSWD Central Office:</span> <span class="hotline-value">8-931-8101</span></div>
                <div class="hotline-item"><span class="hotline-label">DSWD Mobile:</span> <span class="hotline-value">0919-911-6200</span></div>
                <div class="hotline-item"><span class="hotline-label">Makabata Helpline:</span> <span class="hotline-value">1383</span></div>
                <div class="hotline-item"><span class="hotline-label">Bantay Bata Hotline:</span> <span class="hotline-value">163</span></div>
                <div class="hotline-item"><span class="hotline-label">Emergency (All):</span> <span class="hotline-value">911</span></div>
                <div class="hotline-item"><span class="hotline-label">NCMH Mental Health:</span> <span class="hotline-value">1553</span></div>
                <div class="hotline-item"><span class="hotline-label">Complaints Hotline:</span> <span class="hotline-value">8888</span></div>
                <div class="hotline-item"><span class="hotline-label">Anti-Trafficking Line:</span> <span class="hotline-value">1343</span></div>
                <div class="hotline-item"><span class="hotline-label">PNP Women's Desk:</span> <span class="hotline-value">117</span></div>
                <div class="hotline-item"><span class="hotline-label">Medical Assistance:</span> <span class="hotline-value">1555</span></div>
                <div class="hotline-item"><span class="hotline-label">DOH Hotline:</span> <span class="hotline-value">894-COVID</span></div>
                <div class="hotline-item"><span class="hotline-label">DSWD Help:</span> <span class="hotline-value">0932-933-3251</span></div>
            </div>
        </div>
    </div>
</section>

<!-- ===================================== -->
<!-- GOOGLE MAP -->
<!-- ===================================== -->

<section>

    <iframe
        src="https://www.google.com/maps?q=Silang%20Municipal%20Hall&t=&z=15&ie=UTF8&iwloc=&output=embed"
        class="w-full h-[50vh] sm:h-[400px] lg:h-[450px]"
        loading="lazy">
    </iframe>

</section>

<!-- ===================================== -->
<!-- FOOTER -->
<!-- ===================================== -->

<footer class="bg-primary text-white">

    <div class="max-w-7xl mx-auto px-6 py-16">

        <div class="grid gap-10 lg:grid-cols-4">

            <div class="lg:col-span-1">

                <h2 class="text-3xl font-bold">
                    MSWDO Silang
                </h2>

                <p class="mt-5 text-offwhite leading-8">
                    Municipal Social Welfare and Development Office committed to
                    providing quality, transparent, and compassionate public
                    service.
                </p>

            </div>

            <div>

                <h3 class="font-bold text-xl mb-5">
                    Quick Links
                </h3>

                <ul class="space-y-3 text-offwhite">
                    <li><a href="#home" class="hover:text-warm-gold transition">Home</a></li>
                    <li><a href="#services" class="hover:text-warm-gold transition">Services</a></li>
                    <li><a href="#about" class="hover:text-warm-gold transition">About</a></li>
                    <li><a href="#contact" class="hover:text-warm-gold transition">Contact</a></li>
                </ul>

            </div>

            <div>

                <h3 class="font-bold text-xl mb-5">
                    Programs
                </h3>

                <ul class="space-y-3 text-offwhite">
                    <li>Financial Assistance</li>
                    <li>VAWC</li>
                    <li>BCPC</li>
                    <li>Senior Citizens</li>
                    <!-- <li>Solo Parent</li> -->
                </ul>

            </div>

            <div>

                <h3 class="font-bold text-xl mb-5">
                    Office Hours
                </h3>

                <p class="text-offwhite leading-7">
                    Monday - Friday
                    <br><br>
                    8:00 AM - 5:00 PM
                </p>

            </div>

        </div>

        <hr class="border-primary border-opacity-70 my-10">

        <div class="flex justify-center items-center text-sm">

            <p class="text-offwhite text-center">
                © {{ date('Y') }} MSWDO Silang. All Rights Reserved.
            </p>

        </div>

    </div>

</footer>

<!-- ===================================== -->
<!-- SCROLL TO TOP -->
<!-- ===================================== -->

<button
    id="scrollTop"
    class="hidden fixed bottom-6 right-6 bg-warm-gold p-4 rounded-full shadow-xl hover:scale-110 transition">

    ↑

</button>

<!-- ===================================== -->
<!-- JAVASCRIPT -->
<!-- ===================================== -->

<script>

const menuButton = document.getElementById('menuButton');

const mobileMenu = document.getElementById('mobileMenu');

menuButton.addEventListener('click', () => {

    mobileMenu.classList.toggle('hidden');

});

const scrollBtn = document.getElementById('scrollTop');

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

</script>

</body>

</html>

