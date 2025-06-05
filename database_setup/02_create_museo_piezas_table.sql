-- SQL script for creating the museo_piezas table

-- SERIAL is a PostgreSQL specific type for auto-incrementing integers.
-- For Progress OpenEdge, a SEQUENCE would typically be used:
-- CREATE SEQUENCE museo_piezas_id_seq;
-- CREATE TABLE museo_piezas (
--     id INTEGER DEFAULT NEXT-VALUE(museo_piezas_id_seq) PRIMARY KEY,
--     ...
-- );
CREATE TABLE museo_piezas (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    -- TEXT type is generally compatible. In Progress OpenEdge, CLOB or VARCHAR might be used for very large texts.
    descripcion TEXT,
    autor VARCHAR(255),
    imagen_nombre VARCHAR(255) NOT NULL, -- Stores the filename of the image
    fecha_subida TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- TEXT type, see notes above for descripcion.
    notas_adicionales TEXT NULL,
    -- DECIMAL/NUMERIC types are generally compatible.
    -- Progress OpenEdge uses DECIMAL or NUMERIC.
    pos_x DECIMAL(10, 2) NULL, -- For 3D position
    pos_y DECIMAL(10, 2) NULL, -- For 3D position
    pos_z DECIMAL(10, 2) NULL, -- For 3D position
    escala DECIMAL(5, 2) NULL DEFAULT 1.0, -- For 3D scale
    rotacion_y DECIMAL(5, 2) NULL DEFAULT 0.0 -- For 3D Y-axis rotation
);

COMMENT ON COLUMN museo_piezas.id IS 'Auto-incrementing primary key';
COMMENT ON COLUMN museo_piezas.titulo IS 'Title of the museum piece';
COMMENT ON COLUMN museo_piezas.descripcion IS 'Detailed description of the museum piece';
COMMENT ON COLUMN museo_piezas.autor IS 'Author or creator of the museum piece';
COMMENT ON COLUMN museo_piezas.imagen_nombre IS 'Filename of the representative image for the piece';
COMMENT ON COLUMN museo_piezas.fecha_subida IS 'Timestamp of when the piece was added to the database';
COMMENT ON COLUMN museo_piezas.notas_adicionales IS 'Any additional notes or context for the piece';
COMMENT ON COLUMN museo_piezas.pos_x IS 'X-coordinate for 3D positioning in a virtual space';
COMMENT ON COLUMN museo_piezas.pos_y IS 'Y-coordinate for 3D positioning in a virtual space';
COMMENT ON COLUMN museo_piezas.pos_z IS 'Z-coordinate for 3D positioning in a virtual space';
COMMENT ON COLUMN museo_piezas.escala IS 'Scale factor for 3D representation (default 1.0)';
COMMENT ON COLUMN museo_piezas.rotacion_y IS 'Rotation around the Y-axis for 3D representation (default 0.0 degrees)';
