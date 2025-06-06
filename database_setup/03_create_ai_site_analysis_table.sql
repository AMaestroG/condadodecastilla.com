-- database_setup/03_create_ai_site_analysis_table.sql

CREATE TABLE ai_site_thematic_analysis (
    analysis_id INTEGER PRIMARY KEY AUTOINCREMENT, -- Standard SQLite, adapt for Progress (e.g., SERIAL or SEQUENCE)
    analyzed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    identified_themes_json TEXT, -- JSON array of common themes
    identified_entities_json TEXT, -- JSON object categorizing common entities (people, places, events)
    overall_style_summary TEXT, -- AI's description of the site's writing style
    processed_text_ids_json TEXT, -- JSON array of text_ids that were included in this analysis run
    token_count_processed INTEGER -- Optional: store total tokens processed for cost tracking
);

-- Typically, this table will only have one active row, storing the latest full-site analysis.
-- Old analyses could be kept for history if analysis_id wasn't auto-incrementing and we used a fixed ID.
-- For now, new run = new row. A cleanup mechanism could be added later if needed.
