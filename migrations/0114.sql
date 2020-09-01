CREATE VIEW users_dto
AS
SELECT id,
       username,
       first_name,
       last_name,
       row_created_timestamp,
       (CASE
            WHEN first_name = '' AND last_name != '' THEN last_name
            WHEN last_name = '' AND first_name != '' THEN first_name
            WHEN last_name != '' AND first_name != '' THEN first_name + ' ' + last_name
            ELSE username
           END
           ) AS display_name
FROM users;
CREATE VIEW uihelper_chores_current AS
SELECT chores_current.*, chores.name AS chore_name
from chores_current
         join chores on chores_current.chore_id = chores.id;

CREATE VIEW uihelper_batteries_current AS
    SELECT battery_id AS id, * FROM batteries_current;
