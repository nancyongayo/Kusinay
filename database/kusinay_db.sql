-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2026 at 10:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kusinay_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accomplishment_reports`
--

CREATE TABLE `accomplishment_reports` (
  `report_id` int(10) UNSIGNED NOT NULL,
  `bns_id` int(11) NOT NULL,
  `report_month` tinyint(3) UNSIGNED NOT NULL COMMENT '1-12',
  `report_year` smallint(5) UNSIGNED NOT NULL,
  `status` enum('Draft','Submitted','Approved','Returned') NOT NULL DEFAULT 'Draft',
  `ps_0_23_weighed` smallint(5) UNSIGNED DEFAULT 0,
  `ps_24_59_weighed` smallint(5) UNSIGNED DEFAULT 0,
  `ps_malnourished` smallint(5) UNSIGNED DEFAULT 0,
  `total_mam` smallint(5) UNSIGNED DEFAULT 0,
  `mam_monitored` smallint(5) UNSIGNED DEFAULT 0,
  `total_sam` smallint(5) UNSIGNED DEFAULT 0,
  `sam_monitored` smallint(5) UNSIGNED DEFAULT 0,
  `pregnant_new` smallint(5) UNSIGNED DEFAULT 0,
  `lactating_new` smallint(5) UNSIGNED DEFAULT 0,
  `elderly_assessed` smallint(5) UNSIGNED DEFAULT 0,
  `mam_new_admission` smallint(5) UNSIGNED DEFAULT 0,
  `mam_non_cured` smallint(5) UNSIGNED DEFAULT 0,
  `mam_defaulter` smallint(5) UNSIGNED DEFAULT 0,
  `mam_died` smallint(5) UNSIGNED DEFAULT 0,
  `sam_new_admission` smallint(5) UNSIGNED DEFAULT 0,
  `sam_non_cured` smallint(5) UNSIGNED DEFAULT 0,
  `sam_died` smallint(5) UNSIGNED DEFAULT 0,
  `cvd_patients` smallint(5) UNSIGNED DEFAULT 0,
  `families_malnourished` smallint(5) UNSIGNED DEFAULT 0,
  `adolescents` smallint(5) UNSIGNED DEFAULT 0,
  `adults` smallint(5) UNSIGNED DEFAULT 0,
  `infants_vita` smallint(5) UNSIGNED DEFAULT 0,
  `children_vita` smallint(5) UNSIGNED DEFAULT 0,
  `deworm_1_4` smallint(5) UNSIGNED DEFAULT 0,
  `deworm_5_9` smallint(5) UNSIGNED DEFAULT 0,
  `deworm_10_19` smallint(5) UNSIGNED DEFAULT 0,
  `monthly_meetings` smallint(5) UNSIGNED DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL COMMENT 'Nutrition Officer II user_id',
  `reviewed_at` datetime DEFAULT NULL,
  `return_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `no2_signature` mediumtext DEFAULT NULL COMMENT 'Base64 PNG of NO II e-signature'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `accomplishment_reports`
--

INSERT INTO `accomplishment_reports` (`report_id`, `bns_id`, `report_month`, `report_year`, `status`, `ps_0_23_weighed`, `ps_24_59_weighed`, `ps_malnourished`, `total_mam`, `mam_monitored`, `total_sam`, `sam_monitored`, `pregnant_new`, `lactating_new`, `elderly_assessed`, `mam_new_admission`, `mam_non_cured`, `mam_defaulter`, `mam_died`, `sam_new_admission`, `sam_non_cured`, `sam_died`, `cvd_patients`, `families_malnourished`, `adolescents`, `adults`, `infants_vita`, `children_vita`, `deworm_1_4`, `deworm_5_9`, `deworm_10_19`, `monthly_meetings`, `remarks`, `submitted_at`, `reviewed_by`, `reviewed_at`, `return_reason`, `created_at`, `updated_at`, `no2_signature`) VALUES
(7, 1, 6, 2026, 'Draft', 5, 2, 6, 1, 0, 2, 0, 1, 0, 3, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, '2026-06-02 07:54:28', '2026-06-02 07:54:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `diet_consumption_logs`
--

CREATE TABLE `diet_consumption_logs` (
  `consumption_id` int(11) NOT NULL,
  `meal_plan_id` int(11) NOT NULL,
  `meal_plan_item_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL COMMENT 'Specific child if linked to diet plan',
  `fm_member_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Mother/Parent recording the consumption',
  `consumption_date` date NOT NULL DEFAULT curdate(),
  `meal_type` enum('Breakfast','Snack','Lunch','Dinner') NOT NULL,
  `dish_name` varchar(255) NOT NULL,
  `is_consumed` tinyint(1) DEFAULT 1,
  `consumption_notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `diet_plans`
--

CREATE TABLE `diet_plans` (
  `diet_plan_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL COMMENT 'Child receiving the diet plan',
  `fm_member_id` int(11) DEFAULT NULL COMMENT 'Family member ID',
  `family_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `created_by_user_id` int(11) NOT NULL COMMENT 'BNS or Nutrition Officer who created it',
  `plan_type` enum('Therapeutic','Supplementary','Preventive','General') DEFAULT 'General',
  `nutritional_status` varchar(100) DEFAULT NULL COMMENT 'e.g., MAM, SAM, Normal',
  `target_calories_per_day` int(11) DEFAULT NULL,
  `target_protein_grams` int(11) DEFAULT NULL,
  `dietary_restrictions` text DEFAULT NULL COMMENT 'Allergies, cultural restrictions, etc.',
  `recommended_foods` text NOT NULL COMMENT 'Foods to include',
  `foods_to_avoid` text DEFAULT NULL,
  `meal_frequency` varchar(100) DEFAULT NULL COMMENT 'e.g., 3 meals + 2 snacks',
  `special_instructions` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Active','Completed','Discontinued') DEFAULT 'Active',
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `diet_plan_meal_plan_link`
--

CREATE TABLE `diet_plan_meal_plan_link` (
  `link_id` int(11) NOT NULL,
  `diet_plan_id` int(11) NOT NULL,
  `meal_plan_id` int(11) NOT NULL,
  `linked_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education_attendance`
--

CREATE TABLE `education_attendance` (
  `attendance_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL COMMENT 'Name of attendee',
  `purok` varchar(100) DEFAULT NULL,
  `kumainments_discussed` varchar(50) DEFAULT NULL COMMENT 'Which Kumainment # (1-10) or topic',
  `topic_pinggang_pinoy` tinyint(1) NOT NULL DEFAULT 0,
  `topic_10_kumainments` tinyint(1) NOT NULL DEFAULT 0,
  `topic_others` varchar(100) DEFAULT NULL,
  `signature` varchar(255) DEFAULT 'Present' COMMENT 'Signature or mark',
  `attended_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `feedback` text DEFAULT NULL COMMENT 'Attendee feedback',
  `rating` tinyint(4) DEFAULT NULL COMMENT '1-5 stars'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_attendance`
--

INSERT INTO `education_attendance` (`attendance_id`, `session_id`, `user_id`, `full_name`, `purok`, `kumainments_discussed`, `topic_pinggang_pinoy`, `topic_10_kumainments`, `topic_others`, `signature`, `attended_at`, `feedback`, `rating`) VALUES
(11, 12, NULL, 'Santos, Junel Del', 'Purok 3', NULL, 1, 0, NULL, 'Present', '2026-06-02 13:27:55', NULL, NULL),
(12, 12, 52, 'Tiago, Carla Song', 'Purok 1', NULL, 0, 0, NULL, 'Present', '2026-06-02 13:28:51', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `education_topics`
--

CREATE TABLE `education_topics` (
  `topic_id` int(10) UNSIGNED NOT NULL,
  `topic_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL COMMENT 'Nutrition, Breastfeeding, Hygiene, etc.',
  `materials_url` varchar(500) DEFAULT NULL COMMENT 'Link to materials/handouts',
  `duration_minutes` int(11) DEFAULT 60,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_topics`
--

INSERT INTO `education_topics` (`topic_id`, `topic_name`, `description`, `category`, `materials_url`, `duration_minutes`, `is_active`) VALUES
(1, '10 Kumainments', 'Sigla at lakas ng buhay - 10 nutrition commandments', 'Nutrition', NULL, 60, 1),
(2, 'Pinggang Pinoy', 'Healthy food plate guide - GLOW, GROW, GO foods', 'Nutrition', NULL, 60, 1),
(3, 'Exclusive Breastfeeding', 'First 6 months breastfeeding only', 'Breastfeeding', NULL, 90, 1),
(4, 'Complementary Feeding', 'Starting solid foods at 6 months', 'Infant Feeding', NULL, 90, 1),
(5, 'Proper Handwashing', 'Hygiene and sanitation practices', 'Hygiene', NULL, 45, 1),
(6, 'Nutrition for Pregnant Women', 'Proper nutrition during pregnancy', 'Maternal Health', NULL, 90, 1),
(7, 'Micronutrient Supplementation', 'Vitamin A, Iron, Deworming', 'Nutrition', NULL, 60, 1),
(8, 'Food Safety and Preparation', 'Safe food handling and cooking', 'Food Safety', NULL, 60, 1);

-- --------------------------------------------------------

--
-- Table structure for table `family_food_activities`
--

CREATE TABLE `family_food_activities` (
  `id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `activity_id` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_food_activities`
--

INSERT INTO `family_food_activities` (`id`, `family_id`, `activity_id`) VALUES
(18, 51, 1);

-- --------------------------------------------------------

--
-- Table structure for table `family_links`
--

CREATE TABLE `family_links` (
  `link_id` int(11) NOT NULL,
  `user_id_a` int(11) NOT NULL,
  `user_id_b` int(11) NOT NULL,
  `relationship_type` enum('Husband-Wife','Mother-Child','Father-Child') NOT NULL,
  `verification_status` enum('Pending','Verified','Rejected') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verified_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_links`
--

INSERT INTO `family_links` (`link_id`, `user_id_a`, `user_id_b`, `relationship_type`, `verification_status`, `created_at`, `verified_at`) VALUES
(2, 5, 1, 'Husband-Wife', 'Verified', '2026-04-19 01:46:47', '2026-04-19 03:47:00'),
(3, 23, 22, 'Husband-Wife', 'Verified', '2026-04-30 02:11:00', '2026-04-30 04:11:19');

-- --------------------------------------------------------

--
-- Table structure for table `family_members`
--

CREATE TABLE `family_members` (
  `member_id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `role` enum('Head','Wife','Child') NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `sex` enum('M','F') DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated','Live-in') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `status_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `occupation` varchar(150) DEFAULT NULL,
  `monthly_income` decimal(10,2) DEFAULT NULL COMMENT 'Monthly income for Head/Wife members',
  `educ_level_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `sort_order` tinyint(3) UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_members`
--

INSERT INTO `family_members` (`member_id`, `family_id`, `role`, `last_name`, `first_name`, `middle_name`, `suffix`, `sex`, `civil_status`, `dob`, `status_id`, `occupation`, `monthly_income`, `educ_level_id`, `sort_order`) VALUES
(194, 48, 'Head', 'Santiago', 'Noel', 'Cruz', NULL, 'M', 'Married', '1983-05-26', NULL, 'Driver', NULL, 3, 0),
(195, 48, 'Wife', 'Santiago', 'Maria', 'Del', NULL, 'F', NULL, '1993-02-19', NULL, 'Housewife', NULL, 3, 1),
(196, 48, 'Child', 'Santiago', 'Ethan', 'Cruz', NULL, 'M', NULL, '2024-06-10', NULL, NULL, NULL, NULL, 10),
(197, 48, 'Child', 'Santiago', 'Mia', 'Cruz', NULL, 'F', NULL, '2014-05-23', NULL, NULL, NULL, NULL, 11),
(198, 49, 'Head', 'Martines', 'Diana', 'Bondalo', NULL, 'F', 'Married', '1997-12-09', NULL, 'Crew', NULL, 3, 0),
(199, 49, 'Wife', 'Martines', 'Arnold', 'Bin', NULL, 'M', NULL, '1996-09-18', NULL, 'Farmer', NULL, 3, 1),
(200, 49, 'Child', 'Martines', 'Sara', 'Bondalo', NULL, 'F', NULL, '2025-02-14', NULL, NULL, NULL, NULL, 10),
(205, 51, 'Head', 'Cruz', 'Toni', 'Mill', NULL, 'M', 'Single', '1954-01-07', NULL, 'Farmer', NULL, 2, 0),
(206, 52, 'Head', 'Dal', 'Rolly', 'Luke', NULL, 'M', 'Married', '1960-04-15', NULL, 'Manager', NULL, 5, 0),
(207, 52, 'Wife', 'Dal', 'Tina', 'Com', NULL, 'F', NULL, '1965-12-13', NULL, 'Teacher', NULL, 5, 1),
(220, 50, 'Head', 'Santos', 'Junel', 'Del', NULL, 'M', 'Live-in', '1995-11-29', NULL, 'Driver', NULL, 3, 0),
(221, 50, 'Wife', 'Perez', 'Anita', NULL, NULL, 'F', NULL, '1999-07-14', NULL, 'Housewife', NULL, 3, 1),
(222, 50, 'Child', 'Santos', 'Ryan', 'Perez', NULL, 'M', NULL, '2024-10-27', NULL, NULL, NULL, NULL, 10),
(223, 50, 'Child', 'Santos', 'Rain', 'Perez', NULL, 'M', NULL, '2016-11-08', NULL, NULL, NULL, NULL, 11),
(229, 53, 'Head', 'Dove', 'Alex', NULL, NULL, NULL, NULL, '1996-11-15', NULL, 'Teacher', NULL, 5, 0),
(230, 53, 'Wife', 'Dove', 'Starmaine', 'Tan', NULL, NULL, NULL, '1998-02-14', NULL, 'Housewife', NULL, 5, 1),
(231, 53, 'Child', 'Dove', 'Bia', 'Tan', NULL, 'F', NULL, '2024-06-03', NULL, NULL, NULL, NULL, 10),
(232, 53, 'Child', 'Dove', 'Niel', 'Tan', NULL, 'M', NULL, '2025-01-30', NULL, NULL, NULL, NULL, 11),
(248, 58, 'Head', 'Brown', 'Chris', 'Chui', NULL, NULL, NULL, NULL, NULL, 'Manager', NULL, 5, 0),
(249, 58, 'Wife', 'Brown', 'Maria', 'Evan', NULL, NULL, NULL, NULL, NULL, 'Housewife', NULL, 5, 1),
(250, 58, 'Child', 'Brown', 'Lily', 'Evan', NULL, 'F', NULL, '2017-12-16', NULL, NULL, NULL, NULL, 10),
(251, 58, 'Child', 'Brown', 'Lea', 'Evan', NULL, 'F', NULL, '2021-08-23', NULL, NULL, NULL, NULL, 11),
(252, 58, 'Child', 'Brown', 'Leo', 'Evan', NULL, 'M', NULL, '2023-06-02', NULL, NULL, NULL, NULL, 12),
(253, 59, 'Head', 'Peace', 'Ethan', 'Del', NULL, 'M', 'Married', '1994-02-15', NULL, 'Crew', NULL, 5, 0),
(254, 59, 'Wife', 'Peace', 'Elsa', 'Bin', NULL, 'F', NULL, '1991-01-26', NULL, 'Housewife', NULL, 3, 1),
(255, 59, 'Child', 'Peace', 'Nel', 'Bin', NULL, 'M', NULL, '2022-05-28', NULL, NULL, NULL, NULL, 10),
(256, 59, 'Child', 'Peace', 'Kara', 'Bin', NULL, 'F', NULL, '2025-11-23', NULL, NULL, NULL, NULL, 11),
(257, 59, 'Child', 'Peace', 'Ian', 'Bin', NULL, 'M', NULL, '2016-04-09', NULL, NULL, NULL, NULL, 12),
(258, 60, 'Head', 'Tiago', 'Alfred', 'Santos', NULL, NULL, NULL, NULL, NULL, 'Driver', NULL, 4, 0),
(259, 60, 'Wife', 'Tiago', 'Carla', 'Song', NULL, NULL, NULL, NULL, NULL, 'Housewife', NULL, 3, 1),
(260, 60, 'Child', 'Tiago', 'Erika', 'Santos', NULL, 'F', NULL, '2007-02-17', NULL, NULL, NULL, NULL, 10),
(261, 60, 'Child', 'Tiago', 'Erick', 'Santos', NULL, 'M', NULL, '2022-09-28', NULL, NULL, NULL, NULL, 11),
(268, 62, 'Head', 'Rhias', 'Lance', 'Chui', NULL, NULL, NULL, NULL, NULL, 'Manager', NULL, 3, 0),
(269, 62, 'Wife', 'Rhias', 'Erza', 'Ong', NULL, NULL, NULL, NULL, NULL, 'Housewife', NULL, 4, 1),
(270, 62, 'Child', 'Rhias', 'Rence', 'Ong', NULL, 'M', NULL, '2022-07-19', NULL, NULL, NULL, NULL, 10);

-- --------------------------------------------------------

--
-- Table structure for table `family_profiles`
--

CREATE TABLE `family_profiles` (
  `family_id` int(11) NOT NULL,
  `bns_id` int(11) NOT NULL,
  `source_user_id` int(11) DEFAULT NULL,
  `hh_number` varchar(20) DEFAULT NULL,
  `num_hh_members` tinyint(3) UNSIGNED DEFAULT NULL,
  `children_0_5mos` tinyint(3) UNSIGNED DEFAULT NULL,
  `children_6_23mos` tinyint(3) UNSIGNED DEFAULT NULL,
  `children_24_59mos` tinyint(3) UNSIGNED DEFAULT NULL,
  `children_60plus` tinyint(3) UNSIGNED DEFAULT NULL,
  `is_mother_prog` tinyint(1) NOT NULL DEFAULT 0,
  `wife_pregnancy_status` enum('Not Pregnant','Pregnant 1st Trimester','Pregnant 2nd Trimester','Pregnant 3rd Trimester','Postpartum') DEFAULT NULL COMMENT 'Specific pregnancy status with trimester',
  `wife_breastfeeding_status` enum('Not Breastfeeding','EBF (Exclusive Breastfeeding)','Mixed Feeding','Bottle Feeding') DEFAULT NULL COMMENT 'Specific breastfeeding status',
  `fp_method_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `fp_method_other` varchar(100) DEFAULT NULL,
  `is_erf` tinyint(1) NOT NULL DEFAULT 0,
  `is_mixed_milk` tinyint(1) NOT NULL DEFAULT 0,
  `is_bottle_feeding` tinyint(1) NOT NULL DEFAULT 0,
  `toilet_type_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `water_source_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `uses_iodized_salt` tinyint(1) NOT NULL DEFAULT 0,
  `uses_ifr` tinyint(1) NOT NULL DEFAULT 0,
  `dwelling_type_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `total_income` decimal(10,2) DEFAULT NULL,
  `purok` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `family_profiles`
--

INSERT INTO `family_profiles` (`family_id`, `bns_id`, `source_user_id`, `hh_number`, `num_hh_members`, `children_0_5mos`, `children_6_23mos`, `children_24_59mos`, `children_60plus`, `is_mother_prog`, `wife_pregnancy_status`, `wife_breastfeeding_status`, `fp_method_id`, `fp_method_other`, `is_erf`, `is_mixed_milk`, `is_bottle_feeding`, `toilet_type_id`, `water_source_id`, `uses_iodized_salt`, `uses_ifr`, `dwelling_type_id`, `total_income`, `purok`, `remarks`, `created_at`, `updated_at`) VALUES
(48, 1, NULL, '10011', 4, NULL, NULL, 1, 1, 0, NULL, NULL, 2, NULL, 0, 1, 0, 1, 1, 1, 0, 2, 3000.00, 'Purok 3', NULL, '2026-06-01 14:29:54', '2026-06-01 14:29:54'),
(49, 1, NULL, '10012', 3, NULL, 1, NULL, NULL, 0, NULL, NULL, 2, NULL, 0, 0, 1, 1, 1, 1, 0, 2, 10000.00, 'Purok 4', NULL, '2026-06-01 14:37:58', '2026-06-01 14:37:58'),
(50, 1, NULL, '10013', 4, NULL, 1, NULL, 1, 1, 'Pregnant 1st Trimester', 'Not Breastfeeding', 1, NULL, 0, 0, 0, 1, 1, 0, 0, 2, 2000.00, 'Purok 3', NULL, '2026-06-01 14:45:10', '2026-06-01 15:58:16'),
(51, 1, NULL, '100256', 5, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, 0, 0, 1, 1, 1, 0, 3, 500.00, 'Purok 6', NULL, '2026-06-01 14:51:06', '2026-06-01 14:51:06'),
(52, 1, NULL, '200156', 2, NULL, NULL, NULL, NULL, 0, NULL, NULL, 1, NULL, 0, 0, 0, 1, 1, 1, 0, 1, 30000.00, 'Purok 7', NULL, '2026-06-01 14:57:10', '2026-06-01 14:57:10'),
(53, 1, 46, '006', 4, NULL, 1, 1, NULL, 0, NULL, NULL, 1, NULL, 0, 0, 0, 1, 1, 1, 0, 1, 30000.00, 'Purok 5', NULL, '2026-06-01 15:08:40', '2026-06-01 16:31:27'),
(58, 1, 51, '010', 5, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, 0, 0, 1, 1, 1, 0, 1, 5000.00, 'Purok 8', NULL, '2026-06-02 03:57:13', '2026-06-02 03:57:13'),
(59, 1, NULL, '200356', 5, NULL, 1, 1, 1, 0, 'Not Pregnant', 'EBF (Exclusive Breastfeeding)', 1, NULL, 1, 0, 0, 1, 1, 1, 0, 1, 15000.00, 'Purok 1', NULL, '2026-06-02 12:01:47', '2026-06-02 12:01:47'),
(60, 1, 52, '009', 4, NULL, NULL, NULL, NULL, 0, NULL, NULL, 2, NULL, 0, 0, 0, 1, 1, 1, 0, 2, 3000.00, 'Purok 1', NULL, '2026-06-02 12:13:16', '2026-06-02 12:13:16'),
(62, 1, 54, '010', 3, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, 0, 0, 1, 1, 1, 0, 1, 5000.00, 'Purok 2', NULL, '2026-06-02 13:05:09', '2026-06-02 13:05:09');

-- --------------------------------------------------------

--
-- Table structure for table `feeding_program_attendance`
--

CREATE TABLE `feeding_program_attendance` (
  `attendance_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL,
  `mother_id` int(11) DEFAULT NULL,
  `rsvp_status` enum('pending','confirmed','declined') DEFAULT 'pending' COMMENT 'Parent RSVP status',
  `rsvp_date` datetime DEFAULT NULL COMMENT 'When parent confirmed/declined',
  `decline_reason` text DEFAULT NULL,
  `decline_date` datetime DEFAULT NULL,
  `name_of_client` varchar(255) NOT NULL,
  `mother_name` varchar(255) DEFAULT NULL,
  `purok` varchar(50) DEFAULT NULL,
  `pinggang_pinoy` tinyint(1) DEFAULT 0,
  `id_kumainments` tinyint(1) DEFAULT 0,
  `others` varchar(255) DEFAULT NULL,
  `signature_data` longtext DEFAULT NULL,
  `is_present` tinyint(1) DEFAULT NULL COMMENT 'NULL=not yet marked, 1=present, 0=absent',
  `attendance_marked_by` int(11) DEFAULT NULL COMMENT 'BNS user_id who marked attendance',
  `attendance_marked_at` datetime DEFAULT NULL COMMENT 'When attendance was marked',
  `time_in` time DEFAULT NULL,
  `meal_received` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feeding_program_attendance`
--

INSERT INTO `feeding_program_attendance` (`attendance_id`, `session_id`, `proposal_id`, `child_id`, `mother_id`, `rsvp_status`, `rsvp_date`, `decline_reason`, `decline_date`, `name_of_client`, `mother_name`, `purok`, `pinggang_pinoy`, `id_kumainments`, `others`, `signature_data`, `is_present`, `attendance_marked_by`, `attendance_marked_at`, `time_in`, `meal_received`, `created_at`, `updated_at`) VALUES
(1678, 386, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:12:54', '2026-06-02 10:14:31'),
(1679, 386, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:12:54', '2026-06-02 10:14:31'),
(1680, 386, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:12:54', '2026-06-02 10:14:31'),
(1681, 386, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:12:54', '2026-06-02 10:14:31'),
(1682, 386, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:12:54', '2026-06-02 10:12:54'),
(1683, 386, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:12:54', '2026-06-02 10:12:54'),
(1684, 387, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1685, 387, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1686, 387, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1687, 387, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1688, 387, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1689, 387, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1690, 388, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1691, 388, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1692, 388, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1693, 388, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1694, 388, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1695, 388, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1696, 389, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1697, 389, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1698, 389, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1699, 389, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1700, 389, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1701, 389, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1702, 390, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1703, 390, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1704, 390, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1705, 390, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1706, 390, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1707, 390, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1708, 391, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1709, 391, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1710, 391, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1711, 391, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1712, 391, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1713, 391, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1714, 392, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1715, 392, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1716, 392, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1717, 392, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1718, 392, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1719, 392, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1720, 393, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1721, 393, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1722, 393, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1723, 393, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1724, 393, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1725, 393, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1726, 394, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1727, 394, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1728, 394, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1729, 394, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1730, 394, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1731, 394, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1732, 395, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1733, 395, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1734, 395, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1735, 395, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1736, 395, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1737, 395, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1738, 396, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1739, 396, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1740, 396, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1741, 396, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1742, 396, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1743, 396, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1744, 397, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1745, 397, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1746, 397, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1747, 397, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1748, 397, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1749, 397, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1750, 398, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, 1, '2026-06-02 18:27:28', NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:27:28'),
(1751, 398, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:21'),
(1752, 398, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:21'),
(1753, 398, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:21'),
(1754, 398, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1755, 398, 7, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(1762, 400, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, 1, '2026-06-02 22:04:35', NULL, NULL, '2026-06-02 13:52:47', '2026-06-02 14:04:35'),
(1763, 400, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:52:47', '2026-06-02 13:52:47'),
(1764, 400, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:52:47', '2026-06-02 13:52:47'),
(1765, 400, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:52:47', '2026-06-02 13:52:47'),
(1766, 400, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:52:47', '2026-06-02 13:52:47'),
(1767, 400, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:52:47', '2026-06-02 13:52:47'),
(1768, 400, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:52:47', '2026-06-02 13:52:47'),
(1769, 400, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:52:47', '2026-06-02 13:52:47'),
(1770, 401, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, 1, '2026-06-02 22:00:12', NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 14:00:12'),
(1771, 401, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, 1, '2026-06-02 22:00:17', NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 14:00:17'),
(1772, 401, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, 1, '2026-06-02 22:00:21', NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 14:00:21'),
(1773, 401, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, 1, '2026-06-02 22:00:26', NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 14:00:26'),
(1774, 401, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, 1, 1, '2026-06-02 22:00:31', NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 14:00:31'),
(1775, 401, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, 1, 1, '2026-06-02 22:00:35', NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 14:00:35'),
(1776, 401, 8, NULL, NULL, 'confirmed', '2026-06-02 22:07:20', NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, 1, 1, '2026-06-02 22:00:40', NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 14:07:20'),
(1777, 401, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, 0, 1, '2026-06-02 22:00:50', NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 14:00:50'),
(1778, 402, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1779, 402, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1780, 402, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1781, 402, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1782, 402, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1783, 402, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1784, 402, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1785, 402, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1786, 403, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1787, 403, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1788, 403, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1789, 403, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1790, 403, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1791, 403, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1792, 403, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1793, 403, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1794, 404, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1795, 404, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1796, 404, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1797, 404, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1798, 404, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1799, 404, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1800, 404, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1801, 404, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1802, 405, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1803, 405, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1804, 405, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1805, 405, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1806, 405, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1807, 405, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1808, 405, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1809, 405, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1810, 406, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1811, 406, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1812, 406, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1813, 406, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1814, 406, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1815, 406, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1816, 406, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1817, 406, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1818, 407, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1819, 407, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1820, 407, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1821, 407, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1822, 407, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1823, 407, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1824, 407, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1825, 407, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1826, 408, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1827, 408, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1828, 408, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1829, 408, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1830, 408, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1831, 408, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1832, 408, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1833, 408, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1834, 409, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1835, 409, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1836, 409, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1837, 409, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1838, 409, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1839, 409, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1840, 409, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1841, 409, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1842, 410, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1843, 410, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1844, 410, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1845, 410, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1846, 410, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1847, 410, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1848, 410, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1849, 410, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1850, 411, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1851, 411, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Leo Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1852, 411, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1853, 411, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1854, 411, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1855, 411, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1856, 411, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1857, 411, 8, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(1858, 412, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1859, 412, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1860, 412, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1861, 412, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1862, 412, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1863, 412, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1864, 412, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1865, 413, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1866, 413, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1867, 413, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1868, 413, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1869, 413, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1870, 413, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1871, 413, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1872, 414, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1873, 414, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1874, 414, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1875, 414, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1876, 414, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1877, 414, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1878, 414, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1879, 415, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1880, 415, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1881, 415, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1882, 415, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1883, 415, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1884, 415, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1885, 415, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1886, 416, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1887, 416, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1888, 416, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1889, 416, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1890, 416, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1891, 416, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1892, 416, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1893, 417, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-10 09:13:42'),
(1894, 417, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-10 09:13:42'),
(1895, 417, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-10 09:13:42'),
(1896, 417, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, 1, NULL, NULL, '17:17:04', NULL, '2026-06-03 09:48:19', '2026-06-10 09:17:04'),
(1897, 417, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1898, 417, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-10 09:13:42'),
(1899, 417, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1900, 418, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1901, 418, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1902, 418, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1903, 418, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1904, 418, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1905, 418, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1906, 418, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1907, 419, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, 1, '2026-06-03 17:50:09', NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:50:09'),
(1908, 419, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:26'),
(1909, 419, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:26'),
(1910, 419, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1911, 419, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1912, 419, 9, NULL, NULL, 'confirmed', '2026-06-03 17:50:27', NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, 1, 1, '2026-06-03 17:50:20', NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:50:27'),
(1913, 419, 9, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(1914, 420, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:11:23'),
(1915, 420, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:11:23'),
(1916, 420, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:11:23'),
(1917, 420, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1918, 420, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1919, 420, 10, NULL, NULL, 'confirmed', '2026-06-03 18:18:31', NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:18:31'),
(1920, 420, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1921, 421, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1922, 421, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1923, 421, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1924, 421, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1925, 421, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1926, 421, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31');
INSERT INTO `feeding_program_attendance` (`attendance_id`, `session_id`, `proposal_id`, `child_id`, `mother_id`, `rsvp_status`, `rsvp_date`, `decline_reason`, `decline_date`, `name_of_client`, `mother_name`, `purok`, `pinggang_pinoy`, `id_kumainments`, `others`, `signature_data`, `is_present`, `attendance_marked_by`, `attendance_marked_at`, `time_in`, `meal_received`, `created_at`, `updated_at`) VALUES
(1927, 421, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1928, 422, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1929, 422, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1930, 422, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1931, 422, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1932, 422, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1933, 422, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1934, 422, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1935, 423, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-08 17:44:29'),
(1936, 423, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-08 17:44:29'),
(1937, 423, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-08 17:44:29'),
(1938, 423, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1939, 423, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1940, 423, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-08 17:44:29'),
(1941, 423, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1949, 425, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'No Parent Information', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1950, 425, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1951, 425, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'No Parent Information', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1952, 425, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1953, 425, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1954, 425, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'No Parent Information', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1955, 425, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1956, 426, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, 1, '2026-06-09 00:02:15', NULL, NULL, '2026-06-03 10:09:31', '2026-06-08 16:02:15'),
(1957, 426, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 23:44:50', '23:44:50', NULL, '2026-06-03 10:09:31', '2026-06-16 15:44:50'),
(1958, 426, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 23:44:50', '23:44:50', NULL, '2026-06-03 10:09:31', '2026-06-16 15:44:50'),
(1959, 426, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1960, 426, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1961, 426, 10, NULL, NULL, 'confirmed', '2026-06-09 00:19:26', NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, 1, NULL, NULL, '01:13:14', NULL, '2026-06-03 10:09:31', '2026-06-08 17:13:14'),
(1962, 426, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(1963, 427, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:19:14', '2026-06-03 10:19:14'),
(1964, 427, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:19:14', '2026-06-03 10:19:14'),
(1965, 427, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:19:14', '2026-06-03 10:19:14'),
(1966, 427, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:19:14', '2026-06-03 10:19:14'),
(1967, 427, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:19:14', '2026-06-03 10:19:14'),
(1968, 427, 10, NULL, NULL, 'declined', '2026-06-03 18:19:25', NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, 1, 1, '2026-06-03 18:20:04', NULL, NULL, '2026-06-03 10:19:14', '2026-06-03 10:20:04'),
(1969, 427, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-03 10:19:14', '2026-06-03 10:19:14'),
(1970, 428, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 15:57:07', '2026-06-08 15:57:08'),
(1971, 428, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 15:57:07', '2026-06-08 15:57:08'),
(1972, 428, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 15:57:07', '2026-06-08 15:57:08'),
(1973, 428, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 15:57:07', '2026-06-08 15:57:07'),
(1974, 428, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 15:57:07', '2026-06-08 15:57:07'),
(1975, 428, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 15:57:07', '2026-06-08 15:57:08'),
(1976, 428, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 15:57:07', '2026-06-08 15:57:07'),
(1977, 429, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:03:23', '2026-06-08 16:03:24'),
(1978, 429, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:03:23', '2026-06-08 16:03:24'),
(1979, 429, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:03:23', '2026-06-08 16:03:24'),
(1980, 429, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:03:23', '2026-06-08 16:03:23'),
(1981, 429, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:03:23', '2026-06-08 16:03:23'),
(1982, 429, 10, NULL, NULL, 'declined', '2026-06-09 00:19:19', NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:03:23', '2026-06-08 16:19:19'),
(1983, 429, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 16:03:23', '2026-06-08 16:03:23'),
(1985, 426, 10, NULL, NULL, 'confirmed', NULL, NULL, NULL, 'Erza Rhias', NULL, NULL, 0, 0, 'Contact: 09269749522', NULL, 1, NULL, '2026-06-16 23:49:42', '23:49:42', NULL, '2026-06-08 16:50:57', '2026-06-16 15:49:42'),
(1986, 426, 10, NULL, NULL, 'confirmed', NULL, NULL, NULL, 'Nancy', NULL, NULL, 0, 0, NULL, NULL, 1, NULL, NULL, '01:07:23', NULL, '2026-06-08 17:07:23', '2026-06-08 17:07:23'),
(1987, 430, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, NULL, NULL, '12:36:44', NULL, '2026-06-08 17:33:13', '2026-06-09 04:36:44'),
(1988, 430, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, NULL, NULL, '01:56:16', NULL, '2026-06-08 17:33:13', '2026-06-08 17:56:16'),
(1989, 430, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 17:33:13', '2026-06-08 17:33:13'),
(1990, 430, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, 1, NULL, NULL, '01:57:44', NULL, '2026-06-08 17:33:13', '2026-06-08 17:57:44'),
(1991, 430, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, 1, 1, '2026-06-09 12:43:57', '12:45:35', NULL, '2026-06-08 17:33:13', '2026-06-09 04:45:35'),
(1992, 430, 10, NULL, NULL, 'declined', '2026-06-09 01:43:54', 'gikalinyura', '2026-06-09 01:43:54', 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-08 17:33:13', '2026-06-08 17:43:54'),
(1993, 430, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, 1, 1, '2026-06-09 12:44:05', NULL, NULL, '2026-06-08 17:33:13', '2026-06-09 04:44:05'),
(1994, 431, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 0, 1, '2026-06-10 18:05:01', '17:46:46', NULL, '2026-06-10 09:45:32', '2026-06-10 10:05:01'),
(1995, 431, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, 1, '2026-06-10 17:51:27', '17:51:00', NULL, '2026-06-10 09:45:32', '2026-06-10 09:51:27'),
(1996, 431, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, 1, NULL, '2026-06-10 18:01:25', '18:01:25', NULL, '2026-06-10 09:45:32', '2026-06-10 10:01:25'),
(1997, 431, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, 1, NULL, '2026-06-10 18:02:23', '18:02:23', NULL, '2026-06-10 09:45:32', '2026-06-10 10:02:23'),
(1998, 431, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, 1, NULL, '2026-06-10 18:02:58', '18:02:58', NULL, '2026-06-10 09:45:33', '2026-06-10 10:02:58'),
(1999, 431, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, 1, NULL, '2026-06-10 18:03:16', '18:03:16', NULL, '2026-06-10 09:45:33', '2026-06-10 10:03:16'),
(2000, 432, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, NULL, '2026-06-10 18:09:31', '18:09:31', NULL, '2026-06-10 10:07:44', '2026-06-10 10:09:31'),
(2001, 432, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-10 10:07:44', '2026-06-10 10:07:44'),
(2002, 432, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-10 10:07:44', '2026-06-10 10:07:44'),
(2003, 432, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-10 10:07:44', '2026-06-10 10:07:44'),
(2004, 432, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-10 10:07:44', '2026-06-10 10:07:44'),
(2005, 432, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-10 10:07:44', '2026-06-10 10:07:44'),
(2006, 433, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 15:09:44', '15:09:44', NULL, '2026-06-16 04:18:38', '2026-06-16 07:09:44'),
(2007, 433, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 04:18:38', '2026-06-16 04:18:38'),
(2008, 433, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 04:18:38', '2026-06-16 04:18:38'),
(2009, 433, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 04:18:38', '2026-06-16 04:18:38'),
(2010, 433, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 15:20:12', '15:20:12', NULL, '2026-06-16 04:18:38', '2026-06-16 07:20:12'),
(2011, 433, 10, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 04:18:38', '2026-06-16 04:18:38'),
(2012, 434, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 15:40:20', '15:40:20', NULL, '2026-06-16 07:33:56', '2026-06-16 07:40:20'),
(2013, 434, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 22:47:10', '22:47:10', NULL, '2026-06-16 07:33:56', '2026-06-16 14:47:10'),
(2014, 434, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 23:34:48', '23:34:48', NULL, '2026-06-16 07:33:56', '2026-06-16 15:34:48'),
(2015, 434, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 07:33:56', '2026-06-16 07:33:56'),
(2016, 434, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 07:33:56', '2026-06-16 07:33:56'),
(2017, 434, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 07:33:56', '2026-06-16 07:33:56'),
(2018, 434, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 07:33:56', '2026-06-16 07:33:56'),
(2019, 435, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 17:39:22', '17:39:22', NULL, '2026-06-16 09:35:05', '2026-06-16 09:39:22'),
(2020, 435, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 09:35:05', '2026-06-16 09:35:06'),
(2021, 435, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 09:35:05', '2026-06-16 09:35:06'),
(2022, 435, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 09:35:05', '2026-06-16 09:35:05'),
(2023, 435, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, 1, 1, '2026-06-16 17:42:33', NULL, NULL, '2026-06-16 09:35:05', '2026-06-16 09:42:33'),
(2024, 435, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 17:39:39', '17:39:39', NULL, '2026-06-16 09:35:05', '2026-06-16 09:39:39'),
(2025, 435, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 09:35:05', '2026-06-16 09:35:05'),
(2026, 436, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 15:36:42', '2026-06-16 15:36:43'),
(2027, 436, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, NULL, '2026-06-16 23:38:13', '23:38:13', NULL, '2026-06-16 15:36:42', '2026-06-16 15:38:13'),
(2028, 436, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 15:36:42', '2026-06-16 15:36:42'),
(2029, 436, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 15:36:42', '2026-06-16 15:36:42'),
(2030, 436, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 15:36:42', '2026-06-16 15:36:43'),
(2031, 436, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-16 15:36:42', '2026-06-16 15:36:42'),
(2032, 437, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 09:02:58', '2026-06-17 09:02:59'),
(2033, 437, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 09:02:58', '2026-06-17 09:02:59'),
(2034, 437, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 09:02:58', '2026-06-17 09:02:58'),
(2035, 437, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 09:02:58', '2026-06-17 09:02:58'),
(2036, 437, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-17 09:02:58', '2026-06-17 09:02:59'),
(2037, 437, 11, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, 1, NULL, '2026-06-17 17:04:08', '17:04:08', NULL, '2026-06-17 09:02:58', '2026-06-17 09:04:08'),
(2038, 438, 12, NULL, NULL, 'pending', NULL, NULL, NULL, 'Brown, Lea Evan', 'Maria Brown', 'Purok 8', 0, 0, NULL, NULL, 1, 1, '2026-06-24 16:26:12', NULL, NULL, '2026-06-22 09:36:18', '2026-06-24 08:26:12'),
(2039, 438, 12, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Bia Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, 1, 1, '2026-06-24 16:28:28', NULL, NULL, '2026-06-22 09:36:18', '2026-06-24 08:28:28'),
(2040, 438, 12, NULL, NULL, 'pending', NULL, NULL, NULL, 'Dove, Niel Tan', 'Alex Dove', 'Purok 5', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-22 09:36:18', '2026-06-22 09:36:18'),
(2041, 438, 12, NULL, NULL, 'pending', NULL, NULL, NULL, 'Martines, Sara Bondalo', 'Arnold Bin Martines', 'Purok 4', 0, 0, NULL, NULL, 1, NULL, '2026-06-22 17:37:45', '17:37:45', NULL, '2026-06-22 09:36:18', '2026-06-22 09:37:45'),
(2042, 438, 12, NULL, NULL, 'pending', NULL, NULL, NULL, 'Peace, Nel Bin', 'Elsa Bin Peace', 'Purok 1', 0, 0, NULL, NULL, 1, NULL, '2026-06-22 17:38:05', '17:38:05', NULL, '2026-06-22 09:36:18', '2026-06-22 09:38:05'),
(2043, 438, 12, NULL, NULL, 'pending', NULL, NULL, NULL, 'Rhias, Rence Ong', 'Erza Rhias', 'Purok 2', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-06-22 09:36:18', '2026-06-22 09:36:18'),
(2044, 438, 12, NULL, NULL, 'pending', NULL, NULL, NULL, 'Santiago, Ethan Cruz', 'Maria Del Santiago', 'Purok 3', 0, 0, NULL, NULL, 1, NULL, '2026-06-22 17:37:53', '17:37:53', NULL, '2026-06-22 09:36:18', '2026-06-22 09:37:53');

-- --------------------------------------------------------

--
-- Table structure for table `feeding_program_proposals`
--

CREATE TABLE `feeding_program_proposals` (
  `proposal_id` int(11) NOT NULL,
  `created_by_user_id` int(11) NOT NULL,
  `bns_id` int(11) NOT NULL,
  `barangay_code` varchar(20) NOT NULL,
  `proponent` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `proposal_title` varchar(255) NOT NULL,
  `program_type` varchar(50) NOT NULL,
  `target_beneficiaries` text NOT NULL,
  `num_beneficiaries` int(11) DEFAULT 0,
  `implementation_days` int(10) UNSIGNED DEFAULT 120,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `feeding_schedule` text DEFAULT NULL,
  `estimated_budget` decimal(12,2) DEFAULT 0.00,
  `funding_source` varchar(255) DEFAULT NULL,
  `resources_needed` text DEFAULT NULL,
  `objectives` text NOT NULL,
  `rationale` text NOT NULL,
  `implementation_plan` text DEFAULT NULL,
  `monitoring_plan` text DEFAULT NULL,
  `signature_data` longtext DEFAULT NULL COMMENT 'Base64 encoded signature image from Committee Chair',
  `affected_children_data` text DEFAULT NULL,
  `budget_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`budget_items`)),
  `status` varchar(50) DEFAULT 'Draft',
  `attachment_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `submitted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feeding_program_proposals`
--

INSERT INTO `feeding_program_proposals` (`proposal_id`, `created_by_user_id`, `bns_id`, `barangay_code`, `proponent`, `location`, `proposal_title`, `program_type`, `target_beneficiaries`, `num_beneficiaries`, `implementation_days`, `start_date`, `end_date`, `feeding_schedule`, `estimated_budget`, `funding_source`, `resources_needed`, `objectives`, `rationale`, `implementation_plan`, `monitoring_plan`, `signature_data`, `affected_children_data`, `budget_items`, `status`, `attachment_path`, `created_at`, `updated_at`, `submitted_at`) VALUES
(7, 41, 41, '112402015', 'Committee on Health, Sangguniang Barangay', 'Barangay Bayabas Health Center, Davao City', 'Supplementary Feeding Program for Malnourished Children', 'Supplementary Feeding', '20 Children (11 Boys and 9 Girls)', 20, 120, '2026-05-15', '2026-09-12', 'Monday to friday 7:00 to 8:00 am', 168000.00, 'Barangay BCPC Fund', NULL, '•	To provide consistent nutritional support to the 20 identified malnourished children in Barangay Bayabas.\r\n•	To achieve a significant improvement in the weight-for-age status of all beneficiaries within the 120-day implementation period.\r\n•	To empower parents through orientations on how to prepare affordable, balanced, and nutritious meals for their families.', 'Childhood malnutrition remains a significant health concern that requires immediate and sustained intervention. Based on the most recent nutritional assessments conducted in the community, 20 children have been identified as malnourished or underweight. This Supplementary Feeding Program is a strategic effort to address these nutritional deficiencies. By providing consistent, nutrient-dense meals, the program aims to bridge the caloric gap and support the physical recovery and long-term health of the identified children.', 'This program involves the daily distribution of healthy meals to the 11 boys and 9 girls identified as the primary beneficiaries. Managed by the Committee on Health with the direct assistance of the Barangay Nutrition Scholar (BNS), the project focuses on delivering high-protein and vitamin-rich food. The meals are specifically prepared to meet the dietary requirements necessary for the beneficiaries to transition from a malnourished state to a normal, healthy weight.', NULL, 'data:image/png;base64,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', NULL, '[{\"item\":\"Rice \\/ Malagkit\",\"daily_cost\":15,\"total\":36000},{\"item\":\"Protein (Chicken\\/Egg\\/Meat)\",\"daily_cost\":30,\"total\":72000},{\"item\":\"Vegetables & Condiments\",\"daily_cost\":15,\"total\":36000},{\"item\":\"Mineral Water\",\"daily_cost\":10,\"total\":24000}]', 'Approved', NULL, '2026-06-02 05:40:08', '2026-06-02 05:44:04', '2026-06-02 05:43:13'),
(8, 41, 41, '112402015', 'Committee on Health, Sangguniang Barangay', 'Barangay Bayabas Health Center, Davao City', 'Supplementary Feeding Program for Malnourished Children', 'Supplementary Feeding', '20 Children (11 Boys and 9 Girls)', 20, 120, '2026-06-05', '2026-10-03', 'Monday to friday 7:00 to 8:00 am', 168024.00, 'Barangay BCPC Fund', NULL, '• To provide consistent nutritional support to the 20 identified malnourished children in Barangay Bayabas.\r\n• To achieve a significant improvement in the weight-for-age status of all beneficiaries within the 120-day implementation period.\r\n• To empower parents through orientations on how to prepare affordable, balanced, and nutritious meals for their families.', 'Childhood malnutrition remains a significant health concern that requires immediate and sustained intervention. Based on the most recent nutritional assessments conducted in the community, 20 children have been identified as malnourished or underweight. This Supplementary Feeding Program is a strategic effort to address these nutritional deficiencies. By providing consistent, nutrient-dense meals, the program aims to bridge the caloric gap and support the physical recovery and long-term health of the identified children.', 'This program involves the daily distribution of healthy meals to the 11 boys and 9 girls identified as the primary beneficiaries. Managed by the Committee on Health with the direct assistance of the Barangay Nutrition Scholar (BNS), the project focuses on delivering high-protein and vitamin-rich food. The meals are specifically prepared to meet the dietary requirements necessary for the beneficiaries to transition from a malnourished state to a normal, healthy weight.', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAlgAAACWCAYAAAACG/YxAAAQAElEQVR4AezdC7B+X13X8UcBM5AwBcTCQhFRKxUFxcIsG4uYwiYsyZwMo8nMUnJoyqDLlAmUpZQzXbCyJqC8jDXZVU0RJ/AGTDlC0kXLFAMxBNSAv31fv//+8t+/53/Oc55zznPZl8+Z/T1r7b3XXvu73mvvvT577bX3876b/IVACIRACIRACIRACByUQATWQXEmsxAIgRAIgcMQSC4hMG8CEVjzrr94HwIhEAIhEAIhMEECEVgTrJS4FAKHIJA8QiAEQiAEzkcgAut87LPnEAiBEAiBEAiBhRKIwLq0YrMiBEIgBEIgBEIgBG5GIALrZtyyVQiEQAiEQAich0D2OgsCEVizqKY4GQIhEAIhEAIhMCcCEVhzqq34GgIhcAgCySMEQiAEjk4gAuvoiLODEAiBEAiBEAiBtRGIwFpbjR+ivMkjBEIgBEIgBEJgJ4EIrJ14sjIEQiAEQiAEQmAuBKbkZwTWlGojvoRACIRACIRACCyCQATWIqoxhQiBEAiBQxBIHiEQAociEIF1KJLJJwRCIARCIARCIAQGAhFYA4gEIXAIAskjBEIgBEIgBBCIwEIhFgIhEAIhEAIhEAIHJDAxgXXAkiWrEAiBEAiBEAiBEDgTgQisM4HPbkMgBEIgBGZEIK6GwDUJRGBdE1iSh0AIhEAIhEAIhMBVBCKwriKU9SEQAocgkDxC4JAEHnvIzJJXCByDQATWMagmzxAIgRAIgWMR+K7K+I1lLyjLFAKTJRCBNdmq2XIssyEQAiEQAgg80L+yB5VlCoHJEojAmmzVxLEQCIEQCIELCLxrWPbmIUxwZgLZ/cUEIrAu5pKlIRACIRAC0yPwmHLp08tM/8O/WAhMlUAE1lRrJn6FQAishECKeQ0Cf3BIS1y9bIgnCIFJEojAmmS1xKkQCIEQCIEtAnqv/vyw7FlDmCAEJksgAmuyVRPH9iWQdCEQAqsgMO69+s5VlDiFnDWBCKxZV1+cD4EQCIFVEEjv1SqqeVmFfN/NZlkFSmlCIARCIAQWR+CrhxL9ZIXpvSoImaZPID1Y06+jeBgCIRAC6yRwX6k/a4g+ZwgThMDkCURgTb6K4mAIhEAIrJrAXxhK783Blw/xBCEweQIRWJOvojgYAjcmkA1DYAkEPn8oxF8cwgQhMAsCEVizqKY4GQIhEAKrJPCbqtQGuOu9+ocVzxQCsyEQgbWrqrIuBEIgBELgnAT+wbDzrx/CBCEwGwIRWLOpqjgaAiEQAqsioOeK6b3qcVirArCrsFk3fQIRWNOvo3gYAiEQAmsk0F9tT+/VGmt/AWWOwFpAJaYIIRAC1yWQ9BMnoOeqv9ye3quJV1bcu5hABNbFXLI0BEIgBELgfARaXOXNwfPVQfZ8SwIRWLcEuNbNU+4QCIEQOBIBvVf9eDBvDh4JcrI9PoEIrOMzzh5CIARCIAT2J9A/i/Oq2sQA9woyhcDeBCaTMAJrMlURR0IgBEJg9QT0XvXP4jx39TQCYNYEIrBmXX1xPgRCIAQOTOC82fV3r4y9euV5XcneQ+B2BCKwbscvW4dACFyfwPvVJh9f9sHXsI+8IO0H1bJMyyHgq+3MY8G8Obicel1tSSKwVlv1KfiRCKwl20dWQT+q7JPLPrPs95T94TKPdb6iwq8t+ydl31r2PWU/VPa/yt5e9gtlry178zXsRy5I+5Za9otl/31k/6HiekHaNNTeSGMab+YxVCXLNCEC6kTdcSnfvUIhNnsCEVizr8IUIASOSkDDR5wQKkSLRpCoeVPt9Q1lry77d2X/rOzvlr2o7MvLvqjsc8ueVvbryz627FeWPaTsPWX3lP1sGZF0lb2j0kn/tgo7LaFWs3cmPrYRUPxt8zYavxnfGUGmDK+praW3bUUznZGA+rL79F6hEFsEgekJrEVgTSFCYFYECAxCg4hiRAgBwogR4oRQ0QhKp3BEksbw+2vm28q+sewlZX+t7HllX1z2eWW/o+wpZb+27NFlDy17YNkDyn5Z2cP3sF9dafSYfUSFjy97chmfe9tPrPknlH142W8ue9bIjOXxqj/7zlrO9Jy9u+KfUKasyqfc8qxFmU5MAHfHl92qP2EsBGZPIAJr9lWYAoTAtQlo0AgK1iKK0NDIsRZRMiaiiBJChWkA36dWEEkEzZMqvu8jwh+vtOOep5q9cOIfH9o/gmhs248Lf7By0RulN+3zK24iqJg8WnDxnT2iEjyoTHmUz76Um5j8iVpuGz5UNNMJCBC4dtP1IT5Ji1MhcB0CEVjXoZW0ITBPAsQC0cBaUBEUTIn05hgfRZBo5IgQ4omQEpq3LSO2bHNoax8JPUJH2P55PPjW2qHHg7seF+rl0sumwe5yilv2YbX99qQ8Xb7XDysfVaH98oFJw7danOkIBPAlcAld8SPsIlmGwHkIRGCdh3v2GgLHJEAQaKxYCw2igdmvxoxQajGlN4cA0dNjG+ukkfaAdldW7SMhNfZRYyuh/bd/Hid6Y9AjwQ+oleYfVqF55rEjocSUgf+1emMfxBWR9WObzUbvmcH1/7ziX1P2nLLfXWZs1+dU2NsTmjW7sT1mesx62SZ/FxLoN0K97fnYC1Pcf2HztUZdC2MhsBgCEViLqcoUZMUENFSEERuLFeIAFmKF6NCI6Y0iJITSWy7NqYzg8ThP7xD/LhJU3XN2Hf+UkRFCyqaMjOCyL71gBtj7PMTTq7B/ouyvl31T2Q+Uva7s28uIg79fYW/rW0weh3r0KJ/2t5JkGgh8S4X9CJcYfWPNv6DsqsmxII1jUL2Jx0JgMQQisGZUlXE1BAYCcxJUXCZK9CIRf0KDyy03MJ7ou4mgsv0uI7aYhtvgeL1eerueWBs9o+zLyl5c9i/KfrLMJJ0B+q+oGYP3f2uFX1dmUL28cP/3NS/PCjINBPrL6+8c5gUf498Ow5LAloQIFsZCYFEEIrAWVZ0pzIIJaJD06BiErSdF48QUWeOvF4BY6d4bofSWS3Nq0ztBTP2f2rHHgOYruiFONKhElW9n8XFzoj9juPRWfXPtT+/Vl1RIHHxohXqpfFLC7+D9cM175PX7KlQGv4nnrUmfpZBujb1Zlz366/pznOkhNJavsF05OSYkcsw6fsVj8yUQzy8gEIF1AZQsCoGJENDzowHT89OiyiBsg9I1+honQsrjLKG0Grpzuu9xEX8JE6JKrxF/9Vbxk7giss7p40X7JqD+da0wLss3u3wO4o/XvDFbyuPRoWW16M5E8Po8hW3E7yxc6L/vqnJd9uivRb5jsZLtNTlOMSOsxPfaKIlCYG4EIrDmVmPxd8kENDoaHOJEo+4uvxswjZFGjJAyKP1TC4S05xZU5cadie/81SNkAUFFTBFV/NVbpQzWzcH+Szn5t8p8X8t1Enf8vW1IMNaqjceOT62IMUcvq/Dm07S31GvXHnqM2nHfPRMnQvs49DKBZZgIt81x0se042N7feZDYDEEXDgWU5gUJARmSECDQygRJ91LpedHUQgSjTqRwqTrhsz6qZieNr4L+UyMEFR6qsxPxc/b+IE7/sYWPa4yUrZ+HEZoPbOWPb9sidN/HhVK2c06bv+QSNkLy3p61xAx6H2I3hW4ebAATyYeC4FFEojAWmS1rq5QcyuwxkljPR5PRZwoh0aHqDJGqUXVlEUKMUgctu98VgbzSzX1offl06qA31vmDUWPGC/rtakks54+afDeTxt5QYG4bKEk/vJhveDX+Vf282Xbk2OcWU6EC2MhsFgCEViLrdoUbGIEWlQRI3p7PCbp8VS+yaTBJqo0PMTXxNy/nzuElQHs3dAShXy/X8IFLyC0PqXKpwfLY7SlPib0zbEq5uZH/SszyL+FkuO2Ft2ZHOO+V4bL37iz5O5/42Pl7jWZC4EFErhXYC2wYClSCEyEgIZIw9KiyjzXCBKNk/FJX1oL9ARUMPlpPIi9B7B7224OonDycCfoINHkDUKu6Z3ykVZx5vgVtnkDU9ygeOHYHDfy8lg1x8qYTOKLJRCBtdiqTcHOSEBDohHpgep6e7jjzp6w0lNl/VxEFd8ZoTgexK7HikAcPyKSLrYcAo5lpXHsjkPx8aNgNw59bPTgd2mYPHrdcy24jiVtCMyVQATWXGsufk+NgEaEaCKqureKjxqmFlXGJ0lj+dyMuGqhSFgZxD5uYOdWnvi7HwG9q1LqlXKM+3aYeUZUCZlH3kLHuq/fi7f1OsdLxHhTSbh4AhFYi6/iFPCIBDQ4BNN4sLrdtagiqJg0lp/BDrJLj3fG4kpDeZCMk8nkCXTPk14pIpvDLaCMxTJvHbHli/jbx7pzpI+d7UeKto2FwGIJRGAttmpTsCMS0GhoSLqnqgerf33tU+9OiypCqxbNetKodiNrrFXE1ayr81rOO8Zt4Dg2iJ+IMu/tScvM+zxFf67BR1qtH5vjx7yeLduIx0JgFQQisK6o5qwOgYHAWFS1sLJKo6HxMBbJnfqSBMh2z1Ue76jx9Vj3ULlxaKHUvVB9nH/lgMM5sH18EGhEmHNEfEiaIATWQSACax31nFLenABhpXFpUWVeg6FB0VPFlth4KPOaeq58QNPbcn6DUKiX5uZHzfy3dJwzx/qjqzjifp6pX8x4XS0zWX7Zm4E99opAkzZ2WALJbeIEIrAmXkFx7ywENBpEE1HF9ExxRGPjDr5FlXnLl2bG1HSZPfLc7plYWnkN4PbTOH5vzxfIha+oQv5UmZ/L+b0VegxcwWqmrn+fZehHgL7XpkfKt9zG37l60QVUnD8WO0c6bj4WAqshEIG1mqpOQfcgoFHRc0NUufsmtDQQeqt8WoGw6jv4PbKbbZLuuTKmph8FzbYwlzp+3wqiytuf9y25N/aICv5Y2T8t8yKDD6sSC46LWrTYSfkc/wr4M/6VGcD+Ryokroisim4IU6FeP2HbeHs3JL08YQisikAE1qqqO4W9gIDGQKOpgSWuiCzJiCq9N0SV9ZatwfDwAVHCsj8cufRyP6MK6FqorpW7Zu9MP13/v6NM71YFG1wIDwKcOS7wsm5J1jcR/6oKpbwVbPTgtbBybrjhUH7rpBlzMG85cc7EYyGwOgIuKqsrdAp8EAJzz0Tj4G5cQ9kNgsZ13HissXHoxrV7J+Zez9fxX/0T1R6L2c7PvnxGRT6qzDgjy4mOmt0QFI4bx89rNpsN8fGYzTL+fs1QjCcNYQfjc8My54feP/Ev9K8Mg75JSe9VAcm0XgIRWOut+zWW3MWfsNJbpXHUKGpUNRx6L5j1a2SjzHrwPl2kzDisClY3OR5+V5XasUAgtOA00Nuj06fVuh8r+/ay15e9u8wPIBPr+Dl+HGe1eHYTv/Va6qnjvEekQoPUxz1WlrV9zxDp4wYDi5xTWIrHQuDEBKaxuwisadRDvDguAY2eMTR6Gwgre3Px1whoSK03b/la7aVV8O550IvTH5OsxaucHA/EFZHlGBHqqSLOf1UR+S1lH13m+1BvqvBnywh2x5fjzLa1aBYTYeUcdhnjNAAAEABJREFU4Hd/qV05OY9DHxfmt40YlebJtYI4w8C8/GpRphBYL4EIrPXW/dJL3o2GhkKjZwyJ3gZ34xpMlkbg3qPA9658RNQcceXRj3jsXgIEA8H0iTXrmvkbK3xe2b8t+7myDyl7aNl48g2pb6gFTy+b4tTnB1HFnCP8/Hn/yvRYVbAhLIW7zDllfYuznrcsFgKrJeBisdrCp+CLJOAO2mOKcaOhgdRb1R8DNb/Iwt+wUB84bPdVFUZcFYQrpu+u9V9R9tSyB5dtC65adGf67Ppv3BaRzxyTbR4ptjle24h+pteozTHNiCLmrb2bmseZ8ucHUSU/54Pzw7e/3r987snyfY4H4rPfNjRWTf6dR8IQWC2BCKzVVv2iCq6RcFHXiGm0NEwKqNHQU8Wst+wENrtd4Mbpt/gXuzaBiwTX11Uu/69sPDlO2wimNsdrG9HDWnAJHdOMKGIGlt/UPOaUP78IqD5HnB8ed1retk/vVadtkW5sWi9LGAKrJhCBterqn33hNVYaII3OdqPhEYdGQyMy+4IeuQAfN+Tfj4eG2QQ3JEBwPbu2fXxZH39CPUTEPvMolhExYyN49Ai16UFqk4dPR9xT+b61jCC+jr29tnlP2T8q4wNzjtTsnYnguxOpf3qi7LeiV0597kno0ajzUjwWAqsmMEmBteoaSeGvIuDirVEgqpg7f9tokDRU242GdbHLCeDpcwQa7/HXuS/fImv2JYCp45FQwZnwImIst4w5bsfm2HYctxFhbfLyaPAB5YA687bfdcw4Mb1URBAfKpu7prFQ8rj4rpWXzChXn4NEmWTKKIyFwKoJRGCtuvpnVXjjXDQ+RJWGwIVdI+GOX2+VBklDNatCTcDZZvbqCfiyVBcIpOasx9XbdlMrq/Np7NO+PiqP7ZyHzxcpc35WcNTJWDK/GfnYo+7l/plnSQjsTSACa29USXgmAt7ccufvw5d94SasCCp39ETXmVxb1G5ft6jSTK8wjlcD3nnmbbsWJuanYN0LxRfnl/Aqc+7prZJenIg0NoxYe8FVG19z/eMq/ReU2YfHo8aS+c3IN9ayQ++rsswUArcnEIF1e4bJ4TgEHlnZ/s2yHyh7SplxJ34TjqhiLrS1ONMtCXhsJAvfdhIe19adu29G6c1CgaDRG0uMmD+3eWzYPuz7mYW+4Rmn3/7waOd5m/CHamM/V+TFAX4aUN8vZtSqOz9hJIyFwKQIRGBNqjrizEDgT1eo8fniCk0vrn++NfTMCt0tV5DpQAR+YcjnnUOY4LgEjLtyg+A4Jq68Haj357h73Z07P1i/5MDH3VtsNr6dtqk/PwI99p+IVDYfHiUiK8mNJz65DnzskMMrKtT794QKtV0G/Fd0Q4AJYyEwKQIO0kk5FGd2Elj6ys+rAr6h7CvLfF/omyt0MXVR9eihZjMdmMB/GvLDfYgmODIBAkRPlnFLRMSfrf19adm5phZC/Q2sqwQWn/1sEH+f49+WdY+W3qatVXvP8om4si+8iFI/x+Nm67WVi+UG+luXlzMKSKbpEYjAml6drNEj4zh8P+cfV+H9sO73Vvg7y55R5mJaQaYQOCqBUw+WJgz0/BiX5c2+Lztq6XZnTrh0Cn51/LKQ+LFO2peLbJlyWee87rRbSXbO/nCt7TFqhgIQV/Krxe+dLDeDnzB2dgJxYJtABNY2kcyfkoC3gNztekzyGbXjHy/7wrJPKfuXZZlC4BQECPpzDZbWc0U8PLoKSpBUcNJJT9B4v87HqxzosVcG7l+WtvO5Ti8WP/Ra+Y1H+fr5psv2YciANK/yLxYCUyQQgTXFWlm+T7+0iuinRrwF9AcqbvrL9e8jyv5OWaYQOBUBb6c+adjZ5w7hKQPiyqPCTe20e20qerKJwLKzfcdf6Z2Snt+7HiVK5+dziCYi0ja7THo3WvyRr16ri3rH5CFNi7DL0kgXC4GzEojAOiv+Ve78i6rU/63sy8tMuvo9nvENne2fFrE+dlwCTxyyN9ZtiK4q8Ei6C+wr5x0/ZUhQGGNIODzzlDuufRFAFWz2HX/VvVctCm17mRGv1nn8qWzi22b/hNU4X+PTCLjttD3fee1K02kThsDZCERgnQ396nb89Crx95V9bdmjyr6tzNgPjwAIrpq9zZRtb0hgzW8REgCOxUZnHGDHTxkSCq8fdugDmkP0JEELGzvjh/Ay08tknXRujMR3Wb9R6PHnRWOx5EdcEVnyJKws25WndZ1G/ZmPhcAkCURgTbJaFufU/6wSGYyqt8QAVo9iPrOWee26gkxnJLDmtwg9kh6jP+fr/r7xxJceWyR+bOueoH486Bzdtc8eT9Xjq3al7XVEE/FEyLXIsl/CyjLpiDWPBPXkmd9lHqO6MZPmJf7FQmCqBN4rsKbqYPyaNQF3pgatuoNVEF9c9k2bl5mJhcCZCWxf/875uj8RAgfxITyF9c/h/Nyws10DxvUa8Y2f4sMmVwbStyAjqDwCdU1wbbCOANOLfWVGlYC4apFmu1fWskwhMFkC2xeYyToax2ZHwEXYXaqLsjtTd6h/ZnalWL7DPQbJjwgvv7T3ldCHMn/FfbMbjf1mAn/Ol1O58RuGHf3yIdw1YPwmvVdDthvXAj8ErWx9c9XXBOHmij+r1ddYXO27nW1jIXAWAhFYZ8G+6J26iBJW7lYV1GBYd5tTacD4FLuPwP8eov3YZZhdfKAHRSH9rp3w3ON5WjA4f/hzbLOfh9dOCJ8KNrvOT6ykl4ZY2lzzz/bdi21TYzFdE8T3MT1X/WFTn25oVvtsmzQhcDYCEVhnQ7/IHbvD3O7+v8kFeZFwzlao3Tv+e8Pq31ahRrSCVUxvG0rZvTdTGM9DwHDrFPXgXLWv/+pfWT/Gq+j9JgLHwl1prN825XCzxazzpqTQZzH2vS5s91zt6mWTdywEJkMgAmsyVTF7R1xE+0J8nUGrsy/4zAugN8DvySlGj8kRX7LprfqwUQH1sk5hPM8pBVb3MP+SEYeLokQS49u+okg+eq3GN1sYP6JWCCvY2L/rhPhl5nqSnqvL6GT55AlEYF1dRUmxm4AL6S9WEmEFG13/+w5alT52fgKvHlz4pAq7Hiu62Gn89qAvuF9HOBwTygcOmT91CI8VEEzyJpqeLFJ2GQMip1ZvrtN75U1hN1y2I+CNv+z8hf22onFdL6xExv/5VQcmzl5ay7uXzTUlPVcFJNO8CERgzau+puati+/2hdQFdWp+xp/dBHyvSM+CcTLqtBvg3VvNd+37Da6/o8LHlU1laoHVj9KO5VcLlxY6hNZF+3IcENzWE0YXpRkvk1avVX9l/WtqJXFUwV1TH28W/qn6p7x+1YGJM2OtatXG9rmmIHGhZeGUCURgTbl2puubCylh1RdqjbMLoQvxdL2OZ7sIeFyjIdOoqttdaee+7pFDAV47hFMJ+vz5/iM75PHceBfqfTzf8U63T+8VAea4cfzIT6/Vrp/Ikf7f1I7uKXtnmV5wZmzc22veV/X/ZIXyqiBTCMyPQATW/Ors3B67MLqQElkaBMLKsnP7lf3fjoC69GhXqJHUk3W7HKe5tbK1Z6/uyERC5xRXjikq+qZIPfutQPv7Uf+2DCdppdt1fkvnetBi7Do3W7+99vmAsoeUaYvYwyr+0LIHlp3zu2S1+0whcDsCDujb5ZCt10Jg+0Kqx8Nd6jEbg7WwnUo5NaYEM380rrsaVmnmaC0E+P4G/yZizi+uqAPhsaw/x6FXqllcdA73Ouku88Ux4pEgYchvx84Sj5nLyp/lIbCTQATWTjxZORBwAd2+kD5rsxnWJlgSgW4olUkjS0iLL8GIGKKgy/KEjqwkHJe/61V9bwuscbrLBJNtupdTPDdbKzmIUsz9CURg7c9qrSl1/zPlz4UUheWbeu7fxvOm12WN7NxItKj4qcFxY3+G6NkDooYTBI/wGNbi0j7cNNmHuHBsnc7jvvHyjhNW3RNmMLqeq16XMASmQWACXkRgTaASJuqCC3D3WnHR+JxcSJFYhz27itkNrJ6suYussSjor7c/uMo4lcn5xhff6BIew1oUeezXgm57f5arb/u/qM5xbAHmepDPJyAVC4ELCERgXQAlizYurHqtXGz1ZriQ9t3/Jn+rIeA4WILI2hYF7x5q8OOHcApBC6z+VMOhfXIu9z7Ua4st5/d4Xy2eut7H67Y5bm87Tpt4CKyeQATW6g+BuwC4CBNWfQfrIktc5UJ6F6ZVzWiMHQcK7bj4iYpY5lip6OSni0TB6wavP7VCZang7NOHDB70bwMOswcLWji5UVJ3LbbG57bl6thOpRO2XcSx1yUMgRC4gEAE1gVQVrrIBbcfCRqXQVhNpfGZX5Usy2PHQX+U8lFVNI2wY4VZp2GuxZObvrs8amHheG4x8ftr+Vg0vqDmzz31zxUd6xtYxtIpo0eCXV/Oc8vamhU243XfWAl63ZhjLc4UAiFwGYEIrMvIrGu5hlLPlVJrhPJGEBKxMQFf33ZcGIvXvRsa6hZbr6nERHoFZ5/45Zh+yuDJcyp0XFfw3okwbNFIcNnmvSvPEGl2234ewhVlY0STuusPgBJbnb/16tK8NEL2rfXvGWWmiCsUYiGwJ4GpCqw93U+yWxJwUSWshLLKG0EoxC4j0A00kdVi65WV2JimT6jQscRaLNSik096WogrxzR/+fnVl3hBNBI0fiLor1SaDyo7x8RX++Wv8NDWgqkF5WcNO3jJEApwE+LRflj2NAvL/KSNdRXNFAIhsA+BCKx9KC0zjUawG0MXVHeneSNomXV9jFI5ZjTcn1aZ+z0/ccv6uHJsaaBbPFSyo0/fUnswVqiCDX+IKz5tdvwRi1a7uXhLRc5xDjSjq3wt92409fiuV9XWeu4q2NgXcbypP/vv3qvm4Uegm6UewL9a6TLdIZB/IbAfgQis/TgtLVU3gi6s7ko1RMKllTPlOQ0BjbWG2XEkNO8Y00CfSmjZV/fMEEv82Kf0fH1pJfQ7eBVsPqf+tQip6EkmrOyIL8JD2/sPGRrn1WOxfKphWLzpHj4D7PngTUYCdVN/f7us11c0UwiEwL4EIrD2JbWcdBoPjZ4SEVV6rsRjIXAIAhpmQstxpbEm4okfj+2EjznETrby+Iaa11tWwcZ+r9sLZQyWa6HB3fLQm+M8ET+FtcD6v0famTqQddeHsMtnXQvT50pUpg4fX6Hrwx+tMFMIhMANCLio3GCzbDJTAoSVxoP77vA1RuKxEDg0AY1zCy1x+RNBhJZPPQgJLmY5kfHBlWhsH7k13+ueWMu/oIwQ8FX2z664yfHc+zJ/XSM6xiLLwH2+ESHXzes66ceP8K6z3T5p23eiSllsM+696usBboSpT1gQXD9dCV0jKsgUAiFwEwIRWDehdtZtbrRzF1niSiMmAw2Rxkk8FgLHJKDhdryxHvPjUw+OSQ0+I7Icn28uR8b2I1vzve77armf8vG46xEVf0/ZnyuzrwpuNRFZBoP3wH2+EYP/sXLlK50/GtgAAAYXSURBVL8retDpo4fcCJwherCg/fV4sMWUMtqBdcok/vz6p5wfV6HphfWPKKsgUwiEwE0IRGDdhNq8tiGqXDiFLpgaukM0RPOiEG/PTcAxZ0D8+5Qj3bOlh4TQZ2+s5feUva3MYPN3VDiet4z5/UDLX1Hrv6TMDzY/sMK/VHaoyduFD6rMnCuvr5DY6kHfziViiyAkVJxXhEolu9HU2zo3b5TBFRvxT5IP9a/MN60quDMRjyIEpW+G8YUf6udFVsRC4FoEkvguAhFYd+FY3IyLq4ZAwTRwLpxC87EQOBcBjbjjkLAispg3ER9QDj2s7OFlH1A2nreMPWRY7qdeXlzx15Yda+Ljx1TmF4kt55YeIefXbUSXfGoXG0w2R/zDTfbf5F9Zi0OC1SPBWrRRH64Rx/Zlk78QWAOBCKzl1rK7Uxd/JTSuxN24eCwEQuD6BLbFlvPJeTXu4SKWtkWXR3B62Xr8mNDYsrYeRP6mzWZzfa+u3oIQlYo4FfZjSH6af7B/Zdd587KSZwqBELiKQATWVYTmuZ6wcmHnvYbA3ap4LARC4PYEiC3mvBr3cDnXtkWXG50frF32+DGhsWVtn1zrTO/y7whG9HW2fBb3vTBhG79bePWyhCEQArckEIF1S4AT29wYCuLKRVU3vwtnX1Qn5uoR3EmWIXA+As4zti26emzZW8s1Y8iMLfPNrW07xqNO14Pa7eZnNvf++Wkc14Z+JGhprhEoxELgCAQisI4A9UxZunAaCyJ0oc+F80wVkd2GwEDAedhjy/wMj8d0xpa57m7bVw3bHDJogeXDofI1xso3w8SZx4J8FI+FQAgcmICTvLNMOF8C7pr1XCmBCyZxpQfLfCwEQmCdBNxsdcldD55XM0ReBRviKo8FkYiFwJEIRGAdCewJsyWoesCq8R/E1Ql3n12FQAhMlMBYYPkUAzd9csJvC85MXHE9FgLzIhCBNa/62vZWz1W/JeSO1Px2msyHQAisk0B/IV7pX1X/nl3mkxP5bcECkSkEjk0gAuvYhI+Xv7cEu+dKr1XuSI/HevY5pwCrI2D8VX8h3uPBXB9WdwikwOcmEIF17hq42f51/Xv929Y+0ugxoXgsBEIgBBAgsITM24PCWAiEwAkJRGDtBXtSiYirHtBuzJU3gyblYJwJgRA4O4HxT+K85OzexIEQWCGBCKx5VfpYXBFWGXM1r/qLtyFwKgJ+Zsi+/MA2E48tkUDKNFkCEViTrZr7OabLv3uuPBL0aPB+ibIgBEIgBIrAd5TdU7b91fZalCkEQuAUBCKwTkH59vvYFlcGtd8+1+QQAiGwVALPqILpxTrGB0wr60whEAJXEYjAuorQNNYb0E5k6bmKuJpGncSLEAiBEAiBELiUQATWpWgms8JjQWOvvGo9PXE1GUxxJARCIARCIASmQyACazp1cZEnY3H14RclyLIQCIEQCIEQCIH7Ezj3kgisc9fA5fsfi6sMaL+cU9aEQAiEQAiEwOQIRGBNrkruOOQr7R4LmiGujL0Sj4VACITAiQhkNyEQArchEIF1G3rH3bbHXEVcHZdzcg+BEAiBEAiBgxOIwDo40oNk6COixlxFXB0E53kyyV5DIARCIATWSyACa711n5KHQAiEQAiEQAgcicCEBdaRSpxsQyAEQiAEQiAEQuDIBCKwjgw42YdACIRACCyMQIoTAnsQiMDaA1KShEAIhEAIhEAIhMB1CERgXYdW0oZACByCQPIIgRAIgcUTiMBafBWngCEQAiEQAiEQAqcmEIF1auKH2F/yCIEQCIEQCIEQmDSBCKxJV0+cC4EQCIEQCIH5EIin9xGIwLqPRWIhEAIhEAIhEAIhcBACEVgHwZhMQiAEQuAQBJJHCITAUghEYC2lJlOOEAiBEAiBEAiByRCIwJpMVcSRQxBIHiEQAiEQAiEwBQIRWFOohfgQAiEQAiEQAiGwKAJbAmtRZUthQiAEQiAEQiAEQuAsBCKwzoI9Ow2BEAiBELgWgSQOgZkRiMCaWYXF3RAIgRAIgRAIgekTiMCafh3FwxA4BIHkEQIhEAIhcEICEVgnhJ1dhUAIhEAIhEAIrIPA/wcAAP//FT1KlwAAAAZJREFUAwB+2OR42tQXrwAAAABJRU5ErkJggg==', NULL, '[{\"item\":\"Rice \\/ Malagkit\",\"daily_cost\":15,\"total\":36000},{\"item\":\"Protein (Chicken\\/Egg\\/Meat)\",\"daily_cost\":30,\"total\":72000},{\"item\":\"Vegetables & Condiments\",\"daily_cost\":15,\"total\":36000},{\"item\":\"Mineral Water\",\"daily_cost\":10.01,\"total\":24024}]', 'Approved', NULL, '2026-06-02 13:45:12', '2026-06-02 13:47:03', '2026-06-02 13:45:24');
INSERT INTO `feeding_program_proposals` (`proposal_id`, `created_by_user_id`, `bns_id`, `barangay_code`, `proponent`, `location`, `proposal_title`, `program_type`, `target_beneficiaries`, `num_beneficiaries`, `implementation_days`, `start_date`, `end_date`, `feeding_schedule`, `estimated_budget`, `funding_source`, `resources_needed`, `objectives`, `rationale`, `implementation_plan`, `monitoring_plan`, `signature_data`, `affected_children_data`, `budget_items`, `status`, `attachment_path`, `created_at`, `updated_at`, `submitted_at`) VALUES
(9, 41, 41, '112402015', 'Committee on Health, Sangguniang Barangay', 'Barangay Bayabas Health Center, Davao City', 'Supplementary Feeding Program for Malnourished Children', 'Supplementary Feeding', '10 Children 7 girls and 3 boys', 10, 10, '2026-06-03', '2026-06-13', 'Monday to friday 7:00 to 8:00 am', 8500.00, 'Barangay BCPC Fund', NULL, '• To provide consistent nutritional support to the 10 identified malnourished children in Barangay Bayabas.\r\n• To achieve a significant improvement in the weight-for-age status of all beneficiaries within the 10-day implementation period.\r\n• To empower parents through orientations on how to prepare affordable, balanced, and nutritious meals for their families.', 'Childhood malnutrition remains a significant health concern that requires immediate and sustained intervention. Based on the most recent nutritional assessments conducted in the community, 10 children have been identified as malnourished or underweight. This Supplementary Feeding Program is a strategic effort to address these nutritional deficiencies. By providing consistent, nutrient-dense meals, the program aims to bridge the caloric gap and support the physical recovery and long-term health of the identified children.', 'This program involves the daily distribution of healthy meals to the 11 boys and 9 girls identified as the primary beneficiaries. Managed by the Committee on Health with the direct assistance of the Barangay Nutrition Scholar (BNS), the project focuses on delivering high-protein and vitamin-rich food. The meals are specifically prepared to meet the dietary requirements necessary for the beneficiaries to transition from a malnourished state to a normal, healthy weight.', NULL, 'data:image/png;base64,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', NULL, '[{\"item\":\"Rice \\/ Malagkit\",\"daily_cost\":30,\"total\":3000},{\"item\":\"Protein (Chicken\\/Egg\\/Meat)\",\"daily_cost\":15,\"total\":1500},{\"item\":\"Vegetables & Condiments\",\"daily_cost\":30,\"total\":3000},{\"item\":\"Mineral water\",\"daily_cost\":10,\"total\":1000}]', 'Approved', NULL, '2026-06-03 09:44:00', '2026-06-03 09:46:26', '2026-06-03 09:45:51'),
(10, 41, 41, '112402015', 'Committee on Health, Sangguniang Barangay', 'Barangay Bayabas Health Center, Davao City', 'Supplementary Feeding Program for Malnourished Children', 'Supplementary Feeding', '7 Children 4 girls and 3 boys', 7, 10, '2026-06-04', '2026-06-14', 'Monday to friday 7:00 to 8:00 am', 6298.60, 'Barangay BCPC Fund', NULL, '• To provide consistent nutritional support to the 20 identified malnourished children in Barangay Bayabas.\r\n• To achieve a significant improvement in the weight-for-age status of all beneficiaries within the 10-day implementation period.\r\n• To empower parents through orientations on how to prepare affordable, balanced, and nutritious meals for their families.', 'Childhood malnutrition remains a significant health concern that requires immediate and sustained intervention. Based on the most recent nutritional assessments conducted in the community, 7 children have been identified as malnourished or underweight. This Supplementary Feeding Program is a strategic effort to address these nutritional deficiencies. By providing consistent, nutrient-dense meals, the program aims to bridge the caloric gap and support the physical recovery and long-term health of the identified children.', 'This program involves the daily distribution of healthy meals to the 11 boys and 9 girls identified as the primary beneficiaries. Managed by the Committee on Health with the direct assistance of the Barangay Nutrition Scholar (BNS), the project focuses on delivering high-protein and vitamin-rich food. The meals are specifically prepared to meet the dietary requirements necessary for the beneficiaries to transition from a malnourished state to a normal, healthy weight.', NULL, 'data:image/png;base64,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', NULL, '[{\"item\":\"Rice \\/ Malagkit\",\"daily_cost\":30,\"total\":2100},{\"item\":\"Protein (Chicken\\/Egg\\/Meat)\",\"daily_cost\":20,\"total\":1400},{\"item\":\"Vegetables & Condiments\",\"daily_cost\":29.98,\"total\":2098.6},{\"item\":\"Mineral Water\",\"daily_cost\":10,\"total\":700}]', 'Approved', NULL, '2026-06-03 10:06:49', '2026-06-03 10:08:32', '2026-06-03 10:07:53'),
(11, 41, 41, '112402015', 'Committee on Health, Sangguniang Barangay', 'Barangay Bayabas Health Center, Davao City', 'Supplementary Feeding Program for Malnourished Children', 'Supplementary Feeding', '6 Children 4 girls and 2boys', 6, 7, '2026-06-16', '2026-06-23', 'Monday to friday 7:00 to 8:00 am', 3570.00, 'Barangay BCPC Fund', NULL, '• To provide consistent nutritional support to the 20 identified malnourished children in Barangay Bayabas.\r\n• To achieve a significant improvement in the weight-for-age status of all beneficiaries within the 120-day implementation period.\r\n• To empower parents through orientations on how to prepare affordable, balanced, and nutritious meals for their families.', 'Childhood malnutrition remains a significant health concern that requires immediate and sustained intervention. Based on the most recent nutritional assessments conducted in the community, 20 children have been identified as malnourished or underweight. This Supplementary Feeding Program is a strategic effort to address these nutritional deficiencies. By providing consistent, nutrient-dense meals, the program aims to bridge the caloric gap and support the physical recovery and long-term health of the identified children.', 'This program involves the daily distribution of healthy meals to the 11 boys and 9 girls identified as the primary beneficiaries. Managed by the Committee on Health with the direct assistance of the Barangay Nutrition Scholar (BNS), the project focuses on delivering high-protein and vitamin-rich food. The meals are specifically prepared to meet the dietary requirements necessary for the beneficiaries to transition from a malnourished state to a normal, healthy weight.', NULL, 'data:image/png;base64,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', NULL, '[{\"item\":\"Rice \\/ Malagkit\",\"daily_cost\":25,\"total\":1050},{\"item\":\"Protein (Chicken\\/Egg\\/Meat)\",\"daily_cost\":30,\"total\":1260},{\"item\":\"Vegetables & Condiments\",\"daily_cost\":20,\"total\":840},{\"item\":\"Mineral Water\",\"daily_cost\":10,\"total\":420}]', 'Approved', NULL, '2026-06-16 07:32:38', '2026-06-16 07:33:06', '2026-06-16 07:32:44');
INSERT INTO `feeding_program_proposals` (`proposal_id`, `created_by_user_id`, `bns_id`, `barangay_code`, `proponent`, `location`, `proposal_title`, `program_type`, `target_beneficiaries`, `num_beneficiaries`, `implementation_days`, `start_date`, `end_date`, `feeding_schedule`, `estimated_budget`, `funding_source`, `resources_needed`, `objectives`, `rationale`, `implementation_plan`, `monitoring_plan`, `signature_data`, `affected_children_data`, `budget_items`, `status`, `attachment_path`, `created_at`, `updated_at`, `submitted_at`) VALUES
(12, 41, 41, '112402015', 'Committee on Health, Sangguniang Barangay', 'Barangay Bayabas Health Center, Davao City', 'Supplementary Feeding Program for Malnourished Children', 'Supplementary Feeding', '6 Children 4 girls and 2boys', 6, 7, '2026-06-17', '2026-06-24', 'Monday to friday 7:00 to 8:00 am', 3150.00, 'Barangay BCPC Fund', NULL, '• To provide consistent nutritional support to the 20 identified malnourished children in Barangay Bayabas.\r\n• To achieve a significant improvement in the weight-for-age status of all beneficiaries within the 120-day implementation period.\r\n• To empower parents through orientations on how to prepare affordable, balanced, and nutritious meals for their families.', 'Childhood malnutrition remains a significant health concern that requires immediate and sustained intervention. Based on the most recent nutritional assessments conducted in the community, 20 children have been identified as malnourished or underweight. This Supplementary Feeding Program is a strategic effort to address these nutritional deficiencies. By providing consistent, nutrient-dense meals, the program aims to bridge the caloric gap and support the physical recovery and long-term health of the identified children.', 'This program involves the daily distribution of healthy meals to the 11 boys and 9 girls identified as the primary beneficiaries. Managed by the Committee on Health with the direct assistance of the Barangay Nutrition Scholar (BNS), the project focuses on delivering high-protein and vitamin-rich food. The meals are specifically prepared to meet the dietary requirements necessary for the beneficiaries to transition from a malnourished state to a normal, healthy weight.', NULL, 'data:image/png;base64,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', NULL, '[{\"item\":\"Rice \\/ Malagkit\",\"daily_cost\":20,\"total\":840},{\"item\":\"Protein (Chicken\\/Egg\\/Meat)\",\"daily_cost\":25,\"total\":1050},{\"item\":\"Vegetables & Condiments\",\"daily_cost\":30,\"total\":1260}]', 'Approved', NULL, '2026-06-17 09:37:00', '2026-06-17 09:40:29', '2026-06-17 09:39:47');

-- --------------------------------------------------------

--
-- Table structure for table `feeding_program_sessions`
--

CREATE TABLE `feeding_program_sessions` (
  `session_id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `session_date` date NOT NULL,
  `activity_name` varchar(255) NOT NULL,
  `purok_barangay` varchar(100) NOT NULL,
  `iec_age_group` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`iec_age_group`)),
  `iec_others_specify` varchar(255) DEFAULT NULL COMMENT 'Specification when Others is selected in IEC Age Group',
  `conducted_by_user_id` int(11) NOT NULL,
  `prepared_by` varchar(255) DEFAULT NULL,
  `nutrition_officer_signature` longtext DEFAULT NULL,
  `status` enum('Scheduled','Ongoing','Completed','Cancelled') DEFAULT 'Scheduled',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feeding_program_sessions`
--

INSERT INTO `feeding_program_sessions` (`session_id`, `proposal_id`, `session_date`, `activity_name`, `purok_barangay`, `iec_age_group`, `iec_others_specify`, `conducted_by_user_id`, `prepared_by`, `nutrition_officer_signature`, `status`, `remarks`, `created_at`, `updated_at`) VALUES
(386, 7, '2026-06-02', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:12:54', '2026-06-02 10:12:54'),
(387, 7, '2026-06-15', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(388, 7, '2026-06-16', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(389, 7, '2026-06-17', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(390, 7, '2026-06-18', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(391, 7, '2026-06-19', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(392, 7, '2026-06-22', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(393, 7, '2026-06-23', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(394, 7, '2026-06-24', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(395, 7, '2026-06-25', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(396, 7, '2026-06-26', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(397, 7, '2026-06-29', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(398, 7, '2026-06-30', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 10:16:13', '2026-06-02 10:16:13'),
(400, 8, '2025-06-15', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 13:52:47', '2026-06-02 13:52:47'),
(401, 8, '2026-06-02', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-02 13:59:55', '2026-06-02 14:01:55'),
(402, 8, '2026-06-08', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(403, 8, '2026-06-09', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(404, 8, '2026-06-10', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(405, 8, '2026-06-11', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(406, 8, '2026-06-12', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(407, 8, '2026-06-15', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(408, 8, '2026-06-16', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(409, 8, '2026-06-17', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(410, 8, '2026-06-18', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(411, 8, '2026-06-19', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', NULL, NULL, 1, NULL, NULL, 'Scheduled', NULL, '2026-06-02 13:59:56', '2026-06-02 13:59:56'),
(412, 9, '2026-06-03', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(413, 9, '2026-06-04', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(414, 9, '2026-06-05', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(415, 9, '2026-06-08', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(416, 9, '2026-06-09', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(417, 9, '2026-06-10', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(418, 9, '2026-06-11', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(419, 9, '2026-06-12', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 09:48:19', '2026-06-03 09:48:19'),
(420, 10, '2026-06-04', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(421, 10, '2026-06-05', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(422, 10, '2026-06-08', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(423, 10, '2026-06-09', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(425, 10, '2026-06-11', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(426, 10, '2026-06-12', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center, Davao City', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 10:09:31', '2026-06-03 10:09:31'),
(427, 10, '2026-06-03', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-03 10:19:14', '2026-06-03 10:19:14'),
(428, 10, '2026-06-08', 'Supplementary feeding', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-08 15:57:07', '2026-06-08 15:57:07'),
(429, 10, '2026-06-09', 'Supplementary feeding', 'Barangay Bayabas Health Center', '[\"Pregnant women\"]', NULL, 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-08 16:03:23', '2026-06-08 16:03:23'),
(430, 10, '2026-06-09', 'Supplementary feeding', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-08 17:33:13', '2026-06-08 17:33:13'),
(431, 10, '2026-06-10', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-10 09:45:32', '2026-06-10 09:45:32'),
(432, 10, '2026-06-10', 'Supplementary feeding', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-10 10:07:44', '2026-06-10 10:07:44'),
(433, 10, '2026-06-16', 'Supplementary feeding', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-16 04:18:38', '2026-06-16 04:18:38'),
(434, 11, '2026-06-16', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-16 07:33:56', '2026-06-16 07:33:56'),
(435, 11, '2026-06-16', 'Supplementary feeding', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-16 09:35:05', '2026-06-16 09:35:05'),
(436, 11, '2026-06-16', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-16 15:36:42', '2026-06-16 15:36:42'),
(437, 11, '2026-06-17', 'Supplementary feeding', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, 'Nancy Ongayo', NULL, 'Scheduled', NULL, '2026-06-17 09:02:58', '2026-06-17 09:02:58'),
(438, 12, '2026-06-22', 'Supplementary Feeding Session', 'Barangay Bayabas Health Center', '[\"Others\"]', 'Malnourished Children', 1, NULL, NULL, 'Scheduled', NULL, '2026-06-22 09:36:18', '2026-06-22 09:36:18');

-- --------------------------------------------------------

--
-- Table structure for table `filipino_foods`
--

CREATE TABLE `filipino_foods` (
  `food_id` int(11) NOT NULL,
  `food_name` varchar(100) NOT NULL,
  `food_category` enum('GO','GROW','GLOW') NOT NULL,
  `food_type` varchar(50) DEFAULT NULL COMMENT 'e.g., Carbohydrate, Protein, Vegetable, Fruit',
  `common_serving` varchar(50) DEFAULT NULL COMMENT 'e.g., 1 cup, 1 piece, 1 tbsp',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `filipino_foods`
--

INSERT INTO `filipino_foods` (`food_id`, `food_name`, `food_category`, `food_type`, `common_serving`, `description`, `is_active`, `created_at`) VALUES
(1, 'Rice', 'GO', 'Carbohydrate', '1 cup', 'Main staple food, provides energy', 1, '2026-06-19 08:05:22'),
(2, 'Brown Rice', 'GO', 'Carbohydrate', '1 cup', 'Healthier alternative to white rice', 1, '2026-06-19 08:05:22'),
(3, 'Corn', 'GO', 'Carbohydrate', '1 ear', 'Yellow corn, good energy source', 1, '2026-06-19 08:05:22'),
(4, 'Bread', 'GO', 'Carbohydrate', '2 slices', 'Whole wheat or white bread', 1, '2026-06-19 08:05:22'),
(5, 'Pandesal', 'GO', 'Carbohydrate', '2-3 pieces', 'Filipino bread roll', 1, '2026-06-19 08:05:22'),
(6, 'Kamote (Sweet Potato)', 'GO', 'Carbohydrate', '1 medium', 'Orange sweet potato, nutritious', 1, '2026-06-19 08:05:22'),
(7, 'Cassava', 'GO', 'Carbohydrate', '1 cup', 'Root crop, energy source', 1, '2026-06-19 08:05:22'),
(8, 'Gabi (Taro)', 'GO', 'Carbohydrate', '1 cup', 'Root crop used in various dishes', 1, '2026-06-19 08:05:22'),
(9, 'Pasta', 'GO', 'Carbohydrate', '1 cup', 'Noodles, spaghetti, macaroni', 1, '2026-06-19 08:05:22'),
(10, 'Pancit', 'GO', 'Carbohydrate', '1 cup', 'Filipino noodles', 1, '2026-06-19 08:05:22'),
(11, 'Lugaw (Rice Porridge)', 'GO', 'Carbohydrate', '1 bowl', 'Rice porridge, easy to digest', 1, '2026-06-19 08:05:22'),
(12, 'Oatmeal', 'GO', 'Carbohydrate', '1 cup', 'Healthy breakfast option', 1, '2026-06-19 08:05:22'),
(13, 'Potato', 'GO', 'Carbohydrate', '1 medium', 'White potato', 1, '2026-06-19 08:05:22'),
(14, 'Saging na Saba (Plantain)', 'GO', 'Carbohydrate', '1 piece', 'Cooking banana, good for energy', 1, '2026-06-19 08:05:22'),
(15, 'Ube', 'GO', 'Carbohydrate', '1 cup', 'Purple yam', 1, '2026-06-19 08:05:22'),
(16, 'Fish', 'GROW', 'Protein', '1 serving', 'General fish, good protein source', 1, '2026-06-19 08:05:22'),
(17, 'Tilapia', 'GROW', 'Protein', '1 piece', 'Common Filipino fish', 1, '2026-06-19 08:05:22'),
(18, 'Bangus (Milkfish)', 'GROW', 'Protein', '1 piece', 'National fish of Philippines', 1, '2026-06-19 08:05:22'),
(19, 'Galunggong', 'GROW', 'Protein', '3-4 pieces', 'Round scad, affordable fish', 1, '2026-06-19 08:05:22'),
(20, 'Tuyo (Dried Fish)', 'GROW', 'Protein', '2-3 pieces', 'Salted dried fish', 1, '2026-06-19 08:05:22'),
(21, 'Dilis (Anchovies)', 'GROW', 'Protein', '1/4 cup', 'Small dried fish', 1, '2026-06-19 08:05:22'),
(22, 'Chicken', 'GROW', 'Protein', '1 piece', 'Chicken meat', 1, '2026-06-19 08:05:22'),
(23, 'Pork', 'GROW', 'Protein', '1 serving', 'Pork meat', 1, '2026-06-19 08:05:22'),
(24, 'Beef', 'GROW', 'Protein', '1 serving', 'Beef meat', 1, '2026-06-19 08:05:22'),
(25, 'Egg', 'GROW', 'Protein', '1-2 pieces', 'Chicken egg', 1, '2026-06-19 08:05:22'),
(26, 'Itlog na Pula (Salted Egg)', 'GROW', 'Protein', '1 piece', 'Salted duck egg', 1, '2026-06-19 08:05:22'),
(27, 'Tokwa (Tofu)', 'GROW', 'Protein', '1 cup', 'Soybean curd', 1, '2026-06-19 08:05:22'),
(28, 'Monggo (Mung Beans)', 'GROW', 'Protein', '1 cup', 'Green mung beans', 1, '2026-06-19 08:05:22'),
(29, 'Sitaw Beans', 'GROW', 'Protein', '1 cup', 'String beans', 1, '2026-06-19 08:05:22'),
(30, 'Garbanzos (Chickpeas)', 'GROW', 'Protein', '1 cup', 'Chickpeas', 1, '2026-06-19 08:05:22'),
(31, 'Tahure (Fermented Tofu)', 'GROW', 'Protein', '2 tbsp', 'Fermented bean curd', 1, '2026-06-19 08:05:22'),
(32, 'Milk', 'GROW', 'Protein', '1 glass', 'Fresh or powdered milk', 1, '2026-06-19 08:05:22'),
(33, 'Cheese', 'GROW', 'Protein', '1 slice', 'Dairy cheese', 1, '2026-06-19 08:05:22'),
(34, 'Squid', 'GROW', 'Protein', '1 cup', 'Pusit/squid', 1, '2026-06-19 08:05:22'),
(35, 'Shrimp', 'GROW', 'Protein', '1 cup', 'Hipon/shrimp', 1, '2026-06-19 08:05:22'),
(36, 'Malunggay (Moringa)', 'GLOW', 'Vegetable', '1 cup', 'Super nutritious vegetable', 1, '2026-06-19 08:05:22'),
(37, 'Kangkong (Water Spinach)', 'GLOW', 'Vegetable', '1 cup', 'Leafy green vegetable', 1, '2026-06-19 08:05:22'),
(38, 'Pechay (Bok Choy)', 'GLOW', 'Vegetable', '1 cup', 'Chinese cabbage', 1, '2026-06-19 08:05:22'),
(39, 'Sitaw (String Beans)', 'GLOW', 'Vegetable', '1 cup', 'Long beans', 1, '2026-06-19 08:05:22'),
(40, 'Kalabasa (Squash)', 'GLOW', 'Vegetable', '1 cup', 'Yellow squash, rich in vitamin A', 1, '2026-06-19 08:05:22'),
(41, 'Ampalaya (Bitter Gourd)', 'GLOW', 'Vegetable', '1 cup', 'Bitter melon', 1, '2026-06-19 08:05:22'),
(42, 'Talong (Eggplant)', 'GLOW', 'Vegetable', '1 piece', 'Purple eggplant', 1, '2026-06-19 08:05:22'),
(43, 'Okra', 'GLOW', 'Vegetable', '6-8 pieces', 'Lady finger vegetable', 1, '2026-06-19 08:05:22'),
(44, 'Labanos (Radish)', 'GLOW', 'Vegetable', '1 cup', 'White radish', 1, '2026-06-19 08:05:22'),
(45, 'Repolyo (Cabbage)', 'GLOW', 'Vegetable', '1 cup', 'Green or white cabbage', 1, '2026-06-19 08:05:22'),
(46, 'Lettuce', 'GLOW', 'Vegetable', '1 cup', 'Salad vegetable', 1, '2026-06-19 08:05:22'),
(47, 'Carrots', 'GLOW', 'Vegetable', '1 medium', 'Orange carrot, rich in vitamin A', 1, '2026-06-19 08:05:22'),
(48, 'Tomato', 'GLOW', 'Vegetable', '1 medium', 'Red tomato', 1, '2026-06-19 08:05:22'),
(49, 'Onion', 'GLOW', 'Vegetable', '1 medium', 'White or red onion', 1, '2026-06-19 08:05:22'),
(50, 'Garlic', 'GLOW', 'Vegetable', '3-4 cloves', 'Bawang', 1, '2026-06-19 08:05:22'),
(51, 'Ginger', 'GLOW', 'Vegetable', '1 thumb', 'Luya', 1, '2026-06-19 08:05:22'),
(52, 'Patola (Luffa)', 'GLOW', 'Vegetable', '1 cup', 'Sponge gourd', 1, '2026-06-19 08:05:22'),
(53, 'Sayote (Chayote)', 'GLOW', 'Vegetable', '1 cup', 'Chayote squash', 1, '2026-06-19 08:05:22'),
(54, 'Upo (Bottle Gourd)', 'GLOW', 'Vegetable', '1 cup', 'White gourd', 1, '2026-06-19 08:05:22'),
(55, 'Mustasa (Mustard Greens)', 'GLOW', 'Vegetable', '1 cup', 'Green leafy vegetable', 1, '2026-06-19 08:05:22'),
(56, 'Banana', 'GLOW', 'Fruit', '1 medium', 'Saging, common Filipino fruit', 1, '2026-06-19 08:05:22'),
(57, 'Mango', 'GLOW', 'Fruit', '1 medium', 'Mangga, rich in vitamin C', 1, '2026-06-19 08:05:22'),
(58, 'Papaya', 'GLOW', 'Fruit', '1 cup', 'Orange papaya, rich in vitamin A', 1, '2026-06-19 08:05:22'),
(59, 'Pineapple', 'GLOW', 'Fruit', '1 cup', 'Pinya, tropical fruit', 1, '2026-06-19 08:05:22'),
(60, 'Watermelon', 'GLOW', 'Fruit', '1 cup', 'Pakwan, hydrating fruit', 1, '2026-06-19 08:05:22'),
(61, 'Orange', 'GLOW', 'Fruit', '1 medium', 'Dalandan, vitamin C', 1, '2026-06-19 08:05:22'),
(62, 'Apple', 'GLOW', 'Fruit', '1 medium', 'Mansanas', 1, '2026-06-19 08:05:22'),
(63, 'Guava', 'GLOW', 'Fruit', '1 medium', 'Bayabas, high in vitamin C', 1, '2026-06-19 08:05:22'),
(64, 'Santol', 'GLOW', 'Fruit', '1 medium', 'Cotton fruit', 1, '2026-06-19 08:05:22'),
(65, 'Lanzones', 'GLOW', 'Fruit', '10 pieces', 'Small round fruit', 1, '2026-06-19 08:05:22'),
(66, 'Rambutan', 'GLOW', 'Fruit', '5 pieces', 'Hairy red fruit', 1, '2026-06-19 08:05:22'),
(67, 'Calamansi', 'GLOW', 'Fruit', '5-10 pieces', 'Filipino lime, vitamin C', 1, '2026-06-19 08:05:22'),
(68, 'Duhat (Java Plum)', 'GLOW', 'Fruit', '1 cup', 'Purple fruit', 1, '2026-06-19 08:05:22'),
(69, 'Atis (Sugar Apple)', 'GLOW', 'Fruit', '1 medium', 'Custard apple', 1, '2026-06-19 08:05:22'),
(70, 'Guyabano (Soursop)', 'GLOW', 'Fruit', '1 cup', 'Large green fruit', 1, '2026-06-19 08:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `grocery_lists`
--

CREATE TABLE `grocery_lists` (
  `grocery_list_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Mother/Parent who created the list',
  `family_id` int(11) DEFAULT NULL,
  `meal_plan_id` int(11) DEFAULT NULL COMMENT 'Optional: generated from meal plan',
  `list_name` varchar(255) NOT NULL,
  `list_date` date NOT NULL DEFAULT curdate(),
  `total_estimated_cost` decimal(10,2) DEFAULT 0.00,
  `status` enum('Draft','Active','Completed','Cancelled') DEFAULT 'Active',
  `notes` text DEFAULT NULL,
  `created_date` datetime DEFAULT current_timestamp(),
  `completed_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grocery_lists`
--

INSERT INTO `grocery_lists` (`grocery_list_id`, `user_id`, `family_id`, `meal_plan_id`, `list_name`, `list_date`, `total_estimated_cost`, `status`, `notes`, `created_date`, `completed_date`) VALUES
(48, 54, NULL, NULL, 'Items to Buy Locally - Jun 23, 2026', '2026-06-23', 0.00, 'Active', 'These items were not found online. Please buy them at your local market.', '2026-06-23 11:52:50', NULL),
(49, 54, NULL, NULL, 'Items to Buy Locally - Jun 24, 2026', '2026-06-24', 0.00, 'Active', 'These items were not found online. Please buy them at your local market.', '2026-06-24 15:21:26', NULL),
(50, 54, NULL, NULL, 'Items to Buy Locally - Jun 25, 2026', '2026-06-25', 0.00, 'Active', 'These items were not found online. Please buy them at your local market.', '2026-06-25 00:00:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `grocery_list_items`
--

CREATE TABLE `grocery_list_items` (
  `item_id` int(11) NOT NULL,
  `grocery_list_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL COMMENT 'e.g., Vegetables, Meat, Grains, Dairy',
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL COMMENT 'e.g., kg, pcs, liters',
  `estimated_price` decimal(10,2) DEFAULT NULL,
  `is_purchased` tinyint(1) DEFAULT 0,
  `purchased_from_vendor_id` int(11) DEFAULT NULL COMMENT 'Market vendor who sold it',
  `actual_price` decimal(10,2) DEFAULT NULL,
  `purchase_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grocery_list_items`
--

INSERT INTO `grocery_list_items` (`item_id`, `grocery_list_id`, `product_name`, `category`, `quantity`, `unit`, `estimated_price`, `is_purchased`, `purchased_from_vendor_id`, `actual_price`, `purchase_date`, `notes`) VALUES
(1, 2, 'Rice', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(2, 2, 'water', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(3, 2, 'ginger', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(4, 2, 'salt', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(5, 2, 'Squash', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(6, 2, 'eggplant', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(7, 2, 'okra', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(8, 2, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(9, 2, 'bitter melon', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(10, 2, 'tomato', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(11, 2, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(12, 2, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(13, 2, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(14, 2, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(15, 2, 'oil', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(16, 2, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(17, 2, 'carrot', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(18, 2, 'cabbage', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(19, 2, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(20, 2, 'soy sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(21, 2, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(22, 3, 'Rice', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(23, 3, 'water', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(24, 3, 'ginger', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(25, 3, 'salt', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(26, 3, 'Squash', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(27, 3, 'eggplant', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(28, 3, 'okra', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(29, 3, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(30, 3, 'bitter melon', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(31, 3, 'tomato', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(32, 3, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(33, 3, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(34, 3, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(35, 3, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(36, 3, 'oil', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(37, 3, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(38, 3, 'carrot', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(39, 3, 'cabbage', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(40, 3, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(41, 3, 'soy sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(42, 3, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(43, 5, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(44, 5, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(45, 5, 'ginger', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(46, 5, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(47, 5, 'Squash', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(48, 5, 'eggplant', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(49, 5, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(50, 5, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(51, 5, 'bitter melon', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(52, 5, 'tomato', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(53, 5, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(54, 5, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(55, 5, 'brown sugar', NULL, 1.00, 'unit', 65.00, 0, NULL, NULL, NULL, NULL),
(56, 5, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(57, 5, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(58, 5, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', 180.00, 0, NULL, NULL, NULL, NULL),
(59, 5, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(60, 5, 'cabbage', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(61, 5, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(62, 5, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(63, 5, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(64, 6, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(65, 6, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(66, 6, 'ginger', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(67, 6, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(68, 6, 'Squash', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(69, 6, 'eggplant', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(70, 6, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(71, 6, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(72, 6, 'bitter melon', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(73, 6, 'tomato', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(74, 6, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(75, 6, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(76, 6, 'brown sugar', NULL, 1.00, 'unit', 65.00, 0, NULL, NULL, NULL, NULL),
(77, 6, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(78, 6, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(79, 6, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', 180.00, 0, NULL, NULL, NULL, NULL),
(80, 6, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(81, 6, 'cabbage', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(82, 6, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(83, 6, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(84, 6, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(85, 7, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(86, 7, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(87, 7, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(88, 7, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(89, 7, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(90, 7, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(91, 7, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(92, 7, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(93, 7, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(94, 7, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(95, 7, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(96, 7, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(97, 7, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(98, 7, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(99, 7, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(100, 7, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(101, 7, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(102, 7, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(103, 7, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(104, 7, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(105, 7, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(106, 8, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(107, 8, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(108, 8, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(109, 8, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(110, 8, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(111, 8, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(112, 8, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(113, 8, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(114, 8, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(115, 8, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(116, 8, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(117, 8, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(118, 8, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(119, 8, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(120, 8, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(121, 8, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(122, 8, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(123, 8, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(124, 8, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(125, 8, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(126, 8, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(127, 9, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(128, 9, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(129, 9, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(130, 9, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(131, 9, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(132, 9, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(133, 9, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(134, 9, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(135, 9, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(136, 9, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(137, 9, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(138, 9, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(139, 9, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(140, 9, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(141, 9, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(142, 9, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(143, 9, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(144, 9, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(145, 9, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(146, 9, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(147, 9, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(148, 10, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(149, 10, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(150, 10, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(151, 10, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(152, 10, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(153, 10, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(154, 10, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(155, 10, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(156, 10, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(157, 10, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(158, 10, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(159, 10, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(160, 10, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(161, 10, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(162, 10, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(163, 10, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(164, 10, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(165, 10, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(166, 10, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(167, 10, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(168, 10, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(169, 11, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(170, 11, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(171, 11, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(172, 11, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(173, 11, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(174, 11, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(175, 11, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(176, 11, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(177, 11, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(178, 11, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(179, 11, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(180, 11, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(181, 11, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(182, 11, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(183, 11, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(184, 11, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(185, 11, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(186, 11, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(187, 11, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(188, 11, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(189, 11, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(190, 12, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(191, 12, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(192, 12, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(193, 12, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(194, 12, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(195, 12, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(196, 12, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(197, 12, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(198, 12, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(199, 12, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(200, 12, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(201, 12, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(202, 12, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(203, 12, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(204, 12, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(205, 12, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(206, 12, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(207, 12, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(208, 12, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(209, 12, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(210, 12, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(211, 13, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(212, 13, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(213, 13, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(214, 13, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(215, 13, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(216, 13, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(217, 13, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(218, 13, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(219, 13, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(220, 13, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(221, 13, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(222, 13, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(223, 13, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(224, 13, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(225, 13, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(226, 13, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(227, 13, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(228, 13, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(229, 13, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(230, 13, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(231, 13, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(232, 14, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(233, 14, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(234, 14, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(235, 14, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(236, 14, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(237, 14, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(238, 14, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(239, 14, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(240, 14, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(241, 14, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(242, 14, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(243, 14, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(244, 14, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(245, 14, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(246, 14, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(247, 14, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(248, 14, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(249, 14, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(250, 14, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(251, 14, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(252, 14, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(253, 15, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(254, 15, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(255, 15, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(256, 15, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(257, 15, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(258, 15, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(259, 15, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(260, 15, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(261, 15, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(262, 15, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(263, 15, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(264, 15, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(265, 15, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(266, 15, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(267, 15, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(268, 15, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(269, 15, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(270, 15, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(271, 15, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(272, 15, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(273, 15, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(274, 16, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(275, 16, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(276, 16, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(277, 16, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(278, 16, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(279, 16, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(280, 16, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(281, 16, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(282, 16, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(283, 16, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(284, 16, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(285, 16, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(286, 16, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(287, 16, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(288, 16, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(289, 16, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(290, 16, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(291, 16, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(292, 16, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(293, 16, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(294, 16, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(295, 18, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(296, 18, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(297, 18, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(298, 18, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(299, 18, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(300, 18, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(301, 18, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(302, 18, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(303, 18, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(304, 18, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(305, 18, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(306, 18, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(307, 18, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(308, 18, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(309, 18, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(310, 18, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(311, 18, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(312, 18, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(313, 18, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(314, 18, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(315, 18, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(316, 19, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(317, 19, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(318, 19, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(319, 19, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(320, 19, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(321, 19, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(322, 19, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(323, 19, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(324, 19, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(325, 19, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(326, 19, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(327, 19, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(328, 19, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(329, 19, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(330, 19, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(331, 19, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(332, 19, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(333, 19, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(334, 19, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(335, 19, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(336, 19, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(337, 20, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(338, 20, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(339, 20, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(340, 20, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(341, 20, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(342, 20, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(343, 20, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(344, 20, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(345, 20, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(346, 20, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(347, 20, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(348, 20, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(349, 20, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(350, 20, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(351, 20, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(352, 20, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(353, 20, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(354, 20, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(355, 20, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(356, 20, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(357, 20, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(358, 21, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(359, 21, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(360, 21, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(361, 21, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(362, 21, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(363, 21, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(364, 21, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(365, 21, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(366, 21, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(367, 21, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(368, 21, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(369, 21, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(370, 21, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(371, 21, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(372, 21, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(373, 21, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(374, 21, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(375, 21, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(376, 21, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(377, 21, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(378, 21, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(379, 22, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(380, 22, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(381, 22, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(382, 22, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(383, 22, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(384, 22, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(385, 22, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(386, 22, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(387, 22, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(388, 22, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(389, 22, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(390, 22, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(391, 22, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(392, 22, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(393, 22, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(394, 22, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(395, 22, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(396, 22, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(397, 22, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(398, 22, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(399, 22, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(400, 23, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(401, 23, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(402, 23, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(403, 23, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(404, 23, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(405, 23, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(406, 23, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(407, 23, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(408, 23, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(409, 23, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(410, 23, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(411, 23, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(412, 23, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(413, 23, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(414, 23, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(415, 23, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(416, 23, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(417, 23, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(418, 23, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(419, 23, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(420, 23, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(421, 24, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(422, 24, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(423, 24, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(424, 24, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(425, 24, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(426, 24, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(427, 24, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(428, 24, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(429, 24, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(430, 24, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(431, 24, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(432, 24, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(433, 24, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(434, 24, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(435, 24, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(436, 24, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(437, 24, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(438, 24, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(439, 24, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(440, 24, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(441, 24, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(442, 25, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(443, 25, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(444, 25, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(445, 25, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(446, 25, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(447, 25, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(448, 25, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(449, 25, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(450, 25, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(451, 25, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(452, 25, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(453, 25, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(454, 25, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(455, 25, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(456, 25, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(457, 25, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(458, 25, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(459, 25, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(460, 25, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(461, 25, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(462, 25, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(463, 26, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(464, 26, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(465, 26, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(466, 26, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(467, 26, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(468, 26, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(469, 26, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(470, 26, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(471, 26, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(472, 26, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(473, 26, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(474, 26, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(476, 26, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(477, 26, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(478, 26, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(479, 26, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(480, 26, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(481, 26, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(482, 26, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(483, 26, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(484, 27, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(485, 27, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(486, 27, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(487, 27, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(488, 27, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(489, 27, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(490, 27, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(491, 27, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(492, 27, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(493, 27, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(494, 27, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(495, 27, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(496, 27, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(497, 27, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(498, 27, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(499, 27, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(500, 27, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(501, 27, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(502, 27, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(503, 27, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(504, 27, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(505, 28, 'Rice', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(506, 28, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(507, 28, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(508, 28, 'salt', NULL, 1.00, 'unit', 26.00, 0, NULL, NULL, NULL, NULL),
(509, 28, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(510, 28, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(511, 28, 'okra', NULL, 1.00, 'unit', 95.00, 0, NULL, NULL, NULL, NULL),
(512, 28, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(513, 28, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(514, 28, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(515, 28, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(516, 28, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(517, 28, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(518, 28, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(519, 28, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(520, 28, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(521, 28, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(522, 28, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(523, 28, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(524, 28, 'soy sauce', NULL, 1.00, 'unit', 23.00, 0, NULL, NULL, NULL, NULL),
(525, 28, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(652, 35, 'water', NULL, 1.00, 'unit', 45.00, 0, NULL, NULL, NULL, NULL),
(653, 35, 'ginger', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(654, 35, 'Squash', NULL, 1.00, 'unit', 35.00, 0, NULL, NULL, NULL, NULL),
(655, 35, 'eggplant', NULL, 1.00, 'unit', 50.00, 0, NULL, NULL, NULL, NULL),
(656, 35, 'string beans', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(657, 35, 'bitter melon', NULL, 1.00, 'unit', 90.00, 0, NULL, NULL, NULL, NULL),
(658, 35, 'tomato', NULL, 1.00, 'unit', 60.00, 0, NULL, NULL, NULL, NULL),
(659, 35, 'shrimp paste', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(660, 35, 'Saba banana', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(661, 35, 'brown sugar', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(662, 35, 'spring roll wrapper', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(663, 35, 'oil', NULL, 1.00, 'unit', 42.50, 0, NULL, NULL, NULL, NULL),
(664, 35, 'Mixed vegetables (cauliflower', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(665, 35, 'carrot', NULL, 1.00, 'unit', 100.00, 0, NULL, NULL, NULL, NULL),
(666, 35, 'cabbage', NULL, 1.00, 'unit', 80.00, 0, NULL, NULL, NULL, NULL),
(667, 35, 'bell pepper)', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(668, 35, 'oyster sauce', NULL, 1.00, 'unit', NULL, 0, NULL, NULL, NULL, NULL),
(776, 50, 'fish sauce', 'Other', 1.00, 'pc', 0.00, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `households`
--

CREATE TABLE `households` (
  `household_id` int(11) NOT NULL,
  `household_code` varchar(36) NOT NULL,
  `water_source_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `toilet_type_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `uses_iodized_salt` tinyint(1) NOT NULL DEFAULT 0,
  `dwelling_type_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `purok` varchar(100) DEFAULT NULL,
  `hof_user_id` int(11) DEFAULT NULL,
  `hof_needs_review` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `uses_ifr` tinyint(1) NOT NULL DEFAULT 0,
  `children_0_5mos` tinyint(3) UNSIGNED DEFAULT NULL,
  `children_6_23mos` tinyint(3) UNSIGNED DEFAULT NULL,
  `children_24_59mos` tinyint(3) UNSIGNED DEFAULT NULL,
  `children_60plus` tinyint(3) UNSIGNED DEFAULT NULL,
  `num_hh_members` tinyint(3) UNSIGNED DEFAULT NULL,
  `is_mother_prog` tinyint(1) NOT NULL DEFAULT 0,
  `fp_method_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `fp_method_other` varchar(100) DEFAULT NULL,
  `spouse_occupation` varchar(150) DEFAULT NULL,
  `draft_spouse_user_id` int(11) DEFAULT NULL,
  `spouse_monthly_income` decimal(12,2) DEFAULT NULL COMMENT 'Spouse monthly income for HOF determination',
  `spouse_pregnancy_status` enum('Not Pregnant','Pregnant 1st Trimester','Pregnant 2nd Trimester','Pregnant 3rd Trimester','Postpartum') DEFAULT NULL COMMENT 'Spouse pregnancy status',
  `spouse_breastfeeding_status` enum('Not Breastfeeding','Exclusively Breastfeeding','Mixed Feeding','Bottle Feeding') DEFAULT NULL COMMENT 'Spouse breastfeeding status',
  `spouse_name` varchar(150) DEFAULT NULL COMMENT 'Spouse full name (typed, no account required)',
  `spouse_last_name` varchar(100) DEFAULT NULL,
  `spouse_first_name` varchar(100) DEFAULT NULL,
  `spouse_middle_name` varchar(100) DEFAULT NULL,
  `spouse_suffix` varchar(20) DEFAULT NULL,
  `spouse_educ_level_id` tinyint(3) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `households`
--

INSERT INTO `households` (`household_id`, `household_code`, `water_source_id`, `toilet_type_id`, `uses_iodized_salt`, `dwelling_type_id`, `purok`, `hof_user_id`, `hof_needs_review`, `created_at`, `updated_at`, `uses_ifr`, `children_0_5mos`, `children_6_23mos`, `children_24_59mos`, `children_60plus`, `num_hh_members`, `is_mother_prog`, `fp_method_id`, `fp_method_other`, `spouse_occupation`, `draft_spouse_user_id`, `spouse_monthly_income`, `spouse_pregnancy_status`, `spouse_breastfeeding_status`, `spouse_name`, `spouse_last_name`, `spouse_first_name`, `spouse_middle_name`, `spouse_suffix`, `spouse_educ_level_id`) VALUES
(27, 'HH-000-0001', 1, 1, 1, 1, 'Purok 5', 46, 1, '2026-06-01 15:08:40', '2026-06-01 15:08:40', 0, NULL, 1, 1, NULL, 4, 0, 1, NULL, 'Housewife', NULL, NULL, 'Pregnant 2nd Trimester', 'Not Breastfeeding', 'Dove, Starmaine Tan', 'Dove', 'Starmaine', 'Tan', NULL, 5),
(32, 'HH-000-0002', 1, 1, 1, 1, 'Purok 8', 0, 1, '2026-06-02 03:55:35', '2026-06-02 03:57:03', 0, 0, 0, 2, 1, 5, 0, 0, NULL, 'Manager', NULL, 35000.00, '', '', 'Brown, Chris Chui', 'Brown', 'Chris', 'Chui', '', 5),
(33, 'HH-000-0003', 1, 1, 1, 2, 'Purok 1', 52, 1, '2026-06-02 12:13:16', '2026-06-02 12:13:16', 0, NULL, NULL, 1, 1, 4, 0, 2, NULL, 'Housewife', NULL, 1000.00, 'Not Pregnant', 'Not Breastfeeding', 'Tiago, Carla Song', 'Tiago', 'Carla', 'Song', NULL, 3),
(34, 'HH-000-0004', 1, 1, 1, 1, 'Purok 2', 0, 1, '2026-06-02 12:22:25', '2026-06-02 12:24:16', 0, 0, 1, 0, 0, 3, 0, 0, NULL, 'Manager', NULL, 34999.99, '', '', 'Rhias, Lance Chui', 'Rhias', 'Lance', 'Chui', '', 3),
(35, 'HH-000-0005', 1, 1, 1, 1, 'Purok 2', 0, 1, '2026-06-02 12:44:42', '2026-06-02 13:06:58', 0, 0, 0, 1, 0, 3, 0, 0, NULL, 'Manager', NULL, 34999.99, '', '', 'Rhias, Lance Chui', 'Rhias', 'Lance', 'Chui', '', 3);

-- --------------------------------------------------------

--
-- Table structure for table `household_children`
--

CREATE TABLE `household_children` (
  `child_id` int(11) NOT NULL,
  `household_id` int(11) NOT NULL,
  `added_by` int(11) NOT NULL COMMENT 'user_id of parent who added',
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `sex` enum('M','F') DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `household_children`
--

INSERT INTO `household_children` (`child_id`, `household_id`, `added_by`, `last_name`, `first_name`, `middle_name`, `suffix`, `sex`, `dob`, `created_at`, `updated_at`) VALUES
(20, 27, 46, 'Dove', 'Bia', 'Tan', NULL, 'F', '2024-06-03', '2026-06-01 15:08:40', '2026-06-01 15:08:40'),
(21, 27, 46, 'Dove', 'Niel', 'Tan', NULL, 'M', '2025-01-30', '2026-06-01 15:08:40', '2026-06-01 15:08:40'),
(34, 32, 51, 'Brown', 'Leo', 'Evan', NULL, 'M', '2023-06-02', '2026-06-02 03:55:35', '2026-06-02 03:55:35'),
(35, 32, 51, 'Brown', 'Lea', 'Evan', NULL, 'F', '2021-08-23', '2026-06-02 03:55:35', '2026-06-02 03:55:35'),
(36, 32, 51, 'Brown', 'Lily', 'Evan', NULL, 'F', '2017-12-16', '2026-06-02 03:55:35', '2026-06-02 03:55:35'),
(37, 33, 52, 'Tiago', 'Erick', 'Santos', NULL, 'M', '2022-09-28', '2026-06-02 12:13:16', '2026-06-02 12:13:16'),
(38, 33, 52, 'Tiago', 'Erika', 'Santos', NULL, 'F', '2007-02-17', '2026-06-02 12:13:16', '2026-06-02 12:13:16'),
(42, 35, 54, 'Rhias', 'Rence', 'Ong', NULL, 'M', '2022-07-19', '2026-06-02 13:13:57', '2026-06-02 13:13:57');

-- --------------------------------------------------------

--
-- Table structure for table `household_members`
--

CREATE TABLE `household_members` (
  `id` int(11) NOT NULL,
  `household_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `household_members`
--

INSERT INTO `household_members` (`id`, `household_id`, `user_id`, `joined_at`) VALUES
(21, 27, 46, '2026-06-01 15:08:40'),
(22, 28, 47, '2026-06-01 16:14:57'),
(26, 32, 51, '2026-06-02 03:55:35'),
(27, 33, 52, '2026-06-02 12:13:16'),
(28, 34, 53, '2026-06-02 12:22:25'),
(29, 35, 54, '2026-06-02 12:44:42');

-- --------------------------------------------------------

--
-- Table structure for table `household_pantry`
--

CREATE TABLE `household_pantry` (
  `pantry_id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL COMMENT 'Mother/Parent managing the pantry',
  `item_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT 0.00,
  `unit` varchar(50) NOT NULL,
  `last_replenished` datetime DEFAULT current_timestamp(),
  `expiry_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `household_pantry`
--

INSERT INTO `household_pantry` (`pantry_id`, `family_id`, `user_id`, `item_name`, `category`, `quantity`, `unit`, `last_replenished`, `expiry_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 62, 54, 'Banay-Banay', 'Other', 3.00, 'kg', '2026-06-21 15:30:20', NULL, 'Auto-added from order #ORD-2026-984888', '2026-06-20 02:20:57', '2026-06-21 15:30:20'),
(2, 62, 54, 'Kuhaku', 'Other', 1.00, '5kg', '2026-06-21 15:09:29', NULL, 'Auto-added from order #ORD-2026-562174', '2026-06-21 15:09:29', '2026-06-21 15:09:29'),
(3, 62, 54, 'Rice', 'Grains', 26.00, 'kg', '2026-06-22 20:34:13', NULL, 'Auto-added from order #ORD-2026-970482', '2026-06-21 16:16:15', '2026-06-22 20:34:13'),
(4, 62, 54, 'Watermelon', 'Fruits', 2.00, 'kg', '2026-06-21 18:53:22', NULL, 'Auto-added from order #ORD-2026-970482', '2026-06-21 16:16:15', '2026-06-22 21:17:21'),
(5, 62, 54, 'Salt', 'Condiments', 1.00, '1kg', '2026-06-21 16:16:15', NULL, 'Auto-added from order #ORD-2026-970482', '2026-06-21 16:16:15', '2026-06-21 16:16:15'),
(6, 62, 54, 'Okra', 'Vegetables', 1.00, 'kg', '2026-06-21 16:16:15', NULL, 'Auto-added from order #ORD-2026-970482', '2026-06-21 16:16:15', '2026-06-22 21:17:21'),
(7, 62, 54, 'Sugar', 'Condiments', 1.50, 'kg', '2026-06-21 18:53:22', NULL, 'Auto-added from order #ORD-2026-970482', '2026-06-21 16:16:15', '2026-06-24 16:17:53'),
(8, 62, 54, 'Cooking Oil', 'Condiments', 0.00, '350ml', '2026-06-21 18:53:22', NULL, 'Auto-added from order #ORD-2026-970482', '2026-06-21 16:16:15', '2026-06-24 16:17:27'),
(9, 62, 54, 'Cauliflower', 'Vegetables', 2.00, 'kg', '2026-06-21 18:53:22', NULL, 'Auto-added from order #ORD-2026-970482', '2026-06-21 16:16:15', '2026-06-22 21:17:21'),
(10, 62, 54, 'Carrots', 'Vegetables', 2.00, 'kg', '2026-06-21 18:53:22', NULL, 'Auto-added from order #ORD-2026-970482', '2026-06-21 16:16:15', '2026-06-22 21:17:21'),
(11, 62, 54, 'Soy Sauce', 'Condiments', 1.00, '385ml', '2026-06-21 16:16:15', NULL, 'Auto-added from order #ORD-2026-970482', '2026-06-21 16:16:15', '2026-06-21 16:16:15'),
(12, 62, 54, 'Ampalaya', 'Vegetables', 3.00, 'kg', '2026-06-24 16:15:47', NULL, 'Auto-added from order #ORD-2026-283908', '2026-06-21 18:54:37', '2026-06-24 16:15:47'),
(13, 53, 46, 'Rice', 'Grains', 1.00, 'kg', '2026-06-21 21:37:10', NULL, 'Auto-added from order #ORD-2026-096732', '2026-06-21 21:37:10', '2026-06-21 21:37:10'),
(14, 53, 46, 'Eggs', 'Protein', 1.00, 'tray', '2026-06-21 21:37:10', NULL, 'Auto-added from order #ORD-2026-096732', '2026-06-21 21:37:10', '2026-06-21 21:39:03'),
(15, 53, 46, 'Cooking Oil', 'Condiments', 1.00, '350ml', '2026-06-21 21:37:10', NULL, 'Auto-added from order #ORD-2026-096732', '2026-06-21 21:37:10', '2026-06-21 21:37:10'),
(16, 53, 46, 'Salt', 'Condiments', 1.00, '1kg', '2026-06-21 21:37:10', NULL, 'Auto-added from order #ORD-2026-096732', '2026-06-21 21:37:10', '2026-06-21 21:37:10'),
(17, 62, 54, 'Fresh Tilapia', 'Other', 1.00, 'kg', '2026-06-22 13:53:30', NULL, 'Auto-added from order #ORD-2026-172539', '2026-06-22 13:53:30', '2026-06-22 13:53:30'),
(18, 62, 54, 'Kalabasa', 'Vegetables', 2.00, 'kg', '2026-06-24 16:15:47', NULL, 'Auto-added from order #ORD-2026-172539', '2026-06-22 13:53:30', '2026-06-24 16:15:47'),
(19, 62, 54, 'Talong', 'Vegetables', 2.00, 'kg', '2026-06-24 16:15:47', NULL, 'Auto-added from order #ORD-2026-172539', '2026-06-22 13:53:30', '2026-06-24 16:15:47'),
(20, 62, 54, 'Kamatis', 'Vegetables', 2.00, 'kg', '2026-06-24 16:15:47', NULL, 'Auto-added from order #ORD-2026-172539', '2026-06-22 13:53:30', '2026-06-24 16:15:47'),
(21, 62, 54, 'Ahos', 'Spices', 1.00, 'kg', '2026-06-22 21:09:28', NULL, 'Auto-added from order #ORD-2026-002709', '2026-06-22 21:09:28', '2026-06-22 23:27:56'),
(22, 62, 54, 'Mangga (cebu)', 'Fruits', 1.00, 'kg', '2026-06-22 21:10:52', NULL, 'Auto-added from order #ORD-2026-788774', '2026-06-22 21:10:52', '2026-06-22 21:17:21'),
(23, 62, 54, 'Repolyo (wakamini)', 'Vegetables', 0.60, 'kg', '2026-06-25 00:45:55', NULL, 'Auto-added from order #ORD-2026-052504', '2026-06-25 00:45:55', '2026-06-25 00:47:25'),
(24, 62, 54, 'Sibuyas Dahon', 'Spices', 3.00, 'bundle', '2026-06-25 14:11:13', NULL, 'Auto-added from order #ORD-2026-052504', '2026-06-25 00:45:55', '2026-06-25 14:11:13'),
(25, 62, 54, 'Luya (Hawaiian)', 'Spices', 1.00, 'kg', '2026-06-25 12:17:49', NULL, 'Auto-added from order #ORD-2026-432756', '2026-06-25 12:17:49', '2026-06-25 12:17:49'),
(26, 62, 54, 'Bombay', 'Spices', 1.50, 'kg', '2026-06-25 14:53:13', NULL, '', '2026-06-25 14:53:13', '2026-06-25 14:53:33'),
(27, 62, 54, 'Kangkong', 'Vegetables', 2.00, 'kg', '2026-06-25 14:54:25', NULL, '', '2026-06-25 14:54:25', '2026-06-25 14:54:25'),
(28, 62, 54, 'Shrimp paste', 'Condiments', 1.00, 'kg', '2026-06-25 14:55:24', NULL, '', '2026-06-25 14:55:24', '2026-06-25 14:55:24'),
(29, 62, 54, 'Sayote', 'Vegetables', 1.00, 'kg', '2026-06-25 14:57:27', NULL, '', '2026-06-25 14:57:27', '2026-06-25 14:57:27'),
(30, 62, 54, 'Fish sauce', 'Protein', 1.00, 'pcs', '2026-06-25 14:59:29', NULL, '', '2026-06-25 14:59:29', '2026-06-25 14:59:29'),
(31, 62, 54, 'Cabbage', 'Vegetables', 1.00, 'pcs', '2026-06-25 15:11:45', NULL, '', '2026-06-25 15:11:45', '2026-06-25 15:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `ingredient_acquisitions`
--

CREATE TABLE `ingredient_acquisitions` (
  `id` int(11) NOT NULL,
  `meal_plan_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `is_acquired` tinyint(1) DEFAULT 0,
  `acquisition_date` timestamp NULL DEFAULT NULL,
  `acquisition_method` enum('manual','shopping_cart','pantry') DEFAULT 'manual',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ingredient_aliases`
--

CREATE TABLE `ingredient_aliases` (
  `alias_id` int(11) NOT NULL,
  `primary_name` varchar(255) NOT NULL COMMENT 'Primary/preferred name (usually Filipino)',
  `alias_name` varchar(255) NOT NULL COMMENT 'Alternative name (English, local variant, etc.)',
  `category` varchar(100) DEFAULT NULL COMMENT 'Category for organization',
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Ingredient name aliases for multi-language matching';

--
-- Dumping data for table `ingredient_aliases`
--

INSERT INTO `ingredient_aliases` (`alias_id`, `primary_name`, `alias_name`, `category`, `created_date`) VALUES
(1, 'Kamatis', 'Tomato', 'Vegetables', '2026-06-19 11:02:22'),
(2, 'Kamatis', 'Tomatoes', 'Vegetables', '2026-06-19 11:02:22'),
(3, 'Bombay', 'Onion', 'Vegetables', '2026-06-19 11:02:22'),
(4, 'Bombay', 'Onions', 'Vegetables', '2026-06-19 11:02:22'),
(5, 'Sibuyas', 'Onion', 'Vegetables', '2026-06-19 11:02:22'),
(6, 'Sibuyas', 'Onions', 'Vegetables', '2026-06-19 11:02:22'),
(7, 'Talong', 'Eggplant', 'Vegetables', '2026-06-19 11:02:22'),
(8, 'Talong', 'Eggplants', 'Vegetables', '2026-06-19 11:02:22'),
(9, 'Patatas', 'Potato', 'Vegetables', '2026-06-19 11:02:22'),
(10, 'Patatas', 'Potatoes', 'Vegetables', '2026-06-19 11:02:22'),
(11, 'Repolyo', 'Cabbage', 'Vegetables', '2026-06-19 11:02:22'),
(12, 'Kalabasa', 'Squash', 'Vegetables', '2026-06-19 11:02:22'),
(13, 'Kalabasa', 'Pumpkin', 'Vegetables', '2026-06-19 11:02:22'),
(14, 'Sitaw', 'String Beans', 'Vegetables', '2026-06-19 11:02:22'),
(15, 'Sitaw', 'Green Beans', 'Vegetables', '2026-06-19 11:02:22'),
(16, 'Pechay', 'Bok Choy', 'Vegetables', '2026-06-19 11:02:22'),
(17, 'Pechay', 'Chinese Cabbage', 'Vegetables', '2026-06-19 11:02:22'),
(18, 'Kangkong', 'Water Spinach', 'Vegetables', '2026-06-19 11:02:22'),
(19, 'Kangkong', 'River Spinach', 'Vegetables', '2026-06-19 11:02:22'),
(20, 'Ampalaya', 'Bitter Gourd', 'Vegetables', '2026-06-19 11:02:22'),
(21, 'Ampalaya', 'Bitter Melon', 'Vegetables', '2026-06-19 11:02:22'),
(22, 'Labanos', 'Radish', 'Vegetables', '2026-06-19 11:02:22'),
(23, 'Pipino', 'Cucumber', 'Vegetables', '2026-06-19 11:02:22'),
(24, 'Patola', 'Luffa', 'Vegetables', '2026-06-19 11:02:22'),
(25, 'Patola', 'Sponge Gourd', 'Vegetables', '2026-06-19 11:02:22'),
(26, 'Upo', 'Bottle Gourd', 'Vegetables', '2026-06-19 11:02:22'),
(27, 'Sayote', 'Chayote', 'Vegetables', '2026-06-19 11:02:22'),
(28, 'Okra', 'Okra', 'Vegetables', '2026-06-19 11:02:22'),
(29, 'Carrots', 'Karot', 'Vegetables', '2026-06-19 11:02:22'),
(30, 'Lettuce', 'Litsugas', 'Vegetables', '2026-06-19 11:02:22'),
(31, 'Broccoli', 'Broccoli', 'Vegetables', '2026-06-19 11:02:22'),
(32, 'Cauliflower', 'Cauliflower', 'Vegetables', '2026-06-19 11:02:22'),
(33, 'Bawang', 'Garlic', 'Spices', '2026-06-19 11:02:22'),
(34, 'Ahos', 'Garlic', 'Spices', '2026-06-19 11:02:22'),
(35, 'Luya', 'Ginger', 'Spices', '2026-06-19 11:02:22'),
(36, 'Sili', 'Chili', 'Spices', '2026-06-19 11:02:22'),
(37, 'Sili', 'Chili Pepper', 'Spices', '2026-06-19 11:02:22'),
(38, 'Sili', 'Hot Pepper', 'Spices', '2026-06-19 11:02:22'),
(39, 'Paminta', 'Pepper', 'Spices', '2026-06-19 11:02:22'),
(40, 'Paminta', 'Black Pepper', 'Spices', '2026-06-19 11:02:22'),
(41, 'Tanglad', 'Lemongrass', 'Spices', '2026-06-19 11:02:22'),
(42, 'Dahon ng Sibuyas', 'Green Onions', 'Spices', '2026-06-19 11:02:22'),
(43, 'Sibuyas Dahon', 'Spring Onions', 'Spices', '2026-06-19 11:02:22'),
(44, 'Dahon ng Laurel', 'Bay Leaf', 'Spices', '2026-06-19 11:02:22'),
(45, 'Saging', 'Banana', 'Fruits', '2026-06-19 11:02:22'),
(46, 'Saging', 'Bananas', 'Fruits', '2026-06-19 11:02:22'),
(47, 'Mangga', 'Mango', 'Fruits', '2026-06-19 11:02:22'),
(48, 'Mangga', 'Mangoes', 'Fruits', '2026-06-19 11:02:22'),
(49, 'Papaya', 'Papaya', 'Fruits', '2026-06-19 11:02:22'),
(50, 'Pinya', 'Pineapple', 'Fruits', '2026-06-19 11:02:22'),
(51, 'Pakwan', 'Watermelon', 'Fruits', '2026-06-19 11:02:22'),
(52, 'Melon', 'Cantaloupe', 'Fruits', '2026-06-19 11:02:22'),
(53, 'Ubas', 'Grapes', 'Fruits', '2026-06-19 11:02:22'),
(54, 'Kalamansi', 'Calamansi', 'Fruits', '2026-06-19 11:02:22'),
(55, 'Kalamansi', 'Philippine Lime', 'Fruits', '2026-06-19 11:02:22'),
(56, 'Dalandan', 'Orange', 'Fruits', '2026-06-19 11:02:22'),
(57, 'Suha', 'Pomelo', 'Fruits', '2026-06-19 11:02:22'),
(58, 'Bayabas', 'Guava', 'Fruits', '2026-06-19 11:02:22'),
(59, 'Avocado', 'Avocado', 'Fruits', '2026-06-19 11:02:22'),
(60, 'Santol', 'Santol', 'Fruits', '2026-06-19 11:02:22'),
(61, 'Durian', 'Durian', 'Fruits', '2026-06-19 11:02:22'),
(62, 'Kamote', 'Sweet Potato', 'Rootcrops', '2026-06-19 11:02:22'),
(63, 'Kamote', 'Sweet Potatoes', 'Rootcrops', '2026-06-19 11:02:22'),
(64, 'Cassava', 'Cassava', 'Rootcrops', '2026-06-19 11:02:22'),
(65, 'Kasuba', 'Cassava', 'Rootcrops', '2026-06-19 11:02:22'),
(66, 'Gabi', 'Taro', 'Rootcrops', '2026-06-19 11:02:22'),
(67, 'Gabi', 'Taro Root', 'Rootcrops', '2026-06-19 11:02:22'),
(68, 'Ube', 'Purple Yam', 'Rootcrops', '2026-06-19 11:02:22'),
(69, 'Bigas', 'Rice', 'Grains', '2026-06-19 11:02:22'),
(70, 'Mais', 'Corn', 'Grains', '2026-06-19 11:02:22'),
(71, 'Harina', 'Flour', 'Grains', '2026-06-19 11:02:22'),
(72, 'Tinapay', 'Bread', 'Grains', '2026-06-19 11:02:22'),
(73, 'Manok', 'Chicken', 'Protein', '2026-06-19 11:02:22'),
(74, 'Baboy', 'Pork', 'Protein', '2026-06-19 11:02:22'),
(75, 'Baka', 'Beef', 'Protein', '2026-06-19 11:02:22'),
(76, 'Isda', 'Fish', 'Protein', '2026-06-19 11:02:22'),
(77, 'Hipon', 'Shrimp', 'Protein', '2026-06-19 11:02:22'),
(78, 'Pusit', 'Squid', 'Protein', '2026-06-19 11:02:22'),
(79, 'Tahong', 'Mussels', 'Protein', '2026-06-19 11:02:22'),
(80, 'Talaba', 'Oyster', 'Protein', '2026-06-19 11:02:22'),
(81, 'Itlog', 'Egg', 'Protein', '2026-06-19 11:02:22'),
(82, 'Itlog', 'Eggs', 'Protein', '2026-06-19 11:02:22'),
(83, 'Toyo', 'Soy Sauce', 'Condiments', '2026-06-19 11:02:22'),
(84, 'Suka', 'Vinegar', 'Condiments', '2026-06-19 11:02:22'),
(85, 'Asin', 'Salt', 'Condiments', '2026-06-19 11:02:22'),
(86, 'Asukal', 'Sugar', 'Condiments', '2026-06-19 11:02:22'),
(87, 'Patis', 'Fish Sauce', 'Condiments', '2026-06-19 11:02:22'),
(88, 'Bagoong', 'Shrimp Paste', 'Condiments', '2026-06-19 11:02:22'),
(89, 'Mantika', 'Cooking Oil', 'Condiments', '2026-06-19 11:02:22'),
(90, 'Mantika', 'Oil', 'Condiments', '2026-06-19 11:02:22'),
(91, 'Gatas', 'Milk', 'Dairy', '2026-06-19 11:02:22'),
(92, 'Keso', 'Cheese', 'Dairy', '2026-06-19 11:02:22'),
(93, 'Mantikilya', 'Butter', 'Dairy', '2026-06-19 11:02:22');

-- --------------------------------------------------------

--
-- Table structure for table `meal_plans`
--

CREATE TABLE `meal_plans` (
  `meal_plan_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_by_user_id` int(11) DEFAULT NULL,
  `family_id` int(11) DEFAULT NULL,
  `plan_name` varchar(255) NOT NULL,
  `plan_description` text DEFAULT NULL,
  `target_weeks` int(11) DEFAULT 1 COMMENT 'Number of weeks this plan covers',
  `created_date` datetime DEFAULT current_timestamp(),
  `status` enum('Draft','Active','Archived') DEFAULT 'Draft',
  `ingredients_ready_percent` decimal(5,2) DEFAULT 0.00,
  `completion_status` enum('Not Started','In Progress','Completed','Abandoned') DEFAULT 'Not Started',
  `completed_by_mother` tinyint(1) DEFAULT 0 COMMENT 'Did mother complete this meal plan?',
  `completion_date` datetime DEFAULT NULL COMMENT 'When mother marked as completed',
  `mother_feedback` text DEFAULT NULL COMMENT 'Mother feedback about the meal plan',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meal_plans`
--

INSERT INTO `meal_plans` (`meal_plan_id`, `user_id`, `created_by_user_id`, `family_id`, `plan_name`, `plan_description`, `target_weeks`, `created_date`, `status`, `ingredients_ready_percent`, `completion_status`, `completed_by_mother`, `completion_date`, `mother_feedback`, `notes`) VALUES
(1, 54, NULL, NULL, 'Family Weekly Meal Plan', 'Balanced meals for the whole family with affordable ingredients from local markets. Focus on nutritious Filipino dishes.', 1, '2026-06-09 14:27:45', 'Draft', 0.00, 'Not Started', 0, NULL, NULL, '- Use vegetables from wet market\r\n- Buy rice in bulk\r\n- Prepare ingredients on Sunday\r\n- Children prefer less spicy food'),
(8, 54, 1, 62, 'Weight Gain Program - Week 1', 'High-calorie, high-protein meal plan designed for weight gain and recovery from malnutrition in children.', 1, '2026-06-22 13:40:05', 'Active', 0.00, 'Completed', 1, '2026-06-25 00:52:07', 'sacfasvav', 'IMPORTANT INSTRUCTIONS:\r\n• Feed 3 main meals + 2 snacks daily\r\n• Monitor weight weekly\r\n• Continue taking prescribed vitamins\r\n• Schedule follow-up in 4 weeks\r\n• Call BNS if child refuses to eat'),
(11, 54, 1, 62, 'Weight Gain Program - Week 1', 'High-calorie, high-protein meal plan designed for weight gain and recovery from malnutrition in children.', 1, '2026-06-22 20:29:43', 'Active', 0.00, 'Not Started', 0, NULL, NULL, 'IMPORTANT INSTRUCTIONS:\r\n• Feed 3 main meals + 2 snacks daily\r\n• Monitor weight weekly\r\n• Continue taking prescribed vitamins\r\n• Schedule follow-up in 4 weeks\r\n• Call BNS if child refuses to eat'),
(19, 54, 1, 62, 'Weight Gain Program - Week 1', 'High-calorie, high-protein meal plan designed for weight gain and recovery from malnutrition in children.', 1, '2026-06-25 00:01:31', 'Active', 0.00, 'Not Started', 0, NULL, NULL, 'IMPORTANT INSTRUCTIONS:\r\n• Feed 3 main meals + 2 snacks daily\r\n• Monitor weight weekly\r\n• Continue taking prescribed vitamins\r\n• Schedule follow-up in 4 weeks\r\n• Call BNS if child refuses to eat'),
(20, 54, 1, 62, 'Weight Gain Program - Week 1', 'High-calorie, high-protein meal plan designed for weight gain and recovery from malnutrition in children.', 1, '2026-06-25 00:42:55', 'Active', 0.00, 'Not Started', 0, NULL, NULL, 'IMPORTANT INSTRUCTIONS:\r\n• Feed 3 main meals + 2 snacks daily\r\n• Monitor weight weekly\r\n• Continue taking prescribed vitamins\r\n• Schedule follow-up in 4 weeks\r\n• Call BNS if child refuses to eat'),
(21, 54, 1, 62, 'Weight Gain Program - Week 1', 'High-calorie, high-protein meal plan designed for weight gain and recovery from malnutrition in children.', 1, '2026-06-25 12:00:48', 'Active', 0.00, 'Not Started', 0, NULL, NULL, 'IMPORTANT INSTRUCTIONS:\r\n• Feed 3 main meals + 2 snacks daily\r\n• Monitor weight weekly\r\n• Continue taking prescribed vitamins\r\n• Schedule follow-up in 4 weeks\r\n• Call BNS if child refuses to eat'),
(23, 54, 1, 62, 'Weight Gain Program - Week 1', 'High-calorie, high-protein meal plan designed for weight gain and recovery from malnutrition in children.', 1, '2026-06-25 14:08:42', 'Active', 0.00, 'Not Started', 0, NULL, NULL, 'IMPORTANT INSTRUCTIONS:\r\n• Feed 3 main meals + 2 snacks daily\r\n• Monitor weight weekly\r\n• Continue taking prescribed vitamins\r\n• Schedule follow-up in 4 weeks\r\n• Call BNS if child refuses to eat');

-- --------------------------------------------------------

--
-- Table structure for table `meal_plan_items`
--

CREATE TABLE `meal_plan_items` (
  `item_id` int(11) NOT NULL,
  `meal_plan_id` int(11) NOT NULL,
  `day_number` int(11) NOT NULL COMMENT '1=Monday, 2=Tuesday, etc.',
  `meal_type` enum('Breakfast','Lunch','Dinner','Snack') NOT NULL,
  `dish_name` varchar(255) NOT NULL,
  `food_category` enum('GO','GROW','GLOW') DEFAULT NULL,
  `ingredients` text DEFAULT NULL COMMENT 'Comma-separated list or JSON',
  `serving_size` varchar(100) DEFAULT NULL,
  `preparation_notes` text DEFAULT NULL,
  `nutritional_info` text DEFAULT NULL COMMENT 'Optional: calories, protein, etc.',
  `ingredient_completion_percent` decimal(5,2) DEFAULT 0.00,
  `is_consumed` tinyint(1) DEFAULT 0 COMMENT 'Has this meal been consumed?',
  `consumed_date` datetime DEFAULT NULL COMMENT 'When was this meal consumed',
  `consumed_by_user_id` int(11) DEFAULT NULL COMMENT 'Which family member consumed it',
  `consumption_notes` text DEFAULT NULL COMMENT 'Notes about consumption (e.g., children liked it)',
  `consumption_photo` varchar(255) DEFAULT NULL COMMENT 'Photo evidence of meal consumption'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meal_plan_items`
--

INSERT INTO `meal_plan_items` (`item_id`, `meal_plan_id`, `day_number`, `meal_type`, `dish_name`, `food_category`, `ingredients`, `serving_size`, `preparation_notes`, `nutritional_info`, `ingredient_completion_percent`, `is_consumed`, `consumed_date`, `consumed_by_user_id`, `consumption_notes`, `consumption_photo`) VALUES
(1, 1, 1, 'Breakfast', 'Chicken Adobo', NULL, 'Chicken', '5 servings', 'ugasjlkas', '350 calories', 0.00, 0, NULL, NULL, NULL, NULL),
(10, 8, 1, 'Breakfast', 'Pritong Isda (Fried Fish)', 'GROW', 'Tilapia or bangus, salt, pepper, oil', '1 piece', 'Season fish with salt and pepper. Fry until golden and crispy.', '200 kcal, 25g protein, 0g carbs', 0.00, 1, '2026-06-22 13:57:23', 54, 'yes they do love thefood', NULL),
(11, 8, 2, 'Breakfast', 'Lugaw (Rice Porridge)', 'GO', 'Rice, water, ginger, salt', '1 bowl', 'Boil rice with ginger until thick and creamy. Season with salt.', '180 kcal, 4g protein, 38g carbs', 0.00, 0, NULL, NULL, NULL, NULL),
(12, 8, 3, 'Dinner', 'Pinakbet', 'GLOW', 'Squash, eggplant, okra, string beans, bitter melon, tomato, shrimp paste', '1 cup', 'Simmer mixed vegetables with shrimp paste.', '80 kcal, 4g protein, 12g carbs', 0.00, 0, NULL, NULL, NULL, NULL),
(16, 11, 1, 'Breakfast', 'Lugaw (Rice Porridge)', 'GO', 'Rice, water, ginger, salt', '1 bowl', 'Boil rice with ginger until thick and creamy. Season with salt.', '180 kcal, 4g protein, 38g carbs', 0.00, 0, NULL, NULL, NULL, NULL),
(17, 11, 1, 'Lunch', 'Adobong Baboy (Pork Adobo)', 'GROW', 'Pork, soy sauce, vinegar, garlic, bay leaves, pepper', '2-3 pieces', 'Simmer pork in soy sauce and vinegar until tender.', '320 kcal, 25g protein, 5g carbs', 0.00, 1, '2026-06-23 00:41:46', 54, 'yes they do love thefood', NULL),
(28, 19, 1, 'Lunch', 'Ginisang Sayote (Sautéed Chayote)', 'GLOW', 'Sayote, garlic, onion, tomato, fish sauce', '1 cup', 'Sauté sliced sayote with garlic and tomato.', '35 kcal, 1g protein, 8g carbs', 0.00, 0, NULL, NULL, NULL, NULL),
(29, 19, 2, 'Dinner', 'Ginataang Kalabasa (Squash in Coconut Milk)', 'GLOW', 'Kalabasa, coconut milk, shrimp paste, string beans', '1 cup', 'Simmer squash in coconut milk with shrimp paste.', '120 kcal, 3g protein, 15g carbs', 0.00, 0, NULL, NULL, NULL, NULL),
(30, 20, 1, 'Lunch', 'Ginataang Kalabasa (Squash in Coconut Milk)', 'GLOW', 'Kalabasa, coconut milk, shrimp paste, string beans', '1 cup', 'Simmer squash in coconut milk with shrimp paste.', '120 kcal, 3g protein, 15g carbs', 0.00, 0, NULL, NULL, NULL, NULL),
(31, 20, 1, 'Dinner', 'Ginisang Repolyo (Sautéed Cabbage)', 'GLOW', 'Cabbage, garlic, onion, tomato, fish sauce', '1 cup', 'Sauté shredded cabbage with garlic.', '45 kcal, 2g protein, 8g carbs', 0.00, 0, NULL, NULL, NULL, NULL),
(32, 21, 1, 'Lunch', 'Ginisang Sitaw (Sautéed String Beans)', 'GLOW', 'String beans, garlic, onion, tomato, shrimp or pork', '1 cup', 'Sauté string beans with meat and tomato.', '60 kcal, 4g protein, 10g carbs', 0.00, 1, '2026-06-25 12:08:43', 54, 'Consumed by family', 'uploads/meal_consumption/32_1782360523.jpg'),
(34, 23, 1, 'Lunch', 'Ginisang Kangkong (Sautéed Water Spinach)', 'GLOW', 'Kangkong, garlic, onion, tomato, shrimp paste or fish sauce', '1 cup', 'Sauté kangkong with garlic and shrimp paste.', '40 kcal, 3g protein, 6g carbs', 0.00, 0, NULL, NULL, NULL, NULL),
(35, 23, 2, 'Lunch', 'Ginisang Sayote (Sautéed Chayote)', 'GLOW', 'Sayote, garlic, onion, tomato, fish sauce', '1 cup', 'Sauté sliced sayote with garlic and tomato.', '35 kcal, 1g protein, 8g carbs', 0.00, 1, '2026-06-25 15:19:54', 54, 'Consumed by family', 'uploads/meal_consumption/35_1782371994.jpg'),
(36, 23, 3, 'Breakfast', 'Ginisang Repolyo (Sautéed Cabbage)', 'GLOW', 'Cabbage, garlic, onion, tomato, fish sauce', '1 cup', 'Sauté shredded cabbage with garlic.', '45 kcal, 2g protein, 8g carbs', 0.00, 1, '2026-06-25 15:17:10', 54, 'yes they do love thefood', 'uploads/meal_consumption/36_1782371830.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `meal_recipes`
--

CREATE TABLE `meal_recipes` (
  `recipe_id` int(11) NOT NULL,
  `recipe_name` varchar(255) DEFAULT NULL,
  `food_category` enum('GO','GROW','GLOW') DEFAULT NULL,
  `meal_type` enum('Breakfast','Lunch','Dinner','Snack') DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `serving_size` varchar(100) DEFAULT NULL,
  `nutritional_info` varchar(255) DEFAULT NULL,
  `preparation_notes` text DEFAULT NULL,
  `is_popular` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_recipes`
--

INSERT INTO `meal_recipes` (`recipe_id`, `recipe_name`, `food_category`, `meal_type`, `ingredients`, `serving_size`, `nutritional_info`, `preparation_notes`, `is_popular`, `is_active`, `created_at`) VALUES
(1, 'Sinangag (Garlic Fried Rice)', 'GO', 'Breakfast', 'Leftover rice, garlic, oil, salt', '1 cup per person', '200 kcal, 4g protein, 40g carbs', 'Fry leftover rice with minced garlic in hot oil. Season with salt.', 1, 1, '2026-06-19 09:29:40'),
(2, 'Champorado (Chocolate Rice Porridge)', 'GO', 'Breakfast', 'Malagkit rice, cocoa powder, sugar, water', '1 bowl', '250 kcal, 5g protein, 50g carbs', 'Boil malagkit rice until soft. Add cocoa powder and sugar. Serve with milk.', 1, 1, '2026-06-19 09:29:40'),
(3, 'Lugaw (Rice Porridge)', 'GO', 'Breakfast', 'Rice, water, ginger, salt', '1 bowl', '180 kcal, 4g protein, 38g carbs', 'Boil rice with ginger until thick and creamy. Season with salt.', 1, 1, '2026-06-19 09:29:40'),
(4, 'Pandesal', 'GO', 'Breakfast', 'Pandesal bread (2-3 pieces)', '2-3 pieces', '150 kcal, 5g protein, 28g carbs', 'Serve fresh pandesal warm or toasted.', 1, 1, '2026-06-19 09:29:40'),
(5, 'Goto (Rice Porridge with Tripe)', 'GO', 'Breakfast', 'Rice, beef tripe, ginger, garlic, onion, fish sauce', '1 bowl', '220 kcal, 12g protein, 35g carbs', 'Boil rice and tripe with aromatics. Season with fish sauce.', 0, 1, '2026-06-19 09:29:40'),
(6, 'Pritong Itlog (Fried Egg)', 'GROW', 'Breakfast', 'Eggs (1-2 pieces), oil, salt', '1-2 eggs', '140 kcal, 12g protein, 1g carbs', 'Fry eggs sunny side up or scrambled. Season with salt.', 1, 1, '2026-06-19 09:29:40'),
(7, 'Tuyo (Dried Fish)', 'GROW', 'Breakfast', 'Dried fish (tuyo), oil for frying', '2-3 pieces', '120 kcal, 18g protein, 0g carbs', 'Fry dried fish until crispy. Serve hot.', 1, 1, '2026-06-19 09:29:40'),
(8, 'Danggit (Dried Rabbitfish)', 'GROW', 'Breakfast', 'Dried danggit, oil', '2-3 pieces', '130 kcal, 20g protein, 0g carbs', 'Fry danggit until golden and crispy.', 1, 1, '2026-06-19 09:29:40'),
(9, 'Tocino (Sweet Cured Pork)', 'GROW', 'Breakfast', 'Tocino slices, water', '3-4 slices', '200 kcal, 15g protein, 8g carbs', 'Pan-fry tocino with a little water until caramelized.', 1, 1, '2026-06-19 09:29:40'),
(10, 'Longganisa (Filipino Sausage)', 'GROW', 'Breakfast', 'Longganisa (2-3 pieces), water', '2-3 pieces', '220 kcal, 12g protein, 5g carbs', 'Fry longganisa with water until browned.', 1, 1, '2026-06-19 09:29:40'),
(11, 'Corned Beef', 'GROW', 'Breakfast', 'Canned corned beef, onion, oil', '1/2 cup', '180 kcal, 16g protein, 4g carbs', 'Sauté corned beef with onions until heated through.', 1, 1, '2026-06-19 09:29:40'),
(12, 'Ensaladang Kamatis (Tomato Salad)', 'GLOW', 'Breakfast', 'Tomatoes, onion, salt, vinegar', '1/2 cup', '25 kcal, 1g protein, 5g carbs', 'Slice tomatoes and onions. Season with salt and vinegar.', 1, 1, '2026-06-19 09:29:40'),
(13, 'Sliced Papaya', 'GLOW', 'Breakfast', 'Fresh papaya', '1/2 cup', '30 kcal, 0g protein, 8g carbs', 'Slice ripe papaya. Serve chilled.', 1, 1, '2026-06-19 09:29:40'),
(14, 'Banana (Saging)', 'GLOW', 'Breakfast', 'Ripe banana', '1 piece', '90 kcal, 1g protein, 23g carbs', 'Serve fresh ripe banana.', 1, 1, '2026-06-19 09:29:40'),
(15, 'Kanin (Plain White Rice)', 'GO', '', 'Rice, water', '1 cup cooked', '200 kcal, 4g protein, 45g carbs', 'Cook rice with proper water ratio until fluffy.', 1, 1, '2026-06-19 09:29:40'),
(16, 'Brown Rice', 'GO', '', 'Brown rice, water', '1 cup cooked', '215 kcal, 5g protein, 45g carbs', 'Cook brown rice (takes longer than white rice).', 0, 1, '2026-06-19 09:29:40'),
(17, 'Pansit Canton (Stir-fried Noodles)', 'GO', 'Lunch', 'Canton noodles, vegetables, meat, soy sauce', '1 plate', '300 kcal, 10g protein, 50g carbs', 'Stir-fry noodles with vegetables and meat. Season with soy sauce.', 1, 1, '2026-06-19 09:29:40'),
(18, 'Pancit Bihon', 'GO', 'Lunch', 'Bihon noodles, vegetables, chicken, soy sauce', '1 plate', '280 kcal, 12g protein, 48g carbs', 'Sauté bihon with chicken and vegetables.', 1, 1, '2026-06-19 09:29:40'),
(19, 'Adobong Manok (Chicken Adobo)', 'GROW', '', 'Chicken, soy sauce, vinegar, garlic, bay leaves, pepper', '2-3 pieces', '250 kcal, 30g protein, 5g carbs', 'Simmer chicken in soy sauce, vinegar, and garlic until tender.', 1, 1, '2026-06-19 09:29:41'),
(20, 'Adobong Baboy (Pork Adobo)', 'GROW', '', 'Pork, soy sauce, vinegar, garlic, bay leaves, pepper', '2-3 pieces', '320 kcal, 25g protein, 5g carbs', 'Simmer pork in soy sauce and vinegar until tender.', 1, 1, '2026-06-19 09:29:41'),
(21, 'Pritong Isda (Fried Fish)', 'GROW', '', 'Tilapia or bangus, salt, pepper, oil', '1 piece', '200 kcal, 25g protein, 0g carbs', 'Season fish with salt and pepper. Fry until golden and crispy.', 1, 1, '2026-06-19 09:29:41'),
(22, 'Ginisang Monggo (Sautéed Mung Beans)', 'GROW', '', 'Monggo beans, garlic, onion, tomato, pork, shrimp paste', '1 cup', '180 kcal, 15g protein, 20g carbs', 'Boil monggo beans. Sauté with garlic, onion, tomato, and meat.', 1, 1, '2026-06-19 09:29:41'),
(23, 'Tinolang Manok (Chicken Ginger Soup)', 'GROW', '', 'Chicken, ginger, onion, green papaya, malunggay, fish sauce', '1 bowl', '220 kcal, 28g protein, 8g carbs', 'Boil chicken with ginger. Add papaya and malunggay. Season with fish sauce.', 1, 1, '2026-06-19 09:29:41'),
(24, 'Sinigang na Baboy (Pork Tamarind Soup)', 'GROW', '', 'Pork, tamarind, tomato, onion, radish, kangkong, fish sauce', '1 bowl', '280 kcal, 25g protein, 12g carbs', 'Boil pork with tamarind. Add vegetables. Season with fish sauce.', 1, 1, '2026-06-19 09:29:41'),
(25, 'Sinigang na Hipon (Shrimp Tamarind Soup)', 'GROW', '', 'Shrimp, tamarind, tomato, onion, radish, kangkong, fish sauce', '1 bowl', '180 kcal, 22g protein, 10g carbs', 'Boil shrimp with tamarind and vegetables.', 1, 1, '2026-06-19 09:29:41'),
(26, 'Menudo', 'GROW', '', 'Pork, liver, potato, carrot, tomato sauce, bell pepper', '1 cup', '300 kcal, 22g protein, 15g carbs', 'Simmer pork and liver in tomato sauce with vegetables.', 1, 1, '2026-06-19 09:29:41'),
(27, 'Afritada', 'GROW', '', 'Chicken or pork, tomato sauce, potato, carrot, bell pepper', '1 cup', '280 kcal, 24g protein, 18g carbs', 'Simmer meat in tomato sauce with vegetables.', 1, 1, '2026-06-19 09:29:41'),
(28, 'Kaldereta', 'GROW', '', 'Beef or goat, tomato sauce, liver spread, potato, carrot, bell pepper', '1 cup', '350 kcal, 28g protein, 20g carbs', 'Simmer meat in rich tomato sauce with liver spread.', 1, 1, '2026-06-19 09:29:41'),
(29, 'Bicol Express', 'GROW', '', 'Pork, coconut milk, shrimp paste, chili, garlic, onion', '1 cup', '320 kcal, 20g protein, 8g carbs', 'Simmer pork in spicy coconut milk sauce.', 0, 1, '2026-06-19 09:29:41'),
(30, 'Fried Chicken', 'GROW', '', 'Chicken pieces, flour, salt, pepper, oil', '2-3 pieces', '280 kcal, 30g protein, 10g carbs', 'Coat chicken in seasoned flour. Deep fry until golden.', 1, 1, '2026-06-19 09:29:41'),
(31, 'Inihaw na Liempo (Grilled Pork Belly)', 'GROW', '', 'Pork belly, soy sauce, calamansi, garlic, pepper', '3-4 slices', '350 kcal, 22g protein, 2g carbs', 'Marinate pork belly. Grill until charred and cooked.', 1, 1, '2026-06-19 09:29:41'),
(32, 'Inihaw na Bangus (Grilled Milkfish)', 'GROW', '', 'Bangus, salt, pepper, calamansi', '1 piece', '220 kcal, 28g protein, 0g carbs', 'Season bangus. Grill until cooked through.', 1, 1, '2026-06-19 09:29:41'),
(33, 'Tortang Talong (Eggplant Omelette)', 'GROW', '', 'Eggplant, eggs, garlic, onion, tomato, salt', '2 pieces', '150 kcal, 10g protein, 8g carbs', 'Grill eggplant. Dip in beaten eggs. Fry until golden.', 1, 1, '2026-06-19 09:29:41'),
(34, 'Ginisang Kangkong (Sautéed Water Spinach)', 'GLOW', '', 'Kangkong, garlic, onion, tomato, shrimp paste or fish sauce', '1 cup', '40 kcal, 3g protein, 6g carbs', 'Sauté kangkong with garlic and shrimp paste.', 1, 1, '2026-06-19 09:29:41'),
(35, 'Ginataang Kalabasa (Squash in Coconut Milk)', 'GLOW', '', 'Kalabasa, coconut milk, shrimp paste, string beans', '1 cup', '120 kcal, 3g protein, 15g carbs', 'Simmer squash in coconut milk with shrimp paste.', 1, 1, '2026-06-19 09:29:41'),
(36, 'Pinakbet', 'GLOW', '', 'Squash, eggplant, okra, string beans, bitter melon, tomato, shrimp paste', '1 cup', '80 kcal, 4g protein, 12g carbs', 'Simmer mixed vegetables with shrimp paste.', 1, 1, '2026-06-19 09:29:41'),
(37, 'Ensaladang Talong (Eggplant Salad)', 'GLOW', '', 'Grilled eggplant, tomato, onion, vinegar, salt', '1 cup', '50 kcal, 2g protein, 10g carbs', 'Grill eggplant. Mash and mix with tomatoes and onions.', 1, 1, '2026-06-19 09:29:41'),
(38, 'Ginisang Sayote (Sautéed Chayote)', 'GLOW', '', 'Sayote, garlic, onion, tomato, fish sauce', '1 cup', '35 kcal, 1g protein, 8g carbs', 'Sauté sliced sayote with garlic and tomato.', 1, 1, '2026-06-19 09:29:41'),
(39, 'Ginisang Repolyo (Sautéed Cabbage)', 'GLOW', '', 'Cabbage, garlic, onion, tomato, fish sauce', '1 cup', '45 kcal, 2g protein, 8g carbs', 'Sauté shredded cabbage with garlic.', 1, 1, '2026-06-19 09:29:41'),
(40, 'Chopsuey', 'GLOW', '', 'Mixed vegetables (cauliflower, carrot, cabbage, bell pepper), soy sauce, oyster sauce', '1 cup', '70 kcal, 3g protein, 12g carbs', 'Stir-fry mixed vegetables with sauce.', 1, 1, '2026-06-19 09:29:41'),
(41, 'Ginisang Sitaw (Sautéed String Beans)', 'GLOW', '', 'String beans, garlic, onion, tomato, shrimp or pork', '1 cup', '60 kcal, 4g protein, 10g carbs', 'Sauté string beans with meat and tomato.', 1, 1, '2026-06-19 09:29:41'),
(42, 'Laing (Taro Leaves in Coconut Milk)', 'GLOW', '', 'Dried taro leaves, coconut milk, chili, shrimp paste', '1 cup', '140 kcal, 4g protein, 8g carbs', 'Simmer taro leaves in spicy coconut milk.', 0, 1, '2026-06-19 09:29:41'),
(43, 'Salad (Fresh Vegetable Salad)', 'GLOW', '', 'Lettuce, cucumber, tomato, carrot, salad dressing', '1 cup', '30 kcal, 1g protein, 6g carbs', 'Toss fresh vegetables with light dressing.', 1, 1, '2026-06-19 09:29:41'),
(44, 'Banana Cue (Caramelized Banana)', 'GO', 'Snack', 'Saba banana, brown sugar, oil', '2 pieces', '180 kcal, 1g protein, 45g carbs', 'Fry banana with caramelized sugar on a stick.', 1, 1, '2026-06-19 09:29:41'),
(45, 'Turon (Banana Spring Roll)', 'GO', 'Snack', 'Saba banana, brown sugar, spring roll wrapper, oil', '2 pieces', '200 kcal, 2g protein, 42g carbs', 'Wrap banana in wrapper with sugar. Fry until crispy.', 1, 1, '2026-06-19 09:29:41'),
(46, 'Puto (Steamed Rice Cake)', 'GO', 'Snack', 'Rice flour, sugar, baking powder, water', '2 pieces', '120 kcal, 2g protein, 26g carbs', 'Steam rice cake batter in small molds.', 1, 1, '2026-06-19 09:29:41'),
(47, 'Bibingka (Rice Cake)', 'GO', 'Snack', 'Rice flour, coconut milk, sugar, egg, butter', '1 slice', '180 kcal, 4g protein, 28g carbs', 'Bake rice cake batter with toppings.', 1, 1, '2026-06-19 09:29:41'),
(48, 'Kakanin (Sticky Rice Cake)', 'GO', 'Snack', 'Malagkit rice, coconut milk, sugar', '1 slice', '150 kcal, 2g protein, 32g carbs', 'Steam sticky rice with coconut milk and sugar.', 1, 1, '2026-06-19 09:29:41'),
(49, 'Pandesal with Filling', 'GO', 'Snack', 'Pandesal, butter or cheese or jam', '1-2 pieces', '170 kcal, 5g protein, 30g carbs', 'Slice pandesal and add filling.', 1, 1, '2026-06-19 09:29:41'),
(50, 'Boiled Egg (Itlog na Pula)', 'GROW', 'Snack', 'Hard-boiled eggs, salt', '1-2 eggs', '140 kcal, 12g protein, 1g carbs', 'Boil eggs. Peel and eat with salt.', 1, 1, '2026-06-19 09:29:41'),
(51, 'Kwek-Kwek (Battered Quail Eggs)', 'GROW', 'Snack', 'Quail eggs, flour batter, oil, vinegar dip', '5-6 pieces', '150 kcal, 10g protein, 12g carbs', 'Coat boiled eggs in orange batter. Deep fry.', 1, 1, '2026-06-19 09:29:41'),
(52, 'Fresh Fruit (Prutas)', 'GLOW', 'Snack', 'Seasonal fruits (mango, papaya, banana, watermelon)', '1 cup', '60 kcal, 1g protein, 15g carbs', 'Wash and slice fresh fruits.', 1, 1, '2026-06-19 09:29:41'),
(53, 'Halo-Halo Ingredients', 'GLOW', 'Snack', 'Shaved ice, mixed fruits, beans, milk, sugar', '1 glass', '200 kcal, 4g protein, 45g carbs', 'Layer ingredients with shaved ice and milk.', 1, 1, '2026-06-19 09:29:41');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_minutes`
--

CREATE TABLE `meeting_minutes` (
  `minute_id` int(11) NOT NULL,
  `proposal_id` int(11) DEFAULT NULL,
  `recorded_by_user_id` int(11) NOT NULL,
  `meeting_date` date NOT NULL,
  `meeting_time` time NOT NULL,
  `venue` varchar(255) NOT NULL,
  `meeting_type` varchar(50) DEFAULT 'Planning',
  `attendees` text DEFAULT NULL,
  `num_attendees` int(11) DEFAULT 0,
  `agenda` text NOT NULL,
  `discussion_summary` text NOT NULL,
  `decisions_made` text NOT NULL,
  `action_items` text DEFAULT NULL,
  `next_meeting_date` date DEFAULT NULL,
  `is_reviewed` tinyint(1) NOT NULL DEFAULT 0,
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `attachment_path` varchar(500) DEFAULT NULL,
  `signature_data` longtext DEFAULT NULL COMMENT 'Base64 encoded signature image from Committee Secretary',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meeting_minutes`
--

INSERT INTO `meeting_minutes` (`minute_id`, `proposal_id`, `recorded_by_user_id`, `meeting_date`, `meeting_time`, `venue`, `meeting_type`, `attendees`, `num_attendees`, `agenda`, `discussion_summary`, `decisions_made`, `action_items`, `next_meeting_date`, `is_reviewed`, `reviewed_by`, `reviewed_at`, `attachment_path`, `signature_data`, `created_at`, `updated_at`) VALUES
(5, NULL, 43, '2026-05-10', '08:00:00', 'Barangay Bayabas Health Center', 'Planning', '[{\"name\":\"Pedro Cruz\",\"role\":\"Punong Barangay \\/ BNC Chairperson\"},{\"name\":\"Alma Sedano\",\"role\":\"Committee on Health \\/ BNC Vice-Chairperson\"},{\"name\":\"Nancy Ongayo\",\"role\":\"Barangay Nutrition Scholar (BNS)\"},{\"name\":\"Teresa Silagan\",\"role\":\"Barangay Health Worker Representative\"}]', 4, 'Barangay Nutrition Council meeting para sa Supplementary Feeding Program', '1. Resulta sa Operation Timbang Plus (OPT+)\r\n•	Panaghisgot: Gi-report sa BNS ang resulta sa bag-ong screening diin naay 20 ka bata (11 ka lalaki ug 9 ka babaye) ang nakit-an nga malnourished o underweight.\r\n•	Lihok nga Pagahimoon: Nagkauyon ang konseho nga sugdan ang Supplementary Feeding Program para mahatagan og dinaliang tabang ang maong mga bata.\r\n2. Pag-review sa Project Proposal ug Budget\r\n•	Panaghisgot: Gipresentar sa Committee on Health ang plano para sa 120 ka adlaw nga feeding program. Ang budget kay ₱60.00 matag bata kada adlaw (Total: ₱144,000.00).\r\n•	Lihok nga Pagahimoon: Gi-aprobahan ang paggamit sa Barangay BCPC Fund alang niini nga proyekto kay kini alang man sa kaayohan sa mga bata.\r\n3. Seminar para sa mga Ginikanan\r\n•	Panaghisgot: Gihisgutan usab ang pagpahigayon og orientation para sa mga ginikanan bahin sa pagluto og sustansyadong pagkaon nga barato lang.', 'Feeding location: Nagkasabot ang tanan nga ang feeding program pagahimoon sa Barangay Bayabas Health Center / Session Hall aron dali ra ma-monitor sa BNS ug BHW ang matag bata.\r\nSanitation: Gihisgutan usab ang pagsiguro nga kanunay limpyo dapit aron malikay sa sakit ang mga bata.', 'Committee on health will prepare feeding proposal for this one. \r\nTungod kay walay dili angay nga hisgutan ang meeting opisyal nga natapos sa 12 sa udto.', NULL, 1, 41, '2026-06-02 05:35:36', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABLAAAAGQCAYAAAC+tZleAAAQAElEQVR4AezdCfQtW13Y+b/Ne/fNA/BoDDjE4RniBIoao21rtLsTaE1MNGqMUYMkdjog4hBFAQcQwWVQUFtXJGh3bKemHSPYa0XJSjrBMbQi+nRpVCI+9T2GNw88TP++9966t/77v6tOnXNq2Lvq+9be71TtqtrD59SpU/W7der/35z4nwIKKKCAAgoooIACCiiggAIKrF3A8SlQtYABrKrfPjuvgAIKKKCAAgoooIAC8wnYkgIKKKDAUgIGsJaSt10FFFBAAQUUUGCLAo5ZAQUUUEABBRQ4QMAA1gFobqKAAgoooMCSAratgAIKKKCAAgoooMDWBAxgbe0dd7wKKICAWQEFFFBAAQUUUEABBRRQoCIBA1gVvVllddXeKKCAAgoooIACCiiggAIKKKDA+gXKGKEBrDLeB3uhgAIKKKCAAgoooIACCiiwVgHHpYACRwsYwDqa0AoUUEABBRRQQAEFFFBgagHrV0ABBRTYtoABrG2//45eAQUUUEABBbYj4EgVUEABBRRQQIFqBQxgVfvW2XEFFFBAgfkFbFEBBRRQQAEFFFBAAQWWEDCAtYS6bSqwZQHHroACCiiggAIKKKCAAgoooMCeAgaw9gQrYXX7oIACCiiggAIKKKCAAgoooIAC6xdwhJcFDGBdtnBKAQUUUEABBRRQQAEFFFBgXQKORgEFViJgAGslb6TDUEABBRRQQAEFFFBgGgFrVUABBRRQYHkBA1jLvwf2QAEFFFBAAQXWLuD4FFBAAQUUUEABBY4SMIB1FJ8bK6CAAgrMJWA7CiiggAIKKKCAAgoosF0BA1jbfe8d+fYEHLECCiiggAIKKKCAAgoooIACVQoYwNrrbXNlBRRQQAEFFFBAAQUUUEABBRRYv4AjLE3AAFZp74j9UUABBRRQQAEFFFBAAQXWIOAYFFBAgREFDGCNiGlVCiiggAIKKKCAAgqMKWBdCiiggAIKKHBBwADWBQf/r4ACCiiggALrFHBUCiiggAIKKKCAAisQMIC1gjfRISiggALTCli7AgoooIACCiiggAIKKLCsgAGsZf1tfSsCjlMBBRRQQAEFFFBAAQUUUEABBQ4WqCaAdfAI3VABBRRQQAEFFFBAAQUUUEABBaoRsKMK5AQMYOVULFNAAQUUUEABBRSoWeCR6Px/7cjvjnKTAmsXcHwKKKDA6gQMYK3uLXVACiiggAIKKKDApgUejNE/KnJXGnj+27W55QoooIACCiiwhIBf4Euo26YCCiiggAJbEHCMCiwjcNUyzdqqAgoooIACCkwpYABrSl3rVkABBY4UcHMFFFBAgb0E/nyvtV1ZAQUUUEABBaoRMIBVzVtlRw8UcDMFFFBAAQUU2IYAz716jwFDfXjAOq6igAIKKKCAAoUJDAhgFdZju6OAAgoooIACCiigwFmBvudetdf2J4ZtDacVUECBUwLOKFCugAGsct8be6aAAgoooIACCigwTIC/OJiumSvz7qtUyfnxBaxRAQUUUGASAQNYk7BaqQIKKKCAAgoooMChAntu1/Xcq9x5rndf7Ynr6goooIACCpQikPtiL6Vv9kMBBRRQQAEFDhNwKwW2IvBgDDT33Ku7otykgAIKKKCAAisSMIC1ojfToSigwJgC1qWAAgooUIFA7o4q7si6OfruzwUDwaSAAgoooMBaBAxgreWdLHEc9kkBBRRQQAEFFJhOgEBVWjvPvWoe5n5lutB5BRRQQAEFFJhIYIZqDWDNgGwTCiiggAIKKKCAAqMKPBK15X462Jzb5oJbsYlJAQUUKFfAnimgQL9A8yXfv5ZLFVBAAQUUUEABBRQoQ+CO6EZzl1VMXkrvujj19njNBbfujnLTugUcnQIKKKDAigUMYK34zXVoCiiggAIKKKDAfgJVrH1Lppf8dPDcxfJHX3xtv7D8pnaB0woooIACCihQl4ABrLreL3urgAIKKFC6gP1TQIEpBbp+Gtic0/LTwlz7zfLcMssUUEABBRRQoAIBv8wreJPsogJbE3C8CiiggAIKZATeHWW5nwbeFeVN6vtpYbOOrwoooIACCihQoYABrArftAFddhUFFFBAAQUUUGBNAgSvcuet3JF188WBMn1x8tILPx1sflp4qdAJBRRQQAEFViSwmaHkTgQ2M3gHqoACCiiggAIKKFC8QFfwiuBUc8fVwzGK3N1ZBq8CxqSAArsEXK6AAjUIGMCq4V2yjwoooIACCiigwDYF+oJX7fPYKzM83JHV9UyszOoWHSXgxgoooIACCkws0P7in7gpq1dAAQUUUEABBRToErD8jMDQ4BWBqjMbR0Fzd1ZMmhRQQAEFFFCgdgEDWLW/g/ZfAQUUUKAR8FUBBdYjMDR49fYYcu6ng3dHuUkBBRRQQAEFViRgAGtFb6ZDUeB4AWtQQAEFFFBgcYGhwSs6+mj+l2SejXVTUuasAgoooIACClQuYABr7DfQ+hRQQAEFFFBAAQUOFdgneNX1fCvPbw/VdzsFFChZgOMjAXoy0yX3dTt9c6SzCvgFPyu3jSmggAIKKKCAAgp0CHBBljs35WItV557xtW7Ouq2WAEFChWwW4MF2sfB9vTgClxRgdoF3PFrfwftvwIKKKCAAgooUL/AvsGr3IPbCXSdq59i7xG4gQIKKKCAApsQMIC1ibfZQSqggAIKKKBAt4BLFhbYN3jF+rkHt3/mwuOweQUUUEABBRSYUMAA1oS4Vq2AAgpsRsCBKqCAAocJEIzKnY9yN1WunOde5cq5I+vHDuuCWymggALFC3CsnLuTfxANciwmMx2zJgWWFcidACzbI1tXYKMCDlsBBRRQQIGNCXBBljsX5WIpV87zrXLPvWL9XPnGOB2uAgqsWCB3TJx6uO/baqA93Sp2UoF5BZb4IEw1QutVQAEFFFBAAQUUqENg3+DVgzGsKyLnkuezORXLFFBgLQI3rGUgI4/D6jYo4Bf+Bt90h6yAAgoooIACCiwosG/w6t7o61WRc+nTc4WWKaDAEAHXqUTgnZX0024qMLmAAazJiW1AAQUUUEABBRRQ4KLAvsGrn4rtroucS98ThT8ZeblkywoooMD0Al3X7Dz7b/rWL7dwx+VJpxRYRqDrw7BMb2xVAQUUUEABBTYl4GA3JbBv8OozQufTIufSXVH4TyKbFFBAga0K5P4a65QWt0xZuXUrMETAANYQJddRQAEFyhWwZwoooEANAvsGrxjTa/hfJvM8rJsz5RYpoIACWxO4feYBf/3M7dmcAqcEDGCd4nBmmwKOWgEFFFBAAQUmFDgkeNX10xj+EuE1E/bVqhVQQIGaBB4/c2dfOHN7NqfAKYFxAlinqnRGAQUUUEABBRRQQIHzAocGr3I/jaGuc+dr9X8KKKCAAghwrHwvJmbKtHdyMlNjNqNAKmAAKxVxXgEFFFBAAQUUUGAMAQJOuXPN/xqV58qj+IQ7r3IXSJRfwQpmBdYg4BgUGFHgLSPWNaSqO4es5DoKTCHQdfIwRVvWqYACCiiggAIKKLANgbGDV49K2JxVQAEFFLggkAv6X1gyzf8fO0211qrAbgEDWLuNXEMBBRRQQIEVCjgkBSYTOCR4xTa5izDu1jJ4NdlbZcUKKFChAMfLtNv/JS2YeN6HuU8MbPV5AQNYeRdLFVBAgd0CrqGAAgookApwYZU7vyQQlStn+0fif7llfdvEJiYFFFBgkwK5n1M/cWYJH+Y+M7jNXRDInSxcWOL/FZhBwCYUUEABBRRQYDUChwSv+KuCuTusDF6tZrdwIAooMIEAx8h2tbk7WNvLx56eu72x+299Cwkc26wBrGMF3V4BBRRQQAEFFFDgkODVQ8GWu5Mgik88R0XBrIACCpwWaOa+t5lovXIcbs1OPnnH5C3YgAKJgCcHCYizCiiggAIKKKCAAnsJPBhr584puUMgVx6rn9wd/zsXOZeenSu0TIFxBKxFgVUIfHFmFF3H28yqoxTdMkotVqLAHgJz7+R7dM1VFVBAAQUUUEABBYoTONuhq84WnfQFr94z1r8hci7x162+M7fAMgUUUECBUwJ/fmruwswPX3iZ5P//MVOrD3PPoFg0nYABrOlsrVkBBRRQQIGsgIUKrEggdwHVF7zirqvbO8bPXVlv71hmsQIKKKDAaYHc8wM/6/Qqo859fKY2H+aeQbFoOgEDWNPZWrMCCkwnYM0KKKCAAssL8NcDcw/y7Tu/5LlXuZ7zM8SbcgssU0ABBRToFOAfDNoLc8fk9vJjp/nDG+06pm6v3ZbTCpz0nWDIs2oBB6eAAgoooIACChwswMN7c//6n17ctBvI3a3Fcra5hgmzAgoooMBeAvdn1r43UzZW0SdmKrozU2ZRcQLr6JABrHW8j45CAQUUUEABBRSYUyD38F7uBOAngrl+ELzK/Us9fzWra5tcPZYpoIACywiU2er1mW5dmykbq+gNmYp4dmGm2CIFxhcwgDW+qTUqoIACCiiggAJrFiAYlRtf13kl6+eCV5RfkavIsnUKOCoFFJhEgH88aFecO962lx87/e8yFfgw9wyKReMLdJ1ojN+SNSqggAIKKKCAAgocI1DCtjzDKndxxAPYc/0jSJVbn/LcTxBzdVimgAIKKNAt8OrMIu5uzRSPUpT7GaEPcx+F1kp2CRjA2iXkcgUUUECBFQk4FAUUOFIg93M/glG5B7BzAZULXnG3gMGrI98IN1dAAQUuCjzz4mv7ZerrfJ5d2G4vd6xvL3dagVEEpt6xR+mklSigQEECdkUBBRRQYKsCBKrSsXcFo/gLhbnzTNbPlaf1Oq+AAgooMFwgd3x+1fDN914zdxeWD3Pfm9EN9hXwBGJfsRHWtwoFFFBAAQUUUKAyAQJSuX9hz51L8i/zuTusDF5V9qbbXQUUqEYgd8x9xoS992Hue+C66ngCuZOO8Wq3JgUUUEABBRRQQIHaBe6IAeQujghUxaJT6YGY63owu+edgWNSQIG9BdxgmAD/SNBeM/ePDu3lx077MPdjBd1+bwFPJPYmcwMFFFBAAQUUUGBTArdkRsuFUvo8LB7kfnVmXYqezf/MSwnYrgIKbEDg/swY782UjVWU+xmhD3MfS9d6sgIGsLIsFiqggAIKKKCAAi2B7U7mnquCRnoO+cYovCFyLv1MFH5nZJMCCiigwHQC12eqvjZTNmZReifu1Hd9jdl366pQID35qHAIdlkBBRRQoAYB+6iAAtUJPBg9zl2M3BXl7fSsmHlK5Fxi3U/NLbBMAQUUUGB0Ae6ObVeaO4a3lx87nbsLy4e5H6vq9p0CBrA6aVygQHECdkgBBRRQQIE5Ba7KNMYdWTcn5d+RzDezBMDSdZtlviqggAIKjC/w6kyV786UjVXkw9zHkrSeQQIbC2ANMnElBRRQQAEFFFBg6wIEqlID/mU/fZh7bj2242cl1zBhVkABBRSYTeCZmZamvuYv+GHuGQ2LqhaYemeuGsfOK6CAAgoooIACGxR4JMac+9lJet5I8Cq36DyjrAAAEABJREFUHv/anz7gPao0KaBAdQJ2uEYBjs1pv1+VFow4n/sZoQ9zHxHYqi4LpCcil5c4pYACCiiggAIKKLA1gbfEgNO7rKLohKAWr03mAikXvKL8imYlX09ONFBAAQVmFsgdw58xcR+467bdRO77ob3caQUOEjCAdRCbGymggAIKKKDATAI2M6/Ae2ea46eDV7bKCVLlLk4oz104tTZ1UgEFFFBgBgGO2+1mcsfs9vJjp3N33aZBrWPbcHsFTgxguRMooIACqxdwgAoooMAgAQJQuRXb54v8PDB3IcTFksGrnJ5lCiigwPwC92eavDdTNmURd+N+6JQNWPf2BNonJNsbvSNWYKiA6ymggAIKKLBuAf5iYC4wdXdr2PyMMHfuSPAqV97a1EkFFFBAgRkFrs+0dW2mbMyir8lU9sZMmUUKHCww28nGwT10QwUUUEABBRRQQIGpBa7KNMAdWTddLOenILk7rAxeXQTyRQEFFChMgONzu0u5f6RoLz92+pvbFVyc9i6sixC+jCNgAGscR2tRQAEFFFBAAQVqFSBQlfadC58mYPVALOQiJF7OJM8lz5BYoMAoAlaiwLECr85UwM/AM8WjFXkX1miU1Vb0KdHzt0X+zchPjjxq8qRjVE4rU0ABBRRQQAEFqhLgZ4G5f5VvzhH5CeHVHSN6Vkd5IcV2QwEFFNi0wDMzo2+O7ZlFoxR13YX1l0ap3UpqECBw+pjo6F+O/DORR01T78CjdtbKFFBAAQUUUGBGAZtau8AdMcDmLquYvJT4uSAzPLvkBiYymZPS78qUW6SAAgooUI5A7g7bV03cvdxdWL8xcZtWX4bAs6Mb7xO5Sfc1E2O9GsAaS9J6FFBAgYyARQoooEDBArdk+sZPB89FOXdXPSVec+muKPzUyCYFFFBAgbIFcv9I8YyJu9x1F5Z/kXBi+IWr527tr0v68KPJ/NGzBrCOJrSCiQWsXgEFFFBAAQXGF8j9qzytNOeG38FMJvPXCm/OlFukgAIKKFCmAP8w0e5Z7mfj7eVjTOfuwuKu3jHqto4yBb40uvXYyE3iH7u+tZnZ47V31eYkpXclFyqggAIKKKCAAgqsRoAgVO4ChpNNBtkV3OKnhdewglkBBRRQoFSBM/26/0zJycm9mbIxi7wLa0zN8uu6Kbr41ZHb6aUx05xXxOQ4yQDWOI7WooACCiiggAIK1CJwVaajBK24s4rXXHCLv1zFTwszm1qkwMoEHI4C6xK4PjOcazNlYxd9TaZC78LKoKyg6CtiDASx4uV84q8Qfvv5qZH/ZwBrZFCrU0ABBRRQQAEFChYgQJV2j5+X8JwUluWCV5RfkW7UN+8yBRRQQIGiBDjOtzuUO9a3l48x3XUX1hh1W0c5AjxP88uS7nxDzHO3d7yMmwxgjetpbQoooIACCowhYB0KTCHwSFSau2jhfJAgVW4Z5QS3YlOTAgoooEClAq/O9Js7azPFoxbl7sLi5+ijNmJliwp8VbTevqPvv8R813M0Y9FxiROW42pwawUUUKBIATulgAIKKNASuCOmc4EoLiQIUuWCV/yLfW6bqMqkgAIKKFCRwDMzfZ0jFtB1F5Z/kTDzhlRY9Pjo83MitxN3X7XnR52eY6cdtcNWNqOATSmggAIKKKDAWgS4xT8dCwGqK6OwK3jleWLgmBRQQIGVCHDMX2IoubuwfBbWEu/Erjb3X06wivOIZsvfiYl/GXmy5InJZLRWrIACCiiggAIKFCHAHVa5juQCV6zHRY7niEiYFVBAgT0ECl+V4ELaxTl+Rth1F1baF+frEni/6O4XR26nF7Rnppj25GQKVetUQAEFFFBAAQXKEHgoutEVqIpFZ5LBqzMkFkws8EDUz09ZuZAmE3BtZ/bJLeRmzBjgESwmBUYVeFKmtn2+HzKbDy7K3YXlfj6Yr8gVufuq3bFfi5kfjTxpMoA1Ka+VK6CAAgoooIACcwqcaevcmZLuAoIEnht2+7jkssDHxeRYgaero64rIrPvkbmgbudYtInUjBkDPPg85nIa6PrpTeg4yLEE2KfadbHfteenmu66C8tnYU0lPm29BEP/ftLE5Hdf0R4HSF7NCiiggAIKKICAWYH1CHChO3Q0rOt54VCtutcbI/D0H4LAwFMgLJAIOJD5vBLo+tToA0GJNPOZJjd3dLWfUxObmDYqcF9m3NypmykevSh3F5bPwhqdeZYKvyla4RgUL+fTL8X/ZwmmtxuNNk0KKKDA8QLWoIACCiiwuMAj0QMucuNlZ+IC1782uJNp0RW842lR/iob5/NP5nqPQNfDMYo0yMU8QS4yxwF+0vXjsZ5pvQI3ZIY2V3DTu7Ay+BUWfXj0+e9EbqdccLK9fLRpDmijVWZFowlYkQIKKKCAAgoocKjAq2LDoQGpB2NdLm7jxbSQAEEDAghkAgq5vKU7nnLj31LZ3LshQS4y14UcCz49OtD2Zr/8gygzrUeA97c9Gt7/9vyU07lAh3dhnZxMaT523d+SVPj6mP+5yLMkDlSzNGQjCiiggAIKKKCAArMIfNHAVu6M9a6JbJpe4OejCe5wIRhA5gKyyQQNuIAkx2rVpmY87VfGyrgJ0v1WjIwx7spcn2w553z4iReWbdvgnCXRn/eNlmi/4EBW9NA0VIDPZLru7WnBRPPehTUR7EzVfkK089cjt9NXtWemnubLYeo2rF8BBRRQQAEFFFBgHgEuMoe0xEXp44as6Dp7CRCo4T0gt4MNfy1q4bwbd3LMFpMu9PPkpP1K/7nIZTz/OXpKn3dlxpdm7gQkQMcfE/jgqMd0mADPG8Oy7Zt7P/iZIO9d+708rMWzW9GegayzLjWW5H4y+PgZB+JdWDNij9zUS5P6firmfznybImD4GyN2ZACCiiggAIKKLBWgQLGxV0aXGT2dYUL213r9G3vsgsCBHcIFJAxbTLBGnzJF9ac7v9Nm+1X+kPfxgw8fcB0Q7DmkQWuivqmDnSxbxvICuiVJd7XuYbUdRfWXO3bzmEC3HnF8xjbWz+/PTPHtAGsOZRtQwEFFFBgiIDrKKDAcQLc5dJXA4EOz/36hE4vIwhEQIiMXTvjyAUf+fRWh8819ROAot5dmT6kmeAFQTT2BQNPh78Xa99ySKCL/b7Pgf2TQBb7a996LitTIPf+zvYcoyDJ3YXFMTcWmQoVeEnSrx+K+TdFnjXxpTdrgzamgAJTCli3AgoooMBGBXZdRHKx4nlffufADh9yE0TilUAQF+nk/JaHlVI3bf1ebE7dTeb9IdNuLDIpsKgAwVD2TfbVvo6wz/Ytd1mZAry/ac/4qXNaNtW8d2FNJTtNvfzVwY9Mqn5hMj/LrAeclNl5BRRQQAEFFFCgLoFviu72ndPxr9q5i5XYbDMJAy7EyQSQ2hk7LtTJY4E09RMco952pj3ejw8cqzHrUWBCAfZV9l8+OxM2Y9UFCPA+z9mNMu7CmnPE9bb1oqTrr4r53408e+ILdPZGbVABBRRQQAEFFFBgFIEnRi25i4AoPp/ui//zc7J42UQiYMSFNrkJIvHKXU1cnJHHhKBu2vJuqjFVratEgc5AVomdtU+DBDh2DVpxopW8C2si2JGr/QdRX/pHONKAVqwyTzKANY+zrSiggAIKKKCAAmML3BsV/lHkrvStseD6yGtLS91N9UhA8gBbgmBN5lyaC/tj7qaKak0KVCPA/l5NZ+1or8CnZ5bOHdTK/QMMx/hM1yxaQIDPe/rsq++Mfrwl8iKJL91FGrZRBRRQQAEFFFBgHIFN1sIJ/nUdI+euIAIsX9mxvJbi0u6mujLg3hDZpIACCqxB4Kczg+C7I1M8WZF3YU1GO0rF/yhqea/ITXogJl4cebFkAGsxehtWQAEFChKwKwooUJMAgR1+EpfrM8Grms7vCMTxL/5k+t7OjIOLKXJurIeUNfVjSL3tTHv8a7N3Ux0i6zZbEeCzs5WxbmGcHBOXHqd3YS39DuTbvzqK0we1f3uU/WnkxRJf1Is1bsMKrEnAsSiggAIKKDCDAIGevvM3nsU0Qzf2auLnY20ueuk7mQumJhOIa4JIsdpoifppC4+mfl6xI9PuaI1ZkQIbEuDzs6Hhrn6o6c/DGDDHa17nyl13YX3oXB2wnazAs6L0L0RuEs/UfFkzs9RrSQegpQxsVwEFFFBAAQUUqEGAgAxBmK6+svzWroUzlHfdTfXXom3OOek7OWZHSQSpyD6bahROK1Fgp8AnZdbgM5gptqgSgedn+snxOlM8elG7wtxdWG9sr+D0rAI8ouCrkxYJXt2VlM0+u8TOOfsgbVABBRRQQAEFFKhcgIvEXcEffv429TBLvJvKZ1NN/a5bf6ECs3fr5zIt/kKmzKK6BPh+WbrH3oW19Dtwuv0vj9nHRm7S22Li5ZEXTwawFn8L7IACCiiggAIKKNAp8AOxZMjFxcOx3phpqbup+OkKgbp25nyV4Nz4z6YaU8y6FFi/AJ/FdJQflxY4X51A7k4njsVzD8S7sOYWz7d3cxR/ReR24qem/ISwXbbIdO4gtEhHbFQBBRRQQAEF6hOwx5MKPBS1//3IuxIBrqt2rdSxnIsUfnpIpp4m84yoJojUselBxdRPWz6b6iA+N1JgMQGOFWnjfJ7TMufrE3hqpssc/zPFkxZ13YU1aaNWfkbgq6LkhshN+uOYKOLuq+jHiQEsFMwKKKDAcgK2rIACCuQEeK7TudyCTNmQ8znqI3DEBWc7sy0XKuRM1QcVNfVzwUu97Ux73k11EKsbKbCYAJ9lPrtpB3Jl6TrO1yHAcbvdU47b7fm5pnN3YXFH8Fztb72dxwfAl0Zup29ozyw97UFn6XfA9kcQsAoFFFBAAQVWJcDFIkGeIYMiKJWux/aUc0HSZOob+4KEumnHu6nSd8B5BdYjwPEkd83I5389o3QkRfw8LN4G78IKhAXT10bbV0du0u/HxL+IXEy6cDAqpjt2RAEFFFBAAQUU2LQAAaF9zs8ISrENF5NNZnvKx4Bs6uQiljrbmXYIjPlsqjGkrUOB8gT43PM5T3vGcSFXnq7nfKkCZ/vV/snY2aXzlngX1rzeTWvvExPPjtxOL2jPlDDtgaeEd8E+KKCAAgoooIACJycEoggQ7WPB+uR9tsmtywUp7Xs3VU7HMgUSgQ3MGrzawJucDJHvgaRokVnvwlqE/eTrkmbfHPP/Z+SikgGsot4OO6OAAgoooIACGxT4RzFmLhzGCERFVb2JdghU0VY7c044591UvZ10oQIKLCrAM/M4JqSd4PiRK0/Xc75OAb4bSum5d2HN+058UDT3jMjtVNzdV3TOAxAKZgUUUEABBaoTsMMrEeAuhymeL8GFJpkL0VygaiV8DkMBBUYW4IHZBLPTajmeeO2Yqqxrnr8+W8qIvAtr3ncifVD7r0bzPx65uORBqLi3xA4poMBsAjakgAIKzCfwQDTFv26TuRBs8hjnYk1dD0UbTbCKeslXRplJAQUUGCLAcaoriMHxZJMwwUIAABAASURBVEgdrqPAWALehTWWZH89Hx6LPydyOz2/PVPStAeikt6NCvtilxVQQAEFFFDgjAB3MBCoIjfBJf6qTxNcOrPBngXUy7Mpmvo4nyPTxp5VuboCCihwXuCu+H/XMeS6WGbahgDfL6WM1Luw5nknXpw08+9j/mcjZ9PShZzsLN0H21dAAQUUUEABBWoQIDDFT/44wW/nJkjVvHIHQxNcGntc3xkV8vOeD41XkwIKKDCGAM+/ubGjoveN8vsjm8YRKL0Wvl9K6qN3YU37bnxsVP9pkdvpee2Z0qYNYJX2jtgfBRRQQAEFFJhDYGgwqglK8UpginOnJjjVvI7VX4JjfXUSNEv/xPVYbVuPApUI2M2RBa6N+n47ci69NQrfEtmkwFIC3oU1rfxLk+pfF/P/IXKxiZOwYjtnxxRQQAEFFFBAgSMECAgR9CH4lOapg1H7dvvB2IA+xcsJ/eY1zeP8y3haq/MKKLBlgfs6Bs8x6b06llm8bgG+N0saoXdhTfNufEpU+4mR2+lr2zMlThvAKvFdsU8KKKCAAkUL2LkiBV4bveKkm9wEqzjP4Y6mWLRIoh/0hwcj93WAf/G8prUC/W7Nnp98+Pz//Z8CCigwngDHp1xt3KHaPibl1rFsvQKfVNjQvAtrmjfkZUm1/3fMvzFy0Sl3glR0h+2cAgqsQsBBKKCAAmMJcLcSgaKnRYUEq8gxOXmizSZzEUg/7olWab/JnGc9EmV9F4Ks+/RYp0nU1Uw3r7RzVTPjqwIKKDCCAMcajj9pVRzLzqWFzhcl8G0T94aHeE/cxN7V5+7C8h929ma8tMHfjKmnRm6nF7RnSp3mxKrUvtmvXgEXKqCAAgoooEAIzHEuQwCJzAUfdyZw0Ue7Teanffz8L30IMsGrrgtB6qOeGMKplCujnVMrOaOAAgocIcCxLHesoZxj2RFVu+kMAl88Qxt8R83QzOAmcndhXRlb/43IG0mjDvPFSW3/R8z/VuTikydExb9FdlABBRRQQAEFFhbgQo/MeRMnzJzYtzMXfWQCVk1XuYuBwFYz335lW+pqlzHNNry2M/W2551WQAEFjhHgmMLxLK2D8q5jVrpunfPr6fUcd+QO/Y6aU/XvZRr715kyi/oFcPywZJWvS+aLnc3tmMV21o4poIACCiiggAKJAMGgpGj2WS4GyVz80R9y1zkWF4ldy3Ll1Dn7gGxQgZyAZdULECTnWJUOhGOWx5pUpdz53Hs4R29z31FztNu08cMxwT4cL5cS++0/vjTnxBCBb0xW+u6Y/4PIVaSld8IqkOykAgoooIACChQrwLkMz8HgAuzYPPUgqZ/+Nv0kmEXmhJwxsLydWa8977QCCihwqAB3iHL8SbfnOJMrT9dzXgEE/pT/LZg/IdP292TKLMoLfFEUf2DkdnpRe6b0aQ9Wpb9D9k8BBRRQoCXgpAJZAX5OwTnNsZl/1W7nu6I1Akxc4JFjdtTUtEW/+WlirnKCW1x45pZZpoACCgwR4Nl93KmSrstxjeNPWu58WQK8T+0e8d3Rnp9z+nFzNpZp6w1Rlv6DDx5VBWFiDEulb0ga/taYvz1yNckDVjVvlR1VYCQBq1FAAQUUGCpwc6zIRR/nS2ROktuZ4BIXFuRYdfREW7RLH2ijnQmsNZl+cIE6egesUAEFqhd4KEbQ9WB2ji+x2KTAYAG+lwavPNGK/KNVWvXz0wLnzwh8SZQ8MXKT7o2Jl0WuKnnQOuDtchMFFFBAAQUUUCAEuCgkeLTECT1tNpnzOfrSDnC1pw10xZtlUmCDAtxF2vWXUAnQb5CkyiFzPC+p43zvLd0fgi9pH/73tGCs+RXUc02M4Wsjt9PLY+bOyFUlTniq6rCdVUABBRRQQAEFChHgJJ7AUa47XHA0Aab0lYtKgkqsQ85tP2ZZ0z7nffSXNnOZPpEZl3d0jfkOWJcC8wt8dDR5Y+Rc+vAo5DgUL7MkGzlO4MHjNh99a75LRq90zwpvyKz/+Zkyiy4IfGm8/LeRm/T2mODng/FSVyph56tLzN4qoIACCiiggAInJwR6us6j+pZhx50P/CyQ7cmUpbkJMKXlU84b6JpS96i63ViBvQRuirV/KXIuvTUK3xTZVI/AdQV2demHuUPyNv6X5H+bzDt7csLx4CsTiG+J+XsiV5e6TpqqG4gdVkABBRRQQAEFOgXGXUCAimBPrlbuXiI4lVuWK3tKppDgFedoZNppZ55nQ/us0+RMFZMWNf2hf/vc0fV9k/bKyhVQoBF4ZzORvHInz3slZc7WKXDbwt1e+mHuDP8W/pfkT0zmnT05+WeB8OjITSLw94pmprZXTjxq67P9VUABBRRYQMAmFVDgvABBIwI452eS/3FxSEAnKe6d/dXM0jsyZU3R1TFBgIxzuCbTnzSXGOj6wug7fmkmIEcm+MdPFw10BZRJgQMF+CzlNuWzxXNwcsssq0/g1oW7zHfOwl043/xvn///6f/98unZTc89Nkb/nMjtxF8i5HylXVbNNCc+1XTWjipQuYDdV0ABBRSoV+B/ia4TeImXbPonUXrIxWHuXOzxUdexaWig6+FoiAtextbkKJo1cSFExoIA4NBA1yGdbI/1kUMqcBsFChZg/+azlHaR4HDXw9zTdZ2vQyD3Ps/dc/a3udtM23tSWhDzHxXZdEGAB7e3f4L6R1H8HZGrTZwoVNR5u6qAAgoooIACCswuwN1M393TKhcS39OzvGvR9ZkFBJEyxZMVXRU1D7mjq8RAF1a5zEUVmYt27jqJIV5KvFfNDONupn1VoHYB9vn2/t2Mh3ICw828rwqMJZDb38aq+2I9g15yd1wRqBm08YpXekKM7bmR2+nr2jM1ThvAqvFds88KKKCAAgooMJcAd+l03blA8OSYE/jcXwH75LkGtmc7awl0pcPm4j4X6ErXc16BkgXYh3PHoj8/OTkxUFvyO1dX39jP0h6X8DD3j0k7FfNPjLz1lAar+Lnlq2tHMYBV+zto/xVQQAEFFFBgKgFO1rsu/gheHXseldu+9r+gNDTQxV1tBI9wbPJU72Nfve8RC3kfuEOl6cep11hOP8nsD9zR5TO6AsVUjAD7Jftw2iH2467jV7qu8+UL8H62e8mxqz0/xzTHybSdEh7mTp9+gv8lueuPGSSrrXL2/WNUXxS5nb6+PVPrdO5gV+tY7LcCCiiggAIKlCdQa48IWHSdJ/UtO2a86QXKMXWVvq3P6Cr9HbJ/NQh0HYs4lnQdv2oYl30sV4B9q927JQJp7fab6b/dTLReb2pNb22SB7W3A9i/FgA/HLn65IGt+rfQASigwPoFHKECCswowL/YcoLedVLOHTjtk8JDu8ZdE+m2L0kLnD8ZekfXWp7R5VuuwFABgle54xTHL6/xhirWsx7vawm9ze1bue+zJfr6XZlG78+Urb3oQ2KAnxe5nXiYe3u+2uncDljtYOy4Ap0CLlBAAQUUUGC3ACfhff9ie19U0fU8rFi0V8qdgz1/rxpcuS2wK9DVXnfpaYIOZPYBfpLDhWkuE6Ags18SOF2637ZfjgD7BftQ2iP2I/artNz5+gXeUvAQStnnnhVGfAbi5VI65K8DX9q40okXJf3+pZj/mcirSIN3tlWM1kEooIACCiiggAJ5AS4I+86LuFjM/dXAfG37l6Yn3fvX4BZdAjxvq2sZ72sue0dXl5jlSwtwrGKfTftBed8xLF3f+boE3m/u7va0R1A9XVzCw9zp0/P4X5I5nidFq519aows/TnlV0fZapIHudW8lQ5EAQUUUEABBQ4Q4K8MEjzKXRBSHReFXctYfkjOnfyX+tcHDxlfSdtwrnvIXXO77uhinyBzYcQ+wj7U5LnHTz/IjLXrji76SGbf+425O7jR9qYYNvsY73VaN+/tGD9tTut1vmyBuxbqHseZtOlSHub+sugYn5N4uZSujKlPjLyF9M3JIH8+5l8feTWJL7rVDMaBKKCAAgoooIACewjsuujj4mCKi8Lc+Vftf31wD/ahq46yHgGbUSrqqKSWQBdBDzL7Hs9H4QKvyXwOyFgRkOsYqsULC/B+5brA+zbFcSrXlmVlCUx5V/Cukab7I8eXXdvMtfyzMg39XKZsbUWfEAP6HyO309e0Z9YwzZfYGsbhGBRQQAEFFFAgFXC+S4DnCXHy3XXC3Sy7uauCI8pvzGxLe5lii44UIChzZBWjbV5yoIvPAZnrAu5UYH9sZxzJ3K04GogV7SXA+5HbgPckdzdMbl3L1ifA53apUXG8SNsmmJqWLTH/mmiUz0a8XEoEeZ9xaW6dE+ndVz8Vw/zFyKtKuR1vVQN0MAoooMAxAm6rgAKrE+BCvO+C74EY8ZTnR++I+tP09LTA+aMFuHgZcnFX2h1HJQa6cCRzAUggpZ35PJG5cP2zo981K0gF7o0CvOPlTCIQT8DxzAILFFhIYMrvzn2H9LGZDV6VKVtL0dNiIB8fuZ1W+YdhStrJ2thOr0fAkSiggAIKKFCCAA/y5kKQC/Fcf5pl1+YWjliWO/f62RHrt6qTkz8JBIIt8XIqEdQ6VRAzufWiuPg0JND1kTEKxkyAif07ZkdPfJ7I7Nc8A4d22pm2CW4RbPmI0Vtfd4UE06/rGOL9UX7Is91iM1PFAny22t3ns9een3uaz3baZikPc//V6Fj6DxR4fW2UrzG9NBnUj8T8myIvkSZtky+bSRuwcgUUUEABBRRQYGEBLqL7Lva4uF7qnCi9IFmYahXNPz4zCpy3drfKG8OBMROkY//m4q2db4/lfDawIcfs6In2aJu7Hv9T1E47TaZtMkG2WGRqCXBMuro1357kfesKbLXXc7pagWo6zuc67SyB7LRsqXkC/WnbL04LVjD/mTGGD4/cTi9sz6xpmi+UNY3HsSiggAIKKKCAAo1A8/MbLqKbsvYrF9Is6wtutdc/dpqL0rQOz8VSkePmCYikNfA+dznz/qfrb2X+CTHQJriFDxbtzN0V2JFj1dFT0xZ9oI125n0k0wfuntyv8brXJqCXCwwwKsx435g2K1CCAJ/bdj/YR9vzS09zHpD24dVpQeXz35j0n59K/k5StppZvqxWMxgHooACCiiggAIKXBTgwrfvLgUuEuc+D+JC/WL3yn6ptHe857mLp8+tdDxLd5sgCp8RMq7tTDCWABMXr+Sx+9q0RdsEmGmjnWmbzOf4b4/d+IL1sQ93HSf+hwX7ZdNlCPC5K6Mnl3vx3ZcnL03x2bw0s/DEEzPt/8NMWa1FXxgd/8uRm8Q+8g3NzBpf+VJY47gckwIKKKCAAksK2PZyAndE01zodp3jsOzlsQ4/rYqXWRMX5bM2uKHGuEsn954/GAY/HNk0rgBBJQItmJPZt5v85miKC1g+a+SYHT01bdGHH4vaaafJtE0mGPQbsayWRH+xzPWX8f5cboFlmxL4/gJH+08zfWJ/zRQvUnR3tPq2yGn68bSgwnnOY16U9Pt7Yv6PIq82dR0kVztgB6aAArWnIi68AAAQAElEQVQI2E8FFFBgbwEuAG/p2YrlnPt8ec86cy7ignvO9tba1lfHwAioxMupRBDjmlMlJyepeUkXWklXq5390Og5gSU+a2SMm/x3Yhl3TfE+kGN29NS0RdsfErXTTpPZJ8gcC9IHPMeqiyX6RH/TDtBvxpOWO79NgS/ODPv1mbK5i/g8pW2W8jB3+pU7L/h0FlSe/3H0/70iN4l/sPmmZmatr7kD5VrHur1xOWIFFFBAAQW2IfDPY5hdF4Cx6Hx6a/yfn0TFyyIp9zwKTjYX6czKGv3mzHi48CeIkllk0YIC3PXAXQNcg5AJzrQzQSU+y7x/5LG72rRF2/SDNtqZtskE2V4yduMd9dEe/UoX0y/6mZY7r0Bb4OPbMwtN575bS3qYOyy/zf+S/MvJfE2z/JGHFyQdfsXJyUlJgcOke+PMelAcx9FaFFBAAQUUUGAZAS40vyyazl0ARvFJc3HY/ldKyufOH5Bp8NpMmUX7CfD+5rbwHDenUn4ZfzWMwCPvH5nPdZN51hV3ehDYIU8xmqYt+vC8aIB2msy+RqYPfxbLxkjUR5tpXbTJ+NNy5ycWqLD6XPBoiWGwz7bbze3X7eVzTz8p0+BHZcpqKXp2dPTxkZt0T0y8LPLqkwfG1b/FDlABBRRQQIHVCnDxx4Vm1wA5oetb3rXdFOWlncxPMca56yR4mXN9zdwdsb1LAlNO/ERUzsU61y9k3vsmPyaWsT9wTEgvpGPRKKlpi7a5u4R22pm2CW7xEOVHD2iR9akzXZVy2kjLnVegZIHcPsu+XFKffzHTmT/MlJVedEN08GsitxPBq3e0C9Y6ndvR1jpWx6WAAgoooIAC6xDgZ0ZcOOYu/hhhs+xGZgrJaV/pY6ZrFg0U4A6YXHCSIMbf7alD9x6cihdx4cZPAtknuL7h89bOt8fYuJjm/SfH7OiJ9mibINvbo3baaTJtk9k/CbZRzvqx2qnEOozhVKEzClQqwD7+LQX1/WMzfXmfTFnpRTzH8+ZWJ3lI/be35lc9yUF21QN0cAoooIACGxNwuGsX4AKPC9WucXb9Nbqu9Zcq5wJ2qbbX0C53wKTjwLRv32B99h9ezdsSeEIMl8AQ1z5kLqzbmTun2H/IseroqWmLPnCxmWuAtglw5ZZZpkAjwH7STPPKvsVrCZlActqPr0gLFp7nTs60C12fyXS9EuYfG50ggBUvlxIPbr/v0tzKJziAr3yIDk8BBfYVcH0FFFCgQIEHok+cuHedrDfLeLBprFpU4idFaYe4kE3LnB8mkAtC8f4POa8lwDmsFdfakgB3TbH/kDnGtDOfX/Y59jHyVC60yV/TpI12pm0ywS2eAzZV+9arwLEC3F2Y1sF+XdJdWLnPEP0u6Y7t1LA9z1/dvb5VwEPbv7s1v/pJDtKrH+QCA7RJBRRQQAEFFBhPgIu3vsAUF3Yln9MYrBp3X+CCKK1x6PvfPvFP63BegZwAQSU+w+xjZPa/Jr85NuD41AScYnb01LRFH34sam/aal5pv525m4ygW6xqWqEA73vJw6rhLqzcz+34mfExrnNs+57RyLMit9MLY+bByJtJHIQ3M1gHqoACCiiggAJVCdwdveVknQu4mDyTmmW7fjZ2ZsOZC7r6P3M3qm+OC/Oc5bEn7zxTrXocB7CIwIdGqwSWuKYis3+2M/ssx6lYbbLUbo9p+sEdZbTbldsBL6bp58JBr8l81lZxLkBU0hi5myntD/tlWrbk/HOjcT4b8XIpXRtTz4hccnpBdK79j3m/H/P/IvKmEge4TQ3YwSqggAIKKKBAFQJcUPGXdro6y/Jaz2PSE+euMVp+WaDr/ebi+5rLqx00RQDioA2L2sjOlCjAZ73r4p1l7Txn/+lTO3MsNeg15ztweFu3HL7pbFvmgmwcq2frwICGvjKzzqsyZaUU/cXoyP8auZ24+6o9v4lpDlabGKiDVEABBRRQQIEqBP569JKLuq5zFJZ9XKzDxVa8jJcmqumdmXp9DlMGpaeoK3jFvmDwqQfORYsK8NPmruMUwSOOce1MWTuz37OPkxcdyMXG231jmr4zPvrXlQlatDNj8k6vi6AjvvzJiHWNUVUNd2H98xgo+2a8XErs179yaa6sia9PuvObMf8DkTeXOPBsbtAOWAEFFFBgEgErVeBYAS74franEk42OXd5Q886pS3K3UV27B1DpY1xyv5wwct7nrbBBXOuPF1vyDwXLUPWcx0FhgpwrOoKrg7d3wgOsY+T2aad74yO8NmgHT4LTY7iolK7z0wzFsbV9Df3ypjamXEa9Op/W3N/lbV/i+mX5v7xhvd1+paHt/DRmVWfmilbuuiDogNfELmd+Dlhe34z0xxENjNYB6pA+QL2UAEFFNikwJfEqDmx7brgi8UnXLD1LWedEjMXbSX2q4Y+ceGaO1flojdXXsOY7OO6BZ4Zw2P/7Prcd5XHZnslAhYEgjgm8lloMvV35T+OFvhMcaylj02O4qJS2n/Gxlib/uZeGVM7M06CXq8pamTTdQaz6Wo/rOZHZzajnyX9RcL/FH3MPby9tLukvyn62U6/HjP8QYd42V7igLCuUTsaBRRQQAEFFKhJgIuMV0SHObGNlzOJixKWccF2ZmEFBfS93U0uvtrzTucFuADNnafilyvP12KpAvMJ8McAvrejOfbb9FjQsepkxU+MmgkE7RP0+rPYhs8ix2HG0OQoLiph284cIxjrZ0Qvmz6nr4ypnRkn30exiWkkgdyzsL5ipLovV3Pc1BMym5+LspdELiF9RHTiMyO30z9rz2xtmg/31sbseBVQQAEFFFCgDAEuHrjI6OrNfbGAi614WU3iImo1g5loIFxI5s5RscuV79sN6mlvw4Vve95pBfYVYJ/t+muo7G9j7Lf79mnw+j0rPj6WcYzmOMwYmsxnpivXHvTi/erKfGe1M+/7EkEv+hdvzaXEe3FppqCJGp6FBVfurrDnsaCA/OKkD/8x5v+fyJtNHIQ2O3gHroACCiiggAKLCHCnAifgXSfdzbLrF+ndeI3mLmy4EByvhfXVxAVh7vyUfSJXXoKAfdi2AAGNrn1zi/utQa+TE953MvsGmec7bvVTkrsLC5OSPL4qOpP7vn5rlC+Z+IM1T086UNodbEn3pp/tOthO37ItKKCAAgoooMAWBThxTe5UOMVAcGst5ycGq069tTtnDF7tJHKFggQeiL4QpOgKxBO0WMuxLIY6aTok6FXDM73YN8h8F7CvtDPfhRzz+M6bFHfhyrvuwsrd9bRkVz8w0zg/L/zITPlcRS9NGuLOq5r+iE3S/XFmPaiO42gtCiigwLICtq5A+QL3Rxc5eedkPibPpGbZVWeW1FvQNdZ6RzRdz7mQy52Xsl/kyqfriTUrsFuA/fXqntX+YSzrC9THYtORArU/04vvB45t7Ccc55pMYIvMPvb7HUYs71hUZHHuLqzS7iR6S8j9SuQ0/XJaMNP8/xTtfELkdirlZ43tPs0+zYdm9kZtUIESBeyTAgoooMBkApxsX9NT+1buVOACpYdhs4u4UMudk+KVKz8WinqPrcPttyvA8axrv2TfIjDx/dvlKXrkh9zpNfczvdh/yOxjfzE02aeazL5HZnksqiZ13YVV2gA+OjqEdbxcSrwPP3Fpbr6J9C8P8hc13zhf8+W2xBsyVu+sRwEFFFBAAQUUaAu8M2Y4Gew62WbZZ8c6/At0vKwq/bvMaB7KlG29aO7gFd5cAPJqVmAfAQIZHLO6jmdd+/I+bbhueQL7Br3+QgyBf5RhXyHH7GiJfY+cu4bnuEam7fccrcX+ivZZmrsLi/7uU8cc6z4z08jfypRNWUR7H5U08MJkfrOzuZ1/sxgOXAEFFFBAAQVGE+Bi7qae2ljOeciP9qxT86KPz3S+7y60zOqrL2r2gXSgXPSxb6TlY80bSBxLcjv1EBR4XM9w+Yup/LW+nlVyiyxbocCfxJj4RxmOYWQCTu3McY9jHDlWHS01bfC8rdujVupvMoEi2l3yeVu13IX16rC7J3Ka7k4LJpxP7776V9HWb0U2hQAfqngxKaCAAgoooIACowh8StTCyXLXOQYn1Nx1tfaLPS4mgsLUIcDFVG4fYf/IleerOaz0+sxmS17YZbpjUUECHM8ICuS6xP7KZz23T+XWt0wBvvs4xpHZd5r8a0HDvsY+RY7Z0RJt0B6BNepuMu2ROR53PW9rtE5ERbXchXVj9DVNN0TBF0eeOn1uNPAhkdvJu69aGuzIrVknFVBAAQUUUGBLAiOPlbsU/k3UyclyvJxJnChz7rHWu67aA04NuGBoL9/yNBdL7AepAUa58nS9Kea7AhRTtGWddQj8YHSTfTL9LEfx+dQcz87P+D8FjhR4SmzPcYhjIJn9rsnvimXsb+yPMTlaauqnvdzztkZr6GJFXXdhfczF5SW9/GSmM9+dKRu76EVJhbT5B0nZpmfZWTcN4OAVUECBIwXcXAEFLghwcs3J94W5s/+/K4r6lsfiVaexLzxqxSoxeIUlF3K8mhVAgIDB32OiIz8Y5Vs+nsXwTTMKnIu22N+4dudYRY6iSVOuDb7Hjs25Tv9iFKb1ck7B5zAWLZI+PVqlD/FyKWGS+0uFl1Y4cuLbYvv3j9xOaUCrvWyT03wINjlwB12SgH1RQAEFFKhYgBNMTjw5scsNo1l2c27hSsswSYfGxUdatrX5UoNXvA9d+y/LzNsS4KKVn3nlRt0cz3yeXU7HsjkF2BfT9jiONfkbYiHHXNYjx2xViXHwOaTvfCa5w3vuAeTuDHtqdOKWyFOkZyeVfmvM8zyzeFlbOnw8BrAOt3NLBRRQQAEFti7ASSUnmF0OD8SCLZ5rGKyKNz5JXEjl9gUuTnLlyeajz9Lu6JVaYdUCHxy9Z7/gwjkmzySOd0vsq2c6YoECJ7sJvj5W4fuZfZbMft3kKZ+3Fc2Onug336t8PpvMd8roDSUV/mrM/3HkNL01LRhh/oeiDsYYL+cTx5uXnp/yf6cE2JlPFTijgAIKKKCAAgrsEOCvuHESyUllbtVm2bW5hRso63LZwNCzQ+RCI3fOyX6SK89WMnIhbY9cpdXVJJD0lYf4vzkpa89yV2X74rK9zGkFlhA45hi2xPO2xjaa67vjiZmO85POl2TKjyn6u8nGvxTzb4tsSgTmeuOTZp1VQAEFFFBAgUoF+FdBTt66us+FnucXp3WOudA4XVM5c0N7UmLwir7zDCNe2/mz2zNOb0aAfZS/ztY14I+LBX3HvFhsUmB2gdwxbIxOsK8TrM19j/NdNmZO+9uuO1225Pw3Zxp/Xqbs0CL+YATm7e2/oD3j9GWB3I55ealTCiiggAIKKDCRQHXV3hs95uSy6+6iZhknv7HqZtNvZUY+1YVGpqmiiggM5M412Vdy5XN2/rpMY1xEZIotWrEAAfmufZH9lOPdG1Y8fodWr8DnLNB1Pitj5twQmvr57L09VuBzGC+Lpq+J1vnHuXg5lcb4KeH1CNwWYAAAEABJREFUUWP6jyc81P53otyUEWAHyRRbpIACClQgYBcVUGAuAQIRuQv+pn0eruo5xQWND7rwcur/W/wpJftMbp/gYiRXfgpsoRkumBZq2mZnFuD5fOyLXe951/47czdtToFOgZ/OLHlTpqzkIj6D7f6ln8fHxkK+LyjnZ74xu1jK/ePcE6I3Hxn5mPS9sTFjjJdL6fMvTTlxRiDFOrOCBesWcHQKKKCAAgr0CNwRyzjB7DpfYNnLY52+n9/E4k0lTrQ3NeDMYLsu/tlfuvalTDUWKTCJAPvn1T018+wZHn7ds4qLFChSgD9EUGTHOjrFHZDpov+cFlycv+ri65IvHBvS9n85LdhjnruvPitZf/K7r5L2qpv1JKK6t8wOK6CAAgooMIsAF3m39LTEcs4jvrxnnS0uSgNYBG225NDsF+mYcWB/ScuXnKdP7fbT9669zOl1CHDB3LUfsj+wD/yVdQzVUUwkUHK17L8l9y/tWy5Q/BfTlQqa59jAcaLdJY4nP9Uu2GPau6/2wGpWBbyZ9lUBBRRQQAEFFHjPIOi7yIvFJ5zE5U48WWY+LZCe7J5euq65moJX65KvajSLdPYd0Sqfxa4L/K59NzYzKaDAjAJdn9EZu9Db1DMzSz8tUzakKP3Lg78QG/nsq0DoSwaw+nRcpoACCiigwLYEeJbV7THkrhNIAlssy91GH5ttPuUe8pr+ZaHjkcqsgbHnzisJGuTKSxgF+3MJ/bAP0wpwXLu5p4n7YpkB+UAwKbCAAN8RCzR7cJOvji3viZymu9OCHfP80ZD0/MC/PLgDjcWlnlDQN7MCCiiggAKTCFhpVoCL+fRkqr0iJ2x9y9vrbnV6qz78hcpcAIALk5LPNXN9XvpBwVv97Ew17r7jGvsnAXmeQzNV+9arwJQC7MPt+tmf2/M1TOe+I7gjMu17rixdZ675GzMN3RBl/zry0JQ++8q7rwbK5XaYgZu6mgIKbFjAoSugwHoEuGDnJLjrxLdZljthW4/COCPpMhyn9jJreW50q+svVNZ4nrnVIGS8jatKXx2jaY5dMXkmEdiqcf88MxALFFihQPpdSvCqtM9r7rlX/3O8F0OCWD8U66XfNd59FShDUmk7wpA+r2Qdh6GAAgoooMDiAlzE9f0FwYeih54rBMKBiQvoAzetZjP+CmWus6/PFVZQll44VdBlu5gI8HPWb07K2rMPxkx68RhFJgWqE1jLd0w6jvQ4nDsPSbeZ+837W9Egx5J4OZUIYr32VMmlmUsTPvvqEsX+E7mdYf9a3EIBBRRQQAEFahJ4IDrLyV96khjF51Oz7Orzc/7vUAEChIduW8N2XePjrr5PrmEAmT52fSYyq1pUmAABd/bJ3E9D6WpzXLuGGXOFAnY5FXhLWlDpPJ/btOt/fLEgt4zPcglxDI4ld13sZ/vlaTHzk5FzyWdf5VT2KCvhjd+ju66qgAIKKKCAAkcKcDLYF5jigceeH+yPzF0f6VZdF9LpejXOsx/lgj381OOqigbEhVBF3T2uqyvdmn2O9/FcjC+3T0bxCcs9riFhXpPA+61kMLnvyve8OLbcZ7qkzzJ/IOLtF/vafvmbMfN9kdPks69SkT3nS3rz9+y6qyuggAIKKKDAHgL8hRwu4nIng1TTLOv7SSHrbTn3jX1LP0kiYJDbj9iHchcifW5LL6PPS/fB9vcX4C8HEkTl/dt1PUNwedc6+/fALRQoUyB3R1CZPe3vFd8xfMbTtfijIWnZ0vOPjQ7kglhfGOX8oyDHIDLfnem5gs++CqR9kgfzfbRcVwEFFFDgSAE3X0iAkyb+Qk5X8yz3nKBLZ1g5J9vD1qx7LU7Cc/vKkEBCiSPPPcPkw0rsqH06L8Cxin3t2pgb8pljHe7MitVNCmxCoNa/qsnnOn2D+PymZX3nMum6c84TxHpbpkECVvzDDjn97vQvD2bAdhWliLvWd7kCCiwtYPsKKKDAcIH/K1blpLDr+55lr4h1OLGKF9OIAtiOWF0RVfEv3137Stc+VkTHezqR+wuK/1/P+i6aX+APo0nuxOAzNXQ/466H3MVvVGVSYNUCte73Qz7bfAeV/ObdEp37o8hDk3dfDZVqrTdkR2mtvo5JR6GAAgoooMAGBLiA+8yecXJByHnAl/as46LDBfA9fOvytnxBdCkX7Inik1ovmOh7Lq9tPLkx1lDGMYyg1ftEZ4e8J6z7Ty+u60+hA8KkQGUCfIb7unzw3Vd9lY687L2jvt+LvCv9+1jhdyKb9hTgxHXPTVxdAQUUUEABBQoW+BvRN4In3LYek9n0xijtWx6LTXsI8LO6dPWuO5XS9WqZ/8aOjnIS3rHIYgX2FvjK2ILjFxeyQ49R/KyQABfXNf9bbG8aX8AayxXgs9LuHZ+F9nxN03yG0/HU1P+mrx8YEzys/V/G63dl8lOi7L+PbDpAgJ3kgM3cRAEFFFBAAQUKFCCQ8rroV9cJLBeGLPvIWMc0nsDQC+3xWpy3JvabXIsPR+EaTsLTCyY+IzG0MZN17RDg2MX78C2x3hB/1iVYxbprCxYHgUmBzQoQn+DznQLkytJ1SprnEQ7PjA49K5N/LcpMBwqwgxy4qZspoIACCiigQEECBBn6LuT4q131BloKgs50hYvoTPEqitivcuPjrperVjFCB7GUwCdFw+xfXJj2HbtitUuJ9dkfuYbh54KXFjihgAKrEeDzzT+QcGwg3xMjoyxeTFsXcEfY+h7g+BVQYDMCDnS1As1JHhd1uUFy8seyWv8yUW5MpZdhXnofh/SPIBX7Trou4xsacEi3LXGeoEiJ/VprnzhmYf76GGBu/4riU4n9jQA86xqEP0XjjAInfJ7WyMA/kBCrIN+4xgE6psME2CEO29KtFNiegCNWQAEFShPgIrDvYcWc2PpdP/+7xvsyf6vjtshPunL7DsGEXPm4rc9bWy4Y99COLhDcw4LM+33njvVdfHKCE14cswhG7TJhfdZjfzMAv0vL5VsV+NGtDtxxb1OAL4QZR25TCiiggAIKKDCCwP1RBxeCXNzF5JnULONfMM8stGBUAQI9aYW5gEi6Tsnz90bnusawlXPHrvEHzfnUduBz+NgoJeBiICsgWolAIC7NMam1KDvJeg/GEky92yogTArsEPj8zHLubswUL1FkmwqMK9D+8h23ZmtTQAEFFFBAgSkEuPPjmp6KH4llfr8HwkxpjRfZ13XYEVToWLS64kPGyjYEsviMrg5kzwFhQDDqXGyHS7z0JtZlPY5dfce33kpWudBBKbC/wMftv4lbKFCHAF8SdfTUXiqggAIKKLBtAe704SKv67ubZfzpZn6es22peUfPRfe8LU7bGvtRroXPyRXWUHZgH495X/mMEsA5sOlqN+M5Vc3dVhjsGgj7Gsc1rIesv6s+lyugwAUBzwMuOPj/FQr4ZbHCN9UhKaCAAgqsSuDuGA0Xen0/aeJime/034t1x07Wt58A79V+W5SzNsGHXG94ltqP5BasqGyf943P266h83kcst6uempYzjjxuzY6SzAqXnoT67IeRtyh1buyCxVQQAEFFGgE+OJopn1VQAEFFJhEwEoVOFiAgMINPVs3F4J9wa2ezV00gQDv2QTVTl4l/SaokDZEcMJnqZ1WGXr+zHr4nd56HXN/GMNgn+EYxDhjdmfi583sY0PX31mhKyiggAIKbEvAL5Btvd/1jtaeK6CAAtsS4KKXC0Mu9rpGzk9v/B7v0lmuvMZgIvtbbl8jQFHjeA559zE4ZDu2aT6rvDLfznxGcTym/nZ9S08ThGKc7xMdye0zUXwqsS7rkf1Z0ykaZxQYTYDPWbsyPm/teacVqE+go8d8qXYsslgBBRRQQAEFZhYgKMWJaN/3MxfDnJz605uZ35yVNsc+l9vf2A/X+ID6rrcxF1zhp5Pp+rlA1JsuroQjbhdnL73weW2WsZzP8AOXlpY/8W+ii/SZvg/dJ3Bqxh2bmxRQYG4B21NgjQJ8ma5xXI5JAQUUUECBmgTujc5ycdh3twvLuSAcegEZVZoU6BVgv+va5zxHPDnJfdZyLk9uKbOcz2qr6Mzke0TJ1ZFZj8AQwZ6YLS4R3KSPnxI9o8/x0ptYl/XIXftVbwWFLbQ7CtQiwGevlr7aTwWOEuBL9qgK3FgBBRRQQAEFDhb4gNiSC9jr4rUrcWL67Fjod3YgmEYT+LCoqWu/IwARi49N1W8/xIHPZzpQPqu58nQ95mmjWZ9tOB4sfXcWfaAvQ4NQrN+MgzGZFVBgXoF3zNucrSmwnABfmMu1bssKKKCAAgpsV4C7Ln43hs+FX7xk0skJP2Hiu/o7s0stVOBwgV/v2PQzOsq3WNz32Ww8+Hw20+1Xyu+JAgJB8TI40WZzdxbbkgkQtTPHDu6OGlzpgBU51tAG7dGHXZuw3n2xEuvm7lSLRSYFFJhJ4JaZ2rEZBRYX4Mt18U7YAQUUUGAqAetVoEABLjy5+Ov7DuYClQtD//pbgW/gCrpEoCI3jIei8McibzXxuRxz7DdGZXzO+SyT+Vwf0gbbtjN1cncUdXVl3uN2pm2OPb8SfWon1qEOngFGG+1luWnWZz36cH1uBcsUUKAIgT8pohd2QoGRBfjyGblKq1uZgMNRQAEFFBhHgLsVuFDkwrOrRpZzcdi3Tte2liswRIBABvtYui7l3PmTljs/ngCfa8698T/k7qx9ekIb7Uy7tP/UqITjTJNZJ4p6E+s+GGuwrndbBYRJgQoEHldBH+1imQJF94ovs6I7aOcUUEABBRSoXOBjo//ctXBtvHYlLhD/QSz0ezkQTJMI3BW1sp/l9jH2T4IbscqmE0G8uQDGujtryv6yvxC0Yp+5ZsqGrFuBdQkUMRo+u0V0xE4oMKYAX0hj1mddCiiggAIKKHBZgMDAG2K270SSn23xffwDsZ5JgSkECMwQMMnVTZDCu2ouyPAzugtTl//PZ/jy3HRTBBA5DnCs4Kd590dTvG+0z3vU5CieNNEOPzWkH/Rn0sY6K3eBAgoooIACGQG/mDIoFimggAIKKHCkwCOxPReCXATGZDZxccpyf7aV5bHwGIGL2/bddcUq7KOeCyLRnfmMdi+dZgk/N74uqiaoRXCR96jJ9KcrHxP0Yl+gXto5F22bFFCgLgE+w+0e83luz48x/cIxKrEOBY4R4EvqmO3dVgEFFFBAgTUKHDqmB2JDTiK56IzJbGI5J5ZcnGZXsFCBEQQIkHbddUX13NnjeSASpzN/je90yckJVmlZifOHBL2826rEd9I+KVCmwH9XZrfs1ZYEPHHZ0rvtWBWYVcDGFNiUwNNitFzk9t1NReDq6bGe372BYJpMYMhdV38pWu8LssbizabcX/4k4ExAcI0o3m21xnfVMSkwjcD7TlOttSowXMCT6OFW869piwoooIACNQgQuHptdJSL3HjJJv6CF9+5r8sutVCBcQQIsgy56+p3xmlutbXk7sLi80sQerWDdmAKKFC1AOciUw/gsRvwBTkAABAASURBVFM3sPn6BdgpwJfxzpVcQQEFFFBAAQXOCOzznCv/gtcZPgtGFBhy19UHR3vedRUIAxJ3YRmsGgDlKgqUJrDh/rxxhrF7LjMDsk30CxjA6vdxqQIKKKCAAqmAz7lKRZxfUmDoXVe/tWQnK2ybc+QtBrEqfKvssgIKhMBHR07Tm9OCI+evPHJ7N1fgaAG+nI+uxAoUUEABBRTYgMAXxhi5RX/Xc64+Idbz+zUQtplmG7V3XU1Pzee4L4jVt2z63tmCAgoo0C/wpP7Fey/1Lt69ydxgbAG+mMeu0/oUUEABBRQ4XKDMLQlcfV90re85V9yZxffq/xvrmRSYUsC7rqbUPV03n+nm58IErJrMc7JYdnpt5xRQQIFyBPrOWQ7p5dj1HdIHt9m4gF+8G98BHP46BRyVAgqMJtBcuPadtLEOy68drVUrUiAvMOSuq9fHpv4reSCMmPjZDOfM7cxzskZswqoUUECB0QU4Nxm9UitUYEkBvoiXbL/Utu2XAgoooMC2BR6K4XOnRV8ggLuyODnk4jZWNykwqcDQu64+edJeWLkCCiigQKkCnLeU2rfS+2X/KhEwgFXJG2U3FVBAAQVmEfjtaIXA1Ll47UqcID4nFvYFt2KxSYFRBLzrahRGK1FAgWkFrF0BBRSYXsAA1vTGtqCAAgooUIcAgasPiq5yV1W8ZNN9Ucp35yvj1aTA1ALedTW1cEn12xcFFFDgOIG7j9vcrRUoX4CT8PJ7aQ8VUEABBRSYToAgAXdV9QWumudcXT9dN6z5WIEVbe9dVyt6Mx2KAgooMJPAzZl2bsuUWaRAtQIGsKp96+y4AgooMLrA1ipsnnPV913IXVkEtnzO1db2juXGS0D1xp7m2SfZZ33WVQ+SixRQQAEFzgvcev7//k+BlQhwArSSoTgMBUoQsA8KKFCBwOdFHwkC7HrO1aNjPZ9zFQimvQQIQO21wcWVh9x1RTDVffIimC8KKKCAAjsF+N7YuVLHCtyd3l50TF3tepxW4GCB8gJYBw/FDRVQQAEFFNgpQODqX8VafSdh98Ryvh/fGa8mBfYVYN/ZN4jF+kPuutq3L66vgAIKKLBtgb7znTJk7IUCewhwkrXH6q6qgAIKKKBAlQIECPiXxL4TueY5V32BhCoHb6cnFWC/Shvg/Ip9Li1P573rKhVxXgEF9hZwAwVaArnvpNZiJxWoW4ATrLpHYO8VUEABBRToFng4FnEy1/d9x11ZBLZ8zlVgmfYWYN9iH0s3bMpZ1pX7gqXsl9SR1uv8+ALWqIACCiiggAIVCHhiVMGbZBcVUEABBfYWeHtsQQCgLyhFUIHAlc8UCqzj0ua35nyK/WkMCOp5fVTkfhkIJgUUUECBvQTu3mttV1agMgFOuCrrst1VQAEFVijgkMYUIHDFA9gJTuXqJUDAT7f8DszpWHaoAPsT+9ah27Md+y71+BcG0TAroIACCuwrcHNmgzdnyixSoEoBTpKq7LidViAVcF4BBTYvwDOHCCB0Ba4A4jlXfPflTvBYblbgGAH2LfbBfetgG/Zb77raV871FVBAAQV2CTxp1wouV6AWAU60mr76qoACCiigQI0C74pOEwDo+07jzhYCBH0/KYxqTAocLcB+SKCUfXJI5jltbHN0w1aggAIKKKBARoDzn0zxiWUKVCfgCVN1b5kdVkABBRS4KPDOeCUwdUW8diUCCJy4eWdLl5DlUwgQKOUca0i+aooOWKcCCswhYBsKVCHAedAhHeUc65Dt3EaByQQ4sZqscitWQAEFFFBgIgFOqm6KurtOyghcvSOW+z0XCCYFihWwYwoooIACYwtwDjRGnTyaYYx6rEOB0QQ8sR+N0ooUUEABBWYQ4GSKE7OuwBVd4CeFfL89hpm1Z8engAIKKKCAAgpMIPDgBHVapQJHCXCCf1QFbqyAAgpULmD36xAgKEXgqu97i7uyCGydq2NI9lIBBRRQQAEFFBhd4O6Rarx3pHqsRoHRBPouBEZrxIrWLuD4FFBAgckEOAkjcOVzriYjtmIFFFBAAQUUWJFA7i8t33bA+G4/YBs32YTAcoM0gLWcvS0roIACCnQLfEks4o6qG+K1KxHY+uNY6HdZIJgUUEABBRRQoBKB+bt56wFN/voB27iJApMKeNI/Ka+VK6CAAgocIMBzrl4R2/FzwHjJpoejlO+wJ8arSQEFFFBgYwIOVwEF9hLoO6fqqui1XQssV2ApAU7+l2rbdhVQQAEFFGgL7POcq6vaGzqtgAJ7C7iBAgoooMB2BA4JYL1mOzyOtBYBA1i1vFP2UwEFFFivAA8J5eeAlT3nar1viCNTQAEFFFBAgaoFOK+aYgAvmaJS61RgqIABrKFSrqeAAuMLWOPWBZrnXF3XA8EJGM9g8PuqB8lFCiiggAIKKKDADAJ/dYY2bEKBTgEvCDpp6lhgLxVQQIFKBfZ5ztWTKx2j3VZAAQUUUEABBZYQ4K84T9Hue09RqXUOF9j6mgawtr4HOH4FFFBgXoEhz7kiuMWzGnzO1bzvja0poIACCiiwdoGtjO/mzEBvy5TtW/SYfTdwfQXGFDCANaamdSmggAIKdAncFwv4OeCQ51z1rRPVmBRQQAEFlhOwZQUUqFTg1hH67T8ujoBoFYcLGMA63M4tFVBAAQV2C7w8VvnzyNdG7koEtnzOVZeO5esTcEQKKKCAAgrML8Dd7ce2eu7YCtxegWMEDGAdo+e2CiiggAJ9AvwU8LmxQt8J04OxnO+ivZ5zFduYFFBAAQUUUEABBYYL9J2PDa2Fc7ah67qeAqMLuAOOTmqFClQhYCcVmFLgkaicu6r6vmMIbnEidU2sa1JAAQUUUEABBRQYV4BzsXFrPDnh3O3E/xRYSqDv4mKpPlXSrt1UQAEFFEgEmudcPSopb89yMsXJj8+5aqs4rYACCiiggAIKKFCwgF0rQcAAVgnvgn1QQAEF6hZ4bXR/yHOuXhfr+b0TCCYFFFBAAQU2J+CA5xa4e+4GbU+BqQW8kJha2PoVUECBdQsQuHpaDJG7quIlm+6PUr5vnh6vJgUUUECBAwXcTAEFFNhD4ObMurdlyixSoBoBLiiq6awdVUABBRQoRqB5zlVf4Kp5ztV1xfTajmxdwPEroIACCiiwZYFbtzx4x16/gAGs+t9DR6CAAgrMKHDyQDTGc6z6nnPFXVkEtnzOVWCZFFBAAQUUUECBQgQ4PyukK3ZDgf0FDGDtb+YWChwn4NYK1Cnwb6PbBKaujteuRGDr22JhX3ArFpsUUEABBRRQQAEFFhAwgLUAuk2OJ1BlAGu84VuTAgoooMAAAQJXnxjr9Z30NM+5+rJYz6SAAgoooIACCiiwvAD/uHhML9Lt+84Fj2mnd1sXKtAIGMBqJHxVQAEFFEgFhjzninU4mfE5V6me8woooIACCpQhYC8UUECBVQgYwFrF2+ggFFBAgVEFHora+Be3vp8CclcWgasrY12TAgoosHIBh6eAAgpUKXB3lb220wp0CBjA6oCxWAEFFNigwG/HmAlMnYvXrkRg6zmxsC+4FYtNCiQCziqggAIKKKDA3AI3Zxq8LVNmkQJVCBjAquJtspMKKKDAycnEBgSuPija4K6qeMmm+6KU741XxqtJAQUUUEABBRRQoD6BW+vrsj1W4IIAFyIXpvy/AusXcIQKKHBW4N1RxF1VfYGr5jlX18e6JgUUUEABBRRQQIF6BfrO+eodlT3fhMCeAaxNmDhIBRRQYAsCzXOu+r4HuCuLkxyfc7WFPcIxKqCAAgoooMAWBDi3GzhOV1OgLIG+C5eyempvFFBAAQXGEHhrVEJgyudcBYRJAQUUUECBSQWsXIHlBbjTfvle2AMFRhAwgDUColUooIAClQgQuHpC9LXvX97uieV8N/icq4AwKaDA8gL2QAEFFFBgMQHOHRdr3IYVSAW4SEnLnFdAAQUUWJfAPs+5unFdQ3c0JycnIiiggAIKKKDAdgXuPmLonEMesbmbKjCugAGscT2tTQEFVilQ7aAejp5z23jfsZ5/WeOOLJ9zFVgmBRRQQAEFFFBgZQI3Z8ZzW6YsV/RgrtAyBZYS6LuoWapPtrtGAcekgAJzCrw9GiMw1ReUIrBF4OpRsa5JAQUUUEABBRRQYDsCtw4c6r0D13M1BU4LTDRnAGsiWKtVQAEFFhIgcPXoaJvgVLycSQSu7opSj/+BYFJAAQUUUEABBUoUmLhPXeeJabO3pwXOK7CkgBcwS+rbtgIKKDCeAM8oIDjVd0LySDTHcT93K3ksMimggAIKKLAaAQeigALdAn3ni+2thv7UsL2N0wpMJsCFzGSVW7ECCiigwOQC74oWCFz1Hc+5K4sTlb6fFEY1JgUUUKAt4LQCCiigwEoEOFc8ZCg/cchGbqPAVAJ9FzxTtWm9CiiggALHC7wzqiAwdUW8diVOVghc+ZyrLqGpy61fAQUUUEABBRSoV+A19Xbdnq9RwADWGt9Vx6TAigQcSlaAwNVNsYTgVLycSQSu3hGlHuMDwaSAAgoooIACCmxc4O4Rx/+SEeuyKgX2EvDiZi+uKle20woosA4BTjwIXBGc6gpcMVJ+Usix/THMmBVQQAEFFFBAAQU2L5B7/umbD1T5qAO3c7N5BFbdChc5qx6gg1NAAQUqF3gw+k/Q6oZ47QtcEdxi+blYz6SAAgoooIACCihwkMBmNnrSgSN9/wO3czMFjhYwgHU0oRUooIACkwhwJxWBq6t21M46BK58ztUOKBcroIACCswkYDMKKFCDAOePh/QzdzfXIfW4jQJ7CxjA2pvMDRRQQIFJBd4dtROU6ns4e6xywjqceHgcR8OswMoEHI4CCiiggAITC3AeeUgT1xyykdsoMIaAFz5jKFqHAgoocLxAE7gaclxm3SHrHd+remuw5woooIACCiiggAKXBfjHz8tzh0/5uIrD7dzySAEvgI4EdHMF1ivgyGYS4NlVnFAMOR4/En3iX8t23Z0Vq5kUUEABBRRQQAEFFBhdYMg56+iNWqECCLjzoTBVtl4FFFAgL/DaKG4CVwSkYrY3PRRLWe/KeDUpoIACCiiggAIKKLCvAH/Ret9tcutzTport0yByQUMYE1ObAMKKKDAJYG7YorA1dPiddeXP3dl3XNxvavj1aSAAgoooIACCqxawMFNKpB7+Pptk7Zo5QqMLGAAa2RQq1NAAQUyAg9EGYGrG+N1SODq12M9js+sH5MmBRRQQAEFBgm4kgIKKLCPwK37rOy6CiwtwAXS0n2wfQUUUGCtAg/HwLiTijuohgSunhPrc1x+cryaFFBgEQEbVUABBRRQYDMCu85PNwPhQOsQ4EKpjp7aSwUUUKAeAR62TuBqyDOruDOLkweOx6+sZ4g9PXWRAgoooIACCiigQA0CnIPW0E/7qMB5AS6Yzk/4PwUUKEfAnlQr8O7oOYGrR8XrrtQEroasu6sulyuggAIKKKCAAgoosEuA89Rd67hcgWIF1hrQkOQrAAAQAElEQVTAKhbcjimgwCoFCEZxQjDkmMrdWfxrl4GrVe4KDkoBBRRQQAEFFFBgZgGb24jAkIutjVA4TAUUUGAvgZfH2k3gioBUzPYmnofFekN+VthbkQsVUEABBRRQQIFxBaxtIwJ3HzBO/pG2vRnns+15pxWYTcAA1mzUNqSAAisRuCPGQeDqufG66wucL/z7Lq53VbyaFFBAAQXWKuC4FFBAgfIFbs508bZMmUUKFClgAKvIt8VOKaBAgQL3Rp8IXN0Sr0MCV3fGehxjr49XkwIKDBBwFQUUUEABBRSYXeDW2Vu0QQUOFODi6sBN3UwBBRTYhAA//eNOqutitEMCV8+J9Ti2Pi5e5062p4ACCiiggAIKKKDAPgK7zm/3qct1FZhUgIusSRuwcgXqErC3ClwSeFdMEbga8swq1uPLn2PqK2M7kwIKKKCAAgoooIACNQhwDltDP+2jAidcbI3LYG0KKKBA3QLvju4TkLoiXnclflLIl77H0l1SLldAAQUUUEABBRQoQYDz3PH6YU0KzCjgRdeM2DalgAJFCxCM4gt9yHGRIBeBq0cVPSI7p4ACCiiggALFC9hBBRRQQIFhAkMu1IbV5FoKKKBAfQJfEl1uAlcEpGK2N/GzQtYbcndWb0UuVEABBRQYTcCKFFBAAQWGC9w9fFXXVKAsAQNYZb0f9kYBBeYReGs0Q+DqFfFKQCpeOhN3ZT0QS1nvXLyaFFihgENSQAEFFFBAgY0I3JwZ522ZsqaIc+Zm2lcFFhUwgLUov40roMDMAvyLE1/CT4h2CUjFS2cicPWOWMpx8tp47U8uVUABBRRQQAEFFFCgToFbe7rNozN6FrtIgfkEuDCbrzVbUqBHwEUKTCjwYNRNQOqGeB0SuHpdrMfx8THxalJAAQUUUEABBRRQYM0CfefHnEeveeyObSGBQ5rlAu2Q7dxGAQUUqEGAZ1YRuLpqQGdZjy9vjotPH7C+qyiggAIKKKCAAgoosJTAmO1yDtxV371dCyxXYG4BLtTmbtP2FFBAgakFHokGCEgNedg6PynkS9vjYaCZFFBAAQUU2I6AI1VgswKcJw8d/O1DV3Q9BaYW8IJtamHrV0CBOQX4jT5fyI8a0CjrErgasu6A6lxFAQUU2KCAQ1ZAAQUUWLtA3wPe1z52x1eYgAGswt4Qu6OAAgcJcBcVgashxzTuziJwNeTurIM640YK7CPgugoooIACCiigwMwCd+3R3uftsa6rKjCpwJCLvUk7YOUKKKDAgQLfH9sRuCJwRUAqZnvTw7GU9a6MV5MCCiiggAIKKKCAAlsVeHRm4H+aKbNIgaIEDGAV9XYs1RnbVaAqAf7FiMDVF0SvCUjFS2ciuHVPLGW9IQ9yj1VNCiiggAIKKKCAAgpsTuBxe4z4JXus66rFCdTbIQNY9b539lyBrQk8EAMmcHVjvBKQipfORODq12IpxzjWj0mTAgoooIACCiiggAIjCKyzil3n1+1R/9X2jNMKzCXAxd1cbdmOAgoocIgAP/0jIHV1bLzri5X1nhPrcWx7SryaFFBAAQUUUKBAAbukgAKLC3DefGgn3vvQDd1OgWMEuMg7Znu3VUABBaYS4GHrfLEOeWYVd2YR3OKY9sqpOmS9CiigQEECdkUBBRRQQIFjBN6U2fizM2W5osfkCi1TYGoBLvambsP6FVBAgX0E3h0rE7h6VLzuSk3gasi6u+py+eYEHLACCiiggAIKKLBZgSdnRv6DmbJckc+WzalYNrmAAazJiW1AgRULjDs0glEEroYcl7g7izuuDFyN+x5YmwIKKKCAAgoooMB2BTi/HjL6c0NWch0FxhYYcqE4dpvW1xJwUoGNC7w8xt8EroZ8YfI8LNYb8rPCqNqkgAIKKKCAAgoooIACHQL843F7EefZ7fmuaeMIXTI7yl18nIA73nF+bq2AAocJ3BGbEbh6brzu+qLki/W+i+t5u3JAmBRQQAEFFFBAgY0KOOxxBTjPPqTGXefvh9TpNgrsFDCAtZPIFRRQYESBe6MuAle3xOuuLz6+UO+M9ThOXR+vJgUUUEABBRQ4WsAKFFBAgUsCn3tp6vLET12edEqBsgS4MCyrR/ZGAQXWKMBP/whIXReDGxK4ek6sx/HpcfFqUkABBcoSsDcKKKCAAgqsQ+BHMsP41EyZRQoUIcAFYhEdsRMKKLBKgXfFqAhcDXlmFesR3OK49MrYzrRiAYemgAIKKKCAAgooUKQA5+NFdsxOKcCFogoKKFCfQOk9fnd0kIDUFfG6K/GTQr4oPR7tknK5AgoooIACCiiggALjCnDOPm6N1qbARAIbvmCcSNRqFdi2AMEovgSHHFsIchG4etS2yRy9AgoooIACCiiggAKLCdyzWMuzNmxjaxAYcpG5hnE6BgUUmE7gS6LqJnBFQCpmexM/K2S9IXdn9VbkQgUUUEABBRRQQIGZBGxmrQI3ZQb2YKbMIgUWFzCAtfhbYAcUqFbgrdFzAleviFcCUvHSmbgr64FYynrn4tWkgAIKKKDA5gQcsAIKKFCJQHq+zrl8u+uc07fnnVZgFgEDWLMw24gCqxJ4Z4yGwNUT4nXXlxdfdqzDsebaWN+kgAIKHCPgtgoooIACCigwvgDn7O1aOX9vzzutQBECXFQW0RE7oYACxQu8NnpI4IrbjHd9qfEl+LpY32NMIJSV7I0CCiiggAIKKKCAAgooUJ+AF5f1vWf2eGmBbbZP4OppMfQhgSvW4djy9FjfpIACCiiggAIKKKCAAmUL/ECmezwmJFNskQLLCXCROXvrNqiAAtUIELjibiqCUn2dZj3W8ZjSp+QyBRRQQAEFFFBAAQXKE/j8TJeelSk7qMiNFBhLwIvNsSStR4F1Cbw7hrNP4OpRsb5JAQUUUEABBRRQYHwBa1RgCQH+cXqJdm1TgU4BA1idNC5QYJMCj8SoCVztOjawDl9qBq4CzKSAAgooULqA/VNAAQUU2CHA+X17Fc712/NOK7C4wK6L1MU7aAcUUGAWgYejFb60dgWkWOfbYl2PHYFgUmBTAg5WAQUUUEABBdYswD9kr3l8jm0FAl6EruBNdAgKHCFwf2xLUOrKeO1LrHNnrMAx48vi1XSAgJsooIACCiiggAIKKFCowFMy/eL8n2Ked8urWYFFBbgYXbQDNq7AHgKuOp7AO6MqvoiuidddiSAXx4rH7VrR5QoooIACCiiggAIKKFClwG9mev2Yi2U8H/fipC8KzCZwpiEuSs8UWqCAAqsVeG2MjMDVTfG663ft77q4znXxalJAAQUUUEABBRRQQIGqBI7ubHO98ODRNVmBAiMIGMAaAdEqFKhEgMDV06KvzRdRTGYT/8LCOueySy1UQAEFFFBAAQW2IuA4FdiWAI8NyY343lyhZQrMLWAAa25x21NgfgECUnwZEZTqa50AF+tc0beSyxRQQAEFFNhHwHUVUEABBaoReFOmp58dZbdHNimwuIABrMXfAjugwGQCTeBq1+e8CVzt+guEk3XUihVQoFfAhQoooIACCiigwBwCT8408oNRdltkkwKLC+y6sF28g3ZAAQX2FuBP4HLH1a7PN+twx9UGAld7G7qBAgoooIACCiiggAIKnJxwvfATJ/6nQAECuy5wC+iiXShCwE7UIPBwdJKg1K6AFOu8INb18x8IJgUUUEABBRRQQAEFFLgkwLXCpZmYIID1mng1bUmg0LF6AVvoG2O3FNhD4P5Yly+aK+O1L7HOnbECn/sXx6tJAQUUUEABBRRQQAEFJhCouEquGSruvl1fswAXsmsen2NTYM0CPOOK51ddM2CQBLn4vD9uwLquooACCiiggAIKLC1g+woosIxA7tccv7BMV2xVgdMCXNCeLnFOAQVKFXh5dIygFf8qQubzyy29UdyZ3hVLWOe6eDUpoIACCmxKwMEqoIACCigwisDHjFKLlShwpAAXwEdW4eYKKDChwF1RN3dZEbB6bkwP/cwS6CJwdS62MSmgwKECbqeAAgoooIACCijAdYUKCiwuMPRiePGO2gEFNiTwUIy1CVrdGNP7fGGwHetfEdsVkeyEAgoooIACCiiggAIKVCXAP55X1WE7uw0BA1jlv8/2cBsC3DFF8IkvC+6aIgg1dORsc1+szDa536zHIpMCCiiggAIKKKCAAgooMEjg7kFrudIUAtbZI2AAqwfHRQpMKHDI86za3SFo9booIGjF5/j6mDYpoIACCiiggAIKKLBxAYc/gsDNI9RhFQqMLsCF7+iVWqECCmQFDn2eFZURsOIOLQJWZD67T2eBWQEFFFBAAQUUGFXAyhRQQAEFFChQgIvgArtllxRYjcAxz7MiaNX8FUE+q/48cDW7hQNRQIG1Czg+BRRQQAEFKhfgWqTyIdj9tQlwUby2MTkeBZYUeH40zp1SZA76xzzPis8n20eVJgU2J+CAFVBAAQUUUEABBRRQQIFLAlwgX5pxQgEF9hZ4e2zRfgD7i2Ken/iRY3JQItA1wfOsBrXtSgoooIACCiiggAIKKKBAKvCytMB5BZYWMIDV9w64TIGzAukdVo+OVfgc7Ruw4g4ttiGzvc+zCkiTAgoooIACCiiggAIKFCHwvCJ6MWcnbKt4AS6ci++kHVRgQYEx7rCi+9xl5fOskDAroIACCiiggAIKrFLAQSmggAJTChjAmlLXumsW4GeBBJ0OucOKcbNtepeVz7NCxqyAAgoooIACXQKWK6CAAiUJcE1TUn/sy8YFDGBtfAdw+J0C+342OLgTsLozamx+FuhfDQwMkwIKKDCvgK0poIACCiigwEgCj4xUj9UoMIrAvhfpozRqJQqsQKAJWBGsIvNZImD1uBWMzSFsXcDxK6CAAgoooIACCihwcuIvSNwLihLgoruoDtkZBQoRIEDV7grz3GFFsIrMZ+dR7RXa004roIACCiiggAIKKKCAAgoooMB4AlyEj1fbeDVZkwJLC/DZ4JbZu6MjBqwCwaSAAgoooIACCiiggAIKTCBglQoMEuAifdCKrqTABgWujDHfFNmkgAIKKKCAAgoooEDBAnZNgckE+CXKZJVbsQL7CBjA2kfLdRVQQAEFFFBAAQXWKeCoFFBAAQVyAg/lCi1TYAkBA1hLqNumAgoooIACKxRwSAoooIACCiiwOoGPWN2IHFC1Agawqn3r7LgCCqxQwCEpoIACCiiggAIKKFCSwG0ldca+bFvAANa23/8Vjt4hKaCAAgoooIACCiiggAIKKKDA2gTOBrDWNkLHo4ACCiiggAIKKKCAAgoooIACZwWGlfgg92FOrjWxgAGsiYGtXgEFFFBAAQUUUEABBdYr4MgU2ICAD3LfwJtcwxANYNXwLtlHBRRQQAEFFFBgvQKOTAEFFFCgbIFryu6evduKgAGsrbzTjlMBBRRQYMUCDk0BBRRQQAEFFFBAgXULGMBa9/vr6BRQYKiA6ymggAIKKKCAAgoooIACChQrYACr2Lemvo7ZYwUUUEABBgAmvQAAA3VJREFUBRRQQAEFFFBAgdUL+FD31b/Fuwe4xBoGsJZQt00FFFBAAQUUUEABBRRQQIEtC9Q29veIDv9u5F+KbBwhEEzzC7jjzW9uiwoooIACCiiggAIKKHC0gBUooMDMArdGe38lskmBRQQMYC3CbqMKKKCAAgoooEABAnZBAQUUUEABBRSoRMAAViVvlN1UQAEFFChTwF4poIACCiiggAIKKKDA9AIGsKY3tgUFFOgXcKkCCiiggAIKKKCAAgoooIACvQIGsHp5alloPxVQQAEFFFBAAQUUUEABBRRQYP0C2x2hAaztvveOXAEFFFBAAQUUUEABBRTYnoAjVkCBKgUMYFX5ttlpBRRQQAEFFFBAAQWWE7BlBRRQQAEF5hYwgDW3uO0poIACCiiggAInJxoooIACCiiggAIK7CFgAGsPLFdVQAEFFChJwL4ooIACCiiggAIKKKDAVgQMYG3lnXacCuQELFNAAQUUUEABBRRQQAEFFFCgAgEDWEe+SW6ugAIKKKCAAgoooIACCiiggALrF3CEywoYwFrW39YVUEABBRRQQAEFFFBAga0IOE4FFFDgYAEDWAfTuaECCiiggAIKKKCAAnML2J4CCiiggALbFDCAtc333VEroIACCiiwXQFHroACCiiggAIKKFCdgAGs6t4yO6yAAgosL2APFFBAAQUUUEABBRRQQIE5BQxgzaltWwpcFnBKAQUUUEABBRRQQAEFFFBAAQUGClQcwBo4QldTQAEFFFBAAQUUUEABBRRQQIGKBey6AicnBrDcCxRQQAEFFFBAAQUUUECBtQs4PgUUUKByAQNYlb+Bdl8BBRRQQAEFFFBgHgFbUUABBRRQQIHlBAxgLWdvywoooIACCmxNwPEqoIACCiiggAIKKHCQgAGsg9jcSAEFFFhKwHYVUEABBRRQQAEFFFBAge0JGMDa3nvuiBVQQAEFFFBAAQUUUEABBRRQQIGqBA4KYFU1QjurgAIKKKCAAgoooIACCiiggAIHCbiRAqUIGMAq5Z2wHwoooIACCiiggAIKKLBGAcekgAIKKDCCgAGsERCtQgEFFFBAAQUUUGBKAetWQAEFFFBAga0LGMDa+h7g+BVQQAEFtiHgKBVQQAEFFFBAAQUUqFjAAFbFb55dV0CBeQVsTQEFFFBAAQUUUEABBRRQYBmB/x8AAP//9qEAWAAAAAZJREFUAwB8t6DkXvoGVwAAAABJRU5ErkJggg==', '2026-06-02 05:35:09', '2026-06-02 05:35:36');
INSERT INTO `meeting_minutes` (`minute_id`, `proposal_id`, `recorded_by_user_id`, `meeting_date`, `meeting_time`, `venue`, `meeting_type`, `attendees`, `num_attendees`, `agenda`, `discussion_summary`, `decisions_made`, `action_items`, `next_meeting_date`, `is_reviewed`, `reviewed_by`, `reviewed_at`, `attachment_path`, `signature_data`, `created_at`, `updated_at`) VALUES
(6, NULL, 43, '2026-05-29', '08:00:00', 'Barangay Bayabas Health Center', 'Planning', '[{\"name\":\"Pedro Cruz\",\"role\":\"Punong Barangay \\/ BNC Chairperson\"},{\"name\":\"Alma Sedano\",\"role\":\"Committee on Health \\/ BNC Vice-Chairperson\"},{\"name\":\"Nancy Ongayo\",\"role\":\"Barangay Nutrition Scholar (BNS)\"},{\"name\":\"Teresa\",\"role\":\"Barangay Health Worker Representative\"}]', 4, 'Barangay Nutrition Council meeting para sa Supplementary Feeding Program', '1. Resulta sa Operation Timbang Plus (OPT+)\r\n•	Panaghisgot: Gi-report sa BNS ang resulta sa bag-ong screening diin naay 20 ka bata (11 ka lalaki ug 9 ka babaye) ang nakit-an nga malnourished o underweight.\r\n•	Lihok nga Pagahimoon: Nagkauyon ang konseho nga sugdan ang Supplementary Feeding Program para mahatagan og dinaliang tabang ang maong mga bata.\r\n2. Pag-review sa Project Proposal ug Budget\r\n•	Panaghisgot: Gipresentar sa Committee on Health ang plano para sa 120 ka adlaw nga feeding program. Ang budget kay ₱60.00 matag bata kada adlaw (Total: ₱144,000.00).\r\n•	Lihok nga Pagahimoon: Gi-aprobahan ang paggamit sa Barangay BCPC Fund alang niini nga proyekto kay kini alang man sa kaayohan sa mga bata.\r\n3. Seminar para sa mga Ginikanan\r\n•	Panaghisgot: Gihisgutan usab ang pagpahigayon og orientation para sa mga ginikanan bahin sa pagluto og sustansyadong pagkaon nga barato lang.', 'Feeding location: Nagkasabot ang tanan nga ang feeding program pagahimoon sa Barangay Bayabas Health Center / Session Hall aron dali ra ma-monitor sa BNS ug BHW ang matag bata.\r\nSanitation: Gihisgutan usab ang pagsiguro nga kanunay limpyo dapit aron malikay sa sakit ang mga bata.', 'Committee on health will prepare feeding proposal for this one. \r\nTungod kay walay dili angay nga hisgutan ang meeting opisyal nga natapos sa 12 sa udto.', NULL, 1, 41, '2026-06-02 13:39:55', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABLAAAAGQCAYAAAC+tZleAAAQAElEQVR4AezdC8w9614f9PeI5+y9z9nncDTWEq6W1qYFC0nRVKGtJlrElgJFBWOitbQJEEWoVktq05jG1gZLpEoaLCm0GhOspVzEWCQRa9AqRUOF01h74S7ES4Fz2GefG63P97//s//rfd5nZs1aa2bWXD47M/8188wzz+XzzFrvO7/9rHn/rgf/ESBAgAABAgQIECBAgAABAnsX0D8CmxYQwNr08Gk8AQIECBAgQIAAAQLLCaiJAAECBO4lIIB1L3n1EiBAgAABAgSOKKDPBAgQIECAAIErBASwrkBzCgECBAgQuKeAugkQIECAAAECBAgcTUAA62gjrr8ECETASoAAAQIECBAgQIAAAQIbEhDA2tBgraupWkOAAAECBAgQIECAAAECBAjsX2AdPRTAWsc4aAUBAgQIECBAgAABAgQI7FVAvwgQuFlAAOtmQgUQIECAAAECBAgQIDC3gPIJECBA4NgCAljHHn+9J0CAAAECBI4joKcECBAgQIAAgc0KCGBtdug0nAABAgSWF1AjAQIECBAgQIAAAQL3EBDAuoe6OgkcWUDfCRAgQIAAAQIECBAgQIDAhQICWBeCrSG7NhAgQIAAAQIECBAgQIAAAQL7F9DDFwICWC8sbBEgQIAAAQIECBAgQIDAvgT0hgCBnQgIYO1kIHWDAAECBAgQIECAwDwCSiVAgAABAvcXEMC6/xhoAQECBAgQILB3Af0jQIAAAQIECBC4SUAA6yY+JxMgQIDAUgLqIUCAAAECBAgQIEDguAICWMcdez0/noAeEyBAgAABAgQIECBAgACBTQoIYF00bDITIECAAAECBAgQIECAAAEC+xfQw7UJCGCtbUS0hwABAgQIECBAgAABAnsQ0AcCBAhMKCCANSGmoggQIECAAAECBAhMKaAsAgQIECBA4A0BAaw3HPxLgAABAgQI7FNArwgQIECAAAECBHYgIIC1g0HUBQIECMwroHQCBAgQIECAAAECBAjcV0AA677+aj+KgH4SIECAAAECBAgQIECAAAECVwtsJoB1dQ+dSIAAAQIECBAgQIAAAQIECGxGQEMJtAQEsFoq0ggQIECAAAECBAgQILBdAS0nQIDA7gQEsHY3pDpEgAABAgQIECBwu4ASCBAgQIAAgTUJCGCtaTS0hQABAgQI7ElAXwgQIECAAAECBAhMJCCANRGkYggQIDCHgDIJECBAgAABAgQIECBA4OFBAMtVsHcB/SNAgAABAgQIECBAgAABAgQ2LjAigLXxHmo+AQIECBAgQIAAAQIECBAgMEJAFgLrFRDAWu/YaBkBAgQIECBAgAABAlsT0F4CBAgQmEVAAGsWVoUSIECAAAECBAhcK+A8AgQIECBAgEAtIIBVi9gnQIAAAQLbF9ADAgQIECBAgAABArsSEMDa1XDqDAEC0wkoiQABAgQIECBAgAABAgTWIiCAtZaR2GM79IkAAQIECBAgQIAAAQIECBDYv8ACPRTAWgBZFQQIECBAgAABAgQIECBAYEjAMQIEhgUEsIZ9HCVAgAABAgQIECBAYBsCWkmAAAECOxYQwNrx4OoaAQIECBAgQOAyAbkJECBAgAABAusUEMBa57hoFQECBAhsVUC7CRAgQIAAAQIECBCYXEAAa3JSBRIgcKuA8wkQIECAAAECBAgQIECAwKmAANapxn629YQAAQIECBAgQIAAAQIECBDYv8BheiiAdZih1lECBAgQIECAAAECBAgQeCoghQCBLQgIYG1hlLSRAAECBAgQIECAwJoFtI0AAQIECMwsIIA1M7DiCRAgQIAAAQJjBOQhQIAAAQIECBDoFxDA6rdxhAABAgS2JaC1BAgQIECAAAECBAjsVEAAa6cDq1sErhNwFgECBAgQIECAAAECBAgQWJ+AANbUY6I8AgQIECBAgAABAgQIECBAYP8CeriogADWotwqI0CAAAECBAgQIECAAIFOwCsBAgTGCghgjZWSjwABAgQIECBAgMD6BLSIAAECBAgcQkAA6xDDrJMECBAgQIBAv4AjBAgQIECAAAECaxcQwFr7CGkfAQIEtiCgjQQIECBAgAABAgQIEJhRQABrRlxFE7hEQF4CBAgQIECAAAECBAgQIECgLbCnAFa7h1IJECBAgAABAgQIECBAgACBPQnoywEFBLAOOOi6TIAAAQIECBAgQIDA0QX0nwABAtsSEMDa1nhpLQECBAgQIECAwFoEtIMAAQIECBBYTEAAazFqFREgQIAAAQK1gH0CBAgQIECAAAECYwQEsMYoyUOAAIH1CmgZAQIECBAgQIAAAQIEdi8ggLX7IdbB8wJyECBAgAABAgQIECBAgAABAmsWmCaAteYeahsBAgQIECBAgAABAgQIECAwjYBSCNxJQADrTvCqJUCAAAECBAgQIEDgmAJ6TYAAAQKXCwhgXW7mDAIECBAgQIAAgfsKqJ0AAQIECBA4mIAA1sEGXHcJECBAgMAbAv4lQIAAAQIECBAgsB0BAaztjJWWEiCwNgHtIUCAAAECBAgQIECAAIFFBASwFmFWSZ+AdAIECBAgQIAAAQIECBAgQGD/Arf2UADrVkHnEyBAgAABAgQIECBAgACB+QXUQODQAgJYhx5+nSdAgAABAgQIECAwu8BHSg1/u6x/54L19ZJ3hkWRBAgQILBVAQGsrY6cdhMgQIAAAQIE7iGgTgLDAq1g1UeVU95S1kuWl0vmD5XVQoAAAQIEngkIYD1j8A8BAgQIEFhOQE0ECBDYmcAvlv50s6uuCVaV05vLW0uqIFZBsBAgQIDAw4MAlquAAIEtCmgzAQIECBAgcF+B95bqu6DVnPcUglgF2kKAAAECAlgHvgZ0nQABAgQIECBAgMDFAt1sq3defOaLE7rAV+v1Ra4XW4JYLyxsESBA4AqBfZwy5/8t2YeQXhAgQIAAAQIECBA4tsAts60SpMr5bymE3Zp7kL71a0u+1pIgVitdGoFlBNRCgMDdBfKD4+6N0AACBAgQIECAAAECBFYncOlsq75g1UenZyPX31Py9QWxXivHLAQIECBwUAEBrIMOvG4TIECAAAECmxPQYAJLCXywVJRg1Nh7hQS6Mrsq+S8JVpVqmktfECt/mbB5gkQCBAgQ2L9Afsjsv5d6SIAAAQIEngn4hwABAgTOCPztcvxtZT23JMCVoFXWv/tc5iuOJ4hVn5a66jT7BAgQIHAQAQGsgwy0bhKYTEBBBAgQIECAwB4FPlI61QWlymbvcjrbqjfTTAcEsGaCVSwBAgS2ICCAdYdRUiUBAgQIECBAgACBlQj8XGlHAlcfVV6HlgSPss4x26qv3rSr75h0AgT2JfAFpTvveb5mu2zuY9GL6QQEsKazVBIBAgQIECBAgACBLQnk64LnnlmVB6cncHWPfglg3UN9fXVq0TEEvrl081Oer9kumxYCjwUEsB572CNAgAABAgQIECCwM4En3flwSUlwaCgw1R1/teS91/K3GhW/v5EmiQCB7Qu8+6QLp9snyTaPLiCAdfQrQP8JECBAgACB8wJyENiHwDeVbmTW1bmvAX5xybeG+4RfUtpRLy/VCfYJECBA4BgCa/jBdAxpvSRAgMDBBXSfAAECBO4qkIev/47SgqFZV5mZleN/puRb65L2rbVt2kWAAAECMwoIYM2Iq2gCEwsojgABAgQIECBwqcDr5YR8HXDo9/4cT2DobSXv2pe0c+1t1D4CBAgQmEFg6AfZDNXdu0j1EyBAgAABAgQIEDiMQL4u+PKZ3v6NcnzN9wQJrpUmWggQIHCpgPx7E1jzD6u9WesPAQIECBAgQIAAgSUEPlIqSeBnaLZSvlKY47+i5F3zkn6suX37bpveESBAYEUCAlgrGgxNIUCAAAECBAgQ2JfAwr35uVJfAj4fVV6HlgSuzj3Ifej8JY+9tmRl6iJAgACB9QoIYK13bLSMAAECBAgQeHhgQIDAOIF8XfCjz2TN87ASvDqTbVWHX1lVazSGAAECBO4mIIB1N3oVEyBAYCkB9RAgQIDAjgXylwMz62ooMNUdf/sGHdyvbHDQNJnAhQJffmF+2Q8q4AfCQQdety8UkJ0AAQIECBAgsC6BbyrNyayrc18F/OKSb8u/8w8F5krXLAQI7EDgq6o+JOheJdkl8PCw2A8z2AQIECBAgAABAgQITCKQWVe/o5T0lrL2LcmT43+mL8NG0tOHjTRVMwkQ6AQufP1lVf73Vvt2CTwTEMB6xuAfAgQIECBAgAABApsQ+FBp5dCsq8xcSNDnbSWfZbsCWk7gSAJvrTr7vdW+XQLPBASwnjH4hwABAgQIECBAYF8Cu+xNglf1jd5pRxO42vvv9wnQnfbZNgEC2xZoPf/qK7fdJa2fS2DvP+DmclMuAQIECBDYv4AeEiCwJoGh4NUvloYmeFVeLAQIENiUQB2sSpD6xzfVA41dTEAAazFqFREgcEQBfSZAgAABAhMIDAWvErga+krhBNWvqojc3K6qQXdsTAKX8bh1zR8DyPqRO/ZF1ecFXi9Z6rH+vpK29eWTqw54/lUFYveFgADWCwtb6xTQKgIECBAgQIDAkQWGgld/+IAwCdocsNtvdjn974IYU93LJQia9aNKLV3Z3WsCW1kT3PpT5bjlfgIvN6r+rJL2l8u65aX+WrTnX215NG9v+2AJU33oDVbiIAECBAgQIECAAAECFwsMBa8ScPh3Li5x+ycc8eH0cwStxl4Juc6yJrj128tJXWArf+Wy7FoWEkgQsa+qTysHToJYZW87i+dfbWesVtFSAaxVDINGECBAgAABAgQIEHgkMBS8OsrMq/sESR4Nw9127hm0GtPpfG31g2MyyjOJQIKIQwUliPWeoQwrPeb5VysdmLU2SwBrrSOjXQQIECBAgACBjQpo9s0CQ8Gr3MgeZebV0e5Vrg1adbOirnm95WI94my4W7yuPXdo9tVpmZ9SdrY2E8vzr8qgWcYLHO2HwngZOQkQIECAwP0E1EyAwHEFzgWvjiSTYN3e+3tL0Co+WXNPd+2a80/XXH8JmCQYNsZ+bL4xZcnTFsj41EdaacmztZlYnn+VUbOOFsgH3ejMMhIgQGA7AlpKgAABAgQ2J5DgQX1D13Wi74a1O77H1732eaqg1Rxj/lIpNM+7yn1i/E/XXJ/l8JMlAa8niRImEWjZ5vpJ4RmbvNZrZmLlLxbW6Wvb9/yrtY3IBtqTD6YNNFMT7yKgUgIECBAgQIAAgaUEEhzoC15981KNUM9sAgk6ZLZS1kvuwZL/fyutSrDikvPKKZMvCW7lOq0LTtvSvzrd/u0Csa1LyfPHurTP7Daq1/zFwlbwq8p2192vqmrPtf7jVZrdJQU2UNe9PwQ3QKSJBAgQIECAAAECBGYVSFCgL3iVG9gvmbX27RSeG9zttPbhIUGdtDnrJfddyZ9xz5rzPmNFnU4QqxUYSTsP/1D3icep5Zxr6rSav1h2cp2UlydL0nMtPTmwkgTPv1rJQGypkIazjwAAEABJREFUGfmg2VJ7tZUAAQIECBAgQIDAngSGgldH+WuD3Xju4fW9pRMJPCRwcMm9VvL/1+XcBB0uOa+csviSrximvXXFeaj7H6oT7V8tkGuhPvl09tXpsVbe7njG6se6nRW91n353hW1TVNWKrD2D8eVsmkWAQIECBAgQGCNAtq0MYGh4FVuSI/y1wbHDltuxMfmXTpfZsakfe8sFWfsysvZJfmTN2vuyz737BnzZuj6kHZ164d7qkx7W4d+XytR2sUC3984I+PTSH4zKdfRmzvVxieW/QRWy8sqlj/YaMVXNtIkEXgk0PfB8yiTHQIECBAgcBgBHSVAgMAyAueCV8u0Ylu1nLuBX7o318y2SmAogYasa7kXi2va1WpPPUvm1Dh9ON3vttcUKOnatLXXf7jR4KGx6LJnTDKW3f7p69Cx03xLbNfPv0qdnn8VBeugQOtDavAEBwkQIHBOwHECBAgQIDChwM+VsnKDnZvi3JjNsabs1PFaqWuJRfDqvHLGpM6Vr6jVaffYz7WS63DsbKvkTfAg65ruv7p+nGtTayw691zL3Xb3mn6m7G7f67ICGc+hYFCux/cs26QnteW9c5r4vtMd2wT6BHJx9x2Tfj8BNRMgQIAAAQIEjijwkdLp3CznBqtbP7qk5XfW3BSXzVmWlJ063l5K7+rNa9qSG/EpA1u54R96YHtpgqUIZEzKy2qWa2Zb5dpJP3JtraYjpSFpV67vse1KH8ppzWXooe7NEySOEqjNM16jTnye6ZPKa11GSXpz+ZQ3t5bf+C2NKv+jRtqRkvR1pMDYD62RxclGgAABAgQIECBA4KxAnquT4FDW3Jh1ax4OPXTTdbbgiTOkLfl9+TSwlSDbtdUIXo2T+4VGtlwjjeTZk7pgT2aM5Ho4V2HamXxZx3zl61x5Ux7v+pJreqjc9KE+nvdqndbt533bOueW90pX9sjX3Wdr+Y7pdK7DvnP70seUe0ueb2yc/PsbaZIIPBE49+H15AQJBAgQIECAAAECBEYKvF7y5aY5N7+5WerW3NjnxiprybKppe9m/Vwn1h28Otf6ZY8nYFjXuOR9y55mW8Ux78G8984Z5n2a92QrX9JTVt/aOqeV1ne+9BcCP/Bi882tfO68uXPhRsbhp3rOyZj3HJot+WOqkvPZWCXZJdAWyMXcPiKVAAECBAgQIEBgtMDBM/Y9p+rl4pLfN8/d/JZsFy25GZ9ivajSKnPqf3+V1rebGzRfG+zTeZo+9fXytIZ2Shfo2cNsq/Sw60/eg9nvWxPEiPlpkCRpdf5WWp3ndD9lnu7bHifwa8dluyjXx5fc/1ZZ6yVj9BN14sz7qfO0iu853bFNYEjg3IfZ0LmOESBAgACBKQWURYDANgTytaDcyCaA061zPacq5ecmPDc8p2t+h51iPS0z26krdY4diVdKxliUl8FF8GqQ59HBXF+PEsrOGOOS7aplb7OtgtBdx3mPZL9vjWuu+9PAVZe3lZa83fHW6yXvndb50uYV+KOl+NbD0hPcKocWWf73Ri2f20iTRKApcO5DrXmSRAIE1iqgXQQIECBAYDKBJZ9TlRvfrLlBPl3zu2q+bjhZp84UlLpS52kbsp2AQN+pOZ62983GyrHWuTmvlX70tFbgpJV2q1PGNGOzl9lW8ej6lGs4+33rUODq9JzkO93PdivAmPSsCQbm9XQdyn+az/YLgfqzIdfpi6O3bb2rnN4qr5VWsk6+fGpVYusaq7LYJfBC4NyH24ucR9nSTwIECBAgQIDAsQSWfk5VbpRSZ27SujW/k2Zdq3wCW2lr2t7XxszGqm/W+27OUlZfOdIfCwyZP855fi8BloxJyhx7vSUolPEam/98K6bNka+nju1T8qUvYwOCrXyttK5H7+42Tl7X6nbSxNVv5nqdspF9Y5LrY8p6WmXVdf9wK9OiaSrblEB9AW2q8RpLgAABAgQIECBwscDfKGfkRiU3RVnnfE5VHajKzXN+/2w9pLs0a/VL2v7aQCtzcx/TZMkMtvQ326frz5/u2H4kkOvyUULZiWN5uWlJECrjsqfZVrFKn/L11NZ1dgqWvMmT6/M0fcx26qjz/eRpwpnt1Hsmi8MnAh842e42rxm37ty+13s8D+s7Go359EaaJAK9Avkh3HvQAQIECBAgQIAAgV0IZGZQbkSzfnLp0ZQ3lSkzAYKUebrm98ytBqoKUe/yajmSfqbfZbO55FhmbdUHk17PUqnzHHk/rnX/X6oTRu7vcbZV3me5hrK2rGqaWwJXXVl5H3fb3evHdhuN17StkSxppMDbRua7NdvQ87C+99bCe87/nCrdtVKB2D0v0PpAOn+WHAQIECBAgACB1QhoSEOgnmU1xf/Bz81G1tw4n675fbIVrGk0a1dJ6XcCCmM7FbucMzb/0fL9QqPDMWskDyZlTHLeXmZbXfIVwQ4m/c97dIr3fcpMeXnt1pTdbdevU8yYq8s80n5tW9tPadH3PKx/YspKTsqqg3M/c3LMJoFRAn6IjmKSiQABAjsX0D0CBPYgMOUsq9w01V//y++NWfdgNVUfErj7nSMLYzcM1Zqt97uGT3nz6N5mW31j6VlmT+V9OOYrgiX7Q/ImeJQAyNTXWspNHadr2ne6323nPdFte71MIF/nrs/IuNZpU+73XStT1/sHG43+1xppkggMCvRdsIMnOUiAwFMBKQQIECBAYGGBKWZZ5SalDlR1N8CtgMLCXdxEdd9UWhmzWJbN3iUBxt6DDjzEsGaIbZ12up/AStz3Mtuqmz2WwF3L47Tv3XYCScmb+7p6hkuX59bX1tc4U2er3L70Vl5pjwVaz9ebahbd45oe7/WNWa6txzmv3/uqxql/rpEmicCgQD7oBjMseFBVBAgQIECAAAECwwIJguSGPeu1z7JKGblhyZrfBQWqhs3HHo3lUN4lbkSH6l/zsVyTdfvO3Twn2DN2tk/ydtd7Xc+992/5imD6tNR11RqP1rilTfc23Wr997TLM7Fqt7SnNe51vjH7CTKf5nvfw8PD6b5tAqMEzv2gHVWITAQIECBAgAABArMITDXLKjci3ZqvJM3S2IMXmqDiOYKpbgbP1bO1460gTCut61ccz93HZDy6a35soKsrf4nX9CFtzPsx7TxXZ/JmxlnyPu/7uVMmPd4aj1ZaXWnaXafZbwtkbE+PLGmXv0r406eVP99Om1rPp3t+eNTL5zVyfV0jTRKBswL3+PA72ygZCBAgQIAAAQIHFsishty4ZJ1qltWBOQe6Pt2hBCPGlJabwTH5jp4n136fQayHHNc82yptS9+yDvXhtO9df3PfNtdXBE/rG9pOu+vjP1onVPutc6osdovAXyhrvbQCSnWeKffz1yVzvdVlvqMk3PKXCb+hnF8vf6BOsE9gjEA+CMfkk4cAAQIECBAg8ERAwiQCZllNwni3QrpZMXUDfr5OsN8UaN0wx7SVOXn7Aj9Jz7q22VZb+Ypgy7tOa907fuJJpjxP72T32eaYWVrPMh78n9/Q6P/HNdLmTsp4tYKOt/xlwo+pGp33RJVkl8A4gdaH0Lgz5SJAgACBKQSUQYDAMQXMstrPuLcCJrkBfHfpYl7Ly6Pl/Y/2jr2TG9kEnWqF1kPDY9nK25del7n0foJtadtWviI41id9Os17Oib3niF22q6tbZ863rvtfTGCeuzHtrPu258fe6J8BGqBvouzzmefwIoFNI0AAQIECKxeYKpZVp9fepqbgay5MS67ljsKtG7oktb9jt29njbx5dOdg2+3ruEEtU5Z8vycmJ6mddtJbxl3x5d+3fpXBMd4tWbH/cjzE/O59HzTy40CubZvLOKm0/vGMoHZSwr+y43M+TnWSJZE4LzAGx/45/PJQYAAAQIECBAgcJnAHLOsvvOyJsg9o0Dfjdy536/7bgxnbOoqi06wp9Ww09lXeXj0d7QylbTc4J+zLtlmX9KPtCXr2PYk7/eUluVayFe2yuZmltPx6Rr9Cc830p/nm14uEHitkfePNNKmSxpX0tc3smWM+z77Gtkf/qEq8ZJzq1PtEnh4GPshy4oAAQIECBAgQGBY4MfK4fxynpvTrNfcmOa8t5RyurU1Q6UcttxZILNQMkZ1M1rPvcqY1vnsPzTvQ95yAvPBsp2HR5eXJ0veZ/e8j/nF0qKMa9ax7Uje7rrJOZ9dytjLkv60+pI+t9KlPRZ45fHus73f9+zf+/7zFaX61oPk8z5NcLkcPrvU18YPnz1DBgIDAvUFNZDVIQIECBAgQIAAgUogv8TnJi1rHmacX+yrLGd3M1Mr52U9yu9mZ1FWnOHV0rah516Vw4+W73u098ZOAjBvbB3z31b/8x7qNBLo6XueUmY8XRMc7sq+9jX1po1ZL3mfpq/de7uvT9e2ae3nxWrtbVxD+3J9nLZjTW5Df5nwtM2t7e9qJH56I00SgdECl3z4ji5URgIECBAgQGBuAeXfUSA3pLnByNo3Q2Soed15uWnJapbVkNb6jr2v0aSMad/v1b+xkT/j3kg+RNLbSy9b/e/8EihqBQjLaQ8J9vYdy/Gp17QlY5u1a9+YOpI/fcx6j2DbmDZOnSefi3WZU/f9dDwyQ6+uby/7uX7W1JeMY6tNrTE/bXc9y7BVxml+2wTOClzyQXy2MBkIECCwKQGNJUCAwDiB+quBuSkdd+aLXLnxynlZ8/uXv0T3wmZLW303bBnTLfXjnm3NrMW6/s4175M+y9fLSUsEe9OG3Ghn7WtLacqTJfnz/s56yXlPCtpAQvp62sz0Oetp2pTb3ZicumY2W3fdTFnX0mWlb3WdCRjVaffeP7Xv2pIxH/pZVr9ff6Y70SuBawVaF+K1ZTnvgAK6TIAAAQIEdiqQm+zcpGW95quBOS+/3HfrkrNGdjokd+9WvtaW8awb0nruVZ0n10OdNnTjV+fdy37+wmDLMDfsCUb03ZvknMzcmsshQYSMUda+NrTqTv60Lesl57XK2ltaa6bipX3M9RLjPtu453g+ry8tey3504e1tOVcO/KHB+o8red3Jc8P5Z9q/bJq3+4GBe7d5L4Pg3u3S/0ECBAgQIAAgaUFcgOdm6Gs13w1MOfnZiSr37GWHr156/vqUnwrCJlr5d3l2LmldT28fO6kHR6vZ2SkiwlSdO+d7Nfrb64TJtq/JWj1L5c2HPl9nuu+EPQuOf6u3qPjD7Sul9bZ+bzONdQ6tra0uj25jk7TYne6v6btfCWw1b6Wff3XB9OP78w/VgK3CLR+mN5SnnMJECBAgAABAmsXeK00MDev+aU7v4x3a30jUbKdXXLznfOyZhbJ2RNk2KTAv99oda6bW36XzjXTKHa3SXnPtTqXIEWfRdL/m8cn3bSXNmTcsl4ydsmftmTNef/ZTa3Y/slxHOpFjIaOz3EsY5Nx2tLX1P5CA6L1V/8a2e6W1Brb2Ofnateof73bOHn9/pNtmwSuFmhdgFcX5kQCBAgQIECAwIoE8gt1brTqQFW+ipTfgSPv1eEAABAASURBVPJL96XNzQ1SzuvWly4t4O75NeBSgVw/rXNyDbXS+9Jy7fQdO0J6n1feS3X/Y9VKr/ON2c+zszKGKbOvDa1ykj9tyHrJea2y9paW50/19SmfuX3Hbk3POGY88jD/vrJ+aTmQz/7ysvrlNzRa+HGNtLUltb5KmJ+rXTv/aLdx8vrrTrZtErhawIfx1XROJECAAIGjCuj3qgVyY5Mbz6z5hTq/6+SG55ZGdzdNKSfl3VKWc7clcMtzr+qe/nd1woH28x4a2928d6d4n6XOlJWvaua9O6b+5E/erFO0YUyde8oTv9ZXbafqYzfLtZu1l/paZeezv5W+trRcZ2tr05j29H2VMH/wJOdnfPLarVsJKHbt9bpiAR/MKx4cTSOwYwFdI0CAwFwC3Q3OreX7auCtgvs4v3UznpvmMc+9qgX+qTrhQPtjb9Rje8v9yWkA+5I6kzfrLXUfaDh7uzql35gHs6e+jHmrQbmWWulrTttSm2NfW35CSfjRstbLb6sT7BO4VqB14V1blvMWFVAZAQIECBAgUAnc8nW+3DjkBrZbbymrapbdjQpkBk/d9Fwnfn+uVYb3Yzac442j8b7WNuemnrEB7OTt3uvX1vlGq4/5b7zrnse0Trt2P/8DIQ9mH3N+ZvtkLFt5W+1s5btH2g82Kt36tZhx+KSqX7kuWl85rLLZPS8gRwS2/iZJH6wECBAgQIAAgQh8MP+cWfPL9AdKnvyifbr6naigWB4J5Pp4lFB2XCcF4YJlbAAhz00aG3zqqs85eT9nbY1Vl697Tb7M1kle49ipXP6ar4PF8PIzx52R4FWCUuNyv8j1/hebb26lnblO3khY17+ftq7mXNWaMe9vf3nwKlon9Qn48O6TkU6AAAECBAhsUSA3LPmlOjerfYGqV7bYMW1eVKB105vratFGrK2yC9vT9/ywupgElVpf1azzZf+aB7LnsyCfC7nvuSYwknqtLwT6ni8V4xe5rtsaCl6dKz8ztlrv0Yx7gm7XtchZQwJfNnTw+bEveP7qhcAkAnlDT1KQQggQIECAAAECKxHITI78jrO2QNVKeDRjhECunzpbrqs6zX5b4FeX5DFBqQSkzgWVusBEAlFjH8ievF0AuzWWpXmWKwRagd0riuk9pe9aOBe86grMezRj3+13r31Bt+74PV7rPrXafY92XVLnN57J/LNnjjtM4GIBH+gXkzmBAAECBO4noGYCBAjMLpCZQ3UlW7y5rPuw5P5fGVFZbuCHAgsZh7jnYd7JO6LIh8zASd7c4whgjxG7LE9cLztjfO6MXSt3xrOV3pfW18ZcS33nrCF97e27xug3XnOScwgMCfS9wYfOcYwAgS0LaDsBAgQIECAwJNCaOeR35iGxx8f6AhGnuYaCEjk/N/OtcTgto9tO3pSXNTNwunSv0wpkXKYt8XFpGb/HKQ8PrbSHEf/1nTd3H0Y07VmW9zz79/E/W712+0yT/sOPu2iPwO0CfhhfYegUAgQIECBAgACBXQrka2e77NhCncqsqb7gQZrQBZuyfbrmq2k5lnXo/O6c5Muzs5LX/UynMt9rnk0V67lqaP0BjgRAbqlvzQ91z1dsb+nbms79D3oa8w096ZtM1uj1CPjAX89YaAkBAgQIECBAgMB9BV5qVP/zjTRJTwUS5BiaNZWAU33vkSBFglF1+tPS30hJ3gRSkr/veUlv5PTvlAIt61bQKddAq95zaa3yb52R1D07ra47106dZv96gc/rOfVf7UmXTOAmAW/gm/icTIAAAQIECBAgsHOBd9+/f6tvQQIXrSBEGt4FnU6Pd4GrBKOSZ2jN+ZkZl7zuXYak5jmW2XF1yRmTPFC/Tr826JSxPS0r5Z/uX7ud9rTKavXp2jquOW+u/l7TllvP+eWNAn66kSaJwCQCfghMwqgQAgQIECBAYNUCGkfgvEDrRjeBmfNnynEanDrVqGddXRK4St7c6Od+xQPZT1WX3Y5/XWMrLXkyXnm9ZM041/nzVdQ67dr9VltbadeWP8V5rc+eKcpdoozWe/9jl6hYHccUWNub95ijoNcECBDYgIAmEiBA4IACra8UHpDhqi4nMJGb2/wVwWznJv1cgKPLk3yZPXNVxU6aTCDjVhfWSqvzXLKfsa7zT/2+y3VV1/HddcJC+3t6gHvIWuOXdCuBWQQEsGZhVSiBpoBEAgQIECBAYJ0CrZvye3/NaJ1S41sVvwQO8iyicze5yZc87k3G+86dM7OgMiZ1PVMGFlvP0cq1UNd5637ruvpNtxZ65fl7eoD7/9AwmGP8GtVIOqpA6828YgtNI0CAAAECBAgQIDC5QOtGfeiB5JM3YIcFZvbVuW7lZjf27knOSS1/vHX9v69qRsbvNCljebp/brt1jSx1LVza1nN9OeLxXz9/p9VA4LHAUh8Qj2u1R4AAAQIECBAgQGAdApkpVLekNSOrzmP/eoH4JoDgXuR6w3FnXpcr41OfmWDVu+rEG/dzDZwWkTpO96fcbvUps8ymrGNMWUv2eUx7rs3zxeXEui8lyUJgXgE/NOb1VToBAgQIECBAgMC6BVq/D7/5Nal1N30VrWsFBvoalmBhbnr59gndP/3nSxMyRuXl0dJ6nzzKcOFO67qZM6DUuuZaaRd24+bscwbtbm7cQAF/qudYnnnXc0gygdsFpv4gur1FSiBAgAABAgT2JKAvBNYskL+SV7dvqzeUdT/m3P/sUngCELFqBTvK4UdLAhPJ1/pa2qOMdu4u0JpllcBjq2EZ/1b6mLRcD3W+qR/eXpdft7fVhvqcKfd/sFHYVzbStpD0ck8j/1JPumQCkwgIYE3CqBACBAjMKaBsAgQIEJhJoDUDw+/H/dg/UA4lcJW/4Hbu5j/BgtdL/uR7W3m1rF+gL6DbF3jMGF/Tq6Ue3l63rVVvX3CuPneK/U9rFPL1jbS1J7X+kmLX5i/sNrwSmEPAD+g5VJW5PgEtIkCAAAECBAg8FvjQ491ne9fekD87ecf/5GtBCVx9RuljAlLlpXeJYfLkPuPtvbkcWKPApQHda4M/93p4+ysN9FynjWRJAwKfMnAsX0EdOOwQgdsERr9hb6vG2QQIECBAgAABAgRWJbD0TXQCQKsCGNGYBPkSkHpHyZugVHnpXZIvedxf9BKt+kDr+syYDjX62q/85To5LfdcPad5b91u1fV9txY68vx79ntkE19k69n60p50yQQWEfADZhFmlRAgQIAAAQIECKxI4B6zBOqb1xVxPGlKnlmVG/1WkK/OnHzpm/uKxzJb2nutNDZjWF4eLdeMaYKejwqpdlqBslxvVbbZdlt9+szZahsuOO+d4RzrO/ofr69JWnQkgdYb+Ej911cCBAgQIECAAIFVCszaqNaDqt8/Y42tm/Y13rzmGUFpV98zj06J0qcEPdxPnKpsc7v1Vc/W87DG9K71NcTT83LNnO5n+9qZXDl3irXVpinKPS2j9QD3c1an569le0xQey1t1Y4dCviBs8NB1SUCBAgQIPBMwD8ECLQE/nwrsaTla3LlZZaldYO8tt/DE5Aa87D1PPco/dnizfcsg7vxQjOedRcSxLw2UJFroy6v20+AtNvuXlNXt73Ua671uq65Z4G1HuBet2Ht+z9xpoH3GMszTXJ4bwJr+8G5N1/9IUBg4wKaT4AAAQK7E/inGz2a8+a1VfaabvTyla+0ZyjwELLMyEmeMbOzkt+6foFXSxNb94O/sqTPsbSCYq3656j7tMxW8LWVdnqO7YeHj4dA4N4C9/jAuHef1b+sgNoIECBAgAABAmsXGDPz6No+tAI+n3NtYROfl5koraBCV00CW6+XnQSuhvKVLJYNCry30eZcE3+9kT5FUq6j03JyfZ3uL7ld1123beq21OXX9U9d39Tl/eGpC1TebgVm7ZgA1qy8CidAgAABAgQIEFiRQG7O6+a0vkJV57l2P8Gf1rn/bStxwbTMCssNdH1T3TWhO5Z7hdbzkbp8XrcrkJl3rfG/dCZSrpVThVaZOf4L+adacx1WSYvttr7O2Pp8mKtBtdtJPavc/LdHtKo1xiNOk4XAeIH8UBqfW04CBAgQIECAAAEC2xVo3Vy3ZkhN1cOXGwW9r5G2VNInlYpykz7U5wQVtn2PUDppOSvQmlHXCuqcLWhkhlYg9J4Pb3+l0e7W50Mj28VJ72mccWmgsFHEokl1e3+2UftfaqRJIjCpgB9Ok3IqjAABAgQIECCwfYGd9qA10yrBnLm6+1/0FPyunvS5k/MMqx8tlfTdpGdGSI7N+XXKUr1lBQKt90LGvxVwXUFzZ2tC6/3fSru1Ab/61gLufP7/06i/9fXTf7KRTxKBSQUEsCblVBgBAgQIEHgm4B8CBNYn0Pq9t55VMGWrv6hRWGY3NZJnT8pN+VBf83Wyls/sDVPB4gKZZdUa61bamMYl8DUmX4Kjp/nGnnd6ztTbrfdE3c6p69xieX9f1eh8nnxilWaXwCIC135QLdI4lRAgcGQBfSdAgAABApMJtAJH97iBXnp2U2bapJ99N+XdsXt+lWuyQVbQKIHWNZiAxKiTG5lyDTWSzybdUufZwi/I0GpHK+2CIp9krd9/15o9KXiBhG9p1PHHS1rdp5JkITC/gADW/Mb3q0HNBAgQIECAAAECEWg982nOr0u1boATTEpbllpzkzz0u/77S0OGjpfDlp0J5Jpodak1E6mVr5U25rrO11frc1vvyTrPEvutvs8dnOkbhyX6e2kd/3zjhK9opLXGuJFN0qwCByjcD60DDLIuEiBAgAABAgQOLJCvTLW6n6/NtdKnSGvdAC91w56AwtANcoJrad87puioMjYj0HdNvHZjD8bM3lv7PWfeE88YTv5ppZ0cHr255Qe4/7rSy3rsfrqk/R9lrZd/pU6wT2AOgfqCnKMOZRIgQIAAAQIECBC4l0DrK1N/c8bGtG58+4IHUzYjf8I+9Qz9fv9XS4WtGScleZJFIesUaF2TaWlmzbyajZnXBExnruKm4lvvianavOUHuH9PQ/VXlrSs5eXR8p8/2rNDYCaBoR9wM1WpWAIECBAgQIAAgbaA1IkF/lpPeb+8J32K5NaN79y/cydAMTSjKsfTrl81RQeVsSmBbuzrRif9rXXiQvsJtC5U1ehq4lFnbqXVefa8/86qc5ndmUB5PkuqQ3YJLCMw9w/TZXqhFgIECBAg0Al4JUCAwAuBX/Fi882tvq8Uvpnhho2lHxb/gdLWBAP6bihz7OtKntYMk5Js2blAZli1ro1cF0tdE3+yYfxzjbR7J7U8WnaXtrMuI/aXlnGP/N/XqPT3NtKSlOssr1YCswsIYM1OrAIC2xPQYgIECBAgsAOBL+rpw5wPb2895+qre9pxa3Jmhww9fyizJfK7/u++tSLnb1IggdpWUCYBlFwXS3Wq9Wykv3epyi+sJ++p+pRWWp3nkv34X5L/Xnk/s1Hx15a0/7Os9dIa4zqPfQKTCCz54TVJgzdSiGYSIEDk9hH/AAAQAElEQVSAAAECBAjcV+BbGtUnqNNIniQpf9WvVdDXtBJvSMvD53MTXM/s6IrMsc8pO61gWkm2HEDgJ0sfW89+K8kPS9//9V2nacva1lbA75b2/2Cjg1/ZSFtb0m8uDar7/ddLWpbWrFbPv3p4iI11AYGlP8AW6JIqCBAgQIAAAQIECDzUN2AhmTOo80oqqNb3Vfu37mY2yNBzi/JVnvx+/923VuT8TQt8XE/r/0RP+pzJ9fswAdZGfatJynusbkwrrc7T2v+0RuLXN9LWlvTnGg361Odp9Xg+T/ZCYBmB/IBbpia1ECBAgAABAgQIEFhGoDXT6tqb0DEt/q96Mr2rJ/3S5DxbKzf+fTePf+fh4VnAbii49eC/QwjkOml19LWS+KVlvffS1757t6urf+pZWF25W3qtv5qcr6Nm5merDwmat9KlEZhFQABrFlaFEiBAgAABAlsT0N5dCbR+x23dmE7V6c9tFJSgUyP5oqR/vORO4G1o5ljqafW3nGo5mECulVaXE2R4tXVg5rSfbZSfPyrQSF5VUsuxlXau0XXAee3Bu/TnX8g/1doFPv9qlZ5dz7+KgnUxAT/sFqNWEQECBHYvoIMECBBYg0Bu1ut23OPGse8ZRHXb+vbTj/++HKxvgkvSsyV9yrFb63lWmH82L5AAS66HuiNJv9fMvI+uG1P2/82yrn1pBbtbtpf2I+/ZS89ZOv8faVT4p5+n/YPPX09fPP/qVMP27AICWLMTq4DAJQLyEiBAgAABAjcKtG4+5/ydNwGCusmtrzDWefr2P78cSJmtfpRDz5Z8nWfOPj2rxD+bEUiwsxVgScBk6DraTAfv0NC8B+tq41yn9e3nPVof28JY9D0/LX1pXWNJtxJYTGB/P/gWo1MRAQIECBAgQIDAygRaN425iZ+zma2buqGv/A21JYGvby8ZWmWW5If0JcfqZ9TkmPWYAnk+USswkmvl3vd6uVZPRyVtOt1f83bL9BLPaz8D7m1StzvXV1+bLgno9ZVxe7oSDiVwyZvwUDA6S4AAAQIECBAgsDmB1lel/uyMvWjN0rjmJv37Sxtz3tDv5u8veYaOl8OWgwn8ROlv31dIR18rpYylltb7Zam6r6kn78nT8+qA3OmxevuSvPW5a9r/oeeNaQWrPP/qOY6X5QTW+MG2XO/VRIAAAQIECBAgsBeBn+/pyBf1pE+RnJvUupxLf7/OrKt/pC7kZD83/annHSdpNglE4OPzT2P92kba0kmtgEc9u2fpNl1aX9579Tn/bp0wcr8Oho08bdFs/1Kjtu6zqTUjzfOvGmCS5hW49AfsvK1ROgECBAgQIHBAAV0mMInAuxqlZNZSI3mSpPz1v7qgS25Sf6GcnPxDv4/nr361bhzLqZaDC+TaaRG8VhJ/T1nvvQxd1/du29j6WwG3PzDi5NZnwzeOOO/eWf69nga0gpE/1ZNXMoFZBfbwwTIrkMIJECCwCQGNJECAwLEFvrun+3POWmrd3H51Tzvq5MzsGGpbjmfW1a+qT7RPoAjk+igvT5YEGl59knqfhFy/96l53lrH9KsVdP7SeZs1Sel9D3Bv9adv9t8kDVEIgT4BAaw+GemHE9BhAgQIECBAYLMCn91oeWsWRCPbVUmv95z1NT3pXXI366rvJjizanKsdcPYleH12AIJXuUaqRWS3noGXJ3vXvu5tu9V9y31XtPu1vjc0oalzq0/d/IA9wRF6/p/uk6wT2ApgSkDWEu1WT0ECBAgQIAAAQIEzgn0Pdz63Hljjr/cyPS+RtppUv5C4tCsqzwLy+/mp2K2a4EEE1rBkQRZ6uBDfe699+cMKM/Zt9Z7MuNwSZ0Zn0vyryXve0pDWtfVx5b0LFYCiwu03pCLN0KFBAgQIECAAAECBK4UyMyT+tQEg+q0ufdbz+Dq6kzwqm92TG5uE5RofSWxO9/rLgUu6lRmw7SCCbl+1nZP13r/vXRRb9edecg77/W69Vt4/lWuo7rdn14nlH2zrwqC5X4CQ2+++7VKzQQIECBAgAABAgTOCbxxPMGfN7Ze/DtnMKh1ozc0uyQ3tH3Bq8zk8Pv4i3Gz1Rb4yZLcN6NwjddP6z1ZurDZpX7PD/Wv9dmz9udf/a2ekWkFTM2+6sGSvIzAGj/wlum5WggQIECAAIEHBAQ2LpAAUN2F1oysOs/U+33BhaHg1deVRvQFtsohC4E3Bfoerv0n3syxro06wFMHgNbV2vOtaX2mfEvPaXXfe7KtJvmPl5b8PWWtl1af/eXBWsn+4gICWIuTq5AAgZ0J6A4BAgQI3E+gNUOglTZVC1s3da2vS6W+oeDVHysZfndZLQTOCfQFf14rJ659Zk9p4rOlrw/PDm7gn9asqi8a2e419/0fKH348rLWy3eVhFacwF8eLDCW+wq0Lsz7tkjtBxTQZQIECBAgQIDAxQLvvfiM209oza5o3dwOBa9+fWnGV5XVQuCcQCtgmnMy8/DVbGxknTOofC+C1mdB3vd1e9b8/KsfqRtb9vMXVv+Z8lovnn1Vi9i/QeD6UwWwrrdzJgECBAgQIECAwP0E3tmo+gsaaVMltYIJrdkVuYnt+2rgF5bG/I9ltRA4J5DrrRUkSXrf9XWuTMevF2i91+vSWsHseWbJ1TVfvt/33KtvK0W1Ao6efVVgLPcXEMC6/xhoAQECBAgQIECAwDQC3zFNMc1SWsGE+nfpoeBVzs/NYbNwicsKrLy2fC0110vdzARRWsGFOp/96QXq93qrhtaYtfLdO+3Plga0nnv1PSX9XyxrvZh9VYvYv5vAmDfi3RqnYgIECBAgQIAAgVUK3LtRmYVStyHBozptqv3WXxlMMOG0/NTfNzPmd51mtE1gQCDBq9Y9Wq63VvpAUas4lHavoiF3aMRa+/7PNiz+75L2m8raWsy+aqlIu4vAFj8E7wKlUgIECBAgMK2A0ggQuEGgNdPhpRvKO3dq66tBbz85aSh4lbb+yZO8Ngn0CSQw23d/1pfeV5b0+wus+flXpzq57v7+04ST7XecbNskcHcBH4R3HwINIEDgagEnEiBAgMARBfIA67rfuQGr06baz0ONW2V94HniueDV82xeCAwK5BpOsLOVqS+9lXdtaa3Zi2tr41ztWevzr+r+9sUEct29v85sn8A9Bfou1nu2Sd0LCqiKAAECBAgQILAxgdYzgFppU3Xr5UZB73ueNhS8+qLnebwQOCcwFLz6NedOXvnxOWdG3rvrGbd85fPe7Zij/gSv5ihXmXcW2Hr1AlhbH0HtJ0CAAAECBAgcR+C9C3f1W3vqe1dJHwpe/aFy/L8sq4XAOYEEQVrBgjw/Kek/fK4AxxcVOK0s47PH++nWV6ZP+22bwN0E9viGuxumigkQIECAAAECBGYVeGej9H+skTZV0hc2CspXooaCV59azvn9ZbU0BSSeCHRBqpOkZ5tJd5/2jGIT/2S8sm6isWcamaDcXmeVnem6w1sQ8MG4hVHSRgIECBAgQIBAJ+C1Fvif64QF9vv+2mBu/v7KAvWrYvsCfQGPpG/pHi0B3b7RSF/6jklfn0A+v9bXKi0icCKwpQ/Hk2bbJECAAAEC1ws4kwCBTQrkq1Z1wzMTqk6bar918502DAWvpqpbOfsWaF1b6XHSt3Z/NtTevF/Sr72sCfB8cC+d0Q8CWxQY+sDZYn+0mQCBZQTUQoAAAQIElhbIzWNd51wPiO6bVdL3u3OrbXVb7RP4rkKQIFV5ebIk2NN3fT3JvKKEoWt/j89Sav1Rh9Zw9I1zK+8a0rbW3jWYacMdBLb4IXkHpjmqVCYBAgQIECBAgMBIgY808uWGv5E8SdIlN95DN/CTNEYhuxB4rfTit5S1teRanvMvabbqlHa9QN7zr5fTE/Rprf9vObbm++y0/z8tbcwfxchfVP2Gsr3m9pbm7WHRhykEXKhTKCqDAAECBAgQIEBgToHWzX0rbYo2JJgwtpw/NjajfIcWyNfO3t4jkAdmz3Ut91Q5aXKCIZMW2FvYug5kPHMv3Vp/ybqa2mzNby+pH13Wd5X1y8tqIbAJgbzhNtFQjSRAgAABAgQIEDikQGYJLNXxHygVjb0hT/Dqq0r+zSwaeheBfB31bT01Z2bhJbP9eopZZXJmJq2yYRpFgMB2BQSwtjt2Wk6AAAECBAgsK6C2+wi8s1HtxzXSpkj6jJGFJMgleDUS68DZhgJUCWz1/UGAPZAJYO1hFPWBwMoEBLBWNiCaQ4AAgX0L6B0BAgQmEfi/JinlcSEJKDxOae8leNU+IpXAC4GhrwZ+oGTrm5VVDu1i2fLXIncxADpBYI8CAlh7HFV92reA3hEgQIAAgeMItJ5H9aGZuj/mq1yCVzPh76zYBK/67rPyMPdXdtZf3SFAgMAiAn0frItUfq9K1EuAAAECBAgQILAJgVbA6KUZWt4KlNXVJChRp9knUAvkWuq7x/pfSuZXy7qX5Vv30hH92LeA3u1HoO/DdT891BMCBAgQIECAAIEtCrS+0pfgwNR9+V9Lga1AWUl+tIyZofXoBDuHE8j12Xct/RtF4x8t6xaXvjZ/Xs8Bz7/qgZFMgMBtAgJYt/k5mwABAgQIECBAYB6BVsBojufq/NoRzb9x9tWIGmTZusBQ8OoLS+f+w7LubXEvubcR1R8CKxfwobPyAdI8AgQIECBA4OHhAcLRBN67UIdbs7xaVbeCaa180o4p0Be8ykykzMj6tp2ypG+trr3eSpRGgACBWwUEsG4VdD4BAgQ2IqCZBAgQ2JDAOxtt/b2NtFuTxgSmzL66VXnf53dBqrqXST/qvdY7agz7BAgQmELgqB+qU9gp43gCekyAAAECBAjcT+BrJq46s2bGFDkmyDWmHHn2J5AgVatXST/CfVbfDKyWiTQCBAjcLLDwB+vN7VUAAQIECBAgQIDAvgVea3TvQ420W5PG3HyPDXLd2hbnb08gQapWq5PuHqslI+2AArpMYFoBH67TeiqNAAECBAgQIEDgNoGXG6e/1Ei7JSlBhjHnz/HQ+DH1yrNegfeUpvVdPwl4Tnt/VSrb2NJns7FuaC4BAmsUOPoH7BrHRJsIECBAgAABAkcWGDMzarRPI+PYB7d79lUD7+BJmR34KT0GCV4JeD48xKGHSDIBAgRuExDAus3P2QQIECBAYO8C+kdgaYG5A1hjn2k1Nt/SPuq7j8AHS7VvL2trSbBT8OoNGe+bNxz8S4DADAICWDOgKpIAAQKPBewRIECAwEoExs4OGZtvJd3SjJkFMmvvbT11fKSkHzFo8/+VflsIECCwqIAA1qLcKrtawIkECBAgQIDAUQWmeqZOZtCMnd1lNs1Rr7an/R4KUCWw9danpxwi5d2H6KVOEiBwH4GeWgWwemAkEyBAgAABAgQIrEIgAYQpGtKaQdMKjpl9NYX2PspIgKovmPmB0sXWNVWSD7G0gsGt99MhMNbYSW0isEcBAaw9jqo+ESBAgAABAgS2KdAKVk0RJOi7sW7dhPcFk00cVAAAEABJREFULLYpqtXXCiSQ2ffVwDzM/ZVrC97xeX3vsx13WdcIEFhSQABrSW11ESBAgAABAgQOI3BVR+f43TSBiFZjWjfbfXlb50vbr0CujVZwMz1O+qvZOPgah5pA8LcWsU+AwKQCc/ySMGkDFUaAAAECBA4roOMEjifQuim+RSEzulpl5q/GtdLdgN+ivf1z85XBBK/6etK6ZvrySidAgACBiQUEsCYGVRwBAusS0BoCBAgQOKzAT5eetwJSCVC0fgdOUKucYjmoQGbf9X1lMCT/Sf6xEiBAgMD9BFo/vO/XGjWvUUCbCBAgQIAAAQL3Ekiw6dq6P6bnxPz+25pJMxS86ClK8g4Efrz0IddZ65oohx4S2MqxL8uOtVcghr0HHSBAYDMCq25ofoCvuoEaR4AAAQIECBAgcFiBa2+KE3RooSUQ0Tpm9lVLa/9p+YrpJwx080PlWGsWX0k+9PLXDt37s52XgQCBuQQEsOaSVS4BAgQIECBAgMAlAvnLbnX+P10njNhPMCqBqjprV37rmNlXtdY995epO4HMvuBUAqe/rTTjpbJangp88tOkh7zvGsmSCBAgMJ2AANZ0lkoiQIAAAQIECKxCYKONeLnR7i9ppA0lfbAcbP1+m4BE/nJcghYly6PFjfcjjt3vvLf0MNdDK5BZDj37ymCuoW/PjrUp0LJ7azOnRAIECEwokA/nCYtTFAECBAgQ2IWAThAgsLxA66b4klZ8bMn8trLWS4IV3e+8rTrMvqrF9rufYOU7B7r3gXKsb1ZWOWQhQIAAgXsKdD/M79kGdRMgsEsBnSJAgAABAhcJtIJLlxTwUz2Zh37fbc3I6ilG8sYFMtZ910KCnLn+Xtl4H5dqfqyWqks9BAgQeFOg70P8zQw27iigagIECBAgQIAAgTECCU608n1WK/EkzWybE4ydbr5e+tUFqMrmkyXXjnuiJywXJcT3ohNkJkCgISDprIAP67NEMhAgQIAAAQIECNxBYOxNcf6SXGtGSL4u9j/dod2qXI9AroHWs9W6Fr6vbAhiFoQbl7Hv1RurOX+6HAQI7FtAAGvf46t3BAgQIECAAIGtCiQwda7tP1MytAIQuaH2bKuCc+Gyp+xDM6tyfSTo+a49dXihvvxEo55YN5IlESBAYFoBAaxpPZVGgAABAgQIHFpA568UaAWrWg9kr4v/pXXC832/4z6HOOBL/hJlF6BqdT+zslwfLZlxafljCXVOf4GwFrFPgMAsAj68Z2FVKAECBAhcLeBEAgSOKHDN76R9sz4ys+aIhvr88JDg1FDg88cLkpl5BeGGxfvrBjynEiBwm8A1vyzcVqOzCRCYXUAFBAgQIEBgYwKX3hQnUNE657WN9VtzpxH4ylJMZl313dvkWK6XTyr5LLcJxPG2EpxNgACBKwX6PuSvLG43p+kIAQIECBAgQIDAOgU+XJrV+h02QYpXyzHLsQQ+VLr7dWXtWxLsbF0vffmlEyBwPAE93oiAD/ONDJRmEiBAgAABAgQOJJBgVKu7CV61vgKW/H6vbYntOy1fIx16/lJmC7Wul32rLNu7vPceHh6WrVRtBAgcU8AP+mOOu14TIECAAAECBNYs0Lop7gtepR/b/502vbBeIpBrJAGq1jlDx1r5pY0TaP2xhXFnykWAAIEJBPywnwBREQQIECBAgMD9BbRgswKvN1r+UVXaUPCqL4hRFWF3JwK5FhKg6utOgizucfp0bktvuQ6NxW21OZsAAQKVQOtDqMpilwABAgQOIqCbBAgQuIfAS2cqTcCi72tg33zmXIf3JZCvDPZdC+np15d/hr5SWA5bbhBoBYszJjcU6VQCBAiMFxDAGm8lJ4ERArIQIECAAAECFwq0boq7IoaCV99UMn1JWS37F/ix0sXM9Om7VrpjX1HyWZYV+Kxlq1MbAQJHFlhfAOvIo6HvBAgQIECAAAECncBQ8Cozr35nl9HrrgXylcBPHOhh/gqhe5oBoJkPff/M5St+7wL6R+ACAR/2F2DJSoAAAQIECBAgMLtAZtMMBa/+udICM68KwgGWfD2tfh5a1+1cJ59Qds59BbVk2feidwQIEDiKgADWUUZaPwkQIECAAAEC6xNoPcD9F0sz+55zlK+QfWs5PuWirPUJvLc0KQGqjHfZfLIksJX7mJ98ckQCAQIECOxWIB/8u+2cjhEgQIAAAQJLCKiDwNUCrdkzQ8Grqyty4mYEEsB850BrP1CO9c3KKocsCwokyLhgdaoiQODoAgJYR78C9J8AgXUIaAUBAgSOKdA3w6bWyDOv6jT7+xPoZla1epZgSa6XV1oHpREgQIDA/gUEsPY/xofpoY4SIECAAAECuxRI0MIzr3Y5tG92Kl8l7QJUbyaebAwFtk6y2VxYIGO2cJWqI0DgyAKnAawjO+g7AQIECBAgQIDA+gQ+eX1N0qKJBfKVwZcHynxfOeYrgwVhhUsCiytsliaNFJCNwOYEBLA2N2QaTIAAAQIECBA4hEBmXv3IzD11Az4z8Jni4993P5LZPbkG3nWmjDsePnzVbz28AAACBBYV6PuBsWgjVEaAAAECBAgQIHBAgf4uJ3DRf3S6I0vVM12L91HSB0s3ugBV2XyyZFaW+5QnLBIIECBwbAE/GI49/npPgAABAhsX0HwCOxPIjJw5g0opvyZrpdV57E8nkODU2waKy/j3/SXKgdMcIkCAAIG9Cwhg7X2E9Y8AgXMCjhMgQIDAegTmftZRq/wETNYjsO+WZNZV3/1HjhmL7Yx/xms7rdVSAgR2IdD3A2QXndOJpQTUQ4AAAQIECBC4SiABi9wIf6icne3yMvvSmnHVSpu9IQeqILOuMs59Xc5x9yV9OutJz3v0I6U5eb8arwJhIXBMgfv12gfP/ezVTIAAAQIECBAg8PCQ30dfWhDCLKxlsN9bqklgMIGrjHHZbS7fVVJ9ZbAgbGTJg9uXfL9uhOXCZspOgMBVAkM/TK4q0EkECBAgQIAAAQIEVi6QwErdxFZancf+eYHMpkrQ6p0l61vK2rckT47/1r4MQ+mOESBAgMDxBASwjjfmekyAAAECBAgQOLpA3ywsQazrroyxs6260vM1NPchnYZXAgQIEBgl4AfHKCaZCBAgQIBALWCfAIGNC7SCVZkR1ErfeFdna/7Y2VZdA7pZV/kaWpfmlQABAgQIjBIQwBrFJBMBArMIKJQAAQIECNxPoDULK61JECuBFs/5icbT9dLZVikhga64uveIhpUAAQIErhLwQ+QqtvWcpCUECBAgQIAAAQJXC/zQwJkfKMc+XFbLGwIJQiWwd+7ZVm/kfnhI3r9YdhK48pD2AmEhQIDArQJHP18A6+hXgP4TIECAAAECBI4r8Gml6wmwJNhSNp8sCbwkcPPkwEESbp1t9ZkHcdLN7QhoKQECGxYQwNrw4Gk6AQIECBAgQIDAJAL5nbjv2VdDxyapfIWFJGiXoF5jtlWztclrtlWTRiIBAgQITCWQH8hTlaUcAgQIECBAgACBcwKOr1Ugz8TKX8drta+bpfU3Wwd3kma21U4GUjcIECCwVwEBrL2OrH4RIEBgxwK6RoAAgZkE8tfxEqzqK/6XlQOZbZTZWln7Al4l22aWa2ZbvaP0Lk75imXZtBAgQIAAgfkFBLDmN1YDgTUKaBMBAgQIECDQL5DgTAJVfTlyPGtmbSXf6ZrAVtYEt769r4A7p9862+r9d26/6gkQIEDggAICWFcPuhMJECBAgAABAgR2LJDfkxOIurSLCWxlTXDr88vJfcGtcmi25UOl5MysSvu79bQdlzzbKn3JarZVQbUQIHBUAf1eg0B+MK+hHdpAgAABAgQIECBAYG0CCUJlJtVU7UogKGvKPQ0oZbsLNKW+b6sq/K1l/1xQKmV0a74Kmd/zU1e3liJGLwl+5byUMfokGQkMCjhIgACBGwX8ULoR0OkECBAgQIAAAQK7FkgwKMGcBJgSIJqrs6kja4JbX1AqSV3d+p1l/61lze/uydOtJWmyJXW9rZSWss22KhAWAgQIEFiXQH4IrqtFWkOAAAECBAgQmEdAqQRuEUhgKb87J8BzumZm1NzBrVvafe7c09lWHz6X2XECBAgQIHAvgfwQvlfd6iVAgACBzQloMAECBAhUAi+V/S0EtzLDqlsTcOuCcGZblQG0ECBAgMD6BQSw1j9GWrg3Af0hQIAAAQIEjiIwd3CrC0jlNUGpzKb64YLbBadOX/N7f7cm4FayWQgQIECAwHYE8kNsO6193lIvBAgQIECAAAECBDYuMCa41QWlTgNRp9v5Xb5bE5TKbKpfs3EXzSdAgMAjATsEOoH8wOu2vRIgQIAAAQIECBAgcH+BLrjVBaXu3yIt2LKAthMgQGAXAgJYuxhGnSBAgAABAgQIEJhPQMkECBAgQIDAvQUEsO49AuonQIAAAQJHENBHAgQIECBAgAABAjcICGDdgOdUAgQILCmgLgIECBAgQIAAAQIECBxVQADrqCN/zH7rNQECBAgQIECAAAECBAgQILBBgQsDWBvsoSYTIECAAAECBAgQIECAAAECFwrITmBdAgJY6xoPrSFAgAABAgQIECBAYC8C+kGAAAECkwkIYE1GqSACBAgQIECAAIGpBZRHgAABAgQIEIiAAFYUrAQIECBAYL8CekaAAAECBAgQIEBg8wICWJsfQh0gQGB+ATUQIECAAAECBAgQIECAwD0FBLDuqX+kuvWVAAECBAgQIECAAAECBAgQ2L/ATD0UwJoJVrEECBAgQIAAAQIECBAgQOAaAecQIPBUQADrqYkUAgQIECBAgAABAgS2LaD1BAgQILAzAQGsnQ2o7hAgQIAAAQIEphFQCgECBAgQIEBgPQICWOsZCy0hQIAAgb0J6A8BAgQIECBAgAABApMICGBNwqgQAgTmElAuAQIECBAgQIAAAQIECBAQwNr/NaCHBAgQIECAAAECBAgQIECAwP4Fdt1DAaxdD6/OESBAgAABAgQIECBAgMB4ATkJEFirgADWWkdGuwgQIECAAAECBAhsUUCbCRAgQIDADAICWDOgKpIAAQIECBAgcIuAcwkQIECAAAECBB4LCGA99rBHgAABAvsQ0AsCBAgQIECAAAECBHYkIIC1o8HUFQLTCiiNAAECBAgQIECAAAECBAisQ0AAa85xUDYBAgQIECBAgAABAgQIECCwfwE9nF1AAGt2YhUQIECAAAECBAgQIECAwDkBxwkQIDAkIIA1pOMYAQIECBAgQIAAge0IaCkBAgQIENitgADWbodWxwgQIECAAIHLBZxBgAABAgQIECCwRgEBrDWOijYRIEBgywLaToAAAQIECBAgQIAAgYkFBLAmBlUcgSkElEGAAAECBAgQIECAAAECBAi8ENhrAOtFD20RIECAAAECBAgQIECAAAECexXQr4MICGAdZKB1kwABAgQIECBAgAABAm0BqQQIEFi/gADW+sdICwkQIECAAAECBNYuoH0ECBAgQAsDOjwAAAsWSURBVIDArAICWLPyKpwAAQIECBAYKyAfAQIECBAgQIAAgT4BAaw+GekECBDYnoAWEyBAgAABAgQIECBAYJcCAli7HFadul7AmQQIECBAgAABAgQIECBAgMDaBKYPYK2th9pDgAABAgQIECBAgAABAgQITC+gRAILCghgLYitKgIECBAgQIAAAQIECJwK2CZAgACBcQICWOOc5CJAgAABAgQIEFingFYRIECAAAECBxAQwDrAIOsiAQIECBAYFnCUAAECBAgQIECAwLoFBLDWPT5aR4DAVgS0kwABAgQIECBAgAABAgRmExDAmo1WwZcKyE+AAAECBAgQIECAAAECBAjsX+CaHgpgXaPmHAIECBAgQIAAAQIECBAgcD8BNRM4nIAA1uGGXIcJECBAgAABAgQIEHh4YECAAAECWxIQwNrSaGkrAQIECBAgQGBNAtpCgAABAgQIEFhIQABrIWjVECBAgACBloA0AgQIECBAgAABAgTOCwhgnTeSgwCBdQtoHQECBAgQIECAAAECBAjsXEAAa+cDPK57chEgQIAAAQIECBAgQIAAAQL7F9huDwWwtjt2Wk6AAAECBAgQIECAAAECSwuojwCBuwgIYN2FXaUECBAgQIAAAQIEjiug5wQIECBA4FIBAaxLxeQnQIAAAQIECNxfQAsIECBAgAABAocSEMA61HDrLAECBAi8ELBFgAABAgQIECBAgMBWBASwtjJS2klgjQLaRIAAAQIECBAgQIAAAQIEFhAQwFoAeagKxwgQIECAAAECBAgQIECAAIH9C+jhbQICWLf5OZsAAQIECBAgQIAAAQIElhFQCwECBxYQwDrw4Os6AQIECBAgQIDA0QT0lwABAgQIbFNAAGub46bVBAgQIECAwL0E1EuAAAECBAgQILC4gADW4uQqJECAAAECBAgQIECAAAECBAgQuERAAOsSLXkJrEdASwgQIECAAAECBAgQIECAwGEEDhzAOswY6ygBAgQIECBAgAABAgQIEDiwgK7vQUAAaw+jqA8ECBAgQIAAAQIECBCYU0DZBAgQuLOAANadB0D1BAgQIECAAAECxxDQSwIECBAgQOB6AQGs6+2cSYAAAQIECCwroDYCBAgQIECAAIGDCghgHXTgdZsAgaMK6DcBAgQIECBAgAABAgS2JyCAtb0x0+J7C6ifAAECBAgQIECAAAECBAgQWFTgLgGsRXuoMgIECBAgQIAAAQIECBAgQOAuAiolMJWAANZUksohQIAAAQIECBAgQIDA9AJKJECAAIEiIIBVECwECBAgQIAAAQJ7FtA3AgQIECBAYOsCAlhbH0HtJ0CAAAECSwiogwABAgQIECBAgMAdBQSw7oivagIEjiWgtwQIECBAgAABAgQIECBwnYAA1nVuzrqPgFoJECBAgAABAgQIECBAgACB/Qs86aEA1hMSCQQIECBAgAABAgQIECBAYOsC2k9gXwICWPsaT70hQIAAAQIECBAgQGAqAeUQIECAwGoEBLBWMxQaQoAAAQIECBDYn4AeESBAgAABAgSmEBDAmkJRGQQIECBAYD4BJRMgQIAAAQIECBA4vIAA1uEvAQAEjiCgjwQIECBAgAABAgQIECCwZQEBrC2P3pJtVxcBAgQIECBAgAABAgQIECCwf4GV9lAAa6UDo1kECBAgQIAAAQIECBAgsE0BrSZAYHoBAazpTZVIgAABAgQIECBAgMBtAs4mQIAAAQKPBASwHnHYIUCAAAECBAjsRUA/CBAgQIAAAQL7ERDA2s9Y6gkBAgQITC2gPAIECBAgQIAAAQIEViEggLWKYdAIAvsV0DMCBAgQIECAAAECBAgQIHCrgADWrYLzn68GAgQIECBAgAABAgQIECBAYP8CejggIIA1gOMQAQIECBAgQIAAAQIECGxJQFsJENirgADWXkdWvwgQIECAAAECBAhcI+AcAgQIECCwQgEBrBUOiiYRIECAAAEC2xbQegIECBAgQIAAgWkFBLCm9VQaAQIECEwjoBQCBAgQIECAAAECBAi8KSCA9SaFDQJ7E9AfAgQIECBAgAABAgQIECCwDwEBrKFxdIwAAQIECBAgQIAAAQIECBDYv4Aerl5AAGv1Q6SBBAgQIECAAAECBAgQWL+AFhIgQGBOAQGsOXWVTYAAAQIECBAgQGC8gJwECBAgQIBAj4AAVg+MZAIECBAgQGCLAtpMgAABAgQIECCwRwEBrD2Oqj4RIEDgFgHnEiBAgAABAgQIECBAYGUCAlgrGxDN2YeAXhAgQIAAAQIECBAgQIAAAQLTCaw1gDVdD5VEgAABAgQIECBAgAABAgQIrFVAuwiMEhDAGsUkEwECBAgQIECAAAECBNYqoF0ECBDYv4AA1v7HWA8JECBAgAABAgTOCThOgAABAgQIrFpAAGvVw6NxBAgQIEBgOwJaSoAAAQIECBAgQGAuAQGsuWSVS4AAgcsFnEGAAAECBAgQIECAAAECDQEBrAaKpC0LaDsBAgQIECBAgAABAgQIECCwN4GnAay99VB/CBAgQIAAAQIECBAgQIAAgacCUghsSEAAa0ODpakECBAgQIAAAQIECKxLQGsIECBAYBkBAaxlnNVCgAABAgQIECDQFpBKgAABAgQIEDgrIIB1lkgGAgQIECCwdgHtI0CAAAECBAgQILBvAQGsfY+v3hEgMFZAPgIECBAgQIAAAQIECBBYrYAA1mqHZnsN02ICBAgQIECAAAECBAgQIEBg/wL36KEA1j3U1UmAAAECBAgQIECAAAECRxbQdwIELhQQwLoQTHYCBAgQIECAAAECBNYgoA0ECBAgcCQBAawjjba+EiBAgAABAgROBWwTIECAAAECBDYiIIC1kYHSTAIECBBYp4BWESBAgAABAgQIECAwv4AA1vzGaiBAYFjAUQIECBAgQIAAAQIECBAgMCgggDXIs5WD2kmAAAECBAgQIECAAAECBAjsX+C4PRTAOu7Y6zkBAgQIECBAgAABAgSOJ6DHBAhsUkAAa5PDptEECBAgQIAAAQIE7iegZgIECBAgsLSAANbS4uojQIAAAQIECDw8MCBAgAABAgQIELhAQADrAixZCRAgQGBNAtpCgAABAgQIECBAgMBRBASwjjLS+kmgJSCNAAECBAgQIECAAAECBAhsQEAA68ZBcjoBAgQIECBAgAABAgQIECCwfwE9vK+AANZ9/dVOgAABAgQIECBAgACBowjoJwECBK4WEMC6ms6JBAgQIECAAAECBJYWUB8BAgQIEDimgADWMcddrwkQIECAwHEF9JwAAQIECBAgQGBzAgJYmxsyDSZAgMD9BbSAAAECBAgQIECAAAECSwoIYC2prS4CLwRsESBAgAABAgQIECBAgAABAiMFNhzAGtlD2QgQIECAAAECBAgQIECAAIENC2g6gYcHASxXAQECBAgQIECAAAECBPYuoH8ECBDYuIAA1sYHUPMJECBAgAABAgSWEVALAQIECBAgcD8BAaz72auZAAECBAgcTUB/CRAgQIAAAQIECFwlIIB1FZuTCBAgcC8B9RIgQIAAAQIECBAgQOB4AgJYxxtzPSZAgAABAgQIECBAgAABAgQIbErgqgDWpnqosQQIECBAgAABAgQIECBAgMBVAk4isBYBAay1jIR2ECBAgAABAgQIECCwRwF9IkCAAIEJBASwJkBUBAECBAgQIECAwJwCyiZAgAABAgSOLiCAdfQrQP8JECBA4BgCekmAAAECBAgQIEBgwwICWBsePE0nQGBZAbURIECAAAECBAgQIECAwH0E/n8AAAD//7iYpDgAAAAGSURBVAMAsvOmikOrUacAAAAASUVORK5CYII=', '2026-06-02 13:39:02', '2026-06-02 13:39:55');
INSERT INTO `meeting_minutes` (`minute_id`, `proposal_id`, `recorded_by_user_id`, `meeting_date`, `meeting_time`, `venue`, `meeting_type`, `attendees`, `num_attendees`, `agenda`, `discussion_summary`, `decisions_made`, `action_items`, `next_meeting_date`, `is_reviewed`, `reviewed_by`, `reviewed_at`, `attachment_path`, `signature_data`, `created_at`, `updated_at`) VALUES
(7, NULL, 43, '2026-06-03', '09:30:00', 'Barangay Bayabas Health Center', 'Planning', '[{\"name\":\"Pedro Cruz\",\"role\":\"Punong Barangay \\/ BNC Chairperson\"},{\"name\":\"Alma Sedano\",\"role\":\"Committee on Health \\/ BNC Vice-Chairperson\"},{\"name\":\"Nancy Ongayo\",\"role\":\"Barangay Nutrition Scholar (BNS)\"},{\"name\":\"Teresa Dose\",\"role\":\"Barangay Health Worker\"}]', 4, 'Regular Meeting sa Barangay Nurtition Council', '1. Resulta sa Operation Timbang Plus (OPT+)\r\n•	Panaghisgot: Gi-report sa BNS ang resulta sa bag-ong screening diin naay 20 ka bata (11 ka lalaki ug 9 ka babaye) ang nakit-an nga malnourished o underweight.\r\n•	Lihok nga Pagahimoon: Nagkauyon ang konseho nga sugdan ang Supplementary Feeding Program para mahatagan og dinaliang tabang ang maong mga bata.\r\n2. Pag-review sa Project Proposal ug Budget\r\n•	Panaghisgot: Gipresentar sa Committee on Health ang plano para sa 120 ka adlaw nga feeding program. Ang budget kay ₱60.00 matag bata kada adlaw (Total: ₱144,000.00).\r\n•	Lihok nga Pagahimoon: Gi-aprobahan ang paggamit sa Barangay BCPC Fund alang niini nga proyekto kay kini alang man sa kaayohan sa mga bata.\r\n3. Seminar para sa mga Ginikanan\r\n•	Panaghisgot: Gihisgutan usab ang pagpahigayon og orientation para sa mga ginikanan bahin sa pagluto og sustansyadong pagkaon nga barato lang.', 'Feeding Program: Nagkasabot ang tanan nga ang feeding program pagahimoon sa Barangay Bayabas Health Center / Session Hall aron dali ra ma-monitor sa BNS ug BHW ang matag bata.\r\nSanitation: Gihisgutan usab ang pagsiguro nga kanunay limpyo dapit aron malikay sa sakit ang mga bata.', 'Committee on health will prepare feeding proposal for this one. \r\nTungod kay walay dili angay nga hisgutan ang meeting opisyal nga natapos sa 12 sa udto.', NULL, 1, 41, '2026-06-03 09:38:56', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABLAAAAGQCAYAAAC+tZleAAAQAElEQVR4AezdCbg1W1kf+GMi02VUAW3EWQE1ascxKlFxCNrOtjgQZ+M8D/HpVqMxDukEjXNHnOKDUdsJJZqEGEQRpTGmIx1EGdoRiAqIINO9QNq8/3u//X371LdqT2fX3jX87rPWV7tWrVrDr+rss+u9tev8jQv/ESBAgAABAgQIECBAgAABAnMXMD8CkxYQwJr04TN4AgQIECBAgAABAgROJ6AnAgQIEDiXgADWueT1S4AAAQIECBBYooA5EyBAgAABAgQOEBDAOgDNLgQIECBA4JwC+iZAgAABAgQIECCwNAEBrKUdcfMlQCACMgECBAgQIECAAAECBAhMSEAAa0IHa1xDNRoCBAgQIECAAAECBAgQIEBg/gLjmKEA1jiOg1EQIECAAAECBAgQIECAwFwFzIsAgSsLCGBdmVADBAgQIECAAAECBAgMLaB9AgQIEFi2gADWso+/2RMgQIAAAQLLETBTAgQIECBAgMBkBQSwJnvoDJwAAQIETi+gRwIECBAgQIAAAQIEziEggHUOdX0SWLKAuRMgQIAAAQIECBAgQIAAgT0FBLD2BBtDdWMgQIAAAQIECBAgQIAAAQIE5i9ghjcEBLBuWHhFgAABAgQIECBAgAABAvMSMBsCBGYiIIA1kwNpGgQIECBAgAABAgSGEdAqAQIECBA4v4AA1vmPgREQIECAAAECcxcwPwIECBAgQIAAgSsJCGBdic/OBAgQIHAqAf0QIECAAAECBAgQILBcAQGs5R57M1+egBkTIECAAAECBAgQIECAAIFJCghg7XXYVCZAgAABAgQIECBAgAABAgTmL2CGYxMQwBrbETEeAgQIECBAgAABAgQIzEHAHAgQIHBEAQGsI2JqigABAgQIECBAgMAxBbRFgAABAgQI3CEggHWHg38JECBAgACBeQqYFQECBAgQIECAwAwEBLBmcBBNgQABAsMKaJ0AAQIECBAgQIAAAQLnFRDAOq+/3pciYJ4ECBAgQIAAAQIECBAgQIDAwQKTCWAdPEM7EiBAgAABAgQIECBAgAABApMRMFACLQEBrJaKMgIECBAgQIAAAQIECExXwMgJECAwOwEBrNkdUhMiQIAAAQIECBC4uoAWCBAgQIAAgTEJCGCN6WgYCwECBAgQmJOAuRAgQIAAAQIECBA4koAA1pEgNUOAAIEhBLRJgAABAgQIECBAgAABAhcXAljOgrkLmB8BAgQIECBAgAABAgQIECAwcYEdAlgTn6HhEyBAgAABAgQIECBAgAABAjsIqEJgvAICWOM9NkZGgAABAgQIECBAgMDUBIyXAAECBAYREMAahFWjBAgQIECAAAEChwrYjwABAgQIECDQFRDA6opYJ0CAAAEC0xcwAwIECBAgQIAAAQKzEhDAmtXhNBkCBI4noCUCBAgQIECAAAECBAgQGIuAANZYjsQcx2FOBAgQIECAAAECBAgQIECAwPwFTjBDAawTIOuCAAECBAgQIECAAAECBAhsErCNAIHNAgJYm31sJUCAAAECBAgQIEBgGgJGSYAAAQIzFhDAmvHBNTUCBAgQIECAwH4CahMgQIAAAQIExikggDXO42JUBAgQIDBVAeMmQIAAAQIECBAgQODoAgJYRyfVIAECVxWwPwECBAgQIECAAAECBAgQWBcQwFrXmM9rMyFAgAABAgQIECBAgAABAgTmL7CYGQpgLeZQmygBAgQIECBAgAABAgQI3CyghACBKQgIYE3hKBkjAQIECBAgQIAAgTELGBsBAgQIEBhYQABrYGDNEyBAgAABAgR2EVCHAAECBAgQIECgX0AAq9/GFgIECBCYloDREiBAgAABAgQIECAwUwEBrJkeWNMicJiAvQgQIECAAAECBAgQIECAwPgEBLCOfUy0R4AAAQIECBAgQIAAAQIECMxfwAxPKiCAdVJunREgQIAAAQIECBAgQIDASsCSAAECuwoIYO0qpR4BAgQIECBAgACB8QkYEQECBAgQWISAANYiDrNJEiBAgAABAv0CthAgQIAAAQIECIxdQABr7EfI+AgQIDAFAWMkQIAAAQIECBAgQIDAgAICWAPiaprAPgLqEiBAgAABAgQIECBAgAABAm2BOQWw2jNUSoAAAQIECBAgQIAAAQIECMxJwFwWKCCAtcCDbsoECBAgQIAAAQIECCxdwPwJECAwLQEBrGkdL6MlQIAAAQIECBAYi4BxECBAgAABAicTEMA6GbWOCBAgQIAAga6AdQIECBAgQIAAAQK7CAhg7aKkDgECBMYrYGQECBAgQIAAAQIECBCYvYAA1uwPsQluF1CDAAECBAgQIECAAAECBAgQGLPAcQJYY56hsREgQIAAAQIECBAg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AGuMR8WYCBAgMGUBYydAgAABAgQIECBAgMCRBQSwjgyqOQLHENAGAQIECBAgQIAAAQIECBAgcENgrgGsGzP0igABAgQIECBAgAABAgQIEJirgHktREAAayEH2jQJECBAgAABAgQIECDQFlBKgACB8QsIYI3/GBkhAQIECBAgQIDA2AWMjwABAgQIEBhUQABrUF6NEyBAgAABArsKqEeAAAECBAgQIECgT0AAq09GOQECBKYnYMQECBAgQIAAAQIECBCYpYAA1iwPq0kdLmBPAgQIECBAgAABAgQIECBAYGwCxw9gjW2GxkOAAAECBAgQIECAAAECBAgcX0CLBE4oIIB1QmxdESBAgAABAgQIECBAYF3AawIECBDYTUAAazcntQgQIECAAAECBMYpYFQECBAgQIDAAgQEsBZwkE2RAAECBAhsFrCVAAECBAgQIECAwLgFBLDGfXyMjgCBqQgYJwECBAgQIECAAAECBAgMJiCANRithvcVUJ8AAQIECBAgQIAAAQIECBCYv8AhMxTAOkTNPgQIECBAgAABAgQIECBA4HwCeiawOAEBrMUdchMmQIAAAQIECBAgQODiggEBAgQITElAAGtKR8tYCRAgQIAAAQJjEjAWAgQIECBAgMCJBASwTgStGwIECBAg0BJQRoAAAQIECBAgQIDAdgEBrO1GahAgMG4BoyNAgAABAgQIECBAgACBmQsIYM38AO82PbUIECBAgAABAgQIECBAgACB+QtMd4YCWNM9dkZOgAABAgQIECBAgAABAqcW0B8BAmcREMA6C7tOCRAgQIAAAQIECCxXwMwJECBAgMC+AgJY+4qpT4AAAQIECBA4v4ARECBAgAABAgQWJSCAtajDbbIECBAgcEPAKwIECBAgQIAAAQIEpiIggDWVI2WcBMYoYEwECBAgQIAAAQIECBAgQOAEAgJYJ0De1IVtBAgQIECAAAECBAgQIECAwPwFzPBqAgJYV/OzNwECBAgQIECAAAECBAicRkAvBAgsWEAAa8EH39QJECBAgAABAgSWJmC+BAgQIEBgmgICWNM8bkZNgAABAgQInEtAvwQIECBAgAABAicXEMA6ObkOCRAgQIAAAQIECBAgQIAAAQIE9hEQwNpHS10C4xEwEgIECBAgQIAAAQIECBAgsBiBBQewFnOMTZQAAQIECBAgQIAAAQIECCxYwNTnICCANYejaA4ECBAgQIAAAQIECBAYUkDbBAgQOLOAANaZD4DuCRAgQIAAAQIEliFglgQIECBAgMDhAgJYh9vZkwABAgQIEDitgN4IECBAgAABAgQWKiCAtdADb9oECCxVwLwJECBAgAABAgQIECAwPQEBrOkdMyM+t4D+CRAgQIAAAQIECBAgQIAAgZMKnCWAddIZ6owAAQIECBAgQIAAAQIECBA4i4BOCRxLQADrWJLaIUCAAAECBAgQIECAwPEFtEiAAAECJSCAVQgSAQIECBAgQIDAnAXMjQABAgQIEJi6gADW1I+g8RMgQIAAgVMI6IMAAQIECBAgQIDAGQUEsM6Ir2sCBJYlYLYECBAgQIAAAQIECBAgcJiAANZhbvY6j4BeCRAgQIAAAQIECBAgQIAAgfkL3DRDAaybSBQQIECAAAECBAgQIECAAIGpCxg/gXkJCGDN63iaDQECBAgQIECAAAECxxLQDgECBAiMRkAAazSHwkAIECBAgAABAvMTMCMCBAgQIECAwDEEBLCOoagNAgQIECAwnICWCRAgQIAAAQIECCxeQABr8acAAAJLEDBHAgQIECBAgAABAgQIEJiygADWlI/eKceuLwIECBAgQIAAAQIECBAgQGD+AiOdoQDWSA+MYREgQIAAAQIECBAgQIDANAWMmgCB4wsIYB3fVIsECBAgQIAAAQIECFxNwN4ECBAgQOCSgADWJQ4rBAgQIECAAIG5CJgHAQIECBAgQGA+AgJY8zmWZkKAAAECxxbQHgECBAgQIECAAAECoxAQwBrFYTAIAvMVMDMCBAgQIECAAAECBAgQIHBVAQGsqwoOv78eCBAgQIAAAQIECBAgQIAAgfkLmOEGAQGsDTg2ESBAgAABAgQIECBAgMCUBIyVAIG5CghgzfXImhcBAgQIECBAgACBQwTsQ4AAAQIERigggDXCg2JIBAgQIECAwLQFjJ4AAQIECBAgQOC4AgJYx/XUGgECBAgcR0ArBAgQIECAAAECBAgQuC4ggHWdwgsCcxMwHwIECBAgQIAAAQIECBAgMA8BAaxNx9E2AgQIECBAgAABAgQIECBAYP4CZjh6AQGs0R8iAyRAgAABAgQIECBAgMD4BYyQAAECQwoIYA2pq20CBAgQIECAAAECuwuoSYAAAQIECPQICGD1wCgmQIAAAQIEpihgzAQIECBAgAABAnMUEMCa41E1JwIECFxFwL4ECBAgQIAAAQIECBAYmYAA1sgOiOHMQ8AsCBAgQIAAAQIECBAgQIAAgeMJjDWAdbwZaokAAQIECBAgQIAAAQIECBAYq4BxEdhJQABrJyaVCBAgQIAAAQIECBAgMFYB4yJAgMD8BQSw5n+MzZAAAQIECBAgQGCbgO0ECBAgQIDAqAUEsEZ9eAyOAAECBAhMR8BICRAgQIAAAQIECAwlIIA1lKx2CRAgsL+APQgQIECAAAECBAgQIECgISCA1UBRNGUBYydAgAABAgQIECBAgAABAgTmJnBzAGtuMzQfAgQIECBAgAABAgQIECBA4GYBJQQmJCCANaGDZagECBAgQIAAAQIECIxLwGgIECBA4DQCAlincdYLAQIECBAgQIBAW0ApAQIECBAgQGCrgADWViIVCBAgQIDA2AWMjwABAgQIECBAgMC8BQSw5n18zY4AgV0F1CNAgAABAgQIECBAgACB0QoIYI320EwbNAAtAAADLUlEQVRvYEZMgAABAgQIECBAgAABAgQIzF/gHDMUwDqHuj4JECBAgAABAgQIECBAYMkC5k6AwJ4CAlh7gqlOgAABAgQIECBAgMAYBIyBAAECBJYkIIC1pKNtrgQIECBAgACBdQGvCRAgQIAAAQITERDAmsiBMkwCBAgQGKeAUREgQIAAAQIECBAgMLyAANbwxnogQGCzgK0ECBAgQIAAAQIECBAgQGCjgADWRp6pbDROAgQIECBAgAABAgQIECBAYP4Cy52hANZyj72ZEyBAgAABAgQIECBAYHkCZkyAwCQFBLAmedgMmgABAgQIECBAgMD5BPRMgAABAgROLSCAdWpx/REgQIAAAQIELi4YECBAgAABAgQI7CEggLUHlqoECBAgMCYBYyFAgAABAgQIECBAYCkCAlhLOdLmSaAloIwAAQIECBAgQIAAAQIECExAQADrigfJ7gQIECBAgAABAgQIECBAgMD8BczwvAICWOf11zsBAgQIECBAgAABAgSWImCeBAgQOFhAAOtgOjsSIECAAAECBAgQOLWA/ggQIECAwDIFBLCWedzNmgABAgQILFfAzAkQIECAAAECBCYnIIA1uUNmwAQIEDi/gBEQIECAAAECBAgQIEDglAICWKfU1heBGwJeESBAgAABAgQIECBAgAABAjsKTDiAteMMVSNAgAABAgQIECBAgAABAgQmLGDoBC4uBLCcBQQIECBAgAABAgQIEJi7gPkRIEBg4gICWBM/gIZPgAABAgQIECBwGgG9ECBAgAABAucTEMA6n72eCRAgQIDA0gTMlwABAgQIECBAgMBBAgJYB7HZiQABAucS0C8BAgQIECBAgAABAgSWJyCAtbxjbsYECBAgQIAAAQIECBAgQIAAgUkJHBTAmtQMDZYAAQIECBAgQIAAAQIECBA4SMBOBMYiIIA1liNhHAQIECBAgAABAgQIzFHAnAgQIEDgCAICWEdA1AQBAgQIECBAgMCQAtomQIAAAQIEli4ggLX0M8D8CRAgQGAZAmZJgAABAgQIECBAYMICAlgTPniGToDAaQX0RoAAAQIECBAgQIAAAQLnEfgfAAAA//9X1dlBAAAABklEQVQDAK5m0agXom1OAAAAAElFTkSuQmCC', '2026-06-03 09:38:23', '2026-06-03 09:38:56');
INSERT INTO `meeting_minutes` (`minute_id`, `proposal_id`, `recorded_by_user_id`, `meeting_date`, `meeting_time`, `venue`, `meeting_type`, `attendees`, `num_attendees`, `agenda`, `discussion_summary`, `decisions_made`, `action_items`, `next_meeting_date`, `is_reviewed`, `reviewed_by`, `reviewed_at`, `attachment_path`, `signature_data`, `created_at`, `updated_at`) VALUES
(8, NULL, 43, '2026-06-03', '09:30:00', 'Barangay Bayabas Health Center', 'Planning', '[{\"name\":\"Pedro Cruz\",\"role\":\"Punong Barangay \\/ BNC Chairperson\"},{\"name\":\"Alma Sedano\",\"role\":\"Committee on Health \\/ BNC Vice-Chairperson\"},{\"name\":\"Nancy Ongayo\",\"role\":\"Barangay Nutrition Scholar (BNS)\"}]', 3, 'Barangay Nutrition Council meeting para sa Supplementary Feeding Program', '1. Resulta sa Operation Timbang Plus (OPT+)\r\n•	Panaghisgot: Gi-report sa BNS ang resulta sa bag-ong screening diin naay 7 ka bata (11 ka lalaki ug 9 ka babaye) ang nakit-an nga malnourished o underweight.\r\n•	Lihok nga Pagahimoon: Nagkauyon ang konseho nga sugdan ang Supplementary Feeding Program para mahatagan og dinaliang tabang ang maong mga bata.\r\n2. Pag-review sa Project Proposal ug Budget\r\n•	Panaghisgot: Gipresentar sa Committee on Health ang plano para sa 10 ka adlaw nga feeding program. Ang budget kay ₱60.00 matag bata kada adlaw (Total: ₱144,000.00).\r\n•	Lihok nga Pagahimoon: Gi-aprobahan ang paggamit sa Barangay BCPC Fund alang niini nga proyekto kay kini alang man sa kaayohan sa mga bata.\r\n3. Seminar para sa mga Ginikanan\r\n•	Panaghisgot: Gihisgutan usab ang pagpahigayon og orientation para sa mga ginikanan bahin sa pagluto og sustansyadong pagkaon nga barato lang.', 'Feeding Program: Nagkasabot ang tanan nga ang feeding program pagahimoon sa Barangay Bayabas Health Center / Session Hall aron dali ra ma-monitor sa BNS ug BHW ang matag bata.\r\nSanitation: Gihisgutan usab ang pagsiguro nga kanunay limpyo dapit aron malikay sa sakit ang mga bata.', 'Committee on health will prepare feeding proposal for this one. \r\nTungod kay walay dili angay nga hisgutan ang meeting opisyal nga natapos sa 12 sa udto.', NULL, 1, 41, '2026-06-03 10:04:05', NULL, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAABLAAAAGQCAYAAAC+tZleAAAQAElEQVR4AezdCdw12V0X+IeRpDudkA0TwicGCIGAIiIxIwmQALIPjAiDIAgIIosSyOAgAwLiACIOy0SCIsgSFATFQRiHTUGFBBLZPhBZAhFIMmEJZIOs3Ykz8/91v/ft+9R76q51763l259z3rp16tSpc751t/PvuvX8d1f+I0CAAAECBAgQIECAAAECBOYuYHwEJi0ggDXp06fzBAgQIECAAAECBAicT8CRCBAgQOBSAgJYl5J3XAIECBAgQIDAEgWMmQABAgQIECBwgIAA1gFodiFAgAABApcUcGwCBAgQIECAAAECSxMQwFraGTdeAgQiIBMgQIAAAQIECBAgQIDAhAQEsCZ0ssbVVb0hQIAAAQIECBAgQIAAAQIE5i8wjhEKYI3jPOgFAQIECBAgQIAAAQIECMxVwLgIEDhaQADraEINECBAgAABAgQIECBwagHtEyBAgMCyBQSwln3+jZ4AAQIECBBYjoCREiBAgAABAgQmKyCANdlTp+MECBAgcH4BRyRAgAABAgQIECBA4BICAliXUHdMAksWMHYCBAgQIECAAAECBAgQILCngADWnmBjqK4PBAgQIECAAAECBAgQIECAwPwFjPBeAQGsey08IkCAAAECBAgQIECAAIF5CRgNAQIzERDAmsmJNAwCBAgQIECAAAECpxHQKgECBAgQuLyAANblz4EeECBAgAABAnMXMD4CBAgQIECAAIGjBASwjuKzMwECBAicS8BxCBAgQIAAAQIECBBYroAA1nLPvZEvT8CICRAgQIAAAQIECBAgQIDAJAUEsPY6bSoTIECAAAECBAgQIECAAAEC8xcwwrEJCGCN7YzoDwECBAgQIECAAAECBOYgYAwECBAYUEAAa0BMTREgQIAAAQIECBAYUkBbBAgQIECAwD0CAlj3OPiXAAECBAgQmKeAUREgQIAAAQIECMxAQABrBifREAgQIHBaAa0TIECAAAECBAgQIEDgsgICWJf1d/SlCBgnAQIECBAgQIAAAQIECBAgcLDAZAJYB4/QjgQIECBAgAABAgQIECBAgMBkBHSUQEtAAKuloowAAQIECBAgQIAAAQLTFdBzAgQIzE5AAGt2p9SACBAgQIAAAQIEjhfQAgECBAgQIDAmAQGsMZ0NfSFAgAAB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', '2026-06-03 10:03:37', '2026-06-03 10:04:05');

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_visits`
--

CREATE TABLE `monitoring_visits` (
  `visit_id` int(10) UNSIGNED NOT NULL,
  `assessment_id` int(10) UNSIGNED NOT NULL,
  `visit_month_number` tinyint(3) UNSIGNED NOT NULL COMMENT '1 to 6',
  `visit_date` date DEFAULT NULL,
  `intervention_done` text DEFAULT NULL,
  `nutritional_status` varchar(20) DEFAULT NULL,
  `recorded_by` int(11) NOT NULL COMMENT 'bns_id',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `monitoring_visits`
--

INSERT INTO `monitoring_visits` (`visit_id`, `assessment_id`, `visit_month_number`, `visit_date`, `intervention_done`, `nutritional_status`, `recorded_by`, `created_at`) VALUES
(2, 20, 1, '2026-06-02', 'Vitamin A', 'UW', 1, '2026-06-02 05:14:18'),
(3, 35, 1, '2026-06-02', 'Deworming', 'SUW', 1, '2026-06-02 13:31:40');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `action_type`, `reference_id`, `message`, `is_read`, `created_at`) VALUES
(522, 1, 'profile_completed', 129, 'Alex Dove has completed and submitted their family profile. It has been automatically validated.', 1, '2026-06-01 15:08:40'),
(523, 1, 'profile_submitted', 132, 'Maria Brown has submitted their family profile for your validation. Please review it in Profile Validation.', 1, '2026-06-01 16:14:57'),
(524, 47, 'profile_returned', 132, 'Your family profile was returned for correction by your BNS. Reason: Breastfeeding Status', 1, '2026-06-01 16:16:35'),
(525, 1, 'profile_completed', 132, 'Maria Brown has completed and submitted their family profile. It has been automatically validated.', 1, '2026-06-01 16:17:02'),
(526, 1, 'profile_submitted', 135, 'Maria Brown has submitted their family profile for your validation. Please review it in Profile Validation.', 1, '2026-06-01 16:42:11'),
(527, 48, 'profile_returned', 135, 'Your family profile was returned for correction by your BNS. Reason: Monthly Income', 1, '2026-06-01 16:43:05'),
(528, 1, 'profile_completed', 135, 'Maria Brown has completed and submitted their family profile. It has been automatically validated.', 1, '2026-06-01 16:43:42'),
(529, 1, 'profile_submitted', 138, 'Maria Brown has submitted their family profile for your validation. Please review it in Profile Validation.', 1, '2026-06-01 16:58:57'),
(530, 49, 'profile_returned', 138, 'Your family profile was returned for correction by your BNS. Reason: Monthly Income', 0, '2026-06-01 16:59:34'),
(531, 1, 'profile_completed', 138, 'Maria Brown has completed and submitted their family profile. It has been automatically validated.', 1, '2026-06-01 17:00:07'),
(532, 1, 'profile_submitted', 141, 'Maria Brown has submitted their family profile for your validation. Please review it in Profile Validation.', 1, '2026-06-01 17:19:12'),
(533, 50, 'profile_returned', 141, 'Your family profile was returned for correction by your BNS. Reason: Monthly Income', 1, '2026-06-01 17:19:45'),
(534, 1, 'profile_completed', 141, 'Maria Brown has completed and submitted their family profile. It has been automatically validated.', 1, '2026-06-01 17:20:25'),
(535, 1, 'profile_submitted', 144, 'Maria Brown has submitted their family profile for your validation. Please review it in Profile Validation.', 1, '2026-06-02 03:55:35'),
(536, 51, 'profile_returned', 144, 'Your family profile was returned for correction by your BNS. Reason: salary', 1, '2026-06-02 03:56:21'),
(537, 1, 'profile_completed', 144, 'Maria Brown has completed and submitted their family profile. It has been automatically validated.', 1, '2026-06-02 03:57:13'),
(538, 30, 'report_submitted', 6, 'Nancy Ongayo has submitted the Monthly Accomplishment Report for June 2026.', 0, '2026-06-02 04:54:05'),
(539, 1, 'report_returned', 6, 'Your Monthly Accomplishment Report for June 2026 was returned for correction: Review it incomplete', 1, '2026-06-02 04:54:33'),
(540, 30, 'report_submitted', 6, 'Nancy Ongayo has resubmitted the corrected Monthly Accomplishment Report for June 2026.', 0, '2026-06-02 04:55:01'),
(541, 1, 'report_approved', 6, 'Your Monthly Accomplishment Report for June 2026 has been approved.', 1, '2026-06-02 04:56:03'),
(542, 46, 'nutrition_education', 9, 'New nutrition education session: \"Pinggang Pinoy Session\" on June 3, 2026 at 8:30 AM. Venue: Health Center. Topic: Pinggang Pinoy. For: All families.', 1, '2026-06-02 04:59:38'),
(543, 51, 'nutrition_education', 9, 'New nutrition education session: \"Pinggang Pinoy Session\" on June 3, 2026 at 8:30 AM. Venue: Health Center. Topic: Pinggang Pinoy. For: All families.', 1, '2026-06-02 04:59:38'),
(544, 1, 'session_rsvp', 9, 'Alex Dove confirmed attendance for \"Pinggang Pinoy Session\" on June 3, 2026. Total confirmed: 1.', 1, '2026-06-02 05:00:36'),
(545, 41, 'meeting_minutes_added', 5, 'New meeting minutes recorded: \"Barangay Nutrition Council meeting para sa Supplementary Feeding Program\" on May 10, 2026. Please review and create a feeding program proposal if needed.', 0, '2026-06-02 05:35:09'),
(546, 42, 'feeding_proposal_submitted', 7, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 20, Budget: ₱144,000.00', 1, '2026-06-02 05:41:06'),
(547, 41, 'feeding_proposal_needs_revision', 7, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Needs Revision by the Barangay Captain.', 0, '2026-06-02 05:41:41'),
(548, 41, 'feeding_proposal_needs_revision', 7, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Needs Revision by the Barangay Captain.', 0, '2026-06-02 05:41:41'),
(549, 42, 'feeding_proposal_submitted', 7, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 20, Budget: ₱168,000.00', 1, '2026-06-02 05:43:13'),
(550, 41, 'feeding_proposal_approved', 7, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-02 05:44:04'),
(551, 41, 'feeding_proposal_approved', 7, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-02 05:44:04'),
(552, 41, 'feeding_program_approved', 7, 'Feeding program \"Supplementary Feeding Program for Malnourished Children\" has been approved. You can now conduct feeding sessions.', 0, '2026-06-02 05:44:04'),
(553, 46, 'nutrition_education', 10, 'New nutrition education session: \"Pinggang Pinoy Session\" on June 3, 2026 at 9:30 AM. Venue: Health Center. Topic: Pinggang Pinoy. For: All families.', 1, '2026-06-02 06:39:44'),
(554, 51, 'nutrition_education', 10, 'New nutrition education session: \"Pinggang Pinoy Session\" on June 3, 2026 at 9:30 AM. Venue: Health Center. Topic: Pinggang Pinoy. For: All families.', 1, '2026-06-02 06:39:44'),
(555, 1, 'session_rsvp', 10, 'Alex Dove confirmed attendance for \"Pinggang Pinoy Session\" on June 3, 2026. Total confirmed: 1.', 1, '2026-06-02 06:40:01'),
(556, 51, 'nutrition_education', 11, 'New nutrition education session: \"Nutrition for Pregnant Women Session\" on June 3, 2026 at 9:30 AM. Venue: Health Center. Topic: Nutrition for Pregnant Women. For: Pregnant women.', 1, '2026-06-02 10:19:03'),
(557, 1, 'profile_completed', 148, 'Alfred Tiago has completed and submitted their family profile. It has been automatically validated.', 1, '2026-06-02 12:13:16'),
(558, 1, 'profile_submitted', 151, 'Erza Rhias has submitted their family profile for your validation. Please review it in Profile Validation.', 1, '2026-06-02 12:22:25'),
(559, 53, 'profile_returned', 151, 'Your family profile was returned for correction by your BNS. Reason: Monthly Income', 0, '2026-06-02 12:23:24'),
(560, 1, 'profile_completed', 151, 'Erza Rhias has completed and submitted their family profile. It has been automatically validated.', 1, '2026-06-02 12:24:16'),
(561, 1, 'profile_submitted', 154, 'Erza Rhias has submitted their family profile for your validation. Please review it in Profile Validation.', 1, '2026-06-02 12:44:42'),
(562, 54, 'profile_returned', 154, 'Your family profile was returned for correction by your BNS. Reason: Breastfeeding Status', 1, '2026-06-02 13:01:25'),
(563, 1, 'profile_submitted', 154, 'Erza Rhias has resubmitted their corrected family profile. Please review it in Profile Validation.', 1, '2026-06-02 13:05:09'),
(564, 54, 'profile_returned', 154, 'Your family profile was returned for correction by your BNS. Reason: Couple Practice Family Planning', 1, '2026-06-02 13:05:47'),
(565, 1, 'profile_submitted', 154, 'Erza Rhias has resubmitted their corrected family profile. Please review it in Profile Validation.', 1, '2026-06-02 13:06:58'),
(566, 54, 'profile_returned', 154, 'Your family profile was returned for correction by your BNS. Reason: Breastfeeding Status', 1, '2026-06-02 13:13:06'),
(567, 1, 'profile_submitted', 154, 'Erza Rhias has resubmitted their corrected family profile. Please review it in Profile Validation.', 1, '2026-06-02 13:13:57'),
(568, 54, 'profile_validated', 154, 'Your family profile has been validated by your BNS.', 1, '2026-06-02 13:14:16'),
(569, 46, 'nutrition_education', 12, 'New nutrition education session: \"Pinggang Pinoy Session\" on June 3, 2026 at 9:30 AM. Venue: Health Center. Topic: Pinggang Pinoy. For: All families.', 1, '2026-06-02 13:24:05'),
(570, 51, 'nutrition_education', 12, 'New nutrition education session: \"Pinggang Pinoy Session\" on June 3, 2026 at 9:30 AM. Venue: Health Center. Topic: Pinggang Pinoy. For: All families.', 0, '2026-06-02 13:24:05'),
(571, 52, 'nutrition_education', 12, 'New nutrition education session: \"Pinggang Pinoy Session\" on June 3, 2026 at 9:30 AM. Venue: Health Center. Topic: Pinggang Pinoy. For: All families.', 1, '2026-06-02 13:24:05'),
(572, 54, 'nutrition_education', 12, 'New nutrition education session: \"Pinggang Pinoy Session\" on June 3, 2026 at 9:30 AM. Venue: Health Center. Topic: Pinggang Pinoy. For: All families.', 1, '2026-06-02 13:24:05'),
(573, 1, 'session_rsvp', 12, 'Alfred Tiago confirmed attendance for \"Pinggang Pinoy Session\" on June 3, 2026. Total confirmed: 1.', 1, '2026-06-02 13:25:25'),
(574, 1, 'session_rsvp', 12, 'Erza Rhias confirmed attendance for \"Pinggang Pinoy Session\" on June 3, 2026. Total confirmed: 2.', 1, '2026-06-02 13:25:33'),
(575, 41, 'meeting_minutes_added', 6, 'New meeting minutes recorded: \"Barangay Nutrition Council meeting para sa Supplementary Feeding Program\" on May 29, 2026. Please review and create a feeding program proposal if needed.', 0, '2026-06-02 13:39:02'),
(576, 42, 'feeding_proposal_submitted', 8, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 20, Budget: ₱168,024.00', 1, '2026-06-02 13:45:24'),
(577, 41, 'feeding_proposal_approved', 8, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-02 13:47:03'),
(578, 41, 'feeding_proposal_approved', 8, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-02 13:47:03'),
(579, 41, 'feeding_program_approved', 8, 'Feeding program \"Supplementary Feeding Program for Malnourished Children\" has been approved. You can now conduct feeding sessions.', 0, '2026-06-02 13:47:03'),
(580, 41, 'recovery_validated', 1, 'Recovery validation completed for Peace, Nel Bin in Supplementary Feeding Program for Malnourished Children. Status: No Progress', 0, '2026-06-02 18:08:18'),
(581, 41, 'meeting_minutes_added', 7, 'New meeting minutes recorded: \"Regular Meeting sa Barangay Nurtition Council\" on June 3, 2026. Please review and create a feeding program proposal if needed.', 0, '2026-06-03 09:38:23'),
(582, 42, 'feeding_proposal_submitted', 9, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 10, Budget: ₱7,500.00', 1, '2026-06-03 09:44:22'),
(583, 41, 'feeding_proposal_needs_revision', 9, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Needs Revision by the Barangay Captain.', 0, '2026-06-03 09:44:58'),
(584, 41, 'feeding_proposal_needs_revision', 9, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Needs Revision by the Barangay Captain.', 0, '2026-06-03 09:44:58'),
(585, 42, 'feeding_proposal_submitted', 9, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 10, Budget: ₱8,500.00', 1, '2026-06-03 09:45:51'),
(586, 41, 'feeding_proposal_approved', 9, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-03 09:46:26'),
(587, 41, 'feeding_proposal_approved', 9, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-03 09:46:26'),
(588, 41, 'feeding_program_approved', 9, 'Feeding program \"Supplementary Feeding Program for Malnourished Children\" has been approved. You can now conduct feeding sessions.', 0, '2026-06-03 09:46:26'),
(589, 41, 'recovery_validated', 2, 'Recovery validation completed for Rhias, Rence Ong in Supplementary Feeding Program for Malnourished Children. Status: No Progress', 0, '2026-06-03 09:56:12'),
(590, 41, 'meeting_minutes_added', 8, 'New meeting minutes recorded: \"Barangay Nutrition Council meeting para sa Supplementary Feeding Program\" on June 3, 2026. Please review and create a feeding program proposal if needed.', 0, '2026-06-03 10:03:37'),
(591, 42, 'feeding_proposal_submitted', 10, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 7, Budget: ₱5,598.60', 1, '2026-06-03 10:06:54'),
(592, 41, 'feeding_proposal_needs_revision', 10, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Needs Revision by the Barangay Captain.', 0, '2026-06-03 10:07:15'),
(593, 41, 'feeding_proposal_needs_revision', 10, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Needs Revision by the Barangay Captain.', 0, '2026-06-03 10:07:15'),
(594, 42, 'feeding_proposal_submitted', 10, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 7, Budget: ₱6,298.60', 1, '2026-06-03 10:07:53'),
(595, 41, 'feeding_proposal_approved', 10, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-03 10:08:32'),
(596, 41, 'feeding_proposal_approved', 10, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-03 10:08:32'),
(597, 41, 'feeding_program_approved', 10, 'Feeding program \"Supplementary Feeding Program for Malnourished Children\" has been approved. You can now conduct feeding sessions.', 0, '2026-06-03 10:08:32'),
(598, 41, 'feeding_rsvp_declined', 430, 'Rhias, Rence Ong declined attendance for June 9, 2026 (Session: Supplementary feeding). Reason: gikalinyura', 0, '2026-06-08 17:43:54'),
(599, 41, 'recovery_validated', 3, 'Recovery validation completed for Dove, Bia Tan in Supplementary Feeding Program for Malnourished Children. Status: Recovered', 0, '2026-06-09 11:32:36'),
(600, 41, 'recovery_validated', 4, 'Recovery validation completed for Rhias, Rence Ong in Supplementary Feeding Program for Malnourished Children. Status: No Progress', 0, '2026-06-09 11:34:20'),
(601, 42, 'feeding_proposal_submitted', 11, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 6, Budget: ₱3,570.00', 1, '2026-06-16 07:32:44'),
(602, 41, 'feeding_proposal_approved', 11, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-16 07:33:06'),
(603, 41, 'feeding_proposal_approved', 11, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-16 07:33:06'),
(604, 41, 'feeding_program_approved', 11, 'Feeding program \"Supplementary Feeding Program for Malnourished Children\" has been approved. You can now conduct feeding sessions.', 0, '2026-06-16 07:33:06'),
(605, 41, 'recovery_validated', 5, 'Recovery validation completed for Brown, Lea Evan in Supplementary Feeding Program for Malnourished Children. Status: Recovered', 0, '2026-06-16 09:47:19'),
(606, 41, 'recovery_validated', 6, 'Recovery validation completed for Peace, Nel Bin in Supplementary Feeding Program for Malnourished Children. Status: Deteriorating', 0, '2026-06-16 15:01:17'),
(607, 41, 'recovery_validated', 7, 'Recovery validation completed for Dove, Bia Tan in Supplementary Feeding Program for Malnourished Children. Status: Recovered', 0, '2026-06-16 15:03:24'),
(608, 42, 'feeding_proposal_submitted', 12, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 6, Budget: ₱0.00', 0, '2026-06-17 09:37:08'),
(609, 41, 'feeding_proposal_needs_revision', 12, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Needs Revision by the Barangay Captain.', 0, '2026-06-17 09:38:04'),
(610, 41, 'feeding_proposal_needs_revision', 12, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Needs Revision by the Barangay Captain.', 0, '2026-06-17 09:38:04'),
(611, 42, 'feeding_proposal_submitted', 12, 'New feeding program proposal submitted for review: \"Supplementary Feeding Program for Malnourished Children\". Beneficiaries: 6, Budget: ₱3,150.00', 0, '2026-06-17 09:39:47'),
(612, 41, 'feeding_proposal_approved', 12, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-17 09:40:29'),
(613, 41, 'feeding_proposal_approved', 12, 'Your feeding program proposal \"Supplementary Feeding Program for Malnourished Children\" has been Approved by the Barangay Captain.', 0, '2026-06-17 09:40:29'),
(614, 41, 'feeding_program_approved', 12, 'Feeding program \"Supplementary Feeding Program for Malnourished Children\" has been approved. You can now conduct feeding sessions.', 0, '2026-06-17 09:40:29'),
(615, 41, 'recovery_validated', 8, 'Recovery validation completed for Rhias, Rence Ong in Supplementary Feeding Program for Malnourished Children. Status: No Progress', 0, '2026-06-17 09:44:47'),
(616, 41, 'recovery_validated', 9, 'Recovery validation completed for Dove, Niel Tan in Supplementary Feeding Program for Malnourished Children. Status: No Progress', 0, '2026-06-22 10:06:16'),
(617, 41, 'recovery_validated', 10, 'Recovery validation completed for Brown, Lea Evan in Supplementary Feeding Program for Malnourished Children. Status: Deteriorating', 0, '2026-06-23 02:38:59'),
(618, 41, 'recovery_validated', 11, 'Recovery validation completed for Brown, Lea Evan in Supplementary Feeding Program for Malnourished Children. Status: Deteriorating', 0, '2026-06-24 09:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `nutritional_recovery_validations`
--

CREATE TABLE `nutritional_recovery_validations` (
  `validation_id` int(11) NOT NULL,
  `child_id` int(11) DEFAULT NULL COMMENT 'If child has user account',
  `fm_member_id` int(11) DEFAULT NULL COMMENT 'Family member ID if from family profile',
  `full_name` varchar(255) NOT NULL COMMENT 'Child full name',
  `proposal_id` int(11) NOT NULL COMMENT 'Which feeding program',
  `baseline_assessment_id` int(11) DEFAULT NULL COMMENT 'Initial nutrition assessment',
  `baseline_date` date NOT NULL,
  `baseline_weight_kg` decimal(5,2) DEFAULT NULL,
  `baseline_height_cm` decimal(5,2) DEFAULT NULL,
  `baseline_muac_cm` decimal(4,2) DEFAULT NULL,
  `baseline_bmi` decimal(4,2) DEFAULT NULL,
  `baseline_wfa_status` varchar(20) DEFAULT NULL COMMENT 'Weight-for-Age status',
  `baseline_hfa_status` varchar(20) DEFAULT NULL COMMENT 'Height-for-Age status',
  `baseline_wfh_status` varchar(20) DEFAULT NULL COMMENT 'Weight-for-Height status',
  `baseline_bmi_status` varchar(20) DEFAULT NULL COMMENT 'BMI-for-Age status',
  `followup_assessment_id` int(11) DEFAULT NULL COMMENT 'Follow-up nutrition assessment',
  `followup_date` date NOT NULL,
  `followup_weight_kg` decimal(5,2) DEFAULT NULL,
  `followup_height_cm` decimal(5,2) DEFAULT NULL,
  `followup_muac_cm` decimal(4,2) DEFAULT NULL,
  `followup_bmi` decimal(4,2) DEFAULT NULL,
  `followup_wfa_status` varchar(20) DEFAULT NULL,
  `followup_hfa_status` varchar(20) DEFAULT NULL,
  `followup_wfh_status` varchar(20) DEFAULT NULL,
  `followup_bmi_status` varchar(20) DEFAULT NULL,
  `recovery_status` enum('Recovered','Improving','No Progress','Deteriorating') NOT NULL,
  `weight_gain_kg` decimal(5,2) DEFAULT NULL COMMENT 'Weight gained during program',
  `height_gain_cm` decimal(5,2) DEFAULT NULL COMMENT 'Height gained during program',
  `muac_gain_cm` decimal(4,2) DEFAULT NULL COMMENT 'MUAC gained during program',
  `days_in_program` int(11) DEFAULT NULL COMMENT 'Number of days in feeding program',
  `attendance_rate` decimal(5,2) DEFAULT NULL COMMENT 'Percentage of sessions attended',
  `validated_by_user_id` int(11) NOT NULL COMMENT 'Nutrition Officer who validated',
  `validation_date` datetime NOT NULL DEFAULT current_timestamp(),
  `remarks` text DEFAULT NULL COMMENT 'Additional observations or recommendations',
  `recommendation` text DEFAULT NULL COMMENT 'Next steps or continued intervention needed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nutritional_recovery_validations`
--

INSERT INTO `nutritional_recovery_validations` (`validation_id`, `child_id`, `fm_member_id`, `full_name`, `proposal_id`, `baseline_assessment_id`, `baseline_date`, `baseline_weight_kg`, `baseline_height_cm`, `baseline_muac_cm`, `baseline_bmi`, `baseline_wfa_status`, `baseline_hfa_status`, `baseline_wfh_status`, `baseline_bmi_status`, `followup_assessment_id`, `followup_date`, `followup_weight_kg`, `followup_height_cm`, `followup_muac_cm`, `followup_bmi`, `followup_wfa_status`, `followup_hfa_status`, `followup_wfh_status`, `followup_bmi_status`, `recovery_status`, `weight_gain_kg`, `height_gain_cm`, `muac_gain_cm`, `days_in_program`, `attendance_rate`, `validated_by_user_id`, `validation_date`, `remarks`, `recommendation`, `created_at`, `updated_at`) VALUES
(1, 0, 255, 'Peace, Nel Bin', 8, 34, '2026-06-02', 16.10, 103.30, 12.90, NULL, 'Normal', 'Normal', 'SAM', '', 41, '2026-06-03', 17.00, 103.00, 12.90, NULL, 'Normal', 'Normal', 'SAM', '', 'No Progress', 0.90, -0.30, NULL, 120, 8.33, 30, '2026-06-03 02:08:18', '', 'Continue supplementary feeding; BNS home visit; re-assess in 2–4 weeks; nutrition counseling for caregiver.', '2026-06-02 18:08:18', '2026-06-02 18:08:18'),
(2, 42, 0, 'Rhias, Rence Ong', 9, 35, '2026-05-02', 8.60, 96.00, 11.50, NULL, 'SUW', 'Normal', 'SAM', '', 42, '2026-06-03', 8.90, 96.00, 11.50, NULL, 'SUW', 'Normal', 'SAM', '', 'No Progress', 0.30, 0.00, NULL, 10, 12.50, 30, '2026-06-03 17:56:12', '', 'Give this children the more protein.', '2026-06-03 09:56:12', '2026-06-03 09:56:12'),
(3, 20, 0, 'Dove, Bia Tan', 10, 19, '2026-06-02', 8.50, 72.50, 11.00, NULL, 'UW', 'SSt', 'MAM', '', 32, '2026-06-02', 14.50, 91.00, NULL, NULL, 'Normal', 'Normal', 'Normal', '', 'Recovered', 6.00, 18.50, NULL, 10, 9.09, 30, '2026-06-09 19:32:36', '', 'Let her finish the feeding Program', '2026-06-09 11:32:36', '2026-06-09 11:32:36'),
(4, 42, 0, 'Rhias, Rence Ong', 10, 35, '2026-05-02', 8.60, 96.00, 11.50, NULL, 'SUW', 'Normal', 'SAM', '', 42, '2026-06-03', 8.90, 96.00, 11.50, NULL, 'SUW', 'Normal', 'SAM', '', 'No Progress', 0.30, 0.00, NULL, 10, 18.18, 30, '2026-06-09 19:34:20', '', 'No Progress: Continue supplementary feeding; BNS home visit; re-assess in 2–4 weeks; nutrition counseling for caregiver.', '2026-06-09 11:34:20', '2026-06-09 11:34:20'),
(6, 0, 255, 'Peace, Nel Bin', 11, 34, '2026-06-02', 16.10, 103.30, 12.90, NULL, 'Normal', 'Normal', 'SAM', '', 41, '2026-06-03', 17.00, 103.00, 12.90, NULL, 'Normal', 'Normal', 'SAM', '', 'Deteriorating', 0.90, -0.30, NULL, 7, 50.00, 30, '2026-06-16 23:01:17', '', 'Give the children RUTF', '2026-06-16 15:01:17', '2026-06-16 15:01:17'),
(7, 20, 0, 'Dove, Bia Tan', 11, 19, '2026-06-02', 8.50, 72.50, 11.00, NULL, 'UW', 'SSt', 'MAM', '', 32, '2026-06-02', 14.50, 91.00, NULL, NULL, 'Normal', 'Normal', 'Normal', '', 'Recovered', 6.00, 18.50, NULL, 7, 50.00, 30, '2026-06-16 23:03:24', '', 'Let here finish her feeding sessions', '2026-06-16 15:03:24', '2026-06-16 15:03:24'),
(8, 42, 0, 'Rhias, Rence Ong', 11, 35, '2026-05-02', 8.60, 96.00, 11.50, NULL, 'SUW', 'Normal', 'SAM', '', 42, '2026-06-03', 8.90, 96.00, 11.50, NULL, 'SUW', 'Normal', 'SAM', '', 'No Progress', 0.30, 0.00, NULL, 7, 25.00, 30, '2026-06-17 17:44:47', '', 'Continue supplementary feeding; BNS home visit; re-assess in 2–4 weeks; nutrition counseling for caregiver.', '2026-06-17 09:44:47', '2026-06-17 09:44:47'),
(9, 21, 0, 'Dove, Niel Tan', 11, 23, '2026-06-02', 9.50, 88.00, NULL, NULL, 'Normal', 'Tall', 'SAM', '', 44, '2026-06-22', 9.50, 88.50, 11.00, NULL, 'Normal', 'Tall', 'SAM', '', 'No Progress', 0.00, 0.50, NULL, 7, 50.00, 30, '2026-06-22 18:06:16', '', 'Continue supplementary feeding.', '2026-06-22 10:06:16', '2026-06-22 10:06:16'),
(10, 35, 0, 'Brown, Lea Evan', 10, 24, '2026-06-02', 24.00, 90.00, NULL, NULL, 'OW', 'SSt', 'OW', '', 43, '2026-06-16', 11.00, 74.00, 12.70, NULL, 'SUW', 'SSt', 'Normal', '', 'Deteriorating', -13.00, -16.00, NULL, 10, 30.77, 30, '2026-06-23 10:38:59', '', 'Give the Children RUFT and VItamin A.', '2026-06-23 02:38:59', '2026-06-23 02:38:59'),
(11, 35, 0, 'Brown, Lea Evan', 12, 24, '2026-06-02', 24.00, 90.00, NULL, NULL, 'OW', 'SSt', 'OW', '', 43, '2026-06-16', 11.00, 74.00, 12.70, NULL, 'SUW', 'SSt', 'Normal', '', 'Deteriorating', -13.00, -16.00, NULL, 7, 100.00, 30, '2026-06-24 17:47:23', '', 'Provide the children. RUTF', '2026-06-24 09:47:23', '2026-06-24 09:47:23');

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_assessments`
--

CREATE TABLE `nutrition_assessments` (
  `assessment_id` int(10) UNSIGNED NOT NULL,
  `bns_id` int(11) NOT NULL,
  `assessed_type` enum('child','maternal','senior') NOT NULL,
  `child_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK household_children.child_id',
  `fm_member_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL COMMENT 'FK users.user_id (maternal/senior)',
  `full_name` varchar(200) NOT NULL,
  `sex` char(1) NOT NULL COMMENT 'M or F',
  `dob` date NOT NULL,
  `age_in_months` tinyint(3) UNSIGNED DEFAULT NULL COMMENT 'Computed at time of assessment',
  `age_in_years` tinyint(3) UNSIGNED DEFAULT NULL,
  `weight_kg` decimal(5,2) NOT NULL,
  `height_cm` decimal(5,1) NOT NULL,
  `muac_cm` decimal(4,1) DEFAULT NULL COMMENT 'Mid-upper arm circumference',
  `assessment_date` date NOT NULL,
  `wfa_status` enum('SUW','UW','Normal','OW') DEFAULT NULL COMMENT 'Weight-for-Age',
  `hfa_status` enum('SSt','St','Normal','Tall') DEFAULT NULL COMMENT 'Height-for-Age',
  `wfh_status` enum('SAM','MAM','Normal','OW','Ob') DEFAULT NULL COMMENT 'Weight-for-Height',
  `bmi` decimal(5,2) DEFAULT NULL,
  `bmi_status` enum('Underweight','At Risk','Normal','Overweight','Obese') DEFAULT NULL,
  `needs_monitoring` tinyint(1) NOT NULL DEFAULT 0,
  `is_at_risk` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Goes to Form C / P12',
  `caregiver_name` varchar(200) DEFAULT NULL,
  `purok` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `lmp` date DEFAULT NULL,
  `edc` date DEFAULT NULL,
  `pre_preg_weight` decimal(5,2) DEFAULT NULL,
  `aog_months` tinyint(3) UNSIGNED DEFAULT NULL,
  `weight_gain_kg` decimal(5,2) DEFAULT NULL,
  `weight_gain_status` varchar(20) DEFAULT NULL,
  `philhealth` tinyint(1) DEFAULT NULL,
  `is_4ps` tinyint(1) DEFAULT NULL,
  `spouse_name` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nutrition_assessments`
--

INSERT INTO `nutrition_assessments` (`assessment_id`, `bns_id`, `assessed_type`, `child_id`, `fm_member_id`, `user_id`, `full_name`, `sex`, `dob`, `age_in_months`, `age_in_years`, `weight_kg`, `height_cm`, `muac_cm`, `assessment_date`, `wfa_status`, `hfa_status`, `wfh_status`, `bmi`, `bmi_status`, `needs_monitoring`, `is_at_risk`, `caregiver_name`, `purok`, `remarks`, `created_at`, `updated_at`, `lmp`, `edc`, `pre_preg_weight`, `aog_months`, `weight_gain_kg`, `weight_gain_status`, `philhealth`, `is_4ps`, `spouse_name`) VALUES
(19, 1, 'child', 20, NULL, NULL, 'Dove, Bia Tan', 'F', '2024-06-03', 23, NULL, 8.50, 72.5, 11.0, '2026-06-02', 'UW', 'SSt', 'MAM', NULL, NULL, 1, 1, 'Alex Dove', 'Purok 5', NULL, '2026-06-02 04:00:50', '2026-06-02 04:00:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 1, 'child', NULL, 196, NULL, 'Santiago, Ethan Cruz', 'M', '2024-06-10', 23, NULL, 9.50, 73.0, 13.5, '2026-06-02', 'Normal', 'SSt', 'Normal', NULL, NULL, 1, 1, 'Santiago, Noel', 'Purok 3', NULL, '2026-06-02 04:05:20', '2026-06-02 04:05:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 1, 'child', NULL, 222, NULL, 'Santos, Ryan Perez', 'M', '2024-10-27', 19, NULL, 11.50, 80.0, 12.0, '2026-06-02', 'Normal', 'Normal', 'Normal', NULL, NULL, 0, 0, 'Santos, Junel', 'Purok 3', NULL, '2026-06-02 04:09:09', '2026-06-02 04:09:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 1, 'child', NULL, 200, NULL, 'Martines, Sara Bondalo', 'F', '2025-02-14', 15, NULL, 7.50, 74.5, NULL, '2026-06-02', 'UW', 'Normal', 'SAM', NULL, NULL, 1, 1, 'Martines, Diana', 'Purok 4', NULL, '2026-06-02 04:12:43', '2026-06-02 04:12:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 1, 'child', 21, NULL, NULL, 'Dove, Niel Tan', 'M', '2025-01-30', 16, NULL, 9.50, 88.0, NULL, '2026-06-02', 'Normal', 'Tall', 'SAM', NULL, NULL, 1, 1, 'Alex Dove', 'Purok 5', NULL, '2026-06-02 04:14:48', '2026-06-02 04:14:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 1, 'child', 35, NULL, NULL, 'Brown, Lea Evan', 'F', '2021-08-23', 57, NULL, 24.00, 90.0, NULL, '2026-06-02', 'OW', 'SSt', 'OW', NULL, NULL, 1, 1, 'Maria Brown', 'Purok 8', NULL, '2026-06-02 04:16:10', '2026-06-02 04:16:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 1, 'child', 34, NULL, NULL, 'Brown, Leo Evan', 'M', '2023-06-02', 36, NULL, 25.00, 69.0, NULL, '2026-06-02', 'OW', 'SSt', 'Ob', NULL, NULL, 1, 1, 'Maria Brown', 'Purok 8', NULL, '2026-06-02 04:16:39', '2026-06-02 04:16:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 1, 'maternal', NULL, NULL, 51, 'Maria Brown', 'F', '1996-12-15', NULL, 29, 45.00, 148.0, NULL, '2026-06-02', NULL, NULL, NULL, 20.54, 'Normal', 0, 0, NULL, 'Purok 8', NULL, '2026-06-02 04:27:15', '2026-06-02 04:27:15', '2025-09-16', '2026-07-10', 25.00, 8, 20.00, 'High', 0, 0, NULL),
(27, 1, 'senior', NULL, 205, NULL, 'Cruz, Toni Mill', 'M', '1954-01-07', NULL, 72, 59.00, 150.0, NULL, '2026-06-02', NULL, NULL, NULL, 26.22, 'Normal', 0, 0, NULL, 'Purok 6', NULL, '2026-06-02 04:33:24', '2026-06-02 04:33:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 1, 'senior', NULL, 207, NULL, 'Dal, Tina Com', 'F', '1965-12-13', NULL, 60, 40.00, 168.0, NULL, '2026-06-02', NULL, NULL, NULL, 14.17, 'Underweight', 1, 1, NULL, 'Purok 7', NULL, '2026-06-02 04:34:12', '2026-06-02 04:34:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 1, 'senior', NULL, 206, NULL, 'Dal, Rolly Luke', 'M', '1960-04-15', NULL, 66, 82.00, 183.0, NULL, '2026-06-02', NULL, NULL, NULL, 24.49, 'Normal', 0, 0, NULL, 'Purok 7', NULL, '2026-06-02 04:40:28', '2026-06-02 04:40:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 1, 'maternal', NULL, 221, NULL, 'Perez, Anita', 'F', '1999-07-14', NULL, 26, 30.00, 151.9, NULL, '2026-06-02', NULL, NULL, NULL, 13.00, 'Underweight', 1, 1, NULL, 'Purok 3', NULL, '2026-06-02 07:27:56', '2026-06-02 07:27:56', '2025-11-04', '2026-08-11', 50.00, 6, -20.00, 'Low', 1, 1, NULL),
(31, 1, 'maternal', NULL, 221, NULL, 'Perez, Anita', 'F', '1999-07-14', NULL, 26, 30.00, 152.0, NULL, '2026-06-02', NULL, NULL, NULL, 12.98, 'Underweight', 1, 1, NULL, 'Purok 3', NULL, '2026-06-02 07:46:11', '2026-06-02 07:46:11', '2026-02-11', '2026-11-10', 25.00, 3, 5.00, 'Low', 1, 1, NULL),
(32, 1, 'child', 20, NULL, NULL, 'Dove, Bia Tan', 'F', '2024-06-03', 23, NULL, 14.50, 91.0, NULL, '2026-06-02', 'Normal', 'Normal', 'Normal', NULL, NULL, 0, 0, 'Alex Dove', 'Purok 5', NULL, '2026-06-02 10:51:39', '2026-06-02 10:51:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 1, 'child', 34, NULL, NULL, 'Brown, Leo Evan', 'M', '2023-06-02', 36, NULL, 14.90, 89.5, NULL, '2026-06-02', 'Normal', 'Normal', 'Normal', NULL, NULL, 0, 0, 'Maria Brown', 'Purok 8', NULL, '2026-06-02 11:07:19', '2026-06-02 11:07:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 1, 'child', NULL, 255, NULL, 'Peace, Nel Bin', 'M', '2022-05-28', 48, NULL, 16.10, 103.3, 12.9, '2026-06-02', 'Normal', 'Normal', 'SAM', NULL, NULL, 1, 1, 'Peace, Ethan', 'Purok 1', NULL, '2026-06-02 12:04:43', '2026-06-02 12:04:43', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 1, 'child', 42, NULL, NULL, 'Rhias, Rence Ong', 'M', '2022-07-19', 45, NULL, 8.60, 96.0, 11.5, '2026-05-02', 'SUW', 'Normal', 'SAM', NULL, NULL, 1, 1, 'Erza Rhias', 'Purok 2', NULL, '2026-06-02 13:19:59', '2026-06-02 13:19:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 1, 'child', 34, NULL, NULL, 'Brown, Leo Evan', 'M', '2023-06-02', 36, NULL, 14.50, 89.5, 11.5, '2026-06-03', 'Normal', 'Normal', 'Normal', NULL, NULL, 0, 0, 'Maria Brown', 'Purok 8', NULL, '2026-06-02 16:21:41', '2026-06-02 16:21:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 1, 'child', 35, NULL, NULL, 'Brown, Lea Evan', 'F', '2021-08-23', 57, NULL, 24.00, 89.0, 11.5, '2026-06-03', 'OW', 'SSt', 'OW', NULL, NULL, 1, 1, 'Maria Brown', 'Purok 8', NULL, '2026-06-02 16:31:12', '2026-06-02 16:31:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 1, 'child', NULL, 200, NULL, 'Martines, Sara Bondalo', 'F', '2025-02-14', 15, NULL, 7.50, 74.0, NULL, '2026-06-03', 'UW', 'Normal', 'SAM', NULL, NULL, 1, 1, 'Martines, Diana', 'Purok 4', NULL, '2026-06-02 17:09:57', '2026-06-02 17:09:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 1, 'child', 42, NULL, NULL, 'Rhias, Rence Ong', 'M', '2022-07-19', 46, NULL, 8.60, 96.0, NULL, '2026-06-03', 'SUW', 'Normal', 'SAM', NULL, NULL, 1, 1, 'Erza Rhias', 'Purok 2', NULL, '2026-06-02 17:18:17', '2026-06-02 17:18:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 1, 'child', 35, NULL, NULL, 'Brown, Lea Evan', 'F', '2021-08-23', 57, NULL, 19.00, 90.0, NULL, '2026-06-03', 'Normal', 'SSt', 'Normal', NULL, NULL, 1, 1, 'Maria Brown', 'Purok 8', NULL, '2026-06-02 17:22:15', '2026-06-02 17:22:15', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 1, 'child', NULL, 255, NULL, 'Peace, Nel Bin', 'M', '2022-05-28', 48, NULL, 17.00, 103.0, 12.9, '2026-06-03', 'Normal', 'Normal', 'SAM', NULL, NULL, 1, 1, 'Peace, Ethan', 'Purok 1', NULL, '2026-06-02 18:06:58', '2026-06-02 18:06:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 1, 'child', 42, NULL, NULL, 'Rhias, Rence Ong', 'M', '2022-07-19', 46, NULL, 8.90, 96.0, 11.5, '2026-06-03', 'SUW', 'Normal', 'SAM', NULL, NULL, 1, 1, 'Erza Rhias', 'Purok 2', NULL, '2026-06-03 09:53:18', '2026-06-03 09:53:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 1, 'child', 35, NULL, NULL, 'Brown, Lea Evan', 'F', '2021-08-23', 57, NULL, 11.00, 74.0, 12.7, '2026-06-16', 'SUW', 'SSt', 'Normal', NULL, NULL, 1, 1, 'Maria Brown', 'Purok 8', NULL, '2026-06-16 09:44:34', '2026-06-16 09:44:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 1, 'child', 21, NULL, NULL, 'Dove, Niel Tan', 'M', '2025-01-30', 16, NULL, 9.50, 88.5, 11.0, '2026-06-22', 'Normal', 'Tall', 'SAM', NULL, NULL, 1, 1, 'Alex Dove', 'Purok 5', NULL, '2026-06-22 10:05:33', '2026-06-22 10:05:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_education_sessions`
--

CREATE TABLE `nutrition_education_sessions` (
  `session_id` int(10) UNSIGNED NOT NULL,
  `bns_id` int(11) NOT NULL COMMENT 'BNS who planned this session',
  `session_title` varchar(255) NOT NULL COMMENT 'e.g., 10 Kumainments Workshop',
  `session_date` date NOT NULL,
  `session_time` time NOT NULL,
  `venue` varchar(255) NOT NULL,
  `topic` text NOT NULL COMMENT 'Main topic/content to be discussed',
  `target_group` varchar(100) DEFAULT NULL COMMENT 'Pregnant women, Mothers with 0-23 mos, PWD, Adolescents, Adults, Fathers, Others',
  `max_participants` int(11) DEFAULT NULL,
  `materials_needed` text DEFAULT NULL COMMENT 'Flipcharts, handouts, Pinggang Pinoy plates, etc.',
  `objectives` text DEFAULT NULL COMMENT 'Learning objectives',
  `status` enum('Planned','Ongoing','Completed','Cancelled') DEFAULT 'Planned',
  `completed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nutrition_education_sessions`
--

INSERT INTO `nutrition_education_sessions` (`session_id`, `bns_id`, `session_title`, `session_date`, `session_time`, `venue`, `topic`, `target_group`, `max_participants`, `materials_needed`, `objectives`, `status`, `completed_at`, `notes`, `created_at`, `updated_at`) VALUES
(12, 1, 'Pinggang Pinoy Session', '2026-06-03', '09:30:00', 'Health Center', 'Pinggang Pinoy', NULL, NULL, NULL, NULL, 'Completed', '2026-06-02 21:29:37', 'ALMOST PRESENT', '2026-06-02 13:24:05', '2026-06-02 13:29:37');

-- --------------------------------------------------------

--
-- Table structure for table `nutrition_records`
--

CREATE TABLE `nutrition_records` (
  `record_id` int(11) NOT NULL,
  `mother_id` int(11) DEFAULT NULL,
  `bns_id` int(11) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `is_validated` tinyint(1) NOT NULL DEFAULT 0,
  `validated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL COMMENT 'e.g., ORD-2026-001234',
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL COMMENT 'paymongo, cod',
  `fulfillment_method` enum('pickup','delivery') NOT NULL DEFAULT 'pickup',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `order_status` enum('pending','processing','out_for_delivery','delivered','cancelled') DEFAULT 'pending',
  `delivery_address` text DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `paymongo_payment_intent_id` varchar(255) DEFAULT NULL,
  `paymongo_checkout_url` text DEFAULT NULL,
  `paymongo_payment_method` varchar(50) DEFAULT NULL COMMENT 'card, gcash, paymaya, etc.',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_date` timestamp NULL DEFAULT NULL,
  `delivered_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Customer orders';

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_number`, `total_amount`, `delivery_fee`, `grand_total`, `payment_method`, `fulfillment_method`, `payment_status`, `order_status`, `delivery_address`, `contact_number`, `notes`, `paymongo_payment_intent_id`, `paymongo_checkout_url`, `paymongo_payment_method`, `order_date`, `payment_date`, `delivered_date`) VALUES
(4, 54, 'ORD-2026-561094', 47.00, 50.00, 97.00, 'paymongo', 'delivery', 'paid', '', 'Purok 4, Eden Toril Davao City', '09269749522', '', 'cs_915cf5c58510ce37c05bde53', 'https://checkout.paymongo.com/915cf5c58510ce37c05bde53', NULL, '2026-06-19 16:57:54', '2026-06-19 16:58:17', NULL),
(13, 54, 'ORD-2026-002709', 125.00, 0.00, 125.00, 'paymongo', 'pickup', 'paid', '', '', '09269749522', '', 'cs_b3cfa9c28ef443d128fafee5', 'https://checkout.paymongo.com/b3cfa9c28ef443d128fafee5', NULL, '2026-06-22 13:08:59', '2026-06-22 13:09:28', NULL),
(14, 54, 'ORD-2026-788774', 120.00, 0.00, 120.00, 'paymongo', 'pickup', 'paid', '', '', '09269749522', '', 'cs_0363647d4a6765efa6e0947b', 'https://checkout.paymongo.com/0363647d4a6765efa6e0947b', NULL, '2026-06-22 13:10:33', '2026-06-22 13:10:52', NULL),
(15, 54, 'ORD-2026-358393', 287.00, 0.00, 287.00, 'paymongo', 'pickup', 'pending', 'pending', '', '09269749522', '', 'cs_35af9767a933e1c74e1efb3e', 'https://checkout.paymongo.com/35af9767a933e1c74e1efb3e', NULL, '2026-06-24 07:57:13', NULL, NULL),
(16, 54, 'ORD-2026-525539', 197.00, 50.00, 247.00, 'paymongo', 'delivery', 'paid', '', 'Purok 6, Bayabas Toril Davao City', '', '', 'cs_adace8e890b82caa463a87d2', 'https://checkout.paymongo.com/adace8e890b82caa463a87d2', NULL, '2026-06-24 08:15:08', '2026-06-24 08:15:47', NULL),
(17, 54, 'ORD-2026-288674', 1560.00, 0.00, 1560.00, 'paymongo', 'delivery', 'pending', 'pending', 'sdgfcnvb', '09269749522', '', 'cs_1a09a79da707343a25263b9c', 'https://checkout.paymongo.com/1a09a79da707343a25263b9c', NULL, '2026-06-24 15:33:30', NULL, NULL),
(18, 54, 'ORD-2026-812774', 1560.00, 0.00, 1560.00, 'paymongo', 'delivery', 'pending', 'pending', 'gjbmn,', '09269749522', '', 'cs_38b4430fca354e6977949074', 'https://checkout.paymongo.com/38b4430fca354e6977949074', NULL, '2026-06-24 15:37:50', NULL, NULL),
(19, 54, 'ORD-2026-052504', 185.00, 50.00, 235.00, 'paymongo', 'delivery', 'paid', '', 'dgxh,jbhmn', '09269749522', '', 'cs_38921d2ae9be82c24119ecb6', 'https://checkout.paymongo.com/38921d2ae9be82c24119ecb6', NULL, '2026-06-24 16:45:35', '2026-06-24 16:45:55', NULL),
(20, 54, 'ORD-2026-984173', 120.00, 50.00, 170.00, 'paymongo', 'delivery', 'paid', '', 'Purok 2, Bayabas Toril Davao City', '09269749522', '', 'cs_839930aa503aa8993bd46419', 'https://checkout.paymongo.com/839930aa503aa8993bd46419', NULL, '2026-06-25 04:03:18', '2026-06-25 04:03:41', NULL),
(21, 54, 'ORD-2026-135113', 100.00, 50.00, 150.00, 'cod', 'delivery', 'pending', 'pending', 'Purok 6 Bayabas Toril Davao City', '09269749522', '', NULL, NULL, NULL, '2026-06-25 04:13:52', NULL, NULL),
(22, 54, 'ORD-2026-432756', 100.00, 50.00, 150.00, 'paymongo', 'delivery', 'paid', '', 'Purok 6 Bayabas Toril Davao City', '09269749522', '', 'cs_467f721e5f06a4ea4bd73203', 'https://checkout.paymongo.com/467f721e5f06a4ea4bd73203', NULL, '2026-06-25 04:17:16', '2026-06-25 04:17:49', NULL),
(23, 54, 'ORD-2026-859796', 120.00, 50.00, 170.00, 'paymongo', 'delivery', 'paid', '', 'zxgdfh', '09269749522', '', 'cs_b8e4406d29347365ca17fe2e', 'https://checkout.paymongo.com/b8e4406d29347365ca17fe2e', NULL, '2026-06-25 06:10:54', '2026-06-25 06:11:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_type` enum('srp','vendor') NOT NULL DEFAULT 'srp',
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Items in each order';

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `product_type`, `product_name`, `quantity`, `unit`, `price_per_unit`, `subtotal`) VALUES
(6, 4, 71, 'srp', 'Coffee', 1.00, '10pcs', 47.00, 47.00),
(44, 13, 28, 'srp', 'Ahos', 1.00, 'kg', 125.00, 125.00),
(45, 14, 50, 'vendor', 'Mangga (cebu)', 1.00, 'kg', 120.00, 120.00),
(46, 15, 3, 'srp', 'Kalabasa', 1.00, 'kg', 35.00, 35.00),
(47, 15, 10, 'srp', 'Talong', 1.00, 'kg', 50.00, 50.00),
(48, 15, 1, 'srp', 'Ampalaya', 1.00, 'kg', 90.00, 90.00),
(49, 15, 1, 'vendor', 'Ampalaya', 1.00, 'kg', 52.00, 52.00),
(50, 15, 4, 'srp', 'Kamatis', 1.00, 'kg', 60.00, 60.00),
(53, 16, 3, 'srp', 'Kalabasa', 1.00, 'kg', 35.00, 35.00),
(54, 16, 10, 'srp', 'Talong', 1.00, 'kg', 50.00, 50.00),
(55, 16, 1, 'vendor', 'Ampalaya', 1.00, 'kg', 52.00, 52.00),
(56, 16, 4, 'srp', 'Kamatis', 1.00, 'kg', 60.00, 60.00),
(57, 17, 31, 'srp', 'Bombay', 1.00, 'kg', 100.00, 100.00),
(58, 17, 32, 'srp', 'Bombay', 1.00, 'kg', 130.00, 130.00),
(59, 17, 37, 'srp', 'Sibuyas Dahon', 1.00, 'bundle', 120.00, 120.00),
(60, 17, 39, 'vendor', 'Sibuyas dahon', 1.00, 'bundle', 140.00, 140.00),
(61, 17, 33, 'srp', 'Sili', 1.00, 'kg', 220.00, 220.00),
(62, 17, 34, 'srp', 'Sili', 1.00, 'kg', 200.00, 200.00),
(63, 17, 35, 'srp', 'Sili', 1.00, 'kg', 600.00, 600.00),
(64, 17, 44, 'srp', 'Kalamansi', 1.00, 'kg', 50.00, 50.00),
(72, 18, 31, 'srp', 'Bombay', 1.00, 'kg', 100.00, 100.00),
(73, 18, 32, 'srp', 'Bombay', 1.00, 'kg', 130.00, 130.00),
(74, 18, 37, 'srp', 'Sibuyas Dahon', 1.00, 'bundle', 120.00, 120.00),
(75, 18, 39, 'vendor', 'Sibuyas dahon', 1.00, 'bundle', 140.00, 140.00),
(76, 18, 33, 'srp', 'Sili', 1.00, 'kg', 220.00, 220.00),
(77, 18, 34, 'srp', 'Sili', 1.00, 'kg', 200.00, 200.00),
(78, 18, 35, 'srp', 'Sili', 1.00, 'kg', 600.00, 600.00),
(79, 18, 44, 'srp', 'Kalamansi', 1.00, 'kg', 50.00, 50.00),
(87, 19, 22, 'vendor', 'Repolyo (wakamini)', 1.00, 'kg', 65.00, 65.00),
(88, 19, 37, 'srp', 'Sibuyas Dahon', 1.00, 'bundle', 120.00, 120.00),
(89, 20, 37, 'srp', 'Sibuyas Dahon', 1.00, 'bundle', 120.00, 120.00),
(90, 21, 36, 'srp', 'Luya (Hawaiian)', 1.00, 'kg', 100.00, 100.00),
(91, 22, 36, 'srp', 'Luya (Hawaiian)', 1.00, 'kg', 100.00, 100.00),
(92, 23, 37, 'srp', 'Sibuyas Dahon', 1.00, 'bundle', 120.00, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `pantry_history`
--

CREATE TABLE `pantry_history` (
  `history_id` int(11) NOT NULL,
  `pantry_id` int(11) NOT NULL,
  `change_type` enum('Replenishment','Consumption','Manual Adjustment','Expired') NOT NULL,
  `quantity_change` decimal(10,2) NOT NULL,
  `reference_id` int(11) DEFAULT NULL COMMENT 'Link to grocery_list_item_id or meal_plan_item_id',
  `action_date` datetime DEFAULT current_timestamp(),
  `performed_by_user_id` int(11) NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pantry_history`
--

INSERT INTO `pantry_history` (`history_id`, `pantry_id`, `change_type`, `quantity_change`, `reference_id`, `action_date`, `performed_by_user_id`, `notes`) VALUES
(1, 1, 'Replenishment', 1.00, 5, '2026-06-20 02:20:57', 54, 'Auto-added from order #ORD-2026-984888'),
(2, 2, 'Replenishment', 1.00, 6, '2026-06-21 15:09:29', 54, 'Auto-added from order #ORD-2026-562174'),
(3, 1, 'Replenishment', 2.00, 7, '2026-06-21 15:30:20', 54, 'Auto-added from order #ORD-2026-920261'),
(4, 3, 'Replenishment', 1.00, 8, '2026-06-21 16:16:15', 54, 'Auto-added from order #ORD-2026-970482'),
(5, 4, 'Replenishment', 1.00, 8, '2026-06-21 16:16:15', 54, 'Auto-added from order #ORD-2026-970482'),
(6, 5, 'Replenishment', 1.00, 8, '2026-06-21 16:16:15', 54, 'Auto-added from order #ORD-2026-970482'),
(7, 6, 'Replenishment', 1.00, 8, '2026-06-21 16:16:15', 54, 'Auto-added from order #ORD-2026-970482'),
(8, 7, 'Replenishment', 1.00, 8, '2026-06-21 16:16:15', 54, 'Auto-added from order #ORD-2026-970482'),
(9, 8, 'Replenishment', 1.00, 8, '2026-06-21 16:16:15', 54, 'Auto-added from order #ORD-2026-970482'),
(10, 9, 'Replenishment', 1.00, 8, '2026-06-21 16:16:15', 54, 'Auto-added from order #ORD-2026-970482'),
(11, 10, 'Replenishment', 1.00, 8, '2026-06-21 16:16:15', 54, 'Auto-added from order #ORD-2026-970482'),
(12, 11, 'Replenishment', 1.00, 8, '2026-06-21 16:16:15', 54, 'Auto-added from order #ORD-2026-970482'),
(13, 8, 'Consumption', -1.00, NULL, '2026-06-21 16:18:14', 54, 'Used for cooking'),
(14, 8, 'Consumption', 0.00, NULL, '2026-06-21 17:03:29', 54, 'Used for cooking'),
(15, 4, 'Replenishment', 1.00, 9, '2026-06-21 18:53:22', 54, 'Auto-added from order #ORD-2026-019251'),
(16, 7, 'Replenishment', 1.00, 9, '2026-06-21 18:53:22', 54, 'Auto-added from order #ORD-2026-019251'),
(17, 8, 'Replenishment', 1.00, 9, '2026-06-21 18:53:22', 54, 'Auto-added from order #ORD-2026-019251'),
(18, 9, 'Replenishment', 1.00, 9, '2026-06-21 18:53:22', 54, 'Auto-added from order #ORD-2026-019251'),
(19, 10, 'Replenishment', 1.00, 9, '2026-06-21 18:53:22', 54, 'Auto-added from order #ORD-2026-019251'),
(20, 12, 'Replenishment', 1.00, 10, '2026-06-21 18:54:37', 54, 'Auto-added from order #ORD-2026-283908'),
(21, 13, 'Replenishment', 1.00, 11, '2026-06-21 21:37:10', 46, 'Auto-added from order #ORD-2026-096732'),
(22, 14, 'Replenishment', 1.00, 11, '2026-06-21 21:37:10', 46, 'Auto-added from order #ORD-2026-096732'),
(23, 15, 'Replenishment', 1.00, 11, '2026-06-21 21:37:10', 46, 'Auto-added from order #ORD-2026-096732'),
(24, 16, 'Replenishment', 1.00, 11, '2026-06-21 21:37:10', 46, 'Auto-added from order #ORD-2026-096732'),
(25, 17, 'Replenishment', 1.00, 12, '2026-06-22 13:53:30', 54, 'Auto-added from order #ORD-2026-172539'),
(26, 18, 'Replenishment', 1.00, 12, '2026-06-22 13:53:30', 54, 'Auto-added from order #ORD-2026-172539'),
(27, 19, 'Replenishment', 1.00, 12, '2026-06-22 13:53:30', 54, 'Auto-added from order #ORD-2026-172539'),
(28, 12, 'Replenishment', 1.00, 12, '2026-06-22 13:53:30', 54, 'Auto-added from order #ORD-2026-172539'),
(29, 20, 'Replenishment', 1.00, 12, '2026-06-22 13:53:30', 54, 'Auto-added from order #ORD-2026-172539'),
(30, 3, 'Replenishment', 25.00, NULL, '2026-06-22 20:34:13', 54, ''),
(31, 21, 'Replenishment', 1.00, 13, '2026-06-22 21:09:28', 54, 'Auto-added from order #ORD-2026-002709'),
(32, 22, 'Replenishment', 1.00, 14, '2026-06-22 21:10:52', 54, 'Auto-added from order #ORD-2026-788774'),
(33, 18, 'Replenishment', 1.00, 16, '2026-06-24 16:15:47', 54, 'Auto-added from order #ORD-2026-525539'),
(34, 19, 'Replenishment', 1.00, 16, '2026-06-24 16:15:47', 54, 'Auto-added from order #ORD-2026-525539'),
(35, 12, 'Replenishment', 1.00, 16, '2026-06-24 16:15:47', 54, 'Auto-added from order #ORD-2026-525539'),
(36, 20, 'Replenishment', 1.00, 16, '2026-06-24 16:15:47', 54, 'Auto-added from order #ORD-2026-525539'),
(37, 8, 'Consumption', -1.00, NULL, '2026-06-24 16:17:27', 54, 'Used for cooking'),
(38, 7, 'Consumption', -0.50, NULL, '2026-06-24 16:17:53', 54, 'Used for cooking'),
(39, 23, 'Replenishment', 1.00, 19, '2026-06-25 00:45:55', 54, 'Auto-added from order #ORD-2026-052504'),
(40, 24, 'Replenishment', 1.00, 19, '2026-06-25 00:45:55', 54, 'Auto-added from order #ORD-2026-052504'),
(41, 23, 'Consumption', -0.40, NULL, '2026-06-25 00:47:25', 54, 'Used for cooking'),
(42, 24, 'Replenishment', 1.00, 20, '2026-06-25 12:03:41', 54, 'Auto-added from order #ORD-2026-984173'),
(43, 25, 'Replenishment', 1.00, 22, '2026-06-25 12:17:49', 54, 'Auto-added from order #ORD-2026-432756'),
(44, 24, 'Replenishment', 1.00, 23, '2026-06-25 14:11:13', 54, 'Auto-added from order #ORD-2026-859796'),
(45, 26, 'Replenishment', 0.20, NULL, '2026-06-25 14:53:13', 54, ''),
(46, 27, 'Replenishment', 2.00, NULL, '2026-06-25 14:54:25', 54, ''),
(47, 28, 'Replenishment', 1.00, NULL, '2026-06-25 14:55:24', 54, ''),
(48, 29, 'Replenishment', 1.00, NULL, '2026-06-25 14:57:27', 54, ''),
(49, 30, 'Replenishment', 1.00, NULL, '2026-06-25 14:59:29', 54, ''),
(50, 31, 'Replenishment', 1.00, NULL, '2026-06-25 15:11:45', 54, '');

-- --------------------------------------------------------

--
-- Table structure for table `product_sales`
--

CREATE TABLE `product_sales` (
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `vendor_user_id` int(11) NOT NULL,
  `buyer_user_id` int(11) DEFAULT NULL COMMENT 'Mother/Parent who bought it (if tracked)',
  `grocery_list_item_id` int(11) DEFAULT NULL COMMENT 'Link to grocery list item',
  `quantity_sold` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `sale_date` datetime DEFAULT current_timestamp(),
  `payment_method` enum('Cash','Online','Credit','Other') DEFAULT 'Cash',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_sales`
--

INSERT INTO `product_sales` (`sale_id`, `product_id`, `vendor_user_id`, `buyer_user_id`, `grocery_list_item_id`, `quantity_sold`, `unit`, `price_per_unit`, `total_amount`, `sale_date`, `payment_method`, `notes`) VALUES
(1, 1, 44, 54, NULL, 1.00, 'kg', 52.00, 52.00, '2026-06-21 18:54:20', '', 'Backfilled from order #ORD-2026-283908'),
(2, 50, 44, 54, NULL, 1.00, 'kg', 120.00, 120.00, '2026-06-22 21:10:52', '', 'Sale from order #ORD-2026-788774'),
(3, 1, 44, 54, NULL, 1.00, 'kg', 52.00, 52.00, '2026-06-24 16:15:47', '', 'Sale from order #ORD-2026-525539'),
(4, 22, 44, 54, NULL, 1.00, 'kg', 65.00, 65.00, '2026-06-25 00:45:55', '', 'Sale from order #ORD-2026-052504');

-- --------------------------------------------------------

--
-- Table structure for table `proposal_validations`
--

CREATE TABLE `proposal_validations` (
  `validation_id` int(11) NOT NULL,
  `proposal_id` int(11) NOT NULL,
  `validated_by_user_id` int(11) NOT NULL,
  `decision` varchar(50) NOT NULL,
  `feedback` text DEFAULT NULL,
  `signature_data` text DEFAULT NULL,
  `signature_type` varchar(20) DEFAULT 'drawn',
  `ip_address` varchar(45) DEFAULT NULL,
  `conditions` text DEFAULT NULL,
  `validated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `proposal_validations`
--

INSERT INTO `proposal_validations` (`validation_id`, `proposal_id`, `validated_by_user_id`, `decision`, `feedback`, `signature_data`, `signature_type`, `ip_address`, `conditions`, `validated_at`) VALUES
(11, 7, 42, 'Needs Revision', 'Budget', NULL, 'drawn', '::1', NULL, '2026-06-02 05:41:41'),
(12, 7, 42, 'Approved', NULL, 'data:image/png;base64,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', 'drawn', '::1', NULL, '2026-06-02 05:44:04'),
(13, 8, 42, 'Approved', NULL, 'data:image/png;base64,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', 'drawn', '::1', NULL, '2026-06-02 13:47:03'),
(14, 9, 42, 'Needs Revision', 'Add water', NULL, 'drawn', '::1', NULL, '2026-06-03 09:44:58'),
(15, 9, 42, 'Approved', NULL, 'data:image/png;base64,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', 'drawn', '::1', NULL, '2026-06-03 09:46:26'),
(16, 10, 42, 'Needs Revision', 'Add water', NULL, 'drawn', '::1', NULL, '2026-06-03 10:07:15'),
(17, 10, 42, 'Approved', NULL, 'data:image/png;base64,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', 'drawn', '::1', NULL, '2026-06-03 10:08:32');
INSERT INTO `proposal_validations` (`validation_id`, `proposal_id`, `validated_by_user_id`, `decision`, `feedback`, `signature_data`, `signature_type`, `ip_address`, `conditions`, `validated_at`) VALUES
(18, 11, 42, 'Approved', NULL, 'data:image/png;base64,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', 'drawn', '::1', NULL, '2026-06-16 07:33:06'),
(19, 12, 42, 'Needs Revision', 'please make sure the budget', NULL, 'drawn', '::1', NULL, '2026-06-17 09:38:04'),
(20, 12, 42, 'Approved', NULL, 'data:image/png;base64,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', 'drawn', '::1', NULL, '2026-06-17 09:40:29');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `recipe_id` int(11) NOT NULL,
  `recipe_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `cooking_time_mins` int(11) DEFAULT NULL,
  `difficulty` enum('Easy','Medium','Hard') DEFAULT 'Easy',
  `category` varchar(100) DEFAULT NULL COMMENT 'e.g., Breakfast, Lunch, Dinner, Snack',
  `nutritional_info` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipe_ingredients`
--

CREATE TABLE `recipe_ingredients` (
  `ri_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `ingredient_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ref_dwelling_types`
--

CREATE TABLE `ref_dwelling_types` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `label` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ref_dwelling_types`
--

INSERT INTO `ref_dwelling_types` (`id`, `label`) VALUES
(1, 'Concrete'),
(2, 'Semi-Concrete'),
(3, 'Wood'),
(4, 'Barongbarong / Makeshift');

-- --------------------------------------------------------

--
-- Table structure for table `ref_educ_levels`
--

CREATE TABLE `ref_educ_levels` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `label` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ref_educ_levels`
--

INSERT INTO `ref_educ_levels` (`id`, `label`) VALUES
(1, 'No Formal Education'),
(2, 'Elementary'),
(3, 'High School'),
(4, 'Vocational/Tech'),
(5, 'College'),
(6, 'Post-Graduate');

-- --------------------------------------------------------

--
-- Table structure for table `ref_food_activities`
--

CREATE TABLE `ref_food_activities` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `code` varchar(10) NOT NULL,
  `label` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ref_food_activities`
--

INSERT INTO `ref_food_activities` (`id`, `code`, `label`) VALUES
(1, 'VG', 'Vegetable Garden'),
(2, 'P/L', 'Poultry / Livestock'),
(3, 'FP', 'Fishpond');

-- --------------------------------------------------------

--
-- Table structure for table `ref_fp_methods`
--

CREATE TABLE `ref_fp_methods` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `label` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ref_fp_methods`
--

INSERT INTO `ref_fp_methods` (`id`, `label`) VALUES
(1, 'None'),
(2, 'Pills'),
(3, 'IUD'),
(4, 'Condom'),
(5, 'Injectable'),
(6, 'Implant'),
(7, 'NFP/LAM'),
(8, 'Ligation'),
(9, 'Vasectomy'),
(10, 'Others');

-- --------------------------------------------------------

--
-- Table structure for table `ref_nutri_statuses`
--

CREATE TABLE `ref_nutri_statuses` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `label` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ref_nutri_statuses`
--

INSERT INTO `ref_nutri_statuses` (`id`, `label`) VALUES
(1, 'Normal'),
(2, 'Underweight'),
(3, 'Severely Underweight'),
(4, 'Overweight'),
(5, 'Obese'),
(6, 'Stunted'),
(7, 'Wasted'),
(8, 'Severely Wasted');

-- --------------------------------------------------------

--
-- Table structure for table `ref_toilet_types`
--

CREATE TABLE `ref_toilet_types` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `code` varchar(5) NOT NULL,
  `label` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ref_toilet_types`
--

INSERT INTO `ref_toilet_types` (`id`, `code`, `label`) VALUES
(1, 'WS', 'Water Sealed'),
(2, 'OP', 'Open Pit'),
(3, 'O', 'Others'),
(4, 'N', 'None');

-- --------------------------------------------------------

--
-- Table structure for table `ref_water_sources`
--

CREATE TABLE `ref_water_sources` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `code` varchar(5) NOT NULL,
  `label` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ref_water_sources`
--

INSERT INTO `ref_water_sources` (`id`, `code`, `label`) VALUES
(1, 'P', 'Piped'),
(2, 'W', 'Well'),
(3, 'S', 'Spring / Others');

-- --------------------------------------------------------

--
-- Table structure for table `ref_zscore_hfa`
--

CREATE TABLE `ref_zscore_hfa` (
  `id` int(10) UNSIGNED NOT NULL,
  `age_months` tinyint(3) UNSIGNED NOT NULL,
  `sex` char(1) NOT NULL,
  `sd_neg3` decimal(5,1) NOT NULL,
  `sd_neg2` decimal(5,1) NOT NULL,
  `sd_neg1` decimal(5,1) NOT NULL,
  `median` decimal(5,1) NOT NULL,
  `sd_pos1` decimal(5,1) NOT NULL,
  `sd_pos2` decimal(5,1) NOT NULL,
  `sd_pos3` decimal(5,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='WHO 2006 Height/Length-for-Age Z-scores, 0-60 months';

--
-- Dumping data for table `ref_zscore_hfa`
--

INSERT INTO `ref_zscore_hfa` (`id`, `age_months`, `sex`, `sd_neg3`, `sd_neg2`, `sd_neg1`, `median`, `sd_pos1`, `sd_pos2`, `sd_pos3`) VALUES
(1, 0, 'M', 44.2, 46.1, 48.0, 49.9, 51.8, 53.7, 55.6),
(2, 1, 'M', 48.9, 50.8, 52.8, 54.7, 56.7, 58.6, 60.6),
(3, 2, 'M', 52.4, 54.4, 56.4, 58.4, 60.4, 62.4, 64.4),
(4, 3, 'M', 55.3, 57.3, 59.4, 61.4, 63.5, 65.5, 67.6),
(5, 4, 'M', 57.6, 59.7, 61.8, 63.9, 66.0, 68.0, 70.1),
(6, 5, 'M', 59.6, 61.7, 63.8, 65.9, 68.0, 70.1, 72.2),
(7, 6, 'M', 61.2, 63.3, 65.5, 67.6, 69.8, 71.9, 74.0),
(8, 7, 'M', 62.7, 64.8, 67.0, 69.2, 71.3, 73.5, 75.7),
(9, 8, 'M', 64.0, 66.2, 68.4, 70.6, 72.8, 75.0, 77.2),
(10, 9, 'M', 65.2, 67.5, 69.7, 72.0, 74.2, 76.5, 78.7),
(11, 10, 'M', 66.4, 68.7, 71.0, 73.3, 75.6, 77.9, 80.1),
(12, 11, 'M', 67.6, 69.9, 72.2, 74.5, 76.9, 79.2, 81.5),
(13, 12, 'M', 68.6, 71.0, 73.4, 75.7, 78.1, 80.5, 82.9),
(14, 13, 'M', 69.6, 72.1, 74.5, 76.9, 79.4, 81.8, 84.2),
(15, 14, 'M', 70.6, 73.1, 75.6, 78.0, 80.5, 83.0, 85.5),
(16, 15, 'M', 71.6, 74.1, 76.6, 79.1, 81.7, 84.2, 86.7),
(17, 16, 'M', 72.5, 75.0, 77.6, 80.2, 82.8, 85.4, 87.9),
(18, 17, 'M', 73.3, 76.0, 78.6, 81.2, 83.9, 86.5, 89.2),
(19, 18, 'M', 74.2, 76.9, 79.6, 82.3, 85.0, 87.7, 90.4),
(20, 19, 'M', 75.0, 77.7, 80.5, 83.2, 86.0, 88.8, 91.5),
(21, 20, 'M', 75.8, 78.6, 81.4, 84.2, 87.0, 89.8, 92.6),
(22, 21, 'M', 76.5, 79.4, 82.3, 85.1, 88.0, 90.9, 93.8),
(23, 22, 'M', 77.2, 80.2, 83.1, 86.0, 89.0, 91.9, 94.9),
(24, 23, 'M', 78.0, 81.0, 83.9, 86.9, 89.9, 92.9, 95.9),
(25, 24, 'M', 78.7, 81.7, 84.8, 87.8, 90.9, 93.9, 97.0),
(26, 25, 'M', 79.4, 82.5, 85.6, 88.7, 91.8, 94.9, 98.0),
(27, 26, 'M', 80.1, 83.2, 86.4, 89.5, 92.7, 95.9, 99.0),
(28, 27, 'M', 80.7, 83.9, 87.1, 90.4, 93.6, 96.8, 100.1),
(29, 28, 'M', 81.3, 84.6, 87.9, 91.2, 94.5, 97.8, 101.1),
(30, 29, 'M', 81.9, 85.3, 88.6, 92.0, 95.3, 98.7, 102.0),
(31, 30, 'M', 82.5, 85.9, 89.3, 92.7, 96.1, 99.5, 102.9),
(32, 31, 'M', 83.1, 86.5, 89.9, 93.4, 96.9, 100.3, 103.8),
(33, 32, 'M', 83.6, 87.1, 90.6, 94.1, 97.6, 101.1, 104.6),
(34, 33, 'M', 84.1, 87.7, 91.2, 94.8, 98.3, 101.9, 105.4),
(35, 34, 'M', 84.7, 88.2, 91.8, 95.4, 99.0, 102.6, 106.2),
(36, 35, 'M', 85.2, 88.8, 92.4, 96.1, 99.7, 103.3, 107.0),
(37, 36, 'M', 85.7, 89.3, 93.0, 96.7, 100.4, 104.1, 107.8),
(38, 37, 'M', 86.1, 89.9, 93.6, 97.3, 101.1, 104.8, 108.6),
(39, 38, 'M', 86.6, 90.4, 94.2, 98.0, 101.8, 105.6, 109.4),
(40, 39, 'M', 87.1, 90.9, 94.7, 98.6, 102.4, 106.3, 110.1),
(41, 40, 'M', 87.5, 91.4, 95.3, 99.1, 103.1, 107.0, 110.9),
(42, 41, 'M', 87.9, 91.9, 95.8, 99.7, 103.7, 107.6, 111.6),
(43, 42, 'M', 88.4, 92.3, 96.3, 100.3, 104.3, 108.3, 112.3),
(44, 43, 'M', 88.8, 92.8, 96.8, 100.8, 104.9, 108.9, 113.0),
(45, 44, 'M', 89.2, 93.2, 97.3, 101.4, 105.5, 109.5, 113.6),
(46, 45, 'M', 89.6, 93.7, 97.8, 101.9, 106.0, 110.2, 114.3),
(47, 46, 'M', 90.1, 94.1, 98.3, 102.4, 106.6, 110.8, 114.9),
(48, 47, 'M', 90.4, 94.6, 98.8, 103.0, 107.2, 111.4, 115.6),
(49, 48, 'M', 90.8, 95.0, 99.3, 103.5, 107.8, 112.0, 116.3),
(50, 49, 'M', 91.2, 95.4, 99.7, 104.0, 108.3, 112.6, 116.9),
(51, 50, 'M', 91.6, 95.8, 100.2, 104.5, 108.8, 113.2, 117.5),
(52, 51, 'M', 92.0, 96.2, 100.6, 105.0, 109.4, 113.8, 118.1),
(53, 52, 'M', 92.3, 96.6, 101.0, 105.4, 109.9, 114.3, 118.7),
(54, 53, 'M', 92.7, 97.0, 101.4, 105.9, 110.3, 114.8, 119.3),
(55, 54, 'M', 93.1, 97.4, 101.8, 106.3, 110.8, 115.3, 119.9),
(56, 55, 'M', 93.4, 97.8, 102.2, 106.7, 111.3, 115.8, 120.4),
(57, 56, 'M', 93.8, 98.2, 102.6, 107.2, 111.7, 116.3, 121.0),
(58, 57, 'M', 94.2, 98.5, 103.0, 107.6, 112.2, 116.8, 121.5),
(59, 58, 'M', 94.5, 98.9, 103.4, 108.0, 112.6, 117.3, 122.0),
(60, 59, 'M', 94.9, 99.3, 103.8, 108.4, 113.1, 117.8, 122.5),
(61, 0, 'F', 43.6, 45.4, 47.3, 49.1, 51.0, 52.9, 54.7),
(62, 1, 'F', 47.8, 49.8, 51.7, 53.7, 55.6, 57.6, 59.5),
(63, 2, 'F', 51.0, 53.0, 55.0, 57.1, 59.1, 61.1, 63.2),
(64, 3, 'F', 53.5, 55.6, 57.7, 59.8, 61.9, 64.0, 66.1),
(65, 4, 'F', 55.6, 57.8, 59.9, 62.1, 64.3, 66.4, 68.6),
(66, 5, 'F', 57.4, 59.6, 61.8, 64.0, 66.2, 68.5, 70.7),
(67, 6, 'F', 58.9, 61.2, 63.5, 65.7, 68.0, 70.3, 72.5),
(68, 7, 'F', 60.3, 62.7, 65.0, 67.3, 69.6, 72.0, 74.3),
(69, 8, 'F', 61.7, 64.0, 66.4, 68.7, 71.1, 73.5, 75.8),
(70, 9, 'F', 62.9, 65.3, 67.7, 70.1, 72.6, 75.0, 77.4),
(71, 10, 'F', 64.1, 66.5, 69.0, 71.5, 73.9, 76.4, 78.9),
(72, 11, 'F', 65.2, 67.7, 70.3, 72.8, 75.3, 77.8, 80.3),
(73, 12, 'F', 66.3, 68.9, 71.4, 74.0, 76.6, 79.2, 81.7),
(74, 13, 'F', 67.3, 70.0, 72.6, 75.2, 77.8, 80.5, 83.1),
(75, 14, 'F', 68.3, 71.0, 73.7, 76.4, 79.1, 81.7, 84.4),
(76, 15, 'F', 69.3, 72.0, 74.8, 77.5, 80.3, 83.0, 85.8),
(77, 16, 'F', 70.2, 73.0, 75.8, 78.6, 81.4, 84.2, 87.0),
(78, 17, 'F', 71.1, 74.0, 76.8, 79.7, 82.6, 85.4, 88.3),
(79, 18, 'F', 72.0, 74.9, 77.8, 80.7, 83.6, 86.5, 89.4),
(80, 19, 'F', 72.8, 75.8, 78.8, 81.7, 84.7, 87.6, 90.6),
(81, 20, 'F', 73.7, 76.7, 79.7, 82.7, 85.7, 88.7, 91.7),
(82, 21, 'F', 74.5, 77.5, 80.6, 83.7, 86.7, 89.8, 92.9),
(83, 22, 'F', 75.2, 78.4, 81.5, 84.6, 87.7, 90.8, 94.0),
(84, 23, 'F', 76.0, 79.2, 82.3, 85.5, 88.7, 91.9, 95.0),
(85, 24, 'F', 76.7, 80.0, 83.2, 86.4, 89.7, 93.0, 96.2),
(86, 25, 'F', 77.5, 80.8, 84.1, 87.4, 90.7, 94.0, 97.3),
(87, 26, 'F', 78.2, 81.5, 84.9, 88.3, 91.7, 95.0, 98.4),
(88, 27, 'F', 78.9, 82.3, 85.7, 89.1, 92.6, 96.0, 99.4),
(89, 28, 'F', 79.6, 83.0, 86.5, 90.0, 93.5, 96.9, 100.4),
(90, 29, 'F', 80.2, 83.7, 87.3, 90.8, 94.3, 97.9, 101.4),
(91, 30, 'F', 80.8, 84.4, 88.0, 91.5, 95.1, 98.7, 102.3),
(92, 31, 'F', 81.5, 85.1, 88.7, 92.3, 96.0, 99.6, 103.2),
(93, 32, 'F', 82.1, 85.7, 89.4, 93.1, 96.7, 100.4, 104.1),
(94, 33, 'F', 82.7, 86.4, 90.1, 93.8, 97.5, 101.2, 105.0),
(95, 34, 'F', 83.2, 87.0, 90.7, 94.5, 98.3, 102.0, 105.8),
(96, 35, 'F', 83.8, 87.6, 91.4, 95.2, 99.0, 102.8, 106.6),
(97, 36, 'F', 84.3, 88.2, 92.0, 95.9, 99.7, 103.6, 107.4),
(98, 37, 'F', 84.9, 88.8, 92.7, 96.6, 100.5, 104.4, 108.3),
(99, 38, 'F', 85.4, 89.3, 93.3, 97.2, 101.2, 105.1, 109.1),
(100, 39, 'F', 85.9, 89.9, 93.9, 97.9, 101.9, 105.9, 109.9),
(101, 40, 'F', 86.4, 90.4, 94.5, 98.5, 102.6, 106.6, 110.7),
(102, 41, 'F', 86.9, 91.0, 95.1, 99.2, 103.3, 107.4, 111.5),
(103, 42, 'F', 87.4, 91.5, 95.7, 99.8, 103.9, 108.1, 112.2),
(104, 43, 'F', 87.9, 92.1, 96.2, 100.4, 104.6, 108.8, 113.0),
(105, 44, 'F', 88.4, 92.6, 96.8, 101.0, 105.3, 109.5, 113.7),
(106, 45, 'F', 88.9, 93.1, 97.4, 101.6, 105.9, 110.2, 114.4),
(107, 46, 'F', 89.3, 93.6, 97.9, 102.2, 106.5, 110.8, 115.2),
(108, 47, 'F', 89.8, 94.1, 98.5, 102.8, 107.2, 111.6, 116.0),
(109, 48, 'F', 90.3, 94.6, 99.0, 103.4, 107.8, 112.3, 116.7),
(110, 49, 'F', 90.7, 95.1, 99.5, 104.0, 108.5, 112.9, 117.4),
(111, 50, 'F', 91.2, 95.6, 100.1, 104.6, 109.1, 113.6, 118.1),
(112, 51, 'F', 91.6, 96.1, 100.6, 105.1, 109.7, 114.2, 118.8),
(113, 52, 'F', 92.1, 96.6, 101.1, 105.7, 110.3, 114.9, 119.5),
(114, 53, 'F', 92.5, 97.0, 101.6, 106.2, 110.9, 115.5, 120.2),
(115, 54, 'F', 92.9, 97.5, 102.1, 106.8, 111.5, 116.2, 120.9),
(116, 55, 'F', 93.4, 98.0, 102.6, 107.3, 112.1, 116.8, 121.6),
(117, 56, 'F', 93.8, 98.4, 103.1, 107.9, 112.7, 117.5, 122.3),
(118, 57, 'F', 94.2, 98.9, 103.6, 108.4, 113.3, 118.1, 123.0),
(119, 58, 'F', 94.6, 99.3, 104.1, 108.9, 113.8, 118.7, 123.6),
(120, 59, 'F', 95.0, 99.7, 104.5, 109.4, 114.3, 119.2, 124.2);

-- --------------------------------------------------------

--
-- Table structure for table `ref_zscore_wfa`
--

CREATE TABLE `ref_zscore_wfa` (
  `id` int(10) UNSIGNED NOT NULL,
  `age_months` tinyint(3) UNSIGNED NOT NULL,
  `sex` char(1) NOT NULL COMMENT 'M or F',
  `sd_neg3` decimal(5,2) NOT NULL,
  `sd_neg2` decimal(5,2) NOT NULL,
  `sd_neg1` decimal(5,2) NOT NULL,
  `median` decimal(5,2) NOT NULL,
  `sd_pos1` decimal(5,2) NOT NULL,
  `sd_pos2` decimal(5,2) NOT NULL,
  `sd_pos3` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='WHO 2006 Weight-for-Age Z-scores, 0-60 months';

--
-- Dumping data for table `ref_zscore_wfa`
--

INSERT INTO `ref_zscore_wfa` (`id`, `age_months`, `sex`, `sd_neg3`, `sd_neg2`, `sd_neg1`, `median`, `sd_pos1`, `sd_pos2`, `sd_pos3`) VALUES
(1, 0, 'M', 2.10, 2.50, 2.90, 3.30, 3.90, 4.40, 5.00),
(2, 1, 'M', 2.90, 3.40, 3.90, 4.50, 5.10, 5.80, 6.60),
(3, 2, 'M', 3.80, 4.30, 4.90, 5.60, 6.30, 7.10, 8.00),
(4, 3, 'M', 4.40, 5.00, 5.70, 6.40, 7.20, 8.00, 9.00),
(5, 4, 'M', 4.90, 5.60, 6.20, 7.00, 7.80, 8.70, 9.70),
(6, 5, 'M', 5.30, 6.00, 6.70, 7.50, 8.40, 9.30, 10.40),
(7, 6, 'M', 5.70, 6.40, 7.10, 7.90, 8.80, 9.80, 10.90),
(8, 7, 'M', 5.90, 6.70, 7.40, 8.30, 9.20, 10.30, 11.40),
(9, 8, 'M', 6.20, 6.90, 7.70, 8.60, 9.60, 10.70, 11.90),
(10, 9, 'M', 6.40, 7.10, 8.00, 8.90, 9.90, 11.00, 12.30),
(11, 10, 'M', 6.60, 7.40, 8.20, 9.20, 10.20, 11.40, 12.70),
(12, 11, 'M', 6.80, 7.60, 8.40, 9.40, 10.50, 11.70, 13.00),
(13, 12, 'M', 6.90, 7.70, 8.60, 9.60, 10.80, 12.00, 13.30),
(14, 13, 'M', 7.10, 7.90, 8.80, 9.90, 11.00, 12.30, 13.70),
(15, 14, 'M', 7.20, 8.10, 9.00, 10.10, 11.30, 12.60, 14.00),
(16, 15, 'M', 7.40, 8.30, 9.20, 10.30, 11.50, 12.80, 14.30),
(17, 16, 'M', 7.50, 8.40, 9.40, 10.50, 11.70, 13.10, 14.60),
(18, 17, 'M', 7.70, 8.60, 9.60, 10.70, 12.00, 13.40, 14.90),
(19, 18, 'M', 7.80, 8.80, 9.80, 10.90, 12.20, 13.70, 15.30),
(20, 19, 'M', 8.00, 8.90, 10.00, 11.10, 12.50, 14.00, 15.60),
(21, 20, 'M', 8.10, 9.10, 10.10, 11.30, 12.70, 14.20, 15.90),
(22, 21, 'M', 8.20, 9.20, 10.30, 11.50, 12.90, 14.50, 16.20),
(23, 22, 'M', 8.40, 9.40, 10.50, 11.80, 13.20, 14.70, 16.50),
(24, 23, 'M', 8.50, 9.50, 10.70, 12.00, 13.40, 15.00, 16.80),
(25, 24, 'M', 8.60, 9.70, 10.80, 12.20, 13.60, 15.30, 17.10),
(26, 25, 'M', 8.80, 9.80, 11.00, 12.40, 13.90, 15.50, 17.50),
(27, 26, 'M', 8.90, 10.00, 11.20, 12.50, 14.10, 15.80, 17.80),
(28, 27, 'M', 9.00, 10.10, 11.30, 12.70, 14.30, 16.10, 18.10),
(29, 28, 'M', 9.10, 10.20, 11.50, 12.90, 14.50, 16.30, 18.40),
(30, 29, 'M', 9.20, 10.40, 11.70, 13.10, 14.80, 16.60, 18.70),
(31, 30, 'M', 9.40, 10.50, 11.80, 13.30, 15.00, 16.90, 19.00),
(32, 31, 'M', 9.50, 10.70, 12.00, 13.50, 15.20, 17.10, 19.30),
(33, 32, 'M', 9.60, 10.80, 12.10, 13.70, 15.40, 17.40, 19.60),
(34, 33, 'M', 9.70, 10.90, 12.30, 13.80, 15.60, 17.60, 19.90),
(35, 34, 'M', 9.80, 11.00, 12.40, 14.00, 15.80, 17.80, 20.20),
(36, 35, 'M', 9.90, 11.20, 12.60, 14.20, 16.00, 18.10, 20.40),
(37, 36, 'M', 10.00, 11.30, 12.70, 14.30, 16.20, 18.30, 20.70),
(38, 37, 'M', 10.10, 11.40, 12.90, 14.50, 16.40, 18.50, 21.00),
(39, 38, 'M', 10.20, 11.50, 13.00, 14.60, 16.50, 18.70, 21.20),
(40, 39, 'M', 10.30, 11.60, 13.10, 14.80, 16.70, 18.90, 21.50),
(41, 40, 'M', 10.40, 11.70, 13.20, 15.00, 16.90, 19.10, 21.70),
(42, 41, 'M', 10.50, 11.80, 13.40, 15.10, 17.10, 19.30, 22.00),
(43, 42, 'M', 10.60, 11.90, 13.50, 15.30, 17.20, 19.50, 22.20),
(44, 43, 'M', 10.70, 12.00, 13.60, 15.40, 17.40, 19.70, 22.50),
(45, 44, 'M', 10.80, 12.10, 13.70, 15.50, 17.60, 19.90, 22.70),
(46, 45, 'M', 10.90, 12.20, 13.80, 15.70, 17.70, 20.10, 23.00),
(47, 46, 'M', 11.00, 12.30, 14.00, 15.80, 17.90, 20.30, 23.20),
(48, 47, 'M', 11.00, 12.40, 14.10, 16.00, 18.10, 20.50, 23.50),
(49, 48, 'M', 11.10, 12.50, 14.20, 16.10, 18.20, 20.70, 23.70),
(50, 49, 'M', 11.20, 12.60, 14.30, 16.30, 18.40, 20.90, 24.00),
(51, 50, 'M', 11.30, 12.70, 14.40, 16.40, 18.50, 21.00, 24.20),
(52, 51, 'M', 11.40, 12.80, 14.50, 16.50, 18.70, 21.20, 24.40),
(53, 52, 'M', 11.50, 12.90, 14.70, 16.70, 18.90, 21.40, 24.70),
(54, 53, 'M', 11.50, 13.00, 14.80, 16.80, 19.00, 21.60, 24.90),
(55, 54, 'M', 11.60, 13.10, 14.90, 17.00, 19.20, 21.80, 25.20),
(56, 55, 'M', 11.70, 13.20, 15.00, 17.10, 19.30, 22.00, 25.40),
(57, 56, 'M', 11.80, 13.30, 15.10, 17.20, 19.50, 22.10, 25.60),
(58, 57, 'M', 11.90, 13.40, 15.20, 17.40, 19.60, 22.30, 25.80),
(59, 58, 'M', 11.90, 13.40, 15.30, 17.50, 19.80, 22.50, 26.10),
(60, 59, 'M', 12.00, 13.50, 15.40, 17.60, 19.90, 22.70, 26.30),
(61, 0, 'F', 2.00, 2.40, 2.80, 3.20, 3.70, 4.20, 4.80),
(62, 1, 'F', 2.70, 3.20, 3.60, 4.20, 4.80, 5.50, 6.20),
(63, 2, 'F', 3.40, 3.90, 4.50, 5.10, 5.80, 6.60, 7.50),
(64, 3, 'F', 4.00, 4.50, 5.20, 5.80, 6.60, 7.50, 8.50),
(65, 4, 'F', 4.40, 5.00, 5.70, 6.40, 7.30, 8.20, 9.30),
(66, 5, 'F', 4.80, 5.40, 6.10, 6.90, 7.80, 8.80, 10.00),
(67, 6, 'F', 5.10, 5.70, 6.50, 7.30, 8.20, 9.30, 10.60),
(68, 7, 'F', 5.30, 6.00, 6.80, 7.60, 8.60, 9.80, 11.10),
(69, 8, 'F', 5.60, 6.30, 7.00, 7.90, 9.00, 10.20, 11.60),
(70, 9, 'F', 5.80, 6.50, 7.30, 8.20, 9.30, 10.50, 12.00),
(71, 10, 'F', 5.90, 6.70, 7.50, 8.50, 9.60, 10.90, 12.40),
(72, 11, 'F', 6.10, 6.90, 7.70, 8.70, 9.90, 11.20, 12.80),
(73, 12, 'F', 6.30, 7.00, 7.90, 8.90, 10.10, 11.50, 13.10),
(74, 13, 'F', 6.40, 7.20, 8.10, 9.20, 10.40, 11.80, 13.50),
(75, 14, 'F', 6.60, 7.40, 8.30, 9.40, 10.60, 12.10, 13.80),
(76, 15, 'F', 6.70, 7.60, 8.50, 9.60, 10.90, 12.40, 14.10),
(77, 16, 'F', 6.90, 7.70, 8.70, 9.80, 11.10, 12.60, 14.50),
(78, 17, 'F', 7.00, 7.90, 8.90, 10.00, 11.40, 12.90, 14.80),
(79, 18, 'F', 7.20, 8.10, 9.10, 10.20, 11.60, 13.20, 15.10),
(80, 19, 'F', 7.30, 8.20, 9.20, 10.40, 11.80, 13.50, 15.40),
(81, 20, 'F', 7.50, 8.40, 9.40, 10.60, 12.10, 13.70, 15.70),
(82, 21, 'F', 7.60, 8.60, 9.60, 10.90, 12.30, 14.00, 16.00),
(83, 22, 'F', 7.80, 8.70, 9.80, 11.10, 12.50, 14.30, 16.40),
(84, 23, 'F', 7.90, 8.90, 10.00, 11.30, 12.80, 14.60, 16.70),
(85, 24, 'F', 8.10, 9.00, 10.20, 11.50, 13.00, 14.80, 17.00),
(86, 25, 'F', 8.20, 9.20, 10.30, 11.70, 13.30, 15.10, 17.30),
(87, 26, 'F', 8.40, 9.40, 10.50, 11.90, 13.50, 15.40, 17.60),
(88, 27, 'F', 8.50, 9.50, 10.70, 12.10, 13.70, 15.70, 18.00),
(89, 28, 'F', 8.60, 9.70, 10.90, 12.30, 14.00, 15.90, 18.30),
(90, 29, 'F', 8.80, 9.80, 11.10, 12.50, 14.20, 16.20, 18.60),
(91, 30, 'F', 8.90, 10.00, 11.20, 12.70, 14.40, 16.40, 18.90),
(92, 31, 'F', 9.00, 10.10, 11.40, 12.90, 14.60, 16.70, 19.20),
(93, 32, 'F', 9.10, 10.30, 11.50, 13.10, 14.80, 16.90, 19.50),
(94, 33, 'F', 9.30, 10.40, 11.70, 13.30, 15.10, 17.20, 19.80),
(95, 34, 'F', 9.40, 10.50, 11.80, 13.40, 15.30, 17.40, 20.10),
(96, 35, 'F', 9.50, 10.70, 12.00, 13.60, 15.50, 17.70, 20.40),
(97, 36, 'F', 9.60, 10.80, 12.10, 13.80, 15.70, 17.90, 20.70),
(98, 37, 'F', 9.70, 10.90, 12.30, 14.00, 15.90, 18.20, 21.00),
(99, 38, 'F', 9.80, 11.00, 12.40, 14.10, 16.10, 18.40, 21.30),
(100, 39, 'F', 9.90, 11.10, 12.50, 14.30, 16.30, 18.60, 21.60),
(101, 40, 'F', 10.00, 11.30, 12.70, 14.50, 16.50, 18.90, 21.90),
(102, 41, 'F', 10.10, 11.40, 12.80, 14.60, 16.70, 19.10, 22.10),
(103, 42, 'F', 10.20, 11.50, 13.00, 14.80, 16.90, 19.30, 22.40),
(104, 43, 'F', 10.30, 11.60, 13.10, 14.90, 17.00, 19.50, 22.70),
(105, 44, 'F', 10.40, 11.70, 13.20, 15.10, 17.20, 19.70, 23.00),
(106, 45, 'F', 10.50, 11.80, 13.30, 15.20, 17.40, 20.00, 23.20),
(107, 46, 'F', 10.60, 11.90, 13.40, 15.40, 17.60, 20.20, 23.50),
(108, 47, 'F', 10.70, 12.00, 13.60, 15.50, 17.70, 20.40, 23.80),
(109, 48, 'F', 10.80, 12.10, 13.70, 15.70, 17.90, 20.60, 24.00),
(110, 49, 'F', 10.90, 12.20, 13.80, 15.80, 18.10, 20.80, 24.30),
(111, 50, 'F', 11.00, 12.30, 13.90, 16.00, 18.30, 21.00, 24.60),
(112, 51, 'F', 11.10, 12.40, 14.00, 16.10, 18.40, 21.20, 24.80),
(113, 52, 'F', 11.10, 12.50, 14.20, 16.30, 18.60, 21.40, 25.10),
(114, 53, 'F', 11.20, 12.60, 14.30, 16.40, 18.80, 21.60, 25.30),
(115, 54, 'F', 11.30, 12.70, 14.40, 16.60, 19.00, 21.80, 25.60),
(116, 55, 'F', 11.40, 12.80, 14.50, 16.70, 19.10, 22.00, 25.80),
(117, 56, 'F', 11.50, 12.90, 14.60, 16.80, 19.30, 22.20, 26.10),
(118, 57, 'F', 11.60, 13.00, 14.70, 17.00, 19.50, 22.40, 26.30),
(119, 58, 'F', 11.60, 13.10, 14.80, 17.10, 19.60, 22.60, 26.60),
(120, 59, 'F', 11.70, 13.20, 15.00, 17.20, 19.80, 22.80, 26.80);

-- --------------------------------------------------------

--
-- Table structure for table `ref_zscore_wfh`
--

CREATE TABLE `ref_zscore_wfh` (
  `id` int(10) UNSIGNED NOT NULL,
  `height_cm` decimal(4,1) NOT NULL,
  `sex` char(1) NOT NULL,
  `sd_neg3` decimal(5,2) NOT NULL,
  `sd_neg2` decimal(5,2) NOT NULL,
  `sd_neg1` decimal(5,2) NOT NULL,
  `median` decimal(5,2) NOT NULL,
  `sd_pos1` decimal(5,2) NOT NULL,
  `sd_pos2` decimal(5,2) NOT NULL,
  `sd_pos3` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='WHO 2006 Weight-for-Height Z-scores, 45-110 cm';

--
-- Dumping data for table `ref_zscore_wfh`
--

INSERT INTO `ref_zscore_wfh` (`id`, `height_cm`, `sex`, `sd_neg3`, `sd_neg2`, `sd_neg1`, `median`, `sd_pos1`, `sd_pos2`, `sd_pos3`) VALUES
(1, 45.0, 'M', 1.90, 2.10, 2.40, 2.70, 3.00, 3.40, 3.80),
(2, 45.5, 'M', 1.90, 2.20, 2.50, 2.80, 3.10, 3.50, 3.90),
(3, 46.0, 'M', 2.00, 2.30, 2.60, 2.90, 3.20, 3.60, 4.00),
(4, 46.5, 'M', 2.10, 2.30, 2.60, 2.90, 3.30, 3.70, 4.20),
(5, 47.0, 'M', 2.10, 2.40, 2.70, 3.00, 3.40, 3.80, 4.30),
(6, 47.5, 'M', 2.20, 2.50, 2.80, 3.10, 3.50, 3.90, 4.40),
(7, 48.0, 'M', 2.30, 2.60, 2.90, 3.20, 3.60, 4.00, 4.50),
(8, 48.5, 'M', 2.30, 2.60, 2.90, 3.30, 3.70, 4.20, 4.70),
(9, 49.0, 'M', 2.40, 2.70, 3.00, 3.40, 3.80, 4.30, 4.80),
(10, 49.5, 'M', 2.50, 2.80, 3.10, 3.50, 3.90, 4.40, 4.90),
(11, 50.0, 'M', 2.60, 2.90, 3.20, 3.60, 4.00, 4.50, 5.10),
(12, 50.5, 'M', 2.60, 3.00, 3.30, 3.70, 4.20, 4.70, 5.30),
(13, 51.0, 'M', 2.70, 3.10, 3.40, 3.80, 4.30, 4.80, 5.40),
(14, 51.5, 'M', 2.80, 3.20, 3.50, 3.90, 4.40, 5.00, 5.60),
(15, 52.0, 'M', 2.90, 3.30, 3.60, 4.10, 4.50, 5.10, 5.70),
(16, 52.5, 'M', 3.00, 3.40, 3.70, 4.20, 4.70, 5.30, 5.90),
(17, 53.0, 'M', 3.10, 3.50, 3.80, 4.30, 4.80, 5.40, 6.10),
(18, 53.5, 'M', 3.20, 3.60, 4.00, 4.40, 4.90, 5.60, 6.20),
(19, 54.0, 'M', 3.30, 3.70, 4.10, 4.50, 5.10, 5.70, 6.40),
(20, 54.5, 'M', 3.40, 3.80, 4.20, 4.70, 5.20, 5.90, 6.60),
(21, 55.0, 'M', 3.50, 3.90, 4.30, 4.80, 5.40, 6.10, 6.80),
(22, 55.5, 'M', 3.60, 4.00, 4.50, 5.00, 5.60, 6.30, 7.00),
(23, 56.0, 'M', 3.80, 4.20, 4.60, 5.10, 5.80, 6.50, 7.20),
(24, 56.5, 'M', 3.90, 4.30, 4.80, 5.30, 5.90, 6.70, 7.50),
(25, 57.0, 'M', 4.00, 4.50, 4.90, 5.50, 6.10, 6.90, 7.70),
(26, 57.5, 'M', 4.20, 4.60, 5.10, 5.60, 6.30, 7.10, 7.90),
(27, 58.0, 'M', 4.30, 4.80, 5.20, 5.80, 6.50, 7.30, 8.20),
(28, 58.5, 'M', 4.40, 4.90, 5.40, 6.00, 6.70, 7.50, 8.40),
(29, 59.0, 'M', 4.60, 5.10, 5.60, 6.20, 6.90, 7.70, 8.60),
(30, 59.5, 'M', 4.70, 5.20, 5.70, 6.30, 7.10, 7.90, 8.90),
(31, 60.0, 'M', 4.90, 5.40, 5.90, 6.50, 7.30, 8.20, 9.10),
(32, 60.5, 'M', 5.00, 5.50, 6.10, 6.70, 7.50, 8.40, 9.40),
(33, 61.0, 'M', 5.10, 5.70, 6.20, 6.90, 7.70, 8.60, 9.60),
(34, 61.5, 'M', 5.30, 5.80, 6.40, 7.10, 7.90, 8.80, 9.90),
(35, 62.0, 'M', 5.40, 6.00, 6.60, 7.30, 8.10, 9.10, 10.10),
(36, 62.5, 'M', 5.60, 6.10, 6.70, 7.40, 8.30, 9.30, 10.40),
(37, 63.0, 'M', 5.70, 6.30, 6.90, 7.60, 8.50, 9.50, 10.60),
(38, 63.5, 'M', 5.80, 6.40, 7.10, 7.80, 8.70, 9.70, 10.90),
(39, 64.0, 'M', 6.00, 6.60, 7.20, 8.00, 8.90, 10.00, 11.10),
(40, 64.5, 'M', 6.10, 6.70, 7.40, 8.20, 9.10, 10.20, 11.40),
(41, 65.0, 'M', 6.30, 6.90, 7.60, 8.40, 9.30, 10.40, 11.70),
(42, 65.5, 'M', 6.40, 7.10, 7.70, 8.60, 9.50, 10.70, 11.90),
(43, 66.0, 'M', 6.60, 7.20, 7.90, 8.70, 9.70, 10.90, 12.20),
(44, 66.5, 'M', 6.70, 7.40, 8.10, 8.90, 9.90, 11.10, 12.40),
(45, 67.0, 'M', 6.90, 7.50, 8.30, 9.10, 10.10, 11.40, 12.70),
(46, 67.5, 'M', 7.00, 7.70, 8.40, 9.30, 10.40, 11.60, 12.90),
(47, 68.0, 'M', 7.10, 7.80, 8.60, 9.50, 10.60, 11.80, 13.20),
(48, 68.5, 'M', 7.30, 8.00, 8.80, 9.70, 10.80, 12.10, 13.50),
(49, 69.0, 'M', 7.40, 8.10, 8.90, 9.90, 11.00, 12.30, 13.70),
(50, 69.5, 'M', 7.60, 8.30, 9.10, 10.10, 11.20, 12.50, 14.00),
(51, 70.0, 'M', 7.70, 8.50, 9.30, 10.30, 11.40, 12.80, 14.30),
(52, 70.5, 'M', 7.90, 8.60, 9.50, 10.50, 11.60, 13.00, 14.50),
(53, 71.0, 'M', 8.00, 8.80, 9.60, 10.70, 11.80, 13.20, 14.80),
(54, 71.5, 'M', 8.10, 8.90, 9.80, 10.80, 12.00, 13.50, 15.10),
(55, 72.0, 'M', 8.30, 9.10, 10.00, 11.00, 12.30, 13.70, 15.30),
(56, 72.5, 'M', 8.40, 9.20, 10.10, 11.20, 12.50, 14.00, 15.60),
(57, 73.0, 'M', 8.60, 9.40, 10.30, 11.40, 12.70, 14.20, 15.90),
(58, 73.5, 'M', 8.70, 9.50, 10.50, 11.60, 12.90, 14.40, 16.10),
(59, 74.0, 'M', 8.80, 9.70, 10.60, 11.80, 13.10, 14.70, 16.40),
(60, 74.5, 'M', 9.00, 9.80, 10.80, 11.90, 13.30, 14.90, 16.70),
(61, 75.0, 'M', 9.10, 10.00, 11.00, 12.10, 13.50, 15.10, 16.90),
(62, 75.5, 'M', 9.20, 10.10, 11.10, 12.30, 13.70, 15.40, 17.20),
(63, 76.0, 'M', 9.40, 10.30, 11.30, 12.50, 13.90, 15.60, 17.50),
(64, 76.5, 'M', 9.50, 10.40, 11.50, 12.70, 14.10, 15.80, 17.70),
(65, 77.0, 'M', 9.60, 10.60, 11.60, 12.80, 14.30, 16.10, 18.00),
(66, 77.5, 'M', 9.80, 10.70, 11.80, 13.00, 14.50, 16.30, 18.30),
(67, 78.0, 'M', 9.90, 10.90, 12.00, 13.20, 14.70, 16.50, 18.50),
(68, 78.5, 'M', 10.10, 11.00, 12.10, 13.40, 14.90, 16.80, 18.80),
(69, 79.0, 'M', 10.20, 11.20, 12.30, 13.60, 15.10, 17.00, 19.10),
(70, 79.5, 'M', 10.30, 11.30, 12.50, 13.70, 15.30, 17.20, 19.40),
(71, 80.0, 'M', 10.40, 11.50, 12.60, 13.90, 15.50, 17.50, 19.60),
(72, 80.5, 'M', 10.60, 11.60, 12.80, 14.10, 15.70, 17.70, 19.90),
(73, 81.0, 'M', 10.70, 11.80, 13.00, 14.30, 15.90, 17.90, 20.20),
(74, 81.5, 'M', 10.80, 11.90, 13.10, 14.50, 16.20, 18.20, 20.50),
(75, 82.0, 'M', 11.00, 12.10, 13.30, 14.70, 16.40, 18.40, 20.70),
(76, 82.5, 'M', 11.10, 12.20, 13.50, 14.90, 16.60, 18.60, 21.00),
(77, 83.0, 'M', 11.20, 12.40, 13.60, 15.10, 16.80, 18.90, 21.30),
(78, 83.5, 'M', 11.40, 12.50, 13.80, 15.30, 17.00, 19.10, 21.60),
(79, 84.0, 'M', 11.50, 12.70, 14.00, 15.50, 17.20, 19.40, 21.90),
(80, 84.5, 'M', 11.70, 12.80, 14.20, 15.70, 17.50, 19.60, 22.20),
(81, 85.0, 'M', 11.80, 13.00, 14.30, 15.90, 17.70, 19.90, 22.50),
(82, 85.5, 'M', 11.90, 13.10, 14.50, 16.10, 17.90, 20.20, 22.80),
(83, 86.0, 'M', 12.10, 13.30, 14.70, 16.30, 18.20, 20.40, 23.10),
(84, 86.5, 'M', 12.20, 13.50, 14.90, 16.50, 18.40, 20.70, 23.40),
(85, 87.0, 'M', 12.30, 13.60, 15.10, 16.70, 18.60, 20.90, 23.70),
(86, 87.5, 'M', 12.50, 13.80, 15.20, 16.90, 18.80, 21.20, 24.00),
(87, 88.0, 'M', 12.60, 13.90, 15.40, 17.10, 19.10, 21.50, 24.30),
(88, 88.5, 'M', 12.80, 14.10, 15.60, 17.30, 19.30, 21.70, 24.60),
(89, 89.0, 'M', 12.90, 14.20, 15.80, 17.50, 19.50, 22.00, 24.90),
(90, 89.5, 'M', 13.00, 14.40, 16.00, 17.70, 19.70, 22.20, 25.20),
(91, 90.0, 'M', 13.20, 14.60, 16.10, 17.90, 20.00, 22.50, 25.50),
(92, 90.5, 'M', 13.30, 14.70, 16.30, 18.10, 20.20, 22.80, 25.80),
(93, 91.0, 'M', 13.50, 14.90, 16.50, 18.30, 20.40, 23.00, 26.10),
(94, 91.5, 'M', 13.60, 15.00, 16.70, 18.50, 20.70, 23.30, 26.40),
(95, 92.0, 'M', 13.80, 15.20, 16.90, 18.70, 20.90, 23.60, 26.70),
(96, 92.5, 'M', 13.90, 15.40, 17.00, 18.90, 21.10, 23.80, 27.00),
(97, 93.0, 'M', 14.10, 15.50, 17.20, 19.10, 21.40, 24.10, 27.40),
(98, 93.5, 'M', 14.20, 15.70, 17.40, 19.30, 21.60, 24.40, 27.70),
(99, 94.0, 'M', 14.40, 15.90, 17.60, 19.50, 21.80, 24.60, 28.00),
(100, 94.5, 'M', 14.50, 16.00, 17.80, 19.70, 22.10, 24.90, 28.30),
(101, 95.0, 'M', 14.70, 16.20, 18.00, 19.90, 22.30, 25.20, 28.60),
(102, 95.5, 'M', 14.80, 16.40, 18.20, 20.10, 22.50, 25.40, 28.90),
(103, 96.0, 'M', 15.00, 16.50, 18.30, 20.30, 22.80, 25.70, 29.30),
(104, 96.5, 'M', 15.10, 16.70, 18.50, 20.60, 23.00, 26.00, 29.60),
(105, 97.0, 'M', 15.30, 16.90, 18.70, 20.80, 23.30, 26.30, 29.90),
(106, 97.5, 'M', 15.50, 17.10, 18.90, 21.00, 23.50, 26.50, 30.30),
(107, 98.0, 'M', 15.60, 17.20, 19.10, 21.20, 23.80, 26.80, 30.60),
(108, 98.5, 'M', 15.80, 17.40, 19.30, 21.40, 24.00, 27.10, 31.00),
(109, 99.0, 'M', 15.90, 17.60, 19.50, 21.70, 24.30, 27.40, 31.30),
(110, 99.5, 'M', 16.10, 17.80, 19.70, 21.90, 24.50, 27.70, 31.70),
(111, 100.0, 'M', 16.30, 18.00, 19.90, 22.10, 24.80, 28.00, 32.00),
(112, 100.5, 'M', 16.40, 18.10, 20.10, 22.30, 25.00, 28.30, 32.40),
(113, 101.0, 'M', 16.60, 18.30, 20.30, 22.60, 25.30, 28.60, 32.70),
(114, 101.5, 'M', 16.80, 18.50, 20.50, 22.80, 25.60, 28.90, 33.10),
(115, 102.0, 'M', 16.90, 18.70, 20.70, 23.00, 25.80, 29.20, 33.50),
(116, 102.5, 'M', 17.10, 18.90, 20.90, 23.30, 26.10, 29.50, 33.80),
(117, 103.0, 'M', 17.30, 19.10, 21.20, 23.50, 26.40, 29.80, 34.20),
(118, 103.5, 'M', 17.50, 19.30, 21.40, 23.70, 26.60, 30.10, 34.60),
(119, 104.0, 'M', 17.60, 19.50, 21.60, 24.00, 26.90, 30.40, 35.00),
(120, 104.5, 'M', 17.80, 19.70, 21.80, 24.20, 27.20, 30.80, 35.30),
(121, 105.0, 'M', 18.00, 19.90, 22.00, 24.50, 27.50, 31.10, 35.70),
(122, 105.5, 'M', 18.20, 20.10, 22.30, 24.70, 27.70, 31.40, 36.10),
(123, 106.0, 'M', 18.40, 20.30, 22.50, 25.00, 28.00, 31.70, 36.50),
(124, 106.5, 'M', 18.60, 20.50, 22.70, 25.20, 28.30, 32.10, 36.90),
(125, 107.0, 'M', 18.70, 20.70, 23.00, 25.50, 28.60, 32.40, 37.30),
(126, 107.5, 'M', 18.90, 20.90, 23.20, 25.70, 28.90, 32.70, 37.70),
(127, 108.0, 'M', 19.10, 21.20, 23.40, 26.00, 29.20, 33.10, 38.10),
(128, 108.5, 'M', 19.30, 21.40, 23.70, 26.30, 29.50, 33.40, 38.50),
(129, 109.0, 'M', 19.50, 21.60, 23.90, 26.50, 29.80, 33.80, 38.90),
(130, 109.5, 'M', 19.70, 21.80, 24.20, 26.80, 30.10, 34.10, 39.30),
(131, 110.0, 'M', 19.90, 22.00, 24.40, 27.10, 30.40, 34.50, 39.70),
(132, 45.0, 'F', 1.90, 2.10, 2.30, 2.50, 2.80, 3.20, 3.60),
(133, 45.5, 'F', 1.90, 2.10, 2.40, 2.60, 2.90, 3.30, 3.70),
(134, 46.0, 'F', 2.00, 2.20, 2.40, 2.70, 3.00, 3.40, 3.80),
(135, 46.5, 'F', 2.00, 2.30, 2.50, 2.80, 3.10, 3.50, 3.90),
(136, 47.0, 'F', 2.10, 2.30, 2.60, 2.90, 3.20, 3.60, 4.00),
(137, 47.5, 'F', 2.20, 2.40, 2.60, 2.90, 3.30, 3.70, 4.20),
(138, 48.0, 'F', 2.20, 2.50, 2.70, 3.00, 3.40, 3.80, 4.30),
(139, 48.5, 'F', 2.30, 2.50, 2.80, 3.10, 3.50, 3.90, 4.40),
(140, 49.0, 'F', 2.40, 2.60, 2.90, 3.20, 3.60, 4.00, 4.50),
(141, 49.5, 'F', 2.40, 2.70, 3.00, 3.30, 3.70, 4.20, 4.70),
(142, 50.0, 'F', 2.50, 2.80, 3.10, 3.40, 3.80, 4.30, 4.80),
(143, 50.5, 'F', 2.60, 2.90, 3.20, 3.50, 3.90, 4.40, 5.00),
(144, 51.0, 'F', 2.70, 3.00, 3.30, 3.60, 4.00, 4.50, 5.10),
(145, 51.5, 'F', 2.80, 3.10, 3.40, 3.70, 4.20, 4.70, 5.30),
(146, 52.0, 'F', 2.80, 3.20, 3.50, 3.80, 4.30, 4.80, 5.40),
(147, 52.5, 'F', 2.90, 3.30, 3.60, 4.00, 4.40, 5.00, 5.60),
(148, 53.0, 'F', 3.00, 3.40, 3.70, 4.10, 4.60, 5.10, 5.80),
(149, 53.5, 'F', 3.10, 3.50, 3.80, 4.20, 4.70, 5.30, 5.90),
(150, 54.0, 'F', 3.20, 3.60, 3.90, 4.30, 4.80, 5.40, 6.10),
(151, 54.5, 'F', 3.30, 3.70, 4.00, 4.50, 5.00, 5.60, 6.30),
(152, 55.0, 'F', 3.40, 3.80, 4.20, 4.60, 5.10, 5.80, 6.50),
(153, 55.5, 'F', 3.50, 3.90, 4.30, 4.70, 5.30, 5.90, 6.70),
(154, 56.0, 'F', 3.60, 4.00, 4.40, 4.90, 5.40, 6.10, 6.90),
(155, 56.5, 'F', 3.70, 4.10, 4.50, 5.00, 5.60, 6.30, 7.10),
(156, 57.0, 'F', 3.80, 4.30, 4.70, 5.20, 5.80, 6.50, 7.30),
(157, 57.5, 'F', 3.90, 4.40, 4.80, 5.30, 5.90, 6.70, 7.50),
(158, 58.0, 'F', 4.00, 4.50, 5.00, 5.50, 6.10, 6.90, 7.70),
(159, 58.5, 'F', 4.20, 4.60, 5.10, 5.60, 6.30, 7.10, 7.90),
(160, 59.0, 'F', 4.30, 4.80, 5.20, 5.80, 6.50, 7.30, 8.20),
(161, 59.5, 'F', 4.40, 4.90, 5.40, 5.90, 6.60, 7.50, 8.40),
(162, 60.0, 'F', 4.50, 5.00, 5.50, 6.10, 6.80, 7.70, 8.60),
(163, 60.5, 'F', 4.70, 5.20, 5.70, 6.30, 7.00, 7.90, 8.90),
(164, 61.0, 'F', 4.80, 5.30, 5.80, 6.40, 7.20, 8.10, 9.10),
(165, 61.5, 'F', 4.90, 5.40, 6.00, 6.60, 7.40, 8.30, 9.30),
(166, 62.0, 'F', 5.10, 5.60, 6.10, 6.80, 7.60, 8.50, 9.60),
(167, 62.5, 'F', 5.20, 5.70, 6.30, 6.90, 7.80, 8.70, 9.80),
(168, 63.0, 'F', 5.30, 5.90, 6.40, 7.10, 7.90, 9.00, 10.10),
(169, 63.5, 'F', 5.50, 6.00, 6.60, 7.30, 8.10, 9.20, 10.30),
(170, 64.0, 'F', 5.60, 6.20, 6.70, 7.50, 8.30, 9.40, 10.60),
(171, 64.5, 'F', 5.70, 6.30, 6.90, 7.60, 8.50, 9.60, 10.80),
(172, 65.0, 'F', 5.90, 6.50, 7.10, 7.80, 8.70, 9.80, 11.10),
(173, 65.5, 'F', 6.00, 6.60, 7.20, 8.00, 8.90, 10.10, 11.30),
(174, 66.0, 'F', 6.10, 6.70, 7.40, 8.20, 9.10, 10.30, 11.60),
(175, 66.5, 'F', 6.30, 6.90, 7.50, 8.30, 9.30, 10.50, 11.80),
(176, 67.0, 'F', 6.40, 7.00, 7.70, 8.50, 9.50, 10.70, 12.10),
(177, 67.5, 'F', 6.50, 7.20, 7.90, 8.70, 9.70, 11.00, 12.30),
(178, 68.0, 'F', 6.70, 7.30, 8.00, 8.90, 9.90, 11.20, 12.60),
(179, 68.5, 'F', 6.80, 7.50, 8.20, 9.00, 10.10, 11.40, 12.90),
(180, 69.0, 'F', 6.90, 7.60, 8.30, 9.20, 10.30, 11.60, 13.10),
(181, 69.5, 'F', 7.10, 7.70, 8.50, 9.40, 10.50, 11.90, 13.40),
(182, 70.0, 'F', 7.20, 7.90, 8.60, 9.50, 10.70, 12.10, 13.60),
(183, 70.5, 'F', 7.30, 8.00, 8.80, 9.70, 10.90, 12.30, 13.90),
(184, 71.0, 'F', 7.50, 8.20, 9.00, 9.90, 11.10, 12.50, 14.20),
(185, 71.5, 'F', 7.60, 8.30, 9.10, 10.10, 11.30, 12.70, 14.40),
(186, 72.0, 'F', 7.70, 8.50, 9.30, 10.20, 11.50, 13.00, 14.70),
(187, 72.5, 'F', 7.80, 8.60, 9.40, 10.40, 11.70, 13.20, 14.90),
(188, 73.0, 'F', 8.00, 8.80, 9.60, 10.60, 11.90, 13.40, 15.20),
(189, 73.5, 'F', 8.10, 8.90, 9.70, 10.80, 12.10, 13.60, 15.50),
(190, 74.0, 'F', 8.20, 9.00, 9.90, 10.90, 12.20, 13.90, 15.70),
(191, 74.5, 'F', 8.40, 9.20, 10.10, 11.10, 12.40, 14.10, 16.00),
(192, 75.0, 'F', 8.50, 9.30, 10.20, 11.30, 12.60, 14.30, 16.30),
(193, 75.5, 'F', 8.60, 9.50, 10.40, 11.50, 12.80, 14.50, 16.50),
(194, 76.0, 'F', 8.70, 9.60, 10.50, 11.60, 13.00, 14.80, 16.80),
(195, 76.5, 'F', 8.90, 9.70, 10.70, 11.80, 13.20, 15.00, 17.10),
(196, 77.0, 'F', 9.00, 9.90, 10.80, 12.00, 13.40, 15.20, 17.30),
(197, 77.5, 'F', 9.10, 10.00, 11.00, 12.10, 13.60, 15.40, 17.60),
(198, 78.0, 'F', 9.30, 10.20, 11.10, 12.30, 13.80, 15.70, 17.90),
(199, 78.5, 'F', 9.40, 10.30, 11.30, 12.50, 14.00, 15.90, 18.10),
(200, 79.0, 'F', 9.50, 10.40, 11.50, 12.60, 14.20, 16.10, 18.40),
(201, 79.5, 'F', 9.60, 10.60, 11.60, 12.80, 14.40, 16.30, 18.70),
(202, 80.0, 'F', 9.80, 10.70, 11.80, 13.00, 14.60, 16.60, 18.90),
(203, 80.5, 'F', 9.90, 10.90, 11.90, 13.20, 14.80, 16.80, 19.20),
(204, 81.0, 'F', 10.00, 11.00, 12.10, 13.30, 15.00, 17.00, 19.50),
(205, 81.5, 'F', 10.20, 11.10, 12.20, 13.50, 15.20, 17.30, 19.80),
(206, 82.0, 'F', 10.30, 11.30, 12.40, 13.70, 15.40, 17.50, 20.10),
(207, 82.5, 'F', 10.40, 11.40, 12.60, 13.90, 15.60, 17.70, 20.30),
(208, 83.0, 'F', 10.60, 11.60, 12.70, 14.00, 15.80, 18.00, 20.60),
(209, 83.5, 'F', 10.70, 11.70, 12.90, 14.20, 16.00, 18.20, 20.90),
(210, 84.0, 'F', 10.80, 11.90, 13.10, 14.40, 16.20, 18.50, 21.20),
(211, 84.5, 'F', 11.00, 12.00, 13.20, 14.60, 16.40, 18.70, 21.50),
(212, 85.0, 'F', 11.10, 12.20, 13.40, 14.80, 16.60, 19.00, 21.80),
(213, 85.5, 'F', 11.20, 12.30, 13.60, 15.00, 16.90, 19.20, 22.10),
(214, 86.0, 'F', 11.40, 12.50, 13.70, 15.20, 17.10, 19.50, 22.40),
(215, 86.5, 'F', 11.50, 12.60, 13.90, 15.40, 17.30, 19.70, 22.70),
(216, 87.0, 'F', 11.70, 12.80, 14.10, 15.60, 17.50, 20.00, 23.00),
(217, 87.5, 'F', 11.80, 12.90, 14.30, 15.80, 17.70, 20.20, 23.30),
(218, 88.0, 'F', 11.90, 13.10, 14.40, 16.00, 17.90, 20.50, 23.60),
(219, 88.5, 'F', 12.10, 13.20, 14.60, 16.20, 18.20, 20.70, 23.90),
(220, 89.0, 'F', 12.20, 13.40, 14.80, 16.40, 18.40, 21.00, 24.20),
(221, 89.5, 'F', 12.30, 13.50, 15.00, 16.60, 18.60, 21.30, 24.60),
(222, 90.0, 'F', 12.50, 13.70, 15.10, 16.80, 18.80, 21.50, 24.90),
(223, 90.5, 'F', 12.60, 13.80, 15.30, 17.00, 19.10, 21.80, 25.20),
(224, 91.0, 'F', 12.80, 14.00, 15.50, 17.20, 19.30, 22.10, 25.50),
(225, 91.5, 'F', 12.90, 14.20, 15.70, 17.40, 19.50, 22.30, 25.80),
(226, 92.0, 'F', 13.00, 14.30, 15.80, 17.60, 19.70, 22.60, 26.20),
(227, 92.5, 'F', 13.20, 14.50, 16.00, 17.80, 20.00, 22.90, 26.50),
(228, 93.0, 'F', 13.30, 14.60, 16.20, 18.00, 20.20, 23.10, 26.80),
(229, 93.5, 'F', 13.50, 14.80, 16.40, 18.20, 20.40, 23.40, 27.20),
(230, 94.0, 'F', 13.60, 15.00, 16.60, 18.40, 20.60, 23.70, 27.50),
(231, 94.5, 'F', 13.70, 15.10, 16.70, 18.60, 20.90, 23.90, 27.80),
(232, 95.0, 'F', 13.90, 15.30, 16.90, 18.80, 21.10, 24.20, 28.20),
(233, 95.5, 'F', 14.00, 15.40, 17.10, 19.00, 21.30, 24.50, 28.50),
(234, 96.0, 'F', 14.20, 15.60, 17.30, 19.20, 21.60, 24.80, 28.90),
(235, 96.5, 'F', 14.30, 15.80, 17.50, 19.40, 21.80, 25.00, 29.20),
(236, 97.0, 'F', 14.50, 15.90, 17.70, 19.60, 22.10, 25.30, 29.60),
(237, 97.5, 'F', 14.60, 16.10, 17.80, 19.80, 22.30, 25.60, 29.90),
(238, 98.0, 'F', 14.80, 16.30, 18.00, 20.00, 22.50, 25.90, 30.30),
(239, 98.5, 'F', 14.90, 16.40, 18.20, 20.20, 22.80, 26.20, 30.60),
(240, 99.0, 'F', 15.10, 16.60, 18.40, 20.50, 23.00, 26.50, 31.00),
(241, 99.5, 'F', 15.20, 16.80, 18.60, 20.70, 23.30, 26.80, 31.40),
(242, 100.0, 'F', 15.40, 17.00, 18.80, 20.90, 23.50, 27.10, 31.70),
(243, 100.5, 'F', 15.60, 17.10, 19.00, 21.10, 23.80, 27.40, 32.10),
(244, 101.0, 'F', 15.70, 17.30, 19.20, 21.30, 24.00, 27.70, 32.50),
(245, 101.5, 'F', 15.90, 17.50, 19.40, 21.60, 24.30, 28.00, 32.90),
(246, 102.0, 'F', 16.00, 17.70, 19.60, 21.80, 24.60, 28.30, 33.20),
(247, 102.5, 'F', 16.20, 17.80, 19.80, 22.00, 24.80, 28.60, 33.60),
(248, 103.0, 'F', 16.40, 18.00, 20.00, 22.20, 25.10, 29.00, 34.00),
(249, 103.5, 'F', 16.50, 18.20, 20.20, 22.50, 25.40, 29.30, 34.40),
(250, 104.0, 'F', 16.70, 18.40, 20.40, 22.70, 25.60, 29.60, 34.80),
(251, 104.5, 'F', 16.90, 18.60, 20.60, 22.90, 25.90, 29.90, 35.20),
(252, 105.0, 'F', 17.00, 18.80, 20.80, 23.20, 26.20, 30.30, 35.60),
(253, 105.5, 'F', 17.20, 19.00, 21.00, 23.40, 26.50, 30.60, 36.00),
(254, 106.0, 'F', 17.40, 19.10, 21.30, 23.70, 26.70, 30.90, 36.40),
(255, 106.5, 'F', 17.60, 19.30, 21.50, 23.90, 27.00, 31.30, 36.80),
(256, 107.0, 'F', 17.70, 19.50, 21.70, 24.20, 27.30, 31.60, 37.20),
(257, 107.5, 'F', 17.90, 19.70, 21.90, 24.40, 27.60, 32.00, 37.70),
(258, 108.0, 'F', 18.10, 19.90, 22.20, 24.70, 27.90, 32.30, 38.10),
(259, 108.5, 'F', 18.30, 20.10, 22.40, 24.90, 28.20, 32.70, 38.50),
(260, 109.0, 'F', 18.50, 20.30, 22.60, 25.20, 28.50, 33.00, 39.00),
(261, 109.5, 'F', 18.70, 20.50, 22.80, 25.50, 28.80, 33.40, 39.40),
(262, 110.0, 'F', 18.90, 20.70, 23.10, 25.70, 29.10, 33.70, 39.80);

-- --------------------------------------------------------

--
-- Table structure for table `report_attachments`
--

CREATE TABLE `report_attachments` (
  `attachment_id` int(10) UNSIGNED NOT NULL,
  `report_id` int(10) UNSIGNED NOT NULL,
  `bns_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL COMMENT 'Original filename',
  `file_path` varchar(500) NOT NULL COMMENT 'Stored path relative to uploads/',
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Size in bytes',
  `file_type` varchar(100) NOT NULL DEFAULT '' COMMENT 'MIME type',
  `label` varchar(255) DEFAULT NULL COMMENT 'Optional description by BNS',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_attachments`
--

INSERT INTO `report_attachments` (`attachment_id`, `report_id`, `bns_id`, `file_name`, `file_path`, `file_size`, `file_type`, `label`, `uploaded_at`) VALUES
(10, 6, 1, 'Monitoring List.pdf', 'uploads/report_attachments/6/Monitoring_List_1780375845.pdf', 261767, 'application/pdf', NULL, '2026-06-02 04:50:45'),
(11, 6, 1, 'KusiNay – Monitoring List.pdf', 'uploads/report_attachments/6/KusiNay_____Monitoring_List_1780375865.pdf', 265653, 'application/pdf', NULL, '2026-06-02 04:51:05'),
(12, 6, 1, 'KusiNay – All Monitoring Lists.pdf', 'uploads/report_attachments/6/KusiNay_____All_Monitoring_Lists_1780375933.pdf', 294843, 'application/pdf', NULL, '2026-06-02 04:52:13');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Nutrition Officer II'),
(3, 'BNS Staff'),
(4, 'Mother'),
(5, 'Committee Chair on Health'),
(6, 'Committee Secretary'),
(7, 'Barangay Captain'),
(8, 'Market Vendor');

-- --------------------------------------------------------

--
-- Table structure for table `session_materials`
--

CREATE TABLE `session_materials` (
  `material_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `bns_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL COMMENT 'Original filename shown to user',
  `file_path` varchar(500) NOT NULL COMMENT 'Relative path under uploads/',
  `file_size` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `file_type` varchar(100) NOT NULL DEFAULT '',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `session_materials`
--

INSERT INTO `session_materials` (`material_id`, `session_id`, `bns_id`, `file_name`, `file_path`, `file_size`, `file_type`, `uploaded_at`) VALUES
(18, 12, 1, 'PinggangPinoy-Kids.pdf', 'uploads/session_materials/12/mat_6a1ed975c405c7.88700916.pdf', 3463630, 'application/pdf', '2026-06-02 13:24:05'),
(19, 12, 1, 'PinggangPinoy-Teens.pdf', 'uploads/session_materials/12/mat_6a1ed975c507a7.72741441.pdf', 3458994, 'application/pdf', '2026-06-02 13:24:05'),
(20, 12, 1, 'PinggangPinoy-Adult.pdf', 'uploads/session_materials/12/mat_6a1ed975c67527.27075015.pdf', 7723204, 'application/pdf', '2026-06-02 13:24:05'),
(21, 12, 1, 'PP-Older.pdf', 'uploads/session_materials/12/mat_6a1ed975c7c922.47701843.pdf', 2995129, 'application/pdf', '2026-06-02 13:24:05'),
(22, 12, 1, 'PinggangPinoy-Pregnant-and-Lactating-Women.pdf', 'uploads/session_materials/12/mat_6a1ed975c8cda7.16722918.pdf', 3406293, 'application/pdf', '2026-06-02 13:24:05');

-- --------------------------------------------------------

--
-- Table structure for table `session_rsvp`
--

CREATE TABLE `session_rsvp` (
  `rsvp_id` int(10) UNSIGNED NOT NULL,
  `session_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `rsvp_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `session_rsvp`
--

INSERT INTO `session_rsvp` (`rsvp_id`, `session_id`, `user_id`, `rsvp_at`) VALUES
(7, 12, 52, '2026-06-02 13:25:25'),
(8, 12, 54, '2026-06-02 13:25:33');

-- --------------------------------------------------------

--
-- Table structure for table `shopping_cart`
--

CREATE TABLE `shopping_cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL COMMENT 'From srp_references or vendor_products',
  `product_type` enum('srp','vendor') NOT NULL DEFAULT 'srp',
  `product_name` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `added_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Shopping cart items';

--
-- Dumping data for table `shopping_cart`
--

INSERT INTO `shopping_cart` (`cart_id`, `user_id`, `product_id`, `product_type`, `product_name`, `quantity`, `unit`, `price_per_unit`, `subtotal`, `added_date`) VALUES
(52, 46, 510, 'srp', 'Helens Farm', 1.00, '10pcs', 71.50, 71.50, '2026-06-21 13:38:02'),
(54, 46, 70, 'srp', 'Eggs', 1.00, 'tray', 95.00, 95.00, '2026-06-21 13:39:26'),
(55, 46, 58, 'srp', 'Cooking Oil', 1.00, '350ml', 42.50, 42.50, '2026-06-21 13:39:26'),
(56, 46, 56, 'srp', 'Rice', 1.00, 'kg', 50.00, 50.00, '2026-06-21 13:40:18'),
(57, 46, 63, 'srp', 'Sugar', 1.00, 'kg', 65.00, 65.00, '2026-06-21 13:40:18'),
(652, 54, 28, 'srp', 'Ahos (Imported)', 1.00, 'kg', 125.00, 125.00, '2026-06-25 06:58:31'),
(653, 54, 37, 'srp', 'Sibuyas Dahon', 1.00, 'bundle', 120.00, 120.00, '2026-06-25 06:58:31');

-- --------------------------------------------------------

--
-- Table structure for table `srp_references`
--

CREATE TABLE `srp_references` (
  `srp_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL COMMENT 'Product name (e.g., Tomato, CDO Sardines)',
  `product_variant` varchar(100) DEFAULT NULL COMMENT 'Variant/brand (e.g., Galaxy, Regular, 155g)',
  `category` varchar(100) DEFAULT NULL COMMENT 'Category (e.g., Vegetables, Canned Goods)',
  `subcategory` varchar(100) DEFAULT NULL COMMENT 'Subcategory (e.g., Lowland Vegetables, Highland Vegetables)',
  `unit` varchar(50) NOT NULL COMMENT 'Unit of measure (kg, pcs, liters, bundle, etc.)',
  `srp_price` decimal(10,2) NOT NULL COMMENT 'Government suggested retail price',
  `price_source` enum('Food Terminal - Retail','Food Terminal - Wholesale','DTI Supermarket','Other') NOT NULL DEFAULT 'Food Terminal - Retail',
  `market_location` varchar(100) DEFAULT NULL COMMENT 'Market name (e.g., Bankerohan Market, DFTC Taboan, Nationwide)',
  `price_date` date NOT NULL COMMENT 'Date of this SRP (when government published it)',
  `product_image_url` varchar(500) DEFAULT NULL COMMENT 'URL to product image (optional)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '1 = Current price, 0 = Historical/outdated',
  `notes` text DEFAULT NULL COMMENT 'Additional notes',
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Government SRP reference prices for grocery list estimates';

--
-- Dumping data for table `srp_references`
--

INSERT INTO `srp_references` (`srp_id`, `product_name`, `product_variant`, `category`, `subcategory`, `unit`, `srp_price`, `price_source`, `market_location`, `price_date`, `product_image_url`, `is_active`, `notes`, `created_date`, `updated_date`) VALUES
(1, 'Ampalaya', 'Galaxy', 'Vegetables', 'Lowland Vegetables', 'kg', 90.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQkrUg6B6aMNpAkHwg7zEXq4UiCasXaI3RE2TIIJWnztg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:55:56'),
(2, 'Batong', 'Negrostar', 'Vegetables', 'Lowland Vegetables', 'kg', 85.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRVAVi-U-ysi6ISE4Wdp-e1f-W6y3kyLO_urDXz8vIbFg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:57:54'),
(3, 'Kalabasa', 'Suprema', 'Vegetables', 'Lowland Vegetables', 'kg', 35.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT-B0SZfAfH5VYENLZXFO_qtL_f9VTMANDYxMzpYB7_3Q&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:59:57'),
(4, 'Kamatis', 'Diamante Big', 'Vegetables', 'Lowland Vegetables', 'kg', 60.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:39:36'),
(5, 'Pechay', 'Native Condor', 'Vegetables', 'Lowland Vegetables', 'kg', 120.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTRLPoEy8t7-qz376-fD_n3ZjY8HfXk2c9HJr_wyRRsw_yornAKCgmfCHb&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:04:48'),
(6, 'Okra', 'Smooth Green', 'Vegetables', 'Lowland Vegetables', 'kg', 95.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTwUBkGGhrBL0RwCsXkc5LYUq-yhfceFR0i26brfRBiw&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:00:29'),
(7, 'Patola', 'Ordinary', 'Vegetables', 'Lowland Vegetables', 'kg', 75.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQAQ2Hkc7hSTM7QrP7ZlLNJYOlY54Rju6zDSk4b32IdLA&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:03:49'),
(8, 'Pipino', 'Mega C', 'Vegetables', 'Lowland Vegetables', 'kg', 40.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT5GjvVGAvRKJUubHp-pRUnoqvtIhKBazZubkh1qLblJA&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:06:03'),
(9, 'Radish', 'Ordinary', 'Vegetables', 'Lowland Vegetables', 'kg', 75.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTgOINrxlKQbU24TftVYwIpXlPYqSSEL_WpdyYAxF5bCw&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:06:37'),
(10, 'Talong', 'Banate King', 'Vegetables', 'Lowland Vegetables', 'kg', 50.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT2pB6xvSeWsKNSqEdUcYq_WNOWE04kHB0APGLupKWuHA&s', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:09:29'),
(11, 'Upo', 'Mayumi', 'Vegetables', 'Lowland Vegetables', 'kg', 40.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://img.lazcdn.com/g/p/357c12f552d0fe638f7bfb40c4a1e0b4.png_960x960q80.png_.webp', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:10:45'),
(12, 'Alugbati', NULL, 'Vegetables', 'Lowland Vegetables', 'kg', 50.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSgrEAuZ__osY2KTzvROzfWQDds0w-zueVuxss1uY6Idg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:55:03'),
(13, 'Baguio Beans', 'Pencil', 'Vegetables', 'Highland Vegetables', 'kg', 80.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTljQGawg3srZHJZnSsd8mf_66bpSzuvqc07orOTjqkww&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:56:36'),
(14, 'Carrots', 'Big', 'Vegetables', 'Highland Vegetables', 'kg', 100.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(15, 'Carrots', 'Medium', 'Vegetables', 'Highland Vegetables', 'kg', 85.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(16, 'Carrots', 'Small', 'Vegetables', 'Highland Vegetables', 'kg', 75.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1598170845058-32b9d6a5da37?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(17, 'Pechay', 'Chinese', 'Vegetables', 'Highland Vegetables', 'kg', 70.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRt3GdiSYSpQZe2hmxnIUX4p-SVPLA-qFywT0q6Kzq1lg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:05:18'),
(18, 'Patatas', 'Big', 'Vegetables', 'Highland Vegetables', 'kg', 115.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRm0jy9qTF6rGx3LOtAkaJQ_ojNXGoxY_EjjjNq0yCsAA&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:01:00'),
(19, 'Patatas', 'Medium', 'Vegetables', 'Highland Vegetables', 'kg', 95.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQWnNhNBYUzM9JEOjJuGTXWCLHUZkV4ucCY8oiP0x3Jxg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:01:42'),
(20, 'Patatas', 'Small', 'Vegetables', 'Highland Vegetables', 'kg', 75.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_CnSLSXi5mvzPwwzXm3ISmveTiXWY8voSfrRR9vGQHg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:02:45'),
(21, 'Repolyo', 'Wakamini', 'Vegetables', 'Highland Vegetables', 'kg', 80.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQmsIp1N6EtSQ5UlaOicaD2S0lhV8ceuPIIocCT8BZumQ&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:07:20'),
(22, 'Sayote', 'Big', 'Vegetables', 'Highland Vegetables', 'pcs', 18.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSLB10UPHlFVWxXPUqEIuyEUTcCJKiwBwB7OnDdm9neeg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:07:59'),
(23, 'Sayote', 'Small', 'Vegetables', 'Highland Vegetables', 'pcs', 15.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSAb2gtYczDK6hdkrgHBlv7QWu5V_2A3vyLaCWHSheIfzq6fZceUc-2xsPP&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:08:31'),
(24, 'Broccoli', NULL, 'Vegetables', 'Highland Vegetables', 'kg', 220.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqYk631HYHaEyPjgCJVH6QkTIBMoTFLoAYcL2sX8_boQ&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:58:42'),
(25, 'Cauliflower', NULL, 'Vegetables', 'Highland Vegetables', 'kg', 180.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ34uAdlCjfRTmItMo8B11jK24SWIQ9wkJ0SvovGrj2DA&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:59:16'),
(26, 'Lettuce', 'Curly', 'Vegetables', 'Highland Vegetables', 'kg', 400.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(27, 'Lettuce', 'Ball', 'Vegetables', 'Highland Vegetables', 'kg', 250.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1622206151226-18ca2c9ab4a1?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(28, 'Ahos', 'Imported', 'Spices', NULL, 'kg', 125.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTETUJ7KxN5JdLWBKn7BwXMFyDFp3XvNSwkuHEwrhpj9Q&s', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:12:15'),
(29, 'Atsal', 'Smooth Cayene', 'Spices', NULL, 'kg', 180.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRfPq0BgiSAtS829PNjxAnu1-F2AooXZNWrlW6iTE0jRQ&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:12:42'),
(30, 'Atsal', 'Sultan', 'Spices', NULL, 'kg', 150.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRCDK6rG8iMw5dAbtC5KBBy2uqkNFwQ7QeahHxHti3Cw&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:13:13'),
(31, 'Bombay', 'Native', 'Spices', NULL, 'kg', 100.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOkzbBdA1iquqpsjvBHbmmWjvmD_tvuahRXtOTMWOU6Q&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-23 03:26:29'),
(32, 'Bombay', 'White', 'Spices', NULL, 'kg', 130.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTQSTbjI9mOd8521reb9r-wUp-Gxy9YpFIenxG_ZX7Rqg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-23 03:26:36'),
(33, 'Sili', 'Labuyo', 'Spices', NULL, 'kg', 220.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTkoMt3Iu15FZMPyuuDW0OTLXMh9KEzYTGFmdclOcKgSw&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:17:23'),
(34, 'Sili', 'Kolikot', 'Spices', NULL, 'kg', 200.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0c2iw4Day9yXlc-7PHxb7IkuoHsakY5AvG_gpgG9wjg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:18:02'),
(35, 'Sili', 'Native', 'Spices', NULL, 'kg', 600.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQfIOSieGl2f0E-KuKX9aF-rRpCfl0TFrimkmGuBjyGZw&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:19:48'),
(36, 'Luya', 'Hawaiian', 'Spices', NULL, 'kg', 100.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsvhZ6KgPSCNBsAcLKnxg7H9dYyaJ7bkUOSgiqo6e0mg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:16:13'),
(37, 'Sibuyas Dahon', NULL, 'Spices', NULL, 'bundle', 120.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRmQHnkZ37KzI9D9n3O8fFdCSI9qNu9FvmgRK2GSiMZLQ&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:16:50'),
(38, 'Tanglad', NULL, 'Spices', NULL, 'bundle', 10.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSoNRolVT5tikQI5mPIXesW4gqY0_JuLVxiORkq6yh9-g&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 05:20:19'),
(39, 'Gabi', 'Bisol', 'Rootcrops', NULL, 'kg', 70.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1587735243475-46d07e91af3d?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-19 14:51:02'),
(40, 'Kamote', NULL, 'Rootcrops', NULL, 'kg', 55.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1594282554251-5b8f96f0ef24?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-19 14:51:02'),
(41, 'Karlang', NULL, 'Rootcrops', NULL, 'kg', 60.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1594282554251-5b8f96f0ef24?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-19 14:51:02'),
(42, 'Cassava', NULL, 'Rootcrops', NULL, 'kg', 25.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1594282554251-5b8f96f0ef24?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-19 14:51:02'),
(43, 'Durian', 'Puyat', 'Fruits', NULL, 'kg', 250.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1585567694106-8f008c7c0878?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-19 14:51:02'),
(44, 'Kalamansi', 'Local', 'Fruits', NULL, 'kg', 50.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(45, 'Mangga', 'Cebu', 'Fruits', NULL, 'kg', 120.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1605027990121-cbae9d39ce8d?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(46, 'Papaya', 'Solo', 'Fruits', NULL, 'kg', 30.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1587049352846-4a222e784acc?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(47, 'Pomelo', 'Magallanes', 'Fruits', NULL, 'kg', 85.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1609742681970-85a5c5dcd9c7?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-19 14:51:02'),
(48, 'Saging', 'Lakatan', 'Fruits', NULL, 'kg', 55.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:39:36'),
(49, 'Saging', 'Latundan', 'Fruits', NULL, 'kg', 45.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:39:36'),
(50, 'Saging', 'Cardava', 'Fruits', NULL, 'kg', 30.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:39:36'),
(51, 'Avocado', NULL, 'Fruits', NULL, 'kg', 85.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1587735243475-46d07e91af3d?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(52, 'Watermelon', 'Ordinary', 'Fruits', NULL, 'kg', 45.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1621583441131-eb0c9e7e9832?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(53, 'Poncan', NULL, 'Fruits', NULL, 'pcs', 15.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1580480055273-228ff5388ef8?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-19 14:51:02'),
(54, 'Grapes', NULL, 'Fruits', NULL, 'kg', 280.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1515162816999-a0c47dc192f7?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:35:47'),
(55, 'Sweet Corn', NULL, 'Fruits', NULL, 'pcs', 25.00, 'Food Terminal - Retail', 'Bankerohan Market', '2026-06-19', 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-19 14:26:37'),
(56, 'Rice', 'Regular Milled', 'Grains', 'Rice', 'kg', 50.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:39:36'),
(57, 'Rice', 'Well Milled', 'Grains', 'Rice', 'kg', 54.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:39:36'),
(58, 'Cooking Oil', 'Baguio Regular', 'Condiments', NULL, '350ml', 42.50, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSwubuKA1iZSY5KJPdCCCHlheDkU0a20GzoZtKLpV1eYg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:21:15'),
(59, 'Cooking Oil', 'Minola Gold', 'Condiments', NULL, '1L', 110.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRrRF07u4dxvGObaPMdLfk8zhHmzNAkU5t1efK1Do9sBA&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:21:43'),
(60, 'Soy Sauce', 'Silver Swan', 'Condiments', NULL, '385ml', 23.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS3JbyqsjswY1R55qaxurmHN9KZgzldvMSODqBS08bSow&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:30:19'),
(61, 'Vinegar', 'Silver Swan', 'Condiments', NULL, '385ml', 13.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRpaFg0-5WrmUnPi3-Ie7NBOzE1ZcJIPvART6PAEHq52w&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:37:55'),
(62, 'Salt', 'Pamora', 'Condiments', NULL, '1kg', 26.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://image.made-in-china.com/202f0j00TdcWryewSqol/Apam-Polyacrylamide-Acid-Sodium-Salt.webp', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:29:32'),
(63, 'Sugar', 'Brown', 'Condiments', NULL, 'kg', 65.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSEc3usE7kbmHetH9Ut2_zKdrU63jKy5dTBMIAyNwvx-A&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:35:10'),
(64, 'Sugar', 'White', 'Condiments', NULL, 'kg', 70.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLPi82o5ztKlkxa-tO32kolL-bbSjgrMd1QvWFXoTDTQ&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:35:58'),
(65, 'Sardines', 'Mega Regular', 'Canned Goods', NULL, '155g', 22.90, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRrL_A4vgeXQin-wxfQjQo4SkXttZfEatnssztoTvevhw&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:16:11'),
(66, 'Sardines', 'Ligo Regular', 'Canned Goods', NULL, '155g', 26.75, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTROx3A0l4ouVD10E1zd3qBF9eMsd0tX6j0b3rwOB0CNA&s', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:16:56'),
(67, 'Corned Beef', 'Purefoods', 'Canned Goods', NULL, '150g', 55.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS8K7QZfyR92hLEi2W6zcGMZqYlOl6ioVHYoj2I6rNTxg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:19:02'),
(68, 'Milk', 'Alaska Evaporada', 'Dairy', 'Evaporated Milk', '370ml', 38.50, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSW46btXdB-B4IEKcMUoo2jqkZjlGX7PQDxwK2qIVSHUg&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:50:23'),
(69, 'Milk', 'Bear Brand', 'Dairy', 'Powdered Milk', '300g', 220.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPQt9ZbX8npZVMRI0MkhB7t7xoMYnHrJQlpcrfQ4WfcA&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 04:42:52'),
(70, 'Eggs', 'Medium', 'Protein', NULL, 'tray', 95.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:39:37'),
(71, 'Coffee', '3-in-1 Original', 'Beverages', NULL, '10pcs', 47.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRsIHtS9dkexZyYlMXc-Wr5L1e3Qr2JZt3GUlpoxpvfv3SXeiZJX7SZ2ME&s=10', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 03:48:23'),
(72, 'Instant Noodles', 'Lucky Me Pancit Canton', 'Instant Food', NULL, '60g', 9.00, 'DTI Supermarket', 'Nationwide', '2026-05-11', 'https://cdn.manilastandard.net/wp-content/uploads/2022/09/lucky_me1.jpg', 1, NULL, '2026-06-19 10:51:21', '2026-06-22 08:56:00'),
(433, 'Kuhaku', 'Premium Japanese Rice', 'Grains', 'Rice', 'kg', 56.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, 'Premium imported rice ₱54-58/kg', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(434, 'Kuhaku', 'Premium Japanese Rice 5kg', 'Grains', 'Rice', '5kg', 275.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, '5kg sack ₱265-285', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(435, 'Banay-Banay', 'Premium Rice', 'Grains', 'Rice', 'kg', 52.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, 'Local premium ₱51-53/kg', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(436, 'Banay-Banay', 'Premium Rice 5kg', 'Grains', 'Rice', '5kg', 255.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, '5kg sack ₱250-260', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(437, 'Banay-Banay', 'Premium Rice 25kg', 'Grains', 'Rice', '25kg', 1270.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, '25kg sack ₱1,250-1,290', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(438, 'V-160', 'Local Premium Rice', 'Grains', 'Rice', 'kg', 53.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, 'Local premium ₱52-54/kg', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(439, 'V-160', 'Local Premium Rice 25kg', 'Grains', 'Rice', '25kg', 1297.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, '25kg sack ₱1,275-1,320', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(440, 'Toner', 'Local Premium Rice', 'Grains', 'Rice', 'kg', 53.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=400', 1, 'Local premium ₱52-54/kg', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(441, 'Siniat', 'Corn Grits #14', 'Grains', 'Corn', 'kg', 41.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=400', 1, 'Corn grits ₱38-44/kg', '2026-06-19 14:24:46', '2026-06-22 03:39:52'),
(442, 'Siniat', 'Corn Grits #16', 'Grains', 'Corn', 'kg', 41.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1551754655-cd27e38d2076?w=400', 1, 'Corn grits ₱38-44/kg', '2026-06-19 14:24:46', '2026-06-22 03:39:52'),
(443, 'Mega Sardines', 'In Tomato Sauce 155g', 'Canned Goods', 'Sardines', '155g', 20.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT88q3yTtmseXQFtpT4vRn4VmOD5yHaRUqus2JdPb5s5g&s=10', 1, '₱19.50-21.00', '2026-06-19 14:24:46', '2026-06-22 04:10:52'),
(444, 'Ligo Sardines', 'Regular 155g', 'Canned Goods', 'Sardines', '155g', 21.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTROx3A0l4ouVD10E1zd3qBF9eMsd0tX6j0b3rwOB0CNA&s', 1, '₱20.00-22.00', '2026-06-19 14:24:46', '2026-06-22 04:07:31'),
(445, 'Ligo Sardines', 'Extra Hot 155g', 'Canned Goods', 'Sardines', '155g', 21.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS4rZER1gqrQpgM_QIHpn-QlY6cHsE6-3q6mM7yZJkPVQ&s', 1, '₱20.00-22.00', '2026-06-19 14:24:46', '2026-06-22 04:08:09'),
(446, '555 Sardines', 'Fried 155g', 'Canned Goods', 'Sardines', '155g', 19.75, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRxpKgHGZIT0_LJ8FiXjfw07gdqzT0uAp1fSSqIOEFnJw&s=10', 1, '₱19.00-20.50', '2026-06-19 14:24:46', '2026-06-22 03:55:39'),
(447, '555 Sardines', 'Tomato Sauce 155g', 'Canned Goods', 'Sardines', '155g', 19.75, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://shopsuki.ph/cdn/shop/products/748485200019-2_600x600_crop_center.jpg?v=1676857577', 1, '₱19.00-20.50', '2026-06-19 14:24:46', '2026-06-22 03:56:46'),
(448, 'Argentina', 'Corned Beef Regular 150g', 'Canned Goods', 'Corned Beef', '150g', 37.75, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTiykTLMPzJ4E_8-N6zdd3Tfza17Q-yYrOjiuWRemU06A&s=10', 1, '₱36.50-39.00', '2026-06-19 14:24:46', '2026-06-22 03:59:21'),
(449, 'Purefoods', 'Corned Beef Premium 150g', 'Canned Goods', 'Corned Beef', '150g', 78.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQqsz_M1R5XsKu6-AJTuZGXpZKEGOyoliz9Wf4dZ_g4uA&s=10', 1, '₱75.00-82.00', '2026-06-19 14:24:46', '2026-06-22 04:05:27'),
(450, 'Highlands', 'Corned Beef Premium 150g', 'Canned Goods', 'Corned Beef', '150g', 75.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTi_sjjv-G-ZAgSyOp-ZiKPg9f3LBF7FrfFPxsxpbzhXA&s=10', 1, '₱72.00-78.00', '2026-06-19 14:24:46', '2026-06-22 04:06:00'),
(451, 'Bingo', 'Corned Beef Budget 150g', 'Canned Goods', 'Corned Beef', '150g', 24.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTs_hIOTxn9JpadnWPBTPTu_S_kzBiUd84kgTHscOr7upnJgYhFvEhagpY&s=10', 1, '₱23.00-25.50', '2026-06-19 14:24:46', '2026-06-22 04:01:38'),
(452, 'Winner', 'Corned Beef Budget 150g', 'Canned Goods', 'Corned Beef', '150g', 24.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7q5Tn0Fotoi3Uql1di6he54RS0lJFXhlpXD1NBRfplw&s=10', 1, '₱23.00-25.50', '2026-06-19 14:24:46', '2026-06-22 04:17:38'),
(453, 'Century Tuna', 'Flakes in Oil 180g', 'Canned Goods', 'Tuna', '180g', 46.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://store.iloilosupermart.com/wp-content/uploads/2020/05/CENTURY.jpg', 1, '₱43.50-49.50', '2026-06-19 14:24:46', '2026-06-22 04:03:44'),
(454, 'Century Tuna', 'Hot & Spicy 180g', 'Canned Goods', 'Tuna', '180g', 46.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://phroots.com/cdn/shop/products/P-SEAF008_748485100098_P1_4279f577-ee40-4f42-bdf3-0017d363d5bb.jpg?v=1618293876', 1, '₱43.50-49.50', '2026-06-19 14:24:46', '2026-06-22 04:04:28'),
(455, 'San Marino', 'Corned Tuna 180g', 'Canned Goods', 'Tuna', '180g', 47.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQxU5NjVc1rVTBKm2h4CAyAbz-Mwl1aRn0yJCEqLczYNg&s=10', 1, '₱44.00-50.00', '2026-06-19 14:24:46', '2026-06-22 04:14:55'),
(456, '555 Tuna', 'Flakes Caldereta 180g', 'Canned Goods', 'Tuna', '180g', 34.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://foodpanda.dhmedia.io/image/darsktores-ph/food/7484857000384.jpg?height=480', 1, '₱32.00-36.00', '2026-06-19 14:24:46', '2026-06-22 03:57:47'),
(457, '555 Tuna', 'Flakes Mechado 180g', 'Canned Goods', 'Tuna', '180g', 34.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS6lkgU-JjmkdYBIYDr8s2MQ5Wew-_oyjs0ZOoTXIg_PftCs9oOBLX8UsU&s=10', 1, '₱32.00-36.00', '2026-06-19 14:24:46', '2026-06-22 03:58:30'),
(458, 'CDO', 'Carne Norte 150g', 'Canned Goods', 'Canned Meat', '150g', 27.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://primomart.ph/cdn/shop/files/4800249908848_44f154fe-9724-4873-aaf3-ce0fe152e509_1024x1024.jpg?v=1754888273', 1, '₱26.00-29.00', '2026-06-19 14:24:46', '2026-06-22 04:03:05'),
(459, 'Purefoods', 'Beef Loaf 150g', 'Canned Goods', 'Canned Meat', '150g', 22.75, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS7MI-UldMibDeHl80zszk6_DRdRinRVls8Y2wF5GQigw&s=10', 1, '₱21.50-24.00', '2026-06-19 14:24:46', '2026-06-22 04:14:04'),
(460, 'Argentina', 'Meat Loaf 150g', 'Canned Goods', 'Canned Meat', '150g', 23.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT9FHxrXOoct37AFOgFKJ4wXgiS5E-Gy9PKJw5Vnc0R55gPzj5PwM9stY2-&s=10', 1, '₱22.00-24.50', '2026-06-19 14:24:46', '2026-06-22 04:00:08'),
(461, 'Lucky Me!', 'Instant Mami Chicken 55g', 'Instant Food', 'Noodles', '55g', 13.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRYM8DkLfryztbZAVVki170sZXOHmuXzdd_IZ2yReKt3A&s=10', 1, '₱12.00-14.50', '2026-06-19 14:24:46', '2026-06-22 08:57:24'),
(462, 'Lucky Me!', 'Instant Mami Beef 55g', 'Instant Food', 'Noodles', '55g', 13.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSWwGOSgFCCWqzIv2NV6nKtBPFleB90E7Evll16xTByug&s', 1, '₱12.00-14.50', '2026-06-19 14:24:46', '2026-06-22 08:58:04'),
(463, 'Lucky Me!', 'Pancit Canton Extra Hot 80g', 'Instant Food', 'Noodles', '80g', 17.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRxEPvazMg54GfJsUo9pxJyhTpqkuc4c6GsoEocQPWjRKazt4opYWn9Ghk&s=10', 1, '₱16.00-18.50', '2026-06-19 14:24:46', '2026-06-22 08:58:39'),
(464, 'Lucky Me!', 'Pancit Canton Sweet & Spicy 80g', 'Instant Food', 'Noodles', '80g', 17.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR6okGucE2Xg4p9mx--tVgTsxsSUqAJ1bFuwMpuPTnV4w&s=10', 1, '₱16.00-18.50', '2026-06-19 14:24:46', '2026-06-22 08:59:45'),
(465, 'Lucky Me!', 'Pancit Canton Kalamansi 80g', 'Instant Food', 'Noodles', '80g', 17.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOUWDFDhTHczBVWAR62Lri3zwe6MuN7yDPPDjPSz9P7Q&s=10', 1, '₱16.00-18.50', '2026-06-19 14:24:46', '2026-06-22 09:00:24'),
(466, 'Payless', 'Instant Mami 55g', 'Instant Food', 'Noodles', '55g', 12.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTwTUvMStbjJgcvke-W4CufNcZgmhDj45wmsrhr4m7N3g&s=10', 1, '₱11.00-13.00', '2026-06-19 14:24:46', '2026-06-22 09:02:55'),
(467, 'Nissin', 'Cup Noodles Mini 40g', 'Instant Food', 'Cup Noodles', '40g', 23.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRvQ_v_BnCvXytHvl6Zpe7g70rTLFgg2ukPa3JUWRA08Q&s=10', 1, '₱22.00-25.00', '2026-06-19 14:24:46', '2026-06-22 09:01:50'),
(468, 'Bear Brand', 'Fortified Powdered Milk 300g', 'Dairy', 'Powdered Milk', '300g', 135.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTPQt9ZbX8npZVMRI0MkhB7t7xoMYnHrJQlpcrfQ4WfcA&s=10', 1, '₱132-138', '2026-06-19 14:24:46', '2026-06-22 04:43:55'),
(469, 'Bear Brand', 'Fortified Powdered Milk 700g', 'Dairy', 'Powdered Milk', '700g', 302.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR4QCQBUoCIY7iCzYneGSETIaaLm6HCZY-XCS01oMg0IQ&s=10', 1, '₱295-310', '2026-06-19 14:24:46', '2026-06-22 04:44:45'),
(470, 'Nido', 'Fortigrow 370g', 'Dairy', 'Powdered Milk', '370g', 202.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRLOW_4zA2E63ZBwFcZ6EeWCKnetskUabJA13HDkJo2rQ&s=10', 1, '₱195-210', '2026-06-19 14:24:46', '2026-06-22 04:53:00'),
(471, 'Alaska', 'Fortified Powdered Milk 300g', 'Dairy', 'Powdered Milk', '300g', 128.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTWoM60OduXBow5QVIXIacB70iyD4oyJOlK5fFRKQ1u3w&s=10', 1, '₱124-132', '2026-06-19 14:24:46', '2026-06-22 04:39:59'),
(472, 'Cowhead', 'Pure Fresh Milk 1L', 'Dairy', 'Fresh Milk', '1L', 105.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSDif2avnNDsNLAgM0fRMprOMxZ1tyurgcRLe_m5RNT3Q&s=10', 1, '₱95-115', '2026-06-19 14:24:46', '2026-06-22 04:45:39'),
(473, 'Harvey Fresh', 'Full Cream Milk 1L', 'Dairy', 'Fresh Milk', '1L', 108.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRDpwnT4nXoveLmoGEZxwjCJ58OR9rx9n4bAVxFb7Qf6w&s', 1, '₱98-118', '2026-06-19 14:24:46', '2026-06-22 04:47:08'),
(474, 'Nestle', 'Just Milk Full Cream 1L', 'Dairy', 'Fresh Milk', '1L', 112.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSSXWO1nD58gIifFTTaOSO96NdNvZvrt1M-D_Rn67ZooA&s=10', 1, '₱105-120', '2026-06-19 14:24:46', '2026-06-22 04:51:33'),
(475, 'Alaska', 'Evaporated Milk 300ml', 'Dairy', 'Evaporated Milk', '300ml', 38.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSmg8KlNLmCyasrgb8rpjoR-978LZFQXltkq1OwDUCx_g&s=10', 1, '₱36-41', '2026-06-19 14:24:46', '2026-06-22 04:40:33'),
(476, 'Alaska', 'Condensed Milk 300ml', 'Dairy', 'Condensed Milk', '300ml', 55.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcROxnlHhSCXij34Zvn_lt88YnfK1pDGGNsVQLH2NwjANg&s=10', 1, '₱52-58', '2026-06-19 14:24:46', '2026-06-22 04:41:04'),
(477, 'Magnolia', 'Quickmelt Cheese 165g', 'Dairy', 'Cheese', '165g', 88.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ01dq6YgSoTZgvrEpw8A2ykwuzDKR_NqVII3XSDGcbZQ&s=10', 1, '₱82-95', '2026-06-19 14:24:46', '2026-06-22 04:48:34'),
(478, 'Eden', 'Cheese Block 165g', 'Dairy', 'Cheese', '165g', 72.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ-J77K9AfctY-165u3mDoygte0yDHABCwt94FdMktw8Q&s=10', 1, '₱68-76', '2026-06-19 14:24:46', '2026-06-22 04:46:16'),
(479, 'Anchor', 'Salted Butter 225g', 'Dairy', 'Butter', '225g', 155.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQBvavnm3guZ0yNMcFMwLyKFv45zjedQ3JfRX2ox69_6w&s=10', 1, '₱145-165', '2026-06-19 14:24:46', '2026-06-22 04:42:18'),
(480, 'Magnolia', 'Gold Butter Salted 225g', 'Dairy', 'Butter', '225g', 149.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQBqvpBpsr81A-o8YNzZ42cREY_Gum8iowX6F_okOgSiA&s=10', 1, '₱140-158', '2026-06-19 14:24:46', '2026-06-22 04:49:34'),
(481, 'Nestlé', 'Fruit Selection Yogurt 125g', 'Dairy', 'Yogurt', '125g', 54.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQvyqqLIcUWbOhHvcMKGwpcOGmtlHAxq2tOPnp2LcL3wQ&s=10', 1, '₱50-58', '2026-06-19 14:24:46', '2026-06-22 04:52:15'),
(482, 'Pascual', 'Creamy Yogurt 125g', 'Dairy', 'Yogurt', '125g', 61.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSILQQP5VYi9laMF92a6bEHJUkRpsGpKYXiiZx-g0DWiw&s=10', 1, '₱55-68', '2026-06-19 14:24:46', '2026-06-22 04:53:34'),
(483, 'Jack n Jill Piattos', 'Cheese 85g Large', 'Snacks', 'Chips', '85g', 34.95, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400', 1, '₱33.90-36.00', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(484, 'Jack n Jill Nova', 'Country Cheddar 78g', 'Snacks', 'Chips', '78g', 37.30, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400', 1, '₱36.10-38.50', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(485, 'Jack n Jill Chippy', 'Barbecue 110g', 'Snacks', 'Chips', '110g', 30.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400', 1, '₱28.50-32.00', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(486, 'Oishi', 'Prawn Crackers 60g', 'Snacks', 'Crackers', '60g', 23.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1566478989037-eec170784d0b?w=400', 1, '₱22.00-25.00', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(487, 'SkyFlakes', 'Crackers 10-pack', 'Snacks', 'Biscuits', '10pcs', 68.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400', 1, '₱65-72', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(488, 'Fita', 'Crackers 10-pack', 'Snacks', 'Biscuits', '10pcs', 71.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?w=400', 1, '₱68-75', '2026-06-19 14:24:46', '2026-06-19 14:26:37'),
(489, 'UFC', 'Golden Fiesta Palm Oil 1L', 'Condiments', 'Cooking Oil', '1L', 191.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ3gycxzeAZ57b3M8We86DLzMoH5W7s8zplPxQt9RdLMg&s=10', 1, '₱185-198', '2026-06-19 14:24:46', '2026-06-22 04:36:59'),
(490, 'Minola', 'Coconut Oil 1L', 'Condiments', 'Cooking Oil', '1L', 202.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQGRmSJ0ht-XRAGlpSmo90IQ110KL62mNZUx_ULyCLG4Q&s=10', 1, '₱195-210', '2026-06-19 14:24:46', '2026-06-22 04:27:40'),
(491, 'Datu Puti', 'Soy Sauce 385ml', 'Condiments', 'Soy Sauce', '385ml', 22.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQEOq63S4t_tsKAVvItRaigQVJ6On5gRcNxls2HnkXjIw&s=10', 1, '₱20-24', '2026-06-19 14:24:46', '2026-06-22 04:22:47'),
(492, 'Datu Puti', 'Vinegar 385ml', 'Condiments', 'Vinegar', '385ml', 20.25, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT2hq66hmAVgBOQP3b8m3luePgMkhq6YMvYWAwVdK_N8Q&s', 1, '₱18.50-22', '2026-06-19 14:24:46', '2026-06-22 04:23:21'),
(493, 'Silver Swan', 'Soy Sauce 1L', 'Condiments', 'Soy Sauce', '1L', 50.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSjr431NOAFiNpuWXHAxAQzJy-_2n25ECLmYTtG_hcNpQ&s=10', 1, 'Standard price', '2026-06-19 14:24:46', '2026-06-22 04:31:32'),
(494, 'Silver Swan', 'Vinegar 1L', 'Condiments', 'Vinegar', '1L', 50.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTbKgXFrqu5HqFJ36KtDf-UGDcegke6k8oIfaJ7YkRujA&s=10', 1, 'Standard price', '2026-06-19 14:24:46', '2026-06-22 04:32:11'),
(495, 'Del Monte', 'Tomato Sauce 250g', 'Condiments', 'Sauce', '250g', 31.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQBKe4a_st-Kn-1jbw3EHqFd2t-vOMsYMril_2M_Lyolw&s=10', 1, '₱28-34', '2026-06-19 14:24:46', '2026-06-22 04:24:16'),
(496, 'Del Monte', 'Tomato Ketchup', 'Condiments', 'Ketchup', 'pouch', 25.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRegjmSvbslGvs11LD5z0G2Ne7mSuOjYZ29RNWs9Cq6zA&s=10', 1, 'Standard pouch', '2026-06-19 14:24:46', '2026-06-22 04:25:06'),
(497, 'Ajinomoto', 'Crispy Fry Garlic 238g', 'Condiments', 'Breading Mix', '238g', 70.80, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSkwb5K4D6NtwtSXcLBoN3XsV-XQI8eMWrykUNLpHbPzQ&s=10', 1, 'Party pack', '2026-06-19 14:24:46', '2026-06-22 04:20:10'),
(498, 'Magic Sarap', 'Seasoning 8g Nestlé', 'Condiments', 'Seasoning', '8g', 6.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTw1V3Gyb_o74xnVshyvOx39NHimS2wH75-etewr61Wcg&s=10', 1, 'Per sachet', '2026-06-19 14:24:46', '2026-06-22 04:25:51'),
(499, 'Sugar', 'White Refined 1kg', 'Condiments', 'Sugar', '1kg', 91.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRzWyx19D1wQbHj0rMyUjyc3jDtkLDlAOAUue4VuFvklA&s=10', 1, '₱88-95', '2026-06-19 14:24:46', '2026-06-22 04:33:50'),
(500, 'Sugar', 'Brown 1kg', 'Condiments', 'Sugar', '1kg', 79.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVsC67egI7WJGQ5v_QmExvp3ew1RjgG74xM3-8nAL13g&s=10', 1, '₱76-82', '2026-06-19 14:24:46', '2026-06-22 04:32:50'),
(501, 'Maya', 'All-Purpose Flour 1kg', 'Condiments', 'Flour', '1kg', 71.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQhKUNffjafybiGuzXF1r-BvgbBoHpInA8fh5PuYODBtA&s=10', 1, '₱68-75', '2026-06-19 14:24:46', '2026-06-22 04:26:24'),
(502, 'Nescafé', 'Classic Instant Coffee 100g', 'Beverages', 'Coffee', '100g', 95.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSBmV05rv-pEcxcA8MntpWhnP2-XNWOOw3xUBhBk5zo3HjObhfzIogZt3g&s=10', 1, '₱92-98', '2026-06-19 14:24:46', '2026-06-22 03:51:48'),
(503, 'Nescafé', 'Classic Sticks 48-pack', 'Beverages', 'Coffee', '48pcs', 186.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://img.lazcdn.com/g/p/3bee5cc6b4fb67c519586a72b51c0813.png_960x960q80.png_.webp', 1, 'Standard price', '2026-06-19 14:24:46', '2026-06-22 03:52:52'),
(504, 'Kopiko', 'Blanca 3-in-1 10-pack', 'Beverages', 'Coffee', '10pcs', 82.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTABAxzG8DhH4vMrb6tY61ZAdbJxnxv1QEtOahdZHiZ4g&s=10', 1, '₱78-86', '2026-06-19 14:24:46', '2026-06-22 03:49:41'),
(505, 'Nescafé', 'Original 3-in-1 10-pack', 'Beverages', 'Coffee', '10pcs', 84.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSR9SuLuakvap_H6BVbJbzE0dxmHYXTmrIbL6NTwjSu5qMOAte2l2PJ-wk&s=10', 1, '₱80-88', '2026-06-19 14:24:46', '2026-06-22 03:46:43'),
(506, 'Milo', 'Powdered Chocolate 300g', 'Beverages', 'Chocolate Drink', '300g', 138.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQU1PFYImPDf535FEUb4iHAm37Acr-bTEOPeMFhEYn3VkzaV3lTC0r_9mCv&s=10', 1, '₱134-142', '2026-06-19 14:24:46', '2026-06-22 03:50:27'),
(507, 'Coca-Cola', 'Original 1.5L PET', 'Beverages', 'Soft Drinks', '1.5L', 71.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTZtFk-k_9eoe8_wJ74gA6-tu_k5F9auEDhHnkZDjkYsA&s=10', 1, '₱68-75', '2026-06-19 14:24:46', '2026-06-22 05:55:40'),
(508, 'Sprite', '1.5L PET', 'Beverages', 'Soft Drinks', '1.5L', 71.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://shopmetro.ph/wp-content/uploads/2026/01/SM9083978-1.jpg', 1, '₱68-75', '2026-06-19 14:24:46', '2026-06-22 03:54:18'),
(509, 'Royal Tru-Orange', '1.5L PET', 'Beverages', 'Soft Drinks', '1.5L', 71.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSBd0XoUuv0MWx0B8qnEOgCoYaOZHj3lb-UTFoj59vm4Pgtnt9QtcrtlJI&s=10', 1, '₱68-75', '2026-06-19 14:24:46', '2026-06-22 03:53:45'),
(510, 'Helens Farm', 'Small Eggs 10-pc', 'Protein', 'Eggs', '10pcs', 71.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400', 1, '₱68-75', '2026-06-19 14:24:47', '2026-06-22 03:37:36'),
(511, 'Helens Farm', 'Medium Eggs 10-pc', 'Protein', 'Eggs', '10pcs', 81.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400', 1, '₱78-85', '2026-06-19 14:24:47', '2026-06-22 03:37:36'),
(512, 'Helens Farm', 'Large Eggs 10-pc', 'Protein', 'Eggs', '10pcs', 91.50, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400', 1, '₱88-95', '2026-06-19 14:24:47', '2026-06-22 03:37:36'),
(513, 'Choice Quality', 'Extra Large 12-pc', 'Protein', 'Eggs', '12pcs', 137.00, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400', 1, '₱135-139', '2026-06-19 14:24:47', '2026-06-22 03:37:36'),
(514, 'Choice Quality', 'Quail Eggs 24-pc', 'Protein', 'Eggs', '24pcs', 82.10, 'DTI Supermarket', 'Davao City - NCCC/SM/Gaisano/Robinsons', '2026-06-19', 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?w=400', 1, 'Standard price', '2026-06-19 14:24:47', '2026-06-22 03:37:36'),
(515, 'SM Bonus Pork', 'Liempo Belly', 'Protein', 'Pork', 'kg', 385.00, 'DTI Supermarket', 'Davao City - SM Supermarket', '2026-06-19', 'https://images.unsplash.com/photo-1602470520998-f4a52199a3d6?w=400', 1, '₱375-395', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(516, 'SM Bonus Pork', 'Kasim Lean Chop', 'Protein', 'Pork', 'kg', 316.00, 'DTI Supermarket', 'Davao City - SM Supermarket', '2026-06-19', 'https://images.unsplash.com/photo-1602470520998-f4a52199a3d6?w=400', 1, '₱292-340', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(517, 'SM Bonus', 'Ground Pork Giling', 'Protein', 'Pork', 'kg', 280.50, 'DTI Supermarket', 'Davao City - SM Supermarket', '2026-06-19', 'https://images.unsplash.com/photo-1602470520998-f4a52199a3d6?w=400', 1, '₱266-295', '2026-06-19 14:24:47', '2026-06-22 03:37:36'),
(518, 'SM Bonus Pork', 'Sinigang Cut with Bones', 'Protein', 'Pork', 'kg', 219.50, 'DTI Supermarket', 'Davao City - SM Supermarket', '2026-06-19', 'https://images.unsplash.com/photo-1602470520998-f4a52199a3d6?w=400', 1, '₱207-232', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(519, 'SM Bonus Beef', 'Laman Lean Meat', 'Protein', 'Beef', 'kg', 447.50, 'DTI Supermarket', 'Davao City - SM Supermarket', '2026-06-19', 'https://images.unsplash.com/photo-1588347818036-5a6f1e6b2f49?w=400', 1, '₱430-465', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(520, 'SM Bonus Beef', 'Ground Meat Giling', 'Protein', 'Beef', 'kg', 425.00, 'DTI Supermarket', 'Davao City - SM Supermarket', '2026-06-19', 'https://images.unsplash.com/photo-1588347818036-5a6f1e6b2f49?w=400', 1, '₱410-440', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(521, 'SM Bonus Beef', 'Shank Bulalo Cut', 'Protein', 'Beef', 'kg', 402.50, 'DTI Supermarket', 'Davao City - SM Supermarket', '2026-06-19', 'https://images.unsplash.com/photo-1588347818036-5a6f1e6b2f49?w=400', 1, '₱390-415', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(522, 'SM Bonus', 'Fresh Whole Chicken', 'Protein', 'Poultry', 'kg', 172.50, 'DTI Supermarket', 'Davao City - SM Supermarket', '2026-06-19', 'https://images.unsplash.com/photo-1602470520998-f4a52199a3d6?w=400', 1, '₱165-180', '2026-06-19 14:24:47', '2026-06-22 03:37:36'),
(523, 'Magnolia', 'Chicken Wings', 'Protein', 'Poultry', 'kg', 262.00, 'DTI Supermarket', 'Davao City - SM/NCCC/Gaisano', '2026-06-19', 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=400', 1, 'Packed', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(524, 'Magnolia', 'Chicken Recado Cuts', 'Protein', 'Poultry', 'kg', 215.00, 'DTI Supermarket', 'Davao City - SM/NCCC/Gaisano', '2026-06-19', 'https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=400', 1, 'Packed', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(525, 'SM Bonus', 'Chicken Liver', 'Protein', 'Offal', 'kg', 184.00, 'DTI Supermarket', 'Davao City - SM Supermarket', '2026-06-19', 'https://images.unsplash.com/photo-1602470520998-f4a52199a3d6?w=400', 1, 'Fresh chilled', '2026-06-19 14:24:47', '2026-06-22 03:37:36'),
(526, 'BSM Dagupan', 'Bangus Whole Large', 'Protein', 'Fish', 'kg', 240.00, 'DTI Supermarket', 'Davao City - SM/NCCC/Gaisano', '2026-06-19', 'https://images.unsplash.com/photo-1559347490-6e1bcdd12ad7?w=400', 1, 'Cleaned & scaled', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(527, 'JSL Dagupan', 'Boneless Bangus Belly 400g', 'Protein', 'Fish', '400g', 235.00, 'DTI Supermarket', 'Davao City - SM/NCCC/Gaisano', '2026-06-19', 'https://images.unsplash.com/photo-1559347490-6e1bcdd12ad7?w=400', 1, '2-piece pack', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(528, 'NSL', 'Galunggong Round Scad', 'Protein', 'Fish', 'kg', 367.00, 'DTI Supermarket', 'Davao City - SM/NCCC/Gaisano', '2026-06-19', 'https://images.unsplash.com/photo-1559347490-6e1bcdd12ad7?w=400', 1, 'Fresh chilled', '2026-06-19 14:24:47', '2026-06-22 03:35:47'),
(529, 'Fresh Tilapia', 'Large', 'Protein', 'Fish', 'kg', 165.00, 'DTI Supermarket', 'Davao City - SM/NCCC/Gaisano', '2026-06-19', 'https://images.unsplash.com/photo-1559347490-6e1bcdd12ad7?w=400', 1, '₱155-175', '2026-06-19 14:24:47', '2026-06-22 03:35:47');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_logs`
--

INSERT INTO `system_logs` (`log_id`, `user_id`, `action_type`, `description`, `ip_address`, `created_at`) VALUES
(610, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-01 09:42:05'),
(611, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-01 09:44:12'),
(612, 42, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-01 14:10:16'),
(613, 41, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-01 14:10:35'),
(614, 43, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-01 14:10:50'),
(615, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-01 14:11:20'),
(616, 1, 'BNS_RESIDENT_CREATED', 'BNS registered resident: ongayonancy24@gmail.com (user_id=46)', '::1', '2026-06-01 14:58:07'),
(617, 46, 'ACCOUNT_SETUP_COMPLETE', 'Resident completed account setup via invite link', '::1', '2026-06-01 14:59:03'),
(618, NULL, 'REGISTER', 'New registration: nancelee07@gmail.com', '::1', '2026-06-01 16:04:32'),
(619, NULL, 'OTP_SENT', 'OTP sent to nancelee07@gmail.com (first login)', '::1', '2026-06-01 16:04:50'),
(620, NULL, 'LOGIN_SUCCESS', 'User logged in successfully', '::1', '2026-06-01 16:05:34'),
(621, NULL, 'ROLE_SET', 'Role set to: Mother', '::1', '2026-06-01 16:06:08'),
(623, NULL, 'REGISTER', 'New registration: nancelee07@gmail.com', '::1', '2026-06-01 16:35:09'),
(624, NULL, 'OTP_SENT', 'OTP sent to nancelee07@gmail.com (first login)', '::1', '2026-06-01 16:35:21'),
(625, NULL, 'LOGIN_SUCCESS', 'User logged in successfully', '::1', '2026-06-01 16:35:43'),
(626, NULL, 'ROLE_SET', 'Role set to: Mother', '::1', '2026-06-01 16:36:10'),
(627, NULL, 'REGISTER', 'New registration: nancelee07@gmail.com', '::1', '2026-06-01 16:52:28'),
(628, NULL, 'OTP_SENT', 'OTP sent to nancelee07@gmail.com (first login)', '::1', '2026-06-01 16:52:48'),
(629, NULL, 'LOGIN_SUCCESS', 'User logged in successfully', '::1', '2026-06-01 16:53:10'),
(630, NULL, 'ROLE_SET', 'Role set to: Mother', '::1', '2026-06-01 16:53:38'),
(631, NULL, 'REGISTER', 'New registration: nancelee07@gmail.com', '::1', '2026-06-01 17:14:20'),
(632, NULL, 'OTP_SENT', 'OTP sent to nancelee07@gmail.com (first login)', '::1', '2026-06-01 17:14:36'),
(633, NULL, 'LOGIN_SUCCESS', 'User logged in successfully', '::1', '2026-06-01 17:14:57'),
(634, NULL, 'ROLE_SET', 'Role set to: Mother', '::1', '2026-06-01 17:15:20'),
(635, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-02 03:41:30'),
(636, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-02 03:41:54'),
(637, 46, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — BNS-registered resident)', '::1', '2026-06-02 03:42:45'),
(638, NULL, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-02 03:43:06'),
(639, NULL, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-02 03:43:07'),
(640, NULL, 'REGISTER', 'New registration: nancelee07@gmail.com', '::1', '2026-06-02 03:51:07'),
(641, 51, 'OTP_SENT', 'OTP sent to nancelee07@gmail.com (first login)', '::1', '2026-06-02 03:51:31'),
(642, 51, 'LOGIN_SUCCESS', 'User logged in successfully', '::1', '2026-06-02 03:51:49'),
(643, 51, 'ROLE_SET', 'Role set to: Mother', '::1', '2026-06-02 03:52:13'),
(644, 41, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-02 04:01:13'),
(645, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-02 04:48:41'),
(646, 46, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — BNS-registered resident)', '::1', '2026-06-02 04:49:37'),
(647, 43, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-02 05:16:25'),
(648, 42, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-02 05:41:13'),
(649, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-02 11:16:10'),
(650, 43, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-02 11:18:35'),
(651, 1, 'BNS_RESIDENT_CREATED', 'BNS registered resident: ongayonancy24@gmail.com (user_id=52)', '::1', '2026-06-02 12:07:24'),
(652, 46, 'LOGOUT', 'User logged out', '::1', '2026-06-02 12:07:31'),
(653, 52, 'ACCOUNT_SETUP_COMPLETE', 'Resident completed account setup via invite link', '::1', '2026-06-02 12:08:01'),
(654, 51, 'LOGOUT', 'User logged out', '::1', '2026-06-02 12:14:32'),
(655, NULL, 'REGISTER', 'New registration: nancelee07@gmail.com', '::1', '2026-06-02 12:16:16'),
(656, NULL, 'OTP_SENT', 'OTP sent to nancelee07@gmail.com (first login)', '::1', '2026-06-02 12:16:41'),
(657, NULL, 'LOGIN_SUCCESS', 'User logged in successfully', '::1', '2026-06-02 12:16:59'),
(658, NULL, 'ROLE_SET', 'Role set to: Mother', '::1', '2026-06-02 12:17:28'),
(660, NULL, 'REGISTER', 'New registration: nancelee07@gmail.com', '::1', '2026-06-02 12:40:58'),
(661, 54, 'OTP_SENT', 'OTP sent to nancelee07@gmail.com (first login)', '::1', '2026-06-02 12:41:18'),
(662, 54, 'LOGIN_SUCCESS', 'User logged in successfully', '::1', '2026-06-02 12:41:44'),
(663, 54, 'ROLE_SET', 'Role set to: Mother', '::1', '2026-06-02 12:42:09'),
(664, 42, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-02 13:45:32'),
(665, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-03 08:20:33'),
(666, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-03 08:21:01'),
(667, 52, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — BNS-registered resident)', '::1', '2026-06-03 08:31:01'),
(668, 43, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-03 08:31:58'),
(669, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-03 08:32:33'),
(670, 42, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-03 08:34:41'),
(671, 54, 'LOGIN_FAILED', 'Failed login attempt from ::1', '::1', '2026-06-03 08:35:30'),
(672, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-03 08:35:49'),
(673, 41, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-03 08:36:53'),
(674, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-08 15:44:58'),
(675, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-08 15:45:25'),
(676, 52, 'LOGIN_FAILED', 'Failed login attempt from 10.209.173.215', '10.209.173.215', '2026-06-08 16:15:21'),
(677, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '10.209.173.215', '2026-06-08 16:16:20'),
(678, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-08 16:46:49'),
(679, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '10.209.173.215', '2026-06-08 17:54:50'),
(680, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-09 04:20:49'),
(681, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-09 04:21:07'),
(682, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '10.209.173.215', '2026-06-09 04:29:39'),
(683, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '10.209.173.215', '2026-06-09 04:29:41'),
(684, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-09 05:06:24'),
(685, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-09 05:51:14'),
(686, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-09 11:08:04'),
(687, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-09 11:09:05'),
(688, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-09 11:09:23'),
(689, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-10 09:12:54'),
(690, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-10 09:13:24'),
(691, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-10 09:15:46'),
(692, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-10 14:21:50'),
(693, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-15 09:18:14'),
(694, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-15 09:18:55'),
(695, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-15 09:37:53'),
(696, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-16 04:17:45'),
(697, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-16 04:18:07'),
(698, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-16 07:01:30'),
(699, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-16 07:01:46'),
(700, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-16 07:14:24'),
(701, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-16 07:21:30'),
(702, 42, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-16 07:24:08'),
(703, 41, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-16 07:26:46'),
(704, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-16 09:23:47'),
(705, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-16 09:29:24'),
(706, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-16 09:29:43'),
(707, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-16 09:37:09'),
(708, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-16 09:37:28'),
(709, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-16 10:20:39'),
(710, 54, 'LOGOUT', 'User logged out', '192.168.0.157', '2026-06-16 10:33:10'),
(711, 52, 'LOGIN_FAILED', 'Failed login attempt from 10.228.59.215', '10.228.59.215', '2026-06-16 14:49:42'),
(712, 52, 'LOGIN_FAILED', 'Failed login attempt from 10.228.59.215', '10.228.59.215', '2026-06-16 14:50:41'),
(713, 52, 'LOGIN_LOCKED', 'Locked account login attempt from 10.228.59.215', '10.228.59.215', '2026-06-16 14:51:35'),
(714, 52, 'LOGIN_LOCKED', 'Locked account login attempt from 10.228.59.215', '10.228.59.215', '2026-06-16 14:59:25'),
(715, 2, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-16 15:04:43'),
(716, 2, 'ADMIN_UNLOCK_USER', 'unlock user_id=52', '::1', '2026-06-16 15:05:09'),
(717, 52, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — BNS-registered resident)', '10.228.59.215', '2026-06-16 15:05:41'),
(718, 52, 'LOGOUT', 'User logged out', '10.228.59.215', '2026-06-16 15:11:30'),
(719, 46, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — BNS-registered resident)', '10.228.59.215', '2026-06-16 15:11:51'),
(720, 46, 'LOGOUT', 'User logged out', '10.228.59.215', '2026-06-16 15:47:09'),
(721, 46, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — BNS-registered resident)', '10.228.59.215', '2026-06-16 16:17:24'),
(722, 41, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-16 16:43:50'),
(723, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-17 08:59:01'),
(724, 1, 'OTP_FAILED', 'Invalid OTP entered', '::1', '2026-06-17 09:01:57'),
(725, 1, 'OTP_RESENT', 'OTP resent to nancyongayo24@gmail.com', '::1', '2026-06-17 09:02:10'),
(726, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-17 09:02:28'),
(727, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-17 09:05:33'),
(728, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-17 09:33:10'),
(729, 41, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-17 09:34:08'),
(730, 42, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-17 09:37:37'),
(731, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-17 09:42:01'),
(732, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-17 09:58:04'),
(733, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-19 06:46:29'),
(734, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-19 07:32:32'),
(735, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-19 07:37:09'),
(736, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-19 07:37:27'),
(737, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-19 10:42:52'),
(738, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-19 12:50:52'),
(739, 2, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-19 13:00:12'),
(740, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-21 07:04:01'),
(741, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-21 08:08:13'),
(742, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-21 09:04:56'),
(743, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-21 09:05:20'),
(744, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-21 09:23:17'),
(745, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-21 10:32:52'),
(746, 46, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — BNS-registered resident)', '::1', '2026-06-21 13:28:07'),
(747, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-22 02:05:57'),
(748, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-22 05:23:38'),
(749, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-22 05:23:52'),
(750, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-22 07:49:06'),
(751, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-22 08:28:01'),
(752, 44, 'LOGOUT', 'User logged out', '::1', '2026-06-22 08:50:02'),
(753, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-22 08:50:20'),
(754, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-22 09:33:59'),
(755, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-22 09:35:28'),
(756, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-22 09:35:48'),
(757, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-22 09:39:01'),
(758, 54, 'LOGOUT', 'User logged out', '192.168.0.157', '2026-06-22 09:50:34'),
(759, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-22 09:51:40'),
(760, 44, 'LOGOUT', 'User logged out', '192.168.0.157', '2026-06-22 09:55:59'),
(761, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-22 09:56:23'),
(762, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-23 02:37:52'),
(763, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-23 02:39:46'),
(764, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-23 02:40:00'),
(765, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-23 02:43:38'),
(766, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-23 03:25:02'),
(767, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-24 07:20:03'),
(768, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-24 07:20:27'),
(769, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-24 07:21:08'),
(770, 44, 'LOGIN_FAILED', 'Failed login attempt from ::1', '::1', '2026-06-24 07:23:32'),
(771, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-24 07:23:49'),
(772, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-24 07:24:30'),
(773, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-24 08:32:12'),
(774, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '192.168.0.157', '2026-06-24 09:05:05'),
(775, 30, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-24 09:46:26'),
(776, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-24 13:47:43'),
(777, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-24 13:48:59'),
(778, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-24 13:49:57'),
(779, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-24 14:48:02'),
(780, 1, 'GOOGLE_OTP_SENT', 'OTP sent after Google OAuth login', '::1', '2026-06-25 03:21:15'),
(781, 1, 'GOOGLE_LOGIN', 'User logged in successfully', '::1', '2026-06-25 03:21:38'),
(782, 44, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-25 03:23:12'),
(783, 54, 'LOGIN_SUCCESS', 'User logged in (OTP skipped — not first login)', '::1', '2026-06-25 03:26:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated','Annulled') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL DEFAULT '',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `force_password_change` tinyint(1) NOT NULL DEFAULT 0,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `middle_name`, `last_name`, `gender`, `civil_status`, `birthdate`, `contact`, `email`, `password_hash`, `is_verified`, `force_password_change`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'Nancy', NULL, 'Ongayo', NULL, NULL, NULL, NULL, 'nancyongayo24@gmail.com', '', 1, 0, 3, '2026-04-11 15:11:40', '2026-04-11 15:12:59'),
(2, 'Admin', NULL, 'Ongayo', NULL, NULL, NULL, NULL, 'nancyongayo1907@gmail.com', '$2y$12$2tPKTtD0F4wG5vLunNhuNeQ0H6AYkJuhBIKxy4TVZJm7Wi7q1/cx.', 1, 0, 1, '2026-04-11 15:40:55', '2026-04-11 15:45:50'),
(30, 'Eric', 'Chu', 'Flores', 'Male', 'Married', '1998-02-24', '09269749522', 'insaynanz12@gmail.com', '$2y$12$6DuMCioHbzBMhsl7IwuK2esU9VUtvGa4GgkKRf4Ne9TMtip85xuW6', 1, 0, 2, '2026-05-02 15:56:02', '2026-05-04 12:58:51'),
(41, 'Alma', 'Sedano', 'Cominador', NULL, NULL, NULL, NULL, 'almaongayo@gmail.com', '$2y$12$LM7LoVk02h82suXGWRSvLubpE8tNcjNPxuXsoTWxA8BxOcwuPWcEW', 1, 0, 5, '2026-05-17 05:56:24', '2026-05-17 06:27:30'),
(42, 'Pedro', NULL, 'Cruz', NULL, NULL, NULL, NULL, 'natoyongs@gmail.com', '$2y$12$ZwvCeMet.LJWSt9GuatLReQx1.sIWRP7A0k/2Yo4zZVO5hTmZtyl.', 1, 0, 7, '2026-05-17 06:57:20', '2026-05-17 07:00:21'),
(43, 'Myrna', NULL, 'Perez', NULL, NULL, NULL, NULL, 'cycynana26@gmail.com', '$2y$12$WZ2VqYa7/ehzIISSvC6vJueOYEPjeklAp57JN4Ks00g3Oc0.dv3O2', 1, 0, 6, '2026-05-17 07:41:52', '2026-05-17 07:43:49'),
(44, 'Allan', 'Cruz', 'Rodel', NULL, NULL, NULL, NULL, 'scarletrhias26@gmail.com', '$2y$12$Z9znazB7hGmCQUgnJfVNd.By6BtJrWOeq1rhiD/7J.CKlPlC6qVe2', 1, 0, 8, '2026-05-25 17:45:20', '2026-05-25 18:27:06'),
(46, 'Alex', '', 'Dove', 'Male', 'Married', '1996-06-25', '09269749522', 'ongayonancy24@gmail.com', '$2y$12$nRsw4rJ/zisBbXOqNeNLKuxzBI9X90YFk.OswvmxYdKp6JiHrgFKq', 1, 0, 4, '2026-06-01 14:58:07', '2026-06-16 15:10:55'),
(51, 'Maria', 'Evan', 'Brown', 'Female', 'Married', '1996-12-15', '09269749522', 'nancelee0@gmail.com', '$2y$12$Bhj4eQoSNNzbnXVnVyeSNuekuY/QpTAyvwRoxwYBgOzaHumIIxSY6', 1, 0, 4, '2026-06-02 03:51:02', '2026-06-02 12:14:10'),
(52, 'Alfred', 'Santos', 'Tiago', 'Male', 'Married', '1990-09-18', '09269749522', 'ongayonancy@gmail.com', '$2y$12$BttH.F7YY2coxKYClgHweObjWs87nnfkFAUfnVJKiymy/WZRLD.Qy', 1, 0, 4, '2026-06-02 12:07:24', '2026-06-16 15:10:45'),
(54, 'Erza', 'Ong', 'Rhias', 'Female', 'Married', '1992-09-25', '09269749522', 'nancelee07@gmail.com', '$2y$12$yTpED25IHv4z7/ajCbMZMOXZcGJDuvlk3VaQLCrqkJU5sNvkPQMLW', 1, 0, 4, '2026-06-02 12:40:52', '2026-06-02 13:13:57');

-- --------------------------------------------------------

--
-- Table structure for table `user_auth`
--

CREATE TABLE `user_auth` (
  `auth_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `verification_token` varchar(100) DEFAULT NULL,
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `skip_otp` tinyint(1) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `first_login_completed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_auth`
--

INSERT INTO `user_auth` (`auth_id`, `user_id`, `google_id`, `verification_token`, `failed_attempts`, `skip_otp`, `locked_until`, `updated_at`, `first_login_completed`) VALUES
(1, 1, '116645020060272986778', NULL, 1, 0, NULL, '2026-05-17 04:33:47', 1),
(4, 2, '110413409233549364612', 'e5c55afea4d42e2fe1ec437330a4ab49538311eeabc063f05ddc800a051133a7', 0, 0, NULL, '2026-05-17 04:33:47', 1),
(117, 30, NULL, '29814f45b0c43dd348c1afcdd87a688eba9ffe62aa508a3a9bc8a98a09968e80', 0, 0, NULL, '2026-05-17 05:48:18', 1),
(196, 41, NULL, '219b792c6587ed75abbaeaf99b800f8e873b61cfc00b070dc77d887c8cde42ca', 0, 0, NULL, '2026-05-17 06:12:26', 1),
(198, 42, NULL, '378f4e7d28defb467a9845704932fc9f4c6420b051495576d768fd4f2e568c5d', 0, 0, NULL, '2026-05-17 06:59:42', 1),
(200, 43, NULL, 'd3d9edddfd2dbe3676d38ff118f764ef46f4fbca9fd1401fca9a071e1d606b87', 0, 0, NULL, '2026-05-17 07:43:20', 1),
(206, 44, NULL, 'c6fd5c7a9e359bb7e0a42b61724a53d00dcd3d971dcb80753ff8b9020cca759b', 0, 0, NULL, '2026-06-24 07:23:49', 1),
(225, 46, NULL, NULL, 0, 1, NULL, '2026-06-01 14:59:03', 0),
(237, 51, NULL, 'f42751dcb4d40fbced24470aa023ed6fa689890f1f8610165e238d8e25897fb0', 0, 0, NULL, '2026-06-02 03:51:49', 1),
(239, 52, NULL, NULL, 0, 1, NULL, '2026-06-16 15:05:09', 0),
(243, 54, NULL, '0c8feec0fb8b22e56faeccaae8ccd3a1adb87533a0178307f2834859ac361905', 0, 0, NULL, '2026-06-03 08:35:49', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_health_profiles`
--

CREATE TABLE `user_health_profiles` (
  `health_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pregnancy_status` enum('Not Pregnant','Pregnant 1st Trimester','Pregnant 2nd Trimester','Pregnant 3rd Trimester','Postpartum') DEFAULT NULL,
  `breastfeeding_status` enum('Not Breastfeeding','Exclusively Breastfeeding','Mixed Feeding','Bottle Feeding') DEFAULT NULL,
  `monthly_income` decimal(10,2) DEFAULT NULL,
  `occupation` varchar(150) DEFAULT NULL,
  `educ_level_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_health_profiles`
--

INSERT INTO `user_health_profiles` (`health_id`, `user_id`, `pregnancy_status`, `breastfeeding_status`, `monthly_income`, `occupation`, `educ_level_id`, `updated_at`) VALUES
(73, 46, NULL, NULL, 30000.00, 'Teacher', 5, '2026-06-01 15:08:40'),
(74, 47, 'Pregnant 3rd Trimester', 'Not Breastfeeding', 5000.00, 'Housewife', 5, '2026-06-01 16:17:02'),
(76, 48, 'Pregnant 3rd Trimester', 'Not Breastfeeding', 5000.00, 'Housewife', 5, '2026-06-01 16:42:11'),
(78, 49, 'Pregnant 3rd Trimester', 'Not Breastfeeding', 5000.00, 'Housewife', 5, '2026-06-01 16:58:57'),
(80, 50, 'Pregnant 3rd Trimester', 'Not Breastfeeding', 5000.00, 'Housewife', 5, '2026-06-01 17:19:12'),
(82, 51, 'Pregnant 3rd Trimester', 'Not Breastfeeding', 5000.00, 'Housewife', 5, '2026-06-02 03:55:35'),
(85, 52, NULL, NULL, 3000.00, 'Driver', 4, '2026-06-02 12:13:16'),
(86, 53, 'Not Pregnant', '', 5000.00, 'Housewife', 4, '2026-06-02 12:22:25'),
(88, 54, 'Not Pregnant', '', 5000.00, 'Housewife', 4, '2026-06-02 12:44:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_otp`
--

CREATE TABLE `user_otp` (
  `otp_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `otp_expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_otp`
--

INSERT INTO `user_otp` (`otp_id`, `user_id`, `otp_code`, `otp_expiry`, `created_at`) VALUES
(187, 1, '225724', '2026-06-01 17:45:00', '2026-06-01 09:42:00'),
(192, 1, '230691', '2026-06-02 11:44:25', '2026-06-02 03:41:25'),
(193, 51, '308016', '2026-06-02 11:54:31', '2026-06-02 03:51:31'),
(195, 54, '058834', '2026-06-02 20:44:18', '2026-06-02 12:41:18'),
(196, 1, '827386', '2026-06-03 16:23:27', '2026-06-03 08:20:27'),
(197, 1, '689501', '2026-06-08 23:47:53', '2026-06-08 15:44:53'),
(198, 1, '200498', '2026-06-09 12:23:41', '2026-06-09 04:20:41'),
(199, 1, '521199', '2026-06-09 19:11:58', '2026-06-09 11:08:58'),
(200, 1, '161236', '2026-06-10 17:15:49', '2026-06-10 09:12:49'),
(201, 1, '878757', '2026-06-15 17:21:10', '2026-06-15 09:18:10'),
(202, 1, '620568', '2026-06-16 12:20:38', '2026-06-16 04:17:38'),
(203, 1, '258770', '2026-06-16 15:04:26', '2026-06-16 07:01:26'),
(204, 1, '424493', '2026-06-16 17:32:19', '2026-06-16 09:29:19'),
(205, 1, '311015', '2026-06-16 17:40:05', '2026-06-16 09:37:05'),
(206, 1, '472380', '2026-06-17 17:01:56', '2026-06-17 08:58:56'),
(207, 1, '762884', '2026-06-17 17:05:10', '2026-06-17 09:02:10'),
(208, 1, '098558', '2026-06-19 15:40:04', '2026-06-19 07:37:04'),
(209, 1, '137185', '2026-06-21 17:07:50', '2026-06-21 09:04:50'),
(210, 1, '860692', '2026-06-22 13:26:33', '2026-06-22 05:23:33'),
(211, 1, '598026', '2026-06-22 17:38:24', '2026-06-22 09:35:24'),
(212, 1, '935741', '2026-06-23 10:42:40', '2026-06-23 02:39:40'),
(213, 1, '998142', '2026-06-24 15:22:58', '2026-06-24 07:19:58'),
(214, 1, '428645', '2026-06-24 21:50:37', '2026-06-24 13:47:37'),
(215, 1, '865574', '2026-06-25 11:24:10', '2026-06-25 03:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `user_password_resets`
--

CREATE TABLE `user_password_resets` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reset_token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_encrypted` text DEFAULT NULL,
  `barangay_code` varchar(20) DEFAULT NULL,
  `profile_complete` tinyint(1) NOT NULL DEFAULT 0,
  `profile_status` enum('Draft','Submitted','Validated') NOT NULL DEFAULT 'Draft',
  `submitted_at` datetime DEFAULT NULL,
  `validated_by` int(11) DEFAULT NULL,
  `validated_at` datetime DEFAULT NULL,
  `assigned_bns_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `return_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`profile_id`, `user_id`, `address_encrypted`, `barangay_code`, `profile_complete`, `profile_status`, `submitted_at`, `validated_by`, `validated_at`, `assigned_bns_id`, `updated_at`, `return_reason`) VALUES
(1, 1, 'yXgz+hq/Vos4PxrlKNGdGDo6V0hkUnA5bmhsUXBBUDBldVdNV1U0OHhBN0IwR1RJbjc1eEpVRGxkNnFlbVNHbDAxR24rcWkrWnRuTjVJRDFSdFdyNHBVT0JycEx6bFE0bmJEKzJDZDFTcEp1bjQvNG9wTVNXd0ZtZlZyanM9', '112402015', 1, 'Draft', NULL, NULL, NULL, NULL, '2026-05-17 04:28:00', NULL),
(2, 2, 'Null', 'Null', 1, 'Draft', NULL, NULL, NULL, NULL, '2026-04-11 15:48:50', NULL),
(100, 30, 'wAjuh+nMmAlxbk8cQFxSejo6REN5SFNpaVZzRm0rQlFFNWVxS0ZnN0ZnellFQWxZdXF3S0J3a2hkNGxNUnA4S25DV3BNUXdRaXpxSzBoOUh1enlqdFl4TnAxTUZjQjVnVnJpZzlETmc9PQ==', '083720018', 1, 'Draft', NULL, NULL, NULL, NULL, '2026-05-04 10:32:11', NULL),
(123, 41, 'w1QbYpzbMbrUWMR5hI1RqTo6V1B6Mnl6RmdOL1JlMFk5WTNYakNuSXJhNElPaHFTYlNOZEg4Y3AxY0VZemk2cDZyK0k5d3JFTTJsaE83dHZocC9RZmdZWW9wV21QOHpYeG5KL25CNEE9PQ==', '112402015', 1, 'Draft', NULL, NULL, NULL, NULL, '2026-05-17 06:27:30', NULL),
(124, 42, 'rSt1Z02+LM2fO8Cb7VL08To6Vk96N3VmWUJEcUN0ZUh6YWFqcjc3TkR6RUdZU1ROMjBhN1Q4cWx5S1FDb05vTWhKblJpUFBKUWdXN2ZPQU5uNDBWdWJqNWZpY3JWVm45T3RIMjVrMHc9PQ==', '112402015', 1, 'Draft', NULL, NULL, NULL, NULL, '2026-05-17 07:00:21', NULL),
(125, 43, 'hSzdui8Zyv3N/zDA8SArrTo6S0lucWU5RUhaTlg1aVF5N25iN1FXcTBQbVhBMk9VVit6OEdmU3RUNnU1ZjBFaTZKckVIRkVwYkZHSi9CT1ZKTWhYRVoweDVweXlxTG5KYk1ieFJkbEE9PQ==', '112402015', 1, 'Draft', NULL, NULL, NULL, NULL, '2026-05-17 07:43:49', NULL),
(126, 44, 'l/8x4mb45iTA0BwrFunFtjo6U1RORkRSaENsODYzWEg5cm5WMmZ2V0pHN3FhT2FtZlpwWVRWd0lQRnpPaUsxVU9CSWZQM1lBbGh0RitNN3NpamhneGx4ZkQ1TkFDWThGTHF3ZkUvT3c9PQ==', '112402040', 1, 'Draft', NULL, NULL, NULL, NULL, '2026-05-25 18:27:06', NULL),
(129, 46, NULL, NULL, 1, 'Validated', '2026-06-01 23:08:40', 1, '2026-06-01 23:08:40', 1, '2026-06-01 15:08:40', NULL),
(144, 51, 'cUwe/QjmvnQnpS1aRsPGvzo6REwwSFBQSTFPS05JVUN4S3QvVzAzYmNNZzUzR1VMeGV5ejhqN3Y1dEJxeHBiUXRLVDAwRm1DUE5Ed0xLNkJUMWNEd09tMjZYcjhna285bnBEWHJjdGc9PQ==', '112402015', 1, 'Validated', '2026-06-02 11:57:13', 1, '2026-06-02 11:57:13', 1, '2026-06-02 03:57:13', 'salary'),
(148, 52, NULL, NULL, 1, 'Validated', '2026-06-02 20:13:16', 1, '2026-06-02 20:13:16', 1, '2026-06-02 12:13:16', NULL),
(154, 54, 'NJMPYff5xUUpi+jCR3rcGzo6eEkxdzlUSmNhYmkzaTdadWZXYWFCYzJPRUNNdUFEYTV0dVExWXN6WDVWUzBEcFlTWHYzUE84VDJQeTdBUThHcHJTTlRwZDJaampIQUhpbGFwS1daQkE9PQ==', '112402015', 1, 'Validated', '2026-06-02 21:13:57', 1, '2026-06-02 21:14:16', 1, '2026-06-02 13:14:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_trusted_devices`
--

CREATE TABLE `user_trusted_devices` (
  `device_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `device_token` varchar(255) NOT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_trusted_devices`
--

INSERT INTO `user_trusted_devices` (`device_id`, `user_id`, `device_token`, `user_agent`, `ip_address`, `expires_at`, `created_at`) VALUES
(20, 1, '92db0d2c1d53042e559a34f85aebce94a088d3f4278b89767dc980d9cf9df5da', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '::1', '2026-05-19 23:03:28', '2026-05-12 15:03:28'),
(22, 1, 'aa1533eee449e7317d1b58ae2941efdd51dd7e6cb28e1a6525401385dcacff13', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '::1', '2026-05-19 23:54:45', '2026-05-12 15:54:45'),
(25, 1, 'a8d76d6650a16ce1cbd9b1ce75ce6bcb1e107e5eab45b10c677a194c6a12a69c', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '::1', '2026-05-20 00:35:40', '2026-05-12 16:35:40'),
(26, 1, '7fe47ba918e6cf91afdf9e75e42506f2521dec94caec556c0c5f3bafcaac00ac', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '::1', '2026-05-24 12:15:43', '2026-05-17 04:15:43');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_products`
--

CREATE TABLE `vendor_products` (
  `product_id` int(11) NOT NULL,
  `vendor_user_id` int(11) NOT NULL COMMENT 'Market Vendor who owns this product',
  `product_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL COMMENT 'e.g., Vegetables, Fruits, Meat, Fish, Grains, Dairy',
  `description` text DEFAULT NULL,
  `unit` varchar(50) NOT NULL COMMENT 'e.g., kg, pcs, liters, bundles',
  `price_per_unit` decimal(10,2) NOT NULL,
  `stock_quantity` decimal(10,2) DEFAULT 0.00 COMMENT 'Available stock',
  `nutritional_info` text DEFAULT NULL COMMENT 'Optional: calories, vitamins, etc.',
  `product_image` varchar(255) DEFAULT NULL COMMENT 'Path to product image',
  `is_available` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0 COMMENT 'Featured/recommended products',
  `created_date` datetime DEFAULT current_timestamp(),
  `updated_date` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendor_products`
--

INSERT INTO `vendor_products` (`product_id`, `vendor_user_id`, `product_name`, `category`, `description`, `unit`, `price_per_unit`, `stock_quantity`, `nutritional_info`, `product_image`, `is_available`, `is_featured`, `created_date`, `updated_date`) VALUES
(1, 44, 'Ampalaya', 'Vegetables', '', 'kg', 52.00, 19.00, NULL, 'uploads/products/product_1782036235_4499.webp', 1, 0, '2026-06-21 18:03:55', '2026-06-24 16:15:47'),
(2, 44, 'Ampalaya (Galaxy)', 'Vegetables', NULL, 'kg', 85.00, 50.00, NULL, 'uploads/products/product_1782036235_4499.webp', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:51:20'),
(3, 44, 'Batong (negrostar)', 'Vegetables', NULL, 'kg', 75.00, 40.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTyVATjCiDtgPV0lsWQo6JWJwG0cmiDoSNHZAdBz92cKA&s=10', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:52:02'),
(4, 44, 'Kalabasa (suprema)', 'Vegetables', NULL, 'kg', 35.00, 60.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQDqniH5ghtzEHqEcoktgPkatQSB1WcibowcwXwOGG66Q&s=10', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:52:31'),
(5, 44, 'Kamatis (diamante Big)', 'Vegetables', NULL, 'kg', 60.00, 100.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSwjaqMUPEWGeKl4kICksUTSPnn3AsJAHzPlHgCZ-Zqgg&s=10', 1, 1, '2026-06-21 19:12:50', '2026-06-22 15:52:59'),
(6, 44, 'Native Pechay (condor)', 'Vegetables', NULL, 'kg', 130.00, 30.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTROQLYNnjs8sIKSn5OEItKNl3ciYfqmHUXWsVyORGbrw&s=10', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:53:25'),
(7, 44, 'Okra (smooth green)', 'Vegetables', NULL, 'kg', 90.00, 45.00, NULL, 'https://www.cookedandloved.com/wp-content/uploads/2020/02/what-is-okra-s.jpg', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:53:50'),
(8, 44, 'Patola (ordinary)', 'Vegetables', NULL, 'kg', 75.00, 35.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRLKI40U89xH_kYGaGd0DcryEhzK_RtgtXm14VetMBdQ&s=10', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:54:28'),
(9, 44, 'Pipino (mega c)', 'Vegetables', NULL, 'kg', 40.00, 50.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS550dEpNFhjorI3L7zPeMnbwtjkCm28MrCtWvm6sLOqw&s=10', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:54:54'),
(10, 44, 'Radish (ordinary)', 'Vegetables', NULL, 'kg', 75.00, 40.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRkAUVjFwLdXgm39TVqfxB3J1GjQiS7JgvdKr-sEzQrIw&s=10', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:56:15'),
(11, 44, 'Talong (banate king)', 'Vegetables', NULL, 'kg', 50.00, 70.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcShdik2IQOH1EFPIfwOhY7iQN-FTjxeRptgKBlcX_rkgQ&s=10', 1, 1, '2026-06-21 19:12:50', '2026-06-22 15:56:55'),
(12, 44, 'Upo (mayumi)', 'Vegetables', NULL, 'kg', 40.00, 30.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS26pySwcWitUxWqcLHDkZit4arPQa9QGwqTJnxuMjCjA&s', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:57:24'),
(13, 44, 'Alugbati', 'Vegetables', NULL, 'kg', 55.00, 25.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSE2RJa6cjNUOdv5X4VBjLU11BCB3lPMlAFTc71HGasNA&s=10', 1, 0, '2026-06-21 19:12:50', '2026-06-22 15:58:00'),
(14, 44, 'Baguio beans (pencil)', 'Vegetables', NULL, 'kg', 70.00, 40.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRLB2LFcmKZx1hZGSJYypR0A91SMEMi5voVsz8Y1xHKgw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 15:58:28'),
(15, 44, 'Carrots (big)', 'Vegetables', NULL, 'kg', 90.00, 50.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQyVc1tJJEJldmcOIA-yGdOXQwdrJZFrqUUxGQetbIFfQ&s=10', 1, 1, '2026-06-21 19:12:51', '2026-06-22 15:59:04'),
(16, 44, 'Carrots (medium)', 'Vegetables', NULL, 'kg', 80.00, 45.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSVIWWpSCxZSfa3brrK7CJPDyzrCcpTpCD8MYRWsYjIMA&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 15:59:35'),
(17, 44, 'Carrots (small)', 'Vegetables', NULL, 'kg', 70.00, 40.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS-HD5cunCD7jIt126hc9-MC7A0Aj26h2ucsB0HbmoaPw&s', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:03:57'),
(18, 44, 'Chinese Pechay', 'Vegetables', NULL, 'kg', 75.00, 35.00, NULL, 'http://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSFGekW8yIjfx8y6xvEK_PgydSzp85-T8seB56fB5NlWw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:04:45'),
(19, 44, 'Patatas (big)', 'Vegetables', NULL, 'kg', 110.00, 80.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTYEP4bK0Aoh-KbqORIsjA1F4MXECV8Ekhyu8nZDnH66A&s', 1, 1, '2026-06-21 19:12:51', '2026-06-22 16:06:05'),
(20, 44, 'Patatas (medium)', 'Vegetables', NULL, 'kg', 90.00, 70.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTcABwyrrO1Im1hW0gpnUl8nDGlt7GYcEvvTb_i0KH_QQ&s', 1, 1, '2026-06-21 19:12:51', '2026-06-22 16:06:45'),
(21, 44, 'Patatas (small)', 'Vegetables', NULL, 'kg', 75.00, 60.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRYySPIGqO5Ng5tmvxb0Vx9mZWrFMSy4VHQPRCmMBpRog&s', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:07:47'),
(22, 44, 'Repolyo (wakamini)', 'Vegetables', NULL, 'kg', 65.00, 49.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSBiEOr8tZOgxW9U7SSMjO_cYH80G9ltsMppdDayyAp_w&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-25 00:45:55'),
(23, 44, 'Sayote (big)', 'Vegetables', NULL, 'sack', 20.00, 30.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRpdYUY-TvXaBesoCJMTm_Dl-oDUoBm1ryE-H9FRLQnWg&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:08:58'),
(24, 44, 'Sayote (small)', 'Vegetables', NULL, 'pc', 15.00, 100.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_JVFf83iQZyer0vm9tWLwZtMT16VV93TGvmHaJ3vZlw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:10:10'),
(25, 44, 'Broccoli', 'Vegetables', NULL, 'kg', 230.00, 15.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_tBZzdNVlUXjNtdKmQqJBiKqe0_eiHr4XzTAO_KrfHw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:10:38'),
(26, 44, 'Cauliflower', 'Vegetables', NULL, 'kg', 180.00, 20.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR8Pe7HWyS91KejxiMp9wRef4k15lsQNxXU5PZuBngAEQ&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:11:30'),
(27, 44, 'Lettuce (curly)', 'Vegetables', NULL, 'kg', 400.00, 10.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQNjKEXL67v8Az9G_e1m__ItbBybwz6V5dFsQ3y6UNAkw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:12:29'),
(28, 44, 'Lettuce (ball)', 'Vegetables', NULL, 'kg', 250.00, 15.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRm1-OnSBD9MUkPJzouNlpzGSyVoNPHmkmgIUPIPhHlTg&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:14:03'),
(29, 44, 'Ahos (imported)', 'Spices', NULL, 'kg', 125.00, 30.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ9PwhOMYZYvyB6EgxJFBoyZq_8GUlDM43HtHf3zZkkvw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:15:55'),
(30, 44, 'Atsal (smooth cayene)', 'Spices', NULL, 'kg', 190.00, 20.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSiFpg0hDxdFouVj5Y7KLWvK3ASZHucdLbb8YRU71arZQ&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:16:25'),
(31, 44, 'Atsal (sultan)', 'Spices', NULL, 'kg', 140.00, 25.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQePl7j0A4uDRSezCisBZsTcltGq3hdUdTriwLyWuEUUw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:16:56'),
(32, 44, 'Bombay (native)', 'Spices', NULL, 'kg', 100.00, 40.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTpBJIirQJQKFJc3re584icDBFOGA55sg4ZAU5M8XjISQ&s=10', 1, 1, '2026-06-21 19:12:51', '2026-06-22 16:17:30'),
(33, 44, 'Bombay (white)', 'Spices', NULL, 'kg', 120.00, 35.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSRTPnVLijJ2hFvZaREOxwdRtMa-QGxSUeFYZinYIdz0w&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:18:02'),
(34, 44, 'Sili (labuyo)', 'Spices', NULL, 'kg', 200.00, 15.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQsqa0CbjQLVvRKlQXM4SsZTUMlpFsJHJqjzKWeTNIA1Q&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:18:30'),
(35, 44, 'Sili (kolikot)', 'Spices', NULL, 'kg', 150.00, 20.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRNPRor8jrTlDSHn7oHatbr3TGkblO9LAjiLLXFdytoR1C4f6BvGbAVL3M&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:19:58'),
(36, 44, 'Sili (native)', 'Spices', NULL, 'kg', 600.00, 10.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSozZuTSpPUCK7GdGBamuovb0fEF6H7tJ4VFHZfpkE8H6qK5VTQzdjynwg&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:22:36'),
(37, 44, 'Sili (dynamite)', 'Spices', NULL, 'kg', 120.00, 25.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSNj441zs4rnyDGDljYSsRlguI3LCpPbILfGSeuFudlcA&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:23:17'),
(38, 44, 'Luy-a (hawaian)', 'Spices', NULL, 'kg', 110.00, 30.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTonUhvs6PWq6lPK1O_qiLN9hGnIanj0XutoNkGT5ZahA&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:23:54'),
(39, 44, 'Sibuyas dahon', 'Spices', NULL, 'bundle', 140.00, 50.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQcPM32ELAJW0Jvf3RE05GpZMGENHZpt8UAAi8XfScrCQ&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:24:53'),
(40, 44, 'Tanglad', 'Spices', NULL, 'bundle', 10.00, 80.00, NULL, 'https://filipinochow.com/wp-content/uploads/2020/05/lemongrass-tanglad.jpg', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:26:08'),
(41, 44, 'Gabi (bisol)', 'Rice & Grains', NULL, 'kg', 70.00, 40.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0MxGzmhqSAUhi2luzynuQ2NQw2bnP4GSiTDwT6Be9Hg&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:26:46'),
(42, 44, 'Kamote', 'Rice & Grains', NULL, 'kg', 50.00, 60.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSqhqyyGT1tMT3kqTI0P78ioRDkoCQCdsKT8vIli9ingw&s=10', 1, 1, '2026-06-21 19:12:51', '2026-06-22 16:29:57'),
(43, 44, 'Karlang', 'Rice & Grains', NULL, 'kg', 60.00, 35.00, NULL, 'https://magsige.com/wp-content/uploads/2022/06/karlang.jpeg', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:30:29'),
(44, 44, 'Cassava', 'Rice & Grains', NULL, 'kg', 25.00, 50.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ19dPZyHumKOzPtmeh4GEDBYDQHwk-XYjRM64gOwbBwQ&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:31:08'),
(45, 44, 'Durian (Puyat)', 'Fruits', NULL, 'kg', 250.00, 20.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSxTHetz9amzo8QJjhMn_RpOzBHwNIwHV_Mr5MdEH7Y4Q&s=10', 1, 1, '2026-06-21 19:12:51', '2026-06-22 16:31:42'),
(46, 44, 'Durian (Arancillo)', 'Fruits', NULL, 'kg', 200.00, 15.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQFPN2mBh_U2VlZIzl-nYwoLETMDBEJ9j9NWBWIWfWZ6g&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:32:13'),
(47, 44, 'Durian (D101)', 'Fruits', NULL, 'kg', 250.00, 18.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRfBZayM96xy7tR5GpFAdOFBiKYYb5Tif-oj5NHSJtYXw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:32:46'),
(48, 44, 'Durian (Cob)', 'Fruits', NULL, 'kg', 200.00, 12.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRG_eMExCqRM2LjH2i3A4qhtw4KgYeWow9YpTwKWFp7_Q&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:33:09'),
(49, 44, 'Kalamansi (local)', 'Fruits', NULL, 'kg', 60.00, 40.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSXfCOa8K2mgl0xoJ372jnPOVhefMIlkHoau8CZQBgkww&s=10', 1, 1, '2026-06-21 19:12:51', '2026-06-22 15:50:58'),
(50, 44, 'Mangga (cebu)', 'Fruits', NULL, 'kg', 120.00, 49.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ_dBNzz-6C7BxYZ4UPwV2tMcfMBcQShQVVHHUOINilQAqfRP03xNN8ugs&s=10', 1, 1, '2026-06-21 19:12:51', '2026-06-22 21:10:52'),
(51, 44, 'Mangga (carabao)', 'Fruits', NULL, 'kg', 65.00, 45.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR0fiwhDrEoGwZLGkZjEABX2L6aWkcWIm6qThj1exiAtkljT5bLeDOklxw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:34:21'),
(52, 44, 'Papaya (solo)', 'Fruits', NULL, 'kg', 30.00, 60.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSa-A6bl7TeaHvAVBR48lpeGbBtiS4HmedSq07F1XNRXJXxq1zE7X3BxtQ&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:35:08'),
(53, 44, 'Papaya (red lady)', 'Fruits', NULL, 'kg', 40.00, 55.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSF66cLHvNhNvzljdTgyS9wdYDEg_-WixFCLEsfgaXP9ja8d9ub5Ng2oYU&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:35:43'),
(54, 44, 'Pomelo (magallanes)', 'Fruits', NULL, 'kg', 85.00, 30.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT0KmtviEfydcZq85AEpx5_NDeTGHEMPuMC8mCWippFEA&s', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:36:21'),
(55, 44, 'Saging (lakatan)', 'Fruits', NULL, 'kg', 55.00, 70.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTRka1LA2RJF1EShxKZrY2GOkKbg74FmRzYZB6bi3L9lg&s=10', 1, 1, '2026-06-21 19:12:51', '2026-06-22 16:37:02'),
(56, 44, 'Saging (latundan)', 'Fruits', NULL, 'kg', 45.00, 65.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRJCj-zvfv8OTBrYvUw9Tn6XgjptgkGLgOieEYk2_z4DA&s=10', 1, 1, '2026-06-21 19:12:51', '2026-06-22 16:39:21'),
(57, 44, 'Saging (cardava)', 'Fruits', NULL, 'kg', 30.00, 80.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRa8IiwWKBFQpjBZAdL-Uw2Nn0jqILm_9D0NRaqSVCqzaZ_aW-0ZqZFCzY&s=10', 1, 1, '2026-06-21 19:12:51', '2026-06-22 16:40:24'),
(58, 44, 'Avocado', 'Fruits', NULL, 'kg', 85.00, 25.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRt0C4ihKUVMAuocBip2-OeG0EevmQHWT2XrHgndX_Juw&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:41:02'),
(59, 44, 'Watermelon ', 'Fruits', NULL, 'kg', 45.00, 40.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRMSCXPP83J73uo2H4xmD8mhnPVufaVsKh4aowzLHZE3A&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:42:44'),
(60, 44, 'Poncan', 'Fruits', NULL, 'pc', 10.00, 100.00, NULL, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTaLIzwtd5rkVy2oxZQitjmL7B0vPX0QREE-ZI2OPD3-Q&s=10', 1, 0, '2026-06-21 19:12:51', '2026-06-22 16:44:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accomplishment_reports`
--
ALTER TABLE `accomplishment_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD UNIQUE KEY `uq_bns_month_year` (`bns_id`,`report_month`,`report_year`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_bns` (`bns_id`);

--
-- Indexes for table `diet_consumption_logs`
--
ALTER TABLE `diet_consumption_logs`
  ADD PRIMARY KEY (`consumption_id`),
  ADD KEY `idx_meal_plan` (`meal_plan_id`),
  ADD KEY `idx_child` (`child_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_date` (`consumption_date`);

--
-- Indexes for table `diet_plans`
--
ALTER TABLE `diet_plans`
  ADD PRIMARY KEY (`diet_plan_id`),
  ADD KEY `idx_child` (`child_id`),
  ADD KEY `idx_family` (`family_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `diet_plan_meal_plan_link`
--
ALTER TABLE `diet_plan_meal_plan_link`
  ADD PRIMARY KEY (`link_id`),
  ADD UNIQUE KEY `unique_link` (`diet_plan_id`,`meal_plan_id`),
  ADD KEY `meal_plan_id` (`meal_plan_id`);

--
-- Indexes for table `education_attendance`
--
ALTER TABLE `education_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_user` (`user_id`);

--
-- Indexes for table `education_topics`
--
ALTER TABLE `education_topics`
  ADD PRIMARY KEY (`topic_id`);

--
-- Indexes for table `family_food_activities`
--
ALTER TABLE `family_food_activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_family_activity` (`family_id`,`activity_id`),
  ADD KEY `idx_family_id` (`family_id`),
  ADD KEY `idx_activity_id` (`activity_id`);

--
-- Indexes for table `family_links`
--
ALTER TABLE `family_links`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `idx_fl_user_a` (`user_id_a`),
  ADD KEY `idx_fl_user_b` (`user_id_b`),
  ADD KEY `idx_fl_status` (`verification_status`);

--
-- Indexes for table `family_members`
--
ALTER TABLE `family_members`
  ADD PRIMARY KEY (`member_id`),
  ADD KEY `idx_family_id` (`family_id`),
  ADD KEY `idx_educ_level` (`educ_level_id`),
  ADD KEY `idx_status` (`status_id`);

--
-- Indexes for table `family_profiles`
--
ALTER TABLE `family_profiles`
  ADD PRIMARY KEY (`family_id`),
  ADD UNIQUE KEY `uq_source_user_id` (`source_user_id`),
  ADD KEY `idx_bns_id` (`bns_id`),
  ADD KEY `idx_toilet_type` (`toilet_type_id`),
  ADD KEY `idx_water_source` (`water_source_id`),
  ADD KEY `idx_dwelling_type` (`dwelling_type_id`),
  ADD KEY `idx_fp_method` (`fp_method_id`);

--
-- Indexes for table `feeding_program_attendance`
--
ALTER TABLE `feeding_program_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD UNIQUE KEY `unique_session_client` (`session_id`,`name_of_client`,`mother_name`),
  ADD KEY `idx_session` (`session_id`),
  ADD KEY `idx_proposal` (`proposal_id`),
  ADD KEY `idx_child` (`child_id`),
  ADD KEY `idx_mother` (`mother_id`),
  ADD KEY `idx_is_present` (`is_present`);

--
-- Indexes for table `feeding_program_proposals`
--
ALTER TABLE `feeding_program_proposals`
  ADD PRIMARY KEY (`proposal_id`);

--
-- Indexes for table `feeding_program_sessions`
--
ALTER TABLE `feeding_program_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_proposal` (`proposal_id`),
  ADD KEY `idx_session_date` (`session_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_conducted_by` (`conducted_by_user_id`);

--
-- Indexes for table `filipino_foods`
--
ALTER TABLE `filipino_foods`
  ADD PRIMARY KEY (`food_id`),
  ADD UNIQUE KEY `unique_food_name` (`food_name`);

--
-- Indexes for table `grocery_lists`
--
ALTER TABLE `grocery_lists`
  ADD PRIMARY KEY (`grocery_list_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_family` (`family_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `grocery_list_items`
--
ALTER TABLE `grocery_list_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_grocery_list` (`grocery_list_id`),
  ADD KEY `idx_vendor` (`purchased_from_vendor_id`);

--
-- Indexes for table `households`
--
ALTER TABLE `households`
  ADD PRIMARY KEY (`household_id`),
  ADD UNIQUE KEY `uq_household_code` (`household_code`),
  ADD KEY `idx_hof_user` (`hof_user_id`),
  ADD KEY `fk_hh_draft_spouse` (`draft_spouse_user_id`);

--
-- Indexes for table `household_children`
--
ALTER TABLE `household_children`
  ADD PRIMARY KEY (`child_id`),
  ADD KEY `idx_hc_household` (`household_id`),
  ADD KEY `idx_hc_added_by` (`added_by`);

--
-- Indexes for table `household_members`
--
ALTER TABLE `household_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_household_user` (`household_id`,`user_id`),
  ADD KEY `idx_hm_household` (`household_id`),
  ADD KEY `idx_hm_user` (`user_id`);

--
-- Indexes for table `household_pantry`
--
ALTER TABLE `household_pantry`
  ADD PRIMARY KEY (`pantry_id`),
  ADD KEY `idx_family` (`family_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_item` (`item_name`);

--
-- Indexes for table `ingredient_acquisitions`
--
ALTER TABLE `ingredient_acquisitions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_ingredient_per_item` (`meal_plan_item_id`,`user_id`,`ingredient_name`),
  ADD KEY `idx_ingredient_acquisitions_user_item` (`user_id`,`meal_plan_item_id`),
  ADD KEY `idx_ingredient_acquisitions_acquired` (`is_acquired`);

--
-- Indexes for table `ingredient_aliases`
--
ALTER TABLE `ingredient_aliases`
  ADD PRIMARY KEY (`alias_id`),
  ADD KEY `idx_primary_name` (`primary_name`),
  ADD KEY `idx_alias_name` (`alias_name`);

--
-- Indexes for table `meal_plans`
--
ALTER TABLE `meal_plans`
  ADD PRIMARY KEY (`meal_plan_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_family` (`family_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_completion_status` (`completion_status`),
  ADD KEY `idx_completed_by_mother` (`completed_by_mother`);

--
-- Indexes for table `meal_plan_items`
--
ALTER TABLE `meal_plan_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `idx_meal_plan` (`meal_plan_id`),
  ADD KEY `idx_day` (`day_number`),
  ADD KEY `idx_is_consumed` (`is_consumed`),
  ADD KEY `idx_consumed_date` (`consumed_date`),
  ADD KEY `fk_consumed_by_user` (`consumed_by_user_id`),
  ADD KEY `idx_consumption_photo` (`consumption_photo`);

--
-- Indexes for table `meal_recipes`
--
ALTER TABLE `meal_recipes`
  ADD PRIMARY KEY (`recipe_id`);

--
-- Indexes for table `meeting_minutes`
--
ALTER TABLE `meeting_minutes`
  ADD PRIMARY KEY (`minute_id`),
  ADD KEY `fk_minutes_reviewed_by` (`reviewed_by`);

--
-- Indexes for table `monitoring_visits`
--
ALTER TABLE `monitoring_visits`
  ADD PRIMARY KEY (`visit_id`),
  ADD UNIQUE KEY `uq_visit` (`assessment_id`,`visit_month_number`),
  ADD KEY `idx_assessment` (`assessment_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_notif_user_unread` (`user_id`,`is_read`);

--
-- Indexes for table `nutritional_recovery_validations`
--
ALTER TABLE `nutritional_recovery_validations`
  ADD PRIMARY KEY (`validation_id`),
  ADD KEY `idx_child_id` (`child_id`),
  ADD KEY `idx_fm_member_id` (`fm_member_id`),
  ADD KEY `idx_proposal_id` (`proposal_id`),
  ADD KEY `idx_validated_by` (`validated_by_user_id`),
  ADD KEY `idx_recovery_status` (`recovery_status`),
  ADD KEY `idx_validation_date` (`validation_date`);

--
-- Indexes for table `nutrition_assessments`
--
ALTER TABLE `nutrition_assessments`
  ADD PRIMARY KEY (`assessment_id`),
  ADD KEY `idx_bns` (`bns_id`),
  ADD KEY `idx_child` (`child_id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_date` (`assessment_date`),
  ADD KEY `idx_at_risk` (`is_at_risk`),
  ADD KEY `idx_monitoring` (`needs_monitoring`),
  ADD KEY `idx_fm_member_id` (`fm_member_id`);

--
-- Indexes for table `nutrition_education_sessions`
--
ALTER TABLE `nutrition_education_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `idx_bns` (`bns_id`),
  ADD KEY `idx_date` (`session_date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `nutrition_records`
--
ALTER TABLE `nutrition_records`
  ADD PRIMARY KEY (`record_id`),
  ADD KEY `idx_mother_id` (`mother_id`),
  ADD KEY `idx_bns_id` (`bns_id`),
  ADD KEY `idx_validated_by` (`validated_by`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_user_orders` (`user_id`),
  ADD KEY `idx_order_number` (`order_number`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_order_status` (`order_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Indexes for table `pantry_history`
--
ALTER TABLE `pantry_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `pantry_id` (`pantry_id`);

--
-- Indexes for table `product_sales`
--
ALTER TABLE `product_sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `idx_product` (`product_id`),
  ADD KEY `idx_vendor` (`vendor_user_id`),
  ADD KEY `idx_buyer` (`buyer_user_id`),
  ADD KEY `idx_sale_date` (`sale_date`);

--
-- Indexes for table `proposal_validations`
--
ALTER TABLE `proposal_validations`
  ADD PRIMARY KEY (`validation_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`recipe_id`);

--
-- Indexes for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD PRIMARY KEY (`ri_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `ref_dwelling_types`
--
ALTER TABLE `ref_dwelling_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ref_educ_levels`
--
ALTER TABLE `ref_educ_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ref_food_activities`
--
ALTER TABLE `ref_food_activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_code` (`code`);

--
-- Indexes for table `ref_fp_methods`
--
ALTER TABLE `ref_fp_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ref_nutri_statuses`
--
ALTER TABLE `ref_nutri_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ref_toilet_types`
--
ALTER TABLE `ref_toilet_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_code` (`code`);

--
-- Indexes for table `ref_water_sources`
--
ALTER TABLE `ref_water_sources`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_code` (`code`);

--
-- Indexes for table `ref_zscore_hfa`
--
ALTER TABLE `ref_zscore_hfa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_hfa` (`age_months`,`sex`);

--
-- Indexes for table `ref_zscore_wfa`
--
ALTER TABLE `ref_zscore_wfa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_wfa` (`age_months`,`sex`);

--
-- Indexes for table `ref_zscore_wfh`
--
ALTER TABLE `ref_zscore_wfh`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_wfh` (`height_cm`,`sex`);

--
-- Indexes for table `report_attachments`
--
ALTER TABLE `report_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD KEY `idx_report` (`report_id`),
  ADD KEY `idx_bns` (`bns_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `session_materials`
--
ALTER TABLE `session_materials`
  ADD PRIMARY KEY (`material_id`),
  ADD KEY `idx_session` (`session_id`);

--
-- Indexes for table `session_rsvp`
--
ALTER TABLE `session_rsvp`
  ADD PRIMARY KEY (`rsvp_id`),
  ADD UNIQUE KEY `uq_session_user` (`session_id`,`user_id`),
  ADD KEY `idx_session` (`session_id`);

--
-- Indexes for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `idx_user_cart` (`user_id`),
  ADD KEY `idx_added_date` (`added_date`);

--
-- Indexes for table `srp_references`
--
ALTER TABLE `srp_references`
  ADD PRIMARY KEY (`srp_id`),
  ADD KEY `idx_product_name` (`product_name`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_price_date` (`price_date`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action_type` (`action_type`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uq_email` (`email`),
  ADD KEY `idx_role_id` (`role_id`);

--
-- Indexes for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD PRIMARY KEY (`auth_id`),
  ADD UNIQUE KEY `uq_user_auth` (`user_id`),
  ADD UNIQUE KEY `uq_google_id` (`google_id`);

--
-- Indexes for table `user_health_profiles`
--
ALTER TABLE `user_health_profiles`
  ADD PRIMARY KEY (`health_id`),
  ADD UNIQUE KEY `uq_user_health` (`user_id`);

--
-- Indexes for table `user_otp`
--
ALTER TABLE `user_otp`
  ADD PRIMARY KEY (`otp_id`),
  ADD KEY `idx_user_otp` (`user_id`);

--
-- Indexes for table `user_password_resets`
--
ALTER TABLE `user_password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD UNIQUE KEY `uq_reset_token` (`reset_token`),
  ADD KEY `idx_user_resets` (`user_id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD UNIQUE KEY `uq_user_profile` (`user_id`),
  ADD KEY `fk_profiles_assigned_bns` (`assigned_bns_id`);

--
-- Indexes for table `user_trusted_devices`
--
ALTER TABLE `user_trusted_devices`
  ADD PRIMARY KEY (`device_id`),
  ADD UNIQUE KEY `uq_device_token` (`device_token`),
  ADD KEY `idx_user_trusted` (`user_id`);

--
-- Indexes for table `vendor_products`
--
ALTER TABLE `vendor_products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_vendor` (`vendor_user_id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_available` (`is_available`),
  ADD KEY `idx_featured` (`is_featured`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accomplishment_reports`
--
ALTER TABLE `accomplishment_reports`
  MODIFY `report_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `diet_consumption_logs`
--
ALTER TABLE `diet_consumption_logs`
  MODIFY `consumption_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `diet_plans`
--
ALTER TABLE `diet_plans`
  MODIFY `diet_plan_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `diet_plan_meal_plan_link`
--
ALTER TABLE `diet_plan_meal_plan_link`
  MODIFY `link_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `education_attendance`
--
ALTER TABLE `education_attendance`
  MODIFY `attendance_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `education_topics`
--
ALTER TABLE `education_topics`
  MODIFY `topic_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `family_food_activities`
--
ALTER TABLE `family_food_activities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `family_links`
--
ALTER TABLE `family_links`
  MODIFY `link_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `family_members`
--
ALTER TABLE `family_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=271;

--
-- AUTO_INCREMENT for table `family_profiles`
--
ALTER TABLE `family_profiles`
  MODIFY `family_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `feeding_program_attendance`
--
ALTER TABLE `feeding_program_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2045;

--
-- AUTO_INCREMENT for table `feeding_program_proposals`
--
ALTER TABLE `feeding_program_proposals`
  MODIFY `proposal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `feeding_program_sessions`
--
ALTER TABLE `feeding_program_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=439;

--
-- AUTO_INCREMENT for table `filipino_foods`
--
ALTER TABLE `filipino_foods`
  MODIFY `food_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `grocery_lists`
--
ALTER TABLE `grocery_lists`
  MODIFY `grocery_list_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `grocery_list_items`
--
ALTER TABLE `grocery_list_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=777;

--
-- AUTO_INCREMENT for table `households`
--
ALTER TABLE `households`
  MODIFY `household_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `household_children`
--
ALTER TABLE `household_children`
  MODIFY `child_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `household_members`
--
ALTER TABLE `household_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `household_pantry`
--
ALTER TABLE `household_pantry`
  MODIFY `pantry_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `ingredient_acquisitions`
--
ALTER TABLE `ingredient_acquisitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ingredient_aliases`
--
ALTER TABLE `ingredient_aliases`
  MODIFY `alias_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `meal_plans`
--
ALTER TABLE `meal_plans`
  MODIFY `meal_plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `meal_plan_items`
--
ALTER TABLE `meal_plan_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `meal_recipes`
--
ALTER TABLE `meal_recipes`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `meeting_minutes`
--
ALTER TABLE `meeting_minutes`
  MODIFY `minute_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `monitoring_visits`
--
ALTER TABLE `monitoring_visits`
  MODIFY `visit_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=619;

--
-- AUTO_INCREMENT for table `nutritional_recovery_validations`
--
ALTER TABLE `nutritional_recovery_validations`
  MODIFY `validation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `nutrition_assessments`
--
ALTER TABLE `nutrition_assessments`
  MODIFY `assessment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `nutrition_education_sessions`
--
ALTER TABLE `nutrition_education_sessions`
  MODIFY `session_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `nutrition_records`
--
ALTER TABLE `nutrition_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `pantry_history`
--
ALTER TABLE `pantry_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `product_sales`
--
ALTER TABLE `product_sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `proposal_validations`
--
ALTER TABLE `proposal_validations`
  MODIFY `validation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `recipe_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  MODIFY `ri_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ref_dwelling_types`
--
ALTER TABLE `ref_dwelling_types`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ref_educ_levels`
--
ALTER TABLE `ref_educ_levels`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ref_food_activities`
--
ALTER TABLE `ref_food_activities`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ref_fp_methods`
--
ALTER TABLE `ref_fp_methods`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `ref_nutri_statuses`
--
ALTER TABLE `ref_nutri_statuses`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ref_toilet_types`
--
ALTER TABLE `ref_toilet_types`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ref_water_sources`
--
ALTER TABLE `ref_water_sources`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ref_zscore_hfa`
--
ALTER TABLE `ref_zscore_hfa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `ref_zscore_wfa`
--
ALTER TABLE `ref_zscore_wfa`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `ref_zscore_wfh`
--
ALTER TABLE `ref_zscore_wfh`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=395;

--
-- AUTO_INCREMENT for table `report_attachments`
--
ALTER TABLE `report_attachments`
  MODIFY `attachment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `session_materials`
--
ALTER TABLE `session_materials`
  MODIFY `material_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `session_rsvp`
--
ALTER TABLE `session_rsvp`
  MODIFY `rsvp_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=654;

--
-- AUTO_INCREMENT for table `srp_references`
--
ALTER TABLE `srp_references`
  MODIFY `srp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=530;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=784;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `user_auth`
--
ALTER TABLE `user_auth`
  MODIFY `auth_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=288;

--
-- AUTO_INCREMENT for table `user_health_profiles`
--
ALTER TABLE `user_health_profiles`
  MODIFY `health_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `user_otp`
--
ALTER TABLE `user_otp`
  MODIFY `otp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=216;

--
-- AUTO_INCREMENT for table `user_password_resets`
--
ALTER TABLE `user_password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `user_trusted_devices`
--
ALTER TABLE `user_trusted_devices`
  MODIFY `device_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `vendor_products`
--
ALTER TABLE `vendor_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `diet_plan_meal_plan_link`
--
ALTER TABLE `diet_plan_meal_plan_link`
  ADD CONSTRAINT `diet_plan_meal_plan_link_ibfk_1` FOREIGN KEY (`diet_plan_id`) REFERENCES `diet_plans` (`diet_plan_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `diet_plan_meal_plan_link_ibfk_2` FOREIGN KEY (`meal_plan_id`) REFERENCES `meal_plans` (`meal_plan_id`) ON DELETE CASCADE;

--
-- Constraints for table `education_attendance`
--
ALTER TABLE `education_attendance`
  ADD CONSTRAINT `education_attendance_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `nutrition_education_sessions` (`session_id`) ON DELETE CASCADE;

--
-- Constraints for table `family_food_activities`
--
ALTER TABLE `family_food_activities`
  ADD CONSTRAINT `fk_ffa_activity` FOREIGN KEY (`activity_id`) REFERENCES `ref_food_activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ffa_family` FOREIGN KEY (`family_id`) REFERENCES `family_profiles` (`family_id`) ON DELETE CASCADE;

--
-- Constraints for table `family_members`
--
ALTER TABLE `family_members`
  ADD CONSTRAINT `fk_fm_educ` FOREIGN KEY (`educ_level_id`) REFERENCES `ref_educ_levels` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_fm_family` FOREIGN KEY (`family_id`) REFERENCES `family_profiles` (`family_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fm_status` FOREIGN KEY (`status_id`) REFERENCES `ref_nutri_statuses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `family_profiles`
--
ALTER TABLE `family_profiles`
  ADD CONSTRAINT `fk_fp_bns` FOREIGN KEY (`bns_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fp_dwelling` FOREIGN KEY (`dwelling_type_id`) REFERENCES `ref_dwelling_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_fp_fp_method` FOREIGN KEY (`fp_method_id`) REFERENCES `ref_fp_methods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_fp_toilet` FOREIGN KEY (`toilet_type_id`) REFERENCES `ref_toilet_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_fp_water` FOREIGN KEY (`water_source_id`) REFERENCES `ref_water_sources` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `feeding_program_attendance`
--
ALTER TABLE `feeding_program_attendance`
  ADD CONSTRAINT `feeding_program_attendance_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `feeding_program_sessions` (`session_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feeding_program_attendance_ibfk_2` FOREIGN KEY (`proposal_id`) REFERENCES `feeding_program_proposals` (`proposal_id`) ON DELETE CASCADE;

--
-- Constraints for table `feeding_program_sessions`
--
ALTER TABLE `feeding_program_sessions`
  ADD CONSTRAINT `feeding_program_sessions_ibfk_1` FOREIGN KEY (`proposal_id`) REFERENCES `feeding_program_proposals` (`proposal_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feeding_program_sessions_ibfk_2` FOREIGN KEY (`conducted_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `households`
--
ALTER TABLE `households`
  ADD CONSTRAINT `fk_hh_draft_spouse` FOREIGN KEY (`draft_spouse_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `household_children`
--
ALTER TABLE `household_children`
  ADD CONSTRAINT `fk_hc_added_by` FOREIGN KEY (`added_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hc_household` FOREIGN KEY (`household_id`) REFERENCES `households` (`household_id`) ON DELETE CASCADE;

--
-- Constraints for table `ingredient_acquisitions`
--
ALTER TABLE `ingredient_acquisitions`
  ADD CONSTRAINT `ingredient_acquisitions_ibfk_1` FOREIGN KEY (`meal_plan_item_id`) REFERENCES `meal_plan_items` (`item_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ingredient_acquisitions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `meal_plan_items`
--
ALTER TABLE `meal_plan_items`
  ADD CONSTRAINT `fk_consumed_by_user` FOREIGN KEY (`consumed_by_user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `meal_plan_items_ibfk_1` FOREIGN KEY (`meal_plan_id`) REFERENCES `meal_plans` (`meal_plan_id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_minutes`
--
ALTER TABLE `meeting_minutes`
  ADD CONSTRAINT `fk_minutes_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `nutrition_records`
--
ALTER TABLE `nutrition_records`
  ADD CONSTRAINT `fk_nr_bns` FOREIGN KEY (`bns_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_nr_mother` FOREIGN KEY (`mother_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_nr_validator` FOREIGN KEY (`validated_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `pantry_history`
--
ALTER TABLE `pantry_history`
  ADD CONSTRAINT `pantry_history_ibfk_1` FOREIGN KEY (`pantry_id`) REFERENCES `household_pantry` (`pantry_id`) ON DELETE CASCADE;

--
-- Constraints for table `recipe_ingredients`
--
ALTER TABLE `recipe_ingredients`
  ADD CONSTRAINT `recipe_ingredients_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`recipe_id`) ON DELETE CASCADE;

--
-- Constraints for table `session_materials`
--
ALTER TABLE `session_materials`
  ADD CONSTRAINT `session_materials_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `nutrition_education_sessions` (`session_id`) ON DELETE CASCADE;

--
-- Constraints for table `session_rsvp`
--
ALTER TABLE `session_rsvp`
  ADD CONSTRAINT `session_rsvp_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `nutrition_education_sessions` (`session_id`) ON DELETE CASCADE;

--
-- Constraints for table `shopping_cart`
--
ALTER TABLE `shopping_cart`
  ADD CONSTRAINT `shopping_cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_auth`
--
ALTER TABLE `user_auth`
  ADD CONSTRAINT `fk_auth_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_otp`
--
ALTER TABLE `user_otp`
  ADD CONSTRAINT `fk_otp_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_password_resets`
--
ALTER TABLE `user_password_resets`
  ADD CONSTRAINT `fk_resets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `fk_profiles_assigned_bns` FOREIGN KEY (`assigned_bns_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_profiles_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_trusted_devices`
--
ALTER TABLE `user_trusted_devices`
  ADD CONSTRAINT `fk_trusted_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
