<script>
    window.BackupSpaceProftpd = {
        userid: {$userid},
        sign: "{$sign}"
    };
</script>
{include file="include/AdminEditOversellModal.tpl"}
<div class="col-md-12">
    <div id="tableBackground" class="tablebg">
        <table id="servers-list" width="100%" class="datatable no-margin ">
            <thead>
            <tr>
                <th>
                    хостнейм
                </th>
                <th>
                    ip
                </th>
                <th>
                    страна
                </th>
                <th>
                    количество аккаунтов
                </th>
                <th>
                    фактическое свободное место
                </th>
                <th>
                    место свободное учитывая квоты
                </th>
                <th>
                    фактическое занятое место
                </th>
                <th>
                    место занятое учитывая квоты
                </th>
                <th>
                    величина оверсела
                </th>
                <th style="width: 2%;"></th>
            </tr>
            </thead>
            <tbody>
            {foreach $servers as $id => $server}
                <tr style="cursor: pointer;">
                    <td data-id="{$id}">
                        {$server.hostname}
                    </td>
                    <td>
                        {$server.ip}
                    </td>
                    <td>
                        {$server.country}
                    </td>
                    <td>
                        {$server.account}
                    </td>
                    <td>
                        {$server.disk_free_without_quota}
                    </td>
                    <td>
                        {$server.disk_free}
                    </td>
                    <td>
                        {$server.disk_use_without_quota}
                    </td>
                    <td>
                        {$server.disk_use}
                    </td>
                    <td>
                        {$server.oversell}
                    </td>
                    <td>
                        <a href="#" data-toggle="modal" data-target="#EditModal"
                           data-id="{$id}"
                           title="Нажмите для редактирования">
                            <img src="/{{$customadminpath}}/images/edit.gif" border="0" alt="редактировать">
                        </a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
