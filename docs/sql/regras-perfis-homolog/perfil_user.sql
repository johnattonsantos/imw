-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 27/05/2026 às 18:15
-- Versão do servidor: 10.11.13-MariaDB-0ubuntu0.24.04.1
-- Versão do PHP: 8.3.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `imwpgahml`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil_user`
--

CREATE TABLE `perfil_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `perfil_id` bigint(20) UNSIGNED NOT NULL,
  `instituicao_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfil_user`
--

INSERT INTO `perfil_user` (`id`, `user_id`, `perfil_id`, `instituicao_id`, `created_at`, `updated_at`) VALUES
(17, 8, 6, 2266, '2024-04-18 02:48:26', '2024-04-18 02:48:26'),
(18, 8, 4, 1897, '2024-04-18 02:48:26', '2024-04-18 02:48:26'),
(19, 8, 5, 1941, '2024-04-18 02:48:26', '2024-04-18 02:48:26'),
(26, 4, 6, 1897, '2024-04-29 07:11:55', '2024-04-29 07:11:55'),
(31, 9, 4, 1907, '2024-05-07 05:52:49', '2024-05-07 05:52:49'),
(50, 10, 7, 2233, '2024-05-12 03:07:00', '2024-05-12 03:07:00'),
(55, 12, 5, 2734, '2024-05-21 14:15:02', '2024-05-21 14:15:02'),
(56, 13, 5, 2280, '2024-05-21 14:16:14', '2024-05-21 14:16:14'),
(57, 14, 5, 2004, '2024-05-21 14:17:19', '2024-05-21 14:17:19'),
(58, 15, 5, 2073, '2024-05-21 14:18:27', '2024-05-21 14:18:27'),
(72, 17, 5, 2275, '2024-05-25 22:06:06', '2024-05-25 22:06:06'),
(80, 18, 1, 2268, '2024-06-08 04:01:23', '2024-06-08 04:01:23'),
(81, 16, 1, 2275, '2024-06-08 16:15:30', '2024-06-08 16:15:30'),
(101, 19, 1, 1917, '2024-06-10 15:49:22', '2024-06-10 15:49:22'),
(102, 20, 1, 2275, '2024-06-10 16:02:11', '2024-06-10 16:02:11'),
(103, 22, 1, 1979, '2024-06-10 16:09:36', '2024-06-10 16:09:36'),
(104, 21, 1, 2215, '2024-06-10 16:12:51', '2024-06-10 16:12:51'),
(105, 23, 1, 2233, '2024-06-10 16:42:44', '2024-06-10 16:42:44'),
(106, 24, 1, 2233, '2024-06-10 16:44:49', '2024-06-10 16:44:49'),
(127, 8, 1, 1917, '2024-07-03 06:16:26', '2024-07-03 06:16:26'),
(136, 25, 1, 962, '2024-07-13 04:17:20', '2024-07-13 04:17:20'),
(170, 26, 1, 2215, '2024-07-25 19:33:52', '2024-07-25 19:33:52'),
(205, 8, 3, 23, '2024-08-18 09:36:22', '2024-08-18 09:36:22'),
(218, 27, 3, 23, '2024-10-29 15:15:41', '2024-10-29 15:15:41'),
(222, 30, 7, 2252, '2024-12-12 03:46:35', '2024-12-12 03:46:35'),
(224, 28, 2, 1758, '2024-12-12 03:52:21', '2024-12-12 03:52:21'),
(225, 31, 11, 2916, '2025-01-15 06:07:44', '2025-01-15 06:07:44'),
(226, 32, 11, 2913, '2025-01-15 06:08:38', '2025-01-15 06:08:38'),
(236, 2, 3, 23, '2025-02-01 18:49:08', '2025-02-01 18:49:08'),
(325, 29, 7, 2262, '2026-01-07 02:03:25', '2026-01-07 02:03:25'),
(370, 36, 7, 2262, '2026-03-27 22:11:07', '2026-03-27 22:11:07'),
(385, 1, 6, 2215, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(386, 1, 1, 63, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(387, 1, 1, 2212, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(388, 1, 2, 1758, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(389, 1, 11, 86, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(390, 1, 4, 2356, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(391, 1, 3, 23, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(392, 1, 1, 1963, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(393, 1, 7, 2262, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(394, 1, 7, 1963, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(395, 1, 7, 2215, '2026-05-06 23:32:38', '2026-05-06 23:32:38'),
(396, 1, 7, 2587, '2026-05-06 23:32:38', '2026-05-06 23:32:38');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `perfil_user`
--
ALTER TABLE `perfil_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perfil_user_user_id_foreign` (`user_id`),
  ADD KEY `perfil_user_perfil_id_foreign` (`perfil_id`),
  ADD KEY `perfil_user_instituicao_id_foreign` (`instituicao_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `perfil_user`
--
ALTER TABLE `perfil_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=398;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `perfil_user`
--
ALTER TABLE `perfil_user`
  ADD CONSTRAINT `perfil_user_instituicao_id_foreign` FOREIGN KEY (`instituicao_id`) REFERENCES `instituicoes_instituicoes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `perfil_user_perfil_id_foreign` FOREIGN KEY (`perfil_id`) REFERENCES `perfils` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `perfil_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
