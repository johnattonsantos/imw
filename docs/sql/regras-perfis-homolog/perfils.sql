
CREATE TABLE `perfils` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nome` varchar(100) NOT NULL,
  `nivel` varchar(191) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



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


ALTER TABLE `perfils`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `perfils`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

