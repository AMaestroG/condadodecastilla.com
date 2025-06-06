-- Optional seed data for museo_piezas
-- This script inserts a couple of example rows that reference
-- images included in the repository under assets/img.

INSERT INTO museo_piezas (titulo, descripcion, autor, imagen_nombre)
VALUES
    ('Espada Medieval', 'Réplica de una espada de la Edad Media', 'Anónimo', '30832168158_2671f1b853_c.jpg'),
    ('Mapa del Alfoz', 'Mapa histórico de la comarca de Cerezo', 'Archivo Municipal', 'AlfozCerasioLantaron.jpg');
