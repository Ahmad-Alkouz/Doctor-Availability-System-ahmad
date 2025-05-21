-- Add reset token columns to doctors table
ALTER TABLE doctors
ADD COLUMN reset_token VARCHAR(64) NULL,
ADD COLUMN reset_expiry DATETIME NULL;

-- Add reset token columns to students table
ALTER TABLE students
ADD COLUMN reset_token VARCHAR(64) NULL,
ADD COLUMN reset_expiry DATETIME NULL; 