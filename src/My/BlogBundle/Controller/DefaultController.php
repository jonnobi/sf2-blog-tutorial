<?php

namespace My\BlogBundle\Controller;

use My\BlogBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    // 記事一覧を表示するアクション
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $posts = $em->getRepository('MyBlogBundle:Post')->findAll();
        return $this->render('MyBlogBundle:Default:index.html.twig', array('posts' => $posts));
    }

    // 記事を参照するアクション
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $post = $em->find('MyBlogBundle:Post', $id);
        return $this->render('MyBlogBundle:Default:show.html.twig', array('post' => $post));
    }

    // 記事を追加するアクション
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

                // ライフサイクル・コールバックにて更新するのでコメント化。
                //$post->setCreatedAt(new \DateTime());
                //$post->setUpdatedAt(new \DateTime());

                // フォームから取り出したオブジェクトをデータベースに登録するには、
                // persist() メソッドで EntityManager に対して永続化指示を行った後、
                // EntityManager の flush() メソッドを呼び出します。
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($post);
                $em->flush();
                // フラッシュメッセージを登録する。
                $this->get('session')->setFlash('my_blog', '記事を追加しました');
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

    // 記事を削除するアクション
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $post = $em->find('MyBlogBundle:Post', $id);
        if (!$post) {
            throw new NotFoundHttpException('The post does not exist.');
        }
        $em->remove($post);
        $em->flush();
        // フラッシュメッセージを登録する。
        $this->get('session')->setFlash('my_blog', '記事を削除しました');
        return $this->redirect($this->generateUrl('blog_index'));
    }

    // 編集アクション
    public function editAction($id)
    {
        // DBから取得
        $em = $this->getDoctrine()->getEntityManager();
        $post = $em->find('MyBlogBundle:Post', $id);
        if (!$post) {
            throw new NotFoundHttpException('The post does not exist.');
        }

        // フォームのビルド
        $form = $this->createFormBuilder($post)
            ->add('title')
            ->add('body')
            ->getForm();

        // バリデーション
        $request = $this->getRequest();
        if ('POST' === $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()) {
                // 更新されたエンティティをデータベースに保存
                $post = $form->getData();

                // ライフサイクル・コールバックにて更新するのでコメント化。
                //$post->setUpdatedAt(new \DateTime());

                //すでに永続化されているエンティティを EntityManager 経由で取得した場合、
                //オブジェクトのプロパティを変更して EntityManager の flush() を実行するだけで
                //データベースに反映されることに注意してください。persist() は不要です。
                $em->flush();
                // フラッシュメッセージを登録する。
                $this->get('session')->setFlash('my_blog', '記事を編集しました');
                return $this->redirect($this->generateUrl('blog_index'));
            }
        }

        // 描画
        return $this->render('MyBlogBundle:Default:edit.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }
}
