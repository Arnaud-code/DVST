<?php

namespace App\Pressure;

use App\Entity\Circuit;
use App\Entity\Driver;
use App\Entity\PressureRecord;
use App\Entity\Tire;
use App\Repository\CircuitRepository;
use App\Repository\DriverRepository;
use App\Repository\TireRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PressureService
{
    const TEMP = 20;
    const COEF_AIR = 0.013;
    const COEF_TRACK = 0.005;

    protected $session;
    protected $tr;
    protected $dr;
    protected $cr;

    public function __construct(SessionInterface $session, TireRepository $tr, DriverRepository $dr, CircuitRepository $cr)
    {
        $this->session = $session;
        $this->tr = $tr;
        $this->dr = $dr;
        $this->cr = $cr;
    }

    public function getSessionTire(): ?Tire
    {
        $tire = $this->session->get('sessionTireId');
        if (!$tire) {
            return null;
        }
        return $this->tr->find($tire);
    }

    public function getSessionDriver(): ?Driver
    {
        $driver = $this->session->get('sessionDriverId');
        if (!$driver) {
            return null;
        }
        return $this->dr->find($driver);
    }

    public function getSessionCircuit(): ?Circuit
    {
        $circuit = $this->session->get('sessionCircuitId');
        if (!$circuit) {
            return null;
        }
        return $this->cr->find($circuit);
    }

    public function deltaPressure($temp, $coef)
    {
        // Calcul de la variation de pression relative à une différence de température par rapport à la température étalon, et à un coefficient donné
        return ($temp - PressureService::TEMP) * $coef;
    }

    public function getPressures(PressureRecord $pressureRecord, $records)
    {
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
            $pressTires = [
                $record->getPressFrontLeft(),
                $record->getPressFrontRight(),
                $record->getPressRearLeft(),
                $record->getPressRearRight(),
            ];

            // 1.2.2/ Calcul du delta de pression relatif à la différence de température du sol par rapport à la température étalon
            $deltaPressureTrack = $this->deltaPressure($record->getTempGround(), PressureService::COEF_TRACK);

            // 1.2.3/ Calcul des deltas de pression relatifs aux différences de température des pneus par rapport à la température étalon
            foreach ($tempTires as $tempTire) {
                $deltaPressureTires[] = $this->deltaPressure($tempTire, PressureService::COEF_AIR);
            }

            // 1.2.4/ Calcul des pressions corrigées pour chaque pneu
            for ($i = 0; $i < sizeof($correctedPressures); $i++) {
                $correctedPressures[$i][] = $pressTires[$i] + $deltaPressureTrack - $deltaPressureTires[$i];
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

        // 3.2.2/ Calcul du delta de pression relatif à la différence de température du sol par rapport à la température étalon
        $actualDeltaPressureTrack = $this->deltaPressure($pressureRecord->getTempGround(), PressureService::COEF_TRACK);

        // 3.2.3/ Calcul des deltas de pression relatifs aux différences de température des pneus par rapport à la température étalon
        foreach ($actualTempTires as $actualTempTire) {
            $actualDeltaPressureTires[] = $this->deltaPressure($actualTempTire, PressureService::COEF_AIR);
        }

        // 3.2.4/ Calcul de la pression par pneu
        for ($i = 0; $i < sizeof($averagePressures); $i++) {
            $calculatedPressures[] = $averagePressures[$i] + $actualDeltaPressureTires[$i] - $actualDeltaPressureTrack;
        }

        return $calculatedPressures;
    }
}
