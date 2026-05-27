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
-- Estrutura para tabela `perfils`
--

CREATE TABLE `perfils` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `nivel` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `perfils`
--

INSERT INTO `perfils` (`id`, `nome`, `nivel`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Administrador', 'I', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(2, 'Administrador Distrito', 'D', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(3, 'Administrador Região', 'R', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(4, 'Secretario', 'I', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(5, 'Tesoureiro', 'I', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(6, 'Administrador do Sistema', 'S', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(7, 'Pastor', 'I', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(8, 'Tesoureiro Distrito', 'D', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(10, 'Secretário(a) Região', 'R', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(11, 'Tesoureiro Região', 'R', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(12, 'Estatísticas', 'R', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(13, 'Administrador SRA', 'R', '2025-07-22 03:34:47', '2025-07-22 03:34:47', NULL),
(14, 'Lider GCEU', 'I', '2026-02-25 15:35:00', '2026-02-25 15:35:00', NULL),
(15, 'Secretário GCEU', 'I', '2026-02-25 15:35:34', '2026-02-25 15:35:34', NULL),
(16, 'Membresia Validação', 'I', '2026-03-14 22:24:56', '2026-03-14 22:24:56', NULL),
(23, 'crie', 'R', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(24, 'Secretaria da EBD', 'I', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(25, 'Superintendente da EBD', 'I', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL),
(99, 'Inativo', 'I', '2025-04-07 16:41:25', '2025-04-07 16:41:25', NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `perfils`
--
ALTER TABLE `perfils`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `perfils`
--
ALTER TABLE `perfils`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
