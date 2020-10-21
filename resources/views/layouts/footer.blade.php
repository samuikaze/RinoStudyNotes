<div class="text-secondary">
    <div class="d-flex">
        <div class="justify-content-start col-6 p-0">
            &copy; 2020 璃乃學習筆記<br>
            <template v-if="!loading && versionId != null">
                <a href="/version/history">Version @{{ versionId }}</a>
            </template>
            <template v-if="!loading && versionId == null">
                <a href="/version/history">暫時無法取得版本號碼</a>
            </template>
            <template v-if="loading">
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                &nbsp;讀取中
            </template>
            ・<a href="https://github.com/samuikaze/PCRedive-DataAPI">GitHub</a>
        </div>
        <div class="justify-content-end text-right col-6 p-0">
            本站為個人架設，與 Cygames 、So-net Taiwan 無任何關係<br>站上或 API 中所提供的資料、商標其版權所有皆屬原版權持有者或提供者所有
        </div>
    </div>
</div>
