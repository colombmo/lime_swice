<?php

/**
 * This is the model class for table "{{failed_email}}".
 *
 * The following are the available columns in table '{{failed_email}}':
 * @property integer $id primary key
 * @property integer $surveyid the surveyid this one belongs to
 * @property string $email_type the email type
 * @property string $recipient the recipients email address
 * @property string $content the content of the failed email
 * @property string $language the email language
 * @property string $error_message the error message
 * @property string $created datetime when this entry is created
 * @property string $status status in which this entry is default 'SEND FAILED'
 * @property string $update datetim when it was last updated
 */
class FailedEmail extends LSActiveRecord
{
    /**
     * @inheritdoc
     * @return string the associated database table name
     */
    public function tableName(): string
    {
        return '{{failed_email}}';
    }

    /**
     * @inheritdoc
     * @return array validation rules for model attributes.
     */
    public function rules(): array
    {
        return [
            ['id, surveyid, email_type, recipient, error_message, created', 'required'],
            ['email_type', 'length', 'max' => 200],
            ['recipient', 'length', 'max' => 320],
            ['status', 'length', 'max' => 20],
            ['language', 'length', 'max' => 20],
            ['created, updated', 'safe'],
            // The following rule is used by search().
            ['id, email_type, recipient, language, created, error_message, status, updated', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @inheritdoc
     * @return array relational rules.
     */
    public function relations(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(): array
    {
        return [
            'id'            => 'ID',
            'recipient'     => gt('Recipient'),
            'email_type'    => gt('Email type'),
            'language'      => gt('Email language'),
            'created'       => gt('Date of email failing'),
            'status'        => gt('Status'),
            'update'        => gt('Updated'),
            'error_message' => gt('Error message')
        ];
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search(): CActiveDataProvider
    {
        $criteria = new CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('email_type', $this->email_type, true);
        $criteria->compare('recipient', $this->recipient, true);
        $criteria->compare('language', $this->language, true);
        $criteria->compare('error_message', $this->error_message, true);
        $criteria->compare('created', $this->created, true);
        $criteria->compare('status', $this->status, true);
        $criteria->compare('updated', $this->updated, true);

        return new CActiveDataProvider($this, [
            'criteria' => $criteria,
        ]);
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return FailedEmail the static model class
     */
    public static function model($className = __CLASS__): FailedEmail
    {
        return parent::model($className);
    }

    public function getColumns()
    {
        return [
            [
                'id'             => 'id',
                'class'          => 'CCheckBoxColumn',
                'selectableRows' => '100',
            ],
            [
                'header'      => gT('Action'),
                'name'        => 'buttons',
                'type'        => 'raw',
                'value'       => '$data->buttons',
            ],
            [
                'header' => gT('Status'),
                'name'   => 'status',
                'value'  => '$data->status',
            ],
            [
                'header' => gT('Error Message'),
                'name'   => 'error_message',
                'value'  => '$data->error_message',
            ],
            [
                'header' => gT('Created'),
                'name'   => 'created',
                'value'  => '$data->created',
            ],
            [
                'header' => gT('Updated'),
                'name'   => 'updated',
                'value'  => '$data->updated',
            ],
            [
                'header' => gT('Email type'),
                'name'   => 'email_type',
                'value'  => '$data->email_type',
            ],
            [
                'header' => gT("Recipient"),
                'name'   => 'recipient',
                'value'  => '$data->recipient',
            ],
            [
                'header' => gT('Content'),
                'name'   => 'content',
                'value'  => '$data->content',
            ],
            [
                'header' => gT('Language'),
                'name'   => 'language',
                'value'  => '$data->language',
            ],
        ];
    }

    public function getButtons()
    {
        $buttons = 'insert viewfile here';
        return $buttons;
    }
}
