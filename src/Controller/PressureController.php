<?php

namespace App\Controller;

use App\Entity\PressureRecord;
use App\Entity\Product;
use App\Entity\Tire;
use App\Form\PressureAddType;
use App\Form\PressureConditionsType;
use App\Repository\CircuitRepository;
use App\Repository\DriverRepository;
use App\Repository\PressureRecordRepository;
use App\Repository\ProductRepository;
use App\Repository\TireRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\BuilderFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class PressureController extends AbstractController
{
    protected $productRepository;
    protected $session;

    public function __construct(ProductRepository $productRepository, SessionInterface $session)
    {
        $this->product = $productRepository->findOneBy(['slug' => 'pressure']);
        $this->session = $session;
    }

    /**
     * @Route("/tool/pressure", name="pressure")
     */
    public function index(UserInterface $user)
    {
        // si utilisateur pas enregistré
        // alors info + redirection ...?

        // si utilisateur pas autorisé
        // alors info + redirection ...?

        // si configuration non choisie dans session
        // dd($session);
        // dd($user);
        // alors redirection vers configurateur
        if (!$this->session->get('tire') || !$this->session->get('driver') || !$this->session->get('circuit')) {
            return $this->redirectToRoute("pressure_conditions");
        }

        return $this->render('pressure/homepage.html.twig', [
            'product' => $this->product,
        ]);
    }

    /**
     * @Route("/tool/pressure/conditions", name="pressure_conditions")
     */
    public function conditions(Request $request)
    {
        // $conditions = new PressureRecord;

        // $form = $this->createForm(PressureConditionsType::class, $conditions);
        $form = $this->createForm(PressureConditionsType::class);
        $formView = $form->createView();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->session->set('tire', $data->getTire());
            $this->session->set('driver', $data->getDriver());
            $this->session->set('circuit', $data->getCircuit());

            return $this->redirectToRoute('pressure');
        }

        return $this->render('pressure/conditions.html.twig', [
            'formView' => $formView,
        ]);
    }


    /**
     * @Route("/tool/pressure/list", name="pressure_list")
     */
    public function list(PressureRecordRepository $pressureRecordRepository, UserInterface $user)
    {
        $records = $pressureRecordRepository->findBy([
            'user' => $user,
            'tire' => $this->session->get('tire'),
            'driver' => $this->session->get('driver'),
            'circuit' => $this->session->get('circuit'),
        ], [
            'datetime' => 'DESC',
        ]);

        return $this->render('pressure/list.html.twig', [
            'product' => $this->product,
            'records' => $records,
        ]);
    }

    /**
     * @Route("/tool/pressure/remove/{id}", name="pressure_remove")
     */
    public function remove($id)
    {
        // contrôle propriétaire
        // contrôle conditions
        // redirection list
    }

    /**
     * @Route("/tool/pressure/add", name="pressure_add")
     */
    public function add(Request $request, EntityManagerInterface $em, TireRepository $tr, DriverRepository $dr, CircuitRepository $cr)
    {
        $pressureRecord = new PressureRecord;
        $form = $this->createForm(PressureAddType::class, $pressureRecord);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tire = $tr->find($this->session->get('tire')->getId());
            $driver = $dr->find($this->session->get('driver')->getId());
            $circuit = $cr->find($this->session->get('circuit')->getId());
            // $pressureRecord = $form->getData();
            $pressureRecord
                ->setUser($this->getUser())
                ->setTire($tire)
                ->setDriver($driver)
                ->setCircuit($circuit)
                ->setDatetime(new DateTime('now'));

            // dd($pressureRecord);

            $em->persist($pressureRecord);
            $em->flush();

            return $this->redirectToRoute('pressure_list');
        }

        $formView = $form->createView();

        return $this->render('pressure/add.html.twig', [
            'product' => $this->product,
            'formView' => $formView,
        ]);
    }
}
