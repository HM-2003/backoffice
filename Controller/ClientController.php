<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\City;
use App\Form\ClientFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{

    //form
    #[Route('/client/form', name: 'client_form')]
    public function ClientForm(EntityManagerInterface $entityManager,Request $request): Response
    {


        //create object

        $client = new Client();

        //form creation
        $form = $this->createForm(ClientFormType::class,$client);

        //fetch the form data
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $client = $form->getData();
            //preparing the query 
            $entityManager->persist($client);

            //insert query
            $entityManager->flush();

            //flash message

            $this->addFlash(
                'notice',
                'Client added successfuly !'
            );
            
            return $this->redirectToRoute('subscription_new', ['client_id' => $client->getId()]);

        }

        return $this->render('client/form.html.twig',[
            'form' => $form->createView(),
        ]);   
    }

    //list
    #[Route('/client/ ',name:'client_list')]


    public function ClientList(EntityManagerInterface $entityManager):Response
    {
        $clients = $entityManager->getRepository(Client::class)->findall();

        return $this->render('client/list.html.twig',['clients'=> $clients,]);
       
    }

    //detail
    #[Route('/client/{id}',name:'client_detail')]
    public function showClient(EntityManagerInterface $entityManager,int $id):Response
    {
        $client = $entityManager->getRepository(Client::class)->find($id);
        return $this->render('client/detail.html.twig',['client'=>$client]);

    }

     //delete
     #[Route('/client/{id}/delete',name:'client_delete',methods:['POST'])]
     public function deleteClient(EntityManagerInterface $entityManager,Request $request,int $id):Response
     {
         $client = $entityManager->getRepository(Client::class)->find($id);
 
         if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
             $entityManager->remove($client);
             $entityManager->flush();
         }
 
         return $this->redirectToRoute('client_list');
 
    }


     //Edit
   #[Route('/client/{id}/edit',name:'client_edit',methods:['GET','POST'])]
   public function editClient(EntityManagerInterface $entityManager,Request $request, int $id):Response
   {
       
       $client = $entityManager->getRepository(Client::class)->find($id);
   
       $form = $this->createForm(ClientFormType::class, $client);
   
       $form->handleRequest($request);
   
       if ($form->isSubmitted() && $form->isValid()) {
          
           $entityManager->flush();
           
           $this->addflash(
               'notice',
               'Your client has been modified !'
           );
   
           
           return $this->redirectToRoute('client_detail', ['id' => $client->getId()]);
       }
   
   
       return $this->render('client/edit.html.twig', [
           'form' => $form->createView(),
       ]);
   }

}
