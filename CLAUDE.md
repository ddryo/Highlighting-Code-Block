# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## プロジェクト概要

**Highlighting Code Block** — WordPress 用のシンタックスハイライト付きコードブロックプラグイン（v2.1.0）。Gutenberg ブロックエディタおよびクラシックエディタ（TinyMCE）の両方に対応し、フロントエンドは Prism.js によるハイライトを提供する。

-   プラグインスラッグ: `highlighting-code-block`
-   テキストドメイン: `highlighting-code-block`（PHP）/ `loos-hcb`（ブロック）
-   ブロック名: `loos-hcb/code-block`

## 開発コマンド

```bash
# ローカル WordPress 環境
npx wp-env start          # .wp-env.json ベースで起動

# ビルド
nr build                  # JS + CSS 両方ビルド
nr build:js               # wp-scripts build（Webpack）
nr build:css              # node sass-builder.js（SCSS → PostCSS）
nr start                  # wp-scripts start（JS のみ watch）

# リント
composer phpcs             # PHP CodeSniffer（phpcs.xml 設定）

# 翻訳
nr po2json                # PO → JSON（ブロック用翻訳ファイル生成）
```

## アーキテクチャ

### PHP（`class/`）

プラグインのエントリポイントは `highlighting-code-block.php`。SPL autoloader で `LOOS_HCB` プレフィックスのクラスを `class/` から読み込む。

| クラス                | 責務                                          |
| --------------------- | --------------------------------------------- |
| `LOOS_HCB`            | 設定管理、定数定義、各クラスのインスタンス化  |
| `LOOS_HCB_Scripts`    | ブロック登録、フロント/エディタのアセット登録 |
| `LOOS_HCB_Menu`       | 管理画面の設定ページ UI                       |
| `LOOS_HCB_MCE`        | クラシックエディタ（TinyMCE）統合             |
| `LOOS_HCB_Activation` | プラグインの有効化/アンインストール処理       |

設定は `loos_hcb_settings` オプションにシリアライズ配列として保存される。

### JavaScript（`src/js/`）

Webpack エントリポイントは2つ:

-   **`hcb_script.js`** → `build/js/hcb_script.js` — フロントエンド用。Prism.js 初期化、行ハイライト、クリップボードコピー
-   **`code-block/index.js`** → `build/js/code-block.js` — Gutenberg ブロックの登録・エディタ UI（React）

`webpack.config.js` でエイリアス `@blocks` → `src/blocks/` が定義されている。`mode` は常に `production`（start 時含む）。

### SCSS（`src/scss/`）

`sass-builder.js` による独自ビルド。`_` プレフィックスのパーシャルを除いた全 `.scss` が `build/css/` にコンパイルされる。PostCSS（autoprefixer, css-mqpacker, cssnano）で後処理。

テーマ構成:

-   `hcb.scss` — ベーススタイル（カラーなし）
-   `hcb--light.scss` / `hcb--dark.scss` — フロントのカラーテーマ
-   `hcb-editor--light.scss` / `hcb-editor--dark.scss` — エディタのカラーテーマ
-   `hcb-admin.scss` — 管理画面の設定ページ

CSS カスタムプロパティ（`--hcb--*`）でフォントサイズ、カラー、パディング等を制御。

### 静的アセット（`assets/`）

-   `assets/js/prism.js` — Prism.js 本体（設定画面からカスタム URL に差し替え可能）
-   `assets/js/hcb_mce_button.js` — TinyMCE ボタンプラグイン
-   `assets/img/` — SVG アイコン（クリップボード、ファイルアイコン等）

## ドキュメント

- `README.md` — GitHub リポジトリ用
- `readme.txt` — WordPress.org プラグインディレクトリ用（Stable tag、チェンジログ等）

## バージョン更新手順

1. `readme.txt` の Stable tag を更新
2. `readme.txt` のチェンジログを追加
3. `highlighting-code-block.php` のプラグインヘッダコメントのバージョンを更新
4. `highlighting-code-block.php` の `LOOS_HCB_VER` 定数を更新

翻訳ファイルを更新した場合は `nr po2json` で JSON を再生成する。

## デプロイ

GitHub で tag を push すると `.github/workflows/deploy.yml` が実行され、WordPress.org SVN に自動デプロイされる。`.distignore` で除外ファイルを管理。
