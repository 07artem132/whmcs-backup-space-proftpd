{if $systemStatus == 'Active'}
    <div class='row'>
        <div class='col-md-12 no-bs-padding'>

            <div class="col-md-6">
                <div class="product-details">
                    <div class="product-status product-status-{$rawstatus|strtolower}">
                        <div class="product-icon text-center">
                            <span class="fa-stack fa-lg">
                                <i class="fa fa-circle fa-stack-2x"></i>
                                <i class="fa fa-hdd fa-hdd-o fa-stack-1x fa-inverse"></i>
                            </span>
                            <h3>{$product}</h3>
                            <h4>{$groupname}</h4>
                        </div>
                        <div class="product-status-text">
                            {$status}
                        </div>
                    </div>

                    {if $showcancelbutton}
                        <div class="row">
                            {if $showcancelbutton}
                                {if $availableAddonProducts}
                                    <div class="col-xs-12">
                                        <a href="clientarea.php?action=cancel&amp;id={$id}"
                                           class="btn btn-block btn-danger {if $pendingcancellation}disabled{/if}">{if $pendingcancellation}{$LANG.cancellationrequested}{else}{$LANG.clientareacancelrequestbutton}{/if}</a>
                                    </div>
                                {/if}
                            {/if}
                        </div>
                    {/if}
                </div>
            </div>
            {if empty($error)}
                <div class="col-md-6">
                    <div class="panel panel-default" id="TrafficUsagePanel" style='margin-bottom: 15px;'>
                        <div class="panel-heading">
                            <h3 class="panel-title text-center">{$LANG.cPanel.diskUsage}</h3>
                        </div>
                        <div class="panel-body text-center">
                            <div class="row">
                                <div class='col-md-8 col-md-offset-2 no-bs-padding'
                                     style='margin-top: -40px; margin-bottom: -20px;'>
                                    <div id="diskusageCycle"></div>
                                </div>
                            </div>
                            <div>
                                <span style='font-size: 16px;'>{$diskspaceUsed} / {$diskspaceTotal}</span>
                            </div>
                        </div>
                    </div>
                </div>
                {if $availableAddonProducts}
                    <div class='col-md-6'>
                        <div class="panel panel-default" id="ExtrasPurchasePanel">
                            <div class="panel-heading">
                                <h3 class="panel-title text-center">{$LANG.cPanel.addonsExtras}</h3>
                            </div>
                            <div class="panel-body text-center">
                                <form method="post" action="cart.php?a=add" class="form-inline">
                                    <input type="hidden" name="serviceid" value="{$serviceid}">
                                    <select name="aid" class="form-control input-sm margin-bottom-5px"
                                            style='width:70%;'>
                                        {foreach $availableAddonProducts as $addonId => $addonName}
                                            <option value="{$addonId}">{$addonName}</option>
                                        {/foreach}
                                    </select>
                                    <button type="submit" class="btn btn-default btn-sm">
                                        <i class="fa fa-shopping-cart"></i> {$LANG.cPanel.purchaseActivate}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                {/if}

                {if $showcancelbutton}
                    {if ! $availableAddonProducts}
                        <div class="col-xs-6">
                            <a href="clientarea.php?action=cancel&amp;id={$id}"
                               class="btn btn-block btn-danger {if $pendingcancellation}disabled{/if}">{if $pendingcancellation}{$LANG.cancellationrequested}{else}{$LANG.clientareacancelrequestbutton}{/if}</a>
                        </div>
                    {/if}
                {/if}
            {/if}
        </div>
    </div>
    {foreach $hookOutput as $output}
        <div class='row'>
            <div class='col-md-12'>
                {$output|unescape:'html'}
            </div>
        </div>
    {/foreach}

    {if $error}
        <div class='col-md-12'>
            <div class='alert alert-danger'>{$error}</div>
        </div>
    {/if}
    <div class='row' style='margin-top: 20px;'>
        <div class='col-md-12' style='margin-top: 20px;' id='ServerInformations'>
            <div class="panel panel-nav-tabs panel-default">
                <div class="panel-heading">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#accessdetails" data-toggle="tab">
                                <i class="fas fa-user-circle"></i> Основная информация
                            </a>
                        </li>
                        <li>
                            <a href="#notifysetting" data-toggle="tab">
                                <i class="far fa-bell"></i> Настройка уведомлений
                            </a>
                        </li>
                        {if $configurableoptions || $customfields}
                            <li>
                                <a href="#configoptions" data-toggle="tab">
                                    <i class="fas fa fa-cubes fa-fw"></i> {$LANG.orderconfigpackage}
                                </a>
                            </li>
                        {/if}
                    </ul>
                </div>

                <div class="panel-body">
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="accessdetails">
                            <div class='col-md-12 no-bs-padding'>
                                <div class='col-md-6'>
                                    <h4 class='padding-bottom-5px'>Данные для подключения</h4>
                                    <table class='table'>
                                        <tbody>
                                        {if $domain}
                                            <tr>
                                                <td>{$LANG.serverhostname}</td>
                                                <td>{$domain}</td>
                                            </tr>
                                        {/if}
                                        {if $dedicatedip}
                                            <tr>
                                                <td>{$LANG.primaryIP}</td>
                                                <td>{$dedicatedip}</td>
                                            </tr>
                                        {/if}
                                        {if $username}
                                            <tr>
                                                <td>{$LANG.serverusername}</td>
                                                <td>{$username}</td>
                                            </tr>
                                        {/if}
                                        {if $password}
                                            <tr>
                                                <td>{$LANG.serverpassword}</td>
                                                <td>{$password}</td>
                                            </tr>
                                        {/if}
                                        {if 'ftp'|array_key_exists:$allow_protocol}
                                            <tr>
                                                <td>Порт ftp</td>
                                                <td>21</td>
                                            </tr>
                                        {/if}
                                        {if 'scp'|array_key_exists:$allow_protocol}
                                            <tr>
                                                <td>Порт SCP/SFTP</td>
                                                <td>22</td>
                                            </tr>
                                        {/if}
                                        </tbody>
                                    </table>
                                </div>
                                <div class='col-md-6'>
                                    <h4 class='padding-bottom-5px'>Информация об услуге</h4>
                                    <table class='table'>
                                        <tbody>
                                        <tr>
                                            <td>{$LANG.clientareahostingregdate}</td>
                                            <td>{$regdate}</td>
                                        </tr>
                                        <tr>
                                            <td>{$LANG.orderbillingcycle}</td>
                                            <td>{$billingcycle}</td>
                                        </tr>
                                        <tr>
                                            <td>{$LANG.clientareahostingnextduedate}</td>
                                            <td>{$nextduedate}</td>
                                        </tr>
                                        {if $billingcycle != $LANG.orderpaymenttermonetime && $billingcycle != $LANG.orderfree}
                                            <tr>
                                                <td>{$LANG.recurringamount}</td>
                                                <td>{$recurringamount}</td>
                                            </tr>
                                        {/if}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="configoptions">
                            <div class='col-md-12 no-bs-padding'>
                                {if $configurableoptions}
                                    <div class='col-md-6'>
                                        <h4 class='padding-bottom-5px'>{$LANG.orderconfigpackage}</h4>
                                        <table class='table'>
                                            <tbody>
                                            {foreach from=$configurableoptions item=configoption}
                                                <tr>
                                                    <td>{$configoption.optionname}</td>
                                                    <td>{if $configoption.optiontype eq 3}{if $configoption.selectedqty}{$LANG.yes}{else}{$LANG.no}{/if}{elseif $configoption.optiontype eq 4}{$configoption.selectedqty} x {$configoption.selectedoption}{else}{$configoption.selectedoption}{/if}</td>
                                                </tr>
                                            {/foreach}
                                            </tbody>
                                        </table>
                                    </div>
                                {/if}
                                {if $customfields}
                                    <div class='col-md-6'>
                                        <h4 class='padding-bottom-5px'>{$LANG.additionalInfo}</h4>
                                        <table class='table'>
                                            <tbody>
                                            {foreach from=$customfields item=field}
                                                <tr>
                                                    <td>{$field.name}</td>
                                                    <td>
                                                        {if empty($field.value)}
                                                            {$LANG.blankCustomField}
                                                        {else}
                                                            {$field.value}
                                                        {/if}
                                                    </td>
                                                </tr>
                                            {/foreach}
                                            </tbody>
                                        </table>
                                    </div>
                                {/if}
                            </div>
                        </div>
                        <div class="tab-pane fade" id="notifysetting">
                            <div class='col-md-12 no-bs-padding'>
                                <div class='col-md-7'>
                                    <h4 class='padding-bottom-5px'>Настройка уведомлений</h4>
                                    <table class='table'>
                                        <tbody>
                                        <tr>
                                            <td style="width: 190px;">Уведомления</td>
                                            <td>
                                                <input id="notifyStatus" type="checkbox" data-toggle="toggle"
                                                       data-on="Включены" data-off="Отключены" data-width="100"
                                                       data-size="mini">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 190px;">Если занято больше чем</td>
                                            <td>
                                                <select id="thresholdForNotification"
                                                        class="custom-select custom-select-sm">
                                                    <option value="0" disabled selected>Выберите порог уведомлений
                                                    </option>
                                                    <option value="80">80%</option>
                                                    <option value="85">85%</option>
                                                    <option value="90">90%</option>
                                                    <option value="95">95%</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 190px;">Уведомлять</td>
                                            <td>
                                                <select id="NotificationDelay" class="custom-select custom-select-sm">
                                                    <option value="0" disabled selected>Выберите частоту уведомлений
                                                    </option>
                                                    <option value="1">Каждый час</option>
                                                    <option value="3">Каждые 3 часа</option>
                                                    <option value="6">Каждые 6 часов</option>
                                                    <option value="12">Каждые 12 часов</option>
                                                    <option value="24">Каждые 24 часа</option>
                                                </select>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {literal}
        <script>
            $.ajaxSetup({
                timeout: 60000
            });

            {/literal}
            window.BackupSpaceProftpd = {
                user_id: {$user_id},
                service_id: {$service_id},
                sign: "{$sign}"
            };
            {literal}

            $(function () {
                $.ajax({
                    type: "POST",
                    url: "/?m=BackupSpaceProftpd",
                    data: {
                        action: 'get_email_user_notify_threshold',
                        user_id: window.BackupSpaceProftpd.user_id,
                        service_id: window.BackupSpaceProftpd.service_id,
                        client_type: "user",
                        sign: window.BackupSpaceProftpd.sign,
                    },
                    dataType: 'json',
                    success: function (data) {
                        $("#thresholdForNotification").val(data.data.threshold);
                    },
                    error: function (data) {
                        $.notify("не удалось загрузить id шаблона", "error");
                    },
                });
                $.ajax({
                    type: "POST",
                    url: "/?m=BackupSpaceProftpd",
                    data: {
                        action: 'get_email_user_notify_delay',
                        user_id: window.BackupSpaceProftpd.user_id,
                        service_id: window.BackupSpaceProftpd.service_id,
                        client_type: "user",
                        sign: window.BackupSpaceProftpd.sign,
                    },
                    dataType: 'json',
                    success: function (data) {
                        $("#NotificationDelay").val(data.data.delay);
                    },
                    error: function (data) {
                        $.notify("не удалось загрузить id шаблона", "error");
                    },
                });
                $.ajax({
                    type: "POST",
                    url: "/?m=BackupSpaceProftpd",
                    data: {
                        action: 'get_email_user_notify',
                        user_id: window.BackupSpaceProftpd.user_id,
                        service_id: window.BackupSpaceProftpd.service_id,
                        client_type: "user",
                        sign: window.BackupSpaceProftpd.sign,
                    },
                    dataType: 'json',
                    success: function (data) {
                        if (data.data.status === 1) {
                            $('#notifyStatus').bootstrapToggle('destroy')
                                .prop('checked', true)
                                .bootstrapToggle();
                        } else {
                            $('#notifyStatus').bootstrapToggle('destroy')
                                .prop('checked', false)
                                .bootstrapToggle();
                        }
                    },
                    error: function (data) {
                        $.notify("не удалось загрузить статус email уведомлений", "error");
                    },
                });


                $('#notifyStatus').change(function () {
                    $.ajax({
                        type: "POST",
                        url: "/?m=BackupSpaceProftpd",
                        data: {
                            action: 'edit_email_user_notify',
                            user_id: window.BackupSpaceProftpd.user_id,
                            service_id: window.BackupSpaceProftpd.service_id,
                            client_type: "user",
                            sign: window.BackupSpaceProftpd.sign,
                            status: $(this).prop('checked'),
                        },
                        dataType: 'json',
                        success: function (data) {
                            $.notify("Изменения сохранены", "success");
                        },
                        error: function (data) {
                            $.notify("Изменения не сохранены", "error");
                        },
                    });
                });

                $('#thresholdForNotification').change(function () {
                    $.ajax({
                        type: "POST",
                        url: "/?m=BackupSpaceProftpd",
                        data: {
                            action: 'edit_email_user_notify_threshold',
                            user_id: window.BackupSpaceProftpd.user_id,
                            service_id: window.BackupSpaceProftpd.service_id,
                            client_type: "user",
                            sign: window.BackupSpaceProftpd.sign,
                            threshold: $(this).children("option:selected").val(),
                        },
                        dataType: 'json',
                        success: function (data) {
                            $.notify("Изменения сохранены", "success");
                        },
                        error: function (data) {
                            $.notify("Изменения не сохранены", "error");
                        },
                    });
                });
                $('#NotificationDelay').change(function () {
                    $.ajax({
                        type: "POST",
                        url: "/?m=BackupSpaceProftpd",
                        data: {
                            action: 'edit_email_user_notify_delay',
                            user_id: window.BackupSpaceProftpd.user_id,
                            service_id: window.BackupSpaceProftpd.service_id,
                            client_type: "user",
                            sign: window.BackupSpaceProftpd.sign,
                            delay: $(this).children("option:selected").val(),
                        },
                        dataType: 'json',
                        success: function (data) {
                            $.notify("Изменения сохранены", "success");
                        },
                        error: function (data) {
                            $.notify("Изменения не сохранены", "error");
                        },
                    });
                });
            });

            $(document).ready(function () {
                $('#diskusageCycle').circliful({
                    animation: 0,
                    animationStep: 15,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    icon: 'f0a0',
                    percentageTextSize: 16,
                    percent: {/literal}{$diskspaceUsedInPercent}{literal},
                });
            });
        </script>
        {/literal}
    </div>
{elseif $systemStatus=='Suspended'}
    <div class='col-md-12'>
        <div class='alert alert-warning'>Услуга отключена за неуплату</div>
    </div>
{/if}

<link rel='stylesheet' type='text/css' href='modules/servers/BackupSpaceProftpd/templates/assets/style.css?v=5'>
<link rel='stylesheet' type='text/css'
      href='modules/servers/BackupSpaceProftpd/templates/assets/bootstrap-table.min.css'>
<link rel='stylesheet' href='modules/servers/BackupSpaceProftpd/templates/assets/jquery.circliful.css'>
<link rel='stylesheet' href='modules/servers/BackupSpaceProftpd/templates/assets/bootstrap-toggle.min.css'>
<link rel='stylesheet' href='modules/servers/BackupSpaceProftpd/templates/assets/font-awesome.min.css'>
<script src='modules/servers/BackupSpaceProftpd/templates/assets/bootstrap-table.min.js'></script>
<script src='modules/servers/BackupSpaceProftpd/templates/assets/bootstrap-table-en-US.min.js'></script>
<script src='modules/servers/BackupSpaceProftpd/templates/assets/bootstrap-toggle.min.js'></script>
<script src='modules/servers/BackupSpaceProftpd/templates/assets/jquery.circliful.min.js'></script>
<script src="modules/servers/BackupSpaceProftpd/templates/assets/notify.min.js"></script>
