-- 承認フローシステムのSQL登録コード（修正版）
-- 既存のべた書きシステムを再現し、金額による条件分岐を追加

-- 既存の承認フローを削除
DELETE FROM approval_flow_conditions;
DELETE FROM approval_flow_steps;
DELETE FROM approval_flows;

-- 1. 係長・GL・一般からの発注承認フロー（金額分岐対応）

-- 1-1. 技術部 係長・GL・一般 発注承認フロー（10,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('技術部 係長・GL・一般 発注承認フロー（10,000円未満）', '技術部の係長・GL・一般からの発注承認フロー（10,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定（position_id 7, 8, 9のいずれかにマッチ）
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '1', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '10000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 63, true, NOW(), NOW());  -- 常務

-- 1-2. 技術部 係長・GL・一般 発注承認フロー（10,000円以上150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('技術部 係長・GL・一般 発注承認フロー（10,000円以上150,000円未満）', '技術部の係長・GL・一般からの発注承認フロー（10,000円以上150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '1', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '10000', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 63, true, NOW(), NOW()),  -- 常務
(@flow_id, 2, 94, true, NOW(), NOW());  -- 細矢本部長

-- 1-3. 技術部 係長・GL・一般 発注承認フロー（150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('技術部 係長・GL・一般 発注承認フロー（150,000円以上）', '技術部の係長・GL・一般からの発注承認フロー（150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '1', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 63, true, NOW(), NOW()),  -- 常務
(@flow_id, 2, 94, true, NOW(), NOW()),  -- 細矢本部長
(@flow_id, 3, 2, true, NOW(), NOW());   -- 社長

-- 1-4. 品証部 係長・GL・一般 発注承認フロー（10,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('品証部 係長・GL・一般 発注承認フロー（10,000円未満）', '品証部の係長・GL・一般からの発注承認フロー（10,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '2', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '10000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 16, true, NOW(), NOW());  -- 梶谷課長

-- 1-5. 品証部 係長・GL・一般 発注承認フロー（10,000円以上150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('品証部 係長・GL・一般 発注承認フロー（10,000円以上150,000円未満）', '品証部の係長・GL・一般からの発注承認フロー（10,000円以上150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '2', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '10000', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 16, true, NOW(), NOW()),  -- 梶谷課長
(@flow_id, 2, 94, true, NOW(), NOW());  -- 細矢本部長

-- 1-6. 品証部 係長・GL・一般 発注承認フロー（150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('品証部 係長・GL・一般 発注承認フロー（150,000円以上）', '品証部の係長・GL・一般からの発注承認フロー（150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '2', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 16, true, NOW(), NOW()),  -- 梶谷課長
(@flow_id, 2, 94, true, NOW(), NOW()),  -- 細矢本部長
(@flow_id, 3, 2, true, NOW(), NOW());   -- 社長

-- 1-7. 製造一課 係長・GL・一般 発注承認フロー（10,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造一課 係長・GL・一般 発注承認フロー（10,000円未満）', '製造一課の係長・GL・一般からの発注承認フロー（10,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '3', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '10000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 37, true, NOW(), NOW());  -- 長谷川課長

-- 1-8. 製造一課 係長・GL・一般 発注承認フロー（10,000円以上150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造一課 係長・GL・一般 発注承認フロー（10,000円以上150,000円未満）', '製造一課の係長・GL・一般からの発注承認フロー（10,000円以上150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '3', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '10000', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 37, true, NOW(), NOW()),  -- 長谷川課長
(@flow_id, 2, 94, true, NOW(), NOW());  -- 細矢本部長

-- 1-9. 製造一課 係長・GL・一般 発注承認フロー（150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造一課 係長・GL・一般 発注承認フロー（150,000円以上）', '製造一課の係長・GL・一般からの発注承認フロー（150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '3', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 37, true, NOW(), NOW()),  -- 長谷川課長
(@flow_id, 2, 94, true, NOW(), NOW()),  -- 細矢本部長
(@flow_id, 3, 2, true, NOW(), NOW());   -- 社長

-- 1-10. 製造二課 係長・GL・一般 発注承認フロー（10,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造二課 係長・GL・一般 発注承認フロー（10,000円未満）', '製造二課の係長・GL・一般からの発注承認フロー（10,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '4', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '10000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 84, true, NOW(), NOW());  -- 宮原課長

-- 1-11. 製造二課 係長・GL・一般 発注承認フロー（10,000円以上150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造二課 係長・GL・一般 発注承認フロー（10,000円以上150,000円未満）', '製造二課の係長・GL・一般からの発注承認フロー（10,000円以上150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '4', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '10000', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 84, true, NOW(), NOW()),  -- 宮原課長
(@flow_id, 2, 94, true, NOW(), NOW());  -- 細矢本部長

-- 1-12. 製造二課 係長・GL・一般 発注承認フロー（150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造二課 係長・GL・一般 発注承認フロー（150,000円以上）', '製造二課の係長・GL・一般からの発注承認フロー（150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '4', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 84, true, NOW(), NOW()),  -- 宮原課長
(@flow_id, 2, 94, true, NOW(), NOW()),  -- 細矢本部長
(@flow_id, 3, 2, true, NOW(), NOW());   -- 社長

-- 1-13. 保全部 係長・GL・一般 発注承認フロー（150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('保全部 係長・GL・一般 発注承認フロー（150,000円未満）', '保全部の係長・GL・一般からの発注承認フロー（150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '5', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW());  -- 細矢本部長

-- 1-14. 保全部 係長・GL・一般 発注承認フロー（150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('保全部 係長・GL・一般 発注承認フロー（150,000円以上）', '保全部の係長・GL・一般からの発注承認フロー（150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '5', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW()),  -- 細矢本部長
(@flow_id, 2, 2, true, NOW(), NOW());   -- 社長

-- 1-15. 総務部 係長・GL・一般 発注承認フロー（既存品・150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('総務部 係長・GL・一般 発注承認フロー（既存品・150,000円未満）', '総務部の係長・GL・一般からの発注承認フロー（既存品・150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '6', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 36, true, NOW(), NOW());  -- 荒川部長

-- 1-16. 総務部 係長・GL・一般 発注承認フロー（既存品・150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('総務部 係長・GL・一般 発注承認フロー（既存品・150,000円以上）', '総務部の係長・GL・一般からの発注承認フロー（既存品・150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '6', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 36, true, NOW(), NOW()),  -- 荒川部長
(@flow_id, 2, 2, true, NOW(), NOW());   -- 社長

-- 1-17. 総務部 係長・GL・一般 発注承認フロー（新規品）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('総務部 係長・GL・一般 発注承認フロー（新規品）', '総務部の係長・GL・一般からの発注承認フロー（新規品）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '6', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'true', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 63, true, NOW(), NOW());  -- 秋岡拓弥（常務）

-- 1-18. 業務部 係長・GL・一般 発注承認フロー（既存品・150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('業務部 係長・GL・一般 発注承認フロー（既存品・150,000円未満）', '業務部の係長・GL・一般からの発注承認フロー（既存品・150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '7', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 36, true, NOW(), NOW());  -- 荒川部長

-- 1-19. 業務部 係長・GL・一般 発注承認フロー（既存品・150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('業務部 係長・GL・一般 発注承認フロー（既存品・150,000円以上）', '業務部の係長・GL・一般からの発注承認フロー（既存品・150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '7', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 36, true, NOW(), NOW()),  -- 荒川部長
(@flow_id, 2, 2, true, NOW(), NOW());   -- 社長

-- 1-20. 業務部 係長・GL・一般 発注承認フロー（新規品）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('業務部 係長・GL・一般 発注承認フロー（新規品）', '業務部の係長・GL・一般からの発注承認フロー（新規品）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', 'IN', '7,8,9', NOW(), NOW()),
(@flow_id, 'group', '=', '7', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'true', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 63, true, NOW(), NOW());  -- 秋岡拓弥（常務）

-- 2. 課長からの発注承認フロー（金額分岐対応）

-- 2-1. 品証課長 発注承認フロー（10,000円未満）- 自己承認を修正
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('品証課長 発注承認フロー（10,000円未満）', '品証課長からの発注承認フロー（10,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '6', NOW(), NOW()),
(@flow_id, 'group', '=', '2', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '10000', NOW(), NOW());

-- ステップ設定（自己承認を修正）
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW());  -- 細矢本部長

-- 2-2. 品証課長 発注承認フロー（10,000円以上150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('品証課長 発注承認フロー（10,000円以上150,000円未満）', '品証課長からの発注承認フロー（10,000円以上150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '6', NOW(), NOW()),
(@flow_id, 'group', '=', '2', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '10000', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW());  -- 細矢本部長

-- 2-3. 品証課長 発注承認フロー（150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('品証課長 発注承認フロー（150,000円以上）', '品証課長からの発注承認フロー（150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '6', NOW(), NOW()),
(@flow_id, 'group', '=', '2', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW()),  -- 細矢本部長
(@flow_id, 2, 2, true, NOW(), NOW());    -- 社長

-- 2-4. 製造一課課長 発注承認フロー（10,000円未満）- 自己承認を修正
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造一課課長 発注承認フロー（10,000円未満）', '製造一課課長からの発注承認フロー（10,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '6', NOW(), NOW()),
(@flow_id, 'group', '=', '3', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '10000', NOW(), NOW());

-- ステップ設定（自己承認を修正）
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW());  -- 細矢本部長

-- 2-5. 製造一課課長 発注承認フロー（10,000円以上150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造一課課長 発注承認フロー（10,000円以上150,000円未満）', '製造一課課長からの発注承認フロー（10,000円以上150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '6', NOW(), NOW()),
(@flow_id, 'group', '=', '3', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '10000', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW());  -- 細矢本部長

-- 2-6. 製造一課課長 発注承認フロー（150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造一課課長 発注承認フロー（150,000円以上）', '製造一課課長からの発注承認フロー（150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '6', NOW(), NOW()),
(@flow_id, 'group', '=', '3', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW()),  -- 細矢本部長
(@flow_id, 2, 2, true, NOW(), NOW());    -- 社長

-- 2-7. 製造二課課長 発注承認フロー（10,000円未満）- 自己承認を修正
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造二課課長 発注承認フロー（10,000円未満）', '製造二課課長からの発注承認フロー（10,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '6', NOW(), NOW()),
(@flow_id, 'group', '=', '4', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '10000', NOW(), NOW());

-- ステップ設定（自己承認を修正）
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW());  -- 細矢本部長

-- 2-8. 製造二課課長 発注承認フロー（10,000円以上150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造二課課長 発注承認フロー（10,000円以上150,000円未満）', '製造二課課長からの発注承認フロー（10,000円以上150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '6', NOW(), NOW()),
(@flow_id, 'group', '=', '4', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '10000', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW());  -- 細矢本部長

-- 2-9. 製造二課課長 発注承認フロー（150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('製造二課課長 発注承認フロー（150,000円以上）', '製造二課課長からの発注承認フロー（150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '6', NOW(), NOW()),
(@flow_id, 'group', '=', '4', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 94, true, NOW(), NOW()),  -- 細矢本部長
(@flow_id, 2, 2, true, NOW(), NOW());    -- 社長

-- 3. 本部長・荒川部長からの発注承認フロー

-- 3-1. 本部長 発注承認フロー
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('本部長 発注承認フロー', '本部長からの発注承認フロー', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '3', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 2, true, NOW(), NOW());   -- 社長のみ

-- 3-2. 荒川部長 発注承認フロー（150,000円未満）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('荒川部長 発注承認フロー（150,000円未満）', '荒川部長からの発注承認フロー（150,000円未満）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '5', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_max', '<', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 63, true, NOW(), NOW());  -- 常務

-- 3-3. 荒川部長 発注承認フロー（150,000円以上）
INSERT INTO approval_flows (name, description, is_active, created_at, updated_at) VALUES
('荒川部長 発注承認フロー（150,000円以上）', '荒川部長からの発注承認フロー（150,000円以上）', true, NOW(), NOW());

SET @flow_id = LAST_INSERT_ID();

-- 条件設定
INSERT INTO approval_flow_conditions (approval_flow_id, condition_type, operator, condition_value, created_at, updated_at) VALUES
(@flow_id, 'position', '=', '5', NOW(), NOW()),
(@flow_id, 'is_new_item', '=', 'false', NOW(), NOW()),
(@flow_id, 'price_min', '>=', '150000', NOW(), NOW());

-- ステップ設定
INSERT INTO approval_flow_steps (approval_flow_id, step_order, approver_user_id, is_required, created_at, updated_at) VALUES
(@flow_id, 1, 63, true, NOW(), NOW()),  -- 常務
(@flow_id, 2, 2, true, NOW(), NOW());   -- 社長

-- 確認用クエリ
-- 登録された承認フローを確認
SELECT 
    af.id,
    af.name,
    af.description,
    af.is_active,
    COUNT(afs.id) as step_count,
    COUNT(afc.id) as condition_count
FROM approval_flows af
LEFT JOIN approval_flow_steps afs ON af.id = afs.approval_flow_id
LEFT JOIN approval_flow_conditions afc ON af.id = afc.approval_flow_id
GROUP BY af.id, af.name, af.description, af.is_active
ORDER BY af.id;
