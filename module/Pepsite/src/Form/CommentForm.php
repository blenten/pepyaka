<?php

namespace Pepsite\Form;

use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct()
    {
        parent::__construct('comment-form');

        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addFilters();
    }

    public function addElements()
    {
        $this->add([
            'type' => 'textarea',
            'name' => 'content',
            'options' => [],
            'attributes' => [
                'placeholder' => 'Оставить комментарий...'
            ]
        ]);

        $this->add([
            'type' => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Отправить'
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
            'name' => 'content',
            'required' => true,
            'filters' => [
                ['name' => 'StringTrim'],
                ['name' => 'StripTags'],
                ['name' => 'StripNewlines'],
            ],
            'validators' => [],
        ]);
    }
}
