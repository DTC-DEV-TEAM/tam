CREATE VIEW item_master_view AS
SELECT DISTINCT assets.digits_code AS digits_code,
                assets.item_description AS item_description
FROM assets
LEFT JOIN tam_categories ON assets.category_id = tam_categories.id
LEFT JOIN tam_subcategories ON assets.sub_category_id = tam_subcategories.id


