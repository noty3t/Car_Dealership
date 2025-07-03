-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 03, 2025 at 10:56 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `car_dealership`
--

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

DROP TABLE IF EXISTS `brand`;
CREATE TABLE IF NOT EXISTS `brand` (
  `id` int NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`id`, `brand_name`) VALUES
(66, 'Mazda'),
(65, 'Volvo'),
(64, 'Zeekr'),
(63, 'MG'),
(62, 'BYD'),
(61, 'Jeep'),
(60, 'Lexus'),
(59, 'Nissan'),
(58, 'Kia'),
(57, 'Hyundai'),
(56, 'Porsche'),
(55, 'Tesla'),
(54, 'Chevrolet'),
(53, 'Audi'),
(52, 'Mercedes-Benz'),
(51, 'BMW'),
(50, 'Volkswagen'),
(49, 'Ford'),
(48, 'Honda'),
(47, 'Toyota');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

DROP TABLE IF EXISTS `cars`;
CREATE TABLE IF NOT EXISTS `cars` (
  `car_id` int NOT NULL AUTO_INCREMENT,
  `model_id` int DEFAULT NULL,
  `engine_id` int DEFAULT NULL,
  `color_id` int DEFAULT NULL,
  `year` int DEFAULT NULL,
  `car_condition` enum('new','used') DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `description` text,
  `image_url` varchar(255) DEFAULT NULL,
  `mileage` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`car_id`),
  KEY `model_id` (`model_id`),
  KEY `engine_id` (`engine_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `model_id`, `engine_id`, `color_id`, `year`, `car_condition`, `price`, `quantity`, `description`, `image_url`, `mileage`, `created_at`) VALUES
(7, 48, 4, 23, 2022, 'new', 7694.75, 3, 'Type: All-electric luxury performance coupe\r\nPowertrain: Dual-motor AWD\r\nPower: 469 HP (522 HP in boost mode)\r\n0-60 mph: 3.9 sec\r\nTop Speed: 152 mph (limited)\r\nBattery: 93.4 kWh (usable)\r\nRange: 238–249 mi (EPA)\r\nCharging: 270 kW max (5-80% in ~22.5 min)\r\nFeatures: Sleek design, premium interior, virtual cockpit, fast charging\r\nCompetitors: Porsche Taycan, Tesla Model S\r\nA high-performance EV with Audi’s signature luxury and tech.', '/assets/images/cars/image-removebg-preview.png', 0, '2025-07-02 14:43:38'),
(8, 49, 2, 22, 2024, 'new', 8830.00, 2, 'Type: High-performance luxury wagon\r\nEngine: 4.0L Twin-Turbo V8 (Mild Hybrid)\r\nPower: 591 HP / 590 lb-ft torque\r\n0-60 mph: 3.5 sec\r\nTop Speed: 155 mph (190 mph with Dynamic Package)\r\nTransmission: 8-speed Tiptronic (Quattro AWD)\r\nDrivetrain: All-wheel drive with torque vectoring\r\nPracticality: 30 cu-ft cargo (seats up) / 59 cu-ft (seats down)\r\nFeatures: Aggressive styling, sports suspension, premium tech, Nappa leather, Audi Virtual Cockpit\r\nCompetitors: Mercedes-AMG E63 S Wagon, BMW M5 Touring (upcoming)\r\nA brutal yet practical super wagon with V8 power and everyday usability. ', '/assets/images/cars/image-removebg-preview (1).png', 0, '2025-07-02 14:50:32'),
(10, 51, 1, 32, 2019, 'used', 5300.00, 1, 'Type: High-performance American sports car\r\nEngine: 6.2L Naturally Aspirated V8 (LT1)\r\nPower: 455 HP / 460 lb-ft torque (up to 650 HP in Z06)\r\n0-60 mph: 3.7 sec (Stingray) / ~3.0 sec (Z06)\r\nTop Speed: 180+ mph (up to 200+ mph for Z06/ZR1)\r\nTransmission: 7-speed manual or 8-speed automatic\r\nDrivetrain: Rear-wheel drive with electronic limited-slip differential\r\nPracticality: 15 cu-ft cargo space (surprisingly spacious for a sports car)\r\nFeatures: Iconic styling, lightweight aluminum frame, Magnetic Ride Control, performance exhaust, premium leather, heads-up display, Bose audio, Chevrolet Infotainment with Apple CarPlay/Android Auto\r\nCompetitors: Porsche 911 Carrera, Jaguar F-Type, Ford Mustang Shelby GT350', '/assets/images/cars/dsc04649-removebg-preview.png', 5000, '2025-07-03 07:08:49'),
(9, 34, 4, 26, 2025, 'new', 6421.25, 2, 'Type: All-electric luxury SUV\r\nPowertrain: Dual-motor AWD\r\nPower: 516 HP (iX xDrive50) / 610 HP (iX M60)\r\n0-60 mph: 4.4 sec (xDrive50) / 3.6 sec (M60)\r\nTop Speed: 124 mph (xDrive50) / 155 mph (M60)\r\nBattery: 105.2 kWh (usable)\r\nRange: 324 mi (xDrive50, EPA) / 288 mi (M60, EPA)\r\nCharging: 250 kW max (10-80% in ~35 min)\r\nFeatures: Futuristic design, ultra-lux interior, curved display, adaptive air suspension\r\nCompetitors: Audi e-tron, Mercedes EQS SUV, Tesla Model X', '/assets/images/cars/image-removebg-preview (2).png', 0, '2025-07-02 15:08:44'),
(11, 16, 2, 23, 2017, 'used', 1000.00, 3, 'Type: Subcompact hatchback (GK3)\r\nEngine: 1.3L Inline-4 (L13B)\r\nPower: ~98 HP / 89 lb-ft torque\r\n0-60 mph: ~11–12 sec (est.)\r\nTop Speed: ~110 mph (est.)\r\nTransmission: CVT or 5-speed manual (varies by market)\r\nDrivetrain: Front-wheel drive\r\nPracticality: ~16.6 cu-ft cargo (seats up) / up to 52 cu-ft (seats down, Magic Seats folded)\r\nFeatures: Ultra-versatile “Magic Seat” rear folding system, excellent fuel economy (~40–50 MPG highway), compact footprint, spacious interior, basic infotainment with optional touchscreen, Bluetooth (market dependent)\r\nCompetitors: Toyota Vitz/Yaris, Suzuki Swift, Nissan Note', '/assets/images/cars/image-removebg-preview (3).png', 13570, '2025-07-03 07:18:02'),
(12, 96, 2, 25, 2020, 'used', 2000.00, 2, 'Type: Premium mid-size luxury sedan (Hybrid)\r\nEngine: 2.5L Inline-4 + Hybrid Synergy Drive\r\nPower: ~222 HP (system combined output)\r\n0-60 mph: ~8.0 sec (est.)\r\nTop Speed: ~112 mph (electronically limited)\r\nTransmission: E-CVT automatic\r\nDrivetrain: Rear-wheel drive or available AWD (market dependent)\r\nPracticality: Comfortable 5-seater with ~16 cu-ft trunk space\r\nFeatures: Elegant styling, advanced hybrid efficiency (~40–45 MPG combined), adaptive cruise control, Toyota Safety Sense, premium interior with leather seats, large touchscreen infotainment, digital instrument cluster, multi-zone climate control\r\nCompetitors: Lexus ES300h, Nissan Fuga Hybrid, Honda Legend Hybrid', '/assets/images/cars/image-removebg-preview (4).png', 1000, '2025-07-03 07:24:33');

-- --------------------------------------------------------

--
-- Table structure for table `car_news`
--

DROP TABLE IF EXISTS `car_news`;
CREATE TABLE IF NOT EXISTS `car_news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `author_id` int NOT NULL,
  `publish_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_published` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `car_news`
--

INSERT INTO `car_news` (`id`, `title`, `content`, `image_url`, `author_id`, `publish_date`, `is_published`) VALUES
(1, 'The Ford Explorer Finally Has a Real Off-Road Trim', 'It has bigger tires, a 1.0-inch suspension lift, and underbody protection.\r\n\r\nFord continues to expand its lineup of Tremor-branded vehicles. The latest to get the treatment is the Explorer, joining the Maverick, Expedition, and F-150 in the Blue Oval’s garage. The new Explorer Tremor goes on sale for the 2026 model year; however, Ford doesn’t say when the rugged SUV will reach showrooms.\r\n\r\nThe Explorer Tremor features an off-road-tuned suspension that adds an extra inch of height, with tuned springs and sway bars, while improving the departure and approach angles. Eighteen-inch tires with Bridgestone Dueler all-terrain tires sit at the corners, while auxiliary lights, orange tow hooks, and other orange accents—a color Ford calls “electric spice”—differentiate this Explorer from the others.\r\n\r\n2026 Explorer Tremor Powertrain Options\r\nUnder the hood is Ford’s 3.0-liter EcoBoost V-6 engine, the same one powering the Explorer ST. It produces 400 horsepower and 415 pound-feet of torque, but Ford will also offer customers the 300-hp, 2.3-liter four-cylinder as an option. All-wheel drive and a Toresen limited-slip differential are standard, along with some underbody protection.\r\n\r\nInside, the new Tremor includes the Ford Digital Experience and CoPilot 360 2.0 Assist. Optional features include massaging front seats, a 14-speaker B&O sound system, and BlueCruise 1.5.\r\n\r\nFord Explorer Tremor Pricing\r\nFord hasn’t released 2026 Explorer Tremor pricing just yet. That information will likely arrive closer to its on-sale date, but we can estimate a price based on the current lineup. The 2025 Explorer ST, which currently sits at the top of the model range, starts at $56,465 (prices include the destination charge).\r\n\r\nThe Tremor could cost as much, if not more. However, the trim isn’t always the most expensive one available. The Explorer Tremor could start below the ST in the low-$50,000 range, but we’ll have to wait for official details.', 'uploads/news/1751524528_2026-ford-explorer-tremor-front-3-4.png', 1, '2025-06-11 08:03:21', 1),
(2, 'A Good Successor for RX-7: Mazda\'s New Rotary Sports Car Will Be Sold Alongside the Miata', 'We were smitten by the Iconic SP nearly two years ago when Mazda unveiled it at the Japan Mobility Show. However, once we stopped drooling over the coupe’s swoopy design, a question began to surface: Is this the next Miata? It was a fair assumption. Mazda is still a relatively small company, and perhaps it can’t afford to sell two sports cars. The Zoom-Zoom brand even suggested it could shrink the vehicle down to the MX-5’s footprint.\r\n\r\nThankfully, the Iconic SP isn’t a preview of the fifth-generation Miata. Mazda Chief Technical Officer Ryuichi Umeshita told MotorTrend the rotary sports car will be a separate model positioned above the beloved roadster. He even said we \"can expect Iconic SP will be a good successor for RX-7,\" although he stopped short of confirming the return of the iconic nameplate. He did, however, deny that it would wear the Cosmo badge as a nod to the Wankel-powered lineage that began in the mid-1960s.\r\n\r\nSo, how big is the Iconic SP anyway? It measures 164.5 inches in length, 72.8 inches in width, and stands just 45.2 inches tall, riding on a 102-inch wheelbase. That makes it 10.4 inches longer and 4.7 inches wider than the ND-generation Miata.\r\n\r\nAt the same time, it sits 3.3 inches lower. Despite having a wheelbase 11 inches longer than the tossable roadster, it still has just two seats. Well, at least the concept did. The show car was relatively light, weighing 3,197 pounds. For comparison, a Miata RF with a manual transmission is 728 pounds lighter.\r\n\r\nAs for power, the concept delivers 365 hp via an electric motor, with a dual-rotor combustion engine serving as a generator to recharge the battery. Since its debut, Mazda has confirmed plans for a dual-rotor setup in America, offering more muscle than the MX-30’s single-rotor range extender. While the crossover’s system didn’t meet US regulations, the new hardware will be made compliant.\r\n\r\nInterestingly, Mazda is also considering a version where the rotary engine directly drives the wheels. This would be the first time since the RX-8 bowed out in 2012 with the Japan-exclusive Spirit R edition. A fully electric Iconic SP is technically possible, but Mazda says it won’t happen. No surprise there.\r\n\r\nThe rotary sports car could arrive before the next Miata. Mazda’s global design chief, Masashi Nakayama, told Motor Trend that the current MX-5 will remain on sale for a few more years. When the replacement does arrive, it will inherit some styling cues from the Iconic SP. Power will probably come from the newly announced Skyactiv-Z engine, with Umeshita confirming the six-speed manual, rear-wheel drive, and lightweight construction are all here to stay.\r\n\r\nIt’s too early for Mazda to discuss pricing, but the rotary sports car will almost certainly carry a hefty premium over the MX-5. The hardtop RF currently starts at $38,735 before options, so expect the larger Iconic SP to be priced above $50,000, possibly even north of $60,000.', 'uploads/news/1751524431_mazda-iconic-sp-concept.png', 1, '2025-06-11 08:04:11', 1),
(3, 'The Jeep Cherokee Is Back', 'After a brief hiatus, Jeep’s mid-size off-roader returns for the 2026 model year.\r\n\r\nJeep discontinued the fifth-generation Cherokee in 2023 after a solid 10-year production run in the US. But its retirement was short-lived. For the 2026 model year, Jeep has an all-new Cherokee on the horizon—and these early images give us an idea of what to expect from the brand\'s next mid-size off-roader.\r\n\r\nThe 2026 Cherokee adopts the automaker\'s latest design language, first seen on the Wagoneer S, then on the new Compass that debuted in May. Up front, the Cherokee wears the brand\'s iconic seven-slot grille, while the body has a familiar, rugged shape. We can\'t see the rear in these images, but we assume it has a similar look to the Compass.', 'uploads/news/1751524310_2026-jeep-cherokee.png', 1, '2025-06-16 02:47:35', 1),
(4, '2026 Porsche 911 Carrera 4S: More Power, More Driven Wheels, More Money', 'Porsche is fleshing out the 911 lineup with new all-wheel drive variants, Carrera 4S coupe and Cabriolet, plus Targa 4S.\r\n\r\nAs reliably as the sun rises and sets, Porsche is fleshing out the 911 lineup. Following last year\'s facelift, Porsche just revealed the new all-wheel drive \"S\" variants, Carrera 4S coupe and Cabriolet, plus Targa 4S. The headline? More power and standard equipment than their predecessors, and a healthy price increase, too.\r\n\r\nLike the rear-drive Carrera S coupe and Cabriolet, the new all-wheel drive models get a 3.0-liter twin-turbo flat-six making 473 horsepower and 390 pound-feet of torque. The power increase is largely thanks to intercoolers from the 911 Turbo, Porsche says, and the horsepower figure matches the old Carrera GTS\'s, though torque is slightly lower. As with its rear-drive siblings, the only transmission available is an eight-speed PDK dual-clutch. Porsche is, so far, limiting the manual to just the purist-aimed Carrera T and GT3.\r\n\r\nThe new 4S models also get a standard Sports Exhaust and larger brakes from the old Carrera GTS model. As before, adaptive dampers, a rear limited-slip differential, and 20/21-inch wheels are standard, while sport suspension, rear-wheel steering, the Sport Chrono package, and carbon-ceramic brakes are optional. However, the Targa 4S gets rear-steer standard.', 'uploads/news/1751523702_48c64996-1751-5e89-ba24-04e043d50000.png', 1, '2025-06-27 03:42:46', 1),
(5, 'Tesla Finally Updated the Model S and Model X. But Not Much Has Changed', 'The changes are minor, but Tesla is now charging $5,000 more for both.\r\n\r\nTesla updated the Model S and Model X today, but any significant changes are hard to spot. Many of the upgrades lie hidden beneath the familiar-looking exteriors, with Tesla making several minor tweaks to the cars that fail to enhance their appeal in the increasingly competitive luxury EV segment.\r\n\r\nTesla claims the new S and X are “even quieter inside,” with less wind and road noise paired with more effective noise cancellation. The EVs will also have a smoother ride thanks to new bushings and a new suspension design, although Tesla failed to provide specifics.\r\n\r\nThere’s a new Fost Blue exterior color for the pair and a new front fascia camera for improved visibility. Inside, Tesla adds dynamic ambient lighting while increasing third-row passenger and cargo space in the Model X. Oh, and the yoke is still available as an $1,000 option on the Plaid variants.\r\n\r\nThe Model S Plaid has fresh exterior styling that Tesla says is optimized for high-speed stability, but the updated car has a lower top speed than its predecessor. It’s down from 200 miles per hour to 149 mph—a huge drop. At least it can still hit 60 miles per hour in 2.5 seconds.\r\n\r\nTesla also has new wheel designs for the sedan and SUV, which improve aerodynamics and increase range.\r\n\r\nTesla Model S and Model X Range Gains\r\nThe updates have resulted in Tesla creating its longest-range EV ever—the Model S Long Range. The sedan can travel up to 410 miles on a charge, according to the company, while the Model S Plaid improves to 368 miles. The Model X can now go up to 38 miles more than before—352—while the Plaid gets a 21-mile bump in range to 335.\r\n\r\nMild Updates, Fresh New Price\r\nDespite the mediocre enhancements to the updated Model S and Model X, Tesla is now charging $5,000 extra across both trims of each car. The Model S now costs $86,630, while the Model S Plaid starts at $101,630. If you want the crossover, you’ll pay $91,630 for the Model X with all-wheel drive and $106,630 for the high-performance Plaid variant.', 'uploads/news/1751524828_gtsrlp3amaagnlr.png', 1, '2025-07-03 06:40:28', 1);

-- --------------------------------------------------------

--
-- Table structure for table `color`
--

DROP TABLE IF EXISTS `color`;
CREATE TABLE IF NOT EXISTS `color` (
  `id` int NOT NULL AUTO_INCREMENT,
  `color` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `color`
--

INSERT INTO `color` (`id`, `color`) VALUES
(32, 'Beige'),
(31, 'Brown'),
(30, 'Orange'),
(29, 'Yellow'),
(28, 'Green'),
(27, 'Blue'),
(26, 'Red'),
(25, 'Gray'),
(24, 'Silver'),
(23, 'White'),
(22, 'Black'),
(33, 'Gold'),
(34, 'Champagne'),
(35, 'Navy Blue'),
(36, 'Burgundy');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE IF NOT EXISTS `contacts` (
  `contact_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `car_id` int DEFAULT NULL,
  `contact_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `admin_notes` text,
  PRIMARY KEY (`contact_id`),
  KEY `car_id` (`car_id`)
) ENGINE=MyISAM AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`contact_id`, `name`, `email`, `phone`, `subject`, `message`, `car_id`, `contact_date`, `admin_notes`) VALUES
(59, 'thaunghan11', 'thaunghan11@gmail.com', '+9509751971090', 'Inquiry about BMW iX', 'I\'m interested in the BMW iX (ID: 9). Please contact me with more information.', 9, '2025-07-03 03:27:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `engine_type`
--

DROP TABLE IF EXISTS `engine_type`;
CREATE TABLE IF NOT EXISTS `engine_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `engine_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `engine_type`
--

INSERT INTO `engine_type` (`id`, `engine_name`) VALUES
(1, 'Gasoline'),
(2, 'Hybrid'),
(3, 'Diesel'),
(4, 'Electric');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `feedback_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `feedback_title` varchar(255) NOT NULL,
  `feedback_desc` text NOT NULL,
  `feedback_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`feedback_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `feedback_title`, `feedback_desc`, `feedback_date`) VALUES
(1, 2, 'Its good Service', 'asdfsadf', '2025-06-11 04:20:29'),
(2, 2, 'Its good Service', 'asdfsadf', '2025-06-11 04:20:54'),
(3, 2, 'Its good Service', 'afsadfsadf', '2025-06-11 04:21:55'),
(4, 2, 'Its good Service', 'asdfasdf', '2025-06-11 04:26:47'),
(5, 2, 'Its good Serviceafd', 'asdfasdf', '2025-06-11 04:28:22'),
(6, 2, 'Its good Service', 'asfasdf', '2025-06-12 03:49:18');

-- --------------------------------------------------------

--
-- Table structure for table `model`
--

DROP TABLE IF EXISTS `model`;
CREATE TABLE IF NOT EXISTS `model` (
  `id` int NOT NULL AUTO_INCREMENT,
  `model_name` varchar(50) DEFAULT NULL,
  `brand_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`)
) ENGINE=MyISAM AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `model`
--

INSERT INTO `model` (`id`, `model_name`, `brand_id`) VALUES
(1, 'Corolla', 47),
(2, 'Camry', 47),
(3, 'RAV4', 47),
(4, 'Hilux', 47),
(5, 'Land Cruiser', 47),
(6, 'Prius', 47),
(7, 'Supra', 47),
(8, 'Tacoma', 47),
(9, 'Tundra', 47),
(10, 'Highlander', 47),
(11, 'Civic', 48),
(12, 'Accord', 48),
(13, 'CR-V', 48),
(14, 'HR-V', 48),
(15, 'City', 48),
(16, 'Jazz/Fit', 48),
(17, 'NSX', 48),
(18, 'F-150', 49),
(19, 'Mustang', 49),
(20, 'Ranger', 49),
(21, 'Bronco', 49),
(22, 'Explorer', 49),
(23, 'GT', 49),
(24, 'Golf', 50),
(25, 'Tiguan', 50),
(26, 'Passat', 50),
(27, 'ID.4', 50),
(28, 'ID. Buzz', 50),
(29, '3 Series', 51),
(30, '5 Series', 51),
(31, 'X5', 51),
(32, 'X3', 51),
(33, 'i4', 51),
(34, 'iX', 51),
(35, 'M3', 51),
(36, 'M5', 51),
(37, 'C-Class', 52),
(38, 'E-Class', 52),
(39, 'S-Class', 52),
(40, 'GLE', 52),
(41, 'G-Class', 52),
(42, 'AMG GT', 52),
(43, 'EQS', 52),
(44, 'A4', 53),
(45, 'A6', 53),
(46, 'Q5', 53),
(47, 'Q7', 53),
(48, 'e-tron GT', 53),
(49, 'RS6 Avant', 53),
(50, 'Silverado', 54),
(51, 'Corvette', 54),
(52, 'Tahoe', 54),
(53, 'Camaro', 54),
(54, 'Suburban', 54),
(55, 'Model 3', 55),
(56, 'Model Y', 55),
(57, 'Model S', 55),
(58, 'Cybertruck', 55),
(59, '911', 56),
(60, 'Cayenne', 56),
(61, 'Taycan', 56),
(62, 'Macan', 56),
(63, 'Tucson', 57),
(64, 'Santa Fe', 57),
(65, 'Palisade', 57),
(66, 'Ioniq 5', 57),
(67, 'Sportage', 58),
(68, 'Sorento', 58),
(69, 'Telluride', 58),
(70, 'EV6', 58),
(71, 'Rogue', 59),
(72, 'Altima', 59),
(73, 'Z', 59),
(74, 'Leaf', 59),
(75, 'RX', 60),
(76, 'NX', 60),
(77, 'ES', 60),
(78, 'LC', 60),
(79, 'LX', 60),
(80, 'Wrangler', 61),
(81, 'Grand Cherokee', 61),
(82, 'Cherokee', 61),
(83, 'Han', 62),
(84, 'Atto 3', 62),
(85, 'Seal', 62),
(86, 'MG 4', 63),
(87, 'MG ZS', 63),
(88, 'Zeekr 001', 64),
(89, 'Zeekr X', 64),
(90, 'XC60', 65),
(91, 'XC90', 65),
(92, 'EX90', 65),
(93, 'CX-5', 66),
(94, 'Mazda3', 66),
(95, 'MX-5 Miata', 66),
(96, 'Crown', 47);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `car_id` int NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `delivery_location` varchar(255) DEFAULT NULL,
  `delivery_lat` decimal(10,8) DEFAULT NULL,
  `delivery_lng` decimal(11,8) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `car_id` (`car_id`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `car_id`, `order_date`, `status`, `delivery_location`, `delivery_lat`, `delivery_lng`) VALUES
(54, 2, 7, '2025-07-03 08:31:27', 'Processing', 'Area of Institute of Sports and Physical Education, 105th Street (Tharaphi Rd), Chanmyathazi Township, Mahaaungmyay District, Mandalay, Mandalay City, Mandalay, 05041, Myanmar', 21.94800640, 96.09871360);

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
CREATE TABLE IF NOT EXISTS `transaction` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `car_id` int DEFAULT NULL,
  `transaction_code` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','verified','rejected') DEFAULT 'pending',
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`transaction_id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `car_id` (`car_id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `order_id`, `user_id`, `car_id`, `transaction_code`, `payment_method`, `amount`, `status`, `image_url`, `created_at`) VALUES
(31, 54, 2, 7, '41851248300939201', 'Bank Transfer', 7694.75, 'pending', '/assets/images/receipts/receipt_1751531552_e5c45635.jpg', '2025-07-03 08:32:32'),
(30, 53, 2, 7, '2134213423141234', 'Bank Transfer', 7694.75, 'pending', '/assets/images/receipts/receipt_1751531412_0face3f8.png', '2025-07-03 08:30:12'),
(29, 52, 2, 7, '35234532453245', 'Bank Transfer', 7694.75, 'pending', '/assets/uploads/receipts/receipt_1751531261_91e81511.png', '2025-07-03 08:27:41'),
(28, 51, 2, 7, '2134213423141234', 'Bank Transfer', 7694.75, 'pending', '/assets/uploads/receipts/receipt_1751529991_a33a1f57.png', '2025-07-03 08:06:31'),
(27, 49, 2, 7, '2134213423141234', 'Bank Transfer', 7694.75, 'pending', '/assets/uploads/receipts/receipt_1751528925_d18e6480.png', '2025-07-03 07:48:45'),
(26, 48, 2, 8, '41851248300939201', 'Bank Transfer', 8830.00, 'pending', '/assets/uploads/receipts/receipt_1751525825_7a78a01b.jpg', '2025-07-03 06:57:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `ph_no` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `role` enum('admin','client') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `ph_no`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$UacROS64B6IyquT.fGNRO..LEOqx58n.1Z9VXJlQtXLnxzFkAgzM.', 'admin@example.com', '', 'admin', '2025-05-28 03:42:41'),
(2, 'thaunghan11', '$2y$10$nwm7tTe7R5FhXdowBPCV7OYIHyDjIALXDlSQAIR/hecqO5dlFcSSq', 'thaunghan11@gmail.com', '09751971090', 'client', '2025-05-28 06:54:17');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
