-- Add optional attachment path for feedback submissions (see FeedbackModel, PortalController).
-- Skip if: Duplicate column name 'image_path'
ALTER TABLE feedbacks ADD COLUMN image_path VARCHAR(500) NULL DEFAULT NULL AFTER message;
