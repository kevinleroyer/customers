<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Customer;
use AppBundle\Form\CustomerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $customers = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->findBy([], ['firstname' => 'ASC', 'lastname' => 'ASC']);

        // replace this example code with whatever you need
        return $this->render('@App/default/index.html.twig', [
            'customers' => $customers,
        ]);
    }

    /**
     * @Route("/customer/add", name="add_customer")
     */
    public function addCustomerAction(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer = $form->getData();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($customer);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'New customer successfully added!'
            );

            return new RedirectResponse($this->generateUrl('homepage'));
        }

        // replace this example code with whatever you need
        return $this->render('@App/default/customer.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/customer/edit/{id}", name="edit_customer")
     */
    public function editCustomerAction(Request $request, $id)
    {
        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->find($id);

        if ($customer) {
            $form = $this->createForm(CustomerType::class, $customer);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

                $this->addFlash(
                    'notice',
                    'Customer successfully modified!'
                );

                return new RedirectResponse($this->generateUrl('homepage'));
            }

            // replace this example code with whatever you need
            return $this->render('@App/default/customer.html.twig', [
                'form' => $form->createView(),
                'customer' => $customer
            ]);
        }

        return new RedirectResponse($this->generateUrl('homepage'));
    }

   /**
    * @Route("/customer/delete/{id}", name="delete_customer")
    */
    public function deleteCustomerAction(Request $request, $id)
    {
        $customer = $this->getDoctrine()
            ->getRepository(Customer::class)
            ->find($id);

        if ($customer) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($customer);
            $entityManager->flush();

            $this->addFlash(
                'notice',
                'Customer successfully removed!'
            );
        }

        return new RedirectResponse($this->generateUrl('homepage'));
    }

    /**
     * @Route("/customers/find", name="find_customer")
     */
    public function findCustomerAction(Request $request)
    {
        $text = $request->request->get('text');

        if ($text === '') {
            $customers = $this->getDoctrine()
                ->getRepository(Customer::class)
                ->findAll();
        } else {
            $customers = $this->getDoctrine()->getManager()->getRepository("AppBundle:Customer")->createQueryBuilder('c')
                ->where('c.firstname LIKE :text')
                ->orWhere('c.lastname LIKE :text')
                ->setParameter('text', '%' . $text . '%')
                ->getQuery()
                ->getResult();
        }

        $html = $this->renderView('@App/default/customer-list.html.twig', [
            'customers' => $customers
        ]);

        return new JsonResponse([
            'search' => $text,
            'data' => $html]
        );
    }

}
