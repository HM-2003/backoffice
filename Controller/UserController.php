<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserController extends AbstractController
{
    #[Route('/user/form', name: 'user_form')]
    public function UserForm(EntityManagerInterface $entityManager , Request $request,UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //create object
        $user = new User();

        //form creation
        $form = $this->createForm(UserFormType::class,$user);

        //fetch the form data
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {  //encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                 )
            );
            $user->setRoles([]);
            //preparing the query 
            $entityManager->persist($user);

            //insert query
            $entityManager->flush();

            //flash message

            $this->addFlash(
                'notice',
                'User added successfuly !'
            );
            
            return $this->redirectToRoute('user_list');

        }

        return $this->render('user/form.html.twig',[
            'form' => $form->createView(),
        ]);   
    }

    //list
    #[Route('/user/ ',name:'user_list')]


    public function UserList(EntityManagerInterface $entityManager):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $entityManager->getRepository(User::class)->findall();

        return $this->render('user/list.html.twig',['users'=> $users,]);
       
    }

    //detail
    #[Route('/user/{id}',name:'user_detail')]
    public function showUser(EntityManagerInterface $entityManager,int $id):Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $entityManager->getRepository(User::class)->find($id);
        return $this->render('user/detail.html.twig',['user'=>$user]);

    }

    //delete
    #[Route('/user/{id}/delete',name:'user_delete',methods:['POST'])]
    public function deleteUser(EntityManagerInterface $entityManager,Request $request,int $id):Response
    {
          $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $entityManager->getRepository(User::class)->find($id);

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_list');

   }

   //Edit
   #[Route('/user/{id}/edit',name:'user_edit',methods:['GET','POST'])]
public function editUser(EntityManagerInterface $entityManager,Request $request, int $id):Response
{
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    
    $user = $entityManager->getRepository(User::class)->find($id);

    $form = $this->createForm(UserFormType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
       
        $entityManager->flush();
        
        $this->addflash(
            'notice',
            'Your user has been modified !'
        );

        
        return $this->redirectToRoute('user_detail', ['id' => $user->getId()]);
    }


    return $this->render('user/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
