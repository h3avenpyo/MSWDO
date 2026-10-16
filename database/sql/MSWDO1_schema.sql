-- ============================================================
-- MSWDO1 COMPLETE DATABASE SCHEMA
-- Single Database: MSWDO1
-- Engine: InnoDB, Charset: utf8mb4
-- ============================================================

CREATE DATABASE IF NOT EXISTS `MSWDO1`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `MSWDO1`;

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. LARAVEL INFRASTRUCTURE TABLES
-- ============================================================

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` enum('admin','social_worker','encoder','staff') NOT NULL DEFAULT 'staff',
  `phone` varchar(20) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `signature_image` varchar(500) DEFAULT NULL,
  `signature_position` enum('osca_head','mswdo_officer') DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  KEY `users_role_index` (`role`),
  KEY `users_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 2. SENIOR CITIZEN MODULE
-- ============================================================

CREATE TABLE `senior_citizen_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `print_count` int NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `status` enum('active','pending','archived') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `senior_record_number_unique` (`record_number`),
  UNIQUE KEY `senior_control_number_unique` (`control_number`),
  UNIQUE KEY `senior_id_number_unique` (`senior_id_number`),
  KEY `senior_barangay_index` (`barangay`),
  KEY `senior_status_index` (`status`),
  KEY `senior_birth_date_index` (`birth_date`),
  KEY `senior_year_applied_index` (`year_applied`),
  KEY `senior_name_index` (`last_name`, `first_name`),
  CONSTRAINT `senior_citizen_records_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 3. BIRTHDAY PAYOUT MODULE
-- ============================================================

CREATE TABLE `birthday_payouts` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `senior_id` bigint UNSIGNED NOT NULL,
  `payout_year` int NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 500.00,
  `status` enum('pending','released','cancelled') NOT NULL DEFAULT 'pending',
  `released_by` bigint UNSIGNED DEFAULT NULL,
  `released_date` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_senior_payout_year` (`senior_id`, `payout_year`),
  KEY `birthday_payouts_status_index` (`status`),
  CONSTRAINT `birthday_payouts_senior_id_foreign` FOREIGN KEY (`senior_id`) REFERENCES `senior_citizen_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `birthday_payouts_released_by_foreign` FOREIGN KEY (`released_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `birthday_payout_history` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `payout_id` bigint UNSIGNED DEFAULT NULL,
  `senior_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `performed_by` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bph_payout_id_index` (`payout_id`),
  KEY `bph_senior_id_index` (`senior_id`),
  KEY `bph_action_index` (`action`),
  KEY `bph_created_at_index` (`created_at`),
  CONSTRAINT `bph_payout_id_foreign` FOREIGN KEY (`payout_id`) REFERENCES `birthday_payouts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bph_senior_id_foreign` FOREIGN KEY (`senior_id`) REFERENCES `senior_citizen_records` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bph_performed_by_foreign` FOREIGN KEY (`performed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 4. CLIENT REGISTRY
-- ============================================================

CREATE TABLE `clients` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `address` text NOT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clients_name_index` (`last_name`, `first_name`),
  KEY `clients_barangay_index` (`barangay`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 5. SOCIAL CASE MODULE
-- ============================================================

CREATE TABLE `social_case_studies` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint UNSIGNED NOT NULL,
  `officer_id` bigint UNSIGNED DEFAULT NULL,
  `case_number` varchar(50) NOT NULL,
  `date_processed` date DEFAULT NULL,
  `service_provided` varchar(255) DEFAULT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `submitted_to` varchar(255) DEFAULT NULL,
  `encoded_by` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Open',
  `summary` text DEFAULT NULL,
  `interview_date` date DEFAULT NULL,
  `workflow_step` varchar(50) NOT NULL DEFAULT 'requirements_verification',
  `requirements_complete` tinyint(1) NOT NULL DEFAULT 0,
  `interview_complete` tinyint(1) NOT NULL DEFAULT 0,
  `evaluation_complete` tinyint(1) NOT NULL DEFAULT 0,
  `report_generated` tinyint(1) NOT NULL DEFAULT 0,
  `assistance_released` tinyint(1) NOT NULL DEFAULT 0,
  `assistance_amount` decimal(10,2) DEFAULT NULL,
  `assistance_date` date DEFAULT NULL,
  `released_at` timestamp NULL DEFAULT NULL,
  `released_by` bigint UNSIGNED DEFAULT NULL,
  `released_to` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `scs_case_number_unique` (`case_number`),
  KEY `scs_client_id_index` (`client_id`),
  KEY `scs_officer_id_index` (`officer_id`),
  KEY `scs_status_index` (`status`),
  KEY `scs_workflow_step_index` (`workflow_step`),
  KEY `scs_encoded_by_index` (`encoded_by`),
  CONSTRAINT `scs_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `scs_officer_id_foreign` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scs_encoded_by_foreign` FOREIGN KEY (`encoded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scs_released_by_foreign` FOREIGN KEY (`released_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `case_interviews` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `social_case_study_id` bigint UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `case_interviews_study_id_unique` (`social_case_study_id`),
  CONSTRAINT `case_interviews_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `family_members` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `social_case_study_id` bigint UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `relationship` varchar(100) NOT NULL,
  `age` tinyint UNSIGNED DEFAULT NULL,
  `sex` enum('Male','Female') DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `monthly_income` decimal(12,2) DEFAULT NULL,
  `is_dependent` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fm_study_id_index` (`social_case_study_id`),
  CONSTRAINT `fm_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 6. SOCIAL CASE SUPPORT TABLES
-- ============================================================

CREATE TABLE `beneficiary_intakes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `social_case_study_id` bigint UNSIGNED DEFAULT NULL,
  `control_number` varchar(50) NOT NULL,
  `date_processed` date NOT NULL,
  `encoder` bigint UNSIGNED DEFAULT NULL,
  `is_client_beneficiary` tinyint(1) NOT NULL DEFAULT 1,
  `beneficiary_last_name` varchar(255) DEFAULT NULL,
  `beneficiary_first_name` varchar(255) DEFAULT NULL,
  `beneficiary_middle_name` varchar(255) DEFAULT NULL,
  `beneficiary_birthday` date DEFAULT NULL,
  `beneficiary_age` int DEFAULT NULL,
  `beneficiary_sex` varchar(20) DEFAULT NULL,
  `beneficiary_barangay` varchar(255) DEFAULT NULL,
  `beneficiary_relationship` varchar(100) DEFAULT NULL,
  `medical_conditions` json DEFAULT NULL,
  `medical_condition_other` varchar(255) DEFAULT NULL,
  `service_provided` varchar(255) NOT NULL,
  `purpose` varchar(255) NOT NULL,
  `purpose_other` varchar(255) DEFAULT NULL,
  `submitted_to` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bi_control_number_unique` (`control_number`),
  KEY `bi_client_id_index` (`client_id`),
  KEY `bi_study_id_index` (`social_case_study_id`),
  CONSTRAINT `bi_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bi_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `bi_encoder_foreign` FOREIGN KEY (`encoder`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `assistance_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint UNSIGNED NOT NULL,
  `social_case_study_id` bigint UNSIGNED DEFAULT NULL,
  `assistance_type` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL,
  `release_date` date DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ar_client_id_index` (`client_id`),
  KEY `ar_study_id_index` (`social_case_study_id`),
  KEY `ar_client_release_index` (`client_id`, `release_date`),
  CONSTRAINT `ar_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ar_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `case_rejections` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint UNSIGNED NOT NULL,
  `blocking_assistance_id` bigint UNSIGNED DEFAULT NULL,
  `social_case_study_id` bigint UNSIGNED DEFAULT NULL,
  `officer_id` bigint UNSIGNED DEFAULT NULL,
  `officer_name` varchar(255) DEFAULT NULL,
  `reason` text NOT NULL,
  `last_assistance_date` date DEFAULT NULL,
  `last_assistance_type` varchar(255) DEFAULT NULL,
  `next_eligible_date` date DEFAULT NULL,
  `rejected_at` timestamp NOT NULL,
  `closed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cr_client_blocking_unique` (`client_id`, `blocking_assistance_id`),
  KEY `cr_blocking_assistance_index` (`blocking_assistance_id`),
  KEY `cr_study_id_index` (`social_case_study_id`),
  KEY `cr_officer_id_index` (`officer_id`),
  CONSTRAINT `cr_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cr_blocking_assistance_foreign` FOREIGN KEY (`blocking_assistance_id`) REFERENCES `assistance_records` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cr_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `cr_officer_id_foreign` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `eligibility_audit_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `client_name` varchar(255) NOT NULL,
  `officer_id` bigint UNSIGNED DEFAULT NULL,
  `officer_name` varchar(255) DEFAULT NULL,
  `result` varchar(50) NOT NULL,
  `result_details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `search_duration_ms` int UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eal_client_id_index` (`client_id`),
  KEY `eal_officer_id_index` (`officer_id`),
  CONSTRAINT `eal_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `eal_officer_id_foreign` FOREIGN KEY (`officer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `social_case_reports` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `social_case_study_id` bigint UNSIGNED DEFAULT NULL,
  `case_number` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `generated_at` timestamp NULL DEFAULT NULL,
  `generated_by` bigint UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `snapshot` json DEFAULT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `scr_case_number_unique` (`case_number`),
  KEY `scr_study_id_index` (`social_case_study_id`),
  KEY `scr_generated_by_index` (`generated_by`),
  KEY `scr_created_by_index` (`created_by`),
  CONSTRAINT `scr_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scr_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scr_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `social_case_report_release_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `social_case_study_id` bigint UNSIGNED NOT NULL,
  `social_case_report_id` bigint UNSIGNED DEFAULT NULL,
  `released_by` bigint UNSIGNED DEFAULT NULL,
  `released_by_name` varchar(255) DEFAULT NULL,
  `released_to` varchar(255) NOT NULL,
  `released_at` timestamp NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `scrrl_study_id_index` (`social_case_study_id`),
  KEY `scrrl_report_id_index` (`social_case_report_id`),
  KEY `scrrl_released_by_index` (`released_by`),
  CONSTRAINT `scrrl_study_id_foreign` FOREIGN KEY (`social_case_study_id`) REFERENCES `social_case_studies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `scrrl_report_id_foreign` FOREIGN KEY (`social_case_report_id`) REFERENCES `social_case_reports` (`id`) ON DELETE SET NULL,
  CONSTRAINT `scrrl_released_by_foreign` FOREIGN KEY (`released_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- 7. FINANCIAL MODULE
-- ============================================================

CREATE TABLE `financial_assistance_applications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint UNSIGNED DEFAULT NULL,
  `application_number` varchar(50) NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `assistance_type` varchar(255) NOT NULL,
  `amount_requested` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_by` bigint UNSIGNED NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faa_application_number_unique` (`application_number`),
  KEY `faa_client_id_index` (`client_id`),
  KEY `faa_status_index` (`status`),
  KEY `faa_created_by_index` (`created_by`),
  CONSTRAINT `faa_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `faa_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;
