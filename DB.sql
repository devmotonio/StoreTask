-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2021 at 10:19 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `store`
--

-- --------------------------------------------------------

--
-- Table structure for table `orderitems`
--

CREATE TABLE `orderitems` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) UNSIGNED NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `stock` int(11) NOT NULL,
  `sku` char(6) NOT NULL,
  `arrive_stock` int(11) NOT NULL,
  `arrive_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `productview`
-- (See below for the actual view)
--
CREATE TABLE `productview` (
`id` int(11) unsigned
,`stock` int(11)
,`sku` char(6)
,`arrive_stock` int(11)
,`arrive_date` date
,`products_sold` decimal(32,0)
,`avg_in_all_orders` decimal(39,4)
,`avg_in_orders_by_product` decimal(39,4)
);

-- --------------------------------------------------------

--
-- Structure for view `productview`
--
DROP TABLE IF EXISTS `productview`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `productview`  AS SELECT `products`.`id` AS `id`, `products`.`stock` AS `stock`, `products`.`sku` AS `sku`, `products`.`arrive_stock` AS `arrive_stock`, `products`.`arrive_date` AS `arrive_date`, `t_product_sum`.`product_sum` AS `products_sold`, `t_product_sum`.`product_sum`/ `t_aux`.`order_sum` * 100 AS `avg_in_all_orders`, `t_product_sum`.`product_sum`/ `t_count_by_order`.`order_product_sum` * 100 AS `avg_in_orders_by_product` FROM (((`products` join (select sum(`orderitems`.`qty`) AS `order_sum` from `orderitems`) `t_aux`) left join (select `orderitems`.`product_id` AS `product_id`,sum(`orderitems`.`qty`) AS `product_sum` from `orderitems` group by `orderitems`.`product_id`) `t_product_sum` on(`products`.`id` = `t_product_sum`.`product_id`)) left join (select `orderitems`.`product_id` AS `product_id`,sum(`ti_group`.`order_qty`) AS `order_product_sum` from (`orderitems` join (select `orderitems`.`order_id` AS `in_order_id`,sum(`orderitems`.`qty`) AS `order_qty` from `orderitems` group by `orderitems`.`order_id`) `ti_group` on(`orderitems`.`order_id` = `ti_group`.`in_order_id`)) group by `orderitems`.`product_id`) `t_count_by_order` on(`products`.`id` = `t_count_by_order`.`product_id`)) GROUP BY `products`.`id` ORDER BY `t_product_sum`.`product_sum` DESC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `double_key` (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orderitems`
--
ALTER TABLE `orderitems`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orderitems`
--
ALTER TABLE `orderitems`
  ADD CONSTRAINT `orderitems_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `orderitems_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
