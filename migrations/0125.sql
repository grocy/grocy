ALTER TABLE users
ADD picture_file_name TEXT;

DROP VIEW users_dto;
CREATE VIEW users_dto
AS
SELECT
	id,
	username,
	first_name,
	last_name,
	row_created_timestamp,
	(CASE
		WHEN IFNULL(first_name, '') = '' AND IFNULL(last_name, '') != '' THEN last_name
		WHEN IFNULL(last_name, '') = '' AND IFNULL(first_name, '') != '' THEN first_name
		WHEN IFNULL(last_name, '') != '' AND IFNULL(first_name, '') != '' THEN first_name || ' ' || last_name
		ELSE username
	END
	) AS display_name,
	picture_file_name
FROM users;
