-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 18, 2026 at 11:53 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sabor_brasil`
--

-- --------------------------------------------------------

--
-- Table structure for table `favourites`
--

CREATE TABLE `favourites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meal_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meals`
--

CREATE TABLE `meals` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `origin` varchar(100) DEFAULT 'Brazil',
  `prep_time` varchar(50) DEFAULT NULL,
  `serves` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meals`
--

INSERT INTO `meals` (`id`, `title`, `category`, `description`, `image`, `origin`, `prep_time`, `serves`, `created_at`) VALUES
(1, 'Feijoada', 'Main Course', 'Traditional Brazilian black bean stew with pork, beef, and rice.', 'feijoada-completa.jpeg', 'Brazil', '3 hours', '6', '2026-07-08 04:32:27'),
(2, 'Moqueca', 'Main Course', 'A Brazilian seafood stew made with coconut milk, tomatoes, and peppers.', 'moqueca-completa.jpeg', 'Brazil', '1 hour', '4', '2026-07-08 04:32:27'),
(3, 'Brigadeiro', 'Dessert', 'Popular Brazilian chocolate truffle made with condensed milk and cocoa.', 'brigadeiro.jpeg', 'Brazil', '30 minutes', '12', '2026-07-08 04:32:27'),
(4, 'Pudim', 'Dessert', 'Creamy Brazilian caramel flan dessert.', 'pudim.jpeg', 'Brazil', '2 hours', '8', '2026-07-08 04:32:27'),
(5, 'Caipirinha', 'Drink', 'Brazil’s famous cocktail made with lime, sugar, and cachaça.', 'caipirinha.jpeg', 'Brazil', '10 minutes', '1', '2026-07-08 04:32:27'),
(6, 'Guaraná', 'Drink', 'Sweet and refreshing Brazilian soft drink made from guaraná fruit.', 'guarana.jpeg', 'Brazil', '5 minutes', '1', '2026-07-08 04:32:27'),
(7, 'Coxinha', 'Street Food', 'Crispy chicken-filled snack shaped like a teardrop.', 'coxinha.jpeg', 'Brazil', '45 minutes', '6', '2026-07-08 04:32:27'),
(8, 'Pão de Queijo', 'Snack', 'Cheesy Brazilian bread rolls made with cassava flour.', 'paodequeijo.jpeg', 'Brazil', '40 minutes', '10', '2026-07-08 04:32:27'),
(9, 'Picanha', 'BBQ', 'Traditional Brazilian grilled beef cut, typically served with rice, farofa and vinaigrette.', 'picanha.jpeg', 'Brazil', '1 hour', '4', '2026-07-13 10:42:01');

-- --------------------------------------------------------

--
-- Table structure for table `meal_images`
--

CREATE TABLE `meal_images` (
  `id` int(11) NOT NULL,
  `meal_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `meal_images`
--

INSERT INTO `meal_images` (`id`, `meal_id`, `image`) VALUES
(1, 1, 'feijoada.jpeg'),
(2, 1, 'couve.jpeg'),
(3, 1, 'farofa.jpeg'),
(4, 1, 'arroz.jpeg'),
(5, 1, 'laranja.jpeg'),
(6, 1, 'feijoada-completa.jpeg'),
(7, 2, 'moqueca.jpeg'),
(8, 2, 'arroz.jpeg'),
(9, 2, 'pirao.jpeg'),
(11, 2, 'limao.jpeg'),
(12, 2, 'moqueca-completa.jpeg'),
(13, 9, 'picanha.jpeg'),
(14, 9, 'picanha2.jpeg'),
(15, 9, 'farofa.jpeg'),
(16, 9, 'vinagrete.jpeg'),
(22, 3, 'brigadeiro2.jpeg'),
(23, 9, 'arroz.jpeg'),
(24, 4, 'pudim2.jpeg'),
(25, 5, 'caipirinha2.jpeg'),
(26, 6, 'guarana1.jpeg'),
(27, 7, 'coxinha2.jpeg'),
(28, 7, 'coxinha3.jpeg'),
(29, 7, 'coxinha4.jpeg'),
(30, 8, 'paodequeijo2.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meal_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Francine', 'francine@email.com', '$2y$10$w0D7Ri5ezu4rXZovQ7CUo.12iYfGpVh1GC3xyGcHjcKNiNIvwdhcy', '2026-07-08 08:53:09'),
(2, 'Andre', '15182@ait.nsw.edu.au', '$2y$10$/p1AdZtnZls9QaycxIawv.WKt47zygTdAUkH2Z5EvGjEsYEl71AIm', '2026-07-18 09:50:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_favourite` (`user_id`,`meal_id`),
  ADD KEY `fk_favourites_meal` (`meal_id`);

--
-- Indexes for table `meals`
--
ALTER TABLE `meals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meal_images`
--
ALTER TABLE `meal_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `meal_id` (`meal_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_user` (`user_id`),
  ADD KEY `fk_reviews_meal` (`meal_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `favourites`
--
ALTER TABLE `favourites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meals`
--
ALTER TABLE `meals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `meal_images`
--
ALTER TABLE `meal_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favourites`
--
ALTER TABLE `favourites`
  ADD CONSTRAINT `fk_favourites_meal` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_favourites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meal_images`
--
ALTER TABLE `meal_images`
  ADD CONSTRAINT `meal_images_ibfk_1` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_meal` FOREIGN KEY (`meal_id`) REFERENCES `meals` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
