-- Run if social_post_comments lacks parent_id (e.g. DB imported from campusvoice_latest.sql before this column existed).
-- Safe to run once; skip if: Duplicate column name 'parent_id'
ALTER TABLE social_post_comments ADD COLUMN parent_id BIGINT UNSIGNED NULL DEFAULT NULL AFTER post_id;
ALTER TABLE social_post_comments ADD KEY idx_parent_id (parent_id);
ALTER TABLE social_post_comments ADD CONSTRAINT fk_comment_parent FOREIGN KEY (parent_id) REFERENCES social_post_comments(id) ON DELETE CASCADE ON UPDATE CASCADE;
