<?php

namespace App\Controller;

use App\Form\PressureEditType;
use App\Form\PressureCalculationType;
use App\Form\PressureCombinationType;
use App\Pressure\PressureService;
use App\Repository\PressureRecordRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PressureController extends AbstractController
{
    protected $pressureRecordRepository;
    protected $productRepository;
    protected $session;

    public function __construct(
        PressureRecordRepository $pressureRecordRepository,
        ProductRepository $productRepository,
        SessionInterface $session,
        PressureService $pressureService
    ) {
        $this->pressureRecordRepository = $pressureRecordRepository;
        $this->product = $productRepository->findOneBy(['slug' => 'pressure']);
        $this->session = $session;
        $this->pressureService = $pressureService;
        $this->sessionPressureRecord = $this->pressureService->getSessionCombination();
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

        $this->denyAccessUnlessGranted('CAN_USE', $this->product);

        // Test si configuration choisie dans session
        if (!$this->sessionPressureRecord) {
            return $this->redirectToRoute('pressure_combination');
        }

        return $this->redirectToRoute('pressure_list');
    }

    /**
     * @Route("/tool/pressure/combination", name="pressure_combination")
     */
    public function combination(Request $request)
    {
        $this->denyAccessUnlessGranted('CAN_USE', $this->product);

        $form = $this->createForm(PressureCombinationType::class, $this->sessionPressureRecord);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pressureRecord = $form->getData();

            $this->pressureService->setSessionCombination($pressureRecord);

            return $this->redirectToRoute('pressure');
        }

        $formView = $form->createView();

        $combinations = $this->pressureRecordRepository->getCombinationsByUser($this->getUser());

        return $this->render('pressure/combination.html.twig', [
            'product' => $this->product,
            'formView' => $formView,
            'combinations' => $combinations,
        ]);
    }

    /**
     * @Route("/tool/pressure/list", name="pressure_list")
     */
    public function list()
    {
        $this->denyAccessUnlessGranted('CAN_USE', $this->product);

        $records = $this->pressureService->getRecords();

        return $this->render('pressure/list.html.twig', [
            'product' => $this->product,
            'pressureRecord' => $this->sessionPressureRecord,
            'records' => $records,
        ]);
    }

    /**
     * @Route("/tool/pressure/show/{id}", name="pressure_show")
     */
    public function show($id)
    {
        $this->denyAccessUnlessGranted('CAN_USE', $this->product);

        $pressureRecord = $this->pressureRecordRepository->find($id);

        return $this->render('pressure/show.html.twig', [
            'product' => $this->product,
            'pressureRecord' => $pressureRecord,
            'id' => $id,
        ]);
    }

    /**
     * @Route("/tool/pressure/edit/{id}", name="pressure_edit")
     */
    public function edit($id = null, Request $request)
    {
        $this->denyAccessUnlessGranted('CAN_USE', $this->product);

        if ($id) {
            $this->sessionPressureRecord = $this->pressureRecordRepository->find($id);
        }

        $form = $this->createForm(PressureEditType::class, $this->sessionPressureRecord);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->pressureService->saveRecord($this->sessionPressureRecord);

            return $this->redirectToRoute('pressure_list');
        }

        $formView = $form->createView();

        return $this->render('pressure/edit.html.twig', [
            'product' => $this->product,
            'formView' => $formView,
            'pressureRecord' => $this->sessionPressureRecord,
            'id' => $id,
        ]);
    }

    /**
     * @Route("/tool/pressure/remove/{id}", name="pressure_remove")
     */
    public function remove($id)
    {
        $this->denyAccessUnlessGranted('CAN_USE', $this->product);

        // check si champ caché existe pour controler le passage par le formulaire
        // contrôle propriétaire
        // contrôle combination
        $this->pressureService->removeRecord($id);
        // redirection list
        return $this->redirectToRoute('pressure_list');
    }

    /**
     * @Route("/tool/pressure/calculation", name="pressure_calculation")
     */
    public function calculation(Request $request)
    {
        $this->denyAccessUnlessGranted('CAN_USE', $this->product);

        $form = $this->createForm(PressureCalculationType::class, $this->sessionPressureRecord);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->sessionPressureRecord = $this->pressureService->getPressures($this->sessionPressureRecord);
        }

        $formView = $form->createView();

        return $this->render('pressure/calculation.html.twig', [
            'product' => $this->product,
            'formView' => $formView,
            'pressureRecord' => $this->sessionPressureRecord,
        ]);
    }

    /**
     * @Route("/tool/pressure/set-combination/{tire}/{driver}/{circuit}", name="pressure_set-combination")
     */
    public function setCombination($tire, $driver, $circuit)
    {
        $this->denyAccessUnlessGranted('CAN_USE', $this->product);

        $this->session->set('tireId', $tire);
        $this->session->set('driverId', $driver);
        $this->session->set('circuitId', $circuit);

        return $this->redirectToRoute('pressure');
    }
}
