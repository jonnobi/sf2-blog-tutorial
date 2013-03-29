<?php

namespace My\BlogBundle\Controller;

use My\BlogBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $posts = $em->getRepository('MyBlogBundle:Post')->findAll();
        return $this->render('MyBlogBundle:Default:index.html.twig', array('posts' => $posts));
    }

    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $post = $em->find('MyBlogBundle:Post', $id);
        return $this->render('MyBlogBundle:Default:show.html.twig', array('post' => $post));
    }

    public function newAction()
    {
        // フォームのビルド
        // - FormBuilder を用いてコントローラのアクション内で簡単にフォームオブジェクトを作成できる。
        $form = $this->createFormBuilder(new Post())  // ここでPostクラスを使うため、ファイルの先頭あたりにuseを追加していることに注意
            ->add('title')
            ->add('body')
            ->getForm();

        // バリデーション
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                // エンティティを永続化
                $post = $form->getData();
                $post->setCreatedAt(new \DateTime());
                $post->setUpdatedAt(new \DateTime());

                // フォームから取り出したオブジェクトをデータベースに登録するには、
                // persist() メソッドで EntityManager に対して永続化指示を行った後、
                // EntityManager の flush() メソッドを呼び出します。
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($post);
                $em->flush();
                return $this->redirect($this->generateUrl('blog_index'));
            }
        }

        // 描画
        // GET メソッドでアクセスされたときと、POST メソッドだがバリデーションに失敗した時に実行。
        // ここでは、フォームオブジェクトを描画可能な FormView オブジェクトに変換するために createView() メソッドを呼び出し、
        // その結果をテンプレートにパラメータとして引き渡しています。
        return $this->render('MyBlogBundle:Default:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
