<?php

namespace App\Pressure;

use App\Entity\PressureRecord;
use App\Entity\User;
use App\Repository\CircuitRepository;
use App\Repository\DriverRepository;
use App\Repository\PressureRecordRepository;
use App\Repository\TireRepository;
use DateTime;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PressureService
{
    const TEMP = 20;
    const COEF_AIR = 0.013;
    const COEF_TRACK = 0.005;

    protected $session;
    protected $tr;
    protected $dr;
    protected $cr;
    protected $prr;
    protected $em;
    protected $token;

    public function __construct(
        SessionInterface $session,
        TireRepository $tr,
        DriverRepository $dr,
        CircuitRepository $cr,
        PressureRecordRepository $prr,
        EntityManagerInterface $em,
        TokenStorageInterface $token
    ) {
        $this->session = $session;
        $this->tr = $tr;
        $this->dr = $dr;
        $this->cr = $cr;
        $this->prr = $prr;
        $this->em = $em;
        $this->user = $token->getToken()->getUser();
    }

    public function isCheckedUser(PressureRecord $pressureRecord)
    {
        if (
            $this->user !== $pressureRecord->getTire()->getUser() ||
            $this->user !== $pressureRecord->getDriver()->getUser() ||
            $this->user !== $pressureRecord->getCircuit()->getUser()
        ) {
            return false;
        }
        return true;
    }

    public function getSessionCombination(): ?PressureRecord
    {
        $tireId = $this->session->get('tireId');
        $driverId = $this->session->get('driverId');
        $circuitId = $this->session->get('circuitId');

        if (!$tireId || !$driverId || !$circuitId) {
            return null;
        }

        $pressureRecord = new PressureRecord;
        $pressureRecord
            ->setTire($this->tr->find($tireId))
            ->setDriver($this->dr->find($driverId))
            ->setCircuit($this->cr->find($circuitId));

        if (!$this->isCheckedUser($pressureRecord)) {
            return null;
        }

        return $pressureRecord;
    }

    public function setSessionCombination(PressureRecord $pressureRecord): void
    {
        if (!$this->isCheckedUser($pressureRecord)) {
            return;
        }

        $this->session->set('tireId', $pressureRecord->getTire()->getId());
        $this->session->set('driverId', $pressureRecord->getDriver()->getId());
        $this->session->set('circuitId', $pressureRecord->getCircuit()->getId());
    }

    public function getRecords()
    {
        $records = $this->prr->findBy([
            'user' => $this->user,
            'tire' => $this->getSessionCombination()->getTire(),
            'driver' => $this->getSessionCombination()->getDriver(),
            'circuit' => $this->getSessionCombination()->getCircuit(),
        ], [
            'datetime' => 'DESC',
        ]);

        return $records;
    }

    public function saveRecord(PressureRecord $pressureRecord): void
    {
        if (!$this->isCheckedUser($pressureRecord)) {
            return;
        }

        $pressureRecord
            ->setUser($this->user)
            ->setDatetime(new DateTime('now'));

        if (!$pressureRecord->getId()) {
            $this->em->persist($pressureRecord);
        }

        $this->em->flush();
    }

    public function removeRecord($id): void
    {
        $pressureRecord = $this->prr->find($id);

        if (!$this->isCheckedUser($pressureRecord)) {
            return;
        }

        $this->em->remove($pressureRecord);
        $this->em->flush();
    }

    public function deltaPressure($temp, $coef)
    {
        // Calcul de la variation de pression relative à une différence de température par rapport à la température étalon, et à un coefficient donné
        return ($temp - PressureService::TEMP) * $coef;
    }

    public function getPressures(PressureRecord $pressureRecord): PressureRecord
    {
        // ============================================================================================================================
        // 0/ RECUPERATION DES ENREGISTREMENTS
        $records = $this->getRecords();

        // ============================================================================================================================
        // 1/ CALCUL DES PRESSIONS CORRIGEES (POUR UNE TEMPERATURE ETALON)

        // 1.1/ Initialisation des tableaux de pressions intermédiaires

        // Tableau des deltas de pressions par pneu
        $deltaPressureTires = [];

        // Tableau de tableaux de pressions corrigées par pneu
        $correctedPressures = [[], [], [], []];

        // Tableau des moyennes de pressions corrigées par pneu
        $averagePressures = [];

        // 1.2/ Calcul des pressions corrigées pour chaque relevé
        foreach ($records as $record) {

            // 1.2.1/ Stockage des informations du relevé

            // Tableau de températures des pneus
            $tempTires = [
                $record->getTempFrontLeft(),
                $record->getTempFrontRight(),
                $record->getTempRearLeft(),
                $record->getTempRearRight(),
            ];

            // Tableau de pressions des pneus
            $pressureTires = [
                $record->getPressFrontLeft(),
                $record->getPressFrontRight(),
                $record->getPressRearLeft(),
                $record->getPressRearRight(),
            ];

            // 1.2.2/ Calcul du delta de pression relatif à la différence de température piste par rapport à la température étalon
            $deltaPressureTrack = $this->deltaPressure($record->getTempTrack(), PressureService::COEF_TRACK);

            // 1.2.3/ Calcul des deltas de pression relatifs aux différences de température des pneus par rapport à la température étalon
            foreach ($tempTires as $tempTire) {
                $deltaPressureTires[] = $this->deltaPressure($tempTire, PressureService::COEF_AIR);
            }

            // 1.2.4/ Calcul des pressions corrigées pour chaque pneu
            for ($i = 0; $i < sizeof($correctedPressures); $i++) {
                $correctedPressures[$i][] = $pressureTires[$i] + $deltaPressureTrack - $deltaPressureTires[$i];
            }
        }

        // ============================================================================================================================
        // 2/ CALCUL DES MOYENNES DES PRESSIONS CORRIGEES
        for ($i = 0; $i < sizeof($correctedPressures); $i++) {
            $averagePressures[] = array_sum($correctedPressures[$i]) / count($correctedPressures[$i]);
        }

        // ============================================================================================================================
        // 3/ CALCUL DES PRESSIONS (POUR DES TEMPERATURES DONNEES)

        // 3.1/ Initialisation des tableaux de pressions intermédiaires

        // Tableau des deltas actuels de pressions par pneu
        $actualDeltaPressureTires = [];

        // Tableau de pressions calculées par pneu
        $calculatedPressures = [];

        // 3.2/ Calcul des pressions

        // 3.2.1/ Stockage des températures actuelles des pneus dans le tableau
        $actualTempTires = [
            $pressureRecord->getTempFrontLeft(),
            $pressureRecord->getTempFrontRight(),
            $pressureRecord->getTempRearLeft(),
            $pressureRecord->getTempRearRight(),
        ];

        // 3.2.2/ Calcul du delta de pression relatif à la différence de température piste par rapport à la température étalon
        $actualDeltaPressureTrack = $this->deltaPressure($pressureRecord->getTempTrack(), PressureService::COEF_TRACK);

        // 3.2.3/ Calcul des deltas de pression relatifs aux différences de température des pneus par rapport à la température étalon
        foreach ($actualTempTires as $actualTempTire) {
            $actualDeltaPressureTires[] = $this->deltaPressure($actualTempTire, PressureService::COEF_AIR);
        }

        // 3.2.4/ Calcul de la pression par pneu
        for ($i = 0; $i < sizeof($averagePressures); $i++) {
            $calculatedPressures[] = $averagePressures[$i] + $actualDeltaPressureTires[$i] - $actualDeltaPressureTrack;
        }

        // 3.3/ Stockage des pressions
        $pressureRecord
            ->setPressFrontLeft($calculatedPressures[0])
            ->setPressFrontRight($calculatedPressures[1])
            ->setPressRearLeft($calculatedPressures[2])
            ->setPressRearRight($calculatedPressures[3]);

        return $pressureRecord;
    }
}
