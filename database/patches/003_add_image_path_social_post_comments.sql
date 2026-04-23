-- Add optional image for community comments. Skip if column exists (Duplicate column).
ALTER TABLE social_post_comments ADD COLUMN image_path VARCHAR(500) NULL DEFAULT NULL AFTER body;
