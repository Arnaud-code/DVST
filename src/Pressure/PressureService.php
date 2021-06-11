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
        if (!$this->session->get('sessionTireId')) {
            return null;
        }
        return $this->tr->find($this->session->get('sessionTireId'));
    }

    public function getSessionDriver(): ?Driver
    {
        if (!$this->session->get('sessionDriverId')) {
            return null;
        }
        return $this->dr->find($this->session->get('sessionDriverId'));
    }

    public function getSessionCircuit(): ?Circuit
    {
        if (!$this->session->get('sessionCircuitId')) {
            return null;
        }
        return $this->cr->find($this->session->get('sessionCircuitId'));
    }

    public function deltaPressure($temp, $coef)
    {
        // Calcul de la variation de pression relative à une différence de température par rapport à la température étalon, et à un coefficient donné
        return ($temp - PressureService::TEMP) * $coef;
    }

    public function getPressures(PressureRecord $pressureRecord, $records)
    {
        // 1/ CALCUL DES PRESSIONS CORRIGEES (POUR UNE TEMPERATURE ETALON)

        // 1.1/ Initialisation des tableaux de pressions intermédiaires

        // Tableau de tableaux de pressions corrigées par pneu
        $correctedPressures = [[], [], [], []];

        // Tableau des moyennes de pressions corrigées par pneu
        $averagePressures = [];

        // 1.3/ Boucle de calcul des pressions corrigées par ligne d'enregistrement
        foreach ($records as $record) {

            // 1.3.1/ Calcul du delta de pression relatif à la différence de température du sol par rapport à la température étalon
            $deltaPressureTrack = $this->deltaPressure($record->getTempGround(), PressureService::COEF_TRACK);

            // 1.3.2/ Récupération des températures des pneus dans un tableau
            $tempTires = [
                $record->getTempFrontLeft(),
                $record->getTempFrontRight(),
                $record->getTempRearLeft(),
                $record->getTempRearRight(),
            ];

            // 1.3.3/ Récupération des pressions des pneus dans un tableau
            $pressTires = [
                $record->getPressFrontLeft(),
                $record->getPressFrontRight(),
                $record->getPressRearLeft(),
                $record->getPressRearRight(),
            ];

            // calcul des deltas de pression relatifs aux différences de température des pneus par rapport à la température étalon
            // initialisation du tableau
            $deltaPressureTires = [];

            // boucle de calcul des deltas par pneu
            foreach ($tempTires as $tempTire) {

                // enregistrement du delta dans le tableau
                $deltaPressureTires[] = $this->deltaPressure($tempTire, PressureService::COEF_AIR);
            }

            // enregistrement des pressions corrigées dans les tableaux respectifs
            for ($i = 0; $i < 4; $i++) {
                $correctedPressures[$i][] = $pressTires[$i] + $deltaPressureTrack - $deltaPressureTires[$i];
            }
        }

        // calcul des moyennes des pressions corrigées par pneu
        for ($i = 0; $i < 4; $i++) {
            $averagePressures[] = array_sum($correctedPressures[$i]) / count($correctedPressures[$i]);
        }
        // ============================================================================================================================

        // init
        $calculatedPressures = [];

        // Calcul du delta de pression relatif à la différence de température du sol par rapport à la température étalon
        $actualDeltaPressureTrack = $this->deltaPressure($pressureRecord->getTempGround(), PressureService::COEF_TRACK);

        // Récupération des températures des pneus dans un tableau
        $actualTempTires = [
            $pressureRecord->getTempFrontLeft(),
            $pressureRecord->getTempFrontRight(),
            $pressureRecord->getTempRearLeft(),
            $pressureRecord->getTempRearRight(),
        ];

        // calcul des deltas de pression relatifs aux différences de température des pneus par rapport à la température étalon
        // initialisation du tableau
        $actualDeltaPressureTires = [];

        // boucle de calcul des deltas par pneu
        foreach ($actualTempTires as $actualTempTire) {

            // enregistrement du delta dans le tableau
            $actualDeltaPressureTires[] = $this->deltaPressure($actualTempTire, PressureService::COEF_AIR);
        }

        // 
        for ($i = 0; $i < 4; $i++) {

            // 
            $calculatedPressures[] = $averagePressures[$i] + $actualDeltaPressureTires[$i] - $actualDeltaPressureTrack;
        }

        return $calculatedPressures;
    }
}
