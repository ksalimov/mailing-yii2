<?php
/**
 * Created by PhpStorm.
 * User: Konstantin
 * Date: 2016-12-27
 * Time: 13:17
 */

namespace app\models;


use yii\base\Model;

class Gmail extends Model
{
    public $id;
    public $from;
    public $subject;
    public $date;
    public $body;
}