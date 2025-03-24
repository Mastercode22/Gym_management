-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2025 at 03:29 PM
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
-- Database: `gym_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `membership_plan` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `email`, `phone`, `membership_plan`, `payment_method`, `created_at`) VALUES
(24, 'Emmanuel', 'emmanuelquarshie395@gmail.com', '0538726615', '12-months', 'mobile-money', '2025-03-11 13:30:26'),
(25, 'Akua', 'akua12@outlook.com', '0538726699', '3-months', 'mobile-money', '2025-03-11 13:33:57'),
(27, 'Josia Andoh', 'Josia12@outlook.com', '0598726699', '6-months', 'mobile-money', '2025-03-11 14:23:48'),
(28, 'Jerry', 'Jerry12@outlook.com', '05987289999', '1-month', 'credit-card', '2025-03-11 14:29:41'),
(29, 'Joana', 'joana@gmail.com', '053872800', '3-months', 'credit-card', '2025-03-12 12:40:46'),
(30, 'Larry John', 'Larry@gmail.com', '0538726610', '12-months', 'credit-card', '2025-03-12 12:43:22'),
(34, 'Emmanuela', 'emmanuela5@gmail.com', '0538726611', '3-months', 'mobile-money', '2025-03-12 12:47:46'),
(36, 'kwame quarshie', 'kwamequarshie395@gmail.com', '0508726611', '1-month', 'credit-card', '2025-03-12 13:40:07');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`) VALUES
(1, 'Emmanuel Quarshie', 'emmanuelquarshie195@outlook.com', 'Request to be a member', 'Good morning Guy', '2025-03-12 14:29:17');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `card_name` varchar(255) NOT NULL,
  `card_number` varchar(20) NOT NULL,
  `expiry_date` varchar(10) NOT NULL,
  `cvv` varchar(5) NOT NULL,
  `mobile_money_number` varchar(20) DEFAULT NULL,
  `network_provider` varchar(20) DEFAULT NULL,
  `status` enum('Pending','Approved') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `member_id`, `payment_method`, `amount`, `created_at`, `card_name`, `card_number`, `expiry_date`, `cvv`, `mobile_money_number`, `network_provider`, `status`) VALUES
(10, 24, 'mobile-money', 1200.00, '2025-03-11 13:30:26', '', '', '', '', '0538726615', 'Vodafone', 'Approved'),
(11, 25, 'mobile-money', 300.00, '2025-03-11 13:33:57', '', '', '', '', '9057485894', 'AirtelTigo', 'Approved'),
(12, 27, 'mobile-money', 600.00, '2025-03-11 14:23:48', '', '', '', '', '0598726699', 'MTN', 'Approved'),
(13, 28, 'credit-card', 100.00, '2025-03-11 14:29:41', 'Jerry', '575747444', '03/25', '543', NULL, NULL, 'Approved'),
(14, 29, 'credit-card', 300.00, '2025-03-12 12:40:46', 'Joana', '48894949993933', '02/25', '644', NULL, NULL, 'Approved'),
(15, 30, 'credit-card', 1200.00, '2025-03-12 12:43:22', 'Larry John', '47858499494443', '02/25', '899', NULL, NULL, 'Pending'),
(16, 34, 'mobile-money', 300.00, '2025-03-12 12:47:46', '', '', '', '', '0553872800', 'AirtelTigo', 'Pending'),
(17, 36, 'credit-card', 100.00, '2025-03-12 13:40:07', 'Kwame Quarshie', '1234567891234567', '02/25', '453', NULL, NULL, 'Approved');

--
-- Triggers `payments`
--
DELIMITER $$
CREATE TRIGGER `before_payment_insert` BEFORE INSERT ON `payments` FOR EACH ROW BEGIN
    DECLARE pm VARCHAR(50);
    
    -- Get payment method from members table
    SELECT payment_method INTO pm FROM members WHERE id = NEW.member_id;
    
    -- Assign the fetched payment method
    SET NEW.payment_method = pm;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_member` (`member_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payments_member` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
