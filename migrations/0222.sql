CREATE INDEX ix_stock_log_performance1 ON stock_log (
	stock_id,
	transaction_type,
	amount
);

CREATE INDEX ix_stock_log_performance2 ON stock_log (
	product_id,
	best_before_date,
	purchased_date,
	transaction_type,
	stock_id,
	undone
);
