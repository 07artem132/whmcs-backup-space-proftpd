<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 18:55
 *
 */

namespace WHMCS\Module\Addon\BackupSpaceProftpd\Models;

use WHMCS\Model\AbstractModel;

/**
 * Class NotifyQueueModel
 * @package WHMCS\Module\Addon\BackupSpaceProftpd\Models
 * @property int $id
 * @property int $service_id
 * @property int $usage
 * @property int $space
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin \Illuminate\Database\Query\Builder
 */
class NotifyModel extends AbstractModel
{
    protected $table = "mod_addon_backup_space_proftpd_notify";
    protected $booleans = [];
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'service_id',
        'status',
        'delay',
        'threshold'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function service()
    {
        return $this->hasOne("WHMCS\\Service\\Service", 'id', 'service_id');
    }

}