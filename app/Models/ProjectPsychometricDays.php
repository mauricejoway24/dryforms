<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed outside
 * @property mixed unaffected
 * @property mixed affected
 * @property mixed dehumidifier
 * @property mixed hvac
 * @property string current_time
 */
class ProjectPsychometricDays extends Model
{
    const DEFAULT_MEASUREMENTS = [
        'temp' => null,
        'rh' => null,
        'gpp' => null,
        'dew' => null,
    ];

    /**
     * @var string
     */
    public $table = 'project_psychometric_days';

    /**
     * @var array
     */
    public $fillable = [
        'area_id',
        'containment_id',
        'current_time',
        'outside',
        'unaffected',
        'affected',
        'dehumidifier',
        'hvac'
    ];

    /**
     * @var array
     */
    public $visible = [
        'id',
        'area_id',
        'containment_id',
        'current_time',
        'outside',
        'unaffected',
        'affected',
        'dehumidifier',
        'hvac'
    ];

    /**
     * @param int $areaId
     * @param null|string $currentDate
     * @param int|null $containmentId
     * @return ProjectPsychometricDays
     */
    public function createDefault(int $areaId, ?string $currentDate = null, ?int $containmentId = null): self
    {
        $model = $this->create([
            'area_id' => $areaId,
            'current_time' => $currentDate ? $currentDate : date('Y-m-d'),
            'containment_id' => $containmentId,
            'outside' => static::DEFAULT_MEASUREMENTS,
            'unaffected' => static::DEFAULT_MEASUREMENTS,
            'affected' => static::DEFAULT_MEASUREMENTS,
            'dehumidifier' => static::DEFAULT_MEASUREMENTS,
            'hvac' => static::DEFAULT_MEASUREMENTS
        ]);

        return $model;
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getOutsideAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * @param $value
     */
    public function setOutsideAttribute($value)
    {
        $this->attributes['outside'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getUnaffectedAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * @param $value
     */
    public function setUnaffectedAttribute($value)
    {
        $this->attributes['unaffected'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getAffectedAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * @param $value
     */
    public function setAffectedAttribute($value)
    {
        $this->attributes['affected'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getDehumidifierAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * @param $value
     */
    public function setDehumidifierAttribute($value)
    {
        $this->attributes['dehumidifier'] = json_encode($value);
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function getHvacAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * @param $value
     */
    public function setHvacAttribute($value)
    {
        $this->attributes['hvac'] = json_encode($value);
    }
}
