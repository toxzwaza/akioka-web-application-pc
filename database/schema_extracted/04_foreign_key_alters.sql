--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `approval_flow_conditions`
--
ALTER TABLE `approval_flow_conditions`
  ADD CONSTRAINT `approval_flow_conditions_approval_flow_id_foreign` FOREIGN KEY (`approval_flow_id`) REFERENCES `approval_flows` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `approval_flow_steps`
--
ALTER TABLE `approval_flow_steps`
  ADD CONSTRAINT `approval_flow_steps_approval_flow_id_foreign` FOREIGN KEY (`approval_flow_id`) REFERENCES `approval_flows` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `approval_flow_steps_approver_user_id_foreign` FOREIGN KEY (`approver_user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `computers`
--
ALTER TABLE `computers`
  ADD CONSTRAINT `computers_place_id_foreign` FOREIGN KEY (`place_id`) REFERENCES `places` (`id`);

--
-- テーブルの制約 `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `contact_keywords`
--
ALTER TABLE `contact_keywords`
  ADD CONSTRAINT `contact_keywords_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `contact_users`
--
ALTER TABLE `contact_users`
  ADD CONSTRAINT `contact_users_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `contacts` (`id`),
  ADD CONSTRAINT `contact_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `deliveries_staff_member_id_foreign` FOREIGN KEY (`staff_member_id`) REFERENCES `staff_members` (`id`);

--
-- テーブルの制約 `delivery_initial_order`
--
ALTER TABLE `delivery_initial_order`
  ADD CONSTRAINT `delivery_initial_order_delivery_id_foreign` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `device_messages`
--
ALTER TABLE `device_messages`
  ADD CONSTRAINT `device_messages_from_device_id_foreign` FOREIGN KEY (`from_device_id`) REFERENCES `devices` (`id`),
  ADD CONSTRAINT `device_messages_from_user_id_foreign` FOREIGN KEY (`from_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `device_messages_to_device_id_foreign` FOREIGN KEY (`to_device_id`) REFERENCES `devices` (`id`),
  ADD CONSTRAINT `device_messages_to_user_id_foreign` FOREIGN KEY (`to_user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `document_images`
--
ALTER TABLE `document_images`
  ADD CONSTRAINT `document_images_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`);

--
-- テーブルの制約 `document_stocks`
--
ALTER TABLE `document_stocks`
  ADD CONSTRAINT `document_stocks_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`),
  ADD CONSTRAINT `document_stocks_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`);

--
-- テーブルの制約 `location_processes`
--
ALTER TABLE `location_processes`
  ADD CONSTRAINT `location_processes_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  ADD CONSTRAINT `location_processes_process_id_foreign` FOREIGN KEY (`process_id`) REFERENCES `processes` (`id`);

--
-- テーブルの制約 `notification_recipients`
--
ALTER TABLE `notification_recipients`
  ADD CONSTRAINT `notification_recipients_notification_setting_id_foreign` FOREIGN KEY (`notification_setting_id`) REFERENCES `notification_settings` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `notify_queue_users`
--
ALTER TABLE `notify_queue_users`
  ADD CONSTRAINT `notify_queue_users_notify_queue_id_foreign` FOREIGN KEY (`notify_queue_id`) REFERENCES `notify_queues` (`id`),
  ADD CONSTRAINT `notify_queue_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `order_requests`
--
ALTER TABLE `order_requests`
  ADD CONSTRAINT `order_requests_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`);

--
-- テーブルの制約 `order_request_approvals`
--
ALTER TABLE `order_request_approvals`
  ADD CONSTRAINT `order_request_approvals_order_request_id_foreign` FOREIGN KEY (`order_request_id`) REFERENCES `order_requests` (`id`),
  ADD CONSTRAINT `order_request_approvals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `pickups`
--
ALTER TABLE `pickups`
  ADD CONSTRAINT `pickups_staff_member_id_foreign` FOREIGN KEY (`staff_member_id`) REFERENCES `staff_members` (`id`);

--
-- テーブルの制約 `project_group_user`
--
ALTER TABLE `project_group_user`
  ADD CONSTRAINT `project_group_user_project_group_id_foreign` FOREIGN KEY (`project_group_id`) REFERENCES `project_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_group_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `schedule_events`
--
ALTER TABLE `schedule_events`
  ADD CONSTRAINT `schedule_events_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `schedule_participants`
--
ALTER TABLE `schedule_participants`
  ADD CONSTRAINT `schedule_participants_schedule_event_id_foreign` FOREIGN KEY (`schedule_event_id`) REFERENCES `schedule_events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedule_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `stock_images`
--
ALTER TABLE `stock_images`
  ADD CONSTRAINT `stock_images_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`);

--
-- テーブルの制約 `stock_requests`
--
ALTER TABLE `stock_requests`
  ADD CONSTRAINT `stock_requests_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`);

--
-- テーブルの制約 `stock_request_orders`
--
ALTER TABLE `stock_request_orders`
  ADD CONSTRAINT `stock_request_orders_process_id_foreign` FOREIGN KEY (`process_id`) REFERENCES `processes` (`id`),
  ADD CONSTRAINT `stock_request_orders_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`),
  ADD CONSTRAINT `stock_request_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `stock_supplier_prices`
--
ALTER TABLE `stock_supplier_prices`
  ADD CONSTRAINT `stock_supplier_prices_stock_id_foreign` FOREIGN KEY (`stock_id`) REFERENCES `stocks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_supplier_prices_stock_supplier_id_foreign` FOREIGN KEY (`stock_supplier_id`) REFERENCES `stock_suppliers` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `task_transactions`
--
ALTER TABLE `task_transactions`
  ADD CONSTRAINT `task_transactions_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`);

--
-- テーブルの制約 `task_update_checks`
--
ALTER TABLE `task_update_checks`
  ADD CONSTRAINT `task_update_checks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- テーブルの制約 `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`);

--
-- テーブルの制約 `user_schedules`
--
ALTER TABLE `user_schedules`
  ADD CONSTRAINT `user_schedules_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
