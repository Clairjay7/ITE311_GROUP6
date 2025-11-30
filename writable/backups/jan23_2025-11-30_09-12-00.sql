-- Database Backup
-- Generated: 2025-11-30 09:12:00


-- Table: admin_patients
DROP TABLE IF EXISTS `admin_patients`;
CREATE TABLE `admin_patients` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` varchar(10) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `doctor_id` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_patients` VALUES
('1','dsadas','dsadsad','2025-11-05','male','ssssssssssss','sadas','2','2025-11-30 05:25:40','2025-11-30 05:25:40',NULL);


-- Table: appointment_logs
DROP TABLE IF EXISTS `appointment_logs`;
CREATE TABLE `appointment_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `appointment_id` int(11) unsigned NOT NULL,
  `status` enum('scheduled','confirmed','checked_in','in_progress','completed','cancelled','no_show') NOT NULL,
  `changed_by` int(11) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointment_logs_appointment_id_foreign` (`appointment_id`),
  KEY `appointment_logs_changed_by_foreign` (`changed_by`),
  CONSTRAINT `appointment_logs_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `appointment_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: appointments
DROP TABLE IF EXISTS `appointments`;
CREATE TABLE `appointments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) unsigned NOT NULL,
  `doctor_id` int(11) unsigned NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `appointment_type` enum('consultation','follow_up','emergency','routine_checkup') NOT NULL DEFAULT 'consultation',
  `reason` text DEFAULT NULL,
  `status` enum('scheduled','confirmed','in_progress','completed','cancelled','no_show') NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `appointments_patient_id_foreign` (`patient_id`),
  KEY `appointments_doctor_id_foreign` (`doctor_id`),
  CONSTRAINT `appointments_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: billing
DROP TABLE IF EXISTS `billing`;
CREATE TABLE `billing` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) unsigned NOT NULL,
  `service` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `billing_patient_id_foreign` (`patient_id`),
  CONSTRAINT `billing_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `admin_patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: consultations
DROP TABLE IF EXISTS `consultations`;
CREATE TABLE `consultations` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) unsigned NOT NULL,
  `patient_id` int(11) unsigned NOT NULL,
  `consultation_date` date NOT NULL,
  `consultation_time` time NOT NULL,
  `type` enum('upcoming','completed') NOT NULL DEFAULT 'upcoming',
  `notes` text DEFAULT NULL,
  `status` enum('pending','approved','cancelled') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `consultations_doctor_id_foreign` (`doctor_id`),
  KEY `consultations_patient_id_foreign` (`patient_id`),
  CONSTRAINT `consultations_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `consultations_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: departments
DROP TABLE IF EXISTS `departments`;
CREATE TABLE `departments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `department_name` varchar(150) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: doctor_notifications
DROP TABLE IF EXISTS `doctor_notifications`;
CREATE TABLE `doctor_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `doctor_id` int(11) unsigned NOT NULL,
  `type` enum('order_completed','order_updated','lab_request_pending','patient_assigned','system') NOT NULL DEFAULT 'system',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_id` int(11) unsigned DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doctor_notifications_doctor_id_foreign` (`doctor_id`),
  CONSTRAINT `doctor_notifications_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `doctor_notifications` VALUES
('1','2','order_completed','Order Completed','Your diet order for dsadas dsadsad has been completed by a nurse.','2','doctor_order','0',NULL,'2025-11-30 07:16:44','2025-11-30 07:16:44'),
('2','2','order_completed','Order Completed','Your procedure order for dsadas dsadsad has been completed by a nurse.','3','doctor_order','0',NULL,'2025-11-30 07:26:50','2025-11-30 07:26:50');


-- Table: doctor_orders
DROP TABLE IF EXISTS `doctor_orders`;
CREATE TABLE `doctor_orders` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) unsigned NOT NULL,
  `doctor_id` int(11) unsigned NOT NULL,
  `nurse_id` int(11) unsigned DEFAULT NULL,
  `order_type` enum('medication','lab_test','procedure','diet','activity','other') NOT NULL DEFAULT 'medication',
  `order_description` text NOT NULL,
  `instructions` text DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `completed_by` int(11) unsigned DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `doctor_orders_patient_id_foreign` (`patient_id`),
  KEY `doctor_orders_doctor_id_foreign` (`doctor_id`),
  KEY `doctor_orders_completed_by_foreign` (`completed_by`),
  CONSTRAINT `doctor_orders_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `doctor_orders_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `doctor_orders_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `admin_patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `doctor_orders` VALUES
('1','1','2',NULL,'diet','diet lang po idol','','8hrs to 10hrs','2025-12-01','2026-02-01','pending',NULL,NULL,'2025-11-30 07:12:01','2025-11-30 07:12:01'),
('2','1','2',NULL,'diet','diet lang po idol','','8hrs to 10hrs','2025-12-01','2026-02-01','completed','3','2025-11-30 07:16:44','2025-11-30 07:14:46','2025-11-30 07:16:44'),
('3','1','2','3','procedure','dasdas','sadsadas','dsadsa','2025-11-19','2025-11-12','completed','3','2025-11-30 07:26:50','2025-11-30 07:26:22','2025-11-30 07:26:50');


-- Table: doctors
DROP TABLE IF EXISTS `doctors`;
CREATE TABLE `doctors` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `doctor_name` varchar(100) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: lab_requests
DROP TABLE IF EXISTS `lab_requests`;
CREATE TABLE `lab_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) unsigned NOT NULL,
  `doctor_id` int(11) unsigned DEFAULT NULL,
  `nurse_id` int(11) unsigned DEFAULT NULL,
  `test_type` varchar(255) NOT NULL,
  `test_name` varchar(255) NOT NULL,
  `requested_by` enum('doctor','nurse') NOT NULL DEFAULT 'doctor',
  `priority` enum('routine','urgent','stat') NOT NULL DEFAULT 'routine',
  `instructions` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
  `requested_date` date NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lab_requests_patient_id_foreign` (`patient_id`),
  KEY `lab_requests_doctor_id_foreign` (`doctor_id`),
  KEY `lab_requests_nurse_id_foreign` (`nurse_id`),
  CONSTRAINT `lab_requests_doctor_id_foreign` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `lab_requests_nurse_id_foreign` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `lab_requests_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `admin_patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `lab_requests` VALUES
('1','1','2','3','ngiwngiw','krishy','nurse','urgent','please make him blind','in_progress','2025-11-30','2025-11-30 06:02:43','2025-11-30 06:25:38');


-- Table: lab_results
DROP TABLE IF EXISTS `lab_results`;
CREATE TABLE `lab_results` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lab_request_id` int(11) unsigned NOT NULL,
  `result` text DEFAULT NULL,
  `result_file` varchar(255) DEFAULT NULL,
  `result_file_type` varchar(50) DEFAULT NULL,
  `interpretation` text DEFAULT NULL,
  `completed_by` int(11) unsigned DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lab_results_lab_request_id_foreign` (`lab_request_id`),
  KEY `lab_results_completed_by_foreign` (`completed_by`),
  CONSTRAINT `lab_results_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  CONSTRAINT `lab_results_lab_request_id_foreign` FOREIGN KEY (`lab_request_id`) REFERENCES `lab_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: lab_services
DROP TABLE IF EXISTS `lab_services`;
CREATE TABLE `lab_services` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) unsigned NOT NULL,
  `test_type` varchar(255) NOT NULL,
  `result` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lab_services_patient_id_foreign` (`patient_id`),
  CONSTRAINT `lab_services_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `admin_patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: lab_status_history
DROP TABLE IF EXISTS `lab_status_history`;
CREATE TABLE `lab_status_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `lab_request_id` int(11) unsigned NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL,
  `changed_by` int(11) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lab_status_history_lab_request_id_foreign` (`lab_request_id`),
  KEY `lab_status_history_changed_by_foreign` (`changed_by`),
  CONSTRAINT `lab_status_history_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lab_status_history_lab_request_id_foreign` FOREIGN KEY (`lab_request_id`) REFERENCES `lab_requests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `lab_status_history` VALUES
('1','1','pending','3','Lab request created by nurse',NULL),
('2','1','in_progress','2','Lab request confirmed by doctor',NULL);


-- Table: migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `migrations` VALUES
('1','2025-01-20-000001','App\\Database\\Migrations\\CreateAdminPatientsTable','default','App','1764479411','1'),
('2','2025-01-20-000002','App\\Database\\Migrations\\CreateSchedulesTable','default','App','1764479411','1'),
('3','2025-01-20-000003','App\\Database\\Migrations\\CreateBillingTable','default','App','1764479411','1'),
('4','2025-01-20-000004','App\\Database\\Migrations\\CreateLabServicesTable','default','App','1764479412','1'),
('5','2025-01-20-000005','App\\Database\\Migrations\\CreatePharmacyTable','default','App','1764479412','1'),
('6','2025-01-20-000006','App\\Database\\Migrations\\CreateStockMonitoringTable','default','App','1764479413','1'),
('7','2025-01-20-000007','App\\Database\\Migrations\\CreateSystemControlsTable','default','App','1764479413','1'),
('8','2025-01-20-000008','App\\Database\\Migrations\\AddDoctorIdToAdminPatients','default','App','1764479413','1'),
('9','2025-08-21-172042','App\\Database\\Migrations\\CreateUserTable','default','App','1764479414','1'),
('10','2025-11-13-000002','App\\Database\\Migrations\\CreateDoctorsTable','default','App','1764479414','1'),
('11','2025-11-13-000003','App\\Database\\Migrations\\CreatePatientsTable','default','App','1764479414','1'),
('12','2025-11-13-000004','App\\Database\\Migrations\\AlterPatientsAddRegistrationFields','default','App','1764479415','1'),
('13','2025-11-15-000005','App\\Database\\Migrations\\AddExtensionNameToPatients','default','App','1764479416','1'),
('14','2025-11-15-000006','App\\Database\\Migrations\\CreateAppointmentsTable','default','App','1764479419','1'),
('15','2025-11-16-000001','App\\Database\\Migrations\\CreateRoomsTable','default','App','1764479419','1'),
('16','2025-11-16-000002','App\\Database\\Migrations\\AlterPatientsAddWardAndRoomId','default','App','1764479419','1'),
('17','2025-11-16-000003','App\\Database\\Migrations\\CreateDepartmentsTable','default','App','1764479420','1'),
('18','2025-11-16-000004','App\\Database\\Migrations\\CreateConsultationsTable','default','App','1764479420','1'),
('19','2025-11-30-000001','App\\Database\\Migrations\\CreatePatientVitalsTable','default','App','1764482390','2'),
('20','2025-11-30-000002','App\\Database\\Migrations\\CreateNurseNotesTable','default','App','1764482391','2'),
('21','2025-11-30-000003','App\\Database\\Migrations\\CreateDoctorOrdersTable','default','App','1764482391','2'),
('22','2025-11-30-000004','App\\Database\\Migrations\\CreateOrderStatusLogsTable','default','App','1764482391','2'),
('23','2025-11-30-000005','App\\Database\\Migrations\\CreateAppointmentLogsTable','default','App','1764482392','2'),
('24','2025-11-30-000006','App\\Database\\Migrations\\CreateLabRequestsTable','default','App','1764482392','2'),
('25','2025-11-30-000007','App\\Database\\Migrations\\CreateLabResultsTable','default','App','1764482392','2'),
('26','2025-11-30-000008','App\\Database\\Migrations\\CreateLabStatusHistoryTable','default','App','1764482393','2'),
('27','2025-11-30-000009','App\\Database\\Migrations\\CreateNurseNotificationsTable','default','App','1764484804','3'),
('28','2025-11-30-000010','App\\Database\\Migrations\\CreateDoctorNotificationsTable','default','App','1764486289','4'),
('29','2025-11-30-000011','App\\Database\\Migrations\\AddNurseIdToDoctorOrders','default','App','1764487094','5'),
('30','2025-11-30-000012','App\\Database\\Migrations\\CreateSystemLogsTable','default','App','1764492691','6'),
('31','2025-11-30-000013','App\\Database\\Migrations\\CreateSystemBackupsTable','default','App','1764492692','6');


-- Table: nurse_notes
DROP TABLE IF EXISTS `nurse_notes`;
CREATE TABLE `nurse_notes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) unsigned NOT NULL,
  `nurse_id` int(11) unsigned NOT NULL,
  `note_type` enum('progress','observation','medication','incident','other') NOT NULL DEFAULT 'progress',
  `note` text NOT NULL,
  `priority` enum('low','normal','high','urgent') NOT NULL DEFAULT 'normal',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nurse_notes_patient_id_foreign` (`patient_id`),
  KEY `nurse_notes_nurse_id_foreign` (`nurse_id`),
  CONSTRAINT `nurse_notes_nurse_id_foreign` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `nurse_notes_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `admin_patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: nurse_notifications
DROP TABLE IF EXISTS `nurse_notifications`;
CREATE TABLE `nurse_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nurse_id` int(11) unsigned NOT NULL,
  `type` enum('lab_request_approved','lab_result_ready','new_doctor_order','appointment_reminder','system') NOT NULL DEFAULT 'system',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_id` int(11) unsigned DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nurse_notifications_nurse_id_foreign` (`nurse_id`),
  CONSTRAINT `nurse_notifications_nurse_id_foreign` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `nurse_notifications` VALUES
('1','3','new_doctor_order','New Doctor Order','Dr.  has created a new diet order for dsadas dsadsad','2','doctor_order','0',NULL,'2025-11-30 07:14:47','2025-11-30 07:14:47'),
('2','3','new_doctor_order','New Doctor Order','Dr.  has created a new procedure order for dsadas dsadsad. Please execute this order.','3','doctor_order','0',NULL,'2025-11-30 07:26:22','2025-11-30 07:26:22');


-- Table: order_status_logs
DROP TABLE IF EXISTS `order_status_logs`;
CREATE TABLE `order_status_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') NOT NULL,
  `changed_by` int(11) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_status_logs_order_id_foreign` (`order_id`),
  KEY `order_status_logs_changed_by_foreign` (`changed_by`),
  CONSTRAINT `order_status_logs_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `order_status_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `doctor_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `order_status_logs` VALUES
('1','1','pending','2','Order created by doctor',NULL),
('2','2','pending','2','Order created by doctor',NULL),
('3','2','completed','3',NULL,NULL),
('4','3','pending','2','Order created by doctor',NULL),
('5','3','completed','3',NULL,NULL);


-- Table: patient_vitals
DROP TABLE IF EXISTS `patient_vitals`;
CREATE TABLE `patient_vitals` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) unsigned NOT NULL,
  `nurse_id` int(11) unsigned NOT NULL,
  `blood_pressure_systolic` int(3) DEFAULT NULL,
  `blood_pressure_diastolic` int(3) DEFAULT NULL,
  `heart_rate` int(3) DEFAULT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `oxygen_saturation` int(3) DEFAULT NULL,
  `respiratory_rate` int(3) DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `recorded_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_vitals_patient_id_foreign` (`patient_id`),
  KEY `patient_vitals_nurse_id_foreign` (`nurse_id`),
  CONSTRAINT `patient_vitals_nurse_id_foreign` FOREIGN KEY (`nurse_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `patient_vitals_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `admin_patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: patients
DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
  `patient_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(150) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `contact` varchar(50) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL COMMENT 'In-Patient or Out-Patient',
  `doctor_id` int(11) unsigned DEFAULT NULL,
  `department_id` int(11) unsigned DEFAULT NULL,
  `purpose` text DEFAULT NULL,
  `admission_date` date DEFAULT NULL,
  `room_number` varchar(50) DEFAULT NULL,
  `ward` varchar(50) DEFAULT NULL,
  `room_id` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `patient_reg_no` varchar(30) DEFAULT NULL,
  `first_name` varchar(60) DEFAULT NULL,
  `middle_name` varchar(60) DEFAULT NULL,
  `last_name` varchar(60) DEFAULT NULL,
  `extension_name` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `civil_status` varchar(20) DEFAULT NULL,
  `address_street` varchar(120) DEFAULT NULL,
  `address_barangay` varchar(120) DEFAULT NULL,
  `address_city` varchar(120) DEFAULT NULL,
  `address_province` varchar(120) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `nationality` varchar(60) DEFAULT NULL,
  `religion` varchar(60) DEFAULT NULL,
  `emergency_name` varchar(120) DEFAULT NULL,
  `emergency_relationship` varchar(60) DEFAULT NULL,
  `emergency_contact` varchar(30) DEFAULT NULL,
  `emergency_address` varchar(255) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `existing_conditions` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `past_surgeries` text DEFAULT NULL,
  `family_history` text DEFAULT NULL,
  `insurance_provider` varchar(120) DEFAULT NULL,
  `insurance_number` varchar(80) DEFAULT NULL,
  `philhealth_number` varchar(50) DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `payment_type` varchar(20) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `registered_by` varchar(80) DEFAULT NULL,
  `signature_patient` varchar(255) DEFAULT NULL,
  `signature_staff` varchar(255) DEFAULT NULL,
  `date_signed` date DEFAULT NULL,
  PRIMARY KEY (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: pharmacy
DROP TABLE IF EXISTS `pharmacy`;
CREATE TABLE `pharmacy` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: roles
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` VALUES
('1','admin','Administrator with full access','2025-11-30 05:10:13','2025-11-30 05:10:13'),
('2','doctor','Medical doctor','2025-11-30 05:10:13','2025-11-30 05:10:13'),
('3','nurse','Nursing staff','2025-11-30 05:10:13','2025-11-30 05:10:13'),
('4','receptionist','Front desk staff','2025-11-30 05:10:13','2025-11-30 05:10:13'),
('5','patient','Patient','2025-11-30 05:10:13','2025-11-30 05:10:13'),
('6','finance','Finance role','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('7','itstaff','Itstaff role','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('8','lab_staff','Lab_staff role','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('9','pharmacy','Pharmacy role','2025-11-30 05:11:55','2025-11-30 05:11:55');


-- Table: rooms
DROP TABLE IF EXISTS `rooms`;
CREATE TABLE `rooms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ward` varchar(50) NOT NULL,
  `room_number` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Available' COMMENT 'Available or Occupied',
  `current_patient_id` int(11) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `rooms` VALUES
('1','Pedia Ward','P01','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('2','Pedia Ward','P02','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('3','Pedia Ward','P03','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('4','Pedia Ward','P04','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('5','Pedia Ward','P05','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('6','Male Ward','M01','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('7','Male Ward','M02','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('8','Male Ward','M03','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('9','Male Ward','M04','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('10','Male Ward','M05','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('11','Female Ward','F01','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('12','Female Ward','F02','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('13','Female Ward','F03','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('14','Female Ward','F04','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19'),
('15','Female Ward','F05','Available',NULL,'2025-11-30 05:10:19','2025-11-30 05:10:19');


-- Table: schedules
DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `doctor` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedules_patient_id_foreign` (`patient_id`),
  CONSTRAINT `schedules_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `admin_patients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: stock_monitoring
DROP TABLE IF EXISTS `stock_monitoring`;
CREATE TABLE `stock_monitoring` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `item_name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `threshold` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: system_backups
DROP TABLE IF EXISTS `system_backups`;
CREATE TABLE `system_backups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `backup_name` varchar(255) NOT NULL,
  `backup_type` enum('database','files','full') NOT NULL DEFAULT 'database',
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) unsigned DEFAULT NULL,
  `status` enum('pending','in_progress','completed','failed') NOT NULL DEFAULT 'pending',
  `created_by` int(11) unsigned DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_backups_created_by_foreign` (`created_by`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `system_backups_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `system_backups` VALUES
('1','jan2','database','F:\\xammp\\htdocs\\group6\\writable\\backups/jan2_2025-11-30_09-08-56.sql','33644','failed','6','bk for db','2025-11-30 09:08:56','2025-11-30 09:10:51'),
('2','jan2','database','F:\\xammp\\htdocs\\group6\\writable\\backups/jan2_2025-11-30_09-10-34.sql','33879','completed','6','bk for db','2025-11-30 09:10:34','2025-11-30 09:10:34');


-- Table: system_controls
DROP TABLE IF EXISTS `system_controls`;
CREATE TABLE `system_controls` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `setting_name` varchar(255) NOT NULL,
  `setting_value` varchar(500) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_name` (`setting_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: system_logs
DROP TABLE IF EXISTS `system_logs`;
CREATE TABLE `system_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `level` enum('emergency','alert','critical','error','warning','notice','info','debug') NOT NULL DEFAULT 'info',
  `message` text NOT NULL,
  `context` text DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `module` varchar(100) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `level` (`level`),
  KEY `created_at` (`created_at`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `system_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Table: users
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) unsigned NOT NULL DEFAULT 5,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES
('1','sys.admin','sysadmin@group6.edu.ph','$2y$10$qQqVy8u1IsRTbUQ3BBu8wufcT3cySW04VJeSHQL8NhGxl7WKRb2/S','1','active','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('2','dr.delacruz','j.delacruz@group6.edu.ph','$2y$10$iO7alpCM9/L1WozFqqlSbOj3jtrjFi0Mvsu80x0JMKMDnxJidEaDm','2','active','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('3','nurse.reyes','m.reyes@group6.edu.ph','$2y$10$UPAbVzCiRkFM69cXkhCDEeUP16puZOt.zMDwyGY3ZALjFnF3NAUDq','3','active','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('4','frontdesk1','frontdesk@group6.edu.ph','$2y$10$cfynPo42DD.JpNOlUW7CGOVf3h2ak4eO8UvRgkdyHz9E83GPWQVHa','4','active','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('5','acct.dept','accounting@group6.edu.ph','$2y$10$IhHMc3ih2AdIelutPkpVCuXW5iGhHnPkcMbW5oZ8swp3TqMIMgYvm','6','active','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('6','it.support','itsupport@group6.edu.ph','$2y$10$arKSJGmyco6MpOhha5ZdoOGfWOxVm10u2QHdkqsge.TNUB.FD5fhe','7','active','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('7','lab.tech','laboratory@group6.edu.ph','$2y$10$gpWkZz8vJQIssO3546vRQ.cXUHWVAEWettkieE.si72I19oTFBgZm','8','active','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('8','pharm.staff','pharmacy@group6.edu.ph','$2y$10$JqBc6qXyjJB4nwrL6mriNOlg1v.gxBJ68vjd/bA3t1w6bJ.nfEnya','9','active','2025-11-30 05:11:55','2025-11-30 05:11:55'),
('11','admin','admin@example.com','$2y$10$00F3crzHorD6DFSIAFS/OeqFdWkal6x6P8Zf.HsMgwGZxFDuvqf6e','1','active','2025-11-30 08:28:28','2025-11-30 08:28:28'),
('12','Galorpot','clairjaygalorptgamer@gmail.com','$2y$10$nc/rxAxbnNYz9v01mJaASe1ME5o24LY/INYoB6mbU1trTLClrRlmi','1','active','2025-11-30 08:29:32','2025-11-30 08:29:32');

