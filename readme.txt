=== Highlighting Code Block ===
Contributors: looswebstudio
Donate link: https://wemo.tech/2122/
Tags: SyntaxHighlighter, syntax highlighting, sourcecode, block-editor, classic editor, Guternberg,
Requires at least: 4.6
Tested up to: 5.3
Stable tag: 1.0.7
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

「Highlighting Code Block」は、綺麗にシンタックスハイライトされたコードブロックをクリックだけで追加できるようにします。

== Description ==

「Highlighting Code Block」は、綺麗にシンタックスハイライトされたコードブロックをクリックだけで追加できるようにします。

新旧エディタ両方に対応しており、どちらのエディタでもコードの記述をサポートいたします。

当プラグインの詳細な説明については[ こちらのページ ](https://wemo.tech/2122/)をご覧ください。


= インストール・有効化したら =

- ブロックエディタでは、「フォーマット」のカテゴリーの中に「Highlighting Code Block」という名前のコードブロックが用意されているはずです。
「Highlighting Code Block」を選択したら、セレクトボックス付きのコードブロックが出現します。
お好きな言語を選び、コードを記述してください。
- クラシックエディタでは、ツールバー（デフォルトでは２段目）に「コードブロック」と書かれたセレクトボックスが出現しているはずです。
こちらのセレクトボックスからお好きな言語を選択すると、コードブロック（preタグ）が挿入されます。
すでに入力済みのテキストを選択した状態でコードブロックを選択することも可能です。自動でラッピングされます。（改行が含まれる場合はうまくラッピングできません。）


= 動作しない？ =

当プラグインはPHPバージョン5.6以降でのみ動作します。
ご使用のPHPバージョンをご確認ください。



= 各種設定 =

管理画面の左メニュー「設定」項目の中、「CODE BLOCK」という項目から設定を変更できます。


== Installation ==


= 自動インストール =
1. プラグインの検索フィールドより「Highlighting Code Block」と入力します。
2. 当プラグインを見つけたら、"今すぐインストール"をクリックしてインストールし、有効化してください。

= 手動インストール =
1.「highlighting-code-block」フォルダ全体を /wp-content/plugins/ ディレクトリにアップロードします。
2.「プラグイン」メニューからプラグインを有効化します。


== Frequently Asked Questions ==

= 使用可能な言語について =
デフォルトでは以下の言語が使用可能です。
  - HTML
  - CSS
  - SCSS
  - JavaScript
  - TypeScript
  - PHP
  - Ruby
  - Python
  - Swift
  - C
  - C#
  - C++
  - Objective-C
  - SQL
  - JSON
  - Bash
  - Git


== Screenshots ==

1. Code Coloring
2. Select 「Highlighing Code Block
3. Select lang (Guternberg)
4. Writing your code (Guternberg)
5. Added select box (Tinymce)
6. Select lang (Tinymce)
7. Writing your code (Tinymce)
8. ex) Light color
9. ex) Dark color
10. Base Setting
11. Higher Setting


== Changelog ==

= 1.0.7 =
- WordPress5.3に対応。
- HCBのコードブロックの左右marginを削除し、テーマに任せるように変更。
- コアの「ソースコードブロック」との変換を可能に。
- ブロックの実装コードを修正。

= 1.0.6 =
- 読み込むCSSファイルを圧縮
- 一部head内でstyleタグで読み込むように変更
- フォントファミリーの設定が反映されない不具合を修正
- その他、CSSやJSのコード整理

= 1.0.5 =
scriptの読み込みをwp_footerへ移動
HCBブロックのロゴを変更
コードブロックのfont-familyが変更を指定できるようになりました。

= 1.0.4 =
WordPress5.2.1への対応
コードブロックのfont-family変更（windowsのフォントが読みにくかったので）
コードブロックにファイル名を設定できるようにしました

= 1.0.3 =
WordPress5.1.1への対応

= 1.0.2 =
Change Readme.txt

= 1.0.1 =
Comment delete.

= 1.0 =
Initial working version.
