<script>
    window.BackupSpaceProftpd = {
        userid: {$userid},
        sign: "{$sign}"
    };
    $.ajax({
        type: "POST",
        url: "/?m=BackupSpaceProftpd",
        data: {
            action: 'get_email_template',
            user_id: window.BackupSpaceProftpd.userid,
            client_type: "admin",
            sign: window.BackupSpaceProftpd.sign,
        },
        dataType: 'json',
        success: function (data) {
            $("#clientDiskUsageNotifyEmailTemplate").val(data.data.email_template);
        },
        error: function (data) {
            $.notify("не удалось загрузить id шаблона", "error");
        },
    });
    $.ajax({
        type: "POST",
        url: "/?m=BackupSpaceProftpd",
        data: {
            action: 'get_email_notify',
            user_id: window.BackupSpaceProftpd.userid,
            client_type: "admin",
            sign: window.BackupSpaceProftpd.sign,
        },
        dataType: 'json',
        success: function (data) {
            if (data.data.email_notify === 'true') {
                $('#enableEmailNotify').prop('checked', true);
            } else {
                $('#enableEmailNotify').prop('checked', false);
            }
        },
        error: function (data) {
            $.notify("не удалось загрузить статус email уведомлений", "error");
        },
    });
</script>
<div class="col-md-5 ">
    <form>
        <div class="form-group" style="padding-top: 15px">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="enableEmailNotify">
                <label class="form-check-label" for="enableEmailNotify">
                    Включить email уведомления клиентов
                </label>
            </div>
        </div>

        <div class="form-group ">
            <label for="clientDiskUsageNotifyEmailTemplate">Шаблон email уведомлений клиента</label>
            <select class="form-control" id="clientDiskUsageNotifyEmailTemplate"
                    name="clientDiskUsageNotifyEmailTemplate">
                <option value="0" disabled>Не выбрано</option>
                {html_options options=$template}
            </select>
        </div>

    </form>
</div>
