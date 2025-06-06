-- database_setup/02_create_ai_evaluations_table.sql

CREATE TABLE ai_content_evaluations (
    eval_id INTEGER PRIMARY KEY AUTOINCREMENT, -- SQLite style, adaptable for other DBs
    text_id_fk VARCHAR(255) NOT NULL,
    evaluated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    evaluation_prompt TEXT, -- Store the exact prompt used for this evaluation
    raw_ai_response TEXT,   -- Store the full raw response from AI

    -- Specific criteria scores (scale of 1-10, or 1-5)
    -- These are examples; actual criteria will depend on prompting
    clarity_score INTEGER,
    engagement_score INTEGER,
    seo_score INTEGER, -- (If applicable/measurable by AI)
    factual_accuracy_score INTEGER, -- (Experimental, requires careful prompting)

    -- Qualitative feedback
    overall_assessment TEXT,
    positive_points TEXT, -- What the AI liked (can be JSON array stored as text)
    areas_for_improvement TEXT, -- Specific suggestions (can be JSON array stored as text)
    suggested_keywords TEXT, -- Comma-separated if AI provides them

    CONSTRAINT fk_text_id
        FOREIGN KEY(text_id_fk)
        REFERENCES site_texts(text_id)
        ON DELETE CASCADE -- If a text is deleted, its evaluations are also deleted
);

-- Optional: Add indexes for faster querying, e.g., on text_id_fk
CREATE INDEX idx_ai_eval_text_id_fk ON ai_content_evaluations(text_id_fk);
CREATE INDEX idx_ai_eval_evaluated_at ON ai_content_evaluations(evaluated_at);
