<?php

namespace App\Controller;
use App\Entity\Prospect;
use App\Entity\City;
use App\Entity\Client;
use App\Form\ProspectFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProspectController extends AbstractController
{
   //form
   #[Route('/prospect/form', name: 'prospect_form')]
   public function ProspectForm(EntityManagerInterface $entityManager,Request $request): Response
   {


       //create object

       $prospect = new Prospect();

       //form creation
       $form = $this->createForm(ProspectFormType::class,$prospect);

       //fetch the form data
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid())
       {
           $prospect = $form->getData();
           //preparing the query 
           $entityManager->persist($prospect);

           //insert query
           $entityManager->flush();

           //flash message

           $this->addFlash(
               'notice',
               'Prospect added successfuly !'
           );
           
           return $this->redirectToRoute('prospect_list');

       }

       return $this->render('prospect/form.html.twig',[
           'form' => $form->createView(),
       ]);   
   }

   //list
   #[Route('/prospect/ ',name:'prospect_list')]


   public function ProspectList(EntityManagerInterface $entityManager):Response
   {
       $prospects = $entityManager->getRepository(Prospect::class)->findall();

       return $this->render('prospect/list.html.twig',['prospects'=> $prospects,]);
      
   }

   //detail
   #[Route('/prospect/{id}',name:'prospect_detail')]
   public function showProspect(EntityManagerInterface $entityManager,int $id):Response
   {
       $prospect = $entityManager->getRepository(Prospect::class)->find($id);
       return $this->render('prospect/detail.html.twig',['prospect'=>$prospect]);

   }

    //delete
    #[Route('/prospect/{id}/delete',name:'prospect_delete',methods:['POST'])]
    public function deleteProspect(EntityManagerInterface $entityManager,Request $request,int $id):Response
    {
        $prospect = $entityManager->getRepository(Prospect::class)->find($id);

        if ($this->isCsrfTokenValid('delete'.$prospect->getId(), $request->request->get('_token'))) {
            $entityManager->remove($prospect);
            $entityManager->flush();
        }

        return $this->redirectToRoute('prospect_list');

   }


    //Edit
  #[Route('/prospect/{id}/edit',name:'prospect_edit',methods:['GET','POST'])]
  public function editProspect(EntityManagerInterface $entityManager,Request $request, int $id):Response
  {
      
      $prospect = $entityManager->getRepository(Prospect::class)->find($id);
  
      $form = $this->createForm(ProspectFormType::class, $prospect);
  
      $form->handleRequest($request);
  
      if ($form->isSubmitted() && $form->isValid()) {
         
          $entityManager->flush();
          
          $this->addflash(
              'notice',
              'Your prospect has been modified !'
          );
  
          
          return $this->redirectToRoute('prospect_detail', ['id' => $prospect->getId()]);
      }
  
  
      return $this->render('prospect/edit.html.twig', [
          'form' => $form->createView(),
      ]);
  }

    //convert prospect to client 
    #[Route('/prospect/{id}/convertToclient',name:'ConvertToClient',methods:['POST'])]
    public function ConvertToClient(EntityManagerInterface $entityManager,Prospect $Prospect,Request $request,int $id):Response
    {
      $prospect = $entityManager->getRepository(Prospect::class)->find($id);

    //create the new client 
      $client = new Client();
      $client->setName($prospect->getName());
      $client->setEmail($prospect->getEmail());
      $client->setCity($prospect->getCity());
    //persist the new client
      $entityManager->persist($client);
      //remove the converted prospect
      $entityManager->remove($prospect);
      //save the changes
      $entityManager->flush();
      
      return $this->render('client/detail.html.twig',['client'=>$client]);

   }
}
