-- SQL statements for creating the initial tables
-- Note: Progress OpenEdge might have specific syntax for auto-incrementing fields or default timestamps.
-- Consult your Progress DB documentation if these generic statements cause issues.

-- Users table for authentication (PostgreSQL syntax)
CREATE TABLE users (
    id SERIAL PRIMARY KEY, -- SERIAL is an auto-incrementing integer in PostgreSQL. For Progress, a SEQUENCE might be needed, e.g., CREATE SEQUENCE users_id_seq; id INTEGER DEFAULT NEXT-VALUE(users_id_seq)
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Site texts table for editable content snippets
CREATE TABLE site_texts (
    text_id VARCHAR(255) PRIMARY KEY NOT NULL,
    text_content TEXT, -- In Progress, CLOB might be more appropriate for very long texts
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Consider a trigger if automatic update on modification is needed
);

-- Example of how to add a unique constraint explicitly if not done inline
-- ALTER TABLE users ADD CONSTRAINT unique_username UNIQUE (username);

-- Comments on Progress OpenEdge specifics:
-- 1. Auto-increment: Progress typically uses sequences. You might need to:
--    CREATE SEQUENCE user_id_seq;
--    And then define the 'id' column in 'users' as: id INTEGER DEFAULT NEXT VALUE FOR user_id_seq
-- 2. Default Timestamps: CURRENT_TIMESTAMP is fairly standard.
-- 3. TEXT data type: Progress SQL might use CLOB for large text objects. TEXT is often an alias or suitable for moderately sized strings.
-- 4. Primary Key: The `PRIMARY KEY` constraint inline is standard.
-- 5. Unique Constraint: The `UNIQUE` constraint inline is standard.
