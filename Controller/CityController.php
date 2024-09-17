<?php

namespace App\Controller;
use App\Entity\City;
use App\Form\CityFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class CityController extends AbstractController
{
    //form 
    #[Route('/city/form', name: 'city_form',methods:['GET','POST'])]
    public function CityForm(Request $request,EntityManagerInterface $entityManager): Response
    {
        //create object
        $city = new City();

        //form creation
        $form = $this->createForm(CityFormType::class);

        // fetch the form data
         $form->handleRequest($request);

         if( $form->isSubmitted() && $form->isValid())
         {
            $city = $form->getData();
            //preparing the query
            $entityManager->persist($city);

            //insert query 
            $entityManager->flush();

            //flash message

            $this->addFlash(
                'notice',
                'City added successfuly !'
            );

            return $this->redirectToRoute('city_list');

         }

        return $this->render('city/form.html.twig',[
            'form' => $form->createView(),
        ]);   
    }


    //list
    #[Route('/city/ ',name:'city_list')]


    public function CityList(EntityManagerInterface $entityManager):Response
    {
        $cities = $entityManager->getRepository(City::class)->findall();

        return $this->render('city/list.html.twig',['cities'=> $cities,]);
       
    }

    //detail
    #[Route('/city/{id}',name:'city_detail')]
    public function showCity(EntityManagerInterface $entityManager,int $id):Response
    {
        $city = $entityManager->getRepository(City::class)->find($id);
        return $this->render('city/detail.html.twig',['city'=>$city]);

    }


    //delete
    #[Route('/city/{id}/delete',name:'city_delete',methods:['POST'])]
    public function deleteCity(EntityManagerInterface $entityManager,Request $request,int $id):Response
    {
        $city = $entityManager->getRepository(City::class)->find($id);

        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('city_list');

   }


   //Edit
   #[Route('/city/{id}/edit',name:'city_edit',methods:['POST'])]
public function editCity(EntityManagerInterface $entityManager,Request $request, int $id):Response
{
    
    $city = $entityManager->getRepository(City::class)->find($id);

    $form = $this->createForm(CityFormType::class, $city);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
       
        $entityManager->flush();
        
        $this->addflash(
            'notice',
            'Your city has been modified !'
        );

        
        return $this->redirectToRoute('city_detail', ['id' => $city->getId()]);
    }


    return $this->render('city/edit.html.twig', [
        'form' => $form->createView(),
    ]);
}


}
