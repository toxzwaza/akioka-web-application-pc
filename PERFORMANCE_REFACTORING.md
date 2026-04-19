# パフォーマンスリファクタリング計画

## 対象アプリ
- akioka-web-application-pc（PC版管理画面）
- akioka.cloud.iot_app（IoTタブレットアプリ）

## 検出された問題と対応状況

### 優先度1: N+1クエリ（ループ内個別クエリ）

| # | ファイル | 問題 | 状況 |
|---|---------|------|------|
| 1 | PC: OrderRequestController.php:126-156 | ループ内でStockSupplier, OrderRequestApproval, Document, DocumentImageを個別取得 | [ ] |
| 2 | PC: InitialOrderController.php:159-183 | ページネーション後のループ内でOrderRequestApproval, DocumentImageを個別取得 | [ ] |
| 3 | PC: StockTabletController.php:113-217 | 4メソッドでStock::find()をループ実行 | [ ] |
| 4 | PC: LunchController.php:242-250 | exportでユーザー毎にCOUNTクエリ実行 | [ ] |
| 5 | IoT: ReceiveController.php:50-66,156-165,259-268 | 3箇所でStock::find()をループ実行 | [ ] |
| 6 | IoT: ContentController.php:35-45 | FacilityScheduleParticipantをループ内取得 | [ ] |
| 7 | IoT: StockRequestController.php:206-213 | ループ内で個別save()（一括updateに変更可能） | [ ] |

### 優先度2: 全件取得・不要カラム取得

| # | ファイル | 問題 | 状況 |
|---|---------|------|------|
| 8 | PC: MainController.php:23-27 | ダッシュボードのカウントクエリにキャッシュなし | [ ] |
| 9 | PC: StockController.php:459-479 | マスタデータ全件取得（select未指定） | [ ] |
| 10 | PC: InitialOrderController.php:196-232 | フィルタ用マスタを全件取得 | [ ] |
| 11 | PC: NewMovieController.php:61-62 | base_moviesで全映画を全件取得 | [ ] |
| 12 | IoT: SearchController.php:22-24 | Process::all(), Classification::all() | [ ] |
| 13 | IoT: ReceiveController.php:462-469 | Classification::all(), Supplier::all() | [ ] |
| 14 | IoT: NewItemController.php:27-29 | Process::all(), Supplier::all() | [ ] |

### 優先度3: Inertiaへの大量データ渡し

| # | ファイル | 問題 | 状況 |
|---|---------|------|------|
| 15 | PC: InitialOrderController.php:237-248 | フィルタ用マスタ全件をInertiaに渡す | [ ] |
| 16 | PC: NewMovieController.php:65 | base_movies（全映画）をInertiaに渡す | [ ] |
| 17 | IoT: StockRequestController.php:37-45 | passwordを含むユーザー情報をInertiaに渡す | [ ] |

### 優先度4: クエリ最適化

| # | ファイル | 問題 | 状況 |
|---|---------|------|------|
| 18 | PC: StockController.php:352 | eager loadingとJOINの重複 | [ ] |
| 19 | IoT: SearchController.php:50-100 | JOIN内サブクエリ | [ ] |
| 20 | IoT: CalcController.php:120-140 | get()後にPHPでgroupBy | [ ] |

---

## 変更履歴

（修正完了後に記録）
