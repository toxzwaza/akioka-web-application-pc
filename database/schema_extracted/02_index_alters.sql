--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `account_items`
--
ALTER TABLE `account_items`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointments_reception_number_unique` (`reception_number`),
  ADD KEY `appointments_staff_member_id_foreign` (`staff_member_id`);

--
-- テーブルのインデックス `approval_flows`
--
ALTER TABLE `approval_flows`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `approval_flow_conditions`
--
ALTER TABLE `approval_flow_conditions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_flow_conditions_approval_flow_id_foreign` (`approval_flow_id`);

--
-- テーブルのインデックス `approval_flow_steps`
--
ALTER TABLE `approval_flow_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `approval_flow_steps_approval_flow_id_foreign` (`approval_flow_id`),
  ADD KEY `approval_flow_steps_approver_user_id_foreign` (`approver_user_id`);

--
-- テーブルのインデックス `classifications`
--
ALTER TABLE `classifications`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `computers`
--
ALTER TABLE `computers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `computers_place_id_foreign` (`place_id`);

--
-- テーブルのインデックス `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contacts_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `contact_keywords`
--
ALTER TABLE `contact_keywords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_keywords_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `contact_users`
--
ALTER TABLE `contact_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_users_user_id_foreign` (`user_id`),
  ADD KEY `contact_users_contact_id_foreign` (`contact_id`);

--
-- テーブルのインデックス `crane_inspection_colors`
--
ALTER TABLE `crane_inspection_colors`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deliveries_staff_member_id_foreign` (`staff_member_id`);

--
-- テーブルのインデックス `delivery_initial_order`
--
ALTER TABLE `delivery_initial_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `delivery_initial_order_unique` (`delivery_id`,`initial_order_id`),
  ADD KEY `delivery_initial_order_delivery_id_index` (`delivery_id`),
  ADD KEY `delivery_initial_order_initial_order_id_index` (`initial_order_id`);

--
-- テーブルのインデックス `devices`
--
ALTER TABLE `devices`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `device_messages`
--
ALTER TABLE `device_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `device_messages_to_device_id_foreign` (`to_device_id`),
  ADD KEY `device_messages_from_device_id_foreign` (`from_device_id`),
  ADD KEY `device_messages_to_user_id_foreign` (`to_user_id`),
  ADD KEY `device_messages_from_user_id_foreign` (`from_user_id`);

--
-- テーブルのインデックス `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `documents_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `document_images`
--
ALTER TABLE `document_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_images_document_id_foreign` (`document_id`);

--
-- テーブルのインデックス `document_stocks`
--
ALTER TABLE `document_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_stocks_stock_id_foreign` (`stock_id`),
  ADD KEY `document_stocks_document_id_foreign` (`document_id`);

--
-- テーブルのインデックス `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `facilities_name_unique` (`name`);

--
-- テーブルのインデックス `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- テーブルのインデックス `fax_groups`
--
ALTER TABLE `fax_groups`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `fax_parameters`
--
ALTER TABLE `fax_parameters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`) COMMENT 'ステータス検索用インデックス',
  ADD KEY `idx_created_at` (`created_at`) COMMENT '作成日時ソート用インデックス',
  ADD KEY `idx_fax_number` (`fax_number`) COMMENT 'FAX番号検索用インデックス',
  ADD KEY `idx_request_user` (`request_user`) COMMENT '依頼者検索用インデックス',
  ADD KEY `idx_status_created` (`status`,`created_at`) COMMENT 'ステータス+作成日時複合インデックス';

--
-- テーブルのインデックス `fax_sort_settings`
--
ALTER TABLE `fax_sort_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fax_sort_settings_fax_group_id_foreign` (`fax_group_id`);

--
-- テーブルのインデックス `fax_user_groups`
--
ALTER TABLE `fax_user_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fax_user_groups_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `initial_orders`
--
ALTER TABLE `initial_orders`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `interview_phones`
--
ALTER TABLE `interview_phones`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `inventory_operations`
--
ALTER TABLE `inventory_operations`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `inventory_operation_records`
--
ALTER TABLE `inventory_operation_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inventory_operation_records_stock_storage_id_foreign` (`stock_storage_id`),
  ADD KEY `inventory_operation_records_inventory_operation_id_foreign` (`inventory_operation_id`),
  ADD KEY `inventory_operation_records_user_id_foreign` (`user_id`),
  ADD KEY `inventory_operation_records_device_id_foreign` (`device_id`);

--
-- テーブルのインデックス `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `location_processes`
--
ALTER TABLE `location_processes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_processes_location_id_foreign` (`location_id`),
  ADD KEY `location_processes_process_id_foreign` (`process_id`);

--
-- テーブルのインデックス `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `lunches`
--
ALTER TABLE `lunches`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `lunch_orders`
--
ALTER TABLE `lunch_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lunch_orders_lunch_id_foreign` (`lunch_id`),
  ADD KEY `lunch_orders_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `lunch_order_archives`
--
ALTER TABLE `lunch_order_archives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lunch_order_archives_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `movie_memos`
--
ALTER TABLE `movie_memos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_memos_movie_id_foreign` (`movie_id`),
  ADD KEY `movie_memos_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `movie_tags`
--
ALTER TABLE `movie_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_tags_movie_tag_category_id_foreign` (`movie_tag_category_id`);

--
-- テーブルのインデックス `movie_tag_categories`
--
ALTER TABLE `movie_tag_categories`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `notification_recipients`
--
ALTER TABLE `notification_recipients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_recipients_notification_setting_id_is_active_index` (`notification_setting_id`,`is_active`),
  ADD KEY `notification_recipients_user_id_notification_type_index` (`user_id`,`notification_type`);

--
-- テーブルのインデックス `notification_settings`
--
ALTER TABLE `notification_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_settings_trigger_event_is_active_index` (`trigger_event`,`is_active`);

--
-- テーブルのインデックス `notify_groups`
--
ALTER TABLE `notify_groups`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `notify_group_users`
--
ALTER TABLE `notify_group_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notify_group_users_user_id_foreign` (`user_id`),
  ADD KEY `notify_group_users_notify_group_id_foreign` (`notify_group_id`);

--
-- テーブルのインデックス `notify_queues`
--
ALTER TABLE `notify_queues`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `notify_queue_users`
--
ALTER TABLE `notify_queue_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notify_queue_users_notify_queue_id_foreign` (`notify_queue_id`),
  ADD KEY `notify_queue_users_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `object_requests`
--
ALTER TABLE `object_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `object_requests_stock_id_foreign` (`stock_id`),
  ADD KEY `object_requests_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `order_requests`
--
ALTER TABLE `order_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_requests_stock_id_foreign` (`stock_id`);

--
-- テーブルのインデックス `order_request_approvals`
--
ALTER TABLE `order_request_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_request_approvals_user_id_foreign` (`user_id`),
  ADD KEY `order_request_approvals_order_request_id_foreign` (`order_request_id`);

--
-- テーブルのインデックス `part_defects`
--
ALTER TABLE `part_defects`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`email`);

--
-- テーブルのインデックス `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- テーブルのインデックス `pickups`
--
ALTER TABLE `pickups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pickups_staff_member_id_foreign` (`staff_member_id`);

--
-- テーブルのインデックス `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `pre_lunch_orders`
--
ALTER TABLE `pre_lunch_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pre_lunch_orders_lunch_id_foreign` (`lunch_id`),
  ADD KEY `pre_lunch_orders_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `processes`
--
ALTER TABLE `processes`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `product_aliases`
--
ALTER TABLE `product_aliases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_aliases_stock_id_foreign` (`stock_id`);

--
-- テーブルのインデックス `project_groups`
--
ALTER TABLE `project_groups`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `project_group_user`
--
ALTER TABLE `project_group_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_group_user_project_group_id_user_id_unique` (`project_group_id`,`user_id`),
  ADD KEY `project_group_user_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `raspi_data`
--
ALTER TABLE `raspi_data`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `retained_stocks`
--
ALTER TABLE `retained_stocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `retained_stocks_stock_id_foreign` (`stock_id`),
  ADD KEY `retained_stocks_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `safeties`
--
ALTER TABLE `safeties`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `schedule_events`
--
ALTER TABLE `schedule_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_event` (`facility_id`,`date`,`EID`),
  ADD KEY `schedule_events_facility_id_date_index` (`facility_id`,`date`),
  ADD KEY `schedule_events_date_index` (`date`),
  ADD KEY `idx_status` (`status`);

--
-- テーブルのインデックス `schedule_participants`
--
ALTER TABLE `schedule_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `schedule_participants_schedule_event_id_user_id_unique` (`schedule_event_id`,`user_id`),
  ADD KEY `schedule_participants_schedule_event_id_index` (`schedule_event_id`),
  ADD KEY `schedule_participants_user_id_index` (`user_id`);

--
-- テーブルのインデックス `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- テーブルのインデックス `signages`
--
ALTER TABLE `signages`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `signage_displays`
--
ALTER TABLE `signage_displays`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `split_order_quantities`
--
ALTER TABLE `split_order_quantities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `split_order_quantities_initial_order_id_foreign` (`initial_order_id`);

--
-- テーブルのインデックス `staff_members`
--
ALTER TABLE `staff_members`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `stock_aliases`
--
ALTER TABLE `stock_aliases`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `stock_images`
--
ALTER TABLE `stock_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_images_stock_id_foreign` (`stock_id`);

--
-- テーブルのインデックス `stock_price_archives`
--
ALTER TABLE `stock_price_archives`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `stock_processes`
--
ALTER TABLE `stock_processes`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `stock_requests`
--
ALTER TABLE `stock_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_requests_stock_id_foreign` (`stock_id`);

--
-- テーブルのインデックス `stock_request_orders`
--
ALTER TABLE `stock_request_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_request_orders_user_id_foreign` (`user_id`),
  ADD KEY `stock_request_orders_process_id_foreign` (`process_id`),
  ADD KEY `stock_request_orders_stock_id_foreign` (`stock_id`);

--
-- テーブルのインデックス `stock_storages`
--
ALTER TABLE `stock_storages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_storages_stock_id_foreign` (`stock_id`),
  ADD KEY `stock_storages_storage_address_id_foreign` (`storage_address_id`);

--
-- テーブルのインデックス `stock_suppliers`
--
ALTER TABLE `stock_suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_suppliers_stock_id_foreign` (`stock_id`),
  ADD KEY `stock_suppliers_supplier_id_foreign` (`supplier_id`);

--
-- テーブルのインデックス `stock_supplier_prices`
--
ALTER TABLE `stock_supplier_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_supplier_prices_stock_id_start_date_index` (`stock_id`,`start_date`),
  ADD KEY `stock_supplier_prices_stock_supplier_id_active_flg_index` (`stock_supplier_id`);

--
-- テーブルのインデックス `storage_addresses`
--
ALTER TABLE `storage_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `storage_addresses_location_id_foreign` (`location_id`);

--
-- テーブルのインデックス `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `task_transactions`
--
ALTER TABLE `task_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_transactions_task_id_foreign` (`task_id`);

--
-- テーブルのインデックス `task_update_checks`
--
ALTER TABLE `task_update_checks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_update_checks_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `today_lunch_descriptions`
--
ALTER TABLE `today_lunch_descriptions`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_group_id_foreign` (`group_id`),
  ADD KEY `users_position_id_foreign` (`position_id`);

--
-- テーブルのインデックス `user_aliases`
--
ALTER TABLE `user_aliases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_aliases_user_id_foreign` (`user_id`);

--
-- テーブルのインデックス `user_schedules`
--
ALTER TABLE `user_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_event` (`user_id`,`date`,`EID`),
  ADD KEY `user_schedules_user_id_date_index` (`user_id`,`date`),
  ADD KEY `user_schedules_date_index` (`date`),
  ADD KEY `idx_status` (`status`);

--
-- テーブルのインデックス `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitors_staff_member_id_foreign` (`staff_member_id`);

