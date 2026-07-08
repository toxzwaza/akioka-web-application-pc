const defaultTheme = require('tailwindcss/defaultTheme');

/**
 * Akioka Web Application — Design System Tokens
 * -------------------------------------------------
 * ビジュアル方針: コーポレート × フラット × Bento UI / ネイビー基調 / 白背景
 * 重要: Tailwind標準の blue-* / gray-* 等は「上書きしない」。
 *       セマンティックトークンは加算のみ（既存の直書きクラスを壊さないため）。
 * 詳細ルール: doc/design-system.md
 */

/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                // 日本語UIの主フォント。従来の英字向けFigtreeを是正。
                sans: ['"Noto Sans JP"', ...defaultTheme.fontFamily.sans],
                // 見出し等で任意利用（丸ゴシック）
                display: ['"Zen Maru Gothic"', '"Noto Sans JP"', ...defaultTheme.fontFamily.sans],
            },

            colors: {
                // Primary: メイン/主アクション（ネイビー）
                primary: {
                    50: '#eef2f8',
                    100: '#d9e2f0',
                    200: '#b3c5e0',
                    300: '#8098c8',
                    400: '#5470a4',
                    500: '#3a577f',
                    600: '#2f4d78', // 主アクション（ボタン等）
                    700: '#264062',
                    800: '#1f3350',
                    900: '#182741', // ヘッダー等の濃色
                    950: '#0f1a2e',
                    DEFAULT: '#2f4d78',
                },

                // Surface: 背景 / パネル / 沈み
                surface: {
                    base: '#ffffff',
                    muted: '#f8fafc', // slate-50
                    sunken: '#f1f5f9', // slate-100
                },

                // Content: 文字
                content: {
                    DEFAULT: '#1e293b', // slate-800 本文
                    muted: '#64748b', // slate-500 補足
                    subtle: '#94a3b8', // slate-400 微弱
                    inverse: '#ffffff', // 反転（濃色背景上）
                },

                // Border: 罫線
                border: {
                    DEFAULT: '#e2e8f0', // slate-200
                    strong: '#cbd5e1', // slate-300
                },

                // 状態色
                success: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    DEFAULT: '#16a34a',
                },
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    DEFAULT: '#d97706',
                },
                error: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    DEFAULT: '#dc2626',
                },
                info: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    DEFAULT: '#2563eb',
                },
            },

            borderRadius: {
                // Bento UI 向けの意味付きトークン
                card: '0.75rem',
                badge: '0.375rem',
            },

            boxShadow: {
                // フラット方針: 浅い影のみ
                card: '0 1px 3px 0 rgb(15 23 42 / 0.08), 0 1px 2px -1px rgb(15 23 42 / 0.06)',
                'card-hover': '0 4px 12px -2px rgb(15 23 42 / 0.12), 0 2px 6px -2px rgb(15 23 42 / 0.08)',
            },
        },
    },

    plugins: [require('@tailwindcss/forms')],
};
