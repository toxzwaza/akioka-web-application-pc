CREATE TABLE `account_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL COMMENT 'お知らせタイトル',
  `content` text NOT NULL COMMENT 'お知らせ内容',
  `type` varchar(255) NOT NULL DEFAULT 'info' COMMENT '種別（info, warning, error）',
  `start_date` date NOT NULL COMMENT '表示開始日',
  `end_date` date NOT NULL COMMENT '表示終了日',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  `display_order` int(11) NOT NULL DEFAULT 0 COMMENT '表示順',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reception_number` varchar(4) NOT NULL COMMENT '受付番号（4桁）',
  `company_name` varchar(255) NOT NULL COMMENT '会社名',
  `visitor_name` varchar(255) NOT NULL COMMENT '訪問者氏名',
  `visitor_email` varchar(255) DEFAULT NULL COMMENT '訪問者メールアドレス',
  `visitor_phone` varchar(255) DEFAULT NULL COMMENT '訪問者電話番号',
  `staff_member_id` bigint(20) UNSIGNED NOT NULL,
  `visit_date` date NOT NULL COMMENT '訪問予定日',
  `visit_time` time NOT NULL COMMENT '訪問予定時刻',
  `purpose` text DEFAULT NULL COMMENT '訪問目的',
  `qr_code` varchar(255) DEFAULT NULL COMMENT 'QRコードデータ',
  `is_checked_in` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'チェックイン済みフラグ',
  `checked_in_at` timestamp NULL DEFAULT NULL COMMENT 'チェックイン日時',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `send_flg` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'メール送信済みフラグ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_flows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_flow_conditions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approval_flow_id` bigint(20) UNSIGNED NOT NULL,
  `condition_type` varchar(255) NOT NULL,
  `operator` varchar(255) NOT NULL DEFAULT '=',
  `condition_value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `approval_flow_steps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `approval_flow_id` bigint(20) UNSIGNED NOT NULL,
  `step_order` int(11) NOT NULL,
  `approver_user_id` bigint(20) UNSIGNED NOT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `classifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type` tinyint(4) NOT NULL DEFAULT 0,
  `memo` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `computers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `place_id` bigint(20) UNSIGNED NOT NULL,
  `ip_address` varchar(255) NOT NULL,
  `mac_address` varchar(255) NOT NULL,
  `signage` tinyint(4) NOT NULL DEFAULT 0,
  `sensor` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `kind` tinyint(4) DEFAULT NULL COMMENT '0:製品 1:新規案件 2:営業・広告 3:その他',
  `name` varchar(255) DEFAULT NULL COMMENT '名前',
  `furi_name` varchar(255) DEFAULT NULL COMMENT 'フリガナ',
  `email` varchar(255) DEFAULT NULL COMMENT 'メール',
  `tel` varchar(255) DEFAULT NULL COMMENT '電話番号',
  `content` text DEFAULT NULL COMMENT '問い合わせ内容原本',
  `subject` varchar(100) DEFAULT NULL,
  `summary` text DEFAULT NULL COMMENT 'AI要約',
  `progress` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0:未対応 1:対応中 2:対応済み',
  `del_flg` tinyint(4) NOT NULL DEFAULT 0 COMMENT '削除フラグ',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `memo` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contact_keywords` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contact_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `contact_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `crane_inspection_colors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `color` varchar(255) NOT NULL DEFAULT '',
  `active_flg` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `temperature` int(11) NOT NULL,
  `humidity` int(11) NOT NULL,
  `co2` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `deliveries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_type` varchar(255) NOT NULL,
  `document_image` varchar(255) NOT NULL,
  `sealed_document_image` varchar(255) DEFAULT NULL,
  `qr_code_url` varchar(255) DEFAULT NULL,
  `qr_code_file_path` varchar(255) DEFAULT NULL,
  `received_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `staff_member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `initial_order_id` int(11) DEFAULT NULL COMMENT '書類紐づけ用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `delivery_initial_order` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `delivery_id` bigint(20) UNSIGNED NOT NULL,
  `initial_order_id` bigint(20) UNSIGNED NOT NULL COMMENT '発注ID',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `devices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `last_access_date` datetime DEFAULT NULL COMMENT '最終アクセス日時',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `device_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `priority` tinyint(4) NOT NULL DEFAULT 0,
  `to_device_id` bigint(20) UNSIGNED NOT NULL,
  `from_device_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_user_id` bigint(20) UNSIGNED NOT NULL,
  `from_user_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL COMMENT 'リンク',
  `answer` text DEFAULT NULL COMMENT '回答',
  `read_flg` tinyint(4) NOT NULL DEFAULT 0,
  `del_flg` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `evalution_date` date DEFAULT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `main_reason` text NOT NULL,
  `sub_reason` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `document_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `document_id` bigint(20) UNSIGNED NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `extension` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `document_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `document_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `cybozu_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fax_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fax_parameters` (
  `id` varchar(36) NOT NULL COMMENT 'UUID',
  `file_url` text DEFAULT NULL COMMENT 'ファイルURL',
  `fax_number` varchar(20) DEFAULT NULL COMMENT 'FAX番号',
  `status` int(11) DEFAULT 0 COMMENT 'ステータス（0:待機中, 1:完了, 2:処理中, -1:エラー）',
  `created_at` datetime DEFAULT NULL COMMENT '作成日時',
  `updated_at` datetime DEFAULT NULL COMMENT '更新日時',
  `error_message` text DEFAULT NULL COMMENT 'エラーメッセージ',
  `converted_pdf_path` text DEFAULT NULL COMMENT '変換後PDFファイルパス',
  `request_user` varchar(100) DEFAULT NULL COMMENT '依頼者名',
  `file_name` varchar(255) DEFAULT NULL COMMENT 'ファイル名',
  `callback_url` text DEFAULT NULL COMMENT 'コールバックURL',
  `order_destination` varchar(100) DEFAULT NULL COMMENT '発注先'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='FAX送信パラメータ管理テーブル';

CREATE TABLE `fax_sort_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT NULL,
  `fax_group_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `fax_user_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fax_group_id` int(10) NOT NULL,
  `notify_flg` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL COMMENT '受付システム用 内線番号',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `holidays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `is_holiday` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `initial_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_request_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_no` varchar(255) DEFAULT NULL COMMENT '注文No',
  `order_user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '注文依頼者ID',
  `order_user` varchar(255) DEFAULT NULL COMMENT '注文依頼者',
  `user_id` tinyint(4) DEFAULT 0 COMMENT '発注者',
  `order_date` date DEFAULT NULL COMMENT '注文日',
  `supplier_id` bigint(20) UNSIGNED DEFAULT NULL,
  `com_no` varchar(255) DEFAULT NULL COMMENT '手配先会社No',
  `com_name` varchar(255) DEFAULT NULL COMMENT '手配先名',
  `stock_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '物品データ紐づけ用',
  `name` varchar(255) DEFAULT NULL COMMENT '品名',
  `s_name` varchar(255) DEFAULT NULL COMMENT '品番',
  `quantity` bigint(20) UNSIGNED DEFAULT NULL COMMENT '個数',
  `price` decimal(10,1) DEFAULT 0.0 COMMENT '単価',
  `calc_price` int(11) NOT NULL DEFAULT 0 COMMENT '金額',
  `postage` bigint(20) UNSIGNED DEFAULT NULL COMMENT '送料',
  `order_unit` varchar(255) DEFAULT NULL,
  `deli_location` varchar(255) DEFAULT NULL COMMENT '配達先',
  `delifile_path` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `receive_flg` tinyint(4) DEFAULT 0 COMMENT '納品完了フラグ(1: 済 0:未 2:分納中)',
  `receipt_flg` tinyint(4) DEFAULT 0 COMMENT '引き渡し完了フラグ(1:済 0:未)',
  `none_storage_flg` tinyint(4) DEFAULT NULL COMMENT '在庫未登録物品サイネージ表示\r\n(1:表示, 2:非表示)',
  `not_found_flg` tinyint(4) DEFAULT NULL,
  `desire_delivery_date` date DEFAULT NULL COMMENT '納入希望日',
  `expected_delivery_date` date DEFAULT NULL COMMENT '配達予定日',
  `delivery_date` date DEFAULT NULL COMMENT '配達日',
  `lead_time` bigint(20) UNSIGNED DEFAULT NULL COMMENT '記録用リードタイム',
  `stock_process_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '勘定科目紐づけ用',
  `order_complete_flg` tinyint(4) DEFAULT 0 COMMENT '発注済みフラグ',
  `purchase_path` varchar(255) DEFAULT NULL COMMENT '発注書画像パス',
  `del_flg` tinyint(4) DEFAULT 0 COMMENT '送料など削除フラグ',
  `device_message_id` int(10) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL COMMENT '発注者備考',
  `created_at` timestamp NULL DEFAULT NULL,
  `fax_parameter_id` varchar(36) DEFAULT NULL COMMENT '発注書FAX紐づけ用',
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `interview_phones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_name` varchar(255) NOT NULL COMMENT '部署名',
  `contact_person` varchar(255) NOT NULL COMMENT '担当者名',
  `phone_number` varchar(255) NOT NULL COMMENT '電話番号',
  `extension_number` varchar(255) DEFAULT NULL COMMENT '内線番号',
  `notes` text DEFAULT NULL COMMENT '備考',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '有効フラグ',
  `display_order` int(11) NOT NULL DEFAULT 0 COMMENT '表示順',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory_operations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inventory_operation_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `inventory_operation_id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` int(10) UNSIGNED DEFAULT NULL,
  `stock_storage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(11) DEFAULT 0,
  `bef_quantity` int(11) DEFAULT NULL COMMENT '前数量',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `device_id` bigint(20) UNSIGNED DEFAULT NULL,
  `memo` text DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `del_flg` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `location_processes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `process_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `device_name` varchar(100) DEFAULT NULL,
  `service_name` varchar(100) NOT NULL COMMENT 'EDI, ',
  `level` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0:info, 1:warning, 2:error ',
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lunches` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` bigint(20) NOT NULL,
  `del_flg` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lunch_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lunch_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_flg` tinyint(4) DEFAULT 0,
  `receive_flg` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date` datetime DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `lunch_order_archives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lunch_count` bigint(20) UNSIGNED DEFAULT 0,
  `side_dish_count` bigint(20) UNSIGNED DEFAULT 0,
  `lunch_calc` bigint(20) UNSIGNED DEFAULT 0,
  `side_dish_calc` bigint(20) UNSIGNED DEFAULT 0,
  `memo` text DEFAULT '',
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `movies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `youtube_id` varchar(255) DEFAULT NULL,
  `movie_tag_id` tinyint(4) NOT NULL DEFAULT 0,
  `memo` text DEFAULT NULL,
  `del_flg` tinyint(4) NOT NULL DEFAULT 0,
  `transcription_flg` tinyint(4) DEFAULT NULL COMMENT '文字お越し待ち: 1 完了:2',
  `created_at` date DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `movie_memos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `memo` text NOT NULL,
  `time` time NOT NULL,
  `del_flg` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `transcription_flg` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `movie_tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `movie_tag_category_id` bigint(20) UNSIGNED NOT NULL,
  `del_flg` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `accent_color` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `movie_tag_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `del_flg` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `accent_color` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notification_recipients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_setting_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'ユーザーID（akioka_db.usersテーブル参照）',
  `notification_type` enum('phone','email','teams') NOT NULL,
  `notification_data` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notification_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `trigger_event` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`settings`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notify_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `del_flg` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notify_group_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `notify_group_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notify_queues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `msg` text NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `comp_flg` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `notify_queue_users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notify_queue_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `object_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `limit` date NOT NULL,
  `disappear` date NOT NULL,
  `memo` text DEFAULT '',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `accept_date` date DEFAULT NULL,
  `accept_user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `order_status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_no` varchar(255) DEFAULT '',
  `order_quantity` int(11) DEFAULT 0,
  `order_price` int(11) DEFAULT 0,
  `limit` date DEFAULT NULL,
  `conf_date` date DEFAULT NULL,
  `deli_sche_date` date DEFAULT NULL,
  `deli_date` date DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `order_flg` tinyint(4) DEFAULT 0,
  `accept_type` varchar(255) DEFAULT '',
  `accept_id` bigint(20) DEFAULT NULL,
  `del_flg` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(200) DEFAULT NULL COMMENT 'stock_idがない場合有効',
  `s_name` varchar(200) DEFAULT NULL COMMENT 'stock_idがない場合有効',
  `request_user_id` bigint(20) UNSIGNED DEFAULT 0 COMMENT '注文依頼者',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '注文者',
  `supplier_id` bigint(20) UNSIGNED DEFAULT 0 COMMENT '得意先',
  `supplier_name` varchar(255) DEFAULT NULL,
  `stock_supplier_lead_time` int(11) DEFAULT NULL,
  `lead_time` tinyint(4) DEFAULT 0,
  `quantity` int(11) DEFAULT NULL COMMENT '必要数量',
  `unit` varchar(20) DEFAULT NULL COMMENT '発注単位',
  `now_quantity` bigint(20) UNSIGNED DEFAULT NULL COMMENT '現在数量',
  `now_quantity_unit` varchar(10) DEFAULT NULL COMMENT '現在数量単位',
  `digest_date` date DEFAULT NULL COMMENT '消化予定日',
  `desire_delivery_date` date DEFAULT NULL COMMENT '希望納期',
  `price` decimal(10,1) DEFAULT NULL,
  `calc_price` bigint(20) UNSIGNED DEFAULT NULL,
  `postage` bigint(20) UNSIGNED DEFAULT 0 COMMENT '送料',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '注文完了フラグ',
  `accept_flg` tinyint(4) NOT NULL DEFAULT 0 COMMENT '承認フラグ(0:未、1:承認待ち、2:承認, 3:却下, 4:却下後再依頼待ち 5:確認中 6:差し戻し)',
  `new_stock_flg` tinyint(4) DEFAULT 0,
  `stock_process_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '勘定科目紐づけ用',
  `del_flg` tinyint(4) DEFAULT 0,
  `document_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '稟議書ID',
  `file_path` varchar(255) DEFAULT NULL COMMENT '稟議書PDFファイルパス',
  `file_path_sub` text DEFAULT NULL COMMENT '複数ファイルパス（JSON形式）',
  `description` text DEFAULT NULL COMMENT '依頼者備考',
  `sub_description` varchar(255) DEFAULT NULL COMMENT '発注者備考',
  `device_id` int(10) UNSIGNED DEFAULT NULL COMMENT '依頼元端末ID',
  `device_message_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'メッセージ紐づけ用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_request_approvals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_request_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) DEFAULT NULL COMMENT '承認ステータス null:前承認待ち 0:承認待ち 1:承認 2:却下',
  `final_flg` tinyint(4) NOT NULL DEFAULT 0,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `part_defects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `part_num` bigint(20) UNSIGNED NOT NULL,
  `figure_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` text NOT NULL,
  `memo` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pickups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slip_image` varchar(255) NOT NULL,
  `sealed_slip_image` varchar(255) DEFAULT NULL,
  `qr_code_url` varchar(255) DEFAULT NULL,
  `qr_code_file_path` varchar(255) DEFAULT NULL,
  `picked_up_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `staff_member_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `places` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pre_lunch_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lunch_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `order_flg` tinyint(4) DEFAULT 0,
  `receive_flg` tinyint(4) DEFAULT 0,
  `order_date` date DEFAULT '2024-07-23',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `processes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `place_id` int(10) UNSIGNED DEFAULT NULL,
  `division` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_aliases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `alias` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `project_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `project_group_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_group_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `raspi_data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `process_id` bigint(20) UNSIGNED NOT NULL,
  `temperature` varchar(255) NOT NULL,
  `humidity` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `retained_stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `treat_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `safeties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `schedule_events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `facility_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `title` varchar(500) NOT NULL,
  `start_datetime` varchar(10) NOT NULL,
  `end_datetime` varchar(10) NOT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `description_url` text DEFAULT NULL,
  `EID` varchar(100) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 0 COMMENT '1:サイボウズ反映待ち',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `schedule_participants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `schedule_event_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `signages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `file_name` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `is_main` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `signage_displays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `address` varchar(255) NOT NULL,
  `memo` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `split_order_quantities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `initial_order_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `staff_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'ユーザーID（akioka_db.usersテーブル参照）',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_no` varchar(255) DEFAULT '',
  `name` varchar(255) DEFAULT '',
  `s_name` varchar(255) DEFAULT '',
  `img_path` text DEFAULT '/storage/stock/not-image-sample2.png',
  `url` varchar(255) DEFAULT '',
  `stock_process_id` varchar(255) DEFAULT NULL COMMENT '勘定科目変換用',
  `tax_included` tinyint(4) DEFAULT 0 COMMENT '0:税抜き 1:税込み',
  `price` decimal(10,1) DEFAULT NULL,
  `solo_unit` varchar(255) DEFAULT NULL COMMENT '取り出し単位',
  `org_unit` varchar(255) DEFAULT NULL COMMENT '発注単位',
  `quantity_per_org` int(11) DEFAULT 1 COMMENT '納入時のデフォルト換算値',
  `deli_location` varchar(255) DEFAULT '',
  `memo` text DEFAULT '',
  `del_flg` int(11) NOT NULL DEFAULT 0,
  `classification_id` int(10) DEFAULT NULL,
  `not_stock_flg` tinyint(1) DEFAULT NULL,
  `purchase_identification_number` varchar(255) DEFAULT NULL,
  `jan_code` varchar(255) DEFAULT NULL,
  `main_unit_flg` tinyint(4) DEFAULT 0 COMMENT '0:単品 1:まとまり',
  `price_check_flg` tinyint(4) DEFAULT 0 COMMENT '価格チェックフラグ',
  `approval_supplier_name` varchar(255) DEFAULT NULL COMMENT '稟議書記載 発注先（参考までに）',
  `special_area_cd` tinyint(10) DEFAULT 0 COMMENT '1: 鋳出文字',
  `desc_memo` varchar(255) DEFAULT NULL COMMENT '備考',
  `show_price_on_invoice` tinyint(1) NOT NULL DEFAULT 0 COMMENT '納品書金額表示フラグ 0:表示 1:非表示',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_aliases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `alias` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `img_path` text NOT NULL,
  `del_flg` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_price_archives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `system_check_flg` tinyint(4) DEFAULT 0 COMMENT ' 0: 手動 1:システムチェック',
  `price` decimal(10,1) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_processes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` bigint(20) UNSIGNED NOT NULL,
  `account_item_id` int(10) UNSIGNED DEFAULT NULL COMMENT '勘定科目変換ID',
  `process_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `orderNumber` bigint(20) UNSIGNED DEFAULT NULL,
  `unit` varchar(10) DEFAULT NULL COMMENT '単位',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_request_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `process_id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `order_flg` tinyint(4) DEFAULT 0,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_storages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `storage_address_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `reorder_point` tinyint(4) DEFAULT 0 COMMENT '発注点',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `lead_time` int(10) UNSIGNED DEFAULT NULL,
  `postage` bigint(20) UNSIGNED DEFAULT 0,
  `act_flg` tinyint(4) NOT NULL DEFAULT 0,
  `del_flg` tinyint(4) NOT NULL DEFAULT 0,
  `main_flg` tinyint(5) DEFAULT 0,
  `memo` text DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `stock_supplier_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stock_id` bigint(20) UNSIGNED NOT NULL,
  `stock_supplier_id` bigint(20) UNSIGNED NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `active_flg` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0:適用済み 1:未適用',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `storage_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uid` varchar(14) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `shelf` varchar(10) DEFAULT NULL,
  `row` varchar(10) DEFAULT NULL,
  `col` varchar(10) DEFAULT NULL,
  `sub_row` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_no` varchar(255) DEFAULT '',
  `name` varchar(255) NOT NULL,
  `rub_name` varchar(255) DEFAULT '',
  `tel` varchar(255) DEFAULT '',
  `fax` varchar(255) DEFAULT '',
  `p_code` varchar(255) DEFAULT '',
  `address` varchar(255) DEFAULT '',
  `memo` text DEFAULT '',
  `del_flg` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `invoice_registration_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `task_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `task_update_checks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `update_flg` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `today_lunch_descriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `emp_no` varchar(6) DEFAULT NULL COMMENT '社員No, 担当者CO',
  `name` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `gender_flg` tinyint(4) DEFAULT 0 COMMENT '0:男性 1:女性',
  `group_id` bigint(20) UNSIGNED NOT NULL,
  `position_id` bigint(20) UNSIGNED NOT NULL,
  `process_id` bigint(20) NOT NULL DEFAULT 0,
  `is_admin` tinyint(4) DEFAULT 0,
  `dispatch_flg` tinyint(4) DEFAULT 0,
  `part_flg` tinyint(4) DEFAULT 0,
  `always_order_flg` tinyint(4) DEFAULT 0,
  `duty_flg` tinyint(4) DEFAULT 0,
  `fax_folder_name` varchar(255) DEFAULT NULL,
  `cybozu_flg` tinyint(4) DEFAULT 0 COMMENT 'サイボウズオフィスカレンダー連携フラグ',
  `del_flg` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_aliases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `title` varchar(500) NOT NULL,
  `start_datetime` varchar(10) NOT NULL,
  `end_datetime` varchar(10) NOT NULL,
  `badge` varchar(100) DEFAULT NULL,
  `description_url` text DEFAULT NULL,
  `EID` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `visitors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `visitor_name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `business_card_image` varchar(255) DEFAULT NULL,
  `staff_member_id` bigint(20) UNSIGNED DEFAULT NULL,
  `group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `visitor_type` varchar(255) NOT NULL,
  `number_of_people` int(11) NOT NULL DEFAULT 1,
  `purpose` text DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `reception_number` int(11) DEFAULT NULL,
  `check_in_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `check_out_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

