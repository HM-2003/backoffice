<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\PackageSubscription;
use App\Entity\Client;
use App\Entity\Package;
use App\Form\SubscriptionFormType;
use App\Form\SubscriptionDatesFormType;
use App\Form\SubscriptionPackagesFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use App\Event\AddedPackageEvent;


class SubscriptionController extends AbstractController
{
     //temporary store session 
     #[Route('/subscription/temporary_store',name:'subscription_temporary_store',methods:['POST'])]
     public function temporaryStore(Request $request , SessionInterface $session) :JsonResponse
     {
         //fetch Data from request
         $packages = $request->request->get('packages');
         $price = $request->request->get('price');

          // Validate data
        if (empty($packages) || empty($price)) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid data']);
        }

         //sessions data 
         $oldData = $session->get('temp_data',[]);

         //new data entry 
         $newEntry = [
            'packages' => $packages,
            'price' => $price 
         ];

         //add new entry to the data array 
         $oldData[] = $newEntry;

         //store Data in the session
         $session->set('temp_data',$oldData);

          // Log session data
         $this->addFlash('notice', 'Temporary data stored successfully.');
         return new JsonResponse(['status' => 'success']);
     }
 
     //TEMPORARY TOTAL PRICE SESSION 
    #[Route('/subscription/get_total_price', name: 'subscription_get_total_price', methods: ['GET'])]
    public function TempStoreTotalPrice(SessionInterface $session): JsonResponse
    {
        $tempData = $session->get('temp_data', []);
        $totalPrice = array_sum(array_column($tempData, 'price'));

        return new JsonResponse([
            'status' => 'success',
            'totalPrice' => $totalPrice,
        ]);
    }

     //SET CLIENT 

     #[Route('/subscription/new/{client_id}', name: 'subscription_new')]
    public function SubscriptionNew(Request $request, EntityManagerInterface $entityManager, int $client_id ,SessionInterFace $session,EventDispatcherInterface $eventDispatcher): Response
    {
        $client = $entityManager->getRepository(Client::class)->find($client_id);
        
        if (!$client) {
            throw $this->createNotFoundException('Client not found');
        }

        $subscription = new Subscription();

        $form = $this->createForm(SubscriptionFormType::class, $subscription);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {        
            $subscription->setClient($client);
            $entityManager->persist($subscription);
            $entityManager->flush();

            //process temporary data
           $tempData =$session->get('temp_data',[]);
           $totalPrice = 0;

            // Process each entry in temp_data 
            foreach ($tempData as $tempEntry) {
            $price = $tempEntry['price'];
            $packageId = $tempEntry['packages'];

            //fetch packages 
            $package = $entityManager->getRepository(Package::class)->find($packageId);
            $packageSubscription = new PackageSubscription();
            $packageSubscription->setPackage($package);
            $packageSubscription->setSubscription($subscription);
            $packageSubscription->setPrice($price);
            $entityManager->persist($packageSubscription);

             // Add price to total
            $totalPrice += $price;

            //dispatch the event 
            $event = new AddedPackageEvent($packageSubscription);
            $eventDispatcher->dispatch($event,AddedPackageEvent::NAME);
        }
         // Set total price on the subscription
        $subscription->setTotalPrice($totalPrice);
        $entityManager->flush();

        //clear the temp data after processing 
        $session->remove('temp_data');
        $this->addFlash('notice', 'Subscription added successfully!');
        return $this->redirectToRoute('client_list');
        }

        return $this->render('subscription/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
     //detail
     #[Route('/subscription/{id}',name:'subscription_detail',requirements: ['id' => '\d+'])]
     public function showSubscription(EntityManagerInterface $entityManager,int $id):Response
     { 
         $subscription = $entityManager->getRepository(Subscription::class)->find($id);

        if (!$subscription) {
            throw $this->createNotFoundException('Subscription not found');
        }

        $packageSubscriptions = $subscription->getPackageSubscriptions();

         return $this->render('subscription/detail.html.twig',[
            'subscription'=>$subscription,
            'packageSubscriptions' =>$packageSubscriptions
        ]);
 
     }
 
      //delete
      #[Route('/subscription/{id}/delete',name:'subscription_delete',methods:['POST'])]
      public function deleteSubscription(EntityManagerInterface $entityManager,Request $request,int $id):Response
      {
          $subscription = $entityManager->getRepository(Subscription::class)->find($id);
  
          if ($this->isCsrfTokenValid('delete'.$subscription->getId(), $request->request->get('_token'))) {
              $entityManager->remove($subscription);
              $entityManager->flush();
          }
  
          return $this->redirectToRoute('client_list');
  
     }
 

     //delete subscription package 
      #[Route('/SubscriptionPackages/{id}/delete',name:'delete_subscription_package',methods:['POST'])]
      public function deleteSubscriptionPackage(EntityManagerInterface $entityManager,Request $request, PackageSubscription $packageSubscription):Response
      {
        $entityManager->remove($packageSubscription);
        $entityManager->flush();
        $this->addFlash('notice', 'Package subscription deleted !');

          return $this->redirectToRoute('subscription_detail', ['id'=> $packageSubscription->getSubscription()->getId()]);
  
         }


     //edit subscription date 
      #[Route('/subscription/{id}/edit',name:'subscriptionDates_edit',methods:['GET','POST'])]
     public function editSubscriptionDate(Request $request , Subscription $subscription,EntityManagerInterface $entityManager): Response
     {
       
        $form = $this->createForm(SubscriptionDatesFormType::class, $subscription);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $entityManager->flush();
            $this->addFlash(
                'notice',
            'Subscription dates updated successfully'
            );

           return $this->redirectToRoute('subscription_detail', ['id' => $subscription->getId()]);
        }
        return $this->render('subscription/edit.html.twig',[
            'form'=> $form->createView(),
        ]);
     }

      //edit subscription packages 
    #[Route('/subscription/edit_packages/{id}', name: 'subscriptionPackages_edit', methods: ['GET', 'POST'])]
    public function editSubscriptionPackages(Request $request, PackageSubscription $packageSubscription,EventDispatcherInterface $eventDispatcher,EntityManagerInterface $entityManager): Response
    {
        $newPrice = $request->get('edit_package_price');
        $packageSubscription->setPrice($newPrice);
        $entityManager->flush();

    // Dispatch the event to recalculate the total price
        $event = new AddedPackageEvent($packageSubscription);
        $eventDispatcher->dispatch($event, AddedPackageEvent::NAME);

        $this->addFlash('notice', 'Package price updated successfully!');

        return $this->redirectToRoute('subscription_detail', ['id' => $packageSubscription->getSubscription()->getId() ]);
    }


   

}
 
 


 
    

