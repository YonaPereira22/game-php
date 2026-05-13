-- Migración 07: Insertar juego Simulador de Redes (NetSim Academy)
-- Autor del juego: Domingo Perez (admin)
-- Fecha: 2026-05-13

INSERT INTO `games` (`title`, `description`, `author`, `folder_name`, `category`, `age_group`, `created_at`, `approved`, `total_votes`, `average_rating`, `github_link`)
VALUES (
    'NetSim Academy',
    'Simulador educativo de redes de computadoras. Explorá componentes de hardware, ensamblado de PCs, redes locales e Internet mediante misiones interactivas y un quiz final.',
    'Domingo Perez',
    'simulador-redes',
    'Redes',
    '13-16 años',
    '2026-05-13 00:00:00',
    1,
    0,
    0.00,
    NULL
);
