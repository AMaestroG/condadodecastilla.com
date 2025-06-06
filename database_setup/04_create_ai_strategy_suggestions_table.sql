-- database_setup/04_create_ai_strategy_suggestions_table.sql
CREATE TABLE ai_content_strategy_suggestions (
    suggestion_id INTEGER PRIMARY KEY AUTOINCREMENT, -- Adapt for Progress DB
    suggestion_type VARCHAR(50) NOT NULL, -- 'gap', 'underdeveloped', 'interlink', 'new_theme_expansion'
    description_text TEXT NOT NULL, -- AI-generated description of the suggestion
    related_text_id_fk VARCHAR(255) NULL, -- FK to site_texts if suggestion relates to existing text
    related_text_id_fk2 VARCHAR(255) NULL, -- For interlinking, the second text_id
    suggested_new_topic VARCHAR(255) NULL, -- If type is 'gap' or 'new_theme_expansion'
    priority INTEGER DEFAULT 5, -- Optional: AI-assigned priority (1-10)
    status VARCHAR(20) DEFAULT 'new', -- 'new', 'viewed', 'accepted', 'dismissed'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    source_analysis_id_fk INTEGER NULL, -- FK to ai_site_thematic_analysis

    CONSTRAINT fk_related_text_id
        FOREIGN KEY(related_text_id_fk)
        REFERENCES site_texts(text_id)
        ON DELETE SET NULL, -- If related text is deleted, keep suggestion but nullify FK
    CONSTRAINT fk_related_text_id2
        FOREIGN KEY(related_text_id_fk2)
        REFERENCES site_texts(text_id)
        ON DELETE SET NULL,
    CONSTRAINT fk_source_analysis_id
        FOREIGN KEY(source_analysis_id_fk)
        REFERENCES ai_site_thematic_analysis(analysis_id)
        ON DELETE SET NULL
);
CREATE INDEX idx_ai_suggestion_type ON ai_content_strategy_suggestions(suggestion_type);
CREATE INDEX idx_ai_suggestion_status ON ai_content_strategy_suggestions(status);
