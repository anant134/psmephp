<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

trait ModelExtender
{
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if ($model->hasField('created_by')) {
                $model->created_by = app()->request->user_id;
            }
        });
        static::updating(function ($model) {
            if ($model->hasField('updated_by')) {
                $model->updated_by = app()->request->user_id;
            }
        });
    }

    public function hasField($field)
    {
        if (isset($this->auto_fillable) && is_array($this->auto_fillable) && in_array($field, $this->auto_fillable)) {
            return true;
        }
        return false;
    }

    public static function getColumns()
    {
        return static::getSchemaBuilder()->getColumnListing();
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public static function customInsert(array $values)
    {
        $column_list = DB::getSchemaBuilder()->getColumnListing(self::getTableName());
        if (in_array('created_by', $column_list)) {
            foreach ($values as $key => $value) {
                if (!isset($values[$key]['created_by']))
                    $values[$key]['created_by'] = app()->request->user_id;
            }
        }
        if (in_array('updated_by', $column_list)) {
            foreach ($values as $key => $value) {
                if (!isset($values[$key]['updated_by']))
                    $values[$key]['updated_by'] = app()->request->user_id;

            }
        }
        if (in_array('created_at', $column_list)) {
            foreach ($values as $key => $value) {
                if (!isset($values[$key]['created_at']))
                    $values[$key]['created_at'] = date('Y-m-d H:i:s');

            }
        }
        if (in_array('updated_at', $column_list)) {
            foreach ($values as $key => $value) {
                if (!isset($values[$key]['updated_at']))
                    $values[$key]['updated_at'] = date('Y-m-d H:i:s');

            }
        }
        return parent::insert($values);
    }
}
