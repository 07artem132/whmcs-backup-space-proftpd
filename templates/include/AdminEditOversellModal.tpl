<div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-labelledby="EditModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="EditModalOversell">Редактирование сервера</h4>
            </div>
            <div class="modal-body">
                <form>
                    <input type="hidden" id="server_id">
                    <div class="form-group">
                        <label for="oversell">Значение оверселлинга</label>
                        <input class="form-control" id="oversell">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                <button id="saveOversell" type="button" class="btn btn-primary" data-dismiss="modal">Сохранить</button>
            </div>
        </div>
    </div>
</div>