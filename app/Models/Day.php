<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    public function serviceBodies() {
        return $this->hasMany(ServiceBody::class);
    }

    public function meetings() {
        return $this->hasMany(Meeting::class);
    }

    public function events() {
        return $this->hasMany(Event::class);
    }

    public function getEnNameAttribute($value)
    {
        return $value ?: $this->name;
    }

    public function getArNameAttribute($value)
    {
        if ($value) {
            return $value;
        }

        $days = [
            'Saturday' => 'السبت',
            'Sunday' => 'الأحد',
            'Monday' => 'الإثنين',
            'Tuesday' => 'الثلاثاء',
            'Wednesday' => 'الأربعاء',
            'Thursday' => 'الخميس',
            'Friday' => 'الجمعة',
        ];

        return $days[$this->name] ?? $this->name;
    }
}
