/**
 * MainLayout のナビゲーション定義。
 * 巨大なコピペ class をデータ駆動に置き換えるためのソース。
 *
 * mainNav item:
 *   { key, label, icon, route, match, unused? }
 *   - match: route().current().includes(match) でアクティブ判定
 *   - unused: true の場合は showUnusedModuleNav が true の時のみ表示
 *
 * subNav[group] item:
 *   { label, icon, route, component, match, matchKey? }
 *   - component: 'a' | 'Link'（既存の <a>/<Link> 使い分けを踏襲）
 *   - match: 'exact' | 'startsWith'
 *   - matchKey: アクティブ判定に使うルート名。省略時は route を使用
 */

export const mainNav = [
    { key: 'master', label: '基幹マスタ管理', icon: 'database', route: 'master', match: 'master' },
    { key: 'stock', label: '在庫管理', icon: 'list_alt', route: 'stock', match: 'stock' },
    {
        key: 'delivery-signage',
        label: '納品サイネージ管理',
        icon: 'tv',
        route: 'delivery-signage.index',
        match: 'delivery-signage',
    },
    { key: 'lunch', label: '弁当注文', icon: 'restaurant', route: 'lunch', match: 'lunch' },
    // 現状未使用モジュール（showUnusedModuleNav=true で再表示）
    { key: 'movie', label: '動画視聴', icon: 'live_tv', route: 'movie2', match: 'movie2', unused: true },
    { key: 'fax', label: 'FAX振分設定', icon: 'description', route: 'fax', match: 'fax', unused: true },
    { key: 'signage', label: 'サイネージ', icon: 'tv', route: 'signage.home', match: 'signage', unused: true },
    { key: 'contact', label: '問い合わせ', icon: 'description', route: 'contact.home', match: 'contact', unused: true },
];

export const subNav = {
    master: [
        { label: '従業員・部署', icon: 'groups', route: 'master', component: 'Link', match: 'exact' },
        { label: 'カレンダー', icon: 'list_alt', route: 'master.calender', component: 'Link', match: 'exact' },
        { label: 'デバイス情報', icon: 'devices', route: 'master.devices', component: 'Link', match: 'startsWith', matchKey: 'master.devices' },
        { label: '承認フロー作成', icon: 'assignment', route: 'master.approval-flows.index', component: 'Link', match: 'startsWith', matchKey: 'master.approval-flows' },
    ],
    stock: [
        { label: '在庫追加', icon: 'edit_square', route: 'stock.stocks.create', component: 'a', match: 'exact' },
        { label: '新規品発注', icon: 'edit_square', route: 'stock.create.initialOrders', component: 'Link', match: 'exact' },
        { label: '取引先', icon: 'edit_square', route: 'stock.suppliers', component: 'a', match: 'exact' },
        { label: '格納先追加', icon: 'edit_square', route: 'stock.locations', component: 'a', match: 'exact' },
        { label: '在庫一覧', icon: 'list_alt', route: 'stock.stocks', component: 'a', match: 'exact' },
        { label: '発注依頼一覧', icon: 'list_alt', route: 'stock.order_requests', component: 'Link', match: 'exact' },
        { label: '発注一覧', icon: 'list_alt', route: 'stock.initialOrders', component: 'Link', match: 'exact' },
        { label: '滞留品', icon: 'list_alt', route: 'stock.retentions', component: 'Link', match: 'exact' },
        { label: '棚卸し', icon: 'list_alt', route: 'stock.stocks.taking', component: 'Link', match: 'exact' },
    ],
    lunch: [
        { label: '当日発注書', icon: 'description', route: 'lunch.order', component: 'Link', match: 'exact' },
        { label: '弁当予約', icon: 'event_available', route: 'lunch.reserve', component: 'Link', match: 'exact' },
        { label: '弁当注文履歴', icon: 'history', route: 'lunch.order-archive', component: 'a', match: 'exact' },
        { label: '備考作成', icon: 'edit_note', route: 'lunch.create_description', component: 'a', match: 'exact' },
        { label: '注文状況書き出し', icon: 'download', route: 'lunch.export_csv', component: 'a', match: 'exact' },
    ],
    message: [
        { label: '通知送信', icon: 'send', route: 'message', component: 'Link', match: 'exact' },
    ],
    // 未使用モジュール
    movie: [
        { label: '動画一覧', icon: 'list', route: 'movie2', component: 'Link', match: 'exact', unused: true },
        { label: '動画追加', icon: 'add', route: 'movie2.create', component: 'Link', match: 'exact', unused: true },
        { label: '動画タグ追加', icon: 'sell', route: 'movie2.categoryAndTag', component: 'Link', match: 'exact', unused: true },
    ],
    fax: [
        { label: 'マニュアル', icon: 'menu_book', route: 'fax.manual', component: 'Link', match: 'exact', unused: true },
        { label: '振り分け', icon: 'call_split', route: 'fax', component: 'Link', match: 'exact', unused: true },
        { label: 'グループ', icon: 'groups', route: 'fax.group', component: 'Link', match: 'exact', unused: true },
        { label: 'フォルダ割り当て', icon: 'folder', route: 'fax.folder', component: 'Link', match: 'exact', unused: true },
    ],
};

/** 未使用モジュールのサブナビグループ（showUnusedModuleNav 制御対象） */
export const unusedSubNavGroups = ['movie', 'fax'];
