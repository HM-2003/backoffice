<?php

namespace App\Controller;


use App\Entity\Package;
use App\Entity\Product;
use App\Form\PackageFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PackageController extends AbstractController
{
    //form
   #[Route('/package/form', name: 'package_form')]
   public function PackageForm(EntityManagerInterface $entityManager,Request $request): Response
   {
       //create object

       $package = new Package();

       //form creation
       $form = $this->createForm(PackageFormType::class,$package);

       //fetch the form data
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid())
       {
           $package = $form->getData();
           //preparing the query 
           $entityManager->persist($package);

           //insert query
           $entityManager->flush();

           //flash message

           $this->addFlash(
               'notice',
               'Package added successfuly !'
           );
           
           return $this->redirectToRoute('package_list');

       }

       return $this->render('package/form.html.twig',[
           'form' => $form->createView(),
       ]);   
   }

   //list
   #[Route('/package/ ',name:'package_list')]


   public function PackageList(EntityManagerInterface $entityManager):Response
   {
       $packages = $entityManager->getRepository(Package::class)->findall();

       return $this->render('package/list.html.twig',['packages'=> $packages,]);
      
   }

   //detail
   #[Route('/package/{id}',name:'package_detail')]
   public function showPackage(EntityManagerInterface $entityManager,int $id):Response
   {
       $package = $entityManager->getRepository(Package::class)->find($id);
       return $this->render('package/detail.html.twig',['package'=>$package]);

   }

    //delete
    #[Route('/package/{id}/delete',name:'package_delete',methods:['POST'])]
    public function deletePackage(EntityManagerInterface $entityManager,Request $request,int $id):Response
    {
        $package = $entityManager->getRepository(Package::class)->find($id);

        if ($this->isCsrfTokenValid('delete'.$package->getId(), $request->request->get('_token'))) {
            $entityManager->remove($package);
            $entityManager->flush();
        }

        return $this->redirectToRoute('package_list');

   }


    //Edit
  #[Route('/package/{id}/edit',name:'package_edit',methods:['GET','POST'])]
  public function editPackage(EntityManagerInterface $entityManager,Request $request, int $id):Response
  {
      
      $package = $entityManager->getRepository(Package::class)->find($id);
  
      $form = $this->createForm(PackageFormType::class, $package);
  
      $form->handleRequest($request);
  
      if ($form->isSubmitted() && $form->isValid()) {
         
          $entityManager->flush();
          
          $this->addflash(
              'notice',
              'Your package has been modified !'
          );
  
          
          return $this->redirectToRoute('package_detail', ['id' => $package->getId()]);
      }
  
  
      return $this->render('package/edit.html.twig', [
          'form' => $form->createView(),
      ]);
  }
}
