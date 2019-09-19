<?php
namespace Pepsite\Form;

use Pepsite\Entity\User;
use Zend\Form\Form;
use Zend\Validator\{InArray, NotEmpty, StringLength, Regex, Db\NoRecordExists};
use Zend\Validator\File\{MimeType, Size};
use Zend\InputFilter\FileInput;

class ProfileEditForm extends Form
{
    public function __construct()
    {
        parent::__construct('registration-form');

        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addFilters();
    }

    private function addElements()
    {
        $this->add([
            'type' => 'text',
            'name' => 'login',
            'options' => [
                'label' => 'Логин',
            ],
            'attributes' => [
                'required'  => true,
                'autofocus' => true,
                'readonly'  => true,
            ]
        ]);

        $this->add([
            'type' => 'file',
            'name' => 'avatar',
            'options' => [
                'label' => 'Аватар',
            ],
            'attributes' => [
                'accept' => 'image/jpeg, image/jpg, image/gif, image/png'
            ]
        ]);

        $this->add([
            'type' => 'select',
            'name' => 'gender',
            'options' => [
                'label' => 'Пол',
                'value_options' => [
                    User::GENDER_MALE  => 'Мужской',
                    User::GENDER_FEMALE => 'Женский'
                ],
                'empty_option' => 'Выберите пол...',
                'disable_inarray_validator' => true,
                'attributes' => [
                    'required' => true,
                ]
            ]
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Сохранить',
            ]
        ]);

        $this->add([
            'type' => 'csrf',
            'name' => 'token',
            'options' => [
                'timeout' => 600,
            ]
        ]);
    }

    private function addFilters()
    {
        $inputFilter = $this->getInputFilter();

        $inputFilter->add([
            'type' => FileInput::class,
            'name'     => 'avatar',
            'required' => false,
            'validators' => [
                [
                    'name' => MimeType::class,
                    'options' => [
                        'mimeType' => ['image/jpeg', 'image/jpg', 'image/gif', 'image/png'],
                        'messages' => array_fill_keys(
                            [MimeType::FALSE_TYPE, MimeType::NOT_DETECTED, MimeType::NOT_READABLE],
                            'Аватар должен быть файлом расширения .jpeg, .jpg, .gif или .png'
                        )
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => Size::class,
                    'options' => [
                        'max' => '5MB',
                        'messages' => [
                            Size::TOO_BIG => 'Размер аватара не должен превышать %max%'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
            ],
        ]);

        $inputFilter->add([
            'name'     => 'gender',
            'required' => true,
            'filters'  => [
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => 'Пол не указан'
                        ]
                    ],
                    'break_chain_on_failure' => true
                ],
                [
                    'name' => InArray::class,
                    'options' => [
                        'haystack' => [
                            User::GENDER_MALE,
                            User::GENDER_FEMALE,
                        ],
                        'messages' => [
                            InArray::NOT_IN_ARRAY => 'Пол не существует'
                        ]
                    ],
                ],
            ]
        ]);
    }
}
