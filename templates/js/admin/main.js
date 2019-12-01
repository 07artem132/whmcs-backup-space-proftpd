/*
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 28.11.2019, 13:18
 *
 */

$(function () {


    $(document).on("click", "#servers-list tbody tr", function () {
        $('#EditModal').modal('show', $(this).find("td:first"));
    });

    $('#EditModal').on('shown.bs.modal', function (e) {
        let server_id = $(e.relatedTarget).data('id');
        $('#EditModalOversell').text("Редактирование сервера #" + server_id);
        $('#server_id').val(server_id);
        $('#oversell').val($.trim($(e.relatedTarget).closest('tr').find("td:eq(8)").text()));
    });

    $("#saveOversell").click(function () {
        $.ajax({
            type: "POST",
            url: "/?m=BackupSpaceProftpd",
            data: {
                action: 'edit_oversell',
                user_id: window.BackupSpaceProftpd.userid,
                client_type: "admin",
                sign: window.BackupSpaceProftpd.sign,
                server_id: $('#server_id').val(),
                oversell: $('#oversell').val(),
            },
            dataType: 'json',
            success: function (data) {
                $.notify("Изменения сохранены", "success");
                $("td[data-id='53']").parent().find("td:eq(8)").text($('#oversell').val());
            },
            error: function (data) {
                $.notify("Изменения не сохранены", "error");
            },
        })
    });

    $('#clientDiskUsageNotifyEmailTemplate').on('change', function () {
        $.ajax({
            type: "POST",
            url: "/?m=BackupSpaceProftpd",
            data: {
                action: 'edit_email_template',
                user_id: window.BackupSpaceProftpd.userid,
                client_type: "admin",
                sign: window.BackupSpaceProftpd.sign,
                template_id: $(this).children("option:selected").val(),
            },
            dataType: 'json',
            success: function (data) {
                $.notify("Изменения сохранены", "success");
            },
            error: function (data) {
                $.notify("Изменения не сохранены", "error");
            },
        })
    });

    $('#enableEmailNotify').on('change', function () {
        $.ajax({
            type: "POST",
            url: "/?m=BackupSpaceProftpd",
            data: {
                action: 'edit_email_notify',
                user_id: window.BackupSpaceProftpd.userid,
                client_type: "admin",
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
        })
    });
});
