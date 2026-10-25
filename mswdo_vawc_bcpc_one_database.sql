-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 25, 2026 at 10:42 AM
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
-- Database: `mswdo_vawc_bcpc_one_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `event` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archived_users`
--

CREATE TABLE `archived_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `position` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `addtl_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`addtl_info`)),
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `original_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `archived_by` bigint(20) UNSIGNED DEFAULT NULL,
  `archive_reason` text DEFAULT NULL,
  `archived_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` int(11) NOT NULL,
  `code` varchar(10) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `code`, `name`) VALUES
(1, 'ACA', 'Acacia'),
(2, 'ADL', 'Adlas'),
(3, 'ANI', 'Anahaw I'),
(4, 'AN2', 'Anahaw II'),
(5, 'BA1', 'Balite I'),
(6, 'BA2', 'Balite II'),
(7, 'BLB', 'Balubad'),
(8, 'BNB', 'Banaba'),
(9, 'BTS', 'Batas'),
(10, 'BG1', 'Biga I'),
(11, 'BG2', 'Biga II'),
(12, 'BIL', 'Biluso'),
(13, 'BUC', 'Bucal'),
(14, 'BUH', 'Buho'),
(15, 'BUL', 'Bulihan'),
(16, 'CBG', 'Cabangaan'),
(17, 'CRM', 'Carmen'),
(18, 'HOY', 'Hoyo'),
(19, 'HKY', 'Hukay'),
(20, 'IBA', 'Iba'),
(21, 'INC', 'Inchican'),
(22, 'IPI', 'Ipil I'),
(23, 'IP2', 'Ipil II'),
(24, 'KLB', 'Kalubkob'),
(25, 'KNG', 'Kaong'),
(26, 'LL1', 'Lalaan I'),
(27, 'LL2', 'Lalaan II'),
(28, 'LIT', 'Litlit'),
(29, 'LUC', 'Lucsuhin'),
(30, 'LUM', 'Lumil'),
(31, 'MGY', 'Maguyam'),
(32, 'MLB', 'Malabag'),
(33, 'MTT', 'Malaking Tatiao'),
(34, 'MTB', 'Mataas na Burol'),
(35, 'MIL', 'Munting Ilog'),
(36, 'NR1', 'Narra I'),
(37, 'NR2', 'Narra II'),
(38, 'NR3', 'Narra III'),
(39, 'PLG', 'Paligawan'),
(40, 'PSL', 'Pasong Langka'),
(41, 'PB1', 'Poblacion I'),
(42, 'PB2', 'Poblacion II'),
(43, 'PB3', 'Poblacion III'),
(44, 'PB4', 'Poblacion IV'),
(45, 'PB5', 'Poblacion V'),
(46, 'PC1', 'Pooc I'),
(47, 'PC2', 'Pooc II'),
(48, 'PLB', 'Pulong Bunga'),
(49, 'PLS', 'Pulong Saging'),
(50, 'PTK', 'Puting Kahoy'),
(51, 'SBT', 'Sabutan'),
(52, 'SM1', 'San Miguel I'),
(53, 'SM2', 'San Miguel II'),
(54, 'SV1', 'San Vicente I'),
(55, 'SV2', 'San Vicente II'),
(56, 'STL', 'Santol'),
(57, 'TRT', 'Tartaria'),
(58, 'TBG', 'Tibig'),
(59, 'TLD', 'Toledo'),
(60, 'TB1', 'Tubuan I'),
(61, 'TB2', 'Tubuan II'),
(62, 'TB3', 'Tubuan III'),
(63, 'ULT', 'Ulat'),
(64, 'YKL', 'Yakal'),
(65, 'TEST', 'Test Barangay');

-- --------------------------------------------------------

--
-- Table structure for table `bcpc_case_monitoring`
--

CREATE TABLE `bcpc_case_monitoring` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `barangay_id` int(10) UNSIGNED NOT NULL,
  `quarter` tinyint(3) UNSIGNED NOT NULL,
  `year` smallint(5) UNSIGNED NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`data`)),
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `archived_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `submitted_by` bigint(20) UNSIGNED DEFAULT NULL
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
-- Table structure for table `cases`
--

CREATE TABLE `cases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `case_designation` int(255) NOT NULL,
  `case_number` varchar(255) NOT NULL,
  `assigned_to` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `incident_time` time NOT NULL,
  `date_reported` date NOT NULL,
  `abuse_type` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `perpetrator_name` varchar(255) DEFAULT NULL,
  `relationship_to_victim` varchar(255) DEFAULT NULL,
  `case_details` text NOT NULL,
  `status` varchar(255) NOT NULL,
  `priority` enum('Urgent','Emergency','Non-urgent') NOT NULL DEFAULT 'Non-urgent',
  `victim_name` varchar(255) NOT NULL,
  `victim_age` int(11) NOT NULL,
  `is_pwd_victim` tinyint(1) NOT NULL DEFAULT 0,
  `victim_gender` varchar(255) NOT NULL,
  `victim_contact` varchar(255) DEFAULT NULL,
  `victim_address` text NOT NULL,
  `guardian` varchar(255) DEFAULT NULL,
  `guardian_contact` varchar(255) DEFAULT NULL,
  `guardian_address` text DEFAULT NULL,
  `abuser_name` varchar(255) NOT NULL,
  `abuser_age` int(11) DEFAULT NULL,
  `abuse_relationship` varchar(255) NOT NULL,
  `incident_location` text DEFAULT NULL,
  `incident_details` text DEFAULT NULL,
  `witness_name` varchar(255) DEFAULT NULL,
  `witness_contact` varchar(255) DEFAULT NULL,
  `resolution_date` date DEFAULT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `guardian_abuser_name` varchar(255) DEFAULT NULL,
  `guardian_abuser_contact` varchar(255) DEFAULT NULL,
  `guardian_abuser_address` varchar(255) DEFAULT NULL,
  `relationship_to_abuser` varchar(255) DEFAULT NULL,
  `abuser_address` text DEFAULT NULL,
  `bcpc_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`bcpc_data`))
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

-- --------------------------------------------------------

--
-- Table structure for table `case_notes`
--

CREATE TABLE `case_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `case_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `note` text NOT NULL,
  `is_important` tinyint(1) NOT NULL DEFAULT 0,
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`attachments`)),
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_referrals`
--

CREATE TABLE `case_referrals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `case_id` bigint(20) UNSIGNED NOT NULL,
  `referral_number` varchar(255) NOT NULL,
  `date_of_referral` date DEFAULT NULL,
  `referred_by_name` varchar(255) DEFAULT NULL,
  `referred_by_designation` varchar(255) NOT NULL,
  `referred_by_address` varchar(255) NOT NULL,
  `referred_by_contact` varchar(255) DEFAULT NULL,
  `referred_by_email` varchar(255) DEFAULT NULL,
  `recipient_name` varchar(255) DEFAULT NULL,
  `recipient_address` varchar(255) NOT NULL,
  `recipient_contact` varchar(255) DEFAULT NULL,
  `recipient_email` varchar(255) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `client_age` int(11) DEFAULT NULL,
  `client_sex` varchar(255) DEFAULT NULL,
  `client_address` varchar(255) NOT NULL,
  `client_contact` varchar(255) DEFAULT NULL,
  `family_guardian_name` varchar(255) DEFAULT NULL,
  `family_guardian_contact` varchar(255) DEFAULT NULL,
  `family_guardian_address` text DEFAULT NULL,
  `reason_for_referral` text NOT NULL,
  `specific_services_requested` text DEFAULT NULL,
  `additional_notes` text DEFAULT NULL,
  `status` enum('Pending','Accepted','Rejected','Completed') DEFAULT 'Pending',
  `feedback` text DEFAULT NULL,
  `feedback_received_at` timestamp NULL DEFAULT NULL,
  `referred_by_user_id` bigint(20) UNSIGNED NOT NULL,
  `created_by_department_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `case_statuses`
--

CREATE TABLE `case_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `color` varchar(20) NOT NULL DEFAULT '#6c757d',
  `description` text DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_status_histories`
--

CREATE TABLE `case_status_histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `case_id` bigint(20) UNSIGNED NOT NULL,
  `action_type` varchar(255) NOT NULL,
  `old_status` varchar(255) DEFAULT NULL,
  `new_status` varchar(255) DEFAULT NULL,
  `old_priority` varchar(255) DEFAULT NULL,
  `new_priority` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `performed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `case_types`
--

CREATE TABLE `case_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `republic_act` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `case_types`
--

INSERT INTO `case_types` (`id`, `republic_act`, `name`) VALUES
(12, 'Others', 'Others / Other Offenses'),
(7, 'RA 11313', 'Safe Spaces Act'),
(8, 'RA 11596', 'Prohibition of Child Marriage'),
(1, 'RA 11930', 'Anti-Online Sexual Abuse or Exploitation of Children (OSAEC) and Anti-Child Sexual Abuse or Exploitation Materials (CSAEM)'),
(4, 'RA 7610', 'Special Protection of Children Against Abuse, Exploitation, and Discrimination Act'),
(6, 'RA 7877', 'Anti-Sexual Harassment Act'),
(3, 'RA 8353', 'The Anti-Rape Law of 1997'),
(5, 'RA 9208/10364', 'Anti-Trafficking in Persons Act'),
(2, 'RA 9262', 'Anti-Violence Against Women and Their Children (VAWC)'),
(10, 'RA 9344', 'Juvenile Justice and Welfare Act'),
(9, 'RA 9775', 'Anti-Child Pornography Act'),
(11, 'Suicide', 'Depression, Suicide Awareness/Prevention');

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
  `gender` enum('Male','Female') DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `birthplace` varchar(255) DEFAULT NULL,
  `religion` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `income` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `email_login_codes`
--

CREATE TABLE `email_login_codes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `auth_source` varchar(255) NOT NULL DEFAULT 'mswdo',
  `email` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `event_date` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `target_audience` enum('all','admins_only','bcpc_only','vawc_only') NOT NULL DEFAULT 'all',
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
  `suffix` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `relationship` varchar(100) NOT NULL,
  `age` tinyint(3) UNSIGNED DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `monthly_income` varchar(100) DEFAULT NULL,
  `is_dependent` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `online_intake_id` bigint(20) UNSIGNED DEFAULT NULL,
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

--
-- Dumping data for table `in_between_benefit_configurations`
--

INSERT INTO `in_between_benefit_configurations` (`id`, `benefit_name`, `description`, `benefit_amount`, `milestone_ages`, `eligible_intervals`, `is_active`, `effective_date`, `expiry_date`, `ordinance_reference`, `eligibility_rules`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'In-Between Birthday Cash Gift', 'Municipality of Silang In-Between Birthday Cash Gift for Senior Citizens', 1000.00, '[80,85,90,95,100]', '[\"81-84\",\"86-89\",\"91-94\",\"96-99\"]', 1, '2026-10-24', NULL, NULL, NULL, NULL, NULL, '2026-10-23 20:16:13', '2026-10-23 20:16:13');

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
(2, '2026_07_20_000001_make_client_fields_nullable', 2),
(3, '2026_07_20_000002_add_missing_intake_fields', 3),
(4, '2026_07_20_000003_add_signers_reportdate_civilstatus', 4),
(5, '2026_07_20_000002_add_age_to_clients', 5),
(6, '2026_07_26_000001_add_intake_sheet_fields_to_beneficiary_intakes_table', 6),
(7, '2026_07_26_000002_add_hardcopy_fields_to_beneficiary_intakes_table', 6),
(8, '2026_07_27_000001_create_senior_activity_logs_table', 6),
(9, '2026_07_30_000001_add_indexes_for_duplicate_checking_to_beneficiary_intakes', 6),
(10, '2026_07_30_000001_create_social_case_activity_logs_table', 6),
(11, '2026_07_30_000002_modify_role_column_in_users_table', 6),
(12, '2026_08_06_000001_create_email_login_codes_table', 6),
(13, '2026_08_07_072038_add_extra_fields_to_clients_table', 7),
(14, '2026_08_07_072501_add_signers_to_social_case_studies_table', 7),
(15, '2026_08_07_072549_add_education_to_family_members_table', 7),
(16, '2026_08_17_000001_add_eligibility_workflow_to_social_case_studies_table', 7),
(17, '2026_08_17_111354_make_clients_birthdate_nullable', 7),
(18, '2026_08_17_111502_make_clients_name_only_profile_columns_nullable', 7),
(19, '2026_08_17_130000_make_family_members_monthly_income_string', 7),
(20, '2026_08_19_062306_create_password_reset_requests_table', 7),
(21, '2026_08_19_093000_add_requested_from_url_to_password_reset_requests_table', 7),
(22, '2026_08_20_040418_create_online_requests_table', 7),
(23, '2026_08_20_041500_add_case_id_to_online_requests_table', 7),
(24, '2026_08_20_050247_create_online_request_attachments_table', 7),
(25, '2026_08_20_060000_add_mswdo_staff_to_users_signature_position', 7),
(26, '2026_09_02_000001_create_financial_payroll_records_table', 8),
(27, '2026_09_03_000001_add_payroll_generation_fields_to_beneficiary_intakes_table', 8),
(28, '2026_09_03_000002_add_batch_and_payroll_record_relations', 8),
(29, '2026_09_05_000001_add_claim_status_to_beneficiary_intakes_table', 8),
(30, '2026_09_09_000001_add_claiming_date_to_beneficiary_intakes_table', 9),
(31, '2026_09_09_000002_create_financial_assistance_messages_table', 9),
(32, '2026_09_06_075830_create_document_reference_counter_table', 10),
(33, '2026_09_11_051153_create_in_between_benefit_configurations_table', 10),
(34, '2026_09_11_051218_create_in_between_benefit_history_table', 10),
(35, '2026_09_11_112941_add_is_exported_to_in_between_benefit_history_table', 11),
(36, '2026_09_14_120001_add_suffix_to_clients_table', 11),
(37, '2026_09_14_120002_add_main_client_and_intake_snapshot_to_social_case_studies_table', 11),
(38, '2026_09_14_120003_add_person_id_and_name_fields_to_family_members_table', 11),
(39, '2026_09_14_120004_backfill_main_client_relations', 11),
(40, '2026_09_14_120005_make_main_client_id_not_null', 11),
(41, '2026_09_14_120006_drop_client_id_from_social_case_studies', 11),
(43, '2026_09_15_000001_create_online_financial_intakes_table', 12),
(44, '2026_09_16_000001_add_archive_fields_to_beneficiary_intakes_table', 13),
(45, '2026_09_22_000001_add_is_historical_to_beneficiary_intakes_table', 14),
(46, '2026_10_10_000001_add_assistance_type_to_beneficiary_intakes_table', 15),
(47, '2026_09_06_075831_add_document_ref_number_to_social_case_studies_table', 16),
(48, '2026_10_13_110000_update_control_number_format_to_monthly', 16),
(49, '2026_10_16_000001_add_auth_source_to_email_login_codes_table', 16),
(50, '2026_10_16_180000_add_sms_notification_fields_to_online_intakes_and_messages', 16),
(51, '2026_10_17_000001_add_department_activation_flags_to_wcpu_users', 16),
(52, '2026_10_17_000002_make_wcpu_user_barangay_nullable', 16),
(53, '2026_10_17_000003_create_wcpu_events_table', 16),
(54, '2026_10_19_000001_add_deleted_at_to_report_recipients_table', 16),
(55, '2026_10_19_000002_add_archived_at_to_quarterly_reports_table', 16),
(56, '2026_10_19_000003_create_or_update_bcpc_case_monitoring_table', 16),
(57, '2026_10_19_000004_add_priority_and_missing_columns_to_cases_table', 16),
(58, '2026_10_19_000005_add_username_to_wcpu_users_table', 16);

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
  `sms_status` varchar(30) DEFAULT NULL,
  `sms_error` text DEFAULT NULL,
  `sms_notified_at` timestamp NULL DEFAULT NULL,
  `approval_sms_sent_at` timestamp NULL DEFAULT NULL,
  `rejection_sms_sent_at` timestamp NULL DEFAULT NULL,
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
-- Table structure for table `quarterly_reports`
--

CREATE TABLE `quarterly_reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `barangay_id` int(11) NOT NULL,
  `quarter` tinyint(4) NOT NULL,
  `year` smallint(6) NOT NULL,
  `report_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`report_data`)),
  `pdf_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `archived_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_recipients`
--

CREATE TABLE `report_recipients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `is_system` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
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
('22XMZLzBTuiPputtRHDjTvnlSPecn0ACv6iJMmDC', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/156.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR0hEWmhKY2Nac3dxV05pTUk5VHBwSno0SE1JQllleTJOZkp4TllRUSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9fQ==', 1792819663),
('NNmLe1Lj47p1FBmTMhqLQx9dIcqMrI4Dp3fa441O', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiWUxqN245SlJ5NHB0R1pUdE1RUUZud2ZyTk9FRmVUSGd0SFM3SVJZZyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTU6ImFkbWluLmRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MTM6ImFkbWluX3VzZXJfaWQiO2k6MTtzOjE1OiJhZG1pbl91c2VyX25hbWUiO3M6MTQ6IkZyZWRyaWNrIENhbG9zIjtzOjE1OiJhZG1pbl91c2VyX3JvbGUiO3M6NToiYWRtaW4iO3M6MTE6ImF1dGhfc291cmNlIjtzOjU6Im1zd2RvIjt9', 1792825921);

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
  `intake_birthdate` date DEFAULT NULL,
  `intake_age` tinyint(3) UNSIGNED DEFAULT NULL,
  `intake_gender` varchar(20) DEFAULT NULL,
  `intake_barangay` varchar(255) DEFAULT NULL,
  `intake_address` text DEFAULT NULL,
  `intake_contact_number` varchar(20) DEFAULT NULL,
  `intake_income` varchar(255) DEFAULT NULL,
  `intake_occupation` varchar(255) DEFAULT NULL,
  `intake_education` varchar(255) DEFAULT NULL,
  `intake_birthplace` varchar(255) DEFAULT NULL,
  `intake_religion` varchar(255) DEFAULT NULL,
  `intake_civil_status` varchar(255) DEFAULT NULL,
  `intake_full_name` varchar(255) DEFAULT NULL,
  `intake_suffix` varchar(255) DEFAULT NULL,
  `intake_last_name` varchar(255) DEFAULT NULL,
  `intake_middle_name` varchar(255) DEFAULT NULL,
  `intake_first_name` varchar(255) DEFAULT NULL,
  `service_provided` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `submitted_to` varchar(255) DEFAULT NULL,
  `encoded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Open',
  `eligibility_status` enum('pending','eligible','ineligible') NOT NULL DEFAULT 'pending',
  `eligible_by` bigint(20) UNSIGNED DEFAULT NULL,
  `eligible_at` timestamp NULL DEFAULT NULL,
  `ineligible_reason` text DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `interview_date` date DEFAULT NULL,
  `workflow_step` varchar(50) NOT NULL DEFAULT 'requirements_verification',
  `document_ref_number` int(10) UNSIGNED DEFAULT NULL,
  `requirements_complete` tinyint(1) NOT NULL DEFAULT 0,
  `interview_complete` tinyint(1) NOT NULL DEFAULT 0,
  `evaluation_complete` tinyint(1) NOT NULL DEFAULT 0,
  `report_generated` tinyint(1) NOT NULL DEFAULT 0,
  `assistance_released` tinyint(1) NOT NULL DEFAULT 0,
  `assistance_amount` decimal(10,2) DEFAULT NULL,
  `assistance_date` date DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `released_by` bigint(20) UNSIGNED DEFAULT NULL,
  `released_to` varchar(255) DEFAULT NULL,
  `signers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`signers`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, 'Fredrick Calos', 'fredcalos@gmail.com', NULL, '$2y$12$5SUHD2rDk9A4eJPaGIb28ecNfhmgVuo6IR2qFVXJ11JsTXxp3MQoa', NULL, 'admin', NULL, 'MSWDO Administrator', NULL, NULL, 'active', NULL, 'mswdo_officer', '2026-10-23 20:16:13', '2026-10-23 20:16:13');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vawc_cache`
--

CREATE TABLE `vawc_cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vawc_cache_locks`
--

CREATE TABLE `vawc_cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vawc_failed_jobs`
--

CREATE TABLE `vawc_failed_jobs` (
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
-- Table structure for table `vawc_jobs`
--

CREATE TABLE `vawc_jobs` (
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
-- Table structure for table `vawc_job_batches`
--

CREATE TABLE `vawc_job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `vawc_failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vawc_migrations`
--

CREATE TABLE `vawc_migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vawc_migrations`
--

INSERT INTO `vawc_migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_10_04_000000_create_archived_users_table', 1),
(2, '2024_01_01_000000_create_report_recipients_table', 2),
(4, '2025_10_19_181926_update_pending_cases_to_case_reported', 3),
(5, '2025_10_19_192304_update_referred_cases_to_case_referred', 4),
(6, '2025_01_24_000000_create_case_referrals_table', 5),
(7, '2025_12_01_000000_add_abuser_guardian_fields_to_cases_table', 6),
(8, '2025_08_27_165254_create_activity_log_table', 1),
(9, '2025_08_27_165255_add_event_column_to_activity_log_table', 1),
(10, '2025_08_27_165256_add_batch_uuid_column_to_activity_log_table', 1),
(11, '2025_10_08_000001_make_victim_email_nullable_in_cases_table', 7),
(12, '2025_12_06_000001_add_notifications_last_viewed_to_users_table', 7),
(13, '2025_12_06_141348_create_password_reset_requests_table', 8),
(14, '2025_12_06_141500_add_read_notifications_to_users_table', 9),
(15, '2025_12_07_170815_add_login_lockout_columns_to_users_table', 10),
(16, '2025_12_07_172734_add_account_locked_column_to_users_table', 11),
(17, '2026_01_18_083000_add_contact_number_to_report_recipients_table', 12),
(18, '2026_07_11_001440_add_new_password_to_password_reset_requests_table', 13),
(19, '2026_07_08_000001_add_type_to_quarterly_reports_table', 14),
(20, '2026_02_08_234154_create_quarterly_reports_table', 14),
(21, '2026_07_15_193500_add_guardian_fields_to_cases_table', 14),
(22, '2026_07_16_230450_add_priority_to_cases_table', 15),
(24, '2026_07_20_192737_create_case_status_histories_table', 16),
(25, '2026_07_30_191110_add_event_type_to_case_status_histories_table', 17),
(26, '2026_07_30_211447_create_case_histories_table', 18),
(27, '2026_08_07_000000_create_events_table', 19),
(30, '2026_08_07_000001_add_end_time_to_events_table', 20),
(31, '2026_09_07_183507_add_department_active_columns_to_users_table', 21),
(32, '2026_09_08_000000_create_bcpc_case_monitoring_table', 22),
(33, '2026_09_08_000001_update_report_recipients_and_case_priority', 22),
(34, '2026_09_11_011500_make_case_referral_edit_fields_nullable', 23),
(35, '2026_09_11_013000_add_case_department_active_flags_to_users_table', 24),
(36, '2026_09_11_014000_sync_multi_department_user_login_status', 25),
(37, '2026_09_11_000001_add_archived_at_to_quarterly_reports_table', 26),
(38, '2026_09_11_220800_make_username_nullable_on_users_table', 26),
(39, '2026_09_12_000002_add_archived_at_to_bcpc_case_monitoring_table', 27),
(40, '2026_09_12_234500_add_others_to_case_types_table', 28),
(41, '2026_09_13_000001_rename_case_reported_to_case_filed', 29),
(42, '2026_09_13_000002_add_abuser_address_to_cases', 29),
(43, '2026_09_13_000003_add_soft_deletes_to_report_recipients', 29),
(44, '2026_09_13_000004_add_submission_to_bcpc_case_monitoring', 29),
(45, '2026_09_14_000000_allow_reuse_of_archived_user_credentials', 29),
(46, '2026_09_15_000001_backfill_vawc_case_referrals', 29),
(47, '2026_09_15_000002_add_vawc_referral_recipients', 29),
(48, '2026_09_15_000003_restore_charlot_bcpc_recipient', 29),
(49, '2026_09_15_000004_set_bcpc_referral_recipient_names', 29),
(50, '2026_09_16_000000_rename_bcpc_complete_case_status', 29),
(51, '2026_09_18_000000_create_users_table_if_missing', 29),
(52, '2026_09_27_000000_make_user_barangay_nullable_for_administrators', 30),
(53, '2026_09_30_000000_add_is_pwd_victim_to_cases_table', 31),
(54, '2026_10_03_000000_add_creator_role_snapshot_to_case_referrals', 32);

-- --------------------------------------------------------

--
-- Table structure for table `vawc_password_reset_requests`
--

CREATE TABLE `vawc_password_reset_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `resolved_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `new_password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vawc_password_reset_tokens`
--

CREATE TABLE `vawc_password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vawc_sessions`
--

CREATE TABLE `vawc_sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vawc_users`
--

CREATE TABLE `vawc_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL DEFAULT '',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `barangay` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `bcpc_is_active` tinyint(1) NOT NULL DEFAULT 1,
  `vawc_is_active` tinyint(1) NOT NULL DEFAULT 1,
  `vawc_active` tinyint(1) NOT NULL DEFAULT 1,
  `bcpc_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(255) DEFAULT NULL,
  `notifications_last_viewed_at` timestamp NULL DEFAULT NULL,
  `read_notifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`read_notifications`)),
  `failed_login_attempts` int(11) NOT NULL DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `account_locked` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject` (`subject_type`,`subject_id`),
  ADD KEY `causer` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`),
  ADD KEY `user_cons` (`causer_id`),
  ADD KEY `user_create` (`subject_id`);

--
-- Indexes for table `archived_users`
--
ALTER TABLE `archived_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `archived_users_archived_by_foreign` (`archived_by`),
  ADD KEY `archived_users_email_index` (`email`),
  ADD KEY `archived_users_original_user_id_index` (`original_user_id`),
  ADD KEY `archived_users_archived_at_index` (`archived_at`);

--
-- Indexes for table `assistance_records`
--
ALTER TABLE `assistance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assistance_records_client_id_index` (`client_id`),
  ADD KEY `assistance_records_social_case_study_id_index` (`social_case_study_id`),
  ADD KEY `assistance_records_client_id_release_date_index` (`client_id`,`release_date`);

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `bcpc_case_monitoring`
--
ALTER TABLE `bcpc_case_monitoring`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bcpc_case_monitoring_archived_at_index` (`archived_at`),
  ADD KEY `bcpc_case_monitoring_period_index` (`barangay_id`,`quarter`,`year`);

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
-- Indexes for table `cases`
--
ALTER TABLE `cases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cases_case_number_unique` (`case_number`),
  ADD UNIQUE KEY `case_number` (`case_number`),
  ADD KEY `cases_updated_by_foreign` (`updated_by`),
  ADD KEY `cases_case_designation_index` (`case_designation`),
  ADD KEY `cases_status_index` (`status`),
  ADD KEY `cases_victim_name_index` (`victim_name`),
  ADD KEY `cases_abuser_name_index` (`abuser_name`),
  ADD KEY `abuse_type` (`abuse_type`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `case_interviews`
--
ALTER TABLE `case_interviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_interviews_social_case_study_id_unique` (`social_case_study_id`);

--
-- Indexes for table `case_notes`
--
ALTER TABLE `case_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_notes_parent_id_foreign` (`parent_id`),
  ADD KEY `case_notes_case_id_index` (`case_id`),
  ADD KEY `case_notes_user_id_index` (`user_id`);

--
-- Indexes for table `case_referrals`
--
ALTER TABLE `case_referrals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_referrals_referral_number_unique` (`referral_number`),
  ADD KEY `case_referrals_case_id_foreign` (`case_id`),
  ADD KEY `case_referrals_referred_by_user_id_foreign` (`referred_by_user_id`);

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
-- Indexes for table `case_statuses`
--
ALTER TABLE `case_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `case_statuses_slug_unique` (`slug`);

--
-- Indexes for table `case_status_histories`
--
ALTER TABLE `case_status_histories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `case_status_histories_case_id_foreign` (`case_id`),
  ADD KEY `case_status_histories_performed_by_foreign` (`performed_by`);

--
-- Indexes for table `case_types`
--
ALTER TABLE `case_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `republic_act` (`republic_act`,`name`);

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
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `events_created_by_foreign` (`created_by`);

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
  ADD KEY `financial_assistance_messages_status_index` (`status`),
  ADD KEY `financial_assistance_messages_online_intake_id_foreign` (`online_intake_id`);

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
-- Indexes for table `quarterly_reports`
--
ALTER TABLE `quarterly_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quarterly_reports_user_id_foreign` (`user_id`),
  ADD KEY `quarterly_reports_barangay_id_index` (`barangay_id`),
  ADD KEY `quarterly_reports_archived_at_index` (`archived_at`),
  ADD KEY `quarterly_reports_period_index` (`barangay_id`,`type`,`quarter`,`year`);

--
-- Indexes for table `report_recipients`
--
ALTER TABLE `report_recipients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `report_recipients_email_unique` (`email`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

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
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `user_roles_role_id_foreign` (`role_id`);

--
-- Indexes for table `vawc_cache`
--
ALTER TABLE `vawc_cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `vawc_cache_locks`
--
ALTER TABLE `vawc_cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `vawc_failed_jobs`
--
ALTER TABLE `vawc_failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `vawc_jobs`
--
ALTER TABLE `vawc_jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `vawc_job_batches`
--
ALTER TABLE `vawc_job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vawc_migrations`
--
ALTER TABLE `vawc_migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vawc_password_reset_requests`
--
ALTER TABLE `vawc_password_reset_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_reset_requests_user_id_foreign` (`user_id`),
  ADD KEY `password_reset_requests_admin_id_foreign` (`admin_id`),
  ADD KEY `password_reset_requests_status_requested_at_index` (`status`,`requested_at`);

--
-- Indexes for table `vawc_password_reset_tokens`
--
ALTER TABLE `vawc_password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `vawc_sessions`
--
ALTER TABLE `vawc_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `vawc_users`
--
ALTER TABLE `vawc_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barangay` (`barangay`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `archived_users`
--
ALTER TABLE `archived_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assistance_records`
--
ALTER TABLE `assistance_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `bcpc_case_monitoring`
--
ALTER TABLE `bcpc_case_monitoring`
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
-- AUTO_INCREMENT for table `cases`
--
ALTER TABLE `cases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_interviews`
--
ALTER TABLE `case_interviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_notes`
--
ALTER TABLE `case_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_referrals`
--
ALTER TABLE `case_referrals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_rejections`
--
ALTER TABLE `case_rejections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_statuses`
--
ALTER TABLE `case_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_status_histories`
--
ALTER TABLE `case_status_histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `case_types`
--
ALTER TABLE `case_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_reference_counters`
--
ALTER TABLE `document_reference_counters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eligibility_audit_logs`
--
ALTER TABLE `eligibility_audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `email_login_codes`
--
ALTER TABLE `email_login_codes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `online_financial_intakes`
--
ALTER TABLE `online_financial_intakes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_requests`
--
ALTER TABLE `online_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_request_attachments`
--
ALTER TABLE `online_request_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quarterly_reports`
--
ALTER TABLE `quarterly_reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_recipients`
--
ALTER TABLE `report_recipients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vawc_migrations`
--
ALTER TABLE `vawc_migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `vawc_password_reset_requests`
--
ALTER TABLE `vawc_password_reset_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vawc_users`
--
ALTER TABLE `vawc_users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `vawc_user_cons` FOREIGN KEY (`causer_id`) REFERENCES `vawc_users` (`id`);

--
-- Constraints for table `archived_users`
--
ALTER TABLE `archived_users`
  ADD CONSTRAINT `vawc_archived_users_archived_by_foreign` FOREIGN KEY (`archived_by`) REFERENCES `vawc_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vawc_archived_users_original_user_id_foreign` FOREIGN KEY (`original_user_id`) REFERENCES `vawc_users` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `birthday_payouts`
--
ALTER TABLE `birthday_payouts`
  ADD CONSTRAINT `birthday_payouts_released_by_foreign` FOREIGN KEY (`released_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `birthday_payouts_senior_id_foreign` FOREIGN KEY (`senior_id`) REFERENCES `senior_citizen_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `birthday_payout_history`
--
ALTER TABLE `birthday_payout_history`
  ADD CONSTRAINT `birthday_payout_history_payout_id_foreign` FOREIGN KEY (`payout_id`) REFERENCES `birthday_payouts` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `birthday_payout_history_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `birthday_payout_history_senior_id_foreign` FOREIGN KEY (`senior_id`) REFERENCES `senior_citizen_records` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cases`
--
ALTER TABLE `cases`
  ADD CONSTRAINT `vawc_barangay` FOREIGN KEY (`case_designation`) REFERENCES `barangays` (`id`),
  ADD CONSTRAINT `vawc_cases_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `vawc_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vawc_created_by` FOREIGN KEY (`created_by`) REFERENCES `vawc_users` (`id`),
  ADD CONSTRAINT `vawc_fk_cases_abuse_type` FOREIGN KEY (`abuse_type`) REFERENCES `case_types` (`republic_act`) ON UPDATE CASCADE;

--
-- Constraints for table `case_interviews`
--
ALTER TABLE `case_interviews`
  ADD CONSTRAINT `case_interviews_social_case_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `case_notes`
--
ALTER TABLE `case_notes`
  ADD CONSTRAINT `vawc_case_notes_case_id_foreign` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vawc_case_notes_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `case_notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vawc_case_notes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `vawc_users` (`id`);

--
-- Constraints for table `case_referrals`
--
ALTER TABLE `case_referrals`
  ADD CONSTRAINT `vawc_case_referrals_case_id_foreign` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vawc_case_referrals_referred_by_user_id_foreign` FOREIGN KEY (`referred_by_user_id`) REFERENCES `vawc_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `case_rejections`
--
ALTER TABLE `case_rejections`
  ADD CONSTRAINT `case_rejections_blocking_assistance_id_foreign` FOREIGN KEY (`blocking_assistance_id`) REFERENCES `assistance_records` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `case_rejections_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `case_rejections_officer_id_foreign` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `case_rejections_social_case_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `case_status_histories`
--
ALTER TABLE `case_status_histories`
  ADD CONSTRAINT `vawc_case_status_histories_case_id_foreign` FOREIGN KEY (`case_id`) REFERENCES `cases` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vawc_case_status_histories_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `vawc_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `eligibility_audit_logs`
--
ALTER TABLE `eligibility_audit_logs`
  ADD CONSTRAINT `eligibility_audit_logs_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `eligibility_audit_logs_officer_id_foreign` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `vawc_events_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `vawc_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `family_members`
--
ALTER TABLE `family_members`
  ADD CONSTRAINT `family_members_person_id_foreign` FOREIGN KEY (`person_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `family_members_social_case_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `financial_assistance_applications`
--
ALTER TABLE `financial_assistance_applications`
  ADD CONSTRAINT `financial_assistance_applications_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `financial_assistance_applications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `financial_assistance_messages`
--
ALTER TABLE `financial_assistance_messages`
  ADD CONSTRAINT `financial_assistance_messages_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `financial_assistance_messages_intake_id_foreign` FOREIGN KEY (`intake_id`) REFERENCES `beneficiary_intakes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `financial_assistance_messages_online_intake_id_foreign` FOREIGN KEY (`online_intake_id`) REFERENCES `online_financial_intakes` (`id`) ON DELETE SET NULL;

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
-- Constraints for table `online_requests`
--
ALTER TABLE `online_requests`
  ADD CONSTRAINT `online_requests_case_id_foreign` FOREIGN KEY (`case_id`) REFERENCES `social_case_studies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `online_request_attachments`
--
ALTER TABLE `online_request_attachments`
  ADD CONSTRAINT `online_request_attachments_online_request_id_foreign` FOREIGN KEY (`online_request_id`) REFERENCES `online_requests` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset_requests`
--
ALTER TABLE `password_reset_requests`
  ADD CONSTRAINT `password_reset_requests_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quarterly_reports`
--
ALTER TABLE `quarterly_reports`
  ADD CONSTRAINT `vawc_quarterly_reports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `vawc_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `senior_citizen_records`
--
ALTER TABLE `senior_citizen_records`
  ADD CONSTRAINT `senior_citizen_records_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `social_case_reports`
--
ALTER TABLE `social_case_reports`
  ADD CONSTRAINT `social_case_reports_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `social_case_reports_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `social_case_reports_social_case_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `social_case_report_release_logs`
--
ALTER TABLE `social_case_report_release_logs`
  ADD CONSTRAINT `social_case_report_release_logs_released_by_foreign` FOREIGN KEY (`released_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `social_case_report_release_logs_social_case_report_id_foreign` FOREIGN KEY (`social_case_report_id`) REFERENCES `social_case_reports` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `social_case_report_release_logs_social_case_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `social_case_studies`
--
ALTER TABLE `social_case_studies`
  ADD CONSTRAINT `social_case_studies_eligible_by_foreign` FOREIGN KEY (`eligible_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `social_case_studies_encoded_by_foreign` FOREIGN KEY (`encoded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `social_case_studies_main_client_id_foreign` FOREIGN KEY (`main_client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `social_case_studies_officer_id_foreign` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `social_case_studies_released_by_foreign` FOREIGN KEY (`released_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `vawc_user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vawc_user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `vawc_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vawc_password_reset_requests`
--
ALTER TABLE `vawc_password_reset_requests`
  ADD CONSTRAINT `vawc_password_reset_requests_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `vawc_users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vawc_password_reset_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `vawc_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vawc_users`
--
ALTER TABLE `vawc_users`
  ADD CONSTRAINT `user_brgy` FOREIGN KEY (`barangay`) REFERENCES `barangays` (`name`),
  ADD CONSTRAINT `vawc_user_brgy` FOREIGN KEY (`barangay`) REFERENCES `barangays` (`name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
