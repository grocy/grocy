ALTER TABLE chores_log
ADD skipped TINYINT NOT NULL DEFAULT 0 CHECK(skipped IN (0, 1));

ALTER TABLE meal_plan_sections
ADD time_info TEXT;
