START TRANSACTION;

-- 1) Ajuste estes valores
SET @email_usuario = 'seuusuario@dominio.com';
SET @regiao_id = 13; -- ID da região do usuário (instituição regional)

-- 2) Resolve IDs
SET @user_id = (
  SELECT id FROM users WHERE email = @email_usuario LIMIT 1
);

SET @perfil_admin_sistema_id = (
  SELECT id FROM perfils WHERE LOWER(nome) = 'administrador_sistema' LIMIT 1
);

SET @perfil_crie_id = (
  SELECT id FROM perfils WHERE LOWER(nome) = 'crie' LIMIT 1
);

-- 3) Define vínculo regional do usuário
UPDATE users
SET regiao_id = @regiao_id
WHERE id = @user_id;

-- 4) Vincula perfil Administrador do Sistema na região (evita duplicidade)
INSERT INTO perfil_user (user_id, perfil_id, instituicao_id, created_at, updated_at)
SELECT @user_id, @perfil_admin_sistema_id, @regiao_id, NOW(), NOW()
WHERE @user_id IS NOT NULL
  AND @perfil_admin_sistema_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1
    FROM perfil_user pu
    WHERE pu.user_id = @user_id
      AND pu.perfil_id = @perfil_admin_sistema_id
      AND pu.instituicao_id = @regiao_id
  );

-- 5) Vincula perfil CRIE na região (evita duplicidade)
INSERT INTO perfil_user (user_id, perfil_id, instituicao_id, created_at, updated_at)
SELECT @user_id, @perfil_crie_id, @regiao_id, NOW(), NOW()
WHERE @user_id IS NOT NULL
  AND @perfil_crie_id IS NOT NULL
  AND NOT EXISTS (
    SELECT 1
    FROM perfil_user pu
    WHERE pu.user_id = @user_id
      AND pu.perfil_id = @perfil_crie_id
      AND pu.instituicao_id = @regiao_id
  );

COMMIT;

-- Conferência
SELECT
  u.id,
  u.name,
  u.email,
  u.regiao_id,
  p.nome AS perfil,
  i.nome AS instituicao
FROM users u
JOIN perfil_user pu ON pu.user_id = u.id
JOIN perfils p ON p.id = pu.perfil_id
JOIN instituicoes_instituicoes i ON i.id = pu.instituicao_id
WHERE u.email = @email_usuario
ORDER BY p.nome, i.nome;
