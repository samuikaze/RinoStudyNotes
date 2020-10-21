璃乃學習筆記 / RinoStudyNotes
===

<p align="center">⚠ 這個專案目前還在開發階段 ⚠</p>

這個專案是為 Line 機器人所寫的資料 API，做為後端資料對接用。

## TODO
1. ✅ 編輯使用者資料
2. ✅ 審核申請的使用者與停權/復權帳號
3. ▶️ 編輯 API 資料
    1. ▶️ 角色資料編輯
    2. ✅ 角色相關雜項資料編輯（刪除功能還沒）
    3. ❌ 版本管理
    4. ❌ 編輯使用者權限
4. ❌ API 清單
    1. 怎麼顯示？
    2. 手動編輯？
5. 還有很多...

## 說明
這個專案用於快速建置可以提供公主連結資料的 API 伺服器（不包含資料），提供的一個後台系統供新增和編輯資料，支援多人登入增修資料，此專案是以 Laravel 8.0 建置。

機器人講求的是回應快速，原本我是使用爬蟲爬各種資料，但是在經歷各種網站改版和回應過慢的問題後，還是決定自己寫一個提供資料的系統，再也不會因為網站改版或 Chrome 問題而煩惱要怎麼改 code 了

## 線上版本
~~由於這個專案就是我自己需要這些資料，所以目前可以直接從這邊存取資料。~~

由於原本的空間在 API 呼叫會檢查是不是瀏覽器，所以目前正在尋找更好的空間。

## 需求：
- php 7.3 或以上
- 至少包含一個下列其中一種資料庫
    - MySQL 5.6+
    - MariaDB
    - PostgreSQL 9.4+
    - SQLite 3.8.8+
    - SQL Server 2017+
    - 資料庫資訊參照 [Laravel 文件](https://laravel.com/docs/8.x/database)

## 安裝
- `git clone` 這個專案
- 將 `.env.example` 重新命名為 `.env`
- 把設定填進 `.env` 中
- 終端機執行 `composer install`
- 終端機執行 `npm install && npm run dev`
- 終端機執行 `php artisan migrate:refresh --seed`
- 終端機執行 `php artisan key:generate`
- 啟動網頁伺服器和資料庫或終端機執行 `php artisan serve`
- 注意，如果是要部屬到正式環境，部屬前請先執行 `npm run prod`
- 如要部屬到虛擬空間上，請注意須將所有流量重導到 public 資料夾下，下面以 Apache 為例：
    - 在根目錄新增 `.htaccess` 檔案。
    - 在檔案中輸入以下內容後儲存：
    ```xml
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteRule ^(.*)$ public/$1 [L]
    </IfModule>
    ```

## 使用
1. 後臺功能
    - 直接使用瀏覽器存取 `http://yourhostingurl/admin` 即可
    - 第一次安裝後如需登入管理員帳號請使用下面的帳號，並請登入後自行修改密碼
    ```
    使用者名稱: administrator
    密碼: 123
    ```
2. API 存取
    - 請加入以下列出的所有標頭
    ```HTTP
    Accept: application/json
    Content-Type: application/json
    X-Requested-With: XMLHttpRequest
    ```
    - API 路由見[網頁說明](http://smkzsite.byethost15.com/api/all)

## 授權
MIT License
