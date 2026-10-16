-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2026 at 05:21 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mswdo1`
--

-- --------------------------------------------------------

--
-- Table structure for table `assistance_records`
--

CREATE TABLE `assistance_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `social_case_study_id` bigint(20) UNSIGNED DEFAULT NULL,
  `assistance_type` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `release_date` date DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `beneficiary_intakes`
--

CREATE TABLE `beneficiary_intakes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `social_case_study_id` bigint(20) UNSIGNED DEFAULT NULL,
  `control_number` varchar(50) NOT NULL,
  `client_type` varchar(20) NOT NULL DEFAULT 'New',
  `date_processed` date NOT NULL,
  `time_start` varchar(20) DEFAULT NULL,
  `time_end` varchar(20) DEFAULT NULL,
  `encoder` bigint(20) UNSIGNED DEFAULT NULL,
  `is_client_beneficiary` tinyint(1) NOT NULL DEFAULT 1,
  `beneficiary_last_name` varchar(255) DEFAULT NULL,
  `beneficiary_first_name` varchar(255) DEFAULT NULL,
  `beneficiary_middle_name` varchar(255) DEFAULT NULL,
  `beneficiary_extension_name` varchar(20) DEFAULT NULL,
  `beneficiary_birthday` date DEFAULT NULL,
  `beneficiary_age` int(11) DEFAULT NULL,
  `beneficiary_sex` varchar(20) DEFAULT NULL,
  `beneficiary_civil_status` varchar(50) DEFAULT NULL,
  `beneficiary_occupation` varchar(150) DEFAULT NULL,
  `beneficiary_monthly_salary` decimal(12,2) DEFAULT NULL,
  `beneficiary_category` varchar(100) DEFAULT NULL,
  `beneficiary_category_other` varchar(150) DEFAULT NULL,
  `beneficiary_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`beneficiary_categories`)),
  `has_representative` tinyint(1) NOT NULL DEFAULT 0,
  `rep_last_name` varchar(100) DEFAULT NULL,
  `rep_first_name` varchar(100) DEFAULT NULL,
  `rep_middle_name` varchar(100) DEFAULT NULL,
  `rep_extension_name` varchar(20) DEFAULT NULL,
  `rep_street_address` text DEFAULT NULL,
  `rep_barangay` varchar(100) DEFAULT NULL,
  `rep_city` varchar(100) DEFAULT 'Silang',
  `rep_province` varchar(100) DEFAULT 'Cavite',
  `rep_region` varchar(100) DEFAULT 'Region IV-A',
  `rep_contact_number` varchar(50) DEFAULT NULL,
  `rep_birthday` date DEFAULT NULL,
  `rep_age` int(11) DEFAULT NULL,
  `rep_sex` varchar(20) DEFAULT NULL,
  `rep_civil_status` varchar(50) DEFAULT NULL,
  `rep_occupation` varchar(150) DEFAULT NULL,
  `rep_monthly_salary` decimal(12,2) DEFAULT NULL,
  `rep_relationship` varchar(100) DEFAULT NULL,
  `family_composition` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`family_composition`)),
  `social_worker_assessment` text DEFAULT NULL,
  `recommended_assistance_type` varchar(150) DEFAULT NULL,
  `assistance_purpose` varchar(255) DEFAULT NULL,
  `assistance_type` varchar(100) DEFAULT NULL,
  `recommended_amount` decimal(12,2) DEFAULT NULL,
  `is_payroll_generated` tinyint(1) NOT NULL DEFAULT 0,
  `payroll_generated_at` timestamp NULL DEFAULT NULL,
  `payroll_date` date DEFAULT NULL,
  `claiming_date` date DEFAULT NULL,
  `payroll_record_id` bigint(20) UNSIGNED DEFAULT NULL,
  `claim_status` varchar(20) NOT NULL DEFAULT 'Unclaimed',
  `claimed_at` timestamp NULL DEFAULT NULL,
  `claimed_by` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `archived_by` bigint(20) UNSIGNED DEFAULT NULL,
  `archive_module` varchar(20) DEFAULT NULL,
  `archive_reason` text DEFAULT NULL,
  `interviewed_by` varchar(150) DEFAULT NULL,
  `reviewed_by` varchar(150) DEFAULT NULL,
  `beneficiary_barangay` varchar(255) DEFAULT NULL,
  `beneficiary_street_address` text DEFAULT NULL,
  `beneficiary_city` varchar(100) DEFAULT 'Silang',
  `beneficiary_province` varchar(100) DEFAULT 'Cavite',
  `beneficiary_region` varchar(100) DEFAULT 'Region IV-A',
  `beneficiary_contact_number` varchar(50) DEFAULT NULL,
  `beneficiary_relationship` varchar(100) DEFAULT NULL,
  `medical_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medical_conditions`)),
  `medical_condition_other` varchar(255) DEFAULT NULL,
  `service_provided` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `purpose_other` varchar(255) DEFAULT NULL,
  `submitted_to` varchar(255) NOT NULL,
  `is_historical` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `birthday_payouts`
--

CREATE TABLE `birthday_payouts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `senior_id` bigint(20) UNSIGNED NOT NULL,
  `payout_year` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 500.00,
  `status` enum('pending','released','cancelled') NOT NULL DEFAULT 'pending',
  `released_by` bigint(20) UNSIGNED DEFAULT NULL,
  `released_date` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `birthday_payout_history`
--

CREATE TABLE `birthday_payout_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payout_id` bigint(20) UNSIGNED DEFAULT NULL,
  `senior_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `performed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_interviews`
--

CREATE TABLE `case_interviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `social_case_study_id` bigint(20) UNSIGNED NOT NULL,
  `interview_reason` text DEFAULT NULL,
  `interview_situation` text DEFAULT NULL,
  `interview_household` text DEFAULT NULL,
  `monthly_income` decimal(10,2) DEFAULT NULL,
  `monthly_expenses` decimal(10,2) DEFAULT NULL,
  `family_illnesses` text DEFAULT NULL,
  `previous_assistance` varchar(255) DEFAULT NULL,
  `interview_notes` text DEFAULT NULL,
  `social_worker_assessment` text DEFAULT NULL,
  `recommendation` varchar(255) DEFAULT NULL,
  `recommended_amount` decimal(10,2) DEFAULT NULL,
  `additional_requirements` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `case_interviews`
--

INSERT INTO `case_interviews` (`id`, `social_case_study_id`, `interview_reason`, `interview_situation`, `interview_household`, `monthly_income`, `monthly_expenses`, `family_illnesses`, `previous_assistance`, `interview_notes`, `social_worker_assessment`, `recommendation`, `recommended_amount`, `additional_requirements`, `created_at`, `updated_at`) VALUES
(1, 1, 'Medical Assistance', 'The client needs medical assistance for medical bills and chemotherapy', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 06:17:26', '2026-10-15 06:17:26'),
(2, 2, 'Medical Assistance', 'Wala napo akong pera pang top up tanga kase ako', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 07:56:28', '2026-10-15 07:56:28'),
(3, 3, 'Medical Assistance', 'Need pera for alak', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 08:09:00', '2026-10-15 08:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `case_rejections`
--

CREATE TABLE `case_rejections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `blocking_assistance_id` bigint(20) UNSIGNED DEFAULT NULL,
  `social_case_study_id` bigint(20) UNSIGNED DEFAULT NULL,
  `officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `officer_name` varchar(255) DEFAULT NULL,
  `reason` text NOT NULL,
  `last_assistance_date` date DEFAULT NULL,
  `last_assistance_type` varchar(255) DEFAULT NULL,
  `next_eligible_date` date DEFAULT NULL,
  `rejected_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `suffix` varchar(50) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `birthplace` varchar(255) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `income` varchar(100) DEFAULT NULL,
  `gender` enum('Male','Female') DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `birthdate`, `birthplace`, `religion`, `education`, `civil_status`, `occupation`, `income`, `gender`, `age`, `address`, `barangay`, `contact_number`, `created_at`, `updated_at`) VALUES
(1, 'gerald', 'louis', 'sumaylo', NULL, '2005-08-13', 'Silang', 'INC', 'College Grad', 'Single', 'Pull Stack developer', '15,000', 'Male', 21, 'Acacia', 'Acacia', '09223086018', '2026-10-15 06:12:14', '2026-10-15 06:17:26'),
(2, 'rhainy', NULL, 'redona', NULL, '2005-08-13', 'Silang Namik', 'QUIBOLOY', 'Under Graduate', 'Single', 'Tindero', '10,000', 'Female', 21, 'Bulihan', 'Bulihan', '09223086018', '2026-10-15 06:42:55', '2026-10-15 07:56:28'),
(3, 'nick', NULL, 'hernandez', NULL, '1995-08-13', 'New Era', 'INC', 'College Grad', 'Married', 'Teacher', '25,000', 'Male', 31, 'Bulihan', 'Bulihan', '09223086018', '2026-10-15 08:06:15', '2026-10-15 08:09:00'),
(4, 'elaiza', NULL, 'sumaylo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-15 08:24:29', '2026-10-15 08:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `document_reference_counters`
--

CREATE TABLE `document_reference_counters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'social_case',
  `current_number` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_reference_counters`
--

INSERT INTO `document_reference_counters` (`id`, `type`, `current_number`, `created_at`, `updated_at`) VALUES
(1, 'social_case', 4, '2026-10-15 06:12:14', '2026-10-15 06:12:14');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_audit_logs`
--

CREATE TABLE `eligibility_audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `officer_name` varchar(255) DEFAULT NULL,
  `result` varchar(50) NOT NULL,
  `result_details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `search_duration_ms` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `eligibility_audit_logs`
--

INSERT INTO `eligibility_audit_logs` (`id`, `client_id`, `client_name`, `officer_id`, `officer_name`, `result`, `result_details`, `ip_address`, `user_agent`, `search_duration_ms`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Gerald Louis Sumaylo', 3, 'Christopher Social', 'eligible', 'Client is eligible for assistance.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 6, '2026-10-15 06:09:44', '2026-10-15 06:09:44'),
(2, NULL, 'Gerald Louis Sumaylo', 3, 'Christopher Social', 'eligible', 'Client is eligible for assistance.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 4, '2026-10-15 06:12:09', '2026-10-15 06:12:09'),
(3, NULL, 'Rhainy Redona', 3, 'Christopher Social', 'eligible', 'Client is eligible for assistance.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 4, '2026-10-15 06:42:51', '2026-10-15 06:42:51'),
(4, NULL, 'Nick Hernandez', 3, 'Christopher Social', 'eligible', 'Client is eligible for assistance.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 4, '2026-10-15 08:06:11', '2026-10-15 08:06:11'),
(5, NULL, 'Elaiza Sumaylo', 3, 'Christopher Social', 'eligible', 'Client is eligible for assistance.', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 4, '2026-10-15 08:24:26', '2026-10-15 08:24:26');

-- --------------------------------------------------------

--
-- Table structure for table `email_login_codes`
--

CREATE TABLE `email_login_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_members`
--

CREATE TABLE `family_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `social_case_study_id` bigint(20) UNSIGNED NOT NULL,
  `person_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `suffix` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `relationship` varchar(100) NOT NULL,
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `education` varchar(100) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `monthly_income` varchar(100) DEFAULT NULL,
  `is_dependent` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_members`
--

INSERT INTO `family_members` (`id`, `social_case_study_id`, `person_id`, `first_name`, `middle_name`, `last_name`, `suffix`, `full_name`, `relationship`, `age`, `education`, `sex`, `occupation`, `monthly_income`, `is_dependent`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'eloisa', NULL, 'sumaylo', NULL, 'Eloisa Sumaylo', 'Father', 47, 'College Grad', NULL, 'Teacher', '20,000', 0, NULL, '2026-10-15 06:17:26', '2026-10-15 06:17:26'),
(2, 2, NULL, 'mama', NULL, 'nya', NULL, 'Mama nya', 'Mother', 27, 'N/a', NULL, 'R.I.P', '10,000', 0, NULL, '2026-10-15 07:56:28', '2026-10-15 07:56:28'),
(3, 3, NULL, 'cristan', NULL, 'ramos', NULL, 'Cristan Ramos', 'Brother', 22, 'College Grad', NULL, 'Teacher', '25,000', 0, NULL, '2026-10-15 08:09:00', '2026-10-15 08:09:00');

-- --------------------------------------------------------

--
-- Table structure for table `financial_assistance_applications`
--

CREATE TABLE `financial_assistance_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `application_number` varchar(50) NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `assistance_type` varchar(255) NOT NULL,
  `amount_requested` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_assistance_messages`
--

CREATE TABLE `financial_assistance_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `intake_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `beneficiary_name` varchar(255) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `recipient_contact_number` varchar(50) NOT NULL,
  `message_body` text NOT NULL,
  `message_type` varchar(50) NOT NULL DEFAULT 'Unclaimed Assistance',
  `claiming_date` date DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Sent',
  `reference_id` varchar(100) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `sent_by` varchar(255) DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_payroll_records`
--

CREATE TABLE `financial_payroll_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_number` varchar(60) NOT NULL,
  `payroll_date` date NOT NULL,
  `batch_number` int(11) NOT NULL DEFAULT 1,
  `generated_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `generated_by_name` varchar(255) DEFAULT NULL,
  `disbursing_officer` varchar(255) DEFAULT NULL,
  `certified_by` varchar(255) NOT NULL DEFAULT 'MSWDO HEAD / OFFICER-IN-CHARGE',
  `approved_by` varchar(255) NOT NULL DEFAULT 'HON. MUNICIPAL MAYOR',
  `total_beneficiaries` int(11) NOT NULL DEFAULT 0,
  `total_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'Completed',
  `notes` text DEFAULT NULL,
  `payroll_data` longtext DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `in_between_benefit_configurations`
--

CREATE TABLE `in_between_benefit_configurations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `benefit_name` varchar(100) NOT NULL DEFAULT 'In-Between Birthday Cash Gift',
  `description` text DEFAULT NULL,
  `benefit_amount` decimal(10,2) NOT NULL DEFAULT 1000.00,
  `milestone_ages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '[80, 85, 90, 95, 100]' CHECK (json_valid(`milestone_ages`)),
  `eligible_intervals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["81-84", "86-89", "91-94", "96-99"]' CHECK (json_valid(`eligible_intervals`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `effective_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `ordinance_reference` varchar(100) DEFAULT NULL,
  `eligibility_rules` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `in_between_benefit_history`
--

CREATE TABLE `in_between_benefit_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `senior_id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `birth_date` date NOT NULL,
  `current_age` int(11) NOT NULL,
  `benefit_type` varchar(50) NOT NULL DEFAULT 'IN_BETWEEN_BIRTHDAY_GIFT',
  `eligibility_interval` varchar(10) NOT NULL,
  `birthday_year` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `application_date` date NOT NULL,
  `payout_date` date DEFAULT NULL,
  `status` enum('pending','approved','released','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `is_exported` tinyint(1) NOT NULL DEFAULT 0,
  `reference_number` varchar(50) NOT NULL,
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `audit_trail` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`audit_trail`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_07_16_000001_create_mswdo1_schema', 1),
(2, '2026_07_20_000002_add_age_to_clients', 1),
(3, '2026_07_26_000001_add_intake_sheet_fields_to_beneficiary_intakes_table', 1),
(4, '2026_07_26_000002_add_hardcopy_fields_to_beneficiary_intakes_table', 1),
(5, '2026_07_27_000001_create_senior_activity_logs_table', 1),
(6, '2026_07_30_000001_add_indexes_for_duplicate_checking_to_beneficiary_intakes', 1),
(7, '2026_07_30_000001_create_social_case_activity_logs_table', 1),
(8, '2026_07_30_000002_modify_role_column_in_users_table', 1),
(9, '2026_08_06_000001_create_email_login_codes_table', 1),
(10, '2026_08_07_072038_add_extra_fields_to_clients_table', 1),
(11, '2026_08_07_072501_add_signers_to_social_case_studies_table', 1),
(12, '2026_08_07_072549_add_education_to_family_members_table', 1),
(13, '2026_08_17_000001_add_eligibility_workflow_to_social_case_studies_table', 1),
(14, '2026_08_17_111354_make_clients_birthdate_nullable', 1),
(15, '2026_08_17_111502_make_clients_name_only_profile_columns_nullable', 1),
(16, '2026_08_17_130000_make_family_members_monthly_income_string', 1),
(17, '2026_08_19_062306_create_password_reset_requests_table', 1),
(18, '2026_08_19_093000_add_requested_from_url_to_password_reset_requests_table', 1),
(19, '2026_08_20_040418_create_online_requests_table', 1),
(20, '2026_08_20_041500_add_case_id_to_online_requests_table', 1),
(21, '2026_08_20_050247_create_online_request_attachments_table', 1),
(22, '2026_08_20_060000_add_mswdo_staff_to_users_signature_position', 1),
(26, '2026_09_06_075830_create_document_reference_counter_table', 1),
(27, '2026_09_11_051153_create_in_between_benefit_configurations_table', 1),
(28, '2026_09_11_051218_create_in_between_benefit_history_table', 1),
(29, '2026_09_11_112941_add_is_exported_to_in_between_benefit_history_table', 1),
(32, '2026_09_14_120001_add_suffix_to_clients_table', 2),
(33, '2026_09_14_120002_add_main_client_and_intake_snapshot_to_social_case_studies_table', 2),
(34, '2026_09_14_120003_add_person_id_and_name_fields_to_family_members_table', 2),
(35, '2026_09_14_120004_backfill_main_client_relations', 3),
(36, '2026_09_14_120005_make_main_client_id_not_null', 4),
(37, '2026_09_14_120006_drop_client_id_from_social_case_studies', 5),
(38, '2026_09_02_000001_create_financial_payroll_records_table', 6),
(39, '2026_09_03_000001_add_payroll_generation_fields_to_beneficiary_intakes_table', 6),
(40, '2026_09_03_000002_add_batch_and_payroll_record_relations', 6),
(41, '2026_09_05_000001_add_claim_status_to_beneficiary_intakes_table', 6),
(42, '2026_09_09_000001_add_claiming_date_to_beneficiary_intakes_table', 6),
(43, '2026_09_09_000002_create_financial_assistance_messages_table', 6),
(44, '2026_09_15_000001_create_online_financial_intakes_table', 6),
(45, '2026_09_16_000001_add_archive_fields_to_beneficiary_intakes_table', 6),
(46, '2026_09_22_000001_add_is_historical_to_beneficiary_intakes_table', 6);

-- --------------------------------------------------------

--
-- Table structure for table `online_financial_intakes`
--

CREATE TABLE `online_financial_intakes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'For Review',
  `date_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `control_number` varchar(50) DEFAULT NULL,
  `client_type` varchar(20) NOT NULL DEFAULT 'New',
  `is_client_beneficiary` tinyint(1) NOT NULL DEFAULT 1,
  `beneficiary_last_name` varchar(100) NOT NULL,
  `beneficiary_first_name` varchar(100) NOT NULL,
  `beneficiary_middle_name` varchar(100) DEFAULT NULL,
  `beneficiary_extension_name` varchar(20) DEFAULT NULL,
  `beneficiary_street_address` varchar(255) NOT NULL,
  `beneficiary_barangay` varchar(100) NOT NULL,
  `beneficiary_city` varchar(100) NOT NULL DEFAULT 'Silang',
  `beneficiary_province` varchar(100) NOT NULL DEFAULT 'Cavite',
  `beneficiary_region` varchar(100) NOT NULL DEFAULT 'Region IV-A',
  `beneficiary_contact_number` varchar(20) NOT NULL,
  `beneficiary_birthday` date NOT NULL,
  `beneficiary_age` int(11) NOT NULL DEFAULT 0,
  `beneficiary_sex` varchar(10) NOT NULL,
  `beneficiary_civil_status` varchar(50) NOT NULL,
  `beneficiary_occupation` varchar(150) DEFAULT NULL,
  `beneficiary_monthly_salary` decimal(10,2) DEFAULT NULL,
  `beneficiary_category` varchar(100) DEFAULT NULL,
  `beneficiary_category_other` varchar(150) DEFAULT NULL,
  `beneficiary_categories` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`beneficiary_categories`)),
  `beneficiary_relationship` varchar(100) DEFAULT NULL,
  `has_representative` tinyint(1) NOT NULL DEFAULT 0,
  `rep_last_name` varchar(100) DEFAULT NULL,
  `rep_first_name` varchar(100) DEFAULT NULL,
  `rep_middle_name` varchar(100) DEFAULT NULL,
  `rep_extension_name` varchar(20) DEFAULT NULL,
  `rep_street_address` varchar(255) DEFAULT NULL,
  `rep_barangay` varchar(100) DEFAULT NULL,
  `rep_city` varchar(100) DEFAULT NULL,
  `rep_province` varchar(100) DEFAULT NULL,
  `rep_region` varchar(100) DEFAULT NULL,
  `rep_contact_number` varchar(20) DEFAULT NULL,
  `rep_birthday` date DEFAULT NULL,
  `rep_age` int(11) DEFAULT NULL,
  `rep_sex` varchar(10) DEFAULT NULL,
  `rep_civil_status` varchar(50) DEFAULT NULL,
  `rep_occupation` varchar(150) DEFAULT NULL,
  `rep_monthly_salary` decimal(10,2) DEFAULT NULL,
  `rep_relationship` varchar(100) DEFAULT NULL,
  `family_composition` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`family_composition`)),
  `medical_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medical_conditions`)),
  `medical_condition_other` varchar(255) DEFAULT NULL,
  `assistance_purpose` varchar(255) DEFAULT NULL,
  `assistance_type` varchar(100) DEFAULT NULL,
  `service_provided` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `purpose_other` varchar(255) DEFAULT NULL,
  `submitted_to` varchar(255) DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `review_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `beneficiary_intake_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_requests`
--

CREATE TABLE `online_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `request_for` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `barangay` varchar(255) NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `service_type` varchar(255) NOT NULL,
  `assistance_type` varchar(255) NOT NULL,
  `situation` text NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `case_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `processed_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `online_requests`
--

INSERT INTO `online_requests` (`id`, `request_for`, `first_name`, `last_name`, `dob`, `barangay`, `contact_number`, `email`, `address`, `service_type`, `assistance_type`, `situation`, `status`, `case_id`, `notes`, `processed_by`, `created_at`, `updated_at`) VALUES
(1, 'myself', 'GERALD', 'SUMAYLO', '2005-08-13', 'BUHO', '09079276877', 'geraldlouissumaylo@gmail.com', 'INC COMPOUND , BLK 56-7 New Era', 'social_case_study', 'burial', 'Humihingi po ako ng tulong para sa aking pang gamot dahil ako po ay walang trabaho', 'rejected', NULL, 'Not eligible for assistance', NULL, '2026-10-15 06:28:02', '2026-10-15 06:55:03'),
(2, 'child', 'Vince', 'Bernardino', '2005-08-13', 'BIGA 1', '09079276877', 'vince@gmail.com', 'INC COMPOUND , BLK 56-7 New Era', 'social_case_study', 'emergency', 'need kopo ng pera pang talpak', 'approved', NULL, NULL, NULL, '2026-10-15 06:37:31', '2026-10-15 06:54:26');

-- --------------------------------------------------------

--
-- Table structure for table `online_request_attachments`
--

CREATE TABLE `online_request_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `online_request_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `online_request_attachments`
--

INSERT INTO `online_request_attachments` (`id`, `online_request_id`, `file_name`, `file_path`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
(1, 1, 'testing1.pdf', 'online-request-attachments/1792074482_testing1.pdf', 'application/pdf', 371958, '2026-10-15 06:28:02', '2026-10-15 06:28:02'),
(2, 2, 'testing1.pdf', 'online-request-attachments/1792075051_testing1.pdf', 'application/pdf', 371958, '2026-10-15 06:37:31', '2026-10-15 06:37:31');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_requests`
--

CREATE TABLE `password_reset_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `requested_from_url` varchar(500) DEFAULT NULL,
  `status` enum('pending','approved','completed','rejected') NOT NULL DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `public_applicants`
--

CREATE TABLE `public_applicants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `public_requests`
--

CREATE TABLE `public_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `applicant_id` bigint(20) UNSIGNED NOT NULL,
  `request_number` varchar(50) NOT NULL,
  `relationship_to_beneficiary` varchar(50) NOT NULL,
  `beneficiary_first_name` varchar(255) NOT NULL,
  `beneficiary_middle_name` varchar(255) DEFAULT NULL,
  `beneficiary_last_name` varchar(255) NOT NULL,
  `beneficiary_birthdate` date DEFAULT NULL,
  `beneficiary_barangay` varchar(255) DEFAULT NULL,
  `beneficiary_address` varchar(500) DEFAULT NULL,
  `beneficiary_contact_number` varchar(20) DEFAULT NULL,
  `assistance_reason` varchar(100) NOT NULL,
  `assistance_reason_other` varchar(255) DEFAULT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time_of_day` enum('morning','afternoon') DEFAULT NULL,
  `applicant_notes` text DEFAULT NULL,
  `request_status` enum('submitted','under_review','needs_information','accepted','rejected','cancelled','converted') NOT NULL DEFAULT 'submitted',
  `eligibility_status` enum('pending','eligible','ineligible') NOT NULL DEFAULT 'pending',
  `eligibility_checked_at` timestamp NULL DEFAULT NULL,
  `eligibility_checked_by` bigint(20) UNSIGNED DEFAULT NULL,
  `eligibility_note` text DEFAULT NULL,
  `needs_information_note` text DEFAULT NULL,
  `appointment_status` enum('requested','confirmed','rescheduled','cancelled','completed','no_show') NOT NULL DEFAULT 'requested',
  `confirmed_appointment_at` datetime DEFAULT NULL,
  `appointment_notes` text DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `social_case_study_id` bigint(20) UNSIGNED DEFAULT NULL,
  `converted_at` timestamp NULL DEFAULT NULL,
  `converted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `request_token` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `public_request_activities`
--

CREATE TABLE `public_request_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `public_request_id` bigint(20) UNSIGNED NOT NULL,
  `actor_type` enum('applicant','staff') NOT NULL DEFAULT 'applicant',
  `applicant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(60) NOT NULL,
  `message` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `senior_activity_logs`
--

CREATE TABLE `senior_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `identifier` varchar(255) DEFAULT NULL,
  `admin` varchar(255) NOT NULL DEFAULT 'Admin',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `senior_citizen_records`
--

CREATE TABLE `senior_citizen_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `record_number` varchar(50) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `year_applied` varchar(4) DEFAULT NULL,
  `control_number` varchar(50) DEFAULT NULL,
  `senior_id_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `philsys_number` varchar(255) DEFAULT NULL,
  `rrn_number` varchar(255) DEFAULT NULL,
  `osca_id` varchar(255) DEFAULT NULL,
  `blood_type` varchar(10) DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `emergency_contact_relationship` varchar(50) DEFAULT NULL,
  `photo` varchar(500) DEFAULT NULL,
  `avatar_image` varchar(500) DEFAULT NULL,
  `qr_code` text DEFAULT NULL,
  `qr_code_image` varchar(500) DEFAULT NULL,
  `date_issued` date DEFAULT NULL,
  `last_printed_at` timestamp NULL DEFAULT NULL,
  `print_count` int(11) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','pending','archived') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('iZqeH7trWBLR4W7vgq73OlrozQvh5pLe0noHrW30', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiRzR5Qll5M21rNm9NVzc2TFlOMkFKOEwyWWRoWDR1Z3g3MFF0YkxvdSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9wYXNzd29yZC1yZXNldC1tYW5hZ2VtZW50IjtzOjU6InJvdXRlIjtzOjMxOiJhZG1pbi5wYXNzd29yZC1yZXNldC1tYW5hZ2VtZW50Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxMzoiYWRtaW5fdXNlcl9pZCI7aToxO3M6MTU6ImFkbWluX3VzZXJfbmFtZSI7czoxMDoiRnJlZCBDYWxvcyI7czoxNToiYWRtaW5fdXNlcl9yb2xlIjtzOjU6ImFkbWluIjtzOjExOiJhdXRoX3NvdXJjZSI7czo1OiJtc3dkbyI7fQ==', 1792120917);

-- --------------------------------------------------------

--
-- Table structure for table `social_case_activity_logs`
--

CREATE TABLE `social_case_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `case_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`case_info`)),
  `admin` varchar(255) NOT NULL DEFAULT 'Social Case Study Officer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_case_activity_logs`
--

INSERT INTO `social_case_activity_logs` (`id`, `action`, `details`, `case_info`, `admin`, `created_at`, `updated_at`) VALUES
(1, 'created', 'Client forwarded to Marilyn Barameda for case encoding', '{\"clientName\":\"Gerald Louis. Sumaylo\",\"controlNo\":\"MSWD-O-2026-10-0001\",\"assignedEncoder\":\"Marilyn Barameda\"}', 'Christopher Social', '2026-10-15 06:12:14', '2026-10-15 06:12:14'),
(2, 'created', 'New case created', '{\"clientName\":\"Gerald Louis. Sumaylo\",\"controlNo\":\"MSWD-O-2026-10-0001\"}', 'Marilyn Barameda', '2026-10-15 06:17:26', '2026-10-15 06:17:26'),
(3, 'printed', 'Case document printed', '{\"clientName\":\"Gerald Louis. Sumaylo\",\"controlNo\":\"MSWD-O-2026-10-0001\"}', 'Marilyn Barameda', '2026-10-15 06:18:10', '2026-10-15 06:18:10'),
(4, 'created', 'Client forwarded to Marilyn Barameda for case encoding', '{\"clientName\":\"Rhainy  Redona\",\"controlNo\":\"MSWD-O-2026-10-0002\",\"assignedEncoder\":\"Marilyn Barameda\"}', 'Christopher Social', '2026-10-15 06:42:56', '2026-10-15 06:42:56'),
(5, 'archived', 'Case archived', '{\"clientName\":\"Gerald Louis. Sumaylo\",\"controlNo\":\"MSWD-O-2026-10-0001\"}', 'Marilyn Barameda', '2026-10-15 07:45:43', '2026-10-15 07:45:43'),
(6, 'restored', 'Case restored from archive', '{\"clientName\":\"Gerald Louis. Sumaylo\",\"controlNo\":\"MSWD-O-2026-10-0001\"}', 'Marilyn Barameda', '2026-10-15 07:47:34', '2026-10-15 07:47:34'),
(7, 'created', 'New case created', '{\"clientName\":\"Rhainy  Redona\",\"controlNo\":\"MSWD-O-2026-10-0002\"}', 'Marilyn Barameda', '2026-10-15 07:56:28', '2026-10-15 07:56:28'),
(8, 'created', 'Client forwarded to Marilyn Barameda for case encoding', '{\"clientName\":\"Nick  Hernandez\",\"controlNo\":\"MSWD-O-2026-10-0003\",\"assignedEncoder\":\"Marilyn Barameda\"}', 'Christopher Social', '2026-10-15 08:06:16', '2026-10-15 08:06:16'),
(9, 'created', 'New case created', '{\"clientName\":\"Nick  Hernandez\",\"controlNo\":\"MSWD-O-2026-10-0003\"}', 'Marilyn Barameda', '2026-10-15 08:09:01', '2026-10-15 08:09:01'),
(10, 'printed', 'Case document printed', '{\"clientName\":\"Nick  Hernandez\",\"controlNo\":\"MSWD-O-2026-10-0003\"}', 'Marilyn Barameda', '2026-10-15 08:09:14', '2026-10-15 08:09:14'),
(11, 'printed', 'Case document printed', '{\"clientName\":\"Gerald Louis. Sumaylo\",\"controlNo\":\"MSWD-O-2026-10-0001\"}', 'Marilyn Barameda', '2026-10-15 08:09:44', '2026-10-15 08:09:44'),
(12, 'created', 'Client forwarded to Marilyn Barameda for case encoding', '{\"clientName\":\"Elaiza  Sumaylo\",\"controlNo\":\"MSWD-O-2026-10-0004\",\"assignedEncoder\":\"Marilyn Barameda\"}', 'Christopher Social', '2026-10-15 08:24:30', '2026-10-15 08:24:30');

-- --------------------------------------------------------

--
-- Table structure for table `social_case_reports`
--

CREATE TABLE `social_case_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `social_case_study_id` bigint(20) UNSIGNED DEFAULT NULL,
  `case_number` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `generated_at` timestamp NULL DEFAULT NULL,
  `generated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `snapshot` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`snapshot`)),
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_case_report_release_logs`
--

CREATE TABLE `social_case_report_release_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `social_case_study_id` bigint(20) UNSIGNED NOT NULL,
  `social_case_report_id` bigint(20) UNSIGNED DEFAULT NULL,
  `released_by` bigint(20) UNSIGNED DEFAULT NULL,
  `released_by_name` varchar(255) DEFAULT NULL,
  `released_to` varchar(255) NOT NULL,
  `released_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_case_studies`
--

CREATE TABLE `social_case_studies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `main_client_id` bigint(20) UNSIGNED NOT NULL,
  `officer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `case_number` varchar(50) NOT NULL,
  `date_processed` date DEFAULT NULL,
  `service_provided` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `intake_first_name` varchar(255) DEFAULT NULL,
  `intake_middle_name` varchar(255) DEFAULT NULL,
  `intake_last_name` varchar(255) DEFAULT NULL,
  `intake_suffix` varchar(50) DEFAULT NULL,
  `intake_full_name` varchar(255) DEFAULT NULL,
  `intake_birthdate` date DEFAULT NULL,
  `intake_age` tinyint(3) UNSIGNED DEFAULT NULL,
  `intake_gender` varchar(20) DEFAULT NULL,
  `intake_civil_status` varchar(50) DEFAULT NULL,
  `intake_religion` varchar(100) DEFAULT NULL,
  `intake_birthplace` varchar(255) DEFAULT NULL,
  `intake_education` varchar(100) DEFAULT NULL,
  `intake_occupation` varchar(255) DEFAULT NULL,
  `intake_income` varchar(100) DEFAULT NULL,
  `intake_address` text DEFAULT NULL,
  `intake_barangay` varchar(255) DEFAULT NULL,
  `intake_contact_number` varchar(20) DEFAULT NULL,
  `submitted_to` varchar(255) DEFAULT NULL,
  `encoded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('Draft','In Progress','Review','Completed','Printed','Released','Archived') NOT NULL DEFAULT 'Draft',
  `eligibility_status` enum('pending','eligible','ineligible') NOT NULL DEFAULT 'pending',
  `eligible_by` bigint(20) UNSIGNED DEFAULT NULL,
  `eligible_at` timestamp NULL DEFAULT NULL,
  `ineligible_reason` text DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `interview_date` date DEFAULT NULL,
  `workflow_step` enum('requirements_verification','interview','evaluation','report_generation','release') NOT NULL DEFAULT 'requirements_verification',
  `requirements_complete` tinyint(1) NOT NULL DEFAULT 0,
  `signers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`signers`)),
  `interview_complete` tinyint(1) NOT NULL DEFAULT 0,
  `evaluation_complete` tinyint(1) NOT NULL DEFAULT 0,
  `report_generated` tinyint(1) NOT NULL DEFAULT 0,
  `assistance_released` tinyint(1) NOT NULL DEFAULT 0,
  `assistance_amount` decimal(10,2) DEFAULT NULL,
  `assistance_date` date DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `document_ref_number` int(11) NOT NULL DEFAULT 0,
  `released_by` bigint(20) UNSIGNED DEFAULT NULL,
  `released_to` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_case_studies`
--

INSERT INTO `social_case_studies` (`id`, `main_client_id`, `officer_id`, `case_number`, `date_processed`, `service_provided`, `purpose`, `intake_first_name`, `intake_middle_name`, `intake_last_name`, `intake_suffix`, `intake_full_name`, `intake_birthdate`, `intake_age`, `intake_gender`, `intake_civil_status`, `intake_religion`, `intake_birthplace`, `intake_education`, `intake_occupation`, `intake_income`, `intake_address`, `intake_barangay`, `intake_contact_number`, `submitted_to`, `encoded_by`, `status`, `eligibility_status`, `eligible_by`, `eligible_at`, `ineligible_reason`, `summary`, `interview_date`, `workflow_step`, `requirements_complete`, `signers`, `interview_complete`, `evaluation_complete`, `report_generated`, `assistance_released`, `assistance_amount`, `assistance_date`, `released_at`, `document_ref_number`, `released_by`, `released_to`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'MSWD-O-2026-10-0001', '2026-10-15', NULL, 'Medical Assistance', 'gerald', 'louis', 'sumaylo', NULL, 'Gerald Louis. Sumaylo', '2005-08-13', 21, 'Male', 'Single', 'INC', 'Silang', 'College Grad', 'Pull Stack developer', '15,000', 'Acacia', 'Acacia', '09223086018', 'PCSO, DOH, DSWD, MSWDO, OP', 2, 'Released', 'eligible', 3, '2026-10-15 06:12:14', NULL, 'The client needs medical assistance for medical bills and chemotherapy', '2026-10-15', 'requirements_verification', 1, '{\"prepared_by_name\":\"Marilyn Barameda\",\"prepared_by_title\":\"MSWDO Staff\",\"noted_by_name\":\"Fred Calos\",\"noted_by_title\":\"MSWDO Head\"}', 0, 0, 0, 0, NULL, NULL, '2026-10-15 06:18:10', 1, NULL, NULL, '2026-10-15 06:12:14', '2026-10-15 08:09:44'),
(2, 2, 2, 'MSWD-O-2026-10-0002', '2026-10-15', NULL, 'Medical Assistance', 'rhainy', NULL, 'redona', NULL, 'Rhainy  Redona', '2005-08-13', 21, 'Female', 'Single', 'QUIBOLOY', 'Silang Namik', 'Under Graduate', 'Tindero', '10,000', 'Bulihan', 'Bulihan', '09223086018', 'DOH, DSWD, MSWDO, PCSO, OP', 2, 'Review', 'eligible', 3, '2026-10-15 06:42:55', NULL, 'Wala napo akong pera pang top up tanga kase ako', '2026-10-15', 'requirements_verification', 1, '{\"prepared_by_name\":\"Marilyn Barameda\",\"prepared_by_title\":\"MSWDO Staff\",\"noted_by_name\":\"Fred Calos\",\"noted_by_title\":\"MSWDO Head\"}', 0, 0, 0, 0, NULL, NULL, NULL, 2, NULL, NULL, '2026-10-15 06:42:55', '2026-10-15 07:56:28'),
(3, 3, 2, 'MSWD-O-2026-10-0003', '2026-10-15', NULL, 'Medical Assistance', 'nick', NULL, 'hernandez', NULL, 'Nick  Hernandez', '1995-08-13', 31, 'Male', 'Married', 'INC', 'New Era', 'College Grad', 'Teacher', '25,000', 'Bulihan', 'Bulihan', '09223086018', 'DOH, PCSO', 2, 'Released', 'eligible', 3, '2026-10-15 08:06:15', NULL, 'Need pera for alak', '2026-10-16', 'requirements_verification', 1, '{\"prepared_by_name\":\"Marilyn Barameda\",\"prepared_by_title\":\"MSWDO Staff\",\"noted_by_name\":\"Fred Calos\",\"noted_by_title\":\"MSWDO Head\"}', 0, 0, 0, 0, NULL, NULL, '2026-10-15 08:09:14', 3, NULL, NULL, '2026-10-15 08:06:15', '2026-10-15 08:09:14'),
(4, 4, 2, 'MSWD-O-2026-10-0004', '2026-10-15', NULL, NULL, 'elaiza', NULL, 'sumaylo', NULL, 'Elaiza  Sumaylo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Draft', 'eligible', 3, '2026-10-15 08:24:29', NULL, NULL, NULL, 'requirements_verification', 0, NULL, 0, 0, 0, 0, NULL, NULL, NULL, 4, NULL, NULL, '2026-10-15 08:24:29', '2026-10-15 08:24:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'staff',
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `signature_image` varchar(500) DEFAULT NULL,
  `signature_position` enum('osca_head','mswdo_officer','mswdo_staff') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `phone`, `position`, `employee_id`, `address`, `status`, `signature_image`, `signature_position`, `created_at`, `updated_at`) VALUES
(1, 'Fred Calos', 'fredcalos@gmail.com', NULL, '$2y$12$JFF8f2yJky/5lLQNDyoh8eUDIVuwBdQgwT3NtH55.Dj8AKky0MWwK', NULL, 'admin', NULL, 'MSWDO Administrator', NULL, NULL, 'active', NULL, 'mswdo_officer', '2026-10-15 05:59:44', '2026-10-15 05:59:44'),
(2, 'Marilyn Barameda', 'Marilyn@gmail.com', NULL, '$2y$12$PvxtkAXBQ.SbZmTCczmU7e.u9VmL7xnjG9T3uVPJJ88rk0xE1Ugwi', NULL, 'social_worker', '09223086018', NULL, NULL, NULL, 'active', NULL, NULL, '2026-10-15 06:02:29', '2026-10-15 06:02:29'),
(3, 'Christopher Social', 'Christopher@gmail.com', NULL, '$2y$12$WFjQHzjyHaSMMIn2NKoe9ON7Mt.VgTmfCwHwyrsDLB0BG9bPg0m0m', NULL, 'eligibility_checker', '09171234567', NULL, NULL, NULL, 'active', NULL, NULL, '2026-10-15 06:03:46', '2026-10-15 06:03:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assistance_records`
--
ALTER TABLE `assistance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assistance_records_client_id_index` (`client_id`),
  ADD KEY `assistance_records_social_case_study_id_index` (`social_case_study_id`),
  ADD KEY `assistance_records_client_id_release_date_index` (`client_id`,`release_date`);

--
-- Indexes for table `beneficiary_intakes`
--
ALTER TABLE `beneficiary_intakes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `beneficiary_intakes_control_number_unique` (`control_number`),
  ADD KEY `beneficiary_intakes_encoder_foreign` (`encoder`),
  ADD KEY `beneficiary_intakes_client_id_index` (`client_id`),
  ADD KEY `beneficiary_intakes_social_case_study_id_index` (`social_case_study_id`),
  ADD KEY `idx_ben_names` (`beneficiary_last_name`,`beneficiary_first_name`),
  ADD KEY `idx_ben_dob` (`beneficiary_birthday`),
  ADD KEY `idx_rep_names` (`rep_last_name`,`rep_first_name`),
  ADD KEY `idx_rep_dob` (`rep_birthday`),
  ADD KEY `idx_date_processed` (`date_processed`),
  ADD KEY `beneficiary_intakes_is_payroll_generated_index` (`is_payroll_generated`),
  ADD KEY `beneficiary_intakes_payroll_date_index` (`payroll_date`),
  ADD KEY `beneficiary_intakes_payroll_record_id_foreign` (`payroll_record_id`),
  ADD KEY `beneficiary_intakes_claim_status_index` (`claim_status`),
  ADD KEY `beneficiary_intakes_claiming_date_index` (`claiming_date`),
  ADD KEY `beneficiary_intakes_is_archived_index` (`is_archived`),
  ADD KEY `beneficiary_intakes_archived_at_index` (`archived_at`),
  ADD KEY `beneficiary_intakes_archived_by_index` (`archived_by`),
  ADD KEY `beneficiary_intakes_archive_module_index` (`archive_module`),
  ADD KEY `beneficiary_intakes_is_historical_index` (`is_historical`),
  ADD KEY `beneficiary_intakes_assistance_type_index` (`assistance_type`);

--
-- Indexes for table `birthday_payouts`
--
ALTER TABLE `birthday_payouts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_senior_payout_year` (`senior_id`,`payout_year`),
  ADD KEY `birthday_payouts_released_by_foreign` (`released_by`),
  ADD KEY `birthday_payouts_status_index` (`status`);

--
-- Indexes for table `birthday_payout_history`
--
ALTER TABLE `birthday_payout_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `birthday_payout_history_performed_by_foreign` (`performed_by`),
  ADD KEY `birthday_payout_history_payout_id_index` (`payout_id`),
  ADD KEY `birthday_payout_history_senior_id_index` (`senior_id`),
  ADD KEY `birthday_payout_history_action_index` (`action`),
  ADD KEY `birthday_payout_history_created_at_index` (`created_at`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `case_interviews`
--
ALTER TABLE `case_interviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_interviews_social_case_study_id_unique` (`social_case_study_id`);

--
-- Indexes for table `case_rejections`
--
ALTER TABLE `case_rejections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_rejections_client_id_blocking_assistance_id_unique` (`client_id`,`blocking_assistance_id`),
  ADD KEY `case_rejections_blocking_assistance_id_index` (`blocking_assistance_id`),
  ADD KEY `case_rejections_social_case_study_id_index` (`social_case_study_id`),
  ADD KEY `case_rejections_officer_id_index` (`officer_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `clients_last_name_first_name_index` (`last_name`,`first_name`),
  ADD KEY `clients_barangay_index` (`barangay`);

--
-- Indexes for table `document_reference_counters`
--
ALTER TABLE `document_reference_counters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `document_reference_counters_type_unique` (`type`);

--
-- Indexes for table `eligibility_audit_logs`
--
ALTER TABLE `eligibility_audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eligibility_audit_logs_client_id_index` (`client_id`),
  ADD KEY `eligibility_audit_logs_officer_id_index` (`officer_id`);

--
-- Indexes for table `email_login_codes`
--
ALTER TABLE `email_login_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email_login_codes_email_code_index` (`email`,`code`),
  ADD KEY `email_login_codes_user_id_index` (`user_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `family_members`
--
ALTER TABLE `family_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `family_members_social_case_study_id_index` (`social_case_study_id`),
  ADD KEY `family_members_person_id_foreign` (`person_id`);

--
-- Indexes for table `financial_assistance_applications`
--
ALTER TABLE `financial_assistance_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `financial_assistance_applications_application_number_unique` (`application_number`),
  ADD KEY `financial_assistance_applications_client_id_index` (`client_id`),
  ADD KEY `financial_assistance_applications_status_index` (`status`),
  ADD KEY `financial_assistance_applications_created_by_index` (`created_by`);

--
-- Indexes for table `financial_assistance_messages`
--
ALTER TABLE `financial_assistance_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `financial_assistance_messages_client_id_foreign` (`client_id`),
  ADD KEY `financial_assistance_messages_intake_id_created_at_index` (`intake_id`,`created_at`),
  ADD KEY `financial_assistance_messages_recipient_contact_number_index` (`recipient_contact_number`),
  ADD KEY `financial_assistance_messages_status_index` (`status`);

--
-- Indexes for table `financial_payroll_records`
--
ALTER TABLE `financial_payroll_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `financial_payroll_records_payroll_number_unique` (`payroll_number`),
  ADD KEY `financial_payroll_records_generated_by_id_foreign` (`generated_by_id`),
  ADD KEY `financial_payroll_records_payroll_date_created_at_index` (`payroll_date`,`created_at`),
  ADD KEY `financial_payroll_records_payroll_date_index` (`payroll_date`),
  ADD KEY `financial_payroll_records_batch_number_index` (`batch_number`);

--
-- Indexes for table `in_between_benefit_configurations`
--
ALTER TABLE `in_between_benefit_configurations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `in_between_benefit_configurations_created_by_foreign` (`created_by`),
  ADD KEY `in_between_benefit_configurations_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `in_between_benefit_history`
--
ALTER TABLE `in_between_benefit_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_senior_interval` (`senior_id`,`eligibility_interval`),
  ADD UNIQUE KEY `in_between_benefit_history_reference_number_unique` (`reference_number`),
  ADD KEY `in_between_benefit_history_processed_by_foreign` (`processed_by`),
  ADD KEY `in_between_benefit_history_approved_by_foreign` (`approved_by`),
  ADD KEY `in_between_benefit_history_status_index` (`status`),
  ADD KEY `in_between_benefit_history_eligibility_interval_index` (`eligibility_interval`),
  ADD KEY `in_between_benefit_history_birthday_year_index` (`birthday_year`),
  ADD KEY `in_between_benefit_history_payout_date_index` (`payout_date`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_financial_intakes`
--
ALTER TABLE `online_financial_intakes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_financial_intakes_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `online_financial_intakes_status_index` (`status`),
  ADD KEY `online_financial_intakes_date_submitted_index` (`date_submitted`),
  ADD KEY `online_financial_intakes_control_number_index` (`control_number`),
  ADD KEY `online_financial_intakes_beneficiary_barangay_index` (`beneficiary_barangay`),
  ADD KEY `online_financial_intakes_beneficiary_intake_id_index` (`beneficiary_intake_id`);

--
-- Indexes for table `online_requests`
--
ALTER TABLE `online_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_requests_case_id_foreign` (`case_id`);

--
-- Indexes for table `online_request_attachments`
--
ALTER TABLE `online_request_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `online_request_attachments_online_request_id_foreign` (`online_request_id`);

--
-- Indexes for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `password_reset_requests_token_unique` (`token`),
  ADD KEY `password_reset_requests_processed_by_foreign` (`processed_by`),
  ADD KEY `password_reset_requests_email_index` (`email`),
  ADD KEY `password_reset_requests_token_index` (`token`),
  ADD KEY `password_reset_requests_status_index` (`status`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `public_applicants`
--
ALTER TABLE `public_applicants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_applicants_contact_number_unique` (`contact_number`),
  ADD KEY `public_applicants_status_index` (`status`);

--
-- Indexes for table `public_requests`
--
ALTER TABLE `public_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `public_requests_request_number_unique` (`request_number`),
  ADD UNIQUE KEY `public_requests_confirmed_appointment_at_unique` (`confirmed_appointment_at`),
  ADD UNIQUE KEY `public_requests_request_token_unique` (`request_token`),
  ADD KEY `public_requests_eligibility_checked_by_foreign` (`eligibility_checked_by`),
  ADD KEY `public_requests_client_id_foreign` (`client_id`),
  ADD KEY `public_requests_converted_by_foreign` (`converted_by`),
  ADD KEY `public_requests_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `public_requests_applicant_id_index` (`applicant_id`),
  ADD KEY `public_requests_request_status_index` (`request_status`),
  ADD KEY `public_requests_eligibility_status_index` (`eligibility_status`),
  ADD KEY `public_requests_appointment_status_index` (`appointment_status`),
  ADD KEY `prq_beneficiary_name_idx` (`beneficiary_last_name`,`beneficiary_first_name`),
  ADD KEY `public_requests_beneficiary_birthdate_index` (`beneficiary_birthdate`),
  ADD KEY `public_requests_beneficiary_barangay_index` (`beneficiary_barangay`),
  ADD KEY `prq_social_case_idx` (`social_case_study_id`);

--
-- Indexes for table `public_request_activities`
--
ALTER TABLE `public_request_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `public_request_activities_public_request_id_index` (`public_request_id`),
  ADD KEY `public_request_activities_action_index` (`action`),
  ADD KEY `public_request_activities_applicant_id_index` (`applicant_id`),
  ADD KEY `public_request_activities_staff_id_index` (`staff_id`);

--
-- Indexes for table `senior_activity_logs`
--
ALTER TABLE `senior_activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `senior_citizen_records`
--
ALTER TABLE `senior_citizen_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `senior_citizen_records_record_number_unique` (`record_number`),
  ADD UNIQUE KEY `senior_citizen_records_control_number_unique` (`control_number`),
  ADD UNIQUE KEY `senior_citizen_records_senior_id_number_unique` (`senior_id_number`),
  ADD KEY `senior_citizen_records_created_by_foreign` (`created_by`),
  ADD KEY `senior_citizen_records_barangay_index` (`barangay`),
  ADD KEY `senior_citizen_records_status_index` (`status`),
  ADD KEY `senior_citizen_records_birth_date_index` (`birth_date`),
  ADD KEY `senior_citizen_records_year_applied_index` (`year_applied`),
  ADD KEY `senior_citizen_records_last_name_first_name_index` (`last_name`,`first_name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `social_case_activity_logs`
--
ALTER TABLE `social_case_activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_case_reports`
--
ALTER TABLE `social_case_reports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `social_case_reports_case_number_unique` (`case_number`),
  ADD KEY `social_case_reports_social_case_study_id_index` (`social_case_study_id`),
  ADD KEY `social_case_reports_generated_by_index` (`generated_by`),
  ADD KEY `social_case_reports_created_by_index` (`created_by`);

--
-- Indexes for table `social_case_report_release_logs`
--
ALTER TABLE `social_case_report_release_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `social_case_report_release_logs_social_case_study_id_index` (`social_case_study_id`),
  ADD KEY `social_case_report_release_logs_social_case_report_id_index` (`social_case_report_id`),
  ADD KEY `social_case_report_release_logs_released_by_index` (`released_by`);

--
-- Indexes for table `social_case_studies`
--
ALTER TABLE `social_case_studies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `social_case_studies_case_number_unique` (`case_number`),
  ADD KEY `social_case_studies_released_by_foreign` (`released_by`),
  ADD KEY `social_case_studies_officer_id_index` (`officer_id`),
  ADD KEY `social_case_studies_status_index` (`status`),
  ADD KEY `social_case_studies_workflow_step_index` (`workflow_step`),
  ADD KEY `social_case_studies_encoded_by_index` (`encoded_by`),
  ADD KEY `social_case_studies_eligible_by_foreign` (`eligible_by`),
  ADD KEY `social_case_studies_main_client_id_foreign` (`main_client_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  ADD KEY `users_role_index` (`role`),
  ADD KEY `users_status_index` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assistance_records`
--
ALTER TABLE `assistance_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `beneficiary_intakes`
--
ALTER TABLE `beneficiary_intakes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `birthday_payouts`
--
ALTER TABLE `birthday_payouts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `birthday_payout_history`
--
ALTER TABLE `birthday_payout_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_interviews`
--
ALTER TABLE `case_interviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `case_rejections`
--
ALTER TABLE `case_rejections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `document_reference_counters`
--
ALTER TABLE `document_reference_counters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `eligibility_audit_logs`
--
ALTER TABLE `eligibility_audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `email_login_codes`
--
ALTER TABLE `email_login_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `family_members`
--
ALTER TABLE `family_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `financial_assistance_applications`
--
ALTER TABLE `financial_assistance_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_assistance_messages`
--
ALTER TABLE `financial_assistance_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `financial_payroll_records`
--
ALTER TABLE `financial_payroll_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `in_between_benefit_configurations`
--
ALTER TABLE `in_between_benefit_configurations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `in_between_benefit_history`
--
ALTER TABLE `in_between_benefit_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `online_financial_intakes`
--
ALTER TABLE `online_financial_intakes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_requests`
--
ALTER TABLE `online_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `online_request_attachments`
--
ALTER TABLE `online_request_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `public_applicants`
--
ALTER TABLE `public_applicants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `public_requests`
--
ALTER TABLE `public_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `public_request_activities`
--
ALTER TABLE `public_request_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `senior_activity_logs`
--
ALTER TABLE `senior_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `senior_citizen_records`
--
ALTER TABLE `senior_citizen_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social_case_activity_logs`
--
ALTER TABLE `social_case_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `social_case_reports`
--
ALTER TABLE `social_case_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social_case_report_release_logs`
--
ALTER TABLE `social_case_report_release_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social_case_studies`
--
ALTER TABLE `social_case_studies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assistance_records`
--
ALTER TABLE `assistance_records`
  ADD CONSTRAINT `assistance_records_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `assistance_records_social_case_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `beneficiary_intakes`
--
ALTER TABLE `beneficiary_intakes`
  ADD CONSTRAINT `beneficiary_intakes_archived_by_foreign` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `beneficiary_intakes_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `beneficiary_intakes_encoder_foreign` FOREIGN KEY (`encoder`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `beneficiary_intakes_payroll_record_id_foreign` FOREIGN KEY (`payroll_record_id`) REFERENCES `financial_payroll_records` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `beneficiary_intakes_social_case_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `family_members`
--
ALTER TABLE `family_members`
  ADD CONSTRAINT `family_members_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `financial_assistance_messages`
--
ALTER TABLE `financial_assistance_messages`
  ADD CONSTRAINT `financial_assistance_messages_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `financial_assistance_messages_intake_id_foreign` FOREIGN KEY (`intake_id`) REFERENCES `beneficiary_intakes` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `financial_payroll_records`
--
ALTER TABLE `financial_payroll_records`
  ADD CONSTRAINT `financial_payroll_records_generated_by_id_foreign` FOREIGN KEY (`generated_by_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `in_between_benefit_configurations`
--
ALTER TABLE `in_between_benefit_configurations`
  ADD CONSTRAINT `in_between_benefit_configurations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `in_between_benefit_configurations_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `in_between_benefit_history`
--
ALTER TABLE `in_between_benefit_history`
  ADD CONSTRAINT `in_between_benefit_history_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `in_between_benefit_history_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `in_between_benefit_history_senior_id_foreign` FOREIGN KEY (`senior_id`) REFERENCES `senior_citizen_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `online_financial_intakes`
--
ALTER TABLE `online_financial_intakes`
  ADD CONSTRAINT `online_financial_intakes_beneficiary_intake_id_foreign` FOREIGN KEY (`beneficiary_intake_id`) REFERENCES `beneficiary_intakes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `online_financial_intakes_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `social_case_studies`
--
ALTER TABLE `social_case_studies`
  ADD CONSTRAINT `social_case_studies_main_client_id_foreign` FOREIGN KEY (`main_client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
