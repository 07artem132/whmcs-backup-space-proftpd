<?php
/**
 *  Created by PhpStorm.
 *  User: Артём
 *  Date time: 30.11.2019, 18:58
 *
 */
namespace WHMCS\Module\Addon\BackupSpaceProftpd\Models;

use Illuminate\Database\Eloquent\Builder;
use WHMCS\Model\AbstractModel;

/**
 * Class SettingsModel
 * @package WHMCS\Module\Addon\BackupSpaceProftpd\Models
 * @property string $key
 * @property string $val
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @mixin Builder
 */
class SettingsModel extends AbstractModel
{
    protected $table = "mod_addon_backup_space_proftpd_settings";
    protected $booleans = [];
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'key',
        'val'
    ];

}