<?php

namespace My\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Description of PostType
 *
 * blogアプリケーションのフォームもクラスを分離して再利用してみましょう。
 * まずは、Post エンティティに対応する PostType フォームクラスを作成します。
 *
 * @author <yamaken@silic.co.jp>
 */
class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body')
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'My\BlogBundle\Entity\Post',
        );
    }

    public function getName()
    {
        return 'post';
    }
}


