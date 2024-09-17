<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
   //form
   #[Route('/product/form', name: 'product_form')]
   public function ProductForm(EntityManagerInterface $entityManager,Request $request): Response
   {


       //create object

       $product = new Product();

       //form creation
       $form = $this->createForm(ProductFormType::class,$product);

       //fetch the form data
       $form->handleRequest($request);

       if($form->isSubmitted() && $form->isValid())
       {
           $product = $form->getData();
           //preparing the query 
           $entityManager->persist($product);

           //insert query
           $entityManager->flush();

           //flash message

           $this->addFlash(
               'notice',
               'Product added successfuly !'
           );
           
           return $this->redirectToRoute('product_list');

       }

       return $this->render('product/form.html.twig',[
           'form' => $form->createView(),
       ]);   
   }

   //list
   #[Route('/product/ ',name:'product_list')]


   public function ProductList(EntityManagerInterface $entityManager):Response
   {
       $products = $entityManager->getRepository(Product::class)->findall();

       return $this->render('product/list.html.twig',['products'=> $products,]);
      
   }

   //detail
   #[Route('/product/{id}',name:'product_detail')]
   public function showProduct(EntityManagerInterface $entityManager,int $id):Response
   {
       $product = $entityManager->getRepository(Product::class)->find($id);
       return $this->render('product/detail.html.twig',['product'=>$product]);

   }

    //delete
    #[Route('/product/{id}/delete',name:'product_delete',methods:['POST'])]
    public function deleteProduct(EntityManagerInterface $entityManager,Request $request,int $id):Response
    {
        $product = $entityManager->getRepository(Product::class)->find($id);

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_list');

   }


    //Edit
  #[Route('/product/{id}/edit',name:'product_edit',methods:['GET','POST'])]
  public function editProduct(EntityManagerInterface $entityManager,Request $request, int $id):Response
  {
      
      $product = $entityManager->getRepository(Product::class)->find($id);
  
      $form = $this->createForm(ProductFormType::class, $product);
  
      $form->handleRequest($request);
  
      if ($form->isSubmitted() && $form->isValid()) {
         
          $entityManager->flush();
          
          $this->addflash(
              'notice',
              'Your product has been modified !'
          );
  
          
          return $this->redirectToRoute('product_detail', ['id' => $product->getId()]);
      }
  
  
      return $this->render('product/edit.html.twig', [
          'form' => $form->createView(),
      ]);
  }

}
