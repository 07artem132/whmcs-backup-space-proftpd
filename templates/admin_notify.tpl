<script>
    $(function () {
        var table1 = jQuery("#notify-list").DataTable({
            "ordering": false,
            "dom": '<"listtable"fit>pl',
            "responsive": true,
            "oLanguage": {
                "sEmptyTable": "Записей не найдено",
                "sInfo": "Показано с _START_ по _END_ из _TOTAL_",
                "sInfoEmpty": "Показано с 0 по 0 из 0",
                "sInfoFiltered": "(отфильтровано из _MAX_ записей)",
                "sInfoPostFix": "",
                "sInfoThousands": ",",
                "sLengthMenu": "Показать _MENU_ записей",
                "sLoadingRecords": "Загрузка...",
                "sProcessing": "Обработка...",
                "sSearch": "",
                "sZeroRecords": "Записей не найдено",
                "oPaginate": {
                    "sFirst": "Первая",
                    "sLast": "Последняя",
                    "sNext": "Вперед",
                    "sPrevious": "Назад"
                }
            },
            "pageLength": 100,
            "lengthMenu": [
                [50, 100, 500, -1],
                [50, 100, 500, "Все"]
            ], "stateSave": true
        });
    });
</script>
<style>
    div#notify-list_length {
        float: left;
    }

    div#notify-list_paginate {
        float: right;
    }

    div#notify-list_filter {
        float: right;
    }

    div#notify-list_info {
        float: left;
    }
</style>
<div class="col-md-12">
    <div id="tableBackground" class="tablebg">
        <table id="notify-list" width="100%" class="datatable no-margin ">
            <thead>
            <tr>
                <th>
                    id услуги
                </th>
                <th>
                    Статус
                </th>
                <th>
                    Частота
                </th>
                <th>
                    Сейчас занято
                </th>
                <th>
                    Порог для уведомления
                </th>
                <th>
                    Последнее уведомление
                </th>
            </tr>
            </thead>
            <tbody>
            {foreach $notifications as $id => $notify}
                <tr>
                    <td data-id="{$id}">
                        <a href="clientsservices.php?productselect={$notify.service_id}">{$notify.service_id}</a>
                    </td>
                    <td>
                        {if $notify.status eq 1}
                            Включены
                        {else}
                            Отключены
                        {/if}
                    </td>
                    <td>
                        {$notify.delay} час
                    </td>
                    <td>
                        {$notify.usePercentage}%
                    </td>
                    <td>
                        {$notify.threshold}%
                    </td>
                    <td>
                        {$notify.last_notify}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
