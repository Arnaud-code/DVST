<?php

namespace App\Controller;

use App\Entity\PressureRecord;
use App\Entity\Product;
use App\Entity\Tire;
use App\Entity\User;
use App\Form\PressureAddType;
use App\Form\PressureCalculType;
use App\Form\PressureConditionsType;
use App\Pressure\PressureService;
use App\Repository\PressureRecordRepository;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PressureController extends AbstractController
{
    protected $pressureRecordRepository;
    protected $productRepository;
    protected $session;

    public function __construct(PressureRecordRepository $pressureRecordRepository, ProductRepository $productRepository, SessionInterface $session, PressureService $pressureService)
    {
        $this->pressureRecordRepository = $pressureRecordRepository;
        $this->product = $productRepository->findOneBy(['slug' => 'pressure']);
        $this->session = $session;
        $this->pressureService = $pressureService;
    }

    /**
     * @Route("/tool/pressure", name="pressure")
     */
    public function index()
    {
        // si utilisateur pas enregistré
        // alors info + redirection ...?

        // si utilisateur pas autorisé
        // alors info + redirection ...?

        // si configuration non choisie dans session
        // dd($user);
        // alors redirection vers configurateur

        $sessionTire = $this->pressureService->getSessionTire();
        $sessionDriver = $this->pressureService->getSessionDriver();
        $sessionCircuit = $this->pressureService->getSessionCircuit();

        if (!$sessionTire || !$sessionDriver || !$sessionCircuit) {
            return $this->redirectToRoute("pressure_conditions");
        }

        return $this->redirectToRoute('pressure_list');
        // return $this->render('pressure/index.html.twig', [
        //     'product' => $this->product,
        //     'sessionTire' => $sessionTire,
        //     'sessionDriver' => $sessionDriver,
        //     'sessionCircuit' => $sessionCircuit,
        // ]);
    }

    /**
     * @Route("/tool/pressure/conditions", name="pressure_conditions")
     */
    public function conditions(Request $request)
    {
        // $conditions = new PressureRecord;

        // $form = $this->createForm(PressureConditionsType::class, $conditions);
        $form = $this->createForm(PressureConditionsType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conditions = $form->getData();

            $this->session->set('sessionTireId', $conditions->getTire()->getId());
            $this->session->set('sessionDriverId', $conditions->getDriver()->getId());
            $this->session->set('sessionCircuitId', $conditions->getCircuit()->getId());

            return $this->redirectToRoute('pressure');
        }

        $formView = $form->createView();

        $summaries = $this->pressureRecordRepository->getSummaryByUser($this->getUser());
        // dd($summaries);

        // $pressureRecords = $this->getUser()->getPressureRecords();
        // dd($pressureRecords);


        return $this->render('pressure/conditions.html.twig', [
            'formView' => $formView,
            'summaries' => $summaries,
        ]);
    }

    /**
     * @Route("/tool/pressure/list", name="pressure_list")
     */
    public function list()
    {
        $sessionTire = $this->pressureService->getSessionTire();
        $sessionDriver = $this->pressureService->getSessionDriver();
        $sessionCircuit = $this->pressureService->getSessionCircuit();

        $records = $this->pressureRecordRepository->findBy([
            'user' => $this->getUser(),
            'tire' => $sessionTire,
            'driver' => $sessionDriver,
            'circuit' => $sessionCircuit,
        ], [
            'datetime' => 'DESC',
        ]);

        return $this->render('pressure/list.html.twig', [
            'product' => $this->product,
            'records' => $records,
            'sessionTire' => $sessionTire,
            'sessionDriver' => $sessionDriver,
            'sessionCircuit' => $sessionCircuit,
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
    public function add(Request $request, EntityManagerInterface $em)
    {
        $sessionTire = $this->pressureService->getSessionTire();
        $sessionDriver = $this->pressureService->getSessionDriver();
        $sessionCircuit = $this->pressureService->getSessionCircuit();

        $pressureRecord = new PressureRecord;
        $form = $this->createForm(PressureAddType::class, $pressureRecord);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pressureRecord
                ->setUser($this->getUser())
                ->setTire($sessionTire)
                ->setDriver($sessionDriver)
                ->setCircuit($sessionCircuit)
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
            'sessionTire' => $sessionTire,
            'sessionDriver' => $sessionDriver,
            'sessionCircuit' => $sessionCircuit,
        ]);
    }

    /**
     * @Route("/tool/pressure/calculation", name="pressure_calculation")
     */
    public function calculation(Request $request, PressureRecordRepository $pressureRecordRepository)
    {
        $sessionTire = $this->pressureService->getSessionTire();
        $sessionDriver = $this->pressureService->getSessionDriver();
        $sessionCircuit = $this->pressureService->getSessionCircuit();

        $pressureRecord = new PressureRecord;
        $form = $this->createForm(PressureCalculType::class, $pressureRecord);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $records = $pressureRecordRepository->findBy([
                'user' => $this->getUser(),
                'tire' => $sessionTire,
                'driver' => $sessionDriver,
                'circuit' => $sessionCircuit,
            ]);
            $pressures = $this->pressureService->getPressures($pressureRecord, $records);
            $pressureRecord
                ->setPressFrontLeft($pressures[0])
                ->setPressFrontRight($pressures[1])
                ->setPressRearLeft($pressures[2])
                ->setPressRearRight($pressures[3]);
            // dd($pressureRecord);
        }

        $formView = $form->createView();

        // si formulaire soumis...
        // $pressures = $this->pressureService->getPressures($records);
        return $this->render('pressure/calculation.html.twig', [
            'product' => $this->product,
            'formView' => $formView,
            'sessionTire' => $sessionTire,
            'sessionDriver' => $sessionDriver,
            'sessionCircuit' => $sessionCircuit,
            'pressureRecord' => $pressureRecord,
        ]);
    }

    /**
     * @Route("/tool/pressure/set-conditions/{tire}/{driver}/{circuit}", name="pressure_set-conditions")
     */
    public function setConditions($tire, $driver, $circuit)
    {
        $this->session->set('sessionTireId', $tire);
        $this->session->set('sessionDriverId', $driver);
        $this->session->set('sessionCircuitId', $circuit);

        return $this->redirectToRoute('pressure');
    }
}
