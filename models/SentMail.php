<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sent_mail".
 *
 * @property integer $id
 * @property string $sender
 * @property string $receiver
 * @property string $subject
 * @property string $body
 * @property integer $date
 */
class SentMail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sent_mail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender', 'receiver', 'subject', 'body', 'date'], 'required'],
            [['date'], 'integer'],
            [['sender', 'receiver', 'subject', 'body'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sender' => 'Sender',
            'receiver' => 'Receiver',
            'subject' => 'Subject',
            'body' => 'Body',
            'date' => 'Date',
        ];
    }
}
